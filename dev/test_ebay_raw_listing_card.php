<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);

function out(string $message): void {
    if (PHP_SAPI === 'cli') {
        echo $message . PHP_EOL;
    } else {
        echo nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "<br>\n";
    }
}

function outPretty(string $title, $data): void {
    $isCli = (PHP_SAPI === 'cli');

    if ($isCli) {
        echo "=== " . $title . " ===" . PHP_EOL;

        if (is_array($data) || is_object($data)) {
            echo print_r($data, true) . PHP_EOL;
        } else {
            echo (string)$data . PHP_EOL;
        }

        return;
    }

    echo '<h3 style="margin:12px 0 6px;font-family:Arial,sans-serif;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h3>';

    $render = is_array($data) || is_object($data)
        ? print_r($data, true)
        : (string)$data;

    echo '<pre style="background:#0f172a;color:#e2e8f0;padding:12px;border-radius:8px;overflow:auto;white-space:pre-wrap;word-break:break-word;line-height:1.35;">'
        . htmlspecialchars($render, ENT_QUOTES, 'UTF-8')
        . '</pre>';
}

function parseInput(): array {
    global $argv;

    if (PHP_SAPI === 'cli') {
        return [
            'listing_id' => (int)($argv[1] ?? 999),
            'card_number' => trim((string)($argv[2] ?? '18')),
            'marketplace_account_id' => (int)($argv[3] ?? 1),
            'auto_correct' => (int)($argv[4] ?? 1),
            'sort_field' => trim((string)($argv[5] ?? 'price')),
            'ascending' => (int)($argv[6] ?? 1),
        ];
    }

    return [
        'listing_id' => (int)($_GET['listing_id'] ?? 999),
        'card_number' => trim((string)($_GET['card_number'] ?? '18')),
        'marketplace_account_id' => (int)($_GET['marketplace_account_id'] ?? 1),
        'auto_correct' => (int)($_GET['auto_correct'] ?? 1),
        'sort_field' => trim((string)($_GET['sort_field'] ?? 'price')),
        'ascending' => (int)($_GET['ascending'] ?? 1),
    ];
}

function resolveBrowseSort(string $sortField, bool $ascending): string {
    $sortField = strtolower(trim($sortField));

    if (in_array($sortField, ['amount', 'price', 'cost'], true)) {
        return $ascending ? 'price' : '-price';
    }

    if (in_array($sortField, ['date', 'itemcreationdate', 'created', 'created_at'], true)) {
        return $ascending ? 'itemCreationDate' : '-itemCreationDate';
    }

    return $ascending ? 'price' : '-price';
}

function db(): mysqli {
    require_once dirname(__DIR__) . '/config.php';

    $db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int)DB_PORT);
    if ($db->connect_error) {
        throw new RuntimeException('DB connect error: ' . $db->connect_error);
    }

    $db->set_charset('utf8mb4');
    return $db;
}

function getMarketplaceAccount(mysqli $db, int $marketplaceAccountId): array {
    $sql = "SELECT marketplace_account_id, client_id, client_secret, refresh_token
            FROM oc_marketplace_accounts
            WHERE marketplace_account_id = " . (int)$marketplaceAccountId . "
            LIMIT 1";

    $row = $db->query($sql)->fetch_assoc();
    if (!$row) {
        throw new RuntimeException('Marketplace account not found: ' . $marketplaceAccountId);
    }

    return $row;
}

function refreshAccessToken(array $account): string {
    $clientId = trim((string)($account['client_id'] ?? ''));
    $clientSecret = trim((string)($account['client_secret'] ?? ''));
    $refreshToken = trim((string)($account['refresh_token'] ?? ''));

    if ($clientId === '' || $clientSecret === '' || $refreshToken === '') {
        throw new RuntimeException('Missing client_id/client_secret/refresh_token in oc_marketplace_accounts');
    }

    $url = 'https://api.ebay.com/identity/v1/oauth2/token';
    $headers = [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret),
    ];

    $postFields = http_build_query([
        'grant_type' => 'refresh_token',
        'refresh_token' => $refreshToken,
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 45);
    $response = curl_exec($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new RuntimeException('OAuth cURL error: ' . $curlError);
    }

    $json = json_decode((string)$response, true);
    if (!is_array($json) || empty($json['access_token'])) {
        throw new RuntimeException('OAuth failed HTTP ' . $httpCode . ': ' . (string)$response);
    }

    return (string)$json['access_token'];
}

