<?php
// Original: warehouse/card/market.php
namespace Opencart\Admin\Model\Warehouse\Card;

/**
 * CardMarket — Modèle de recherche eBay Finding API (Completed/Sold items)
 */
class Market extends \Opencart\System\Engine\Model {

    // eBay Trading card category IDs (US site)
    private const CATEGORY_SPORTS_CARDS = '261328';
    private const FINDING_API_ENDPOINT  = 'https://svcs.ebay.com/services/search/FindingService/v1';

    // Grading companies keywords
    private const GRADERS = ['psa', 'bgs', 'bgsx', 'sgc', 'csa', 'hga', 'gai', 'ace', 'cgc', 'ksa'];

    // Cache TTL in seconds (1 hour)
    private const CACHE_TTL = 3600;

    private function getImageSearchDirectory(): string {
        return rtrim(DIR_STORAGE, '/\\') . '/market_image_search/';
    }

    public function getImageSearchFile(string $token): ?string {
        if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
            return null;
        }

        $matches = glob($this->getImageSearchDirectory() . $token . '.*');

        return !empty($matches[0]) && is_file($matches[0]) ? $matches[0] : null;
    }

    public function saveUploadedSearchImage(array $file): array {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
            throw new \Exception($this->language->get('error_image_upload'));
        }

        $imageInfo = @getimagesize($file['tmp_name']);
        if (!$imageInfo || empty($imageInfo['mime'])) {
            throw new \Exception($this->language->get('error_image_invalid'));
        }

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowed[$imageInfo['mime']])) {
            throw new \Exception($this->language->get('error_image_invalid'));
        }

        if ((int)($file['size'] ?? 0) <= 0 || (int)$file['size'] > 8 * 1024 * 1024) {
            throw new \Exception($this->language->get('error_image_invalid'));
        }

        $directory = $this->getImageSearchDirectory();
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \Exception($this->language->get('error_image_upload'));
        }

        $token = bin2hex(random_bytes(16));
        $filePath = $directory . $token . '.' . $allowed[$imageInfo['mime']];

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new \Exception($this->language->get('error_image_upload'));
        }

        foreach (glob($directory . '*') ?: [] as $existing) {
            if (is_file($existing) && filemtime($existing) < (time() - self::CACHE_TTL)) {
                @unlink($existing);
            }
        }

        return ['token' => $token, 'path' => $filePath];
    }

   


    /**
     * Search eBay items by selected mode (present via Browse API, sold via scraper API)
     */
    public function searchCompletedItems(array $filters): array {
        $this->load->model('warehouse/marketplace/ebay/api');

        $parts = [];
        if (!empty($filters['filter_player'])) $parts[] = trim($filters['filter_player']);
        if (!empty($filters['filter_set'])) $parts[] = trim($filters['filter_set']);
        elseif (!empty($filters['filter_year'])) $parts[] = trim($filters['filter_year']);
        if (!empty($filters['filter_brand']) && empty($filters['filter_set'])) $parts[] = trim($filters['filter_brand']);
        if (!empty($filters['filter_sport'])) $parts[] = trim($filters['filter_sport']);
        if (!empty($filters['filter_card_number'])) $parts[] = '#' . trim($filters['filter_card_number']);

        $cond = $filters['filter_condition'] ?? 'all';
        $grader = $filters['filter_grader'] ?? 'all';
        $grade = trim((string)($filters['filter_grade'] ?? ''));

        $keyword = !empty($filters['keyword']) ? $filters['keyword'] : implode(' ', array_filter($parts));
        $siteId = (int)($filters['filter_site_id'] ?? $filters['site_id'] ?? 0);
        $limit = min((int)($filters['limit'] ?? 100), 200);
        $page = max((int)($filters['page'] ?? 1), 1);
        $sort = $filters['sort'] ?? 'price_desc';
        $listingType = $filters['filter_listing_type'] ?? 'all';
        $imageToken = trim((string)($filters['filter_image_token'] ?? ''));

        $saleMode = strtolower(trim((string)($filters['filter_sale_mode'] ?? 'present')));
        if (!in_array($saleMode, ['present', 'sold'], true)) {
            $saleMode = 'present';
        }

        // $this->log->write('[CardMarketModel] searchCompletedItems start mode=' . $saleMode . ' keyword=' . $keyword . ' listing=' . $listingType . ' condition=' . $cond . ' grader=' . $grader . ' grade=' . $grade . ' site=' . $siteId . ' page=' . $page . ' limit=' . $limit . ' image=' . ($imageToken !== '' ? '1' : '0'));

        $cacheKey = md5($saleMode . '|' . $keyword . '|' . $imageToken . '|' . $siteId . '|' . $limit . '|' . $page . '|' . $sort . '|' . $listingType . '|' . $cond . '|' . $grader . '|' . $grade);
        $cacheFile = sys_get_temp_dir() . '/ebay_market_' . $cacheKey . '.json';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < self::CACHE_TTL) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            if ($cached) {
                // $this->log->write('[CardMarketModel] cache hit key=' . $cacheKey . ' total=' . (int)($cached['total'] ?? 0) . ' items=' . count($cached['items'] ?? []));
                return $cached;
            }
        }

        if ($imageToken !== '' && $saleMode === 'present') {
            $imagePath = $this->getImageSearchFile($imageToken);

            if (!$imagePath) {
                // $this->log->write('[CardMarketModel] image token invalid/not found token=' . $imageToken);
                return [
                    'items' => [],
                    'total' => 0,
                    'total_found' => 0,
                    'pages' => 0,
                    'keyword' => $this->language->get('text_image_search'),
                    'error' => $this->language->get('error_image_not_found'),
                ];
            }

            // $this->log->write('[CardMarketModel] branch=present_by_image path=' . $imagePath);
            $r = $this->model_warehouse_marketplace_ebay_api->searchByImageItems($imagePath, [
                'sort' => $sort,
                'limit' => $limit,
                'page' => $page,
                'site_id' => $siteId,
                'listing_type' => $listingType,
                'condition_type' => $cond,
                'grader' => $grader,
                'grade' => $grade,
                'category_id' => self::CATEGORY_SPORTS_CARDS,
            ]);
        } else {
            if ($saleMode === 'sold') {
                if ($keyword === '') {
                    // $this->log->write('[CardMarketModel] sold mode aborted due to empty keyword');
                    return [
                        'items' => [],
                        'total' => 0,
                        'total_found' => 0,
                        'pages' => 0,
                        'keyword' => '',
                        'error' => $this->language->get('error_keyword_required'),
                    ];
                }

                // $this->log->write('[CardMarketModel] branch=sold_scraper keyword=' . $keyword);
                $r = $this->model_warehouse_marketplace_ebay_api->searchSoldItemsScraper($keyword, [
                    'sort' => $sort,
                    'limit' => $limit,
                    'page' => $page,
                    'site_id' => $siteId,
                    'listing_type' => $listingType,
                    'condition_type' => $cond,
                    'grader' => $grader,
                    'grade' => $grade,
                    'category_id' => self::CATEGORY_SPORTS_CARDS,
                ]);
            } else {
                // $this->log->write('[CardMarketModel] branch=present_browse keyword=' . $keyword);
                $r = $this->model_warehouse_marketplace_ebay_api->searchActiveItems($keyword, [
                    'sort' => $sort,
                    'limit' => $limit,
                    'page' => $page,
                    'site_id' => $siteId,
                    'listing_type' => $listingType,
                    'condition_type' => $cond,
                    'grader' => $grader,
                    'grade' => $grade,
                    'category_id' => self::CATEGORY_SPORTS_CARDS,
                ]);
            }
        }

        if (!empty($r['error'])) {
            // $this->log->write('[CardMarketModel] upstream error=' . (string)$r['error']);
            return [
                'items' => [],
                'total' => 0,
                'total_found' => 0,
                'pages' => 0,
                'keyword' => $keyword,
                'error' => $r['error'],
            ];
        }

        $total = (int)($r['total'] ?? 0);
        $result = [
            'items' => $r['items'] ?? [],
            'total' => $total,
            'total_found' => $total,
            'pages' => (int)ceil($total / ($limit ?: 1)),
            'keyword' => ($imageToken !== '' && $saleMode === 'present') ? $this->language->get('text_image_search') : $keyword,
            'error' => '',
        ];

        file_put_contents($cacheFile, json_encode($result));
        // $this->log->write('[CardMarketModel] done total=' . $total . ' items=' . count($result['items']) . ' cache_key=' . $cacheKey);
        return $result;
    }

    /**
     * Parse a single eBay item from Finding API response (kept as fallback)
     */
    private function parseItem(\SimpleXMLElement $item, string $condType): array {
        $itemId    = (string)($item->itemId ?? '');
        $title     = (string)($item->title ?? '');
        $viewUrl   = (string)($item->viewItemURL ?? '');
        $galleryUrl= (string)($item->galleryURL ?? '');
        // Try larger picture
        $pictureUrl = (string)($item->pictureURLLarge ?? $item->galleryURL ?? '');
        $condition  = (string)($item->condition->conditionDisplayName ?? '');

        // Price (sellingStatus)
        $currentPrice  = (float)($item->sellingStatus->currentPrice ?? 0);
        $currency      = (string)($item->sellingStatus->currentPrice['currencyId'] ?? 'USD');
        $sellingState  = (string)($item->sellingStatus->sellingState ?? '');
        $endTime       = (string)($item->listingInfo->endTime ?? '');

        // Convert endTime to readable
        $dateSold = $endTime ? (new \DateTime($endTime))->format('Y-m-d H:i') : '';

        // Parse item specifics
        $specifics = $this->parseSpecifics($item->attribute ?? null, $item->itemSpecifics->nameValueList ?? null);

        // Auto-detect grade, player, card number from title if not in specifics
        if (empty($specifics['grade'])) {
            $specifics = array_merge($specifics, $this->parseGradeFromTitle($title));
        }
        if (empty($specifics['player'])) {
            // Try to detect from title
            $specifics['player'] = '';
        }

        return [
            'item_id'      => $itemId,
            'title'        => $title,
            'url'          => $viewUrl,
            'picture'      => $pictureUrl,
            'gallery'      => $galleryUrl,
            'price'        => $currentPrice,
            'currency'     => $currency,
            'condition'    => $condition,
            'selling_state'=> $sellingState,
            'date_sold'    => $dateSold,
            'grade'        => $specifics['grade'] ?? '',
            'grader'       => $specifics['grader'] ?? '',
            'grade_score'  => $specifics['grade_score'] ?? '',
            'player'       => $specifics['player'] ?? '',
            'card_number'  => $specifics['card_number'] ?? '',
            'set_name'     => $specifics['set_name'] ?? '',
            'year'         => $specifics['year'] ?? '',
            'team'         => $specifics['team'] ?? '',
            'sport'        => $specifics['sport'] ?? '',
            'is_graded'    => !empty($specifics['grader']),
        ];
    }

    /**
     * Parse nameValueList specifics from Finding API
     */
    private function parseSpecifics($attributes, $nameValueList): array {
        $result = [
            'grade'       => '',
            'grader'      => '',
            'grade_score' => '',
            'player'      => '',
            'card_number' => '',
            'set_name'    => '',
            'year'        => '',
            'team'        => '',
            'sport'       => '',
        ];

        $map = [
            'grade'          => ['Grade', 'Card Grade', 'Grade (Numerical)', 'Certification Number'],
            'grader'         => ['Professional Grader', 'Grading Company', 'Grader', 'Certification Company'],
            'player'         => ['Player', 'Player/Athlete', 'Athlete', 'Featured Person/Artist', 'Featured Player'],
            'card_number'    => ['Card Number', 'Card #', 'Card No.', 'Card no.'],
            'set_name'       => ['Set', 'Card Set'],
            'year'           => ['Season', 'Year Manufactured', 'Year'],
            'team'           => ['Team', 'Team Name', 'Team/Country'],
            'sport'          => ['Sport', 'League'],
        ];

        $source = $nameValueList ?? $attributes;
        if (!$source) return $result;

        $pairs = [];
        foreach ($source as $nvl) {
            $name  = strtolower(trim((string)($nvl->name ?? $nvl->attributeName ?? '')));
            $value = trim((string)($nvl->value ?? $nvl->attributeValue ?? ''));
            if ($name && $value) {
                $pairs[$name] = $value;
            }
        }

        foreach ($map as $key => $labels) {
            foreach ($labels as $label) {
                $lc = strtolower($label);
                if (isset($pairs[$lc]) && $pairs[$lc] !== '') {
                    $result[$key] = $pairs[$lc];
                    break;
                }
            }
        }

        // Parse "PSA 10" type grade into grader + score
        if (!empty($result['grade']) && empty($result['grader'])) {
            $parsed = $this->splitGraderScore($result['grade']);
            if ($parsed) {
                $result['grader']      = $parsed['grader'];
                $result['grade_score'] = $parsed['score'];
            }
        } elseif (!empty($result['grader'])) {
            // Normalize grader name
            $result['grader'] = strtoupper($result['grader']);
            // grade might be numeric score
        }

        return $result;
    }

    /**
     * Try to extract grade info from title string
     * e.g. "1994 Bowman #92 Anthony Newman PSA 9" → grader=PSA, score=9
     */
    private function parseGradeFromTitle(string $title): array {
        $result = ['grade' => '', 'grader' => '', 'grade_score' => ''];
        $graderPattern = implode('|', array_map('strtoupper', self::GRADERS));
        // Pattern: "PSA 10", "BGS 9.5", "SGC 8"
        if (preg_match('/\b(' . $graderPattern . ')\s+(\d{1,2}(?:\.\d)?)\b/i', $title, $m)) {
            $result['grader']      = strtoupper($m[1]);
            $result['grade_score'] = $m[2];
            $result['grade']       = strtoupper($m[1]) . ' ' . $m[2];
        }
        return $result;
    }

    /**
     * Split "PSA 10" into grader=PSA, score=10
     */
    private function splitGraderScore(string $grade): ?array {
        $graderPattern = implode('|', array_map('strtoupper', self::GRADERS));
        if (preg_match('/^(' . $graderPattern . ')\s*(\d{1,2}(?:\.\d)?)?$/i', trim($grade), $m)) {
            return [
                'grader' => strtoupper($m[1]),
                'score'  => $m[2] ?? '',
            ];
        }
        return null;
    }

    /**
     * eBay site_id → Marketplace ID string (REST API format)
     */
    private function getMarketplaceId(int $siteId): string {
        $map = [
            0   => 'EBAY_US',
            2   => 'EBAY_ENCA',
            3   => 'EBAY_GB',
            77  => 'EBAY_DE',
            71  => 'EBAY_FR',
            101 => 'EBAY_IT',
            186 => 'EBAY_ES',
        ];
        return $map[$siteId] ?? 'EBAY_US';
    }

    /**
     * eBay site_id → Finding API Global ID (kept for reference)
     */
    private function getSiteGlobalId(int $siteId): string {
        $map = [
            0  => 'US',
            2  => 'ENCA',   // Canada
            3  => 'GB',
            77 => 'DE',
            71 => 'FR',
            101=> 'IT',
            186=> 'ES',
        ];
        return $map[$siteId] ?? 'US';
    }

    /**
     * Get eBay API credentials — delegates to ebay model (handles bearer token + cookie refresh)
     */
    private function getEbayCredentials(): array {
        $this->load->model('warehouse/marketplace/ebay/api');
        return $this->model_warehouse_marketplace_ebay_api->getApiCredentials(1);
    }

    /**
     * Refresh OAuth bearer token (fallback if ebay model unavailable)
     */
    private function refreshBearerToken(array $cred): string {
        $encoded = base64_encode($cred['client_id'] . ':' . $cred['client_secret']);
        $ch = curl_init('https://api.ebay.com/identity/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type'    => 'refresh_token',
            'refresh_token' => $cred['refresh_token'],
            'scope'         => 'https://api.ebay.com/oauth/api_scope',
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . $encoded,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $resp = curl_exec($ch);
        
        $data = json_decode($resp, true);
        return $data['access_token'] ?? '';
    }

    /**
     * cURL helper — GET when body empty, POST otherwise
     */
    private function curlPost(string $url, array $headers, string $body): string {
        $ch = curl_init($url);
        if ($body !== '') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($ch);
        
        return $response ?: '';
    }
}
