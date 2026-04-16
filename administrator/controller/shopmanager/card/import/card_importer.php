<?php
namespace Opencart\Admin\Controller\Shopmanager\Card\Import;

class CardImporter extends \Opencart\System\Engine\Controller {
    
    public function index(): void {
        $this->load->language('shopmanager/card/import/card_importer');
        $data = [];
        
        
        $this->document->setTitle(($lang['heading_title'] ?? ''));
        
        $data['heading_title'] = ($lang['heading_title'] ?? '');
        
        $user_token = isset($this->session->data['user_token']) ? $this->session->data['user_token'] : '';
        
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => ($lang['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $user_token)
        ];
        $data['breadcrumbs'][] = [
            'text' => ($lang['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/card/import/card_importer', 'user_token=' . $user_token)
        ];
        
        // Language strings for Twig
        $data['text_upload_instructions'] = ($lang['text_upload_instructions'] ?? '');
        $data['text_csv_file'] = ($lang['text_csv_file'] ?? '');
        $data['text_csv_format'] = ($lang['text_csv_format'] ?? '');
        $data['text_preview_title'] = ($lang['text_preview_title'] ?? '');
        $data['text_listing_configuration'] = ($lang['text_listing_configuration'] ?? '');
        $data['text_listing_type'] = ($lang['text_listing_type'] ?? '');
        $data['text_multi_variation'] = ($lang['text_multi_variation'] ?? '');
        $data['text_single_listings'] = ($lang['text_single_listings'] ?? '');
        $data['text_upload_success'] = ($lang['text_upload_success'] ?? '');
        $data['text_generate_success'] = ($lang['text_generate_success'] ?? '');
        $data['text_generation_complete'] = ($lang['text_generation_complete'] ?? '');
        $data['text_ebay_file_ready'] = ($lang['text_ebay_file_ready'] ?? '');
        $data['text_uploading'] = ($lang['text_uploading'] ?? '');
        $data['text_upload_modal_title'] = ($lang['text_upload_modal_title'] ?? '');
        $data['text_upload_modal_subtitle'] = ($lang['text_upload_modal_subtitle'] ?? '');
        $data['text_upload_modal_hint'] = ($lang['text_upload_modal_hint'] ?? '');
        $data['text_grading_potential_detected'] = ($lang['text_grading_potential_detected'] ?? '');
        $data['text_grading_listing_menu_hint'] = ($lang['text_grading_listing_menu_hint'] ?? '');
        $data['text_grading_group_badge'] = ($lang['text_grading_group_badge'] ?? '');
        $data['text_generating'] = ($lang['text_generating'] ?? '');
        $data['text_saving'] = ($lang['text_saving'] ?? '');
        $data['text_error'] = ($lang['text_error'] ?? '');
        $data['text_brand_mismatch_block_save'] = ($lang['text_brand_mismatch_block_save'] ?? '');
        $data['text_total_cards'] = ($lang['text_total_cards'] ?? '');
        $data['text_with_images'] = ($lang['text_with_images'] ?? '');
        $data['text_without_images'] = ($lang['text_without_images'] ?? '');
        $data['text_price_range'] = ($lang['text_price_range'] ?? '');
        $data['text_placeholder_listing_title'] = ($lang['text_placeholder_listing_title'] ?? '');
        $data['text_confirm_cancel'] = ($lang['text_confirm_cancel'] ?? '');
        
        // Entry labels
        $data['entry_listing_title'] = ($lang['entry_listing_title'] ?? '');
        $data['entry_category'] = ($lang['entry_category'] ?? '');
        $data['entry_condition'] = ($lang['entry_condition'] ?? '');
        $data['entry_shipping_price'] = ($lang['entry_shipping_price'] ?? '');
        $data['entry_handling_time'] = ($lang['entry_handling_time'] ?? '');
        
        // Buttons
        $data['button_upload'] = ($lang['button_upload'] ?? '');
        $data['button_generate'] = ($lang['button_generate'] ?? '');
        $data['button_download'] = ($lang['button_download'] ?? '');
        $data['button_cancel'] = ($lang['button_cancel'] ?? '');
        $data['button_confirm_save'] = ($lang['button_confirm_save'] ?? '');
        $data['button_save_to_db'] = ($lang['button_save_to_db'] ?? '');
        
        // Drop zone & auto-group notice
        $data['text_drop_here'] = ($lang['text_drop_here'] ?? '');
        $data['text_auto_grouped'] = ($lang['text_auto_grouped'] ?? '');
        $data['text_auto_grouped_desc'] = ($lang['text_auto_grouped_desc'] ?? '');
        
        // eBay policies section
        $data['text_ebay_policies'] = ($lang['text_ebay_policies'] ?? '');
        $data['text_configured_auto'] = ($lang['text_configured_auto'] ?? '');
        
        // Save confirmation modal
        $data['text_save_confirm_title'] = ($lang['text_save_confirm_title'] ?? '');
        $data['text_save_confirm_desc'] = ($lang['text_save_confirm_desc'] ?? '');
        $data['text_ebay_disabled'] = ($lang['text_ebay_disabled'] ?? '');
        
        // Help texts
        $data['help_listing_type'] = ($lang['help_listing_type'] ?? '');
        $data['help_listing_title'] = ($lang['help_listing_title'] ?? '');
        $data['help_category'] = ($lang['help_category'] ?? '');
        
        // Errors
        $data['error_no_data'] = ($lang['error_no_data'] ?? '');
        $data['error_ajax'] = ($lang['error_ajax'] ?? '');
        
        // Import Results Modal
        $data['text_import_results'] = ($lang['text_import_results'] ?? '');
        $data['text_import_summary'] = ($lang['text_import_summary'] ?? '');
        $data['button_close'] = ($lang['button_close'] ?? '');
        
        // Modal Dialogs
        $data['text_success'] = ($lang['text_success'] ?? '');
        $data['text_view_listing'] = ($lang['text_view_listing'] ?? '');
        $data['text_view_all_listings'] = ($lang['text_view_all_listings'] ?? '');
        $data['text_upload_error'] = ($lang['text_upload_error'] ?? '');
        $data['text_save_error'] = ($lang['text_save_error'] ?? '');
        $data['text_save_success'] = ($lang['text_save_success'] ?? '');
        $data['text_save_success_reload'] = ($lang['text_save_success_reload'] ?? '');
        $data['text_no_data_error'] = ($lang['text_no_data_error'] ?? '');
        $data['button_yes'] = ($lang['button_yes'] ?? '');
        $data['button_no'] = ($lang['button_no'] ?? '');
        $data['button_ok'] = ($lang['button_ok'] ?? '');
        
        // URLs
        $data['upload'] = html_entity_decode($this->url->link('shopmanager/card/import/card_importer.upload', 'user_token=' . $user_token), ENT_QUOTES, 'UTF-8');
        $data['generate'] = html_entity_decode($this->url->link('shopmanager/card/import/card_importer.generate', 'user_token=' . $user_token), ENT_QUOTES, 'UTF-8');
        $data['user_token'] = $user_token;
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('shopmanager/card/import/card_importer', $data));
    }
    
    /**
     * Main upload endpoint - HTTP entry point
     * Validates request, processes file, returns JSON response
     */
    public function upload(): void {
        $this->load->language('shopmanager/card/import/card_importer');
        $data = [];
        
        
        $json = [];
        
        try {
            // Step 1: Validate request
            $validation_error = $this->validateUploadRequest();
            if ($validation_error) {
                $json['error'] = $validation_error;
                $this->sendJsonResponse($json);
                return;
            }
            
            // Step 2: Process uploaded file
            $json = $this->processUploadedFile();
            
        } catch (\Exception $e) {
            $json['error'] = 'Exception: ' . $e->getMessage();
        }
        
        $this->sendJsonResponse($json);
    }
    
    /**
     * Validates upload request (permissions, file presence)
     * @return string|null Error message or null if valid
     */
    private function validateUploadRequest(): ?string {
        // Check permission
        if (!$this->user->hasPermission('modify', 'shopmanager/card/import/card_importer')) {
            return ($lang['error_permission'] ?? '');
        }
        
        // Check file presence
        if (!isset($this->request->files['file']['name']) || empty($this->request->files['file']['name'])) {
            return ($lang['error_no_file'] ?? '');
        }
        
        return null;
    }
    
    /**
     * Processes the uploaded CSV file
     * @return array JSON response data
     */
    private function processUploadedFile(): array {
        $file = $this->request->files['file'];
        
        // Check PHP upload error (use != instead of !== because error might be string)
        if ((int)$file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => $this->getUploadErrorMessage((int)$file['error'])];
        }
        
        // Increase limits for large files
        set_time_limit(300);
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');
        
        // Step 1: Parse CSV (model handles parsing logic)
        $this->load->model('shopmanager/card/card_listing');
        $parse_result = $this->model_shopmanager_card_card_listing->parseCSV($file['tmp_name']);
        if (!empty($parse_result['error'])) {
            return ['error' => $parse_result['error']];
        }
        
        $cards = $parse_result['data'];
        
        // Step 1.5: Lookup prices from oc_card_price DB (card_number + player [+ brand])
        $cards = $this->lookupAndSetPrices($cards);
        
        // Step 2: Group cards (model handles grouping logic)
        $groups = $this->model_shopmanager_card_card_listing->smartGroupCards($cards);
        //error_log('Grouped Cards: ' . print_r($groups, true) . "\n", 3, '/home/n7f9655/public_html/storage_phoenixliquidation/logs/debug.log');       

        // Step 2.5: Check for existing listings
        $groups = $this->checkExistingListings($groups);
        // Step 2.6: Fetch eBay market prices in backend and inject directly into preview cards
        $groups = $this->injectPreviewMarketPrices($groups);
        //error_log('Groups After Checking Existing Listings: ' . print_r($groups, true) . "\n", 3, '/home/n7f9655/public_html/storage_phoenixliquidation/logs/debug.log');
        // Step 3: Calculate statistics
        $stats = $this->calculateStatistics($cards, $groups);
        
        // Step 4: Format response (data ready for JS display)
        return [
            'success' => true,
            'total' => count($cards),
            'stats' => $stats,
            'groups' => $groups,
            'html' => $this->getPreviewTable($groups),
            'debug' =>  $parse_result
            
        ];
    }

