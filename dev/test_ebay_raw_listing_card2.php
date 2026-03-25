<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);

// ─────────────────────────────────────────────
// OUTPUT HELPERS
// ─────────────────────────────────────────────
function out(string $msg): void {
    echo PHP_SAPI === 'cli' ? $msg . PHP_EOL : nl2br(htmlspecialchars($msg, ENT_QUOTES, 'UTF-8')) . "<br>\n";
}

function outPretty(string $title, $data): void {
    $text = is_array($data) ? print_r($data, true) : (string)$data;
    if (PHP_SAPI === 'cli') {
        echo "=== {$title} ===\n{$text}\n";
        return;
    }
    echo '<h3 style="margin:12px 0 6px;font-family:Arial,sans-serif;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h3>';
    echo '<pre style="background:#0f172a;color:#e2e8f0;padding:12px;border-radius:8px;overflow:auto;white-space:pre-wrap;word-break:break-word;line-height:1.35;">'
        . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</pre>';
}

// ─────────────────────────────────────────────
// INPUT  (keyword obligatoire)
// ─────────────────────────────────────────────
function parseInput(): array {
    global $argv;
    if (PHP_SAPI === 'cli') {
        return [
            'keyword'                => trim((string)($argv[1] ?? '')),
            'marketplace_account_id' => (int)($argv[2] ?? 1),
            'limit'                  => max(1, min(200, (int)($argv[3] ?? 100))),
            'site_id'                => (int)($argv[4] ?? 0),
        ];
    }
    return [
        'keyword'                => trim((string)($_GET['keyword'] ?? '')),
        'marketplace_account_id' => (int)($_GET['marketplace_account_id'] ?? 1),
        'limit'                  => max(1, min(200, (int)($_GET['limit'] ?? 100))),
        'site_id'                => (int)($_GET['site_id'] ?? 0),
    ];
}

// ─────────────────────────────────────────────
// DB — credentials OAuth seulement
// ─────────────────────────────────────────────
function getMarketplaceCredentials(int $accountId): array {
    require_once dirname(__DIR__) . '/config.php';
    $db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int)DB_PORT);
    if ($db->connect_error) throw new RuntimeException('DB: ' . $db->connect_error);
    $db->set_charset('utf8mb4');
    $row = $db->query("SELECT client_id, client_secret, refresh_token FROM oc_marketplace_accounts WHERE marketplace_account_id = " . (int)$accountId . " LIMIT 1")->fetch_assoc();
    $db->close();
    if (!$row) throw new RuntimeException('Marketplace account not found: ' . $accountId);
    return $row;
}

// ─────────────────────────────────────────────
// OAUTH
// ─────────────────────────────────────────────
function refreshAccessToken(array $creds): string {
    $ch = curl_init('https://api.ebay.com/identity/v1/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . base64_encode($creds['client_id'] . ':' . $creds['client_secret']),
        ],
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type'    => 'refresh_token',
            'refresh_token' => $creds['refresh_token'],
        ]),
        CURLOPT_TIMEOUT => 45,
    ]);
    $resp = curl_exec($ch);
    $err  = curl_error($ch)
    c
    if ($err) throw new 

    if (empty($j['acces
    return (string)$j['access_t
}

// ────

// ─────────────────────────────────────────────
function browseSe
    $siteMap = [0 => 'EBAY_US', 2 => 'EBAY_CA', 3 => 'EBAY_GB', 71 => 'EBAY_FR', 77 => 'EBAY_DE'];
    $url = 'https://api.ebay.com/buy/br
        'q'            => $keywor
        'category_ids' => '261328',
        'auto_correct' => 'KEYWORD',
        'sort'         => 'price',
 
 

    ]);
    $headers = ['Authorization: Bearer '
    if (isset($siteMap[$siteId])) $headers[] = 'X-EBAY-C-MARKETPLAC

    $ch = curl_init($url);
 
    $raw      = curl_exec($ch);
    $httpCo
    $err      =
    c

    return ['url' => $url, 'http_code' => $httpCode, 'curl_error' => $err, 'json' => json_decode((string)$raw, true), 'raw' => (s
}

// ─────────────────────────────────────────────
// PARSE ITEMS
// ─
function parseItems(array $summaries): array {
    $rows = [];
   
 

        $isAuction    = false;
        foreach ($buyingOptions as $o) {
            if (stripos((string)$o, 'AUCTION') !== false) { $isAuc
        }
        $priceField =
        $price = (float)($priceField['value'] ?? 0);
        if ($price <= 0) continue;

        $condition  = strtolower(trim((string)($i
        $grader     = '';
    
        if (preg_
            $grader     = str
            $gra
        }
        $isUngraded        = str_contains($condition, 'ungrad
        $isGradedCondition = ($condition === 'graded') || (st
        $isGraded          = $isGradedCondition || (!$isUngraded && $grader !== '

        $rows[] = [
            'item_id'      => (string)($
          
     
            
            'currency'     => (string)($priceField['currency'] ?? 'USD'),
 
            'bid_count'    => (int)($item['bidCount'] ?? 0),
            'is_gra
            'grade'        => $grader !== '' ? ($grader . ' ' . $gradeScore) : '',

    }
    return $rows;
}

// ───────────
�// CLA
/

    $pick = static fn(array $c, ?array $cur): bool => $cur === null || (float)$c['price'] < (float)$cur['price'];
    $b = ['auction_raw' 
    foreach ($items as $it) {
       
        $isG = !empty($it['is_graded']);
        if ( $isA && !$isG && $pick($it, $b['auction_raw']))    $b['auction_raw']    = $it;
     
        if (!$isA && !$isG && $pick($it, $b['buy_now_raw'])
        if (!$isA &&  $isG && $pick($it, $b['buy_n
    }
    return $b;
}

// ────────────────────
// MAIN
// ────────────────────────
�try {
    $input     = parseInpu
    $keyword   = $input['keyword'];
    $accountId = $input['marketplace_account_id'];
    $limit     = $input['limit'];
    $siteId    = $input['site_id'];

    if (mb_strlen($ke
        throw new
    }

    $creds  = getMarketplaceCredentials($accountId);
    $token  = refreshAccessTok
    $result = br



    $parsed    = parseItems($summaries);
    $buckets   = classify($parsed);

    out('keyword=' . $keyword);
    out('http_code=' . $resu
    out(
    out('browse_url=' . $result['url']);
    outPretty('CLASSIFIED BUCKETS', $buckets);

} catch (Throwable $e) {
    http_response_code(5
    out('ERROR: ' . $e->getMessage());
}