function getCardByListingAndNumber(mysqli $db, int $listingId, string $cardNumber): array {
    $safeCardNumber = $db->real_escape_string($cardNumber);

    $sql = "SELECT c.card_id, c.listing_id, c.batch_id, c.card_number, c.title, c.player_name,
                   cl.set_name,
                   cld.ebay_item_id
            FROM oc_card c
            LEFT JOIN oc_card_listing cl ON cl.listing_id = c.listing_id
            LEFT JOIN oc_card_listing_description cld
              ON cld.listing_id = c.listing_id
             AND cld.language_id = 1
             AND cld.batch_id = c.batch_id
            WHERE c.listing_id = " . (int)$listingId . "
              AND c.card_number = '" . $safeCardNumber . "'
            ORDER BY c.card_id ASC
            LIMIT 1";

    $row = $db->query($sql)->fetch_assoc();
    if (!$row) {
        // Carte absente de la DB → fake Wayne Gretzky rookie pour test
        return [
            'card_id'      => 0,
            'listing_id'   => $listingId,
            'batch_id'     => 0,
            'card_number'  => $cardNumber,
            'title'        => 'HTF Teemu Selanne The Leaf Set 1993-94 card #13',
            'player_name'  => 'Teemu Selanne',
            'set_name'     => 'The Leaf Set 1993-94',
            'ebay_item_id' => '',
            'site_id'      => 0,
        ];
    }

    return $row;
}

function buildKeyword(array $card): string {
    $keyword = trim((string)($card['title'] ?? ''));
    if ($keyword !== '') {
        return $keyword;
    }

    $parts = [];
    if (!empty($card['set_name'])) {
        $parts[] = trim((string)$card['set_name']);
    }
    if (!empty($card['player_name'])) {
        $parts[] = trim((string)$card['player_name']);
    }
    if (!empty($card['card_number'])) {
        $parts[] = '#' . trim((string)$card['card_number']);
    }

    return trim(implode(' ', $parts));
}

function normalizeKeyword(string $keyword): string {
    return trim($keyword);
}