    private function injectPreviewMarketPrices(array $groups): array {
        $this->load->model('shopmanager/ebay');

        $rateLimited = false;
        $rateLimitedError = '';

        foreach ($groups as &$group) {
            if (!isset($group['cards']) || !is_array($group['cards'])) {
                continue;
            }

            foreach ($group['cards'] as &$card) {
                $payload = [
                    'keyword' => (string)($card['keyword'] ?? ''),
                    'title' => (string)($card['title'] ?? ''),
                    'player' => (string)($card['player'] ?? ''),
                    'card_number' => (string)($card['card_number'] ?? ''),
                    'set_name' => (string)($card['set'] ?? $group['set'] ?? ''),
                ];

                $keyword = $this->buildPreviewKeyword($payload);
                $card['market_keyword'] = $keyword;
                $card['market_rate_limited'] = false;
                $card['market_api_error'] = '';
                $card['price_sold'] = null;
                $card['price_sold_url'] = '';
                $card['price_sold_bids'] = 0;
                $card['price_sold_graded'] = null;
                $card['price_sold_graded_url'] = '';
                $card['price_sold_graded_bids'] = 0;
                $card['price_sold_graded_grade'] = '';
                $card['price_list'] = null;
                $card['price_list_url'] = '';
                $card['price_list_graded'] = null;
                $card['price_list_graded_url'] = '';
                $card['price_list_graded_grade'] = '';
                $card['market_profit_potential'] = null;
                $card['market_is_profitable'] = false;

                if ($keyword === '' || strlen($keyword) < 3) {
                    $card['market_api_error'] = 'keyword too short (min 3 chars)';
                    continue;
                }

                if ($rateLimited) {
                    $card['market_rate_limited'] = true;
                    $card['market_api_error'] = $rateLimitedError !== '' ? $rateLimitedError : 'Rate limited';
                    continue;
                }

                $rowResult = $this->getPreviewMarketPrices($keyword);

                if (!empty($rowResult['rate_limited'])) {
                    $rateLimited = true;
                    $rateLimitedError = (string)($rowResult['error'] ?? $rowResult['api_error'] ?? 'Rate limited');
                    $card['market_rate_limited'] = true;
                    $card['market_api_error'] = $rateLimitedError;
                    continue;
                }

                if (!empty($rowResult['success'])) {
                    $card['price_sold'] = $rowResult['price_sold'] ?? null;
                    $card['price_sold_url'] = (string)($rowResult['price_sold_url'] ?? '');
                    $card['price_sold_bids'] = (int)($rowResult['price_sold_bids'] ?? 0);
                    $card['price_sold_graded'] = $rowResult['price_sold_graded'] ?? null;
                    $card['price_sold_graded_url'] = (string)($rowResult['price_sold_graded_url'] ?? '');
                    $card['price_sold_graded_bids'] = (int)($rowResult['price_sold_graded_bids'] ?? 0);
                    $card['price_sold_graded_grade'] = (string)($rowResult['price_sold_graded_grade'] ?? '');
                    $card['price_list'] = $rowResult['price_list'] ?? null;
                    $card['price_list_url'] = (string)($rowResult['price_list_url'] ?? '');
                    $card['price_list_graded'] = $rowResult['price_list_graded'] ?? null;
                    $card['price_list_graded_url'] = (string)($rowResult['price_list_graded_url'] ?? '');
                    $card['price_list_graded_grade'] = (string)($rowResult['price_list_graded_grade'] ?? '');
                    $card['market_api_error'] = (string)($rowResult['api_error'] ?? '');

                    $salePrice = (float)($card['sale_price'] ?? 0);
                    $gradedCandidates = [];
                    if (is_numeric($card['price_sold_graded'] ?? null) && (float)$card['price_sold_graded'] > 0 && (int)($card['price_sold_graded_bids'] ?? 0) > 0) {
                        $gradedCandidates[] = (float)$card['price_sold_graded'];
                    }
                    // Buy now graded is valid even when grade label is empty (ex: grader found but no numeric grade)
                    if (is_numeric($card['price_list_graded'] ?? null) && (float)$card['price_list_graded'] > 0) {
                        $gradedCandidates[] = (float)$card['price_list_graded'];
                    }

                    if (!empty($gradedCandidates)) {
                        $bestGraded = min($gradedCandidates);
                        $ebayFeeRate = 0.13;
                        $psaCertificationCad = 55.0;
                        $netAfterFeesAndPsa = $bestGraded - ($bestGraded * $ebayFeeRate) - $psaCertificationCad;
                        $profit = $netAfterFeesAndPsa - $salePrice;
                        $card['market_profit_potential'] = round($profit, 2);
                        $card['market_is_profitable'] = ($profit > 0);
                    }
                } else {
                    $card['market_api_error'] = (string)($rowResult['error'] ?? $rowResult['api_error'] ?? 'unknown error');
                }
            }
            unset($card);
        }
        unset($group);

        return $groups;
    }
    
    /**
     * Check if listings already exist for each group
     * @param array $groups Array of card groups
     * @return array Groups with existing_listing_id field added
     */
    private function checkExistingListings(array $groups): array {
        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/card/card_type');
        
        foreach ($groups as &$group) {
            // Listing exists = au moins un titre de carte trouvé dans oc_card
            $titles = array_column($group['cards'], 'title');
            $existing_id = $this->model_shopmanager_card_card_listing->findListingByCardTitles($titles);

            // Fallback : chercher par set_name + card_type_id si titre non trouvé
            $group['set_name_match'] = false;
            if ($existing_id <= 0) {
                $set_name = trim($group['set_name'] ?? $group['set'] ?? '');
                $category = trim($group['cards'][0]['category'] ?? '');
                if ($set_name !== '') {
                    $card_type_id = $this->model_shopmanager_card_card_type->getCardTypeIdByName($category);
                    $existing_id  = $this->model_shopmanager_card_card_listing->findListingBySetName($set_name, $card_type_id);
                    if ($existing_id > 0) {
                        $group['set_name_match'] = true;
                    }
                }
            }

            $group['existing_listing_id'] = $existing_id;
            $group['is_existing'] = ($existing_id > 0);

            // If listing exists, fetch location + flag each card
            if ($existing_id > 0) {
                $group['existing_location'] = $this->model_shopmanager_card_card_listing->getListingLocation($existing_id);
                $existing_cards = $this->model_shopmanager_card_card_listing->getExistingCardsForListing($existing_id);
                foreach ($group['cards'] as &$card) {
                    // merge=1 when price < 10 (same rule as twig hidden input)
                    $merge       = (isset($card['sale_price']) && (float)$card['sale_price'] < 10) ? 1 : 0;
                    $card_number = trim($card['card_number'] ?? '');
                    $csv_title   = strtolower(trim($card['title'] ?? ''));
                    $card['db_exists']   = false;
                    $card['db_quantity'] = 0;
                    if ($merge === 1 && $card_number !== '' && isset($existing_cards[$card_number])) {
                        // Substring match: DB title must be contained IN the CSV title
                        // "Hakeem Olajuwon Gold" (DB) NOT in "Hakeem Olajuwon" (CSV) → no false positive
                        // "Hakeem Olajuwon Gold" (DB) IS in "1993-94 Topps #2 Hakeem Olajuwon Gold" (CSV) → match
                        foreach ($existing_cards[$card_number] as $db_card) {
                            $db_title = strtolower($db_card['title']);
                            if ($csv_title !== '' && str_contains($csv_title, $db_title)) {
                                $card['db_exists']   = true;
                                $card['db_quantity'] = $db_card['quantity'];
                                break;
                            }
                        }
                    }
                }
                unset($card);
            }

            // Compute total value for this group (qty * price per card)
            $group_total = 0.0;
            foreach ($group['cards'] as $c) {
                $group_total += (float)($c['sale_price'] ?? 0) * max(1, (int)($c['quantity'] ?? 1));
            }
            $group['group_total_value'] = round($group_total, 2);
        }
        unset($group);
        return $groups;
    }
    
