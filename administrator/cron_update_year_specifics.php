<?php
/**
 * Cron: Auto-compléter les aspects "Year" SELECTION_ONLY dans category_description
 * Ajoute les années manquantes jusqu'à l'année courante.
 * 
 * Usage crontab: 0 3 1 1 * php /home/n7f9655/public_html/phoenixliquidation/administrator/cron_update_year_specifics.php
 * (Exécute le 1er janvier à 3h du matin)
 */

// Sécurité: CLI seulement
if (php_sapi_name() !== 'cli') {
	http_response_code(403);
	exit('CLI only');
}

// Config admin
require_once(__DIR__ . '/config.php');

$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($db->connect_error) {
	echo "DB connection failed: " . $db->connect_error . "\n";
	exit(1);
}
$db->set_charset('utf8mb4');

$current_year = (int)date('Y');
$fixed = 0;
$prefix = DB_PREFIX;

$q = $db->query("SELECT cd.category_id, cd.language_id, cd.specifics 
	FROM {$prefix}category_description cd
	WHERE cd.specifics LIKE '%Year%' AND cd.specifics LIKE '%SELECTION_ONLY%'");

while ($row = $q->fetch_assoc()) {
	$specs = json_decode($row['specifics'], true);
	if (!$specs) continue;
	
	$changed = false;
	foreach ($specs as $key => &$data) {
		if (stripos($key, 'year') === false) continue;
		if (($data['aspectConstraint']['aspectMode'] ?? '') !== 'SELECTION_ONLY') continue;
		if (empty($data['aspectValues'])) continue;
		
		// Extraire les années numériques existantes
		$years = [];
		foreach ($data['aspectValues'] as $av) {
			$v = $av['localizedValue'] ?? '';
			if (preg_match('/^\d{4}$/', $v)) {
				$years[] = (int)$v;
			}
		}
		if (empty($years)) continue;
		
		$max_year = max($years);
		if ($max_year >= $current_year) continue;
		
		// Générer les années manquantes
		$new_entries = [];
		for ($y = $current_year; $y > $max_year; $y--) {
			$new_entries[] = ['localizedValue' => (string)$y];
		}
		
		// Détecter l'ordre de tri
		$first_val = $data['aspectValues'][0]['localizedValue'] ?? '';
		if (preg_match('/^\d{4}$/', $first_val) && (int)$first_val > $max_year - 5) {
			// Triée desc — insérer au début
			array_splice($data['aspectValues'], 0, 0, $new_entries);
		} else {
			// Triée asc ou mixte — insérer à la fin
			$data['aspectValues'] = array_merge($data['aspectValues'], array_reverse($new_entries));
		}
		$changed = true;
	}
	unset($data);
	
	if ($changed) {
		$json = json_encode($specs, JSON_UNESCAPED_UNICODE);
		$db->query("UPDATE {$prefix}category_description 
			SET specifics = '" . $db->real_escape_string($json) . "' 
			WHERE category_id = " . (int)$row['category_id'] . " 
			AND language_id = " . (int)$row['language_id']);
		$fixed++;
	}
}

$db->close();
echo date('Y-m-d H:i:s') . " — Year specifics updated: $fixed entries (current year: $current_year)\n";
