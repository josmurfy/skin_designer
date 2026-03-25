<?php
/**
 * Restaure les URLs eBay dans oc_card_image pour listing_id=117
 * en lisant les VariationSpecificPictureSet des 2 lots eBay.
 */
set_time_limit(0);
ini_set('memory_limit', '256M');

$isCli = php_sapi_name() === 'cli';
function out(string $msg): void {
    global $isCli;
    echo $isCli ? $msg . "\n" : nl2br(htmlspecialchars($msg)) . "<br>\n";
    @ob_flush(); @flush();
}

$listing_id = 117;
$ebayItems  = ['306796389183', '298076564312'];

// ── DB ────────────────────────────────────────────────────────────────────────
$db = new mysqli('127.0.0.1', 'n7f9655_n7f9655', 'jnthngrvs01$$', 'n7f9655_phoenixliquidation');
if ($db->connect_error) die("DB error: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

// ── OAuth ─────────────────────────────────────────────────────────────────────
$cred = $db->query("SELECT application_id, developer_id, certification_id, client_id, client_secret, refresh_token FROM oc_marketplace_accounts LIMIT 1")->fetch_assoc();
$c2   = base64_encode($cred['client_id'] . ':' . $cred['client_secret']);
$ch   = curl_init('https://api.ebay.com/identity/v1/oauth2/token');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'grant_type'    => 'refresh_token',
    'refresh_token' => $cred['refresh_token'],
    'scope'         => 'https://api.ebay.com/oauth/api_scope',
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Authorization: Basic ' . $c2,
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$bearer = json_decode(curl_exec($ch), true)['access_token'] ?? '';

if (!$bearer) die("Erreur refresh token\n");
out("Token OAuth OK.");

// ── Index cartes : clé = \"#NUM playername\" (lowercase) → card_id ────────────
$cards = $db->query("SELECT card_id, card_number, player_name FROM oc_card WHERE listing_id=$listing_id AND status=1")->fetch_all(MYSQLI_ASSOC);
$index = [];
foreach ($cards as $c) {
    $num    = trim($c['card_number']);
    $player = strtolower(trim($c['player_name']));
    // avec zéro padding tel quel
    $index['#' . $num . $player] = (int)$c['card_id'];
    // sans zéro leading
    $index['#' . ltrim($num, '0') . $player] = (int)$c['card_id'];
}
out(count($cards) . " cartes chargées pour listing $listing_id.");

// ── Helper GetItem ─────────────────────────────────────────────────────────────
function ebayGetItem(string $itemId, string $bearer, array $cred): ?SimpleXMLElement {
    $xml = '<?xml version="1.0" encoding="utf-8"?>
<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
    <DetailLevel>ReturnAll</DetailLevel>
    <ItemID>' . $itemId . '</ItemID>
</GetItemRequest>';
    $ch = curl_init('https://api.ebay.com/ws/api.dll');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-EBAY-API-COMPATIBILITY-LEVEL: 1371',
        'X-EBAY-API-CALL-NAME: GetItem',
        'X-EBAY-API-SITEID: 0',
        'X-EBAY-API-DEV-NAME: '  . $cred['application_id'],
        'X-EBAY-API-APP-NAME: '  . $cred['developer_id'],
        'X-EBAY-API-CERT-NAME: ' . $cred['certification_id'],
        'X-EBAY-API-IAF-TOKEN: Bearer ' . $bearer,
        'Content-Type: text/xml',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $r = curl_exec($ch);
    
    if (!$r) return null;
    return @simplexml_load_string($r) ?: null;
}

// ── Traitement ─────────────────────────────────────────────────────────────────
$done = $notfound = $errors = 0;

foreach ($ebayItems as $itemId) {
    out("");
    out("=== GetItem $itemId ===");
    $x = ebayGetItem($itemId, $bearer, $cred);
    if (!$x || (string)$x->Ack !== 'Success') {
        out("ERREUR lot $itemId : " . ($x ? (string)$x->Errors->ShortMessage : 'curl fail'));
        $errors++;
        continue;
    }

    if (!isset($x->Item->Variations->Pictures)) {
        out("Pas de VariationSpecificPictureSet pour $itemId");
        continue;
    }

    $countLot = 0;
    foreach ($x->Item->Variations->Pictures->VariationSpecificPictureSet as $set) {
        $val   = (string)$set->VariationSpecificValue;   // "#92 Anthony Newman"
        $clean = strtolower(trim($val));                  // "#92 anthony newman"

        // Parse: "#NUM rest" 
        if (!preg_match('/^(#\S+)\s+(.+)$/', $clean, $m)) {
            out("  Format inattendu: $val");
            $notfound++;
            continue;
        }
        $numRaw = ltrim($m[1], '#');  // "92" ou "092"
        $player = trim($m[2]);        // "anthony newman"

        $key1   = '#' . $numRaw . $player;
        $key2   = '#' . ltrim($numRaw, '0') . $player;
        $cardId = $index[$key1] ?? $index[$key2] ?? null;

        if (!$cardId) {
            out("  NOT FOUND: $val");
            $notfound++;
            continue;
        }

        // Collecter URLs
        $urls = [];
        foreach ($set->PictureURL as $p) {
            $u = trim((string)$p);
            if ($u) $urls[] = $u;
        }
        if (empty($urls)) continue;

        // Remplacer oc_card_image
        $db->query("DELETE FROM oc_card_image WHERE card_id=$cardId");
        $sort = 1;
        foreach ($urls as $url) {
            $esc = $db->real_escape_string($url);
            $db->query("INSERT INTO oc_card_image (card_id, image_type, image_url, sort_order) VALUES ($cardId, 'product', '$esc', $sort)");
            $sort++;
        }
        $done++;
        $countLot++;
    }
    out("Lot $itemId → $countLot cartes restaurées.");
}

out("");
out("=== TERMINÉ === Restaurées: $done | Pas trouvées: $notfound | Erreurs: $errors");
$db->close();
