<?php
/**
 * restore_ebay_card_images.php
 *
 * Restaure les URLs eBay dans oc_card_image pour un listing donné.
 * Appelle GetItem (Trading API) avec le offer_id de chaque carte.
 *
 * Usage web : /interne/restore_ebay_card_images.php?listing_id=117
 * Usage CLI : php restore_ebay_card_images.php 117
 */

set_time_limit(0);
ini_set('memory_limit', '256M');

$isCli = php_sapi_name() === 'cli';
function out(string $msg): void {
    global $isCli;
    echo $isCli ? $msg . "\n" : nl2br(htmlspecialchars($msg)) . "<br>\n";
    @ob_flush(); @flush();
}

// ── listing_id ────────────────────────────────────────────────────────────────
$listing_id = $isCli
    ? (isset($argv[1]) ? (int)$argv[1] : 0)
    : (isset($_GET['listing_id']) ? (int)$_GET['listing_id'] : 0);

if (!$listing_id) {
    die("Usage: ?listing_id=117\n");
}

// ── Connexion DB directe ──────────────────────────────────────────────────────
$db = new mysqli('127.0.0.1', 'n7f9655_n7f9655', 'jnthngrvs01$$', 'n7f9655_phoenixliquidation');
if ($db->connect_error) die("DB error: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

// ── Credentials eBay depuis oc_marketplace_accounts ──────────────────────────
$credRow = $db->query("SELECT application_id, developer_id, certification_id, client_id, client_secret, refresh_token FROM oc_marketplace_accounts ORDER BY marketplace_account_id ASC LIMIT 1")->fetch_assoc();
if (!$credRow || empty($credRow['refresh_token'])) {
    die("Erreur: pas de credentials eBay (refresh_token) dans oc_marketplace_accounts\n");
}
$devName   = $credRow['application_id'];
$appName   = $credRow['developer_id'];
$certName  = $credRow['certification_id'];

// ── Refresh OAuth token ───────────────────────────────────────────────────────
function refreshEbayToken(string $clientId, string $clientSecret, string $refreshToken): string {
    $credentials = base64_encode($clientId . ':' . $clientSecret);
    $postFields  = http_build_query([
        'grant_type'    => 'refresh_token',
        'refresh_token' => $refreshToken,
        'scope'         => 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory',
    ]);
    $ch = curl_init('https://api.ebay.com/identity/v1/oauth2/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Basic ' . $credentials,
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (!$resp) die("Erreur curl refresh token\n");
    $data = json_decode($resp, true);
    if (empty($data['access_token'])) {
        die("Erreur refresh token (HTTP $httpCode): " . $resp . "\n");
    }
    return $data['access_token'];
}

out("Rafraîchissement du token OAuth eBay...");
$bearerToken = refreshEbayToken($credRow['client_id'], $credRow['client_secret'], $credRow['refresh_token']);
out("Token OK.");

// ── Fonction GetItem → URLs photos ───────────────────────────────────────────
function ebayGetPictures(string $itemId, string $bearerToken, string $devName, string $appName, string $certName): array {
    // Pas de <RequesterCredentials> avec OAuth — utiliser X-EBAY-API-IAF-TOKEN
    $xml = '<?xml version="1.0" encoding="utf-8"?>
<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
    <DetailLevel>ReturnAll</DetailLevel>
    <WarningLevel>High</WarningLevel>
    <ItemID>' . $itemId . '</ItemID>
</GetItemRequest>';

    $headers = [
        'X-EBAY-API-COMPATIBILITY-LEVEL: 1371',
        'X-EBAY-API-CALL-NAME: GetItem',
        'X-EBAY-API-SITEID: 0',
        'X-EBAY-API-DEV-NAME: '  . $devName,
        'X-EBAY-API-APP-NAME: '  . $appName,
        'X-EBAY-API-CERT-NAME: ' . $certName,
        'X-EBAY-API-IAF-TOKEN: Bearer ' . $bearerToken,
        'Content-Type: text/xml',
    ];

    $ch = curl_init('https://api.ebay.com/ws/api.dll');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    

    if (!$response) return [];
    $xmlObj = @simplexml_load_string($response);
    if (!$xmlObj) return [];

    if (isset($xmlObj->Ack) && in_array((string)$xmlObj->Ack, ['Failure', 'PartialFailure'])) {
        return ['__error__' => (string)($xmlObj->Errors->ShortMessage ?? 'Unknown')];
    }

    $urls = [];
    if (isset($xmlObj->Item->PictureDetails->PictureURL)) {
        foreach ($xmlObj->Item->PictureDetails->PictureURL as $p) {
            $u = trim((string)$p);
            if ($u) $urls[] = $u;
        }
    }
    return array_values(array_unique($urls));
}

// ── Cartes du listing ─────────────────────────────────────────────────────────
$result = $db->query("SELECT card_id, offer_id FROM oc_card
    WHERE listing_id = $listing_id AND offer_id IS NOT NULL AND offer_id != '' AND status = 1
    ORDER BY card_id ASC");
$cards = $result->fetch_all(MYSQLI_ASSOC);
$total = count($cards);

out("=== Restauration images eBay — listing_id=$listing_id ===");
out("$total cartes avec offer_id.");
if (!$total) die("Aucune carte avec offer_id pour listing_id=$listing_id\n");

// ── Traitement ────────────────────────────────────────────────────────────────
$done = $errors = 0;

foreach ($cards as $idx => $card) {
    $cardId  = (int)$card['card_id'];
    $offerId = trim($card['offer_id']);

    $pics = ebayGetPictures($offerId, $bearerToken, $devName, $appName, $certName);

    if (isset($pics['__error__'])) {
        out("  [$cardId] offer=$offerId → ERREUR: " . $pics['__error__']);
        $errors++;
        continue;
    }
    if (empty($pics)) {
        out("  [$cardId] offer=$offerId → aucune image retournée");
        $errors++;
        continue;
    }

    $db->query("DELETE FROM oc_card_image WHERE card_id = $cardId");

    $sortOrder = 1;
    foreach ($pics as $url) {
        $urlEsc = $db->real_escape_string($url);
        $db->query("INSERT INTO oc_card_image (card_id, image_type, image_url, sort_order)
                    VALUES ($cardId, 'product', '$urlEsc', $sortOrder)");
        $sortOrder++;
    }
    $done++;

    if (($idx + 1) % 10 === 0) {
        out("  ... " . ($idx + 1) . "/$total ([$cardId] " . count($pics) . " img)");
    }
}

out("");
out("=== TERMINÉ === Restaurés: $done / $total  |  Erreurs: $errors");
$db->close();
