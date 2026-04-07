<?php
/**
 * Batch fix: translate SELECTION_ONLY aspectValues for FR language
 * Strategy: translate each unique aspect ONCE, then replicate to all categories
 * Usage: nohup php dev/fix_fr_specifics.php > /tmp/fix_fr_specifics.log 2>&1 &
 */

chdir(__DIR__ . '/../administrator');
require_once('config.php');
require_once(DIR_SYSTEM . 'startup.php');

$autoloader = new \Opencart\System\Engine\Autoloader();
$autoloader->register('Opencart\Admin', DIR_APPLICATION);
$autoloader->register('Opencart\Extension', DIR_EXTENSION);
$autoloader->register('Opencart\System', DIR_SYSTEM);
require_once(DIR_SYSTEM . 'vendor.php');

$registry = new \Opencart\System\Engine\Registry();
$registry->set('autoloader', $autoloader);

$config = new \Opencart\System\Engine\Config();
$config->addPath(DIR_CONFIG);
$config->load('default');
$config->load('admin');
$config->set('application', 'Admin');
$config->set('config_language_id', 1);
date_default_timezone_set($config->get('date_timezone'));
$registry->set('config', $config);

$log = new \Opencart\System\Library\Log('fix_fr_specifics.log');
$registry->set('log', $log);
$event = new \Opencart\System\Engine\Event($registry);
$registry->set('event', $event);
$registry->set('factory', new \Opencart\System\Engine\Factory($registry));
$loader = new \Opencart\System\Engine\Loader($registry);
$registry->set('load', $loader);
$request = new \Opencart\System\Library\Request();
$registry->set('request', $request);
$response = new \Opencart\System\Library\Response();
$registry->set('response', $response);
$db = new \Opencart\System\Library\DB($config->get('db_engine'), $config->get('db_hostname'), $config->get('db_username'), $config->get('db_password'), $config->get('db_database'), $config->get('db_port'), $config->get('db_ssl_key'), $config->get('db_ssl_cert'), $config->get('db_ssl_ca'));
$registry->set('db', $db);
$registry->set('cache', new \Opencart\System\Library\Cache($config->get('cache_engine'), $config->get('cache_expire')));

set_time_limit(0);
ini_set('memory_limit', '512M');

// Load AI model for translation
$loader->model('shopmanager/ai');
$ai = $registry->get('model_shopmanager_ai');

// ===== STEP 1: Collect all unique untranslated aspects =====
$query = $db->query("
    SELECT cd1.category_id, cd1.specifics as en_spec, cd2.specifics as fr_spec
    FROM " . DB_PREFIX . "category_description cd1
    JOIN " . DB_PREFIX . "category_description cd2 ON cd1.category_id = cd2.category_id AND cd2.language_id = 2
    WHERE cd1.language_id = 1 
    AND cd1.specifics IS NOT NULL AND cd1.specifics != ''
    AND cd1.specifics LIKE '%SELECTION_ONLY%'
");

// Map: aspect_name => [en_values => [...], categories => [cat_id => fr_key]]
$aspect_map = [];

foreach ($query->rows as $row) {
    $en = json_decode($row['en_spec'], true);
    $fr = json_decode($row['fr_spec'], true);
    if (!$en || !$fr) continue;
    
    foreach ($en as $en_key => $data) {
        if (($data['aspectConstraint']['aspectMode'] ?? '') !== 'SELECTION_ONLY') continue;
        if (empty($data['aspectValues'])) continue;
        
        $en_vals = array_column($data['aspectValues'], 'localizedValue');
        
        // Skip if all values are numeric
        $all_numeric = true;
        foreach ($en_vals as $v) {
            if (!preg_match('/^[\d\.\+\-\/\s]+$/', $v)) { $all_numeric = false; break; }
        }
        if ($all_numeric) continue;
        
        // Find the FR key for this aspect (may be translated name)
        $fr_key = null;
        foreach ($fr as $fk => $fd) {
            $fr_test = array_column($fd['aspectValues'] ?? [], 'localizedValue');
            // Match by same values (EN=FR means untranslated)
            if ($fr_test === $en_vals) {
                $fr_key = $fk;
                break;
            }
        }
        if (!$fr_key) continue; // Already translated
        
        // Group by EN aspect name + values fingerprint
        $fingerprint = $en_key . '|' . md5(json_encode($en_vals));
        if (!isset($aspect_map[$fingerprint])) {
            $aspect_map[$fingerprint] = [
                'en_key'     => $en_key,
                'en_values'  => $en_vals,
                'categories' => []
            ];
        }
        $aspect_map[$fingerprint]['categories'][$row['category_id']] = $fr_key;
    }
}

echo "Found " . count($aspect_map) . " unique aspect+values combinations to translate\n";
$total_cats = 0;
foreach ($aspect_map as $a) { $total_cats += count($a['categories']); }
echo "Covering $total_cats category-aspect pairs\n\n";

// ===== STEP 2: Translate each unique combo ONCE and apply to all categories =====
$success = 0;
$errors = 0;
$i = 0;

foreach ($aspect_map as $fingerprint => $info) {
    $i++;
    $cat_count = count($info['categories']);
    echo "[$i/" . count($aspect_map) . "] {$info['en_key']} (" . count($info['en_values']) . " values, $cat_count cats)... ";
    flush();
    
    try {
        // Translate values EN -> FR via GPT (one call for all values)
        $json_values = json_encode($info['en_values'], JSON_UNESCAPED_UNICODE);
        $translated_raw = $ai->translate($json_values, 'fr');
        
        if (is_string($translated_raw)) {
            $translated = json_decode($translated_raw, true);
        } else {
            $translated = $translated_raw;
        }
        
        if (!is_array($translated) || count($translated) !== count($info['en_values'])) {
            echo "SKIP (translation mismatch: got " . (is_array($translated) ? count($translated) : 'non-array') . " expected " . count($info['en_values']) . ")\n";
            $errors++;
            continue;
        }
        
        // Build the translated aspectValues array
        $translated_aspect_values = [];
        foreach ($translated as $val) {
            $translated_aspect_values[] = ['localizedValue' => $val];
        }
        
        // Apply to all categories
        $updated = 0;
        foreach ($info['categories'] as $cat_id => $fr_key) {
            $q2 = $db->query("SELECT specifics FROM " . DB_PREFIX . "category_description WHERE category_id = " . (int)$cat_id . " AND language_id = 2");
            if (!$q2->num_rows) continue;
            
            $fr_specifics = json_decode($q2->row['specifics'], true);
            if (!$fr_specifics || !isset($fr_specifics[$fr_key])) continue;
            
            $fr_specifics[$fr_key]['aspectValues'] = $translated_aspect_values;
            
            $db->query("UPDATE " . DB_PREFIX . "category_description SET specifics = '" . $db->escape(json_encode($fr_specifics, JSON_UNESCAPED_UNICODE)) . "' WHERE category_id = " . (int)$cat_id . " AND language_id = 2");
            $updated++;
        }
        
        echo "OK ($updated cats updated)\n";
        $success++;
        
    } catch (\Exception $e) {
        echo "EXCEPTION: " . $e->getMessage() . "\n";
        $errors++;
    }
    
    usleep(100000); // 100ms between GPT calls
}

echo "\nDone! Aspects translated: $success, Errors: $errors\n";