    /**
     * Lookup card price from oc_card_set (card_set_importer module)
     * Matches by card_number + player, and by brand if provided.
     * Falls back to $0.99 minimum when no DB match found.
     * @param array $cards Array of card data
     * @return array Cards with sale_price set from DB
     */
    private function lookupAndSetPrices(array $cards): array {
        $this->load->model('shopmanager/card/import/card_set_importer');

        foreach ($cards as &$card) {
            $card_number = trim($card['card_number'] ?? '');
            $player      = trim($card['player']      ?? '');
            $brand       = trim($card['brand']       ?? '');
            $year        = trim($card['year']        ?? '');
            $category    = trim($card['category']    ?? '');

            // Get ALL matching rows first
            $rows = $this->model_shopmanager_card_import_card_set_importer
                ->getPriceRowsByCard($card_number, $player, $brand, $year, $category);

            // DEBUG: calculate old CSV price for comparison display
            $csv_market = isset($card['market_price']) ? floatval($card['market_price']) : 0;
            $csv_sale   = isset($card['sale_price'])   ? floatval($card['sale_price'])   : 0;
            if ($csv_market >= 0.99 && $csv_market > $csv_sale) {
                $card['debug_csv_price'] = number_format($this->currency->convert($csv_market, 'USD', 'CAD'), 2, '.', '') . ' (mkt)';
            } elseif ($csv_sale > 0.99) {
                $card['debug_csv_price'] = number_format($this->currency->convert($csv_sale, 'USD', 'CAD'), 2, '.', '') . ' (sale)';
            } else {
                $card['debug_csv_price'] = '0.99 (min)';
            }

            // Multiple matches → let user choose
            if (count($rows) > 1) {
                $candidates = [];
                foreach ($rows as $row) {
                    $usd = $this->model_shopmanager_card_import_card_set_importer->getLowestImporterUsdPriceFromRow($row);
                    $cad = $this->currency->convert($usd, 'USD', 'CAD');
                    $candidates[] = [
                        'label'    => trim(implode(' · ', array_filter([
                                          $row['set_name'] ?? '',
                                          $row['year']     ?? '',
                                          $row['brand']    ?? '',
                                          $row['category'] ?? '',
                                          $row['subset']   ?? '',
                                      ]))),
                        'usd'      => number_format($usd, 2, '.', ''),
                        'cad'      => number_format($cad, 2, '.', ''),
                    ];
                }
                $card['price_source']     = 'multiple';
                $card['price_candidates'] = $candidates;
                $card['sale_price']       = ''; // force user to pick
                continue;
            }

            $db_price = count($rows) === 1
                ? $this->model_shopmanager_card_import_card_set_importer->getLowestImporterUsdPriceFromRow($rows[0])
                : 0.0;

            if ($db_price >= 0.99) {
                // Real DB price in USD — convert to CAD
                $cad_price = $this->currency->convert($db_price, 'USD', 'CAD');
                $card['sale_price'] = number_format($cad_price, 2, '.', '');
                $card['original_price_usd'] = number_format($db_price, 2, '.', '');
                $card['price_source'] = 'db';
            } elseif ($db_price > 0) {
                // DB match found but < $0.99 — apply minimum
                 $cad_price = $this->currency->convert($db_price, 'USD', 'CAD');
                $card['sale_price'] =  $cad_price>=.99?$cad_price:'0.99';
                $card['price_source'] = 'db_min';
            } else {
                // No DB match (returned 0) — fallback to CSV prices
                $market_price = $csv_market;
                $sale_price   = $csv_sale;

                if ($market_price >= 0.99 && $market_price > $sale_price) {
                    $cad_price = $this->currency->convert($market_price, 'USD', 'CAD');
                    $card['sale_price'] = number_format($cad_price, 2, '.', '');
                    $card['original_price_usd'] = number_format($market_price, 2, '.', '');
                } elseif ($sale_price > 0.99) {
                    $cad_price = $this->currency->convert($sale_price, 'USD', 'CAD');
                    $card['sale_price'] = number_format($cad_price, 2, '.', '');
                    $card['original_price_usd'] = number_format($sale_price, 2, '.', '');
                } else {
                    $card['sale_price'] = '0.99';
                }
                $card['price_source'] = 'csv';
            }
        }

        return $cards;
    }  
  

    /**
     * Calculate statistics from cards data
     * @param array $cards Raw card data
     * @param array $groups Grouped card data
     * @return array Statistics array
     */
    private function calculateStatistics(array $cards, array $groups): array {
        $stats = [
            'total_cards' => count($cards),
            'groups_count' => count($groups),
            'with_images' => 0,
            'without_images' => 0,
            'min_price' => 0,
            'max_price' => 0,
            'avg_price' => 0
        ];
        
        $prices = [];
        
        foreach ($cards as $card) {
            // Count images
            if (!empty($card['front_image']) || !empty($card['back_image'])) {
                $stats['with_images']++;
            } else {
                $stats['without_images']++;
            }
            
            // Collect prices
            $price = floatval($card['sale_price'] ?? 0);
            if ($price > 0) {
                $prices[] = $price;
            }
        }
        
        // Calculate price statistics
        if (!empty($prices)) {
            $stats['min_price'] = min($prices);
            $stats['max_price'] = max($prices);
            $stats['avg_price'] = array_sum($prices) / count($prices);
        }
        
        return $stats;
    }
    
    /**
     * Get human-readable upload error message
     * @param int $error_code PHP upload error code
     * @return string Error message
     */
    private function getUploadErrorMessage(int $error_code): string {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'PHP extension stopped upload'
        ];
        
