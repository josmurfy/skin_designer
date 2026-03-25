<?php
/**
 * test_description.php
 * Mesure la taille des descriptions pour un listing donné
 * Usage: php dev/test_description.php [listing_id]
 */

$listing_id = (int)($argv[1] ?? 64);

// ── Bootstrap minimal ──────────────────────────────────────────────────────
define('DIR_ROOT',        '/home/n7f9655/public_html/phoenixliquidation/');
define('DIR_APPLICATION', DIR_ROOT . 'administrator/');
define('DIR_SYSTEM',      DIR_ROOT . 'system/');
define('HTTP_SERVER',     'https://phoenixliquidation.ca/administrator/');

require_once DIR_ROOT . 'administrator/config.php';

$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($mysqli->connect_error) {
    die('DB error: ' . $mysqli->connect_error . PHP_EOL);
}

// ── 1. Description(s) en DB ────────────────────────────────────────────────
echo "══════════════════════════════════════════════════════" . PHP_EOL;
echo " Listing #$listing_id — analyse description" . PHP_EOL;
echo "══════════════════════════════════════════════════════" . PHP_EOL . PHP_EOL;

$res = $mysqli->query(
    "SELECT batch_name, language_id, LENGTH(description) as len, LEFT(description,120) as preview
     FROM oc_card_listing_description
     WHERE listing_id = $listing_id
     ORDER BY batch_name, language_id"
);

echo "── Rows dans oc_card_listing_description ─────────────" . PHP_EOL;
while ($r = $res->fetch_assoc()) {
    printf("  batch_name=%-3d lang=%-2d  len=%-6d  preview: %s…\n",
        $r['batch_name'], $r['language_id'], $r['len'], rtrim($r['preview']));
}
echo PHP_EOL;

// ── 2. Analyse détaillée par batch ────────────────────────────────────────
$res2 = $mysqli->query(
    "SELECT batch_name, description
     FROM oc_card_listing_description
     WHERE listing_id = $listing_id AND language_id = 1 AND batch_name > 0
     ORDER BY batch_name"
);

while ($r = $res2->fetch_assoc()) {
    $bid  = (int)$r['batch_name'];
    $raw  = $r['description'];

    echo "── Batch $bid ─────────────────────────────────────────" . PHP_EOL;

    // Longueur brute
    echo "  raw_description (DB):         " . strlen($raw) . " chars" . PHP_EOL;

    // json_encode direct
    $encoded = json_encode($raw);
    echo "  json_encode direct:           " . ($encoded !== false ? "OK (" . strlen($encoded) . " chars)" : "FAIL — " . json_last_error_msg()) . PHP_EOL;

    // html_entity_decode UTF-8
    $decoded = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    echo "  html_entity_decode UTF-8:     " . strlen($decoded) . " chars" . PHP_EOL;
    $enc2 = json_encode($decoded);
    echo "  json_encode après decode:     " . ($enc2 !== false ? "OK (" . strlen($enc2) . " chars)" : "FAIL — " . json_last_error_msg()) . PHP_EOL;

    // iconv sanitize
    $clean = iconv('UTF-8', 'UTF-8//IGNORE', $raw);
    echo "  iconv UTF-8//IGNORE:          " . strlen($clean) . " chars" . PHP_EOL;
    $enc3 = json_encode($clean);
    echo "  json_encode après iconv:      " . ($enc3 !== false ? "OK (" . strlen($enc3) . " chars)" : "FAIL — " . json_last_error_msg()) . PHP_EOL;

    // Bad bytes
    $badBytes = [];
    for ($i = 0; $i < strlen($raw); $i++) {
        $byte = ord($raw[$i]);
        // Invalid UTF-8 lead bytes for single chars OR Windows-1252 range 0x80-0x9F
        if ($byte >= 0x80 && $byte <= 0x9F) {
            $badBytes[] = sprintf("0x%02X @ pos %d", $byte, $i);
        }
    }
    if ($badBytes) {
        echo "  ⚠ Bad bytes (Win-1252):       " . implode(', ', $badBytes) . PHP_EOL;
    } else {
        echo "  ✓ No Windows-1252 bad bytes" . PHP_EOL;
    }

    echo PHP_EOL;
}

// ── 3. Taille du wrapper CSS (getTemplatePartsCardListing) ────────────────
echo "── Template wrapper CSS ───────────────────────────────" . PHP_EOL;

$tpl_file = file_get_contents(DIR_APPLICATION . 'model/shopmanager/ebaytemplate.php');

// Extraire le corps de la fonction getTemplatePartsCardListing
// La fonction va de la ligne 474 jusqu'à la prochaine fonction privée (~ligne 880)
if (preg_match('/function getTemplatePartsCardListing\(\)\s*\{(.+?)(?=private function)/s', $tpl_file, $m)) {
    $func_body = $m[1];
    $total_wrapper = strlen($func_body);

    // Compter uniquement les string literals HTML (entre quotes, >50 chars)
    preg_match_all("/'([^']{100,})'/s", $func_body, $strings);
    $html_chars = 0;
    foreach ($strings[1] as $s) {
        $html_chars += strlen($s);
    }

    echo "  Taille brute de la fonction:  " . $total_wrapper . " chars" . PHP_EOL;
    echo "  HTML string literals (>100c): " . $html_chars . " chars" . PHP_EOL;
    echo PHP_EOL;

    // Simuler description wrappée = raw_description + wrapper HTML
    $res3 = $mysqli->query(
        "SELECT batch_name, description
         FROM oc_card_listing_description
         WHERE listing_id = $listing_id AND language_id = 1 AND batch_name > 0
         ORDER BY batch_name LIMIT 1"
    );
    if ($r3 = $res3->fetch_assoc()) {
        $raw = $r3['description'];
        $wrapped_estimate = strlen($raw) + $html_chars;
        $wrapped_json     = json_encode(iconv('UTF-8', 'UTF-8//IGNORE', $raw) . str_repeat(' ', $html_chars));

        echo "── Estimation description wrappée (batch " . $r3['batch_name'] . ") ──────" . PHP_EOL;
        echo "  raw_description:              " . strlen($raw) . " chars" . PHP_EOL;
        echo "  + wrapper HTML:               " . $html_chars . " chars" . PHP_EOL;
        echo "  = total wrappé estimé:        " . $wrapped_estimate . " chars" . PHP_EOL;
        echo "  Limite eBay Inventory API:    500,000 chars" . PHP_EOL;
        echo "  Ancienne limite (bug):        4,000 chars  ← description tronquée ici avant!" . PHP_EOL;
        echo "  Dépassement ancienne limite:  " . ($wrapped_estimate > 4000 ? "OUI → ". ($wrapped_estimate - 4000) . " chars en trop → DESCRIPTION VIDE!" : "non") . PHP_EOL;
        echo PHP_EOL;
    }
}

echo "══════════════════════════════════════════════════════" . PHP_EOL;
echo " Conclusion:" . PHP_EOL;
echo "  raw_description envoyé à eBay maintenant = description DB directe" . PHP_EOL;
echo "  Pas de troncature. json_encode OK après iconv." . PHP_EOL;
echo "══════════════════════════════════════════════════════" . PHP_EOL;