function buildKeywordCandidates(array $card, string $baseKeyword): array {
    $candidates = [];
    $baseKeyword = normalizeKeyword($baseKeyword);
    if ($baseKeyword !== '') {
        $candidates[] = $baseKeyword;
    }

    if (!empty($card['set_name']) || !empty($card['player_name']) || !empty($card['card_number'])) {
        $parts = [];
        if (!empty($card['set_name'])) {
            $parts[] = trim((string)$card['set_name']);
        }
        if (!empty($card['player_name'])) {
            $parts[] = trim((string)$card['player_name']);
        }
        if (!empty($card['card_number'])) {
            $parts[] = '#' . trim((string)$card['card_number']);
        }

        $candidate = normalizeKeyword(implode(' ', array_filter($parts)));
        if ($candidate !== '') {
            $candidates[] = $candidate;
        }
    }

    if (!empty($card['player_name']) && !empty($card['card_number'])) {
        $candidate = normalizeKeyword(trim((string)$card['player_name']) . ' #' . trim((string)$card['card_number']));
        if ($candidate !== '') {
            $candidates[] = $candidate;
        }
    }

    if (!empty($card['card_number']) && !empty($card['set_name'])) {
        $candidate = normalizeKeyword(trim((string)$card['set_name']) . ' #' . trim((string)$card['card_number']));
        if ($candidate !== '') {
            $candidates[] = $candidate;
        }
    }

    $candidates[] = normalizeKeyword(str_replace('#', ' #', $baseKeyword));
    $candidates[] = normalizeKeyword(str_replace('#', ' ', $baseKeyword));

    $unique = [];
    $seen = [];
    foreach ($candidates as $candidate) {
        $key = mb_strtolower(trim($candidate));
        if ($key === '' || isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $unique[] = $candidate;
    }

    return $unique;
}

function marketplaceHeaderBySiteId(int $siteId): ?string {
    $map = [
        0 => 'EBAY_US',
        2 => 'EBAY_CA',
        3 => 'EBAY_GB',
        71 => 'EBAY_FR',
        77 => 'EBAY_DE',
    ];

    return $map[$siteId] ?? null;
}

function fetchBrowseRaw(string $keyword, string $accessToken, int $siteId, string $sortValue = 'price'): array {
    $query = [
        'q' => $keyword,
        'category_ids' => '261328',
        'auto_correct' => 'KEYWORD',
        'sort' => $sortValue,
        'limit' => 100,
        'offset' => 0,
        'filter' => 'buyingOptions:{AUCTION|FIXED_PRICE}',
    ];

    $url = 'https://api.ebay.com/buy/browse/v1/item_summary/search?' . http_build_query($query);
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    $marketplaceHeader = marketplaceHeaderBySiteId($siteId);
    if ($marketplaceHeader !== null) {
        $headers[] = 'X-EBAY-C-MARKETPLACE-ID: ' . $marketplaceHeader;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $raw = curl_exec($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    return [
        'url' => $url,
        'headers' => $headers,
        'http_code' => $httpCode,
        'curl_error' => $curlError,
        'raw' => (string)$raw,
        'json' => json_decode((string)$raw, true),
    ];
}

function parseBrowseItems(array $rawItems): array {
    $items = [];

    foreach ($rawItems as $item) {
        $title = (string)($item['title'] ?? '');
        $buyingOptions = is_array($item['buyingOptions'] ?? null) ? $item['buyingOptions'] : [];
        $isAuction = false;

        foreach ($buyingOptions as $buyingOption) {
            if (stripos((string)$buyingOption, 'AUCTION') !== false) {
                $isAuction = true;
                break;
            }
        }

        $priceField = $isAuction && isset($item['currentBidPrice']['value']) ? ($item['currentBidPrice'] ?? []) : ($item['price'] ?? []);
        $price = (float)($priceField['value'] ?? 0);
        $currency = (string)($priceField['currency'] ?? 'USD');
        $shippingCost = (float)($item['shippingOptions'][0]['shippingCost']['value'] ?? 0);

        $grader = '';
        $gradeScore = '';
        $grade = '';
        $hasGraderMention = false;
        if (preg_match('/\b(PSA|BGS|BGSX|SGC|CSA|HGA|GAI|ACE|CGC|KSA)\b/i', $title, $mGrader)) {
            $hasGraderMention = true;
            $grader = strtoupper((string)$mGrader[1]);
        }
        if (preg_match('/\b(PSA|BGS|BGSX|SGC|CSA|HGA|GAI|ACE|CGC|KSA)\s+(\d{1,2}(?:\.\d)?)\b/i', $title, $m)) {
            $grader = strtoupper((string)$m[1]);
            $gradeScore = (string)$m[2];
            $grade = $grader . ' ' . $gradeScore;
        }

        $conditionText = strtolower(trim((string)($item['condition'] ?? '')));
        $isConditionUngraded = ($conditionText === 'ungraded');
        $isExplicitUngraded = $isConditionUngraded
            || stripos($title, 'ungraded') !== false
            || stripos($title, 'not graded') !== false;
        $hasGraderAndScore = ($grader !== '' && $gradeScore !== '');
        $isConditionGraded = ($conditionText === 'graded');
        $isGraded = !$isExplicitUngraded && ($hasGraderMention || $hasGraderAndScore);
        if ($isConditionGraded && !$hasGraderMention) {
            $isGraded = false;
        }

        $items[] = [
            'item_id' => (string)($item['legacyItemId'] ?? $item['itemId'] ?? ''),
            'title' => $title,
            'url' => (string)($item['itemWebUrl'] ?? ''),
            'price' => $price,
            'currency' => $currency,
            'shipping_cost' => $shippingCost,
            'total_price' => $price + $shippingCost,
            'is_graded' => $isGraded,
            'listing_type' => $isAuction ? 'AUCTION' : 'FIXED_PRICE',
            'bid_count' => (int)($item['bidCount'] ?? 0),
            'grade' => $grade,
        ];
    }

    return $items;
}

function classifyBuckets(array $items, string $excludeItemId = ''): array {
    $auctionRaw = null;
    $auctionGraded = null;
    $buyNowRaw = null;
    $buyNowGraded = null;

    $isBetter = static function(array $candidate, ?array $current): bool {
        if ($current === null) {
            return true;
        }

        $candidatePrice = (float)($candidate['price'] ?? 0);
        $currentPrice = (float)($current['price'] ?? 0);
        if ($candidatePrice < $currentPrice) {
            return true;
        }
        if ($candidatePrice > $currentPrice) {
            return false;
        }

        $candidateShipping = (float)($candidate['shipping_cost'] ?? 0);
        $currentShipping = (float)($current['shipping_cost'] ?? 0);
        if ($candidateShipping < $currentShipping) {
            return true;
        }
        if ($candidateShipping > $currentShipping) {
            return false;
        }

        return false;
    };

    foreach ($items as $item) {
        $itemId = (string)($item['item_id'] ?? '');
        $itemPrice = (float)($item['price'] ?? 0);
        $itemCurrency = (string)($item['currency'] ?? 'USD');
        $isGraded = !empty($item['is_graded']);
        $isAuction = (($item['listing_type'] ?? '') === 'AUCTION');
        $isOwn = ($excludeItemId !== '' && $itemId === $excludeItemId);

        if ($itemPrice <= 0) {
            continue;
        }

        if ($isAuction) {
            if (!$isGraded) {
                $candidate = [
                    'price' => $itemPrice,
                    'currency' => $itemCurrency,
                    'url' => (string)($item['url'] ?? ''),
                    'bids' => (int)($item['bid_count'] ?? 0),
                    'shipping_cost' => (float)($item['shipping_cost'] ?? 0),
                    'total_price' => (float)($item['total_price'] ?? $itemPrice),
                    'item_id' => $itemId,
                ];

                if ($isBetter($candidate, $auctionRaw)) {
                    $auctionRaw = $candidate;
                }
            }

            if ($isGraded) {
                $candidate = [
                    'price' => $itemPrice,
                    'currency' => $itemCurrency,
                    'url' => (string)($item['url'] ?? ''),
                    'bids' => (int)($item['bid_count'] ?? 0),
                    'shipping_cost' => (float)($item['shipping_cost'] ?? 0),
                    'total_price' => (float)($item['total_price'] ?? $itemPrice),
                    'grade' => (string)($item['grade'] ?? ''),
                    'item_id' => $itemId,
                ];

                if ($isBetter($candidate, $auctionGraded)) {
                    $auctionGraded = $candidate;
                }
            }
        } else {
            if (!$isGraded && !$isOwn) {
                $candidate = [
                    'price' => $itemPrice,
                    'currency' => $itemCurrency,
                    'url' => (string)($item['url'] ?? ''),
                    'shipping_cost' => (float)($item['shipping_cost'] ?? 0),
                    'total_price' => (float)($item['total_price'] ?? $itemPrice),
                    'item_id' => $itemId,
                ];

                if ($isBetter($candidate, $buyNowRaw)) {
                    $buyNowRaw = $candidate;
                }
            }

            if ($isGraded) {
                $candidate = [
                    'price' => $itemPrice,
                    'currency' => $itemCurrency,
                    'url' => (string)($item['url'] ?? ''),
                    'shipping_cost' => (float)($item['shipping_cost'] ?? 0),
                    'total_price' => (float)($item['total_price'] ?? $itemPrice),
                    'grade' => (string)($item['grade'] ?? ''),
                    'item_id' => $itemId,
                ];

                if ($isBetter($candidate, $buyNowGraded)) {
                    $buyNowGraded = $candidate;
                }
            }
        }
    }

    return [
        'auction_raw' => $auctionRaw,
        'auction_graded' => $auctionGraded,
        'buy_now_raw' => $buyNowRaw,
        'buy_now_graded' => $buyNowGraded,
    ];
}

try {
    $input = parseInput();
    $db = db();

    $listingId = (int)$input['listing_id'];
    $cardNumber = (string)$input['card_number'];
    $marketplaceAccountId = (int)$input['marketplace_account_id'];
    $sortField = (string)($input['sort_field'] ?? 'price');
    $ascending = !empty($input['ascending']);
    $sortValue = resolveBrowseSort($sortField, $ascending);

    $card = getCardByListingAndNumber($db, $listingId, $cardNumber);
    $keyword = buildKeyword($card);
    $autoCorrect = true;

    if (mb_strlen($keyword) < 3) {
        throw new RuntimeException('Keyword too short after build ("' . $keyword . '")');
    }

    $account = getMarketplaceAccount($db, $marketplaceAccountId);
    $token = refreshAccessToken($account);

    $siteId = (int)($card['site_id'] ?? 0);
    $excludeItemId = trim((string)($card['ebay_item_id'] ?? ''));

    $triedKeywords = [];
    $usedKeyword = $keyword;
    $result = fetchBrowseRaw($usedKeyword, $token, $siteId, $sortValue);
    $triedKeywords[] = $usedKeyword;

    out('=== INPUT ===');
    out('listing_id=' . $listingId . ' card_number=' . $cardNumber . ' card_id=' . (int)$card['card_id']);
    out('keyword=' . $usedKeyword);
    out('auto_correct=' . ($autoCorrect ? '1' : '0'));
    out('sort_field=' . $sortField);
    out('ascending=' . ($ascending ? '1' : '0'));
    out('browse_sort=' . $sortValue);
    out('api_auto_corrections=' . json_encode((array)($result['json']['autoCorrections'] ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    out('keywords_tried=' . implode(' | ', $triedKeywords));
    out('site_id=' . $siteId . ' exclude_ebay_item_id=' . ($excludeItemId !== '' ? $excludeItemId : '(none)'));
    out('browse_url=' . $result['url']);
    out('http_code=' . (int)$result['http_code'] . ' curl_error=' . ($result['curl_error'] ?: '(none)'));

    out('');
    $rawJson = json_decode((string)$result['raw'], true);
    if (is_array($rawJson)) {
        outPretty('RAW EBAY OUTPUT (array)', $rawJson);
    } else {
        outPretty('RAW EBAY OUTPUT (text)', $result['raw']);
    }

    $itemsRaw = is_array($result['json']) && isset($result['json']['itemSummaries']) && is_array($result['json']['itemSummaries'])
        ? $result['json']['itemSummaries']
        : [];

    $parsedItems = parseBrowseItems($itemsRaw);
    $buckets = classifyBuckets($parsedItems, $excludeItemId);

    out('');
    out('=== SUMMARY ===');
    out('raw_itemSummaries_count=' . count($itemsRaw));
    out('parsed_items_count=' . count($parsedItems));

    out('');
    outPretty('CLASSIFIED BUCKETS', $buckets);

    $outputFile = __DIR__ . '/ebay_raw_listing_' . $listingId . '_card_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $cardNumber) . '.json';
    file_put_contents($outputFile, (string)$result['raw']);
    out('');
    out('Raw JSON saved to: ' . $outputFile);
} catch (Throwable $e) {
    http_response_code(500);
    out('ERROR: ' . $e->getMessage());
}