        return $error_messages[$error_code] ?? 'Unknown upload error: ' . $error_code;
    }
    
    /**
     * Send JSON response to client
     * @param array $data Response data
     */
    private function sendJsonResponse(array $data): void {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }
    
    /**
     * Generate eBay CSV endpoint - HTTP entry point
     * Validates request, generates CSV file, returns download link
     */
    public function generate(): void {
        $this->load->language('shopmanager/card/import/card_importer');
        $data = [];
        
        $this->load->model('shopmanager/card/card_listing');
        
        $json = [];
        
        try {
            // Step 1: Validate request
            $validation_error = $this->validateGenerateRequest();
            if ($validation_error) {
                $json['error'] = $validation_error;
                $this->sendJsonResponse($json);
                return;
            }
            
            // Step 2: Get and validate input data
            $input = $this->getGenerateInput();
            if (isset($input['error'])) {
                $this->sendJsonResponse($input);
                return;
            }
            
            // Step 3: Generate CSV file
            $json = $this->generateEbayFile($input['cards'], $input['config']);
            
        } catch (\Exception $e) {
            $json['error'] = 'Exception: ' . $e->getMessage();
        }
        
        $this->sendJsonResponse($json);
    }
    
    /**
     * Validates generate request (permissions)
     * @return string|null Error message or null if valid
     */
    private function validateGenerateRequest(): ?string {
        if (!$this->user->hasPermission('modify', 'shopmanager/card/import/card_importer')) {
            return ($lang['error_permission'] ?? '');
        }
        return null;
    }
    
    /**
     * Get and validate input data from request
     * @return array Cards and config data or error
     */
    private function getGenerateInput(): array {
        $json_input = file_get_contents('php://input');
        $post_data = json_decode($json_input, true);
        
        $cards = isset($post_data['cards']) ? $post_data['cards'] : [];
        $config = isset($post_data['config']) ? $post_data['config'] : [];
        
        if (empty($cards)) {
            return ['error' => ($lang['error_no_data'] ?? '')];
        }
        
        return [
            'cards' => $cards,
            'config' => $config
        ];
    }
    
    /**
     * Generate eBay CSV file and save to disk
     * @param array $cards Card data
     * @param array $config Configuration options
     * @return array JSON response data
     */
    private function generateEbayFile(array $cards, array $config): array {
        // Increase limits for large CSV generation
        set_time_limit(300);
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');
        
        // Ensure upload directory exists
        if (!is_dir(DIR_UPLOAD)) {
            mkdir(DIR_UPLOAD, 0755, true);
        }
        $this->load->model('shopmanager/card/card_listing');
        // Generate eBay CSV content (model handles CSV logic)
        $ebay_csv = $this->model_shopmanager_card_card_listing->generateEbayCSV($cards, $config);
        
        if (!$ebay_csv) {
            return ['error' => ($lang['error_generation_failed'] ?? '')];
        }
        
        // Save CSV to file
        $filename = 'ebay_multi_variation_' . date('Ymd_His') . '.csv';
        $filepath = DIR_UPLOAD . $filename;
        
        if (file_put_contents($filepath, $ebay_csv) === false) {
            return ['error' => 'Failed to save CSV file'];
        }
        
        $user_token = isset($this->session->data['user_token']) ? $this->session->data['user_token'] : '';
        
        // Return success response with download link
        return [
            'success' => ($lang['text_generate_success'] ?? ''),
            'filename' => $filename,
            'download_url' => $this->url->link(
                'shopmanager/card/import/card_importer.download',
                'file=' . $filename . '&user_token=' . $user_token
            ),
            'total_rows' => count($cards)
        ];
    }
    
    /**
     * Download CSV file endpoint
     * Validates file existence and sends file to browser
     */
    public function download(): void {
        $lang = $this->load->language('shopmanager/card/import/card_importer');
        $data = [];
        
        
        // Validate permission
        if (!$this->user->hasPermission('access', 'shopmanager/card/import/card_importer')) {
            $this->response->setOutput('Error: ' . ($lang['error_permission'] ?? ''));
            return;
        }
        
        // Validate file parameter
        if (!isset($this->request->get['file'])) {
            $this->response->setOutput('Error: No file specified');
            return;
        }
        
        // Sanitize filename and build path
        $file = basename($this->request->get['file']);
        $filepath = DIR_UPLOAD . $file;
        
        // Check file exists
        if (!file_exists($filepath)) {
            $this->response->setOutput('Error: File not found');
            return;
        }
        
        // Send file to browser
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
    
    /**
     * Validate if manufacturer exists in database
     * AJAX endpoint for brand validation
     */
    public function validateManufacturer(): void {
        $this->load->language('shopmanager/card/import/card_importer');
        $data = [];
        
        
        $json = [];
        
        // Check permission
        if (!$this->user->hasPermission('modify', 'shopmanager/card/import/card_importer')) {
            $json['success'] = false;
            $json['error'] = ($lang['error_permission'] ?? '');
        } else {
            // Get manufacturer name from request
            $name = isset($this->request->post['name']) ? trim($this->request->post['name']) : '';
            
            if (empty($name)) {
                $json['success'] = false;
                $json['error'] = 'Manufacturer name is required';
            } else {
                // Check if exists
                $this->load->model('shopmanager/card/card_listing');
                $exists = $this->model_shopmanager_card_card_listing->checkManufacturerExists($name);
                
                $json['success'] = true;
                $json['exists'] = $exists;
                $json['name'] = $name;
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Add new manufacturer to database
     * AJAX endpoint for adding new brand
     */
    public function addManufacturer(): void {
        $this->load->language('shopmanager/card/import/card_importer');
        $data = [];
        
        
        $json = [];
        
        // Check permission
        if (!$this->user->hasPermission('modify', 'shopmanager/card/import/card_importer')) {
            $json['success'] = false;
            $json['error'] = ($lang['error_permission'] ?? '');
        } else {
            // Get manufacturer name from request
            $name = isset($this->request->post['name']) ? trim($this->request->post['name']) : '';
            
            if (empty($name)) {
                $json['success'] = false;
                $json['error'] = 'Manufacturer name is required';
            } else {
                // Add manufacturer
                $this->load->model('shopmanager/card/card_listing');
                $manufacturer_id = $this->model_shopmanager_card_card_listing->addManufacturer($name);
                
                if ($manufacturer_id > 0) {
                    $json['success'] = true;
                    $json['manufacturer_id'] = $manufacturer_id;
                    $json['name'] = $name;
                } else {
                    $json['success'] = false;
                    $json['error'] = 'Failed to add manufacturer';
                }
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * AJAX: Fetch market prices for preview rows (no card_id required)
     * Returns ungraded + graded auction/list prices in CAD.
     */
    public function getMarketPricesPreview(): void {
        $this->load->language('shopmanager/card/import/card_importer');
        $data = [];
        
        $this->load->model('shopmanager/ebay');

        $json = [];
        $startedAt = microtime(true);

        $hasAccessPermission = $this->user->hasPermission('access', 'shopmanager/card/import/card_importer');
        $hasModifyPermission = $this->user->hasPermission('modify', 'shopmanager/card/import/card_importer');
        if (!$hasAccessPermission && !$hasModifyPermission) {
            $cards = $this->request->post['cards'] ?? [];
            $permissionError = ($lang['error_permission'] ?? 'Permission denied');

            if (is_array($cards) && !empty($cards)) {
                $json['success'] = false;
                $json['batch'] = true;
                $json['rate_limited'] = false;
                $json['error'] = $permissionError;
                $json['results'] = [];

                foreach ($cards as $cardPayload) {
                    $rowKey = (string)($cardPayload['row_key'] ?? '');
                    $keyword = $this->buildPreviewKeyword((array)$cardPayload);
                    $json['results'][$rowKey] = [
                        'success' => false,
                        'keyword' => $keyword,
                        'error' => $permissionError,
                        'rate_limited' => false,
                    ];
                }

                $json['requested_count'] = count($cards);
                $json['processed_count'] = 0;
                $json['elapsed_ms'] = (int)round((microtime(true) - $startedAt) * 1000);
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }

            $json['error'] = $permissionError;
            $json['success'] = false;
            $json['rate_limited'] = false;
            $json['elapsed_ms'] = (int)round((microtime(true) - $startedAt) * 1000);
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $cards = $this->request->post['cards'] ?? [];
        $processedCount = 0;

        if (is_array($cards) && !empty($cards)) {
            $json['success'] = true;
            $json['batch'] = true;
            $json['rate_limited'] = false;
            $json['results'] = [];

            foreach ($cards as $index => $cardPayload) {
                $rowKey = (string)($cardPayload['row_key'] ?? '');
                $keyword = $this->buildPreviewKeyword($cardPayload);

                if (strlen($keyword) < 3) {
                    $json['results'][$rowKey] = [
                        'success' => false,
                        'keyword' => $keyword,
                        'error' => 'keyword too short (min 3 chars)',
                        'rate_limited' => false
                    ];
                    continue;
                }

                $rowResult = $this->getPreviewMarketPrices($keyword);
                $json['results'][$rowKey] = $rowResult;
                $processedCount++;

                if (!empty($rowResult['rate_limited'])) {
                    $json['rate_limited'] = true;
                    $json['error'] = $rowResult['error'] ?? $rowResult['api_error'] ?? 'Rate limited';

                    for ($remainingIndex = $index + 1; $remainingIndex < count($cards); $remainingIndex++) {
                        $remainingPayload = $cards[$remainingIndex];
                        $remainingRowKey = (string)($remainingPayload['row_key'] ?? '');
                        $remainingKeyword = $this->buildPreviewKeyword($remainingPayload);

                        $json['results'][$remainingRowKey] = [
                            'success' => false,
                            'keyword' => $remainingKeyword,
                            'rate_limited' => true,
                            'api_error' => $json['error'],
                            'manual_urls' => $this->model_shopmanager_ebay->buildManualEbayUrls($remainingKeyword)
                        ];
                    }

                    break;
                }
            }

            $json['requested_count'] = count($cards);
            $json['processed_count'] = $processedCount;
            $json['elapsed_ms'] = (int)round((microtime(true) - $startedAt) * 1000);
        } else {
            $keyword = $this->buildPreviewKeyword($this->request->post);

            if (strlen($keyword) < 3) {
                $json['error'] = 'keyword too short (min 3 chars)';
                $json['elapsed_ms'] = (int)round((microtime(true) - $startedAt) * 1000);
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }

            $json = $this->getPreviewMarketPrices($keyword);
            $json['requested_count'] = 1;
            $json['processed_count'] = 1;
            $json['elapsed_ms'] = (int)round((microtime(true) - $startedAt) * 1000);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function buildPreviewKeyword(array $payload): string {
        $keyword = trim((string)($payload['keyword'] ?? ''));
        $title = trim((string)($payload['title'] ?? ''));
        $year = trim((string)($payload['year'] ?? ''));
        $player = trim((string)($payload['player'] ?? ''));
        $cardNumber = trim((string)($payload['card_number'] ?? ''));
        $setName = trim((string)($payload['set_name'] ?? ''));

        if ($year !== '' && $setName !== '') {
            $yearPattern = preg_quote(substr($year, 0, 4), '/');
            $setName = preg_replace('/^' . $yearPattern . '(?:[-\/][0-9]{2,4})?\s+/i', '', $setName) ?? $setName;
            $setName = trim($setName);
        }

        $parts = [];
        if ($year !== '') {
            $parts[] = $year;
        }
        if ($setName !== '') {
            $parts[] = $setName;
        }
        if ($cardNumber !== '') {
            $parts[] = '#' . $cardNumber;
        }
        if ($player !== '') {
            $parts[] = $player;
        }

        $built = trim(implode(' ', array_filter($parts)));

        if ($built !== '') {
            return $built;
        }

        if ($keyword !== '') {
            return $keyword;
        }

        return $title;
    }

    private function getPreviewMarketPrices(string $keyword): array {
        $keyword = trim($keyword);

        if (strlen($keyword) < 3) {
            return [
                'success' => false,
                'keyword' => $keyword,
                'error' => 'keyword too short (min 3 chars)',
                'rate_limited' => false,
                'manual_urls' => $this->model_shopmanager_ebay->buildManualEbayUrls($keyword)
            ];
        }

        try {
            $searchOptions = [
                'sort'           => 'price_asc',
                'limit'          => 100,
                'page'           => 1,
                'site_id'        => 2,
                'condition_type' => 'all',
                'category_id'    => '261328',
            ];

            $marketData = $this->model_shopmanager_ebay->searchAndClassifyActiveItems($keyword, $searchOptions, 1);
            $apiError = (string)($marketData['error'] ?? '');
            $manualUrls = $this->model_shopmanager_ebay->buildManualEbayUrls($keyword);

            if ($this->isApiRateLimitedMessage($apiError)) {
                return [
                    'success' => false,
                    'keyword' => $keyword,
                    'rate_limited' => true,
                    'api_error' => $apiError,
                    'manual_urls' => $manualUrls
                ];
            }

            $auctionRaw = $marketData['buckets']['auction_raw'] ?? null;
            $auctionGraded = $marketData['buckets']['auction_graded'] ?? null;
            $buyNowRaw = $marketData['buckets']['buy_now_raw'] ?? null;
            $buyNowGraded = $marketData['buckets']['buy_now_graded'] ?? null;

            if ($auctionRaw !== null) {
                $auctionRaw['price'] = round((float)$this->currency->convert((float)$auctionRaw['price'], (string)($auctionRaw['currency'] ?? 'USD'), 'CAD'), 2);
            }
            if ($auctionGraded !== null) {
                $auctionGraded['price'] = round((float)$this->currency->convert((float)$auctionGraded['price'], (string)($auctionGraded['currency'] ?? 'USD'), 'CAD'), 2);
            }
            if ($buyNowRaw !== null) {
                $buyNowRaw['price'] = round((float)$this->currency->convert((float)$buyNowRaw['price'], (string)($buyNowRaw['currency'] ?? 'USD'), 'CAD'), 2);
            }
            if ($buyNowGraded !== null) {
                $buyNowGraded['price'] = round((float)$this->currency->convert((float)$buyNowGraded['price'], (string)($buyNowGraded['currency'] ?? 'USD'), 'CAD'), 2);
            }

            $result = [
                'success' => true,
                'keyword' => $keyword,
                'price_sold' => $auctionRaw !== null ? number_format($auctionRaw['price'], 2, '.', '') : null,
                'price_sold_url' => $auctionRaw['url'] ?? '',
                'price_sold_bids' => $auctionRaw['bids'] ?? 0,
                'price_sold_graded' => $auctionGraded !== null ? number_format($auctionGraded['price'], 2, '.', '') : null,
                'price_sold_graded_url' => $auctionGraded['url'] ?? '',
                'price_sold_graded_bids' => $auctionGraded['bids'] ?? 0,
                'price_sold_graded_grade' => $auctionGraded['grade'] ?? '',
                'price_list' => $buyNowRaw !== null ? number_format($buyNowRaw['price'], 2, '.', '') : null,
                'price_list_url' => $buyNowRaw['url'] ?? '',
                'price_list_graded' => $buyNowGraded !== null ? number_format($buyNowGraded['price'], 2, '.', '') : null,
                'price_list_graded_url' => $buyNowGraded['url'] ?? '',
                'price_list_graded_grade' => $buyNowGraded['grade'] ?? '',
                'api_error' => $apiError,
                'rate_limited' => false,
                'manual_urls' => $manualUrls
            ];

            return $result;
        } catch (\Throwable $e) {
            $error = 'Exception: ' . $e->getMessage();
            return [
                'success' => false,
                'keyword' => $keyword,
                'error' => $error,
                'rate_limited' => $this->isApiRateLimitedMessage($error),
                'manual_urls' => $this->model_shopmanager_ebay->buildManualEbayUrls($keyword)
            ];
        }
    }

    private function isApiRateLimitedMessage(string $message): bool {
        if ($message === '') {
            return false;
        }

        $text = strtolower($message);
        $patterns = [
            'call limit',
            'rate limit',
            'quota',
            'too many requests',
            'http 429',
            'error 429',
            'request limit'
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($text, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function getPreviewTable(array $groups): string {
        $this->load->language('shopmanager/card/import/card_importer');
        $data = [];
        
        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/card/card_type');
        $this->load->model('shopmanager/card/card_manufacturer');
        
        $user_token = isset($this->session->data['user_token']) ? $this->session->data['user_token'] : '';
        
        $data = [
            'groups' => $groups, // Pass grouped structure
            'manufacturers' => array_column(
                $this->model_shopmanager_card_card_manufacturer->getManufacturers(['filter_status' => 1]),
                'name'
            ),
            'card_types' => $this->model_shopmanager_card_card_type->getCardTypes(),
            'user_token' => $user_token,
            'column_card_title' => ($lang['column_card_title'] ?? ''),
            'column_price' => ($lang['column_price'] ?? ''),
            'column_condition' => ($lang['column_condition'] ?? ''),
            'column_brand' => ($lang['column_brand'] ?? ''),
            'column_images' => ($lang['column_images'] ?? ''),
            'column_qty' => ($lang['column_qty'] ?? ''),
            'text_placeholder_title' => ($lang['text_placeholder_title'] ?? ''),
            'text_placeholder_description' => ($lang['text_placeholder_description'] ?? ''),
            'text_placeholder_price' => ($lang['text_placeholder_price'] ?? ''),
            'text_placeholder_condition' => ($lang['text_placeholder_condition'] ?? ''),
            'text_placeholder_brand' => ($lang['text_placeholder_brand'] ?? ''),
            'text_ebay_title_label' => ($lang['text_ebay_title_label'] ?? ''),
            'text_placeholder_listing_title' => ($lang['text_placeholder_listing_title'] ?? ''),
            'button_remove_line' => ($lang['button_remove_line'] ?? ''),
            'button_remove_listing' => ($lang['button_remove_listing'] ?? ''),
            'text_remove_card_line_confirm' => ($lang['text_remove_card_line_confirm'] ?? ''),
            'text_remove_listing_confirm' => ($lang['text_remove_listing_confirm'] ?? ''),
            'text_remaining_listings' => ($lang['text_remaining_listings'] ?? ''),
            'text_remaining_cards' => ($lang['text_remaining_cards'] ?? ''),
            'button_fetch_market_prices' => ($lang['button_fetch_market_prices'] ?? ''),
            'text_market_fetch_progress_done' => ($lang['text_market_fetch_progress_done'] ?? ''),
            'text_market_column_auction' => ($lang['text_market_column_auction'] ?? ''),
            'text_market_column_buy_now' => ($lang['text_market_column_buy_now'] ?? ''),
            'text_market_url_missing' => ($lang['text_market_url_missing'] ?? ''),
            'text_market_no_rows' => ($lang['text_market_no_rows'] ?? ''),
            'text_market_checking' => ($lang['text_market_checking'] ?? ''),
            'text_market_api_limit_reached' => ($lang['text_market_api_limit_reached'] ?? ''),
            'text_market_fallback_kept' => ($lang['text_market_fallback_kept'] ?? ''),
            'text_market_manual_raw' => ($lang['text_market_manual_raw'] ?? ''),
            'text_market_manual_graded' => ($lang['text_market_manual_graded'] ?? ''),
            'text_market_manual_sold_graded' => ($lang['text_market_manual_sold_graded'] ?? ''),
            'text_market_apply_raw_buy_now' => ($lang['text_market_apply_raw_buy_now'] ?? ''),
            'url_fetch_market_price_preview' => html_entity_decode($this->url->link('shopmanager/card/import/card_importer.getMarketPricesPreview', 'user_token=' . $user_token), ENT_QUOTES, 'UTF-8'),
            'text_already_exists' => ($lang['text_already_exists'] ?? ''),
            'text_placeholder_location' => ($lang['text_placeholder_location'] ?? ''),
            'text_total_prefix' => ($lang['text_total_prefix'] ?? ''),
            'text_cards' => ($lang['text_cards'] ?? ''),
            'text_unique' => ($lang['text_unique'] ?? ''),
            
            // Labels
            /*'text_card_title' => ($lang['text_card_title'] ?? ''),
            'text_price' => ($lang['text_price'] ?? ''),
            'text_condition' => ($lang['text_condition'] ?? ''),
            'text_year_brand' => ($lang['text_year_brand'] ?? ''),
            'text_front_image' => ($lang['text_front_image'] ?? ''),
            'text_back_image' => ($lang['text_back_image'] ?? ''),
            'text_group_title' => ($lang['text_group_title'] ?? ''),
            'text_cards_in_group' => ($lang['text_cards_in_group'] ?? ''),
            'column_row' => ($lang['column_row'] ?? ''),
            'column_card_title' => ($lang['column_card_title'] ?? ''),
            'column_price' => ($lang['column_price'] ?? ''),
            'column_condition' => ($lang['column_condition'] ?? ''),
            'column_year' => ($lang['column_year'] ?? ''),
            'column_brand' => ($lang['column_brand'] ?? ''),
            'column_images' => ($lang['column_images'] ?? ''),
            'text_placeholder_title' => ($lang['text_placeholder_title'] ?? ''),
            'text_placeholder_price' => ($lang['text_placeholder_price'] ?? ''),
            'text_placeholder_condition' => ($lang['text_placeholder_condition'] ?? ''),
            'text_placeholder_year' => ($lang['text_placeholder_year'] ?? ''),
            'text_placeholder_brand' => ($lang['text_placeholder_brand'] ?? ''),
            'text_placeholder_front_image' => ($lang['text_placeholder_front_image'] ?? ''),
            'text_placeholder_back_image' => ($lang['text_placeholder_back_image'] ?? ''),
            
            // Brand validation modal
            'text_brand_not_found' => ($lang['text_brand_not_found'] ?? ''),
            'text_brand_not_found_message' => ($lang['text_brand_not_found_message'] ?? ''),
            'button_add_brand' => ($lang['button_add_brand'] ?? ''),
            'button_cancel_brand' => ($lang['button_cancel_brand'] ?? ''),
            'text_brand_added' => ($lang['text_brand_added'] ?? ''),
            'text_brand_validating' => ($lang['text_brand_validating'] ?? ''),
            'error_brand_failed' => ($lang['error_brand_failed'] ?? ''),
            'button_remove_line' => ($lang['button_remove_line'] ?? ''),
            'button_remove_listing' => ($lang['button_remove_listing'] ?? ''),
            'text_remove_card_line_confirm' => ($lang['text_remove_card_line_confirm'] ?? ''),
            'text_remove_listing_confirm' => ($lang['text_remove_listing_confirm'] ?? ''),
            'text_remaining_listings' => ($lang['text_remaining_listings'] ?? ''),
            'text_remaining_cards' => ($lang['text_remaining_cards'] ?? ''),
            'button_fetch_market_prices' => ($lang['button_fetch_market_prices'] ?? ''),
            'text_market_fetch_progress_done' => ($lang['text_market_fetch_progress_done'] ?? ''),
            'text_market_column_auction' => ($lang['text_market_column_auction'] ?? ''),
            'text_market_column_buy_now' => ($lang['text_market_column_buy_now'] ?? ''),
            'text_market_url_missing' => ($lang['text_market_url_missing'] ?? ''),
            'text_market_no_rows' => ($lang['text_market_no_rows'] ?? ''),
            'text_market_checking' => ($lang['text_market_checking'] ?? ''),
            'text_market_api_limit_reached' => ($lang['text_market_api_limit_reached'] ?? ''),
            'text_market_fallback_kept' => ($lang['text_market_fallback_kept'] ?? ''),
            'url_fetch_market_price_preview' => html_entity_decode($this->url->link('shopmanager/card/import/card_importer.getMarketPricesPreview', 'user_token=' . $user_token), ENT_QUOTES, 'UTF-8'),
            
            // Preview list table
            'text_already_exists' => ($lang['text_already_exists'] ?? ''),
            'text_placeholder_location' => ($lang['text_placeholder_location'] ?? ''),
            'text_total_prefix' => ($lang['text_total_prefix'] ?? ''),
            'text_cards' => ($lang['text_cards'] ?? ''),
            'text_unique' => ($lang['text_unique'] ?? ''),
            'text_ebay_title_label' => ($lang['text_ebay_title_label'] ?? ''),
            'text_placeholder_listing_title' => ($lang['text_placeholder_listing_title'] ?? ''),
            'column_qty' => ($lang['column_qty'] ?? '')*/
        ];
        //error_log('Preview Table Data: ' . print_r($data, true) . "\n", 3, '/home/n7f9655/public_html/storage_phoenixliquidation/logs/debug.log');
       // error_log('view' . $this->load->view('shopmanager/card/import/card_importer_list', $data)  . "\n", 3, '/home/n7f9655/public_html/storage_phoenixliquidation/logs/debug.log');
        return $this->load->view('shopmanager/card/import/card_importer_list', $data);
    }

    // =====================================================
    // DATABASE OPERATIONS - Save listings to database
    // =====================================================
    
    /**
     * Install database tables
     */
    public function installTables(): void {
        $this->load->model('shopmanager/card/card_listing');
        
        $json = [];
        
        if ($this->model_shopmanager_card_card_listing->install()) {
            $json['success'] = true;
            $json['message'] = 'Multi-variation tables installed successfully!';
        } else {
            $json['error'] = 'Failed to install tables. Check error logs.';
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Save listings to database endpoint - HTTP entry point
     * Validates request, processes groups, saves to database
     */
    public function saveListings(): void {
        // Ensure JSON response headers immediately
        $json = [];
        
        try {
            $this->load->model('shopmanager/card/card_listing');
            
            // Validate request method
            if ($this->request->server['REQUEST_METHOD'] != 'POST') {
                $json['error'] = 'Invalid request method';
                $this->response->setOutput(json_encode($json));
                return;
            }
            
                    //error_log('saveListings called with POST data: ' . print_r($this->request->post, true) . "\n", 3, '/home/n7f9655/public_html/storage_phoenixliquidation/logs/debug.log');
            // Step 2: Get and validate input data
            $input = $this->getSaveListingsInput();
             //error_log('Save Listings Input: ' . print_r($input, true) . "\n", 3, '/home/n7f9655/public_html/storage_phoenixliquidation/logs/debug.log');
            if (isset($input['error'])) {
                $json['error'] = $input['error'];
                $this->response->setOutput(json_encode($json));
                return;
            }
            
            // Step 3: Process and save all groups
            $json = $this->processAndSaveGroups($input['groups'], $input['config']);
             //error_log('Process and Save Groups Result: ' . print_r($json, true) . "\n", 3, '/home/n7f9655/public_html/storage_phoenixliquidation/logs/debug.log');
            
        } catch (\Throwable $t) {
            // Catch ALL errors including fatal errors
            $json = [
                'success' => false,
                'error' => 'Critical error: ' . $t->getMessage(),
                'file' => $t->getFile(),
                'line' => $t->getLine()
            ];
            
            // Log the error
            //error_log('Critical error in saveListings: ' . $t->getMessage() . ' in ' . $t->getFile() . ' on line ' . $t->getLine() . "\n", 3, '/home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log');
        }
        //error_log('Final JSON Response: ' . print_r($json, true) . "\n", 3, '/home/n7f9655/public_html/storage_phoenixliquidation/logs/debug.log');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Ensure database tables are installed
     */
    private function ensureDatabaseTables(): void {
        $this->load->model('shopmanager/card/card_listing');
        if (!$this->model_shopmanager_card_card_listing->isInstalled()) {
            $this->model_shopmanager_card_card_listing->install();
        }
    }
    
    /**
     * Get and validate input data for saving listings
     * @return array Groups, config data or error
     */
    private function getSaveListingsInput(): array {
        $this->load->language('shopmanager/card/import/card_importer');
        $groups = $this->request->post['groups'] ?? [];
        
        if (empty($groups)) {
            return ['error' => 'No groups provided'];
        }

        $invalidGroups = $this->findGroupsWithBrandMismatch($groups);
        if (!empty($invalidGroups)) {
            $labels = array_map(static function($index) {
                return '#' . ((int)$index + 1);
            }, $invalidGroups);

            return ['error' => 'Brand mismatch detected in listing(s): ' . implode(', ', $labels) . '. All brands must match per listing.'];
        }

        $brandTitleMismatches = $this->findCardsWithBrandTitleMismatch($groups);
        if (!empty($brandTitleMismatches)) {
            $labels = array_map(static function(array $row) {
                return '#' . ((int)$row['group'] + 1) . '/card ' . ((int)$row['card'] + 1);
            }, $brandTitleMismatches);

            $messageTemplate = (string)($lang['text_brand_title_mismatch_block_save'] ?? 'Brand/title mismatch detected: %s. Brand must be present in each card title.');
            $labelsText = implode(', ', $labels);
            $message = strpos($messageTemplate, '%s') !== false ? sprintf($messageTemplate, $labelsText) : ($messageTemplate . ' ' . $labelsText);

            return ['error' => $message];
        }
        
        $config = [
            'ebay_category_id' => $this->request->post['ebay_category_id'] ?? null,
            'condition_id' => $this->request->post['condition_id'] ?? 4000
        ];
        
        return [
            'groups' => $groups,
            'config' => $config
        ];
    }

    private function findGroupsWithBrandMismatch(array $groups): array {
        $invalid = [];

        foreach ($groups as $groupIndex => $groupData) {
            if (empty($groupData['cards']) || !is_array($groupData['cards'])) {
                continue;
            }

            $distinctBrands = [];

            foreach ($groupData['cards'] as $cardData) {
                $brand = strtolower(trim((string)($cardData['brand'] ?? '')));
                if ($brand === '') {
                    continue;
                }

                $distinctBrands[$brand] = true;
                if (count($distinctBrands) > 1) {
                    $invalid[] = (int)$groupIndex;
                    break;
                }
            }
        }

        return $invalid;
    }

    private function findCardsWithBrandTitleMismatch(array $groups): array {
        $invalid = [];

        foreach ($groups as $groupIndex => $groupData) {
            if (empty($groupData['cards']) || !is_array($groupData['cards'])) {
                continue;
            }

            foreach ($groupData['cards'] as $cardIndex => $cardData) {
                $brand = trim((string)($cardData['brand'] ?? ''));
                $title = trim((string)($cardData['title'] ?? ''));

                if ($brand === '' || $title === '') {
                    continue;
                }

                if (!$this->isBrandPresentInTitle($brand, $title)) {
                    $invalid[] = [
                        'group' => (int)$groupIndex,
                        'card' => (int)$cardIndex
                    ];
                }
            }
        }

        return $invalid;
    }

    private function isBrandPresentInTitle(string $brand, string $title): bool {
        $normalizedBrand = $this->normalizeBrandComparable($brand);
        $normalizedTitle = $this->normalizeBrandComparable($title);

        if ($normalizedBrand === '' || $normalizedTitle === '') {
            return false;
        }

        if (strpos($normalizedTitle, $normalizedBrand) !== false) {
            return true;
        }

        $brandWords = array_values(array_filter(explode(' ', $normalizedBrand), static function(string $word): bool {
            return strlen($word) > 1;
        }));

        if (count($brandWords) > 1) {
            foreach ($brandWords as $word) {
                if (strpos($normalizedTitle, $word) === false) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    private function normalizeBrandComparable(string $value): string {
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/i', ' ', $value);
        $value = preg_replace('/\s+/', ' ', (string)$value);

        return trim((string)$value);
    }
    
    /**
     * Process groups and save to database
     * @param array $groups_data Form groups data
     * @param array $config eBay configuration
     * @return array JSON response data
     */
    private function processAndSaveGroups(array $groups_data, array $config): array {
        $saved_listings = [];
        $published_listings = [];

        $this->load->model('shopmanager/card/card_listing');
        
        foreach ($groups_data as $group_index => $group_data) {
            // Step 1: Reconstruct group structure from form data
            $group = $this->reconstructGroup($group_data, $group_index);
            
            // Skip empty groups
            if (empty($group['cards'])) {
                continue;
            }
            
            // Step 2: Convert group to listing format (model handles business logic)
            try {
                $listing_data = $this->model_shopmanager_card_card_listing->convertGroupToListing($group, $config);
            } catch (\Exception $e) {
                // Log the error and skip this group
                error_log('Error converting group to listing: ' . $e->getMessage() . ' Group: ' . json_encode($group));
                continue; // Skip this group and continue with others
            } catch (\Throwable $t) {
                // Log fatal errors and skip this group
                error_log('Fatal error converting group to listing: ' . $t->getMessage() . ' Group: ' . json_encode($group));
                continue; // Skip this group and continue with others
            }
            
            // Step 3: Save to database (model handles SQL)
            try {
                $listing_id = $this->model_shopmanager_card_card_listing->saveListing($listing_data);
            } catch (\Exception $e) {
                // Log the error and skip this listing
                error_log('Error saving listing: ' . $e->getMessage() . ' Data: ' . json_encode($listing_data));
                continue; // Skip this listing and continue with others
            } catch (\Throwable $t) {
                // Log fatal errors and skip this listing
                error_log('Fatal error saving listing: ' . $t->getMessage() . ' Data: ' . json_encode($listing_data));
                continue; // Skip this listing and continue with others
            }
            
            
            // Step 4: Automatically publish to eBay Canada (English + French)
            // TEMPORARILY DISABLED FOR DEBUGGING - Check if images grouped by price and quantities added
            $publish_result = [
                'success' => false,
                'error' => 'eBay publishing disabled for debugging'
            ];
            /*
            try {
                $publish_result = $this->publishToEbay($listing_id);
            } catch (\Exception $e) {
                $publish_result = [
                    'success' => false,
                    'error' => 'Exception during eBay publishing: ' . $e->getMessage()
                ];
            } catch (\Throwable $t) {
                $publish_result = [
                    'success' => false,
                    'error' => 'Fatal error during eBay publishing: ' . $t->getMessage()
                ];
            }
            */
           
            // Update location if listing already existed and location was provided
            if ($group['is_existing'] && $group['existing_listing_id'] > 0) {
                $this->model_shopmanager_card_card_listing->updateListingLocation(
                    $group['existing_listing_id'],
                    $group['location']
                );
            }

            // Track saved listings
            $saved_listings[] = [
                'listing_id' => $listing_id,
                'title' => $listing_data['title'],
                'variations' => count($listing_data['variations']),
                'ebay_published' => $publish_result['success'] ?? false
            ];
            
            if ($publish_result['success']) {
                $published_listings[] = [
                    'listing_id' => $listing_id,
                    'ebay_english' => $publish_result['english']['ItemID'] ?? null,
                    'ebay_french' => $publish_result['french']['ItemID'] ?? null
                ];
            }
        }
        
        $message = 'Successfully saved ' . count($saved_listings) . ' listings to database!';
        if (!empty($published_listings)) {
            $message .= ' ' . count($published_listings) . ' listings published to eBay Canada.';
        }
        
        return [
            'success' => true,
            'message' => $message,
            'listings' => $saved_listings ?? [],
            'ebay_published' => $published_listings ?? [],
            'listing_id' => $listing_id ?? null,
            'publish_result' => $publish_result ?? null
        ];
    }
    
    /**
     * Reconstruct group structure from form data
     * @param array $group_data Single group from form
     * @param int $group_index Index of group
     * @return array Reconstructed group structure
     */
    private function reconstructGroup(array $group_data, int $group_index): array {
        $group = [
            'key'                 => $group_data['key']      ?? 'group_' . $group_index,
            'set'                 => $group_data['set_name'] ?? 'Unknown Set',
            'set_name'            => $group_data['set_name'] ?? 'Unknown Set',
            'subset'              => $group_data['subset']  ?? '',
            'title'               => $group_data['title']    ?? '',
            'title_fr'            => $group_data['title_fr'] ?? '',
            'is_existing'         => !empty($group_data['is_existing']) && $group_data['is_existing'] === '1',
            'existing_listing_id' => (int)($group_data['existing_listing_id'] ?? 0),
            'location'            => trim($group_data['location'] ?? ''),
            'year'                => null,
            'brand'               => null,
            'cards'               => []
        ];
        
        // Process cards in this group
        if (!empty($group_data['cards'])) {
            foreach ($group_data['cards'] as $card_data) {
                // Skip empty cards
                if (empty($card_data['title'])) {
                    continue;
                }
                
                $card = [
                    'title' => $card_data['title'],
                    'description' => $card_data['description'] ?? $card_data['title'],
                    'sale_price' => $card_data['sale_price'] ?? .95,
                    'condition' => $card_data['condition'] ?? 'See Pictures',
                    'brand' => $card_data['brand'] ?? '',
                    'year' => $card_data['year'] ?? '',
                    'card_number' => $card_data['card_number'] ?? '',
                    'player' => $card_data['player'] ?? '',
                    'team' => $card_data['team'] ?? '',
                    'set' => $card_data['set'] ?? '',
                    'subset' => $card_data['subset'] ?? '',
                    'category' => $card_data['category'] ?? '',
                    'quantity' => $card_data['quantity'] ,
                    'merge' => $card_data['merge'] ,
                    'all_images' => $card_data['all_images'] ?? '',
                    'front_image' => $card_data['front_image'] ?? '',
                    'back_image' => $card_data['back_image'] ?? ''
                ];
                
                // Extract year and brand for listing metadata
                if (!$group['year'] && !empty($card['year'])) {
                    $group['year'] = $card['year'];
                }
                if (!$group['brand'] && !empty($card['brand'])) {
                    $group['brand'] = $card['brand'];
                }
                
                $group['cards'][] = $card;
            }
        }
        
        return $group;
    }
    
    /**
     * List all saved listings - redirect to card_listing list
     */
    public function listSaved(): void {
        $user_token = isset($this->session->data['user_token']) ? $this->session->data['user_token'] : '';
        
        // Redirect to card_listing list page
        $this->response->redirect($this->url->link('shopmanager/card/card_listing', 'user_token=' . $user_token));
    }
    
    /**
     * View single listing details
     */
    public function viewListing(): void {
        $this->load->model('shopmanager/card/card_listing');
        
        $user_token = isset($this->session->data['user_token']) ? $this->session->data['user_token'] : '';
        $listing_id = $this->request->get['listing_id'] ?? 0;
        
        if (!$listing_id) {
            $this->response->redirect($this->url->link('shopmanager/card/card_listing', 'user_token=' . $user_token));
            return;
        }
        
        $listing = $this->model_shopmanager_card_card_listing->getListing($listing_id);
        
        if (empty($listing)) {
            $this->response->redirect($this->url->link('shopmanager/card/card_listing', 'user_token=' . $user_token));
            return;
        }
        
        $json = [
            'success' => true,
            'listing' => $listing
        ];
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Delete listing
     */
    public function deleteListing(): void {
        $this->load->model('shopmanager/card/card_listing');
        
        $json = [];
        
        $listing_id = $this->request->post['listing_id'] ?? 0;
        
        if (!$listing_id) {
            $json['error'] = 'Invalid listing ID';
        } else {
            $result = $this->model_shopmanager_card_card_listing->deleteListing($listing_id);
            if ($result['ok']) {
                $json['success'] = true;
                $json['message'] = 'Listing deleted successfully';
                $json['deleted'] = $result['deleted'];
            } else {
                $json['error'] = $result['error'] ?? 'Failed to delete listing';
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Publish listing to eBay Canada (English and French)
     * @param int|null $listing_id The listing ID to publish
     * @return array Result array with success/error status
     */
    public function publishToEbay($listing_id = null): array {
        $this->load->language('shopmanager/card/import/card_importer');
        $data = [];
        
        
        try {
            $this->load->model('shopmanager/ebay');
            $this->load->model('shopmanager/card/card_listing');
            $this->load->model('shopmanager/marketplace');
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to load models: ' . $e->getMessage()
            ];
        }
        
        $json = [];
        
        if (!$listing_id) {
            $json['error'] = 'Invalid listing ID';
            return $json;
        }
        
        try {
            $listing_data = $this->model_shopmanager_card_card_listing->getListing($listing_id);
           
            $listing_data['descriptions'] = $this->model_shopmanager_card_card_listing->getDescriptions($listing_id);

            // Get marketplace account
            $response = [];
            $site_setting = [];
                
            // AVANT de créer le listing
            foreach ($listing_data['descriptions'] as $description) {
                $marketplace_account = $this->model_shopmanager_marketplace->getMarketplaceAccount([
                    'customer_id' => 10,'filter_language_id' => $description['language_id']
                ]);
                if (empty($marketplace_account) || !isset($marketplace_account['site_setting']) || !isset($marketplace_account['marketplace_account_id'])) {
                    continue;
                }
                $listing = array_merge($listing_data, $description);
                $site_setting = $marketplace_account['site_setting'];
                $marketplace_account_id = $marketplace_account['marketplace_account_id'];

                if(!isset($description['ebay_item_id']) || empty($description['ebay_item_id'])) {
                    $response = $this->model_shopmanager_ebay->addCardListing($listing , $site_setting, $marketplace_account_id, false);
                    if (isset($response['ebay_item_id'])) {
                        $this->model_shopmanager_card_card_listing->updateEbayListingId($listing_id, $response['ebay_item_id'], $description['language_id']);
                        $error = empty($response['errors']) ? '' : json_encode($response['errors']);
                        if(!empty($error)) {
                            $this->model_shopmanager_marketplace->editCardListingERROR($listing_id,$response['ebay_item_id'],$error);
                        }
                    }
                }
            } 
                    
            $json['success'] = true;
            $json['message'] = 'Listing published to eBay Canada';
            $json['results'] = $response;
                    
        } catch (\Exception $e) {
            $json = [
                'success' => false,
                'error' => 'Exception in publishToEbay: ' . $e->getMessage()
            ];
        } catch (\Throwable $t) {
            $json = [
                'success' => false,
                'error' => 'Fatal error in publishToEbay: ' . $t->getMessage()
            ];
        }
        
        return $json;
    }
    
   
    
    /**
     * Update existing eBay listing with new cards and photos
     * @param int $listing_id Listing ID
     * @param array $listing_data Listing data
     * @param string|null $existing_english Existing English eBay item ID
     * @param string|null $existing_french Existing French eBay item ID
     * @return array Update result
     */
    private function updateExistingEbayListing(int $listing_id, array $listing_data, ?string $existing_english, ?string $existing_french): array {
        // For now, we'll mark as needing manual update
        // In a full implementation, this would call eBay's ReviseItem API
        return [
            'success' => true,
            'action' => 'needs_update',
            'message' => 'Listing exists on eBay - manual update required',
            'existing_english' => $existing_english,
            'existing_french' => $existing_french
        ];
    }
}
