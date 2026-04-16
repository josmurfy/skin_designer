<?php
// Original: shopmanager/ebay.php
namespace Opencart\Admin\Model\Shopmanager;

class Ebay extends \Opencart\System\Engine\Model {

    private $conditionsCache = [];

    public function addListing($product, $quantity = 0,$site_setting=[],$marketplace_account_id=null) {

       
        // Charger les modèles nécessaires
        $this->load->model('shopmanager/ebaytemplate');
        $this->load->model('shopmanager/catalog/product');
    
        // Récupérer les informations du produit

        $productDescriptionEbay = $this->model_shopmanager_ebaytemplate->getEbayTemplate($product,$site_setting,$marketplace_account_id);
    //"<pre>".print_r ($productDescriptionEbay,true )."</pre>");
        // Construire l'élément Quantity si nécessaire
        $quantity = is_numeric($quantity) ? "<Quantity>".$quantity."</Quantity>" : '';
    
        // Créer les données pour AddItemRequest
        $postFields = $this->buildAddItemRequest($product, $productDescriptionEbay, $quantity, $site_setting);
        //$postFieldXML= simplexml_load_string($postFields);
        //$postFieldsARRAY= json_decode(json_encode($postFieldXML), true);
    	//"<pre>".print_r (835,true )."</pre>");
		//"<pre>".print_r ($postFieldsARRAY,true )."</pre>");
        // Construire les headers pour l'appel API
        //"<pre>".print_r (value: '200:ebay.php' )."</pre>");
        $headers = $this->buildEbayHeaders("AddItem",1371,$marketplace_account_id);
        //"<pre>".print_r (835,true )."</pre>");
		//"<pre>".print_r ($headers,true )."</pre>");
        // Faire l'appel API pour ajouter l'annonce
        $response = $this->makeCurlRequest('https://api.ebay.com/ws/api.dll', $headers, $postFields);
        $responseXml = simplexml_load_string($response);
        $responseArray = json_decode(json_encode($responseXml), true);
        $this->load->model('shopmanager/marketplace');
            
            if (isset($responseArray['ErrorLanguage'])) {
                $error = json_encode($responseArray);
            } elseif (isset($responseArray['Ack']) && $responseArray['Ack'] != 'Failure') {
                $error = '';
            } else {
                $error = json_encode($responseArray);
            }
        $_pm_row = $this->model_shopmanager_marketplace->getProductMarketplaceRow($responseArray['ItemID'] ?? null);
        if ($_pm_row) {
            $_pm_row['error'] = $error;
            $_pm_row['to_update'] = empty($error) ? 0 : 9;
            $this->model_shopmanager_marketplace->editProductMarketplace($_pm_row);
        }

         

        return $responseArray;
    }

/**
 * Execute eBay Inventory API operations for ONE batch of variations.
 * Called by addCardListing for both single-batch (≤250) and multi-batch (>250) flows.
 *
 * Steps: image migration → createInventoryItem (per variation) →
 *        createInventoryItemGroup → publishInventoryOffer.
 *
 * @param array  $template_data  Must include 'variations' and 'group_key'.
 * @param array  $headers        REST headers (already built).
 * @param string $merchantLocationKey
 * @return array ['inventory_items', 'inventory_group', 'offer', 'ebay_item_id', 'errors']
 */
private function runCardBatchOperations(array  $template_data,array  $site_setting,array  $headers,string $merchantLocationKey,bool $migrate_images): array {
    $batchResults = [
        'inventory_items' => [],
        'inventory_group' => null,
        'offer'           => null,
        'ebay_item_id'    => null,
        'errors'          => [],
    ];

    // ÉTAPE 0.9 : Migrer les images vers eBay
    //$this->log->write("[runCardBatchOperations] template_data: " . json_encode($template_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    if ($migrate_images) {
        foreach ($template_data['variations'] as $index => $variation) {
            if (!isset($variation['images']) || !is_array($variation['images'])) {
                continue;
            }
            $ebay_image_urls = [];
            foreach ($variation['images'] as $external_url) {
                if (strpos($external_url, 'ebayimg.com') !== false) {
                    $ebay_image_urls[] = $external_url;
                    continue;
                }
                $upload_result = $this->uploadImageToEbay($external_url, $headers);
                if ($upload_result['success'] && isset($upload_result['ebay_url'])) {
                    $ebay_image_urls[] = $upload_result['ebay_url'];
                    if (!empty($variation['card_id'])) {
                        $this->model_shopmanager_card_card->updateCardImageUrl(
                            (int)$variation['card_id'],
                            $external_url,
                            $upload_result['ebay_url']
                        );
                    }
                } else {
                    $ebay_image_urls[] = $external_url;
                }
                usleep(300000); // 300ms between images
            }
            $template_data['variations'][$index]['images'] = $ebay_image_urls;
        }
        sleep(3);
    }

    // ÉTAPE 1 : Create each inventory item
    foreach ($template_data['variations'] as $index => $variation) {
        $itemResult = $this->createInventoryItem($variation, $template_data, $headers, $merchantLocationKey, $index, $site_setting);
        $batchResults['inventory_items'][] = $itemResult;
        if (isset($itemResult['error'])) {
            $batchResults['errors'][] = $itemResult['error'];
        }
    }

    // Abort if any item failed
    foreach ($batchResults['inventory_items'] as $item) {
        if (!isset($item['status']) || $item['status'] !== 'success') {
            $batchResults['errors'][] = ['message' => 'Some inventory items failed to create. Stopping batch.'];
            return $batchResults;
        }
    }

    // ÉTAPE 2 : Create inventory item group (uses template_data['group_key'])
    $groupResult = $this->createInventoryItemGroup($template_data, $headers, $site_setting);
    $batchResults['inventory_group'] = $groupResult;
    if (isset($groupResult['error'])) {
        $batchResults['errors'][] = $groupResult['error'];
        return $batchResults;
    }

    sleep(2);

    // ÉTAPE 3 : Create + publish offer
    $offerResult = $this->publishInventoryOffer($template_data, $site_setting, $headers, $merchantLocationKey);
    $batchResults['offer'] = $offerResult;
    if (isset($offerResult['listingId'])) {
        $batchResults['ebay_item_id'] = $offerResult['listingId'];
    }
    if (isset($offerResult['error'])) {
        $batchResults['errors'][] = $offerResult['error'];
    }

    return $batchResults;
}

/**
 * Publier un listing de cartes sur eBay.
 * Itère simplement sur les batches configurés en DB (getDescriptions),
 * charge les cartes par batch (getCards + filter_batch_name),
 * et skip les batches déjà publiés (ebay_item_id non-null).
 */
public function addCardListing(int $listing_id, $site_setting = [], $marketplace_account_id = null, $cleanup = true, $migrate_images = false, $only_batch_name = null): array {
    $this->load->model('shopmanager/card/card_listing');
    $this->load->model('shopmanager/card/card');
    $this->load->model('shopmanager/ebaytemplate');

    if (!$listing_id) {
        return ['errors' => [['message' => 'listing_id manquant']]];
    }

    $countryCode = $site_setting['Currency']['Country'] ?? 'CA';
    if (!$this->supportsVariations($countryCode)) {
        return ['errors' => [['message' => "Le site {$countryCode} ne supporte pas les annonces avec variations."]]];
    }

    // 1. Données du listing (set_name, sport, brand, year, ebay_category_id, etc.)
    $listing_data = $this->model_shopmanager_card_card_listing->getListing($listing_id);
    if (empty($listing_data)) {
        return ['errors' => [['message' => "Listing #{$listing_id} introuvable."]]];
    }

    // Merge exact duplicates before publishing so no duplicate Customized values reach eBay
    $this->model_shopmanager_card_card_listing->mergeAndDeduplicateCards($listing_id);

    // 2. Descriptions par batch (indexées par batch_name, contiennent title/description/specifics/ebay_item_id)
    $batchDescriptions = $this->model_shopmanager_card_card_listing->getDescriptions($listing_id);
    if (empty($batchDescriptions)) {
        return ['errors' => [['message' => "Aucun batch configuré pour listing #{$listing_id}."]]];
    }
    $totalBatches = count(array_filter(array_keys($batchDescriptions), fn($k) => $k > 0));

    // 3. Credentials API
    $connectionapi   = $this->getApiCredentials($marketplace_account_id);
    $bearerToken     = $connectionapi['bearer_token'];
    $contentLanguage = !empty($site_setting['Language']) ? str_replace('_', '-', $site_setting['Language']) : 'en-US';
    $headers = [
        "Authorization: Bearer " . $bearerToken,
        "Content-Type: application/json",
        "Content-Language: " . $contentLanguage,
        "Accept: application/json",
    ];

    $results = ['batches' => [], 'errors' => []];

    try {
        // Location / cleanup : on utilise le premier batch disponible comme template structurel
        $firstDesc = null;
        foreach ($batchDescriptions as $bid => $bd) {
            if ($bid > 0) { $firstDesc = $bd; break; }
        }
        $locationData = array_merge($listing_data, [
            'title'      => $firstDesc['title']    ?? ($listing_data['title'] ?? ''),
            'specifics'  => $firstDesc['specifics'] ?? [],
            'variations' => [],
        ]);
        $locationTemplate    = $this->model_shopmanager_ebaytemplate->getEbayTemplateCardListing($locationData, $site_setting, $marketplace_account_id);
        $merchantLocationKey = $this->ensureInventoryLocation($locationTemplate, $headers);
        sleep(1);

        if ($cleanup) {
            $results['cleanup'] = $this->deleteInventoryItems($locationTemplate, $headers, $site_setting);
            sleep(1);
        }

        // 4. Boucle sur chaque batch configuré
        foreach ($batchDescriptions as $batchNumber => $batchDesc) {
            if ($batchNumber === 0) continue;
            // Filter to single batch if requested
            if ($only_batch_name !== null && (int)$batchNumber !== (int)$only_batch_name) continue;

            // Skip si déjà publié sur eBay
            if (!empty($batchDesc['ebay_item_id'])) {
                $results['batches'][$batchNumber] = ['ebay_item_id' => $batchDesc['ebay_item_id'], 'skipped' => true];
                $results['ebay_item_ids'][$batchNumber] = $batchDesc['ebay_item_id'];
                //$this->log->write("[addCardListing] Batch {$batchNumber} skipped (already published: {$batchDesc['ebay_item_id']})");
                continue;
            }
//$this->log->write("[addCardListing] Processing batch {$batchNumber} - title: '{$batchDesc['title']}'");
//$this->log->write("[addCardListing] Batch {$batchNumber} description preview: " . substr($batchDesc['description'] ?? '', 0, 100) . '...');
            // Cartes du batch — filter par batch_id FK (c.batch_id)
            $batchFkId  = (int)($batchDesc['batch_id'] ?? 0);
            $batchCards = $this->model_shopmanager_card_card->getCards([
                'filter_listing_id' => $listing_id,
                'filter_batch_name'   => $batchFkId,
            ]);
            if (empty($batchCards)) {
                //$this->log->write("[addCardListing] Batch {$batchNumber} (batch_id={$batchFkId}) ignoré — aucune carte trouvée");
                continue;
            }

            // Construire le listing_data pour ce batch : title/description/specifics/variations depuis la DB
            $batchListingData = array_merge($listing_data, [
                'title'       => $batchDesc['title']       ?? '',
                'description' => $batchDesc['description'] ?? '',
                'specifics'   => $batchDesc['specifics']   ?? [],
                'variations'  => $batchCards,
            ]);

            // Template eBay : getEbayTemplateCardListing utilise listing_data['description'] en priorité
            $batchTemplate = $this->model_shopmanager_ebaytemplate->getEbayTemplateCardListing($batchListingData, $site_setting, $marketplace_account_id);
            $batchTemplate['aspects'] = $this->formatAspects($batchTemplate['aspects']);

            //$this->log->write("[addCardListing] Batch {$batchNumber} - title: '{$batchTemplate['title']}'");
           // $this->log->write("[addCardListing] Batch {$batchNumber} - description preview: " . substr($batchTemplate['description'] ?? '', 0, 100) . '...');   
            //$this->log->write("[addCardListing] Batch {$batchNumber} - variations count: " . count($batchTemplate['variations']));

            foreach ($batchTemplate['variations'] as $i => $v) {
                $batchTemplate['variations'][$i]['aspects'] = $this->formatAspects($v['aspects']);
            }

            // group_key DOIT venir de oc_card_listing_batch — pas de fallback calculé
            $storedGroupKey  = $batchDesc['group_key'] ?? null;
            $batchLogicalNum = (int)($batchDesc['batch_name'] ?? 0);  // pour saveEbayBatch (logical number)
            if (empty($storedGroupKey)) {
                $errMsg = "[addCardListing] Batch {$batchNumber} (logical={$batchLogicalNum}) ERREUR: group_key manquant dans oc_card_listing_batch — appelez recalculateBatches() d'abord";
                $this->log->write($errMsg);
                $results['errors'][] = $errMsg;
                $results['batches'][$batchNumber] = ['error' => $errMsg];
                continue;
            }
            $batchTemplate['group_key'] = $storedGroupKey;

            // Publier ce batch
            $batchResult = $this->runCardBatchOperations($batchTemplate, $site_setting, $headers,$merchantLocationKey, $migrate_images);

            // Sauvegarder le batch en DB (saveEbayBatch uses logical batch_name)
            $this->model_shopmanager_card_card_listing->saveEbayBatch($listing_id, $batchLogicalNum, [
                'group_key'       => $batchTemplate['group_key'],
                'variation_count' => count($batchCards),
            ]);

            // Persister l'ebay_item_id et le statut de publication (use FK batch_id)
            if (!empty($batchResult['ebay_item_id'])) {
                $this->model_shopmanager_card_card_listing->updateEbayListingId($listing_id, $batchResult['ebay_item_id'], 1, $batchFkId);
                $this->model_shopmanager_card_card_listing->updateBatchPublishedStatus($listing_id, $batchFkId, 1, date('Y-m-d H:i:s'));
            } else {
                $this->model_shopmanager_card_card_listing->updateBatchPublishedStatus($listing_id, $batchFkId, 0);
            }

            $results['batches'][$batchNumber] = $batchResult;
            if (!empty($batchResult['ebay_item_id'])) {
                $results['ebay_item_ids'][$batchNumber] = $batchResult['ebay_item_id'];
            }
            if (!empty($batchResult['errors'])) {
                $results['errors'] = array_merge($results['errors'], $batchResult['errors']);
            }

            /*
            $this->log->write(
                "[addCardListing] Batch {$batchNumber}/{$totalBatches}"
                . ' cards=' . count($batchCards)
                . ' ebay_item_id=' . ($batchResult['ebay_item_id'] ?? 'none')
                . ' groupKey=' . $batchTemplate['group_key']
            );
            */

            sleep(3);
        }

    } catch (\Exception $e) {
        $results['errors'][] = $e->getMessage();
    }

    return $results;
}

    private function buildAddItemRequest($product, $productDescriptionEbay, $quantity = '',$site_setting = []) {

        // Construire la requête XML pour AddItem
        return '<?xml version="1.0" encoding="utf-8"?>
            <AddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
            
                <ErrorLanguage>'.$site_setting['Language'].'</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
                <Item>
                
                    <PrimaryCategory>
                        <CategoryID>' . $product['category_id'] . '</CategoryID>
                    </PrimaryCategory>
                    <ProductListingDetails>
                        <IncludePrefilledItemInformation>false</IncludePrefilledItemInformation>
                    </ProductListingDetails>
                
                    '.$this->getCurrency($product['made_in_country_id']??null,$product['price'],$site_setting).'
                
                    <ListingType>FixedPriceItem</ListingType>
                    <ListingDuration>GTC</ListingDuration>
                '.$this->getLocation($product['made_in_country_id']??null,$site_setting).'
                    <DispatchTimeMax>3</DispatchTimeMax>
                ' . $productDescriptionEbay . $quantity . '
                </Item>
            </AddItemRequest>';
    }


    
    public function buildEbayHeaders($callName, $level = 1349, $marketplace_account_id = 1,  $site_id = null, $bearer = TRUE) {
    
        //"<pre>".print_r (value: '1623:ebay.php' )."</pre>");
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        // Vérifier si une ligne d'appel existe
        if (isset($backtrace[1])) {
            $caller = $backtrace[1]; // Ligne appelante
            $file = isset($caller['file']) ? $caller['file'] : 'N/A';
            $line = isset($caller['line']) ? $caller['line'] : 'N/A';
    
            // Afficher l'information dans les logs ou l'écran
      //      error_log("Function buildEbayHeaders() called in $file on line $line");
        //    echo "<pre>Function buildEbayHeaders() called in $file on line $line</pre>";
        }
		$connectionapi = $this->getApiCredentials($marketplace_account_id);
	//"<pre>".print_r (value: '1703:ebay.php' )."</pre>");
     if(isset($site_id)){
        $connectionapi['site_id']=$site_id;
     }
    //"<pre>".print_r($connectionapi, true)."</pre>");

        $defaultHeaders = [
            "X-EBAY-API-COMPATIBILITY-LEVEL: ".$level,
            "X-EBAY-API-DEV-NAME: " . $connectionapi['application_id'],
            "X-EBAY-API-APP-NAME: " . $connectionapi['developer_id'],
            "X-EBAY-API-CERT-NAME: " . $connectionapi['certification_id'],
            "X-EBAY-API-CALL-NAME: $callName",
            "X-EBAY-API-SITEID: ".$connectionapi['site_id']."" // 3 for UK
        ];

      if ($bearer) {
		//	$connectionapi['TOKEN'] = $this->getEbayAccessToken($connectionapi);
            if (empty($connectionapi['bearer_token'])) {
                $this->log->write('eBay API ERREUR: bearer_token manquant dans connectionapi — le refresh_token est probablement expiré.');
            }
            $defaultHeaders[] = "X-EBAY-API-IAF-TOKEN: Bearer ".($connectionapi['bearer_token'] ?? '');
      }
//"<pre>".print_r ('1560:ebay.php',true )."</pre>");
//"<pre>".print_r ($defaultHeaders,true )."</pre>");
        return $defaultHeaders;
    }




    // Other functions are omitted for brevity
private function buildEndItemRequest($marketplace_item_id, $site_setting) {
    $language = isset($site_setting['Language']) ? $site_setting['Language'] : 'en_US';
	return '<?xml version="1.0" encoding="utf-8"?>
			<EndItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">

				<ErrorLanguage>'.$language.'</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
				<EndingReason>Incorrect</EndingReason>
				<ItemID>'.$marketplace_item_id.'</ItemID>
			</EndItemRequest>';
}

  
public function buildFinalResult($items,$marketplace_item_id = [], $marketplace_account_id = 1, $upc = null, $product_name = null) {

    $this->load->model('shopmanager/tools');
    $this->load->model('shopmanager/catalog/category');
    $this->load->model('shopmanager/ai');
  
    $items = isset($items[0]) ? $items : array($items);
    $cleanitems=[];
    
    //print("<pre>".print_r($items, true)."</pre>");
    if(isset($product_name)){
        $totalItems = count($items);
        $categoryCounts = []; // Ensure $categoryCounts is always an array
        foreach ($items as $key=>$item) {
            //"<pre>".print_r($item, true)."</pre>");
            
            $this->updateCategoryCount($categoryCounts, $item);
        }
        
        $categoryPercentages = [];
        if (!is_array($categoryCounts)) {
            $categoryCounts = [];
        }
        foreach ($categoryCounts as $category_id => $data) {
            $count = $data['count'];
            $categoryName = $data['name'];
            $site_setting= $data['site_id'];
            $percentage = ($count / $totalItems) * 100;
            $specifics= $this->model_shopmanager_catalog_category->getSpecific($category_id, 1); 
            $categoryPercentages[] = array(
                'category_id'   => $category_id,
                'site_id'   => $site_setting,
                'category_name' => $categoryName,
                'has_specifics'   => ($specifics)?'set':'not_set',
                'percent'      => number_format($percentage, 0)
            );
        }
        $product_name = $this->model_shopmanager_ai->getShortTitle($product_name,$categoryPercentages[0]['category_id'],'english');
        $cleanitems = $this->cleanItems($items, $product_name,$upc);
        
        $items = count($cleanitems)>0?$cleanitems:$items;
        //print("<pre>".print_r('888EBAY', true)."</pre>");
        //print("<pre>".print_r($cleanitems, true)."</pre>");
    }
    

//"<pre>".print_r($cleanitems, true)."</pre>");
  
    $finalResults = [];
    $categoryCounts = [];
    $epid_info=null;
    $totalItems = count($items);
    $name = "";
 //$finalResults['our_price']=null;
    // $price = 99999;
    
    // Liste des mots à exclure (mots qui indiquent des produits endommagés, pour pièces, etc.)
    $excludeWords = [
        'disk only', 'damage', 'scratch', 'for parts', 'broken', 'disc only','discs only',
        'defective', 'as is', 'crack', 'not working', 
        'partially working', 'missing', 'incomplete', 'dented', 'stained', 'torn', 'bent',
        'worn', 'blemish',  'faulty', 'bad condition', 'not new', 'you pick',
        'damaged', 'no return', 'no refund', 'replacement only', 'scratches', 'dent', 'blemished', 'no case',
        'scuffed', 'cracked', 'parts only', 'inoperative', 'see detail', 'see detail', 'You Can choose', 'without a case'
    ];

    foreach ($items as $key=>$item) {
        if(!isset($item['legacyItemId'])){
            die();
        }

        if(isset($item['epid'])){ 
            $epid_info[$item['epid']]=[
               'epid' => $item['epid'],
               'site_id' => $this->getEbaySiteId($item['itemLocation']['country'])
            ];
        }
        if (isset($item['buyingOptions']) && is_array($item['buyingOptions'])) {
            if (in_array('AUCTION', $item['buyingOptions'])) {
                unset($items[$key]);
                continue;
            }
        }
        $lowerTitle = strtolower($item['title']);

        $containsExcludedWord = false;
        foreach ($excludeWords as $word) {
            if (strpos($lowerTitle, $word) !== false) {
                $containsExcludedWord = true;
                break;
            }
        }

        if ($containsExcludedWord) {
            unset($items[$key]);
            continue;
        }   
            
        if (strlen($name) < strlen($item['title'])) {
            $name = $item['title'];
        }
    }
    
    // Reindex array after unset operations
    $items = array_values($items);
    
    // Normaliser itemId APRÈS avoir fini de filtrer
    foreach ($items as $key=>$item) {
        $items[$key]['itemId'] = $item['legacyItemId'];
    }

    if(is_array($epid_info)){
        //"<pre>".print_r('905', true)."</pre>");
        //"<pre>".print_r($epid_info, true)."</pre>");
        $epid = [];
        foreach($epid_info as $key=>$value){
            $epid[]=$value['epid'];
        }
        $finalResults['epid'] = is_array($epid) 
        ? $this->model_shopmanager_tools->removeArrayDuplicates($epid) 
        : ($epid !== null ? [$epid] : null);
    
        
    
        $finalResults['epid_details'] = isset($epid_info)?($this->model_shopmanager_ebay->getDetailsByepid($epid_info, $marketplace_account_id, $upc)):null;
        //"<pre>".print_r($finalResults['epid_details'], true)."</pre>");
        $formatEpidDetailsToSpecifics = isset($finalResults['epid_details']['aspects'])?$this->model_shopmanager_ebay->formatEpidDetailsToSpecifics($finalResults['epid_details']['aspects'],$upc):null;
    
    }

    $finalResults['specific_info']=$this->getDetailProductSellers($items,  $formatEpidDetailsToSpecifics??null, $upc);



   // $finalResults['specific_info']=$finalResults['detail_info']['specifics']??null;

   foreach ($items as $key=>$item) {
    //"<pre>".print_r($item, true)."</pre>");
    
    $this->updateCategoryCount($categoryCounts, $item);
    }

    $categoryPercentages = [];
    foreach ($categoryCounts as $category_id => $data) {
        $count = $data['count'];
        $categoryName = $data['name'];
        $site_setting= $data['site_id'];
        $percentage = ($count / $totalItems) * 100;
        $specifics= $this->model_shopmanager_catalog_category->getSpecific($category_id, 1); 
        $categoryPercentages[] = array(
            'category_id'   => $category_id,
            'site_id'   => $site_setting,
            'category_name' => $categoryName,
            'has_specifics'   => ($specifics)?'set':'not_set',
            'percent'      => number_format($percentage, 0)
        );
    }
//"<pre>".print_r(value: 586, )."</pre>");
//"<pre>".print_r($categoryPercentages, true)."</pre>");
if(!isset($categoryPercentages[0])){
  //print(  "<pre>".print_r(value: 586, )."</pre>");
//print("<pre>".print_r($categoryPercentages, true)."</pre>");
}else{
    $pricevariant = $this->initializePriceVariants($categoryPercentages[0]['category_id']);
}
    foreach ($items as $key=>$item) {
     //"<pre>".print_r($item, true)."</pre>");
     //"<pre>" . print_r($finalResults['detail_info']['images'], true) . "</pre>");

        if (isset($marketplace_item_id) && (in_array($item['itemId'], $marketplace_item_id))) {
            unset($items[$key]);
            continue;
        
        }else{
            $this->updatePriceVariant($pricevariant, $item);
        }
  
//print("<pre>".print_r($item, true)."</pre>");
$finalResults['items'][$key] = array_merge(
    $item, // tout ce qui est dans $item
    [ // ton tableau personnalisé qui écrase au besoin les clés de $item
        'marketplace_item_id' => $item['itemId'],
        'legacyItemId' => $item['legacyItemId'],
        'itemLocation' => $item['itemLocation'],
        'title' => $item['title'],
        'name' => $item['title'],
        'images' => $item['images'] ?? [],
        'image' => $item['image']['imageUrl'] ?? '',
        'category_id' => $item['leafCategoryIds'][0],
        'category_name' => $item['categories'][0]['categoryName'] ?? '',
        'condition' => $item['conditionId'] ?? '',
        'condition_name' => $item['condition'] ?? '',
        'url' => "https://www.ebay.com/itm/" . $item['itemId'],
        'epid' => $item['epid'] ?? null,
    ]
);

        /*$finalResults['items'][$key] = array(
            'marketplace_item_id' => $item['itemId'],
            'legacyItemId' => $item['legacyItemId'],
            'itemLocation' => $item['itemLocation'],
            'title' => $item['title'],
            'name' => $item['title'],
            'images' => $item['images']??[],
            'image' => $item['image']['imageUrl']??'',
            'category_id' => $item['leafCategoryIds'][0],
            'category_name' => $item['categories'][0]['categoryName']??'', 
            'condition' => $item['conditionId']??'',
            'condition_name' => $item['condition']??'',
            'url' =>  "https://www.ebay.com/itm/" . $item['itemId'],
            'epid' => $item['epid']??null,
        );*/
        //"<pre>".print_r($items, true)."</pre>");
           
    }
 
    unset($item);
   // die();
    // Filtrer les variantes de prix pour exclure celles avec le prix par défaut de 99999
  /* $filteredPriceVariants = array_filter($pricevariant, function ($variant) {
        return $variant['price'] != 99999;
    });*/
    $filteredPriceVariants = [];
  //"<pre>".print_r('449:ebay.php', true)."</pre>");
  //"<pre>".print_r($pricevariant, true)."</pre>");
  
    foreach ($pricevariant as $condition_key => &$variant) {
        if ($variant['price'] == 99999) {
            $variant['price'] = null;
            $variant['marketplace_item_id'] = '';
            $variant['url'] = '';
    
            if (empty($variant['condition_name'])) {
                // Prendre le premier item pour récupérer category_id
                $first_item = reset($items);
                if ($first_item) {
                    $condition_name = $this->getConditionNameById($first_item['leafCategoryIds'][0], $condition_key, $marketplace_account_id,( isset($first_item['listingMarketplaceId']) && $first_item['listingMarketplaceId']=='EBAY-MOTOR')?100:0);
                    if($condition_name){
                        $variant['condition_name']=$condition_name;
                    }else{
                        unset($pricevariant[$condition_key]);
                        continue; 
                    }
                }
            }
         
        }
        $filteredPriceVariants[$condition_key] = $variant;
    }
    unset($variant);
    
    $finalResults['pricevariant'] = $this->calculateMissingPrices($filteredPriceVariants);
    
    //"<pre>".print_r($finalResults, true)."</pre>");

   // $finalResults['name'] = $name;

   unset($item);

    // Trier les percentages par ordre décroissant
    usort($categoryPercentages, function($a, $b) {
        return $b['percent'] - $a['percent'];
    });
    
    $finalResults['category'] = $categoryPercentages;

   
    return $finalResults;
}


    private function buildGetItemRequest($marketplace_item_id,$language = 'en_US') {


        return '<?xml version="1.0" encoding="utf-8"?>
            <GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
         
                <IncludeItemCompatibilityList>true</IncludeItemCompatibilityList>
                <IncludeItemSpecifics>true</IncludeItemSpecifics>
                <DetailLevel>ReturnAll</DetailLevel>
                <ErrorLanguage>'.$language.'</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
                <ItemID>' . $marketplace_item_id . '</ItemID>
            </GetItemRequest>';

               /*    <RequesterCredentials>
                    <eBayAuthToken>' . $connectionapi['auth_token'] . '</eBayAuthToken>
                </RequesterCredentials>*/
    }

    private function buildGetMySellingRequest($page,$language = 'en_US') {


        return '<?xml version="1.0" encoding="utf-8"?>
            <GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">
              
                <ActiveList>
                    <Sort>TimeLeft</Sort>
                    <Include>true</Include>
                    <Pagination>
                        <EntriesPerPage>200</EntriesPerPage>
                        <PageNumber>' . $page . '</PageNumber>
                    </Pagination>
                </ActiveList>
                <DetailLevel>ReturnAll</DetailLevel>
                <OutputSelector>ActiveList.ItemArray.Item.ItemID</OutputSelector>
                <OutputSelector>ActiveList.ItemArray.Item.SKU</OutputSelector>
                <OutputSelector>ActiveList.ItemArray.Item.Title</OutputSelector>
                <OutputSelector>ActiveList.ItemArray.Item.StartPrice</OutputSelector>
                <OutputSelector>ActiveList.ItemArray.Item.Currency</OutputSelector>
                <OutputSelector>ActiveList.ItemArray.Item.Quantity</OutputSelector>
                <OutputSelector>ActiveList.ItemArray.Item.SellingStatus</OutputSelector>
                <OutputSelector>ActiveList.ItemArray.Item.PrimaryCategory</OutputSelector>
                <OutputSelector>ActiveList.ItemArray.Item.ConditionID</OutputSelector>
                <OutputSelector>ActiveList.ItemArray.Item.ConditionDisplayName</OutputSelector>
                <OutputSelector>ActiveList.ItemArray.Item.ItemSpecifics</OutputSelector>
                <OutputSelector>ActiveList.ItemArray.Item.ListingDetails</OutputSelector>
                <OutputSelector>ActiveList.PaginationResult</OutputSelector>
                <ErrorLanguage>'.$language.'</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
            </GetMyeBaySellingRequest>';
    }


    private function buildGetOrdersRequest($date_start, $date_end, $page, $limit, $marketplace_account_id = 1){

        return '<?xml version="1.0" encoding="utf-8"?>
        <GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
            <ErrorLanguage>en_US</ErrorLanguage>
            <WarningLevel>High</WarningLevel>
            <CreateTimeFrom>' . $date_start . '</CreateTimeFrom>
            <CreateTimeTo>' . $date_end . '</CreateTimeTo>
            <Pagination>
                <EntriesPerPage>'.$limit.'</EntriesPerPage>
                <PageNumber>' . $page . '</PageNumber>
            </Pagination>
            <OrderRole>Seller</OrderRole>
            <OrderStatus>Completed</OrderStatus>
            <SortingOrder>Descending</SortingOrder>
        </GetOrdersRequest>';

    }


    private function buildGetSellerListRequest($startTimeFrom = '', $startTimeTo = '', $limit = 100, $page = 1, $language = 'en_US') {
        if (empty($startTimeFrom) && empty($startTimeTo)) {
            //error_log("Erreur : StartTimeFrom et StartTimeTo sont manquants.");
            return null;
        }
    
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                <ErrorLanguage>' . $language . '</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
                <GranularityLevel>Coarse</GranularityLevel>';
    
        if (!empty($startTimeFrom)) {
            $xml .= '<StartTimeFrom>' . $startTimeFrom . 'T00:00:00.005Z</StartTimeFrom>';
        }
    
        if (!empty($startTimeTo)) {
            $xml .= '<StartTimeTo>' . $startTimeTo . 'T23:59:59.005Z</StartTimeTo>';
        }
    
        $xml .= '
                <IncludeWatchCount>true</IncludeWatchCount>
                 <Sort>1</Sort>
                <DetailLevel>ReturnAll</DetailLevel>
                 <GranularityLevel>Fine</GranularityLevel>
                <Pagination>
                    <EntriesPerPage>' . (int)$limit . '</EntriesPerPage>
                    <PageNumber>' . (int)$page . '</PageNumber>
                </Pagination>
                   

            </GetSellerListRequest>';
    
        return $xml;
    }

private function buildRelistItemRequest($marketplace_item_id,$site_setting = []) {
	//echo $updquantity."allo".$marketplace_item_id;
	return '<?xml version="1.0" encoding="utf-8"?>
			<RelistItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
			
				<ErrorLanguage>'.$site_setting['Language'].'</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
				<Item>
					<ItemID>'.$marketplace_item_id.'</ItemID>
				</Item>
			</RelistItemRequest>'; 
}


    /**
     * Build REST API headers (eBay Inventory/Sell APIs).
     * Derives Content-Language automatically from site_setting.
     *
     * @param mixed $marketplace_account_id
     * @param bool  $withContentType      Include Content-Type: application/json (false for GET)
     * @param bool  $withContentLanguage  Include Content-Language (required for PUT offer)
     */
    private function buildRestHeaders($marketplace_account_id, bool $withContentType = true, bool $withContentLanguage = false): array {
        $connectionapi   = $this->getApiCredentials($marketplace_account_id);
        $bearerToken     = $connectionapi['bearer_token'] ?? '';
        $siteSetting     = is_array($connectionapi['site_setting'])
            ? $connectionapi['site_setting']
            : json_decode($connectionapi['site_setting'] ?? '{}', true);

        $headers = [
            'Authorization: Bearer ' . $bearerToken,
            'Accept: application/json',
        ];

        if ($withContentType) {
            $headers[] = 'Content-Type: application/json';
        }

        if ($withContentLanguage) {
            $contentLanguage = 'en-CA';
            if (!empty($siteSetting['Language'])) {
                $contentLanguage = str_replace('_', '-', $siteSetting['Language']);
            }
            $headers[] = 'Content-Language: ' . $contentLanguage;
        }

        return $headers;
    }


    private function buildReviseCategoryItemRequest($marketplace_item_id, $category_id = 0, $site_setting = []) {


        return '<?xml version="1.0" encoding="utf-8"?>
            <ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
           
                <ErrorLanguage>'.$site_setting['Language'].'</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
                <Item>
                    <ItemID>' . $marketplace_item_id . '</ItemID>
                    <PrimaryCategory>
                        <CategoryID>' . $category_id . '</CategoryID>
                    </PrimaryCategory>
                </Item>
            </ReviseItemRequest>';
    }




    private function buildReviseItemRequest($marketplace_item_id,$category_id, $productDescriptionEbay, $quantity='',$site_setting = []) {

        // Strip <ProductListingDetails> from template — eBay uses UPC to override PrimaryCategory, we want to keep ours
        $productDescriptionEbay = preg_replace('/<ProductListingDetails>.*?<\/ProductListingDetails>/s', '', $productDescriptionEbay);

        return '<?xml version="1.0" encoding="utf-8"?>
            <ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
             
                <ErrorLanguage>'.$site_setting['Language'].'</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
                <Item>
                    <ItemID>' .  $marketplace_item_id . '</ItemID>
                    <PrimaryCategory>
                        <CategoryID>' . $category_id . '</CategoryID>
                    </PrimaryCategory>
                    ' . $productDescriptionEbay . $quantity . '
                </Item>
            </ReviseItemRequest>';
    }


    private function buildRevisePriceItemRequest($marketplace_item_id, $price='',$made_in_country_id=null,$site_setting = []) {


        return '<?xml version="1.0" encoding="utf-8"?>
            <ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
           
                <ErrorLanguage>'.$site_setting['Language'].'</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
                <Item>
                    <ItemID>' . $marketplace_item_id . '</ItemID>
                    ' . $price . '
                      '.$this->getLocation($made_in_country_id??NULL,$site_setting).'
                </Item>
            </ReviseItemRequest>';
    }


    private function buildReviseQuantityItemRequest($marketplace_item_id, $quantity=0,$made_in_country_id=null, $site_setting = []) {


        return '<?xml version="1.0" encoding="utf-8"?>
            <ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
           
                <ErrorLanguage>'.$site_setting['Language'].'</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
                <Item>
                    <ItemID>' . $marketplace_item_id . '</ItemID>
                    <Quantity>' . $quantity . '</Quantity>
                     '.$this->getLocation($made_in_country_id??NULL,$site_setting).'
                </Item>
            </ReviseItemRequest>';
    }


public function calculateMissingPrices($ebay_pricevariant) {
    $last_reduction_factor = 0.90;

    // Trier dans l'ordre des conditions (plus neuf → plus usé)
    ksort($ebay_pricevariant);
    $keys = array_keys($ebay_pricevariant);

    // Étape 1: Nettoyer les prix incohérents (Open Box ne peut pas coûter plus que New, etc.)
    $last_valid_price = null;
    foreach ($keys as $key) {
        $price = isset($ebay_pricevariant[$key]['price']) ? floatval($ebay_pricevariant[$key]['price']) : null;
        if ($price === null || $price === 0.0) continue;
        if ($last_valid_price === null) {
            $last_valid_price = $price;
        } elseif ($price <= $last_valid_price) {
            $last_valid_price = $price;
        } else {
            // Prix incohérent: supprimer le prix et les détails
            $ebay_pricevariant[$key]['price'] = null;
            $ebay_pricevariant[$key]['marketplace_item_id'] = '';
            $ebay_pricevariant[$key]['url'] = '';
            if (isset($ebay_pricevariant[$key]['title'])) {
                $ebay_pricevariant[$key]['title'] = '';
            }
        }
    }

    // Étape 2: Collecter les positions des anchors (vrais prix connus)
    $anchor_positions = [];
    foreach ($keys as $pos => $key) {
        if (!empty($ebay_pricevariant[$key]['price'])) {
            $anchor_positions[] = $pos;
        }
    }

    if (empty($anchor_positions)) {
        return $ebay_pricevariant;
    }

    // Étape 3: Interpoler linéairement entre chaque paire d'anchors consécutifs
    // Ex: Brand New=$16 et Very Good=$12.99 → Like New/Excellent interpolés entre les deux
    for ($a = 0; $a < count($anchor_positions) - 1; $a++) {
        $left_pos   = $anchor_positions[$a];
        $right_pos  = $anchor_positions[$a + 1];
        $left_price  = floatval($ebay_pricevariant[$keys[$left_pos]]['price']);
        $right_price = floatval($ebay_pricevariant[$keys[$right_pos]]['price']);
        $left_currency = $ebay_pricevariant[$keys[$left_pos]]['currency'] ?? 'USD';
        $steps = $right_pos - $left_pos;

        for ($i = $left_pos + 1; $i < $right_pos; $i++) {
            $key = $keys[$i];
            if (empty($ebay_pricevariant[$key]['price'])) {
                $t = ($i - $left_pos) / $steps;
                $ebay_pricevariant[$key]['price']         = round($left_price + ($right_price - $left_price) * $t, 2);
                $ebay_pricevariant[$key]['currency']      = $left_currency;
                $ebay_pricevariant[$key]['is_calculated'] = true;
            }
        }
    }

    // Étape 4: Extrapoler vers le haut (plus neuf que le premier anchor connu)
    $first_pos     = $anchor_positions[0];
    $current_price = floatval($ebay_pricevariant[$keys[$first_pos]]['price']);
    $first_currency = $ebay_pricevariant[$keys[$first_pos]]['currency'] ?? 'USD';
    for ($i = $first_pos - 1; $i >= 0; $i--) {
        $key = $keys[$i];
        if (empty($ebay_pricevariant[$key]['price'])) {
            $current_price /= $last_reduction_factor;
            $ebay_pricevariant[$key]['price']         = round($current_price, 2);
            $ebay_pricevariant[$key]['currency']      = $first_currency;
            $ebay_pricevariant[$key]['is_calculated'] = true;
        }
    }

    // Étape 5: Extrapoler vers le bas (plus usé que le dernier anchor connu)
    $last_pos      = $anchor_positions[count($anchor_positions) - 1];
    $current_price = floatval($ebay_pricevariant[$keys[$last_pos]]['price']);
    $last_currency = $ebay_pricevariant[$keys[$last_pos]]['currency'] ?? 'USD';
    for ($i = $last_pos + 1; $i < count($keys); $i++) {
        $key = $keys[$i];
        if (empty($ebay_pricevariant[$key]['price'])) {
            $current_price *= $last_reduction_factor;
            $ebay_pricevariant[$key]['price']         = round($current_price, 2);
            $ebay_pricevariant[$key]['currency']      = $last_currency;
            $ebay_pricevariant[$key]['is_calculated'] = true;
        }
    }

    return $ebay_pricevariant;
}

  
    public function checkApiLimit($apiName = 'FindingService', $marketplace_account_id = 1) {
        $url = 'https://api.ebay.com/developer/analytics/v1_beta/rate_limit/';
        
        $queryParams = http_build_query([
            'api_context' => 'tradingapi',
           // 'api_name' => $apiName
        ]);
        
        $connectionapi = $this->getApiCredentials($marketplace_account_id);
        
        $headers = [
            "Authorization: Bearer " . $connectionapi['bearer_token'],
            "Content-Type: application/json",
            "Accept: application/json"
        ];
        
        $ch = curl_init("$url?$queryParams");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //"<pre>".print_r (36,true )."</pre>");
        //"<pre>".print_r ($httpCode,true )."</pre>");
        //"<pre>".print_r (json_decode($response,true),true )."</pre>");

        if ($httpCode == 200 && $response !== false) {
            $responseArray = json_decode($response, true);
            if (isset($responseArray['rateLimits'])) {
                foreach ($responseArray['rateLimits'] as $limit) {
                    echo "API : " . $limit['api_name'] . "\n";
                    echo "Appels maximum : " . $limit['callLimit'] . "\n";
                    echo "Appels restants : " . $limit['remainingQuota'] . "\n";
                    echo "Réinitialisation à : " . $limit['resetTime'] . "\n";
                    echo "--------------------------\n";
                }
            } else {
                echo "Erreur : Pas de données de limite reçues.\n";
            }
        } else {
            echo "Erreur : Impossible de récupérer les limites d'API.\n";
        }
    }

private function checkEbayImage($imageUrl, $min = 600) {
    // Vérifier si l'URL est valide et accessible
   $data= explode('s-l',$imageUrl);
   if(isset($data[1])){
        $data2=explode('.',$data[1]);
        if($data2[0] >= $min){
                return TRUE;
        }else{
                return FALSE;
        }
    }else{
        return $this->checkEbayImageLong($imageUrl);
       //"<pre>" . print_r($data, true) . "</pre>");
      //  die();
    }
}

private function checkEbayImageLong($imageUrl, $minWidth = 400, $minHeight = 600) {
    // Vérifier si l'URL est valide et accessible
    if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        return true; // URL invalide considérée comme mauvaise
    }
    // Utiliser getimagesize pour obtenir les dimensions de l'image
    $imageSize = @getimagesize($imageUrl);
    if ($imageSize === false) {
        return true; // Impossible d'obtenir les dimensions, image considérée comme mauvaise
    }
    list($height,$width ) = $imageSize;
       
            // Retourner la largeur, la hauteur, et le type d'image
    return ($width < $minWidth || $height < $minHeight)?FALSE:TRUE;
}


/**
 * Clean card title by removing redundant set name prefix and ensuring # before card number
 * Example: "1998-99 UD Choice #SQ1 Wayne Gretzky StarQuest Red" → "#SQ1 Wayne Gretzky StarQuest Red"
 * Example: "1998-99 UD Choice SQ1 Wayne Gretzky StarQuest Red" → "#SQ1 Wayne Gretzky StarQuest Red"
 * 
 * @param string $title The full card title from oc_card
 * @param string $set_name The set name to remove (if present at start)
 * @return string Cleaned title (max 65 chars)
 */

private function cleanCustomizedCardTitle(string $title, string $set_name = ''): string {
    $decodeHtmlEntitiesDeep = static function(string $value): string {
        $decoded = $value;

        for ($i = 0; $i < 4; $i++) {
            $next = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($next === $decoded) {
                break;
            }
            $decoded = $next;
        }

        $decoded = str_replace("\xC2\xA0", ' ', $decoded);
        return trim($decoded);
    };

    $title = $decodeHtmlEntitiesDeep($title);
    $set_name = $decodeHtmlEntitiesDeep($set_name);

    // Remove set_name prefix if present (case-insensitive)
    if (!empty($set_name)) {
        $set_pattern = '/^' . preg_quote(trim($set_name), '/') . '\s*/i';
        $title = preg_replace($set_pattern, '', $title);
    }
    
    // Trim whitespace
    $title = trim($title);
    
    // Ensure # before card number if not present
    // Match first alphanumeric sequence that looks like a card number (not preceded by #)
    // Example: "SQ1" → "#SQ1", "123" → "#123", but "#SQ1" stays "#SQ1"
    if (!preg_match('/^#/', $title)) {
        // Add # before first word/number combination if it looks like a card number
        $title = preg_replace('/^([A-Z0-9]+\b)/i', '#$1', $title);
    }
    
    // Truncate to 65 characters max (eBay Customized limit)
    if (strlen($title) > 65) {
        $title = substr($title, 0, 65);
    }
    
    return $title;
}

private function normalizeEbayText(string $text): string {
    $decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $decoded = str_replace("\xC2\xA0", ' ', $decoded);
    return trim($decoded);
}

private function hasEncodedHtmlEntity(string $text): bool {
    return preg_match('/&(?:quot|amp|apos|#0?39|lt|gt);/i', $text) === 1;
}

public function cleanItems($items, $product_name, $upc) {
    if (empty($items) || empty($product_name)) {
        return $items;
    }
    $product_name = trim($product_name);
    $product_name_lower = strtolower($product_name);
    $upc_str = (string)$upc;
    
    // Séparer le product_name en mots-clés (ignorer les mots courts/communs)
    $keywords = array_filter(explode(' ', $product_name_lower), function($word) {
        $ignore = ['the', 'and', 'or', 'for', 'in', 'on', 'at', 'to', 'a', 'an'];
        return strlen($word) > 2 && !in_array($word, $ignore);
    });
    
    foreach ($items as $key => $item) {
        unset($item['itemWebUrl']); // Supprimer les champs non nécessaires pour la comparaison
        $item_json = strtolower(json_encode($item));

        // Vérifier si UPC est présent - si oui, garder l'item
        if (!empty($upc) && $upc!='' && strpos($item_json, $upc_str) !== false) {
            continue; // UPC trouvé, on garde l'item
        }

        // Vérifier si TOUS les mots-clés importants sont présents
        $all_keywords_found = true;
        foreach ($keywords as $keyword) {
            if (strpos($item_json, $keyword) === false) {
                $all_keywords_found = false;
                break;
            }
        }
        
        if (!$all_keywords_found) {
            unset($items[$key]); // Supprime l'item si tous les mots-clés ne sont pas trouvés
            continue;
        }
    }

    return array_values($items);
}


private function createInventoryItem($variation, $template_data, $headers, $merchantLocationKey, $variation_index, $site_setting): array {
    // Lire la clé d'inventaire depuis la DB — générée à la création de la carte
    $sku = !empty($variation['inventory_key'])
        ? $variation['inventory_key']
        : (function() use ($variation, $site_setting) {
            $clean = preg_replace('/[^A-Za-z0-9]/', '', $variation['card_number'] ?? '');
            return substr('CA_CARD_' . $clean . '_' . $variation['card_id'], 0, 50);
          })();
    
    // Nettoyer les images
    $imageUrls = [];
    if (isset($variation['images']) && is_array($variation['images'])) {
        foreach ($variation['images'] as $img) {
            if (is_string($img) && !empty(trim($img))) {
                $imageUrls[] = trim($img);
            }
        }
    }
    
    if (empty($imageUrls)) {
        $imageUrls = ['https://i.ebayimg.com/images/g/placeholder.jpg'];
    }
    
    $imageUrls = array_values(array_slice($imageUrls, 0, 24));
    
    // ✅ CONSTRUIRE LA VALEUR CUSTOMIZED À PARTIR DU TITRE DE LA CARTE
    // Utiliser le titre complet et enlever le préfixe redondant du set
    $set_name = $template_data['set_name'] ?? '';
    $card_title = $variation['title'] ?? '';
    
    $customized_value = '';
    if (!empty($card_title)) {
        $customized_value = $this->cleanCustomizedCardTitle($card_title, $set_name);
    }
    
    // ✅ COPIER LES ASPECTS DE BASE (sans les aspects de variation)
    $item_aspects = $template_data['aspects'];
    unset($item_aspects['Team']);
    unset($item_aspects['Player/Athlete']);
    unset($item_aspects['Card Number']);
    unset($item_aspects['Customized']);
    
    // ✅ AJOUTER CUSTOMIZED AVEC SA VALEUR SPÉCIFIQUE
    if (!empty($customized_value)) {
        $item_aspects['Customized'] = [$customized_value];
    }
    
    // CRÉER L'INVENTORY ITEM
    $normalizedVariationTitle = $this->normalizeEbayText((string)($variation['title'] ?? ''));

    $normalizedVariationDescription = substr($normalizedVariationTitle, 0, 80);

    if ($this->hasEncodedHtmlEntity($normalizedVariationTitle) || $this->hasEncodedHtmlEntity($normalizedVariationDescription)) {
        $this->log->write('[createInventoryItem][warn_html_entity] sku=' . $sku . ' title=' . $normalizedVariationTitle . ' description=' . $normalizedVariationDescription);
    }

    $itemData = [
        "availability" => [
            "shipToLocationAvailability" => [
                "quantity" => (int)$variation['quantity'],
                "merchantLocationKey" => $merchantLocationKey
            ]
        ],
        "condition" => "USED_VERY_GOOD",
        "conditionDescriptors" => [
            [
                "name" => "40001",
                "values" => ["400011"]
            ]
        ],
        "product" => [
            "title" => substr($normalizedVariationTitle, 0, 80),
            "aspects" => $item_aspects,  // ← AVEC Customized spécifique!
            "brand" => $variation['brand'],
            "description" => $normalizedVariationDescription,
            "imageUrls" => $imageUrls
        ]
    ];
    
    $url = "https://api.ebay.com/sell/inventory/v1/inventory_item/" . urlencode($sku);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($itemData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
   //print("<div style='background: #f0f8ff; padding: 15px; margin: 15px; border: 2px solid #4CAF50;'>");
   //print("<h3>Creating Inventory Item for SKU: {$sku}</h3>");
   //print("HTTP Code: {$httpCode}<br>");
   
   // HTTP 204 = Success with no content (normal for inventory item creation)
   if ($httpCode == 204 || empty($response)) {
    //$this->log->write("[createInventoryItem] Success for SKU: {$sku} - HTTP Code: {$httpCode}");

   } else {
        $responseDecoded = json_decode($response, true);
        $errorIds = array_column($responseDecoded['errors'] ?? [], 'errorId');

        if (in_array(25019, $errorIds)) {
            // Error 25019: eBay blocks ANY update while item is part of an active sale.
            // Cannot work around this — skip gracefully.
            //$this->log->write("[createInventoryItem] SKIP SKU: {$sku} — part of active sale (25019), cannot update. Skipping.");
            return [
                'sku' => $sku,
                'status' => 'skipped_sale_active',
                'customized' => $customized_value
            ];
        }

        $this->log->write("[createInventoryItem] Failed for SKU: {$sku} - HTTP Code: {$httpCode} - Response: " . json_encode($responseDecoded, JSON_PRETTY_PRINT));
   }

    if ($httpCode >= 200 && $httpCode < 300) {
        return [
            'sku' => $sku,
            'status' => 'success',
            'customized' => $customized_value
        ];
    } else {
        return [
            'sku' => $sku,
            'error' => json_decode($response, true),
            'status' => 'failed'
        ];
    }
}


private function createInventoryItemGroup($template_data, $headers, $site_setting): array {
    $groupKey = $template_data['group_key'];
    
    // Utiliser SEULEMENT Customized avec les titres nettoyés pour éviter les conflits
    // Exemple: "1998-99 UD Choice #SQ1 Wayne Gretzky StarQuest Red" → "#SQ1 Wayne Gretzky StarQuest Red"
    
    $customized_values = [];
    $set_name = $template_data['set_name'] ?? '';
    
    // Construire les valeurs Customized à partir des titres de chaque variation
    foreach ($template_data['variations'] as $variation) {
        $title = $variation['title'] ?? '';
        if (!empty($title)) {
            $cleaned_title = $this->cleanCustomizedCardTitle($title, $set_name);
            if (!empty($cleaned_title)) {
                $customized_values[] = $cleaned_title;
            }
        }
    }

    // Listing multi-cartes : retirer les aspects de variation (propres à chaque item)
    // Listing 1 carte : garder tous les aspects dans le groupe
    if (count($template_data['variations']) > 1) {
        unset($template_data['aspects']['Team']);
        unset($template_data['aspects']['Customized']);
        unset($template_data['aspects']['Player/Athlete']);
        unset($template_data['aspects']['Card Number']);
    } else {
        unset($template_data['aspects']['Customized']);
    }

    // Images - Prendre les 2 premières de chaque carte pour le listing principal
    $imageUrls = [];
    $variantSKUs = [];
    
    foreach ($template_data['variations'] as $variation) {
        // Lire la clé d'inventaire depuis la DB (générée à la création de la carte)
        $sku = !empty($variation['inventory_key'])
            ? $variation['inventory_key']
            : substr('CA_CARD_' . preg_replace('/[^A-Za-z0-9]/', '', $variation['card_number'] ?? '') . '_' . $variation['card_id'], 0, 50);
        $variantSKUs[] = $sku;
        
        // Prendre seulement les 2 premières images de chaque carte
        if (isset($variation['images']) && is_array($variation['images'])) {
            $card_images = array_slice($variation['images'], 0, 2); // 2 premières images
            foreach ($card_images as $imgUrl) {
                if (!in_array($imgUrl, $imageUrls) && count($imageUrls) < 24) {
                    $imageUrls[] = $imgUrl;
                }
            }
        }
    }

    //$this->log->write('[createInventoryItemGroup] groupKey=' . $groupKey . ' aspects=' . json_encode($template_data['aspects'], JSON_PRETTY_PRINT) . ' customized_values=' . json_encode($customized_values, JSON_PRETTY_PRINT) . ' variantSKUs=' . json_encode($variantSKUs, JSON_PRETTY_PRINT) . ' imageUrls=' . json_encode($imageUrls, JSON_PRETTY_PRINT));

    // eBay exige au moins 1 image dans le groupe
    if (empty($imageUrls)) {
        $imageUrls = ['https://i.ebayimg.com/images/g/placeholder.jpg'];
    }

    // Sanitize description: strip invalid UTF-8 that would break json_encode → empty body → eBay 25709
    // Use full 'description' (with header/footer images + policies template) first; fall back to raw_description if empty
    $rawDesc = !empty($template_data['description']) ? $template_data['description'] : ($template_data['raw_description'] ?? '');
    $rawDesc = $this->normalizeEbayText((string)$rawDesc);
    $cleanDescription = iconv('UTF-8', 'UTF-8//IGNORE', $rawDesc);
    // eBay Inventory REST API accepts up to 500,000 chars — no need to truncate normal descriptions

    $groupTitle = substr($this->normalizeEbayText((string)($template_data['title'] ?? '')), 0, 80);
    if ($this->hasEncodedHtmlEntity($groupTitle) || $this->hasEncodedHtmlEntity((string)$cleanDescription)) {
        $this->log->write('[createInventoryItemGroup][warn_html_entity] group=' . $groupKey . ' title=' . $groupTitle);
    }

    $groupData = [
        "aspects"     => $template_data['aspects'],
        "description" => $cleanDescription,
        "imageUrls"   => $imageUrls,
        "title"       => $groupTitle,
        "variantSKUs" => $variantSKUs,
        "variesBy"    => [
            "specifications" => [
                [
                    "name"   => "Customized",
                    "values" => array_values($customized_values)
                ]
            ],
            "aspectsImageVariesBy" => ["Customized"]
        ]
    ];

    $encodedGroupData = json_encode($groupData);
    if ($encodedGroupData === false) {
        // Fallback: sanitize every string in groupData
        array_walk_recursive($groupData, function (&$val) {
            if (is_string($val)) {
                $val = iconv('UTF-8', 'UTF-8//IGNORE', $val);
            }
        });
        $encodedGroupData = json_encode($groupData, JSON_INVALID_UTF8_SUBSTITUTE);
        //$this->log->write('[createInventoryItemGroup] json_encode needed fallback sanitize: ' . json_last_error_msg());
    }
    //$this->log->write('[createInventoryItemGroup] groupData=' . substr($encodedGroupData ?: '(encode failed)', 0, 800));

    $url = "https://api.ebay.com/sell/inventory/v1/inventory_item_group/" . urlencode($groupKey);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedGroupData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    //print("<div style='background: #e8f4f8; padding: 15px; margin: 15px; border: 2px solid #4CAF50;'>");
    //print("<h3>Creating Inventory Item Group with groupKey: {$groupKey}</h3>");
    //print("HTTP Code: {$httpCode}<br>");
    //print("<pre>" . json_encode(json_decode($response, true), JSON_PRETTY_PRINT) . "</pre>");
    //print("</div>");
    
    if ($httpCode >= 200 && $httpCode < 300) {
        //$this->log->write('[createInventoryItemGroup] Success for groupKey: ' . $groupKey . ' - HTTP Code: ' . $httpCode);
        //$this->log->write('[createInventoryItemGroup] Response: ' . json_encode($response, JSON_PRETTY_PRINT));
        return ['groupKey' => $groupKey, 'status' => 'success'];
    } else {
        $error_details = json_decode($response, true);
        $groupErrorIds = array_column($error_details['errors'] ?? [], 'errorId');

        // Error 25019: eBay blocks ANY revision while listing is part of an active sale.
        // Cannot send price (nor any field) — skip cleanly without logging as failure.
        if (in_array(25019, $groupErrorIds)) {
            //$this->log->write('[createInventoryItemGroup] SKIP group: ' . $groupKey . ' — part of active sale (25019). Cannot revise listing during sale.');
            return ['groupKey' => $groupKey, 'status' => 'skipped_sale_active'];
        }

        $this->log->write('[createInventoryItemGroup] eBay Inventory Group Creation Failed: ' . $groupKey . ' - HTTP: ' . $httpCode . ' - Error: ' . json_encode($error_details, JSON_PRETTY_PRINT));

        // ── Erreur 25703 : SKU déjà membre d'un AUTRE groupe (ancien ghost group) ──
        // On extrait l'ancien groupId, on le supprime, et on réessaye une fois.
        $staleGroupKey = null;
        foreach ($error_details['errors'] ?? [] as $err) {
            if (($err['errorId'] ?? 0) === 25703) {
                foreach ($err['parameters'] ?? [] as $param) {
                    if (($param['name'] ?? '') === 'text2') {
                        $staleGroupKey = $param['value'];
                        break 2;
                    }
                }
            }
        }

        if ($staleGroupKey && $staleGroupKey !== $groupKey) {
            //$this->log->write('[createInventoryItemGroup] Deleting stale group: ' . $staleGroupKey . ' then retrying ' . $groupKey);
            $this->deleteInventoryItemGroup($staleGroupKey, $headers);
            sleep(2);

            // Retry
            $ch2 = curl_init($url);
            curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch2, CURLOPT_POSTFIELDS, $encodedGroupData);
            curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            $response2  = curl_exec($ch2);
            $httpCode2  = curl_getinfo($ch2, CURLINFO_HTTP_CODE);

            if ($httpCode2 >= 200 && $httpCode2 < 300) {
                //$this->log->write('[createInventoryItemGroup] Retry success after stale-group cleanup: ' . $groupKey . ' HTTP=' . $httpCode2);
                return ['groupKey' => $groupKey, 'status' => 'success'];
            }
            $error_details = json_decode($response2, true);
            $this->log->write('[createInventoryItemGroup] Retry failed: ' . $groupKey . ' HTTP=' . $httpCode2 . ' Error=' . json_encode($error_details));
        }

        return ['groupKey' => $groupKey, 'error' => $error_details];
    }
}

	public function endListing($marketplace_item_id,$marketplace_account_id=null,$site_setting = []){ //UTILISER

		$postFields = $this->buildEndItemRequest($marketplace_item_id,$site_setting);
		//"<pre>".print_r ($postFields,true )."</pre>");
        //"<pre>".print_r (value: '254:ebay.php' )."</pre>");
		$headers = $this->buildEbayHeaders('EndItem',1077,$marketplace_account_id);
	//"<pre>".print_r ($headers,true )."</pre>");
		$response	=	$this->makeCurlRequest('https://api.ebay.com/ws/api.dll', $headers, $postFields) ;
		$responseXml = simplexml_load_string($response);
		$responseArray = json_decode(json_encode($responseXml), true);

        $this->load->model('shopmanager/marketplace');
            
        if (isset($responseArray['ErrorLanguage'])) {
            $error = json_encode($responseArray);
        } elseif (isset($responseArray['Ack']) && $responseArray['Ack'] != 'Failure') {
            $error = '';
        } else {
            $error = json_encode($responseArray);
        }

        $_pm_row = $this->model_shopmanager_marketplace->getProductMarketplaceRow($responseArray['ItemID'] ?? null);
        if ($_pm_row) {
            $_pm_row['error'] = $error;
            $_pm_row['to_update'] = empty($error) ? 0 : 9;
            $this->model_shopmanager_marketplace->editProductMarketplace($_pm_row);
        }

        //$responseArray = json_decode($response, true);
		return $responseArray;


		
	//	$xml = new SimpleXMLElement($response);
	}


/**
 * Delete an inventory item group from eBay
 */
private function deleteInventoryItemGroup($group_key, $headers): array {
    $url = "https://api.ebay.com/sell/inventory/v1/inventory_item_group/" . urlencode($group_key);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    
    //$this->log->write('eBay deleteInventoryItemGroup: group_key=' . $group_key . ' HTTP=' . $httpCode . ' Response=' . substr($response, 0, 200));
    
    return [
        'group_key' => $group_key,
        'httpCode' => $httpCode,
        'deleted' => ($httpCode == 204 || $httpCode == 404),
        'response' => $response
    ];
}




/**
 * Delete all inventory items for a listing (cleanup)
 */
private function deleteInventoryItems($template_data, $headers, $site_setting): array {
    $results = [];
    $this->load->model('shopmanager/card/card');

        // ── Étape 1 : supprimer les offers eBay liées à ce listing (B1)
        //    Les offers doivent être supprimées AVANT les inventory items
        $listing_id = (int)($template_data['listing_id'] ?? 0);
        if ($listing_id > 0) {
            $offers = $this->getOffersForListing($listing_id, $headers);
            foreach ($offers as $offer) {
                if (!empty($offer['offerId'])) {
                    $this->deleteOffer($offer['offerId'], $headers);
                }
            }
        }

        // ── Étape 2 : supprimer le groupe d'inventaire (B2)
        $group_key = $template_data['inventory_item_group_key'] ?? $template_data['group_key'] ?? null;
        if (!$group_key && isset($template_data['country_code'], $template_data['listing_id'])) {
            $group_key = $template_data['country_code'] . '_CARD_LIST_' . ($template_data['batch_name'] ?? 1) . '_' . (int)$template_data['listing_id'];
        }
        if ($group_key) {
            $this->deleteInventoryItemGroup($group_key, $headers);
        }

        // ── Étape 3 : supprimer les inventory items (logique originale ci-dessous)

    foreach ($template_data['variations'] as $variation) {
        // Lire la clé d'inventaire depuis la DB (générée à la création de la carte)
        $sku = !empty($variation['inventory_key'])
            ? $variation['inventory_key']
            : substr('CA_CARD_' . preg_replace('/[^A-Za-z0-9]/', '', $variation['card_number'] ?? '') . '_' . $variation['card_id'], 0, 50);
        $url = "https://api.ebay.com/sell/inventory/v1/inventory_item/" . urlencode($sku);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Fermer le handle curl
        $this->model_shopmanager_card_card->updateCardOffer($variation['card_id']);
        // Log pour debugging
        //$this->log->write('eBay deleteInventoryItem: SKU=' . $sku . ' HTTP=' . $httpCode . ' Response=' . substr($response, 0, 200));
        
        $results[] = [
            'sku' => $sku,
            'httpCode' => $httpCode,
            'deleted' => ($httpCode == 204 || $httpCode == 404), // 204 = deleted, 404 = already gone
            'response' => $response
        ];
    }
    
    return $results;
}


/**
 * Delete an offer from eBay inventory
 */
private function deleteOffer($offer_id, $headers): array {
    $url = "https://api.ebay.com/sell/inventory/v1/offer/" . urlencode($offer_id);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    
    //$this->log->write('eBay deleteOffer: offer_id=' . $offer_id . ' HTTP=' . $httpCode . ' Response=' . substr($response, 0, 200));
    
    return [
        'offer_id' => $offer_id,
        'httpCode' => $httpCode,
        'deleted' => ($httpCode == 204 || $httpCode == 404),
        'response' => $response
    ];
}

/**
 * Retire de eBay les offres tracées dans oc_card_pending_delete pour ce listing.
 * Appelé avant la boucle de sync dans editCardListing().
 * HTTP 204 ou 404 = succès (déjà supprimée) → processed=1.
 * Tout autre code = échec → on conserve processed=0 pour retry ultérieur.
 */
public function processPendingDeletes(int $listing_id, array $headers): array {
    $pendingRows = $this->db->query(
        "SELECT * FROM `" . DB_PREFIX . "card_pending_delete`
          WHERE `listing_id` = " . (int)$listing_id . "
            AND `processed`  = 0
            AND `offer_id`   IS NOT NULL
            AND `offer_id`  != ''
          ORDER BY `id` ASC"
    )->rows;

    $processed = 0;
    $failed    = 0;

    foreach ($pendingRows as $row) {
        $result = $this->deleteOffer($row['offer_id'], $headers);
        if ($result['deleted']) {
            $this->db->query(
                "DELETE FROM `" . DB_PREFIX . "card_pending_delete`
                 WHERE `id` = " . (int)$row['id']
            );
            $processed++;
        } else {
            $this->log->write(
                "[processPendingDeletes] listing={$listing_id} offer_id={$row['offer_id']}"
                . " HTTP={$result['httpCode']} — will retry next sync"
            );
            $failed++;
        }
    }

    return ['processed' => $processed, 'failed' => $failed];
}


    /**
     * POST /bulk_create_offer — creates up to 25 offers per batch call.
     * Returns array indexed by sku:
     *   ['offerId' => string|null, 'statusCode' => int, 'error' => string|null]
     */
    private function doBulkCreateOffer(array $offersData, array $headers): array {
        $results = [];

        // Pre-fill with placeholder
        foreach ($offersData as $offerData) {
            $results[$offerData['sku']] = ['offerId' => null, 'statusCode' => 0, 'error' => 'not sent'];
        }

        foreach (array_chunk($offersData, 25) as $batch) {
            $payload  = ['requests' => $batch];
            $response = $this->makeCurlRestRequest(
                'https://api.ebay.com/sell/inventory/v1/bulk_create_offer',
                'POST', $headers, $payload
            );

            //$this->log->write('[doBulkCreateOffer] HTTP=' . $response['httpCode'] . ' batch_size=' . count($batch));

            if ($response['error'] || ($response['httpCode'] ?? 0) >= 500) {
                $err = $response['error'] ?: 'HTTP ' . $response['httpCode'];
                foreach ($batch as $offer) {
                    $results[$offer['sku']] = ['offerId' => null, 'statusCode' => $response['httpCode'] ?? 0, 'error' => $err];
                }
                continue;
            }

            foreach ($response['body']['responses'] ?? [] as $r) {
                $sku        = $r['sku']        ?? '';
                $offerId    = $r['offerId']    ?? null;
                $statusCode = $r['statusCode'] ?? 0;
                $errMsg     = null;

                if (!empty($r['errors'])) {
                    $e      = $r['errors'][0];
                    $errMsg = $e['longMessage'] ?? $e['message'] ?? json_encode($e, JSON_UNESCAPED_UNICODE);
                    // Error 25729 (duplicate combo) or 25002 (already exists) — extract existing offerId
                    $eId = (int)($e['errorId'] ?? 0);
                    if ($eId === 25729 || $eId === 25002) {
                        $offerId = $e['parameters'][0]['value'] ?? null;
                        $errMsg  = null; // treat as success
                    }
                }

                if ($sku) {
                    $results[$sku] = ['offerId' => $offerId, 'statusCode' => $statusCode, 'error' => $errMsg];
                }
            }
        }

        return $results;
    }

    
   
    public function editListing($product, $updquantity = 0, $site_setting = [], $marketplace_accounts = []) {
        $this->log->write('[edit() START] product_id=' . ($product['product_id'] ?? 'NULL') . ' category_id=' . ($product['category_id'] ?? 'NULL') . ' qty=' . $updquantity . ' accounts=' . count($marketplace_accounts));
        $this->load->model('shopmanager/ebaytemplate');
        $this->load->model('shopmanager/catalog/product');
        $this->load->model('localisation/currency');
        $this->log->write('[edit() currency key] ' . ($site_setting['Currency']['Currency'] ?? 'MISSING'));
        $currency_info = $this->model_localisation_currency->getCurrencyByCode($site_setting['Currency']['Currency']);
        
       // $product['price'] = $product['price'] * $currency_info['value'];
        
        $responseArray = [];
        

        foreach ($marketplace_accounts as $marketplace_id => $marketplace) {
            $marketplace_item_id = (int)$marketplace['marketplace_item_id'];
            $marketplace_account_id = (int)$marketplace['marketplace_account_id'];
            $quantity = is_numeric($updquantity) ? "<Quantity>$updquantity</Quantity>" : '';
            $site_setting = $marketplace['site_setting'];

            $productDescriptionEbay = $this->model_shopmanager_ebaytemplate->getEbayTemplate($product, $site_setting, $marketplace_account_id);
            $postFields = $this->buildReviseItemRequest($marketplace_item_id, $product['category_id'], $productDescriptionEbay, $quantity, $site_setting);
            $this->log->write('[edit() postFields product_id=' . $product['product_id'] . '] ' . $postFields);
            $headers = $this->buildEbayHeaders("ReviseItem", 1371, $marketplace_account_id);
            $response = $this->makeCurlRequest('https://api.ebay.com/ws/api.dll', $headers, $postFields);
            $responseXml = simplexml_load_string($response);
            $responseArray = json_decode(json_encode($responseXml), true);
            $this->log->write('[edit() RESPONSE product_id=' . $product['product_id'] . '] Ack=' . ($responseArray['Ack'] ?? 'NULL') . ' Error=' . json_encode($responseArray['Errors'] ?? []));

            // Vérifier si eBay a changé la catégorie (ErrorCode 21917164)
            $retryWithoutUPC = false;
            if (isset($responseArray['Errors'])) {
                foreach ($responseArray['Errors'] as $err) {
                    if (
                        (isset($err['ErrorCode']) && $err['ErrorCode'] == '21917164') ||
                        (isset($err[0]['ErrorCode']) && $err[0]['ErrorCode'] == '21917164')
                    ) {
                        $upc = '';
                        // Cherche l'UPC dans ItemSpecifics si dispo
                        if (isset($product['upc']) && !empty($product['upc'])) {
                            $upc = $product['upc'];
                        } elseif (isset($product['item_specifics']) && is_array($product['item_specifics'])) {
                            foreach ($product['item_specifics'] as $spec) {
                                if (
                                    (isset($spec['name']) && strtolower($spec['name']) == 'upc') ||
                                    (isset($spec['Name']) && strtolower($spec['Name']) == 'upc')
                                ) {
                                    $upc = $spec['value'] ?? $spec['Value'] ?? '';
                                    break;
                                }
                            }
                        }
                        if (!empty($upc)) {
                            $catalog_epid = $this->findProductIDByGTIN($upc);
                            $this->log->write('[edit() CATALOG LOOKUP] UPC=' . $upc . ' => eBay catalog productId=' . print_r($catalog_epid, true));
                        } else {
                            $this->log->write('[edit() CATALOG LOOKUP] UPC not found in product data.');
                        }

                        // Appel GetItem pour logguer la vraie catégorie eBay imposée
                        $item_id = $responseArray['ItemID'] ?? $marketplace_item_id;
                        $cat_details = $this->getItemDetails($item_id, $marketplace_account_id);
                        if ($cat_details && isset($cat_details['category_id'])) {
                            $this->log->write('[edit() GETITEM CATEGORY] item_id=' . $item_id . ' => eBay PrimaryCategoryID=' . $cat_details['category_id']);
                        } else {
                            $this->log->write('[edit() GETITEM CATEGORY] item_id=' . $item_id . ' => No category found in GetItem response');
                        }

                        // Supprimer l'annonce eBay (endListing) puis republier avec les mêmes données
                        $this->log->write('[edit() ENDLISTING] Ending item_id=' . $marketplace_item_id . ' à cause de l\'erreur 21917164...');
                        $endResult = $this->endListing($marketplace_item_id, $marketplace_account_id, $site_setting);
                        $this->log->write('[edit() ENDLISTING RESULT] ' . json_encode($endResult));
                        $this->log->write('[edit() ADD] Republishing product_id=' . $product['product_id'] . '...');
                        $addResult = $this->addListing($product, $updquantity, $site_setting, $marketplace_account_id);
                        $this->log->write('[edit() ADD RESULT] ' . json_encode($addResult));

                    }

                    // Warning 21920277 : eBay a renommé des item specifics automatiquement
                    if (
                        (isset($err['ErrorCode']) && $err['ErrorCode'] == '21920277') ||
                        (isset($err[0]['ErrorCode']) && $err[0]['ErrorCode'] == '21920277')
                    ) {
                        $this->log->write('[edit() WARNING 21920277] product_id=' . $product['product_id'] . ' item_id=' . $marketplace_item_id . ' — eBay a renommé des item specifics. Appel GetItem pour voir les noms actuels...');
                        $renamed_details = $this->getItemDetails($marketplace_item_id, $marketplace_account_id);
                        if ($renamed_details && isset($renamed_details['item_specifics'])) {
                            $this->log->write('[edit() WARNING 21920277 SPECIFICS ACTUELS] ' . json_encode($renamed_details['item_specifics']));
                        } else {
                            $this->log->write('[edit() WARNING 21920277 SPECIFICS] Impossible de récupérer les specifics via GetItem.');
                        }
                    }
                }
            }

            $responseArray['marketplace_id'] = $marketplace_id;
            $responseArray['marketplace_account_id'] = $marketplace_account_id;
            $responseArray['product_id'] = $product['product_id'];
            $this->editPrice($marketplace_item_id, $product['price'],$product['made_in_country_id'] ,$site_setting,$marketplace_account_id);
            $this->load->model('shopmanager/marketplace');

            if (isset($responseArray['ErrorLanguage'])) {
                $error = json_encode($responseArray);
            } elseif (isset($responseArray['Ack']) && $responseArray['Ack'] != 'Failure') {
                $error = '';
            } else {
                $error = json_encode($responseArray);
            }

            $_pm_row = $this->model_shopmanager_marketplace->getProductMarketplaceRow($responseArray['ItemID'] ?? $marketplace_item_id);
            if ($_pm_row) {
                $_pm_row['error'] = $error;
                $_pm_row['to_update'] = empty($error) ? 0 : 9;
                if (empty($error) && !empty($product['category_id'])) {
                    $_pm_row['category_id'] = (int)$product['category_id'];
                }
                $this->model_shopmanager_marketplace->editProductMarketplace($_pm_row);
            }
        }
        return $responseArray;
    }




    
    public function editCategory($marketplace_item_id, $category_id = 0, $marketplace_account_id = 1, $site_setting = []) {

        $postFields = $this->buildReviseCategoryItemRequest($marketplace_item_id, $category_id, $site_setting);
      
        $headers = $this->buildEbayHeaders("ReviseItem", 1371, $marketplace_account_id);

        $response = $this->makeCurlRequest('https://api.ebay.com/ws/api.dll', $headers, $postFields);
        $responseXml = simplexml_load_string($response);
        $responseArray = json_decode(json_encode($responseXml), true);
        $this->load->model('shopmanager/marketplace');
            
        if (isset($responseArray['ErrorLanguage'])) {
            $error = json_encode($responseArray);
        } elseif (isset($responseArray['Ack']) && $responseArray['Ack'] != 'Failure') {
            $error = '';
        } else {
            $error = json_encode($responseArray);
        }

        $_pm_row = $this->model_shopmanager_marketplace->getProductMarketplaceRow($responseArray['ItemID'] ?? null);
        if ($_pm_row) {
            $_pm_row['error'] = $error;
            $_pm_row['to_update'] = empty($error) ? 0 : 9;
            $this->model_shopmanager_marketplace->editProductMarketplace($_pm_row);
        }

        return $responseArray;
    }


    public function editPrice($marketplace_item_id, $price = 9999,$made_in_country_id = null,$site_setting=[],$marketplace_account_id = 1) {

        $this->load->model('localisation/currency');
        $currency_info = $this->model_localisation_currency->getCurrencyByCode($site_setting['Currency']['Currency']);
    //"<pre>".print_r ($currency_info,true )."</pre>");
        
		//"<pre>".print_r ($productDescriptionEbay,true )."</pre>");
      // 		//"<pre>".print_r (288,true )."</pre>");
	//"<pre>".print_r ($productDescriptionEbay,true )."</pre>");
     //if($made_in_country_id!=44 && $made_in_country_id!==null){
            $value=$currency_info['value'];
    /*    }else{
            $value=$currency_info['value'];
            $site_setting=2;
        }*/
        
	//	$quantity = is_numeric($updquantity) ? " <Quantity>$updquantity</Quantity>" : '';
        $StartPrice = ' <StartPrice>' . $price*$value . '</StartPrice>';
       
        $postFields = $this->buildRevisePriceItemRequest($marketplace_item_id, $StartPrice,$made_in_country_id,$site_setting);
      
        $headers = $this->buildEbayHeaders( "ReviseItem",1371,$marketplace_account_id);

		$response	=	$this->makeCurlRequest('https://api.ebay.com/ws/api.dll', $headers, $postFields) ;
		$responseXml = simplexml_load_string($response);
		$responseArray = json_decode(json_encode($responseXml), true);
        $this->load->model('shopmanager/marketplace');
            
        if (isset($responseArray['ErrorLanguage'])) {
            $error = json_encode($responseArray);
        } elseif (isset($responseArray['Ack']) && $responseArray['Ack'] != 'Failure') {
            $error = '';
        } else {
            $error = json_encode($responseArray);
        }

        $_pm_row = $this->model_shopmanager_marketplace->getProductMarketplaceRow($responseArray['ItemID'] ?? null);
        if ($_pm_row) {
            $_pm_row['error'] = $error;
            $_pm_row['to_update'] = empty($error) ? 0 : 9;
            $this->model_shopmanager_marketplace->editProductMarketplace($_pm_row);
        }

		return $responseArray;
    }


    public function editQuantity($marketplace_item_id, $quantity = 0,$made_in_country_id=null,$product_id= NULL,$marketplace_account_id = 1,$site_setting=[]) {

        $postFields = $this->buildReviseQuantityItemRequest($marketplace_item_id, $quantity,$made_in_country_id,$site_setting);
      
        $headers = $this->buildEbayHeaders( "ReviseItem",1371,$marketplace_account_id);
     

		$response	=	$this->makeCurlRequest('https://api.ebay.com/ws/api.dll', $headers, $postFields) ;
		$responseXml = simplexml_load_string($response);
		$responseArray = json_decode(json_encode($responseXml), true);
		return $responseArray;
    }









  

/**
 * End (terminate) a card listing on eBay and completely remove it from inventory
 * This withdraws an active listing from sale and cleans up all inventory data
 * 
 * @param string $ebay_item_id The eBay listing ID
 * @param int $marketplace_account_id Marketplace account ID for API credentials
 * @param array $site_setting Site configuration
 * @param int|null $listing_id Internal listing ID for inventory cleanup
 * @param int|null $language_id Language ID for inventory cleanup
 * @return array Result with success status
 */
public function endCardListing($ebay_item_id, $marketplace_account_id, $site_setting, $listing_id = null, $language_id = null): array {
    $result = ["success" => false];

    if (empty($ebay_item_id)) {
        $result["error"] = "eBay Item ID is empty";
        return $result;
    }

    // Use the proven Trading API approach instead of the problematic Inventory API
    $postFields = $this->buildEndItemRequest($ebay_item_id, $site_setting);
    $headers = $this->buildEbayHeaders('EndItem', 1077, $marketplace_account_id);

    // Log the request for debugging
    //$this->log->write('eBay endCardListing request for item ' . $ebay_item_id . ': ' . $postFields);

    $response = $this->makeCurlRequest('https://api.ebay.com/ws/api.dll', $headers, $postFields);

    if (!$response) {
        $result["error"] = "No response from eBay API";
        //$this->log->write('eBay endCardListing: No response for item ' . $ebay_item_id);
        return $result;
    }

    // Log the response for debugging
    //$this->log->write('eBay endCardListing response for item ' . $ebay_item_id . ': ' . $response);

    $responseXml = simplexml_load_string($response);
    if (!$responseXml) {
        $result["error"] = "Failed to parse eBay response XML";
        //$this->log->write('eBay endCardListing: Failed to parse XML response for item ' . $ebay_item_id);
        $this->log->write('eBay endCardListing: Failed to parse XML response for item ' . $ebay_item_id . ': ' . json_decode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)    );
        return $result;
    }

    $responseArray = json_decode(json_encode($responseXml), true);
//$this->log->write('eBay endCardListing response array for item ' . $ebay_item_id . ': ' . json_encode($responseArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    // Check for API errors
    if (isset($responseArray['Errors'])) {
        $errors = $responseArray['Errors'];
        // Normaliser en tableau si objet unique
        if (isset($errors['ErrorCode'])) {
            $errors = [$errors];
        }

        // Erreur 1047 : listing déjà terminé sur eBay → traiter comme succès + nettoyer DB
        foreach ($errors as $err) {
            if (($err['ErrorCode'] ?? '') === '1047') {
                //$this->log->write('eBay endCardListing: item ' . $ebay_item_id . ' already ended (1047) — treating as success');
                $responseArray['Ack'] = 'Success';
                $responseArray['_already_ended'] = true;
                break;
            }
        }

        if ($responseArray['Ack'] !== 'Success') {
            $errorMessage = "eBay API Error: ";
            foreach ($errors as $error) {
                $errorMessage .= $error['LongMessage'] ?? $error['ShortMessage'] ?? 'Unknown error';
            }
            $result["error"] = $errorMessage;
            return $result;
        }
    }

    // Check for success
    if (isset($responseArray['Ack']) && $responseArray['Ack'] === 'Success') {
        $result["success"] = true;
        $result["item_id"] = $ebay_item_id;
        $result["message"] = !empty($responseArray['_already_ended'])
            ? "Listing already ended on eBay — ebay_item_id cleared"
            : "Listing ended successfully";
        //$this->log->write('eBay endCardListing success for item ' . $ebay_item_id);
        
        // Réinitialiser les données eBay en base de données
        if ($listing_id) {
            try {
                $this->load->model('shopmanager/card/card_listing');
                $this->load->model('shopmanager/card/card');

                $listing_data = $this->model_shopmanager_card_card_listing->getListing($listing_id);

                // Réinitialiser chaque carte : offer_id = NULL, published = 0
                foreach (($listing_data['variations'] ?? []) as $variation) {
                    $this->model_shopmanager_card_card->updateCardOffer((int)$variation['card_id']);
                }

                // Effacer ebay_item_id, publis et date_published pour toutes les descriptions du listing
                $this->db->query(
                    "UPDATE `" . DB_PREFIX . "card_listing_description`
                        SET `ebay_item_id`   = NULL,
                            `status`         = 0,
                            `date_published` = NULL
                      WHERE `listing_id` = " . (int)$listing_id
                );

            } catch (\Exception $e) {
                $this->log->write('[endCardListing] cleanup exception: ' . $e->getMessage());
                $result['cleanup_error'] = $e->getMessage();
            }
        }
        
        return $result;
    }

    // Fallback error
    $result["error"] = "Failed to end listing: " . json_encode($responseArray);
    //$this->log->write('eBay endCardListing fallback error for item ' . $ebay_item_id . ': ' . json_encode($responseArray));
    return $result;
}






// Enrichir les itemSummaries avec les shipping costs CALCULATED AVANT buildFinalResult
private function enrichCalculatedShippingForItems($items, $marketplace_account_id) {
    $connectionapi = $this->getApiCredentials($marketplace_account_id);
    $bearerToken = $connectionapi['bearer_token'];
    
    if (!is_array($items)) {
        return $items;
    }
    
    foreach ($items as $key => $item) {
        // Vérifier si l'item a un shipping CALCULATED sans valeur
        if (isset($item['shippingOptions'][0]['shippingCostType']) && 
            $item['shippingOptions'][0]['shippingCostType'] === 'CALCULATED' &&
            !isset($item['shippingOptions'][0]['shippingCost']['value'])) {
            
            // Construire le bon format d'itemId: v1|legacyItemId|0
            $legacyId = $item['legacyItemId'];
            $itemId = "v1|{$legacyId}|0";
            $encodedItemId = str_replace('|', '%7C', $itemId);
            $url = "https://api.ebay.com/buy/browse/v1/item/" . $encodedItemId;
            
            $headers = [
                "Authorization: Bearer " . $bearerToken,
                "Content-Type: application/json",
                "Accept: application/json",
                "X-EBAY-C-ENDUSERCTX: contextualLocation=country%3DUS%2Czip%3D12919"
            ];
            
            $response = $this->makeCurlRequest($url, $headers);
            if ($response) {
                $itemDetails = json_decode($response, true);
                // Si on obtient le shipping cost, mettre à jour l'item ET changer le type en FIXED
                if (isset($itemDetails['shippingOptions'][0]['shippingCost']['value'])) { 
                    // Convertir CALCULATED en FIXED maintenant qu'on a le coût réel
                    $items[$key]['shippingOptions'][0]['shippingCostType'] = 'FIXED';
                    $items[$key]['shippingOptions'][0]['shippingCost'] = $itemDetails['shippingOptions'][0]['shippingCost'];
                }
            }
        }
    }
    
    return $items;
}


/**
 * Create or get inventory location
 */
private function ensureInventoryLocation($template_data, $headers): string {
    $merchantLocationKey = 'default_location';
    
    // Vérifier si existe
    $url = "https://api.ebay.com/sell/inventory/v1/location/" . urlencode($merchantLocationKey);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    
    //echo "<div style='background: #ffffcc; padding: 10px; margin: 10px; border: 2px solid orange;'>";
   // echo "<strong>DEBUG ensureInventoryLocation:</strong><br>";
  //  echo "Checking merchantLocationKey: {$merchantLocationKey}<br>";
  //  echo "GET HTTP Code: {$httpCode}<br>";
    
    if ($httpCode == 200) {
    //    echo "✅ Location exists<br>";
    //    echo "<pre>" . htmlspecialchars($response) . "</pre>";
     //   echo "</div>";
        return $merchantLocationKey;
    }
    
  //  echo "❌ Location doesn't exist, creating...<br>";
    
    // Créer la location
    $locationData = [
        "location" => [
            "address" => [
                "addressLine1" => "655 Jean-Paul Vincent",
                "city" => $template_data['location']['city'],
                "stateOrProvince" => $template_data['location']['stateOrProvince'],
                "postalCode" => $template_data['location']['postalCode'],
                "country" => $template_data['location']['country']
            ]
        ],
        "locationInstructions" => "Items ship from our main warehouse",
        "name" => "Main Warehouse",
        "merchantLocationStatus" => "ENABLED",
        "locationTypes" => ["WAREHOUSE"]
    ];
    
    // echo "Location data to create:<br>";
    // echo "<pre>" . htmlspecialchars(json_encode($locationData, JSON_PRETTY_PRINT)) . "</pre>";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($locationData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    
    // echo "POST HTTP Code: {$httpCode}<br>";
    // echo "Response:<br>";
    // echo "<pre>" . htmlspecialchars($response) . "</pre>";
    // echo "</div>";
    
    if ($httpCode >= 200 && $httpCode < 300) {
        return $merchantLocationKey;
    } else {
        //error_log("Failed to create inventory location: " . $response);
        return $merchantLocationKey;
    }
}

    
       
    // Fonction pour récupérer le Product ID (epid) d'un produit à partir d'un GTIN
public function findProductIDByGTIN($gtin = null) {
  

    if ($gtin === null) {
        return 'GTIN must be provided';
    }

    // Construire les en-têtes pour l'appel à l'API Finding d'eBay
    $headers = array(
        "X-EBAY-SOA-SECURITY-APPNAME: CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28",
        "X-EBAY-SOA-OPERATION-NAME: findItemsByProduct",
        "X-EBAY-SOA-SERVICE-VERSION: 1.13.0",
        "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML",
        "X-EBAY-SOA-RESPONSE-DATA-FORMAT: XML"
    );

    // Construire la requête XML pour `findItemsByProduct`
    $postFields = '<?xml version="1.0" encoding="utf-8"?>';
    $postFields .= '<FindProductsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
    $postFields .= '<ProductId type="UPC">' . $gtin . '</ProductId>'; // Utiliser le GTIN comme identifiant du produit
    $postFields .= '<paginationInput><entriesPerPage>1</entriesPerPage></paginationInput>';
    $postFields .= '</FindProductsRequest>';

    // Effectuer la requête cURL vers l'API eBay
    $response = $this->makeCurlRequest('https://svcs.ebay.com/services/search/FindingService/v1', $headers, $postFields);

    // Convertir la réponse XML en tableau
    $responseXml = simplexml_load_string($response);
    if ($responseXml === false) {
        return 'Error parsing the response';
    }
    $responseArray = json_decode(json_encode($responseXml), true);
    //$responseArray = json_decode($response, true);
    // Afficher le contenu de la réponse pour débogage
    //"<pre>" . print_r($responseArray, true) . "</pre>";

    // Vérifier si l'epid est présent dans la réponse
    if (isset($responseArray['searchResult']['item'][0]['productId'])) {
        return $responseArray['searchResult']['item'][0]['productId']; // Retourner le epid
    }

    return 'No epid found for the given GTIN';
}


    public function formatActualDetails($data){
        $data_return=[];
  //print("<pre>".print_r ('1627:ebay.php',true )."</pre>");
  //print("<pre>".print_r ($data,true )."</pre>");
    //"<pre>".print_r ($category_specific_info,true )."</pre>");
        if(isset($data)){
        //	$english_specifics=json_decode($rows[1]['specifics'],true);
            foreach($data as $key=>$value){		
           //"<pre>".print_r ('1948:ebay.php',true )."</pre>");
          //"<pre>".print_r ($value,true )."</pre>");
                    $data_return[$key] = $value['Actual_value']??'';
		
            }
        //"<pre>".print_r ($data_return,true )."</pre>");
            return $data_return;

        }else{
            return array();
        }
    }


/**
 * Formate les aspects pour eBay (capitalisation correcte)
 */
private function formatAspects($aspects) {
    // Normalize every aspect value to array of strings (eBay Inventory API requirement)
    foreach ($aspects as $name => $value) {
        if (is_string($value)) {
            $aspects[$name] = [trim($value)];
        } elseif (is_array($value)) {
            $aspects[$name] = array_values(array_filter(array_map('trim', $value), 'strlen'));
            if (empty($aspects[$name])) {
                unset($aspects[$name]);
                continue;
            }
        } elseif ($value === null || $value === '') {
            unset($aspects[$name]);
            continue;
        } else {
            $aspects[$name] = [(string)$value];
        }
    }

    // Sport: capitalize properly (HOCKEY → Hockey)
    if (isset($aspects['Sport'])) {
        $aspects['Sport'] = array_map(fn($v) => ucfirst(strtolower($v)), $aspects['Sport']);
    }

    return $aspects;
}

    public function formatEpidDetails($epid_details,$category_specific_info){
        $data_return=[];
  
    //"<pre>".print_r ($category_specific_info,true )."</pre>");
        if(isset($epid_details) && isset($category_specific_info)){
        //	$english_specifics=json_decode($rows[1]['specifics'],true);
        $category_specific= $category_specific_info[1]['specifics'];
        

            foreach($epid_details as $epid_detail){		
                if(isset($category_specific[$epid_detail['localizedName']])){
                    $data_return[$epid_detail['localizedName']] = $epid_detail['localizedValues'][0];
                 //$data_return[$epid_detail['localizedName']] = $this->model_shopmanager_tools->splitNames($epid_detail['localizedValues'][0]);
                }				
            }
        //"<pre>".print_r ('1627:ebay.php',true )."</pre>");
       //"<pre>".print_r ($data_return,true )."</pre>");
            return $data_return;

        }else{
            return array();
        }
    }

   
    

    public function formatEpidDetailsToSpecifics($epid_details, $upc = null) {
        $data_return=[];
  
    //"<pre>".print_r ($category_specific_info,true )."</pre>");
        if(isset($epid_details)){
        //	$english_specifics=json_decode($rows[1]['specifics'],true);
           // $epid_details = $this->filterLocalizedValues($epid_details, $upc);
            foreach($epid_details as $epid_detail){		
                    $data_return[$epid_detail['localizedName']] = array(
                        'Name' => $epid_detail['localizedName'],
                        'Value' => $epid_detail['localizedValues'],
                        'VerifiedSource' => 'yes'
                    );
                    
                 //$data_return[$epid_detail['localizedName']] = $this->model_shopmanager_tools->splitNames($epid_detail['localizedValues'][0]);
               				
            }
        //"<pre>".print_r ('1627:ebay.php',true )."</pre>");
       //"<pre>".print_r ($data_return,true )."</pre>");
            return $data_return;

        }else{
            return array();
        }
    }



public function get($gtin = null, $keyword = null, $sold = null, $order = null, $limit = 50, $marketplace_item_id = null, $permutation = null, $marketplace_account_id = 1, $product_name = null, $getCategoryLeafID = false) {
    $this->load->model('shopmanager/tools');
 
    // Génération des mots-clés en fonction des permutations
    $keywords = [];
    if (isset($keyword)) {
        if ($permutation) {
            $keywords = $this->model_shopmanager_tools->generateKeywordPermutations(
                $this->model_shopmanager_tools->cleanString(htmlspecialchars_decode($keyword))
            );
        } else {
            $keywords[] = $this->model_shopmanager_tools->cleanString(htmlspecialchars_decode($keyword));
        }
    }

    // Préparer les paramètres de requête
    $queryParams = [];
    if (!empty($keywords)) {
        $queryParams['q'] = implode(' ', $keywords);
    }
    if (isset($gtin)) {
        // limit=1 → gtin= pour match exact (usage spécifique)
        // sinon → q= car gtin= retourne beaucoup moins de résultats (les listings non tagués GTIN sont exclus)
        if ($limit == 1) {
            $queryParams['gtin'] = $gtin;
        } else {
            $queryParams['q'] = $gtin;
        }
    }
    $queryParams['limit'] = 50; // Max 50 par page selon eBay API
    $queryParams['offset'] = 0;

    // Récupération du token d'authentification
    $connectionapi = $this->getApiCredentials($marketplace_account_id);
    $bearerToken = $connectionapi['bearer_token'];

    // Préparer les headers — EBAY_CA uniquement pour les recherches UPC/EAN (gtin numérique)
    $headers = [
        "Authorization: Bearer " . $bearerToken,
        "Content-Type: application/json",
        "Accept: application/json",
    ];
    if (isset($gtin) && is_numeric($gtin)) {
        $headers[] = "X-EBAY-C-MARKETPLACE-ID: EBAY_CA";
    }

    // Récupérer TOUS les items par pagination
    $allItemSummaries = [];
    $totalResults = 0;
    
    do {
        $url = 'https://api.ebay.com/buy/browse/v1/item_summary/search?' . http_build_query($queryParams);
        
        // Exécution de la requête API
        $response = $this->makeCurlRequest($url, $headers);
        if (!$response) {
            break;
        }

        // Convertir la réponse JSON en tableau
        $responseArray = json_decode($response, true);
        
        if (!isset($responseArray['itemSummaries'])) {
            break;
        }
        
        // Fusionner les items de cette page
        $allItemSummaries = array_merge($allItemSummaries, $responseArray['itemSummaries']);
        
        // Récupérer le total à la première itération
        if ($totalResults === 0 && isset($responseArray['total'])) {
            $totalResults = $responseArray['total'];
        }
        
        // Passer à la page suivante
        $queryParams['offset'] += 50;
        
        // Continuer tant qu'on n'a pas tout récupéré
    } while (count($allItemSummaries) < $totalResults && isset($responseArray['next']));
    
    // Mettre tous les items dans responseArray
    $responseArray['itemSummaries'] = $allItemSummaries;
    $responseArray['total'] = $totalResults;

    // Vérifier si des résultats sont disponibles
    if (isset($responseArray['itemSummaries']) && count($responseArray['itemSummaries']) > 0) {
        if($getCategoryLeafID === false){
             // Enrichir les items avec CALCULATED shipping AVANT buildFinalResult
             $enrichedItems = $this->enrichCalculatedShippingForItems($responseArray['itemSummaries'], $marketplace_account_id);
             $buildFinalResult= $this->buildFinalResult($enrichedItems, $marketplace_item_id, $marketplace_account_id, $gtin, $product_name);
            return $buildFinalResult;
        }else{
            return $this->getCategoryLeafID($responseArray['itemSummaries']);
        }
    } elseif(!isset($gtin)) {
        // Retenter avec permutations si aucun résultat n'a été trouvé
        if (!$permutation && isset($keyword)) {
            return $this->get($gtin, $keyword, $sold, $order, $limit, $marketplace_item_id, $permutation = 'yes', $marketplace_account_id, $product_name);
        }
    }

    return null;
}


public function getApiCredentials($marketplace_account_id = 1) {
    // Récupérer les informations d'authentification de l'API eBay
   // $marketplace_account_id = 1;
//"<pre>".print_r($marketplace_account_id, true)."</pre>");
   $this->load->model('shopmanager/marketplace');
   $connectionapi= $this->model_shopmanager_marketplace->getMarketplaceAccount(['customer_id' => 10,'filter_marketplace_account_id' => $marketplace_account_id ]);
  
   //$connectionapi=isset($connectionapi[$marketplace_account_id])?$connectionapi[$marketplace_account_id]:$connectionapi;
  
  //"<pre>".print_r('863:ebay.php', true)."</pre>");
  //"<pre>".print_r($connectionapi, true)."</pre>");
 //$this->refreshAccessToken($connectionapi['refresh_token']);
    // Vérifier si le cookie 'bearer_token' est présent
    if (!isset($this->request->cookie['bearer_token'])) {
        // Rafraîchir le token d'accès si le cookie n'existe pas
        $newAccessTokenData = $this->refreshAccessToken($connectionapi['refresh_token']);
        
        // Si nécessaire, mettre à jour l'access token dans le cookie
        if (isset($newAccessTokenData['bearer_token'])) {
      
            $connectionapi['bearer_token'] = $newAccessTokenData['bearer_token'];
        } else {
            $this->log->write('eBay OAuth ERREUR: refreshAccessToken a échoué — le refresh_token est probablement expiré ou invalide. Ré-autorisez l\'app eBay pour obtenir un nouveau refresh_token.');
            $connectionapi['bearer_token'] = '';
        }
    //"<pre>".print_r('863:ebay.php', true)."</pre>");
//"<pre>".print_r($this->request->cookie, true)."</pre>");
    } else {
        $connectionapi['bearer_token'] = $this->request->cookie['bearer_token'];
    }

    // Afficher des informations de débogage APRÈS avoir défini le cookie
   

    return $connectionapi;
}




public function getCategoryLeafID($items) {
    $totalItems = count($items);
    $categoryCounts = [];
    $categoryPercentages = [];

    foreach ($items as $key=>$item) {
        //"<pre>".print_r($item, true)."</pre>");
        
        $this->updateCategoryCount($categoryCounts, $item);
    }
    $categoryPercentages = [];
    foreach ($categoryCounts as $category_id => $data) {
        $count = $data['count'];
        $categoryName = $data['name'];
        $percentage = ($count / $totalItems) * 100;
        $categoryPercentages[] = array(
            'category_id'   => $category_id,
            'category_name' => $categoryName,
            'percent'      => number_format($percentage, 0)
        );
    }

    return $categoryPercentages[0]['category_id'];


}


public function getCategorySpecifics($category_id,$site_id = 0,$marketplace_account_id = 1) {
    //"<pre>".print_r (value: 377 )."</pre>");  
	
	$connectionapi = $this->getApiCredentials($marketplace_account_id);
   

	$apiEndpoint = "https://api.ebay.com/commerce/taxonomy/v1/category_tree/".$site_id."/get_item_aspects_for_category?category_id=$category_id";

	$headers = [
		"Authorization: Bearer ".$connectionapi['bearer_token'],
		'Content-Type: application/json',
	];

	$response = $this->makeCurlRequest($apiEndpoint, $headers);

	$responseData = json_decode($response, true);
   //"<pre>".print_r ($responseData,true )."</pre>");
	/*if(!isset($responseData['aspects']) && $connectionapi['site_id']==100){
		return null;
	}elseif(!isset($responseData['aspects'])){
		$this->getCategorySpecifics($category_id,100);
	}*/
	if(isset($responseData['aspects'])){
        $categoryspecifics=array();
		$reponseFormat =  array_column($responseData['aspects'], null, 'localizedAspectName');
		$category_specific_infoEN = json_encode($reponseFormat);
		

		if (json_last_error() !== JSON_ERROR_NONE) {
			return (['error' => 'Error parsing JSON response', 'response' => $responseData]);
		
		}
        $this->load->model('localisation/language');
        $this->load->model('shopmanager/catalog/category');
        $this->load->model('shopmanager/translate');
        //$this->load->model('shopmanager/translate");
        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language) {
            //print("<pre>".print_r ($language,true )."</pre>");
            $lang_code = substr($language['code'], 0, 2); // Extraire 'en' de 'en-gb', 'fr' de 'fr-fr', etc.
            
            if ($lang_code == 'en') {
                //print("<pre>".print_r ($category_specific_infoEN,true )."</pre>");
                $this->model_shopmanager_catalog_category->editSpecifics($category_id, $language['language_id'], $category_specific_infoEN);
                $categoryspecifics[$language['language_id']] = json_decode($category_specific_infoEN, true);
                $language_default=$language['language_id'];
            } else {
                $category_specific_info = json_decode($category_specific_infoEN, true);
        
                foreach ($category_specific_info as $key => $data) {
                    // Vérifier si une valeur pour la langue existe déjà dans la base de données
                    $existingTranslation = $this->model_shopmanager_catalog_category->getSpecificNameByLanguage($data['localizedAspectName'], $lang_code);
                    
                    if ($existingTranslation) {
                        if($existingTranslation!='exclude'){
                        // Utiliser la valeur existante au lieu d'appeler la fonction translate
                            $category_specific_info[$key]['localizedAspectName'] = $existingTranslation;
                        }else{
                           unset($categoryspecifics[$language_default][$key] );
                           $category_specific_infoEN=json_encode($categoryspecifics[$language_default]);
                           $this->model_shopmanager_catalog_category->editSpecifics($category_id, $language_default, $category_specific_infoEN);
                        
                        }
                    } else {
                        // Sinon, appeler la fonction translate et sauvegarder la nouvelle valeur
                        $translatedValue = isset($data['localizedAspectName'])?$this->model_shopmanager_translate->translate($data['localizedAspectName'], $lang_code):'';
                        $category_specific_info[$key]['localizedAspectName'] = $translatedValue;
                        
                        // Ajouter la nouvelle traduction dans la base de données
                        $this->model_shopmanager_catalog_category->addSpecificTranslation($data['localizedAspectName'], $lang_code, $translatedValue);
                    }
                }

                // Traduire les aspectValues pour les aspects SELECTION_ONLY
                $this->load->model('shopmanager/ai');
                foreach ($category_specific_info as $key => $data) {
                    if (isset($data['aspectConstraint']['aspectMode']) && 
                        $data['aspectConstraint']['aspectMode'] === 'SELECTION_ONLY' &&
                        !empty($data['aspectValues']) && is_array($data['aspectValues'])) {
                        
                        $enValues = array_column($data['aspectValues'], 'localizedValue');
                        if (empty($enValues)) continue;

                        $translatedValues = $this->model_shopmanager_ai->translate(
                            json_encode($enValues, JSON_UNESCAPED_UNICODE), 
                            $lang_code
                        );
                        if (is_string($translatedValues)) {
                            $translatedValues = json_decode($translatedValues, true);
                        }
                        
                        if (is_array($translatedValues) && count($translatedValues) === count($enValues)) {
                            foreach ($data['aspectValues'] as $i => $val) {
                                $category_specific_info[$key]['aspectValues'][$i]['localizedValue'] = $translatedValues[$i];
                            }
                        }
                    }
                }
        
                // Mettre à jour les spécificités de la catégorie avec les nouvelles traductions
                $this->model_shopmanager_catalog_category->editSpecifics($category_id, $language['language_id'], json_encode($category_specific_info));
                $categoryspecifics[$language['language_id']] = $category_specific_info;
        
                unset($category_specific_info);
            }
        }
        
	
		return $categoryspecifics;
	}else{
        return $responseData;
    }
}

private function getConditionNameById($category_id, $condition_id, $marketplace_account_id = 1, $site_setting=[]) {

  //"<pre>".print_r('502:ebay.php', true)."</pre>");
  //"<pre>".print_r($category_id, true)."</pre>");
  //"<pre>".print_r($condition_id, true)."</pre>");
    // Vérifier si les conditions pour cette catégorie sont déjà dans le cache
    if (!isset($this->conditionsCache[$category_id])) {
        // Si non, récupérer les conditions via l'API et les stocker dans le cache
       //"<pre>".print_r (713,true )."</pre>");
      //"<pre>".print_r ($site_setting,true )."</pre>");
        $this->conditionsCache[$category_id] = $this->getConditionsByCategory($category_id, $marketplace_account_id,$site_setting);
    }
 //"<pre>".print_r('505:ebay.php', true)."</pre>");
 //"<pre>".print_r($this->conditionsCache[$category_id], true)."</pre>");
    // Chercher la condition par son ID dans le cache
    if (isset($this->conditionsCache[$category_id][$condition_id])) {
        return $this->conditionsCache[$category_id][$condition_id];
    } else {
        return NULL; // Valeur par défaut si la condition n'est pas trouvée
    }
}

      
    public function getConditionsByCategory($category_id, $marketplace_account_id = 1, $site_id = null) { 
        // Récupérer les informations d'authentification API
     //$category_id=29223;
 //"<pre>".print_r ('124:'.$category_id,true )."</pre>");
 //"<pre>".print_r ($site_setting,true )."</pre>");
        // Construire la requête XML pour obtenir les détails de la catégorie
        $requestBody = '<?xml version="1.0" encoding="utf-8"?>
            <GetCategoryFeaturesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
           
                <CategoryID>' . $category_id . '</CategoryID>
                <DetailLevel>ReturnAll</DetailLevel>
                <ViewAllNodes>true</ViewAllNodes>
               
            </GetCategoryFeaturesRequest>';
    // <FeatureID>ConditionValues</FeatureID>
        // Headers pour l'appel de l'API
        //"<pre>".print_r (value: '137:ebay.php' )."</pre>");
        $headers = $this->buildEbayHeaders('GetCategoryFeatures',1197,$marketplace_account_id,site_id: $site_id);
    
        // Faire l'appel API eBay
        $response = $this->makeCurlRequest('https://api.ebay.com/ws/api.dll', $headers, $requestBody);
        $responseXml = simplexml_load_string($response);
       $responseArray = json_decode(json_encode($responseXml), true);
 //"<pre>".print_r('33:ebay.php', true)."</pre>"); 
    //"<pre>".print_r ($category_id,true )."</pre>"); 
 //"<pre>".print_r ($responseArray,true )."</pre>"); 
        //$responseArray = json_decode($response, true);

        // Vérifier si les conditions existent dans la réponse
        if (isset($responseArray['Category']['ConditionValues']['Condition'])) {
           
            if(!isset($responseArray['Category']['ConditionValues']['Condition'][0])){
                $conditions[] = $responseArray['Category']['ConditionValues']['Condition'];
            }else{
                $conditions = $responseArray['Category']['ConditionValues']['Condition'];
            }
            // Stocker les conditions sous forme de tableau associatif
            $conditionNames = [];
            foreach ($conditions as $condition) {
                $conditionNames[$condition['ID']] = $condition['DisplayName'];
            }
          //"<pre>".print_r ($conditionNames,true )."</pre>"); 
            return $conditionNames;
        }elseif(!isset($responseArray['Category']['ConditionEnabled'])){
  //"<pre>".print_r('164:ebay.php', true)."</pre>");
  //"<pre>".print_r ($responseArray,true )."</pre>"); 
            $conditionNames = array(
                9999 => "",
            );
            return NULL;
        }else{
    /*        $headers = $this->buildEbayHeaders('GetCategoryFeatures',1197,$site_setting);
    
        // Faire l'appel API eBay
        $response = $this->makeCurlRequest('https://api.ebay.com/ws/api.dll', $headers, $requestBody);
        $responseXml = simplexml_load_string($response);
       $responseArray = json_decode(json_encode($responseXml), true);*/
        }
    
        // Retourner un tableau vide si aucune condition n'est trouvée
        return NULL;
    }

    private function getCurrency($made_in_country_id = null,$price=9999,$site_setting = []) {
        $this->load->model('localisation/currency');
        $currency_info = $this->model_localisation_currency->getCurrencyByCode($site_setting['Currency']['Currency']);

  //if($made_in_country_id != 44 && $made_in_country_id !== null ){
            $Currency = '  <StartPrice currencyID="'.$site_setting['Currency']['currencyID'].'">' . $price*$currency_info['value']. '</StartPrice>
                            <Country>'.$site_setting['Currency']['Country'].'</Country>
                          <Currency>'.$site_setting['Currency']['Currency'].'</Currency>';
    /*    }else{

       
           $Currency = '  <StartPrice currencyID="CAD">' . $price*$currency_info['value']. '</StartPrice>
                        <Country>CA</Country>
                         <Currency>CAD</Currency>';
        }*/
		return $Currency;
    }

public function getDetailProduct($marketplace_item_id, $upc ) {
    // Récupérer les détails du produit depuis l'API
    $item = $this->getProduct($marketplace_item_id);

    // Vérifier si l'élément existe
    if (!isset($item)) {
        return ["error" => "No item details found"];
    }
    $responseJson= json_encode($item);
    $normalizedUpc = is_string($upc) ? ltrim($upc, '0') : null;

    // Vérifier si l'UPC existe dans la réponse JSON
    if (!is_null($normalizedUpc) && strpos($responseJson, $normalizedUpc) === false) {
        return; // Ignorer ce produit si l'UPC n'est pas trouvé
    }

    $outputArray = [];

    // Récupérer les spécificités (localizedAspects)
    if (isset($item['localizedAspects'])) {
        $outputArray['specifics'] = [];

        foreach ($item['localizedAspects'] as $aspect) {
            $aspectName = $aspect['name'];
            $aspectValue = isset($aspect['value']) ? $aspect['value'] : '';

            // Assurer que 'Value' est toujours un tableau
            if (!is_array($aspectValue)) {
                $aspectValue = [$aspectValue];
            }

            foreach ($aspectValue as $value) {
                // Vérifier si la valeur n'est pas déjà présente pour éviter les doublons
                if (!isset($outputArray['specifics'][$aspectName]['Value']) || 
                    !in_array($value, $outputArray['specifics'][$aspectName]['Value'])) {
                    $outputArray['specifics'][$aspectName]['Value'][] = $value;
                    $outputArray['specifics'][$aspectName]['Actual_value'][] = $value;
                }
            }

            // Assurer que la clé 'Name' est bien enregistrée
            $outputArray['specifics'][$aspectName]['Name'] = $aspectName;
        }
    }

    // Récupérer les images (suppression des paramètres après `?`)
    $outputArray['PictureDetails'] = [];
    if (isset($item['image']['imageUrl'])) {
        $outputArray['PictureDetails'][] = preg_replace('/\?.*/', '', $item['image']['imageUrl']);
    }
    if (isset($item['additionalImages'])) {
        foreach ($item['additionalImages'] as $img) {
            $outputArray['PictureDetails'][] = preg_replace('/\?.*/', '', $img['imageUrl']);
        }
    }

    // Récupérer le coût d'expédition
    $outputArray['shippingCost'] = null;
    if (isset($item['shippingOptions'][0]['shippingCost']['value'])) {
        $outputArray['shippingCost'] = $item['shippingOptions'][0]['shippingCost']['value']; 
    } elseif (isset($item['shippingOptions']['shippingCost']['value'])) {
        $outputArray['shippingCost'] = $item['shippingOptions']['shippingCost']['value']; 
    }

    return $outputArray;
}



    public function getDetailProductSellers(&$items, $formatEpidDetailsToSpecifics = null, $upc = null) {
        //"<pre>".print_r(2038, true)."</pre>");
        //"<pre>".print_r ($formatEpidDetailsToSpecifics,true )."</pre>");
        $this->load->model('shopmanager/ai');
        $specifics_list = [];
        $specifics_list_final = [];
        if (!empty($formatEpidDetailsToSpecifics)) {
            foreach ($formatEpidDetailsToSpecifics as $key => $value) {
                if (isset($value['Value'])) {
                    $specifics_list[$key]['VerifiedSource'] = $value['VerifiedSource']??'';
                    foreach ($value['Value'] as $actualValue) {
                        // Ajouter uniquement les valeurs uniques
                      
                        if (!isset($specifics_list[$key]['Value']) || !in_array($actualValue, $specifics_list[$key]['Value'])) {
                            $specifics_list[$key]['Value'][] = $actualValue;
                        }
                    }
                }
            }
        }
        foreach ($items as $keyitem => $item) {
            // Récupérer les spécificités pour chaque marketplace_item_id
            $detailproduct = $this->getDetailProduct($item['itemId'], $upc);
           
            // Ajouter les spécificités directement
            if (!empty($detailproduct['specifics'])) {
                foreach ($detailproduct['specifics'] as $key => $value) {
                    $specifics_list[$key]['VerifiedSource'] = $value['VerifiedSource']??'';
                    if (isset($value['Value'])) {
                        foreach ($value['Value'] as $actualValue) {
                            // Ajouter uniquement les valeurs uniques
                            if (!isset($specifics_list[$key]['Value']) || !in_array($actualValue, $specifics_list[$key]['Value'])) {
                                $specifics_list[$key]['Value'][] = $actualValue;
                            }
                        }
                    }
                }
            }
    
            // Ajouter les images si disponibles
            if (!empty($detailproduct['PictureDetails'])) {
                $items[$keyitem]['images'] = $detailproduct['PictureDetails'];
            }
    
            // Gérer les coûts d'expédition
            if (isset($detailproduct['shippingCost']) && !isset($items[$keyitem]['shippingOptions']['shippingCost']['value'])) {
                $items[$keyitem]['shippingOptions']['shippingCost']['value'] = $detailproduct['shippingCost'];
            }
            // REMOVED: Don't delete price from Browse API if supplementary API call is missing shipping
            // The Browse API already has price and shipping data, only supplement it, don't delete it
        }
        //"<pre>".print_r ($specifics_list,true )."</pre>");
        // Construire `specifics_list_final` directement sans refaire un `foreach`
        foreach ($specifics_list as $key => $values) {
            $specifics_list_final[$key] = [
                'Name' => $key,
                'VerifiedSource' => $value['VerifiedSource']??'',
                'Value' => array_values(array_unique($values['Value'])) // Suppression des doublons
            ];
        }
        //"<pre>".print_r ($specifics_list_final,true )."</pre>");
        $specifics_list_final=$this->model_shopmanager_ai->cleanSpecifics($specifics_list_final);
        //"<pre>".print_r ($specifics_list_final,true )."</pre>");

        return $specifics_list_final;
    }

    function getDetailsByepid($productIDS = [], $marketplace_account_id = 1, $upc = null) {
        //print("<pre>" . print_r('1955:EBAY.PHP', true) . "</pre>");
        //print("<pre>" . print_r($upc, true) . "</pre>");
        $connectionapi = $this->getApiCredentials($marketplace_account_id);

        $productIDScheck = $productIDS;
        $product_data = [];
        //print("<pre>" . print_r($productIDS, true) . "</pre>");
        foreach ($productIDS as $key=>$productID_info) {
            $productID=$productID_info['epid'];
            $site_id=$productID_info['site_id'];
            $headers = [
                'Authorization: Bearer ' . $connectionapi['bearer_token'],
                'Content-Type: application/json',
                'X-EBAY-C-MARKETPLACE-ID: '.$site_id
            ];
            //"<pre>" . print_r('1968:EBAY.PHP', true) . "</pre>");
            //"<pre>" . print_r($productID, true) . "</pre>");
            // URL de l'API Catalog pour récupérer les détails d'un produit spécifique via son EPID
            $url = 'https://api.ebay.com/commerce/catalog/v1_beta/product/' . $productID;
    
            // Effectuer la requête cURL vers l'API eBay
            $responseJson = $this->makeCurlRequest($url, $headers);
            $responseArray = json_decode($responseJson, true);
    
            // Vérifier si la réponse est valide et ne contient pas d'erreurs
            if (!empty($responseArray) && !isset($responseArray['errors'])) {
                //print("<pre>" . print_r('1977:EBAY.PHP', true) . "</pre>");
               //print("<pre>" . print_r($responseArray, true) . "</pre>");
                unset($responseArray['version']);
                // Normaliser l'UPC pour comparaison
                $normalizedUpc = is_string($upc) ? ltrim($upc, '0') : null;

                // Vérifier si l'UPC existe dans la réponse JSON
              /*  if (!is_null($normalizedUpc) && strpos($responseJson, $normalizedUpc) === false) {
                    continue; // Ignorer ce produit si l'UPC n'est pas trouvé
                }*/
            
                // Ajouter la réponse complète si l'UPC est trouvé ou si $upc est null
                $product_data[] = $responseArray;
                unset($productIDScheck[$key]);
            }else{
                //"<pre>" . print_r('1991:EBAY.PHP', true) . "</pre>");
                //"<pre>" . print_r($responseArray, true) . "</pre>");
                $productIDScheck[$key] = array (
                    'productID' =>$productID,
                    'site_id' =>$site_id,
                    'response' => $responseArray
                );
            }
        }
        //print("<pre>" . print_r('2002:EBAY.PHP', true) . "</pre>");
        //print("<pre>" . print_r($product_data, true) . "</pre>");
        // Si aucun produit n'est trouvé, retourner null
        if (empty($product_data)) {
            return null;
        }
    
        // Si un seul produit est trouvé, retourner directement ses détails
        if (count($product_data) === 1) {
            return $product_data[0];
        }
    //print("<pre>" . print_r('2012:EBAY.PHP', true) . "</pre>");
        // Sinon, retourner un tableau contenant les détails de tous les produits trouvés
        //print("<pre>" . print_r($product_data, true) . "</pre>");
        // Fusionner les données de plusieurs produits
        $product_data = array_reduce($product_data, function ($merged, $current) {
            return array_merge_recursive($merged, $current);
        }, []);
        //print("<pre>" . print_r('2018:EBAY.PHP', true) . "</pre>");
        //print("<pre>" . print_r($product_data, true) . "</pre>");
        return $product_data;
    }

    
   private function getEbaySiteId($countryCode) {
    switch (strtoupper($countryCode)) {
        case 'US': return 'EBAY_US'; // États-Unis
        case 'CA': return 'EBAY_CA'; // Canada
        case 'GB': return 'EBAY_GB'; // Royaume-Uni
        case 'AU': return 'EBAY_AU'; // Australie
        case 'DE': return 'EBAY_DE'; // Allemagne
        case 'FR': return 'EBAY_FR'; // France
        case 'IT': return 'EBAY_IT'; // Italie
        case 'ES': return 'EBAY_ES'; // Espagne
        case 'NL': return 'EBAY_NL'; // Pays-Bas
        case 'BE': return 'EBAY_BE'; // Belgique
        case 'AT': return 'EBAY_AT'; // Autriche
        case 'CH': return 'EBAY_CH'; // Suisse
        case 'IE': return 'EBAY_IE'; // Irlande
        case 'PL': return 'EBAY_PL'; // Pologne
        case 'SG': return 'EBAY_SG'; // Singapour
        case 'IN': return 'EBAY_IN'; // Inde
        case 'HK': return 'EBAY_HK'; // Hong Kong
        case 'MY': return 'EBAY_MY'; // Malaisie
        case 'PH': return 'EBAY_PH'; // Philippines
        case 'VN': return 'EBAY_VN'; // Vietnam
        default: return 'UNKNOWN'; // Si le pays n'est pas reconnu
    }
}

public function getImages($marketplace_item_id) {
    // Obtenir les détails du produit à partir de l'API eBay
    $productDetails = $this->getProduct($marketplace_item_id);
    //print("<pre>".print_r('1153:ebay.php', true)."</pre>");
    //print("<pre>".print_r($productDetails, true)."</pre>");
    // DEBUG: Log what getProduct returns

    
    // Si getProduct retourne un tableau indexé [0 => [...]], prendre le premier élément
    // Sinon, utiliser directement le tableau (détecté par la présence de clés comme 'itemId' ou 'Item')
    if (isset($productDetails[0]) && is_array($productDetails[0])) {
        $productDetails = $productDetails[0];
    }
    // Si $productDetails est vide ou n'est pas un tableau, retourner vide
    if (!is_array($productDetails) || empty($productDetails)) {
        return [];
    }
    
    
    // Initialiser un tableau pour stocker les URLs des images
    $imageUrls = [];
    
    // Check for Browse API format (image + additionalImages with imageUrl)
    if (isset($productDetails['image']['imageUrl']) && !empty($productDetails['image']['imageUrl'])) {
        // Add primary image
        $imageUrls[] = $productDetails['image']['imageUrl'];
        
        // Add additional images
        if (isset($productDetails['additionalImages']) && is_array($productDetails['additionalImages'])) {
            foreach ($productDetails['additionalImages'] as $additionalImage) {
                if (isset($additionalImage['imageUrl']) && !empty($additionalImage['imageUrl'])) {
                    $imageUrls[] = $additionalImage['imageUrl'];
                }
            }
        } else {
           
        }
    }
    // Check for Trading API format (PictureDetails/PictureURL)
    elseif (isset($productDetails['Item']['PictureDetails']['PictureURL'])) {
       
        $pictureURL = $productDetails['Item']['PictureDetails']['PictureURL'];
        
        // Handle both single string and array of URLs
        if (is_array($pictureURL)) {
         
            $imageUrls = array_merge($imageUrls, $pictureURL);
        } else {

            $imageUrls[] = $pictureURL;
        }
        
        // Add ExternalPictureURL if present
        if (isset($productDetails['Item']['PictureDetails']['ExternalPictureURL']) && !empty($productDetails['Item']['PictureDetails']['ExternalPictureURL'])) {
            $imageUrls[] = $productDetails['Item']['PictureDetails']['ExternalPictureURL'];
        }
    } 

    // Supprimer les doublons éventuels
    $imageUrls = array_unique($imageUrls);

    // Nettoyer les URLs pour enlever les paramètres (par exemple ?set_id=880000500F)
    foreach ($imageUrls as $key => $url) {
        $imageUrls[$key] = strtok($url, '?'); // Enlever tout ce qui vient après le '?'
    }
    
    // Log the URLs for debugging
    //print("<pre>".print_r('1161:ebay.php', true)."</pre>");
    //print("<pre>".print_r($imageUrls, true)."</pre>");
    return $imageUrls;
}




    public function getInventory($startTimeFrom = '', $startTimeTo = '', $limit = 100, $page = 1, $marketplace_account_id = 1) {
        // Construire la requête XML GetSellerList
        $post = $this->buildGetSellerListRequest($startTimeFrom, $startTimeTo, $limit, $page);
    
        // Définir l'URL de l'API eBay (à modifier si nécessaire)
        $url = "https://api.ebay.com/ws/api.dll"; // Assure-toi que c'est bien l'URL correcte pour l'API
       
        // Construire les headers pour l'appel API
        $headers = $this->buildEbayHeaders("GetSellerList", 1349, $marketplace_account_id);
    
        // Effectuer la requête CURL
        $response = $this->makeCurlRequest($url, $headers, $post);
    
        // Vérification si la réponse n'est pas vide
        if (!$response) {
            //error_log("Erreur : Réponse vide de l'API eBay.");
            return null;
        }
    
        // Nettoyage des caractères spéciaux potentiels dans la réponse XML
        $response = str_replace(
            ['&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '**', "\r\n", '\"'],
            ['', '', '', '', '"'],
            $response
        );
    
        // Charger la réponse XML en tant qu'objet SimpleXML sécurisé
        $xmlObject = simplexml_load_string($response);
    
        if ($xmlObject === false) {
            //error_log("Erreur : Impossible de parser la réponse XML eBay.");
            return null;
        }
    
        // Convertir XML en tableau associatif JSON
        $json = json_decode(json_encode($xmlObject), true);
        //"<pre>".print_r('1412', true)."</pre>");
        //"<pre>".print_r($json, true)."</pre>");
        // Debugging (désactive ces lignes après le test)
        //error_log("1474:ebay.php");
        //error_log(print_r($json, true));
    
        return $json;
    }


    /**
     * Get full item details including category, condition, and specifics
     * 
     * @param string $marketplace_item_id The eBay Item ID
     * @param int $marketplace_account_id The marketplace account ID
     * @return array|null Item details or null on error
     */
    public function getItemDetails($marketplace_item_id, $marketplace_account_id = 1) {
        $post = $this->buildGetItemRequest($marketplace_item_id);
        $headers = $this->buildEbayHeaders("GetItem", 1371, $marketplace_account_id);
        $url = "https://api.ebay.com/ws/api.dll";

        $response = $this->makeCurlRequest($url, $headers, $post);
        
        if (!$response) {
            return null;
        }

        // Parse XML response
        $xml = simplexml_load_string($response);
        if ($xml === false) {
            return null;
        }

        $json = json_decode(json_encode($xml), true);

        // Check for errors
        if (isset($json['Ack']) && ($json['Ack'] == 'Failure' || $json['Ack'] == 'PartialFailure')) {
            //error_log("[GetItem Error] " . ($json['Errors']['ShortMessage'] ?? 'Unknown error'));
            return null;
        }

        // Extract relevant data
        $item = $json['Item'] ?? null;
        if (!$item) {
            return null;
        }

        return [
            'category_id' => $item['PrimaryCategory']['CategoryID'] ?? null,
            'condition_id' => $item['ConditionID'] ?? null,
            'item_specifics' => $item['ItemSpecifics']['NameValueList'] ?? null,
            'image_count' => (function($item) {
                $urls = $item['PictureDetails']['PictureURL'] ?? null;
                if (is_array($urls)) return count($urls);
                if (is_string($urls) && !empty($urls)) return 1;
                if (!empty($item['PictureDetails']['GalleryURL'])) return 1;
                return 0;
            })($item),
        ];
    }

    /**
     * Vérifie qu'un group_key eBay REST Inventory existe et est actif.
     * Fait 1 seul appel : GET /sell/inventory/v1/inventory_item_group/{key}
     * Réutilise buildRestHeaders() (déjà privé dans ce fichier).
     *
     * @param string $group_key              Clé du groupe (ex: "CA_CARD_LIST_3_92")
     * @param string $ebay_item_id           L'ebay_item_id stocké en DB pour cross-check
     * @param int    $marketplace_account_id Compte eBay (défaut: 1)
     * @return array [
     *   'status' => int,        // 1=ok, 2=error, 3=ended/deleted
     *   'error'  => string|null // message d'erreur ou null si OK
     * ]
     */
    public function verifyInventoryGroupStatus(string $group_key, string $ebay_item_id, int $marketplace_account_id = 1): array
    {
        if (empty($group_key)) {
            return ['status' => 2, 'error' => 'No group_key provided'];
        }

        // buildRestHeaders() sans Content-Type (requête GET)
        $headers = $this->buildRestHeaders($marketplace_account_id, false);
        $url = 'https://api.ebay.com/sell/inventory/v1/inventory_item_group/' . urlencode($group_key);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        

        if ($httpCode === 404) {
            return ['status' => 3, 'error' => 'eBay group not found (ended or deleted)'];
        }

        if ($httpCode !== 200) {
            $err = json_decode($response, true);
            $msg = $err['errors'][0]['message'] ?? ('HTTP ' . $httpCode);
            return ['status' => 2, 'error' => 'eBay API error: ' . $msg];
        }

        $data = json_decode($response, true);
        $listingId = $data['listingId'] ?? null;

        // Cross-check: le listingId retourné par eBay doit correspondre à l'ebay_item_id stocké en DB
        if ($listingId && $ebay_item_id && (string)$listingId !== (string)$ebay_item_id) {
            return ['status' => 2, 'error' => 'group_key mismatch: eBay listingId=' . $listingId . ' vs DB=' . $ebay_item_id];
        }

        return ['status' => 1, 'error' => null];
    }

   
    private function getLocation($made_in_country_id = null,$site_setting = []) {

       // if($made_in_country_id != 44 && $made_in_country_id !== null ){
            $location = '<Location>'.$site_setting['Location']['Location'].'</Location>
            <PostalCode>'.$site_setting['Location']['PostalCode'].'</PostalCode>';
     /*   }else{
            $location = '<Location>Longueuil, Quebec</Location>
            <PostalCode>J4G1R3</PostalCode>';
        }*/
		return $location;
    }

    
    /**
     * Get bulk active listings using GetMyeBaySelling (more efficient for large inventories)
     * 
     * @param int $page Page number
     * @param int $marketplace_account_id Marketplace account ID
     * @return array eBay API response
     */
    public function getMyeBaySellingBulk($page = 1, $marketplace_account_id = 1) {
        // Build GetMyeBaySelling request
        $post = $this->buildGetMySellingRequest($page, 'en_US');
        
        // eBay API URL
        $url = "https://api.ebay.com/ws/api.dll";
        
        // Build headers
        $headers = $this->buildEbayHeaders("GetMyeBaySelling", 1349, $marketplace_account_id);
        
        // Make CURL request
        $response = $this->makeCurlRequest($url, $headers, $post);
        
        if (!$response) {
            //error_log("Error: Empty response from eBay GetMyeBaySelling");
            return [];
        }
        
        // Clean special characters
        $response = str_replace(
            ['&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '**', "\r\n", '\"'],
            ['', '', '', '', '"'],
            $response
        );
        
        // Parse XML
        $xmlObject = simplexml_load_string($response);
        
        if ($xmlObject === false) {
            //error_log("Error: Cannot parse eBay GetMyeBaySelling XML response");
            return [];
        }
        
        // Convert to array
        $json = json_decode(json_encode($xmlObject), true);
        
        //error_log("[eBay GetMyeBaySelling] Page $page - Response received");
        
        return $json;
    }


private function getOffersForListing($listingId, $headers): array {
    //$this->log->write('GET OFFERS FOR LISTING: Start for listing ID ' . $listingId);
    
    // Pour GET, on ne doit pas avoir Content-Type
    // Filtrer les headers pour garder seulement Authorization, Accept, Content-Language
    $getHeaders = [];
    foreach ($headers as $header) {
        if (strpos($header, 'Content-Type:') === false) {
            $getHeaders[] = $header;
        }
    }
    
    // Log headers (masquer le token pour sécurité)
    $safeHeaders = array_map(function($header) {
        if (strpos($header, 'Bearer') !== false) {
            return 'Authorization: Bearer ***MASKED***';
        }
        return $header;
    }, $getHeaders);
    //$this->log->write('GET OFFERS FOR LISTING: Headers (filtered for GET) = ' . json_encode($safeHeaders));
    
    // eBay API ne supporte pas de filtre par listing_id sur GET /offer
    // On doit récupérer toutes les offres puis filtrer en PHP
    // Note: Si l'erreur 25707 (invalid SKU) apparaît, c'est que certaines offres 
    // dans le compte eBay ont des SKU avec underscores ou caractères non-alphanumériques
    $url = "https://api.ebay.com/sell/inventory/v1/offer?limit=200";
    
    //$this->log->write('GET OFFERS FOR LISTING: URL = ' . $url);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $getHeaders);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    
    if ($curlError) {
        //$this->log->write('GET OFFERS FOR LISTING: CURL ERROR - ' . $curlError);
        return ['error' => 'cURL error: ' . $curlError];
    }
    
    //$this->log->write('GET OFFERS FOR LISTING: HTTP Code = ' . $httpCode);
    //$this->log->write('GET OFFERS FOR LISTING: Response = ' . substr($response, 0, 1000));

    if ($httpCode != 200) {
        //$this->log->write('GET OFFERS FOR LISTING: ERROR - HTTP ' . $httpCode);
        
        // Try to parse error details from response
        $errorData = json_decode($response, true);
        if ($errorData && isset($errorData['errors'])) {
            //$this->log->write('GET OFFERS FOR LISTING: eBay API Errors - ' . json_encode($errorData['errors']));
            
            // Check if it's the "invalid SKU" error (25707)
            foreach ($errorData['errors'] as $error) {
                if (isset($error['errorId']) && $error['errorId'] == 25707) {
                    //$this->log->write('GET OFFERS FOR LISTING: ERROR 25707 - Invalid SKU format detected');
                    //$this->log->write('GET OFFERS FOR LISTING: This means some offers in your eBay account have SKUs with non-alphanumeric characters');
                    //$this->log->write('GET OFFERS FOR LISTING: Trying alternative method - get offers from inventory item group');
                    
                    // Build the group key (same format as used in createInventoryItemGroup)
                    // groupKey format: {Country}_CARD_LIST_{listing_id}
                    // We need to extract this from the listing structure
                    // Try alternative method (we'll need to pass the groupKey)
                    return ['error' => 'Need groupKey to retrieve offers. Cannot determine from listing_id alone.', 'needs_groupkey' => true];
                }
            }
        }
        
        return ['error' => "Failed to retrieve offers (HTTP " . $httpCode . "): " . ($errorData['errors'][0]['message'] ?? 'Unknown error')];
    }

    $offers_data = json_decode($response, true);
    
    //$this->log->write('GET OFFERS FOR LISTING: Decoded response - ' . (is_array($offers_data) ? 'valid JSON' : 'invalid JSON'));

    if (empty($offers_data["offers"])) {
        //$this->log->write('GET OFFERS FOR LISTING: ERROR - No offers found in response');
        return ['error' => 'No offers found'];
    }
    
    //$this->log->write('GET OFFERS FOR LISTING: Found ' . count($offers_data["offers"]) . ' total offers');

    // Filter offers that match our listing ID
    $matchingOffers = [];
    foreach ($offers_data["offers"] as $offer) {
        if (isset($offer["listingId"]) && $offer["listingId"] == $listingId) {
            $matchingOffers[] = $offer;
        }
    }
    
    //$this->log->write('GET OFFERS FOR LISTING: Matched ' . count($matchingOffers) . ' offers for listing ID ' . $listingId);

    return $matchingOffers;
}


/**
 * Alternative method to get offers when GET /offer fails due to invalid SKUs in account
 * This method retrieves the inventory item group first, then gets offers for each SKU
 * 
 * @param string $groupKey The inventory item group key (e.g., "CA_CARD_LIST_92")
 * @param array $headers Request headers
 * @return array List of offers or error
 */
private function getOffersFromInventoryGroup($groupKey, $headers): array {
    //$this->log->write('GET OFFERS FROM INVENTORY GROUP: Start for groupKey ' . $groupKey);
    
    // Step 1: Get the inventory item group using the groupKey
    $groupUrl = "https://api.ebay.com/sell/inventory/v1/inventory_item_group/" . urlencode($groupKey);
    //$this->log->write('GET OFFERS FROM INVENTORY GROUP: Fetching group at ' . $groupUrl);
    
    $ch = curl_init($groupUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    
    if ($httpCode != 200) {
        //$this->log->write('GET OFFERS FROM INVENTORY GROUP: ERROR - Could not fetch group (HTTP ' . $httpCode . ')');
        //$this->log->write('GET OFFERS FROM INVENTORY GROUP: Response: ' . substr($response, 0, 500));
        
        $errorData = json_decode($response, true);
        if ($httpCode == 404) {
            //$this->log->write('GET OFFERS FROM INVENTORY GROUP: Group not found - groupKey may be incorrect');
            return ['error' => 'Inventory group not found. The listing may need to be republished.'];
        }
        
        return ['error' => 'Cannot retrieve offers via inventory group (HTTP ' . $httpCode . ')'];
    }
    
    $groupData = json_decode($response, true);
    
    if (!isset($groupData['variantSKUs']) || empty($groupData['variantSKUs'])) {
        //$this->log->write('GET OFFERS FROM INVENTORY GROUP: ERROR - No variant SKUs found in group');
        return ['error' => 'No variants found in listing group'];
    }
    
    //$this->log->write('GET OFFERS FROM INVENTORY GROUP: Found ' . count($groupData['variantSKUs']) . ' variant SKUs');
    
    // Step 2: For each SKU, get its offer
    $matchingOffers = [];
    
    foreach ($groupData['variantSKUs'] as $sku) {
        //$this->log->write('GET OFFERS FROM INVENTORY GROUP: Fetching offers for SKU ' . $sku);
        
        $offerUrl = "https://api.ebay.com/sell/inventory/v1/offer?sku=" . urlencode($sku);
        
        $ch = curl_init($offerUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $offerResponse = curl_exec($ch);
        $offerHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        
        if ($offerHttpCode == 200) {
            $offerData = json_decode($offerResponse, true);
            
            if (isset($offerData['offers']) && !empty($offerData['offers'])) {
                // Since we're getting offers by SKU from the group, all belong to this listing
                // No need to filter by listingId
                foreach ($offerData['offers'] as $offer) {
                    $matchingOffers[] = $offer;
                    //$this->log->write('GET OFFERS FROM INVENTORY GROUP: Found offer ' . $offer['offerId'] . ' for SKU ' . $sku);
                }
            }
        } else {
            //$this->log->write('GET OFFERS FROM INVENTORY GROUP: WARNING - Could not fetch offers for SKU ' . $sku . ' (HTTP ' . $offerHttpCode . ')');
        }
        
        usleep(100000); // 100ms delay between requests to avoid rate limiting
    }
    
    //$this->log->write('GET OFFERS FROM INVENTORY GROUP: Total matched offers: ' . count($matchingOffers));
    
    return $matchingOffers;
}





public function getOrders($date_start = null, $page = 1, $limit = 25, $marketplace_account_id = 1, $orders = array()) {
        if ($date_start === null) {
            $date_start = gmdate('Y-m-d\TH:i:s.000\Z', time() - 300);
        }
        
        $date_end = gmdate('Y-m-d\TH:i:s.000\Z', time() - 15);
        $url = 'https://api.ebay.com/ws/api.dll';
        $post = $this->buildGetOrdersRequest($date_start, $date_end, $page, $limit, $marketplace_account_id);
        $headers = $this->buildEbayHeaders("GetOrders", 1391, $marketplace_account_id, null);
        $response = $this->makeCurlRequest($url, $headers, $post);
        
        // Remplacer: $xml = new SimpleXMLElement($response);
        // Par:
        $xml = new \SimpleXMLElement($response);
        $result = json_decode(json_encode($xml), true);

        if (isset($result['OrderArray']['Order'])) {
            $orderNode = $result['OrderArray']['Order'];
            // Normalize: eBay returns a single associative array when only one order exists
            if (isset($orderNode[0])) {
                foreach ($orderNode as $order) {
                    $orders[] = $order;
                }
            } else {
                $orders[] = $orderNode;
            }
        }

        $totalPages = $result['PaginationResult']['TotalNumberOfPages'] ?? 1;
        if ($page < $totalPages) {
            $orders = $this->getOrders($date_start, $page + 1, $limit, $marketplace_account_id, $orders);
        }

        return $orders;
    }


public function getProduct($marketplace_item_id, $quantity = 1, $zipCode = "12919", $countryCode = "US", $marketplace_account_id = 1) {
    $url = "https://api.ebay.com/buy/browse/v1/item/v1|{$marketplace_item_id}|0";
  //"<pre>".print_r ($url,true )."</pre>"); 
    // Ajouter les paramètres de requête pour obtenir l'estimation d'expédition
    $queryParams = [
        "quantity_for_shipping_estimate" => $quantity
    ];
    
    $url .= "?" . http_build_query($queryParams);

    // Construire les en-têtes avec le bon token
    $connectionapi = $this->getApiCredentials($marketplace_account_id);
    $bearerToken = $connectionapi['bearer_token'];

    // Préparer les headers
    $headers = [
        "Authorization: Bearer " . $bearerToken,
        "Content-Type: application/json",
        "Accept: application/json",
        "X-EBAY-C-ENDUSERCTX: contextualLocation=country={$countryCode},zip={$zipCode}"
    ];

    $response = $this->makeCurlRequest($url, $headers);
    if (!$response) {
        return null;
    }

    // Convertir la réponse JSON en tableau PHP
    $responseArray = json_decode($response, true);
   //"<pre>".print_r ($responseArray,true )."</pre>"); 
    //die();
    // Vérifier si l'API a bien retourné les frais d'expédition
    if (isset($responseArray)) {
        return $responseArray;
        
    } else {
        return [
            "error" => $responseArray['errors'][0]['message'] ?? 'No shipping information available'
        ];
    }
}

    

    public function getStatus($account)
    {
      /*  $devId = '73b8492a-f471-4170-86b8-ce9e6e2d6796';
        $certId = 'PRD-93ff3ada979d-7fcf-4938-be46-ba89';
        $appId = 'CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28';
        $ruName = 'CanUShip-CanUShip-CanUsh-kxtwuegvx';
        $client = new \GuzzleHttp\Client(['verify' => false]);
        // Initialiser GuzzleHTTP Client
      //$client = new Client(['verify' => false]); // 'verify' => false désactive la vérification SSL
    
        // URL de l'API eBay
        $url = 'https://api.ebay.com/ws/api.dll';
    
        // En-têtes de la requête
        $headers = [
            'X-EBAY-API-COMPATIBILITY-LEVEL' => '1311',
            'X-EBAY-API-DEV-NAME' => $devId,
            'X-EBAY-API-APP-NAME' => $appId,
            'X-EBAY-API-CERT-NAME' => $certId,
            'X-EBAY-API-CALL-NAME' => 'GetTokenStatus',
            'X-EBAY-API-SITEID' => '0' // '0' = US
        ];
    
        // Corps de la requête XML
        $body = '<?xml version="1.0" encoding="utf-8" ?>';
        $body .= '<GetTokenStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $body .= '<RequesterCredentials>';
        $body .= '<eBayAuthToken>' . $account['auth_token'] . '</eBayAuthToken>';
        $body .= '</RequesterCredentials>';
        $body .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $body .= '<WarningLevel>High</WarningLevel>';
        $body .= '</GetTokenStatusRequest>';
    
        try {
            // Envoyer la requête POST avec Guzzle
            $response = $client->post($url, [
                'headers' => $headers,
                'body' => $body
            ]);
    
            // Récupérer le contenu de la réponse
            $xmlResponse = $response->getBody()->getContents();
    
            // Convertir la réponse XML en objet SimpleXMLElement
            $new = simplexml_load_string($xmlResponse);
    
            // Convertir l'objet XML en tableau PHP
            $json = json_decode(json_encode($new), true);
    
            // Vérifier le statut du token et déterminer l'image du statut
            if ($json['Ack'] == "Success") {
                if ($json['TokenStatus']['Status'] == "Active") {
                    $status_image = '<i class="fas fa-check-circle fa-2x" style="color:green"></i>';
                } else {
                    $status_image = '<i class="fas fa-times-circle fa-2x" style="color:red"></i>';
                }
            } else {
                $status_image = '<i class="fas fa-times-circle fa-2x" style="color:red"></i>';
            }
    
        } catch (\Exception $e) {
            // Gestion des erreurs
            echo "Error during GetTokenStatus request: " . $e->getMessage();
            $status_image = '<i class="fas fa-times-circle fa-2x" style="color:red"></i>';
        }
    */  $status_image = '<i class="fas fa-check-circle fa-2x" style="color:green"></i>';
        return $status_image;
    }

private function initializePriceVariants($category_id, $site_setting = []) {
//"<pre>".print_r (1092,true )."</pre>");
//"<pre>".print_r ($site_setting,true )."</pre>");
    $this->load->model('shopmanager/condition');
    //"<pre>" . print_r(value: '1419:ebaY.php') . "</pre>");

    $conditions=$this->model_shopmanager_condition->getConditionDetails($category_id);
    //"<pre>".print_r ($conditions,true )."</pre>"); 
    $conditions = isset($conditions['1']) && is_array($conditions['1']) ? $conditions['1'] : [];
  //"<pre>".print_r ($conditions,true )."</pre>"); 
 //   $conditions = $this->getConditionsByCategory($category_id, null,$site_setting);
    $conditions_return=[];
//"<pre>".print_r (1099,true )."</pre>");
 //"<pre>".print_r ($conditions,true )."</pre>"); 

   if(isset($category_id) && is_array($conditions)){
    foreach($conditions as $condition){
        $conditions_return[$condition['condition_marketplace_item_id']] = array (
            'price' => 99999,
            'marketplace_item_id' => '',
            'url' => '',
            'condition_name' => $condition['condition_name'] ?? '',
            'is_calculated' => false
        );
    }
 //"<pre>".print_r('834:ebay.php', true)."</pre>");
//"<pre>".print_r($conditions_return, true)."</pre>");
    return $conditions_return;

   }else{
        return array(
            '1000' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'New', 'is_calculated' => false),
            '1500' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Open box', 'is_calculated' => false),
            '1750' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Seller refurbished', 'is_calculated' => false),
            '2000' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Manufacturer refurbished', 'is_calculated' => false),
            '2010' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Certified refurbished', 'is_calculated' => false),
            '2020' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Excellent - Refurbished', 'is_calculated' => false),
            '2030' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Very Good - Refurbished', 'is_calculated' => false),
            '2500' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Like New', 'is_calculated' => false),
            '2750' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Excellent', 'is_calculated' => false),
            '3000' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Used', 'is_calculated' => false),
            '4000' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Very Good', 'is_calculated' => false),
            '5000' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Good', 'is_calculated' => false),
            '6000' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'Acceptable', 'is_calculated' => false),
            '7000' => array('price' => 99999, 'marketplace_item_id' => '', 'url' => '', 'condition_name' => 'For parts or not working', 'is_calculated' => false)
        );
    }
}



    /**
     * Search eBay sold items via Marketplace Insights API
     *
     * @param string $keyword     Search keywords (player name, set, year, etc.)
     * @param array  $options     sort, limit, page, site_id, category_id
     * @param int    $marketplace_account_id
     * @return array ['items' => [...], 'total' => int, 'keyword' => string, 'error' => string]
     */
    /**
     * Search eBay items — single Browse API call, no pagination, no enrichment.
     */
    public function searchActiveItems(string $keyword, array $options = [], int $marketplace_account_id = 1): array {
        $limit       = min((int)($options['limit'] ?? 100), 200);
        $page        = max((int)($options['page']  ?? 1), 1);
        $sort        = $options['sort'] ?? 'price_desc';
        $listingType = $options['listing_type'] ?? 'all';
        $siteId      = (int)($options['site_id'] ?? 0);
        $condFilter  = strtolower(trim((string)($options['condition_type'] ?? 'all')));
        $grader      = strtoupper(trim((string)($options['grader'] ?? 'all')));
        $grade       = trim((string)($options['grade'] ?? ''));
        $autoCorrect = 'KEYWORD';

        // Browse API sort values (même pattern que get())
        $sortMap = ['price_desc' => '-price', 'price_asc' => 'price', 'date_desc' => '-itemCreationDate'];
        $ebaySort = $sortMap[$sort] ?? '-price';

        $filterParts = [];
        if ($listingType === 'AUCTION') {
            $filterParts[] = 'buyingOptions:{AUCTION}';
        } elseif ($listingType === 'FIXED_PRICE') {
            $filterParts[] = 'buyingOptions:{FIXED_PRICE}';
        } else {
            $filterParts[] = 'buyingOptions:{AUCTION|FIXED_PRICE}';
        } 
        
        $headers = $this->getBrowseHeaders($marketplace_account_id, $siteId);

        $queryParams = [
            'q'            => $keyword,
            'category_ids' => (string)($options['category_id'] ?? '212'),
            'sort'         => $ebaySort,
            'limit'        => min($limit, 200),
            'offset'       => ($page - 1) * $limit,
        ];

        $queryParams['auto_correct'] = $autoCorrect;

        $categoryId  = (string)($options['category_id'] ?? '212');
        $sportAspect = trim((string)($options['sport_aspect'] ?? ''));

        if ($condFilter === 'graded') {
            $aspectParts = ['categoryId:' . $categoryId, 'Card Condition:{Graded}'];

            if ($grader !== '' && $grader !== 'ALL') {
                $aspectParts[] = 'Professional Grader:{' . $grader . '}';
            }

            if ($grade !== '') {
                $aspectParts[] = 'Grade:{' . $grade . '}';
            }

            if ($sportAspect) {
                $aspectParts[] = 'Sport:{' . $sportAspect . '}';
            }

            $queryParams['aspect_filter'] = implode(',', $aspectParts);
        } elseif ($condFilter === 'raw') {
            $af = 'categoryId:' . $categoryId . ',Card Condition:{Ungraded}';
            if ($sportAspect) $af .= ',Sport:{' . $sportAspect . '}';
            $queryParams['aspect_filter'] = $af;
        } elseif ($sportAspect) {
            $queryParams['aspect_filter'] = 'categoryId:' . $categoryId . ',Sport:{' . $sportAspect . '}';
        }

        $url = 'https://api.ebay.com/buy/browse/v1/item_summary/search?' . http_build_query($queryParams);
        if (!empty($filterParts)) {
            $url .= '&filter=' . rawurlencode(implode(',', $filterParts));
        }

        $ebayLogFile = '/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log';
        $ts = '[' . date('Y-m-d H:i:s') . ']';

        $response = $this->makeCurlRequest($url, $headers);
        if (!$response) {
            file_put_contents($ebayLogFile, "$ts [searchActiveItems] ERROR: empty response. keyword=\"$keyword\"", FILE_APPEND | LOCK_EX);
            return ['items' => [], 'total' => 0, 'keyword' => $keyword, 'error' => 'Empty response from Browse API', 'auto_corrections' => [], 'warnings' => []];
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            file_put_contents($ebayLogFile, "$ts [searchActiveItems] ERROR: invalid JSON. keyword=\"$keyword\" raw=" . substr($response, 0, 500) . "", FILE_APPEND | LOCK_EX);
            return ['items' => [], 'total' => 0, 'keyword' => $keyword, 'error' => 'Invalid JSON from Browse API', 'auto_corrections' => [], 'warnings' => []];
        }
        if (isset($data['errors'])) {
            $msg = $data['errors'][0]['longMessage'] ?? $data['errors'][0]['message'] ?? json_encode($data['errors'][0]);
            file_put_contents($ebayLogFile, "$ts [searchActiveItems] API error: $msg. keyword=\"$keyword\"", FILE_APPEND | LOCK_EX);
            return ['items' => [], 'total' => 0, 'keyword' => $keyword, 'error' => $msg, 'auto_corrections' => $data['autoCorrections'] ?? [], 'warnings' => $data['warnings'] ?? []];
        }

        $rawItems = $data['itemSummaries'] ?? [];
        $parsedItems = $this->parseBrowseSummaryItems($rawItems, false, $options);
        if (!empty($rawItems) && empty($parsedItems)) {
            file_put_contents($ebayLogFile, "$ts [searchActiveItems] WARNING: parsed 0 from non-empty raw. keyword=\"$keyword\" First raw item=" . json_encode($rawItems[0] ?? []) . "", FILE_APPEND | LOCK_EX);
        }



        return [
            'items' => $parsedItems,
            'total' => (int)($data['total'] ?? 0),
            'keyword' => $keyword,
            'error' => '',
            'auto_corrections' => $data['autoCorrections'] ?? [],
            'warnings' => $data['warnings'] ?? [],
        ];
    }

    public function buildManualEbayUrls(string $keyword): array {
        $base = 'https://www.ebay.ca/sch/i.html';
        $rawQuery = trim($keyword);

        return [
            'auction_raw' => $base . '?' . http_build_query([
                '_nkw' => $rawQuery,
                '_sacat' => '261328',
                '_sop' => '2',
                'LH_Auction' => '1',
                'Graded' => 'No'
            ]),
            'auction_graded' => $base . '?' . http_build_query([
                '_nkw' => $rawQuery,
                '_sacat' => '261328',
                '_sop' => '2',
                'LH_Auction' => '1',
                'Graded' => 'Yes'
            ]),
            'buy_now_raw' => $base . '?' . http_build_query([
                '_nkw' => $rawQuery,
                '_sacat' => '261328',
                '_sop' => '2',
                'LH_BIN' => '1',
                'Graded' => 'No'
            ]),
            'buy_now_graded' => $base . '?' . http_build_query([
                '_nkw' => $rawQuery,
                '_sacat' => '261328',
                '_sop' => '2',
                'LH_BIN' => '1',
                'Graded' => 'Yes'
            ]),
            'sold_graded' => $base . '?' . http_build_query([
                '_nkw' => $rawQuery,
                '_sacat' => '261328',
                'LH_Sold' => '1',
                'Graded' => 'Yes',
                '_sop' => '3',
                '_udlo' => '35'
            ])
        ];
    }

    public function searchAndClassifyActiveItems(string $keyword, array $searchOptions = [], int $marketplace_account_id = 1, string $excludeItemId = ''): array {
        $activeAll = $this->searchActiveItems($keyword, $searchOptions, $marketplace_account_id);

        return [
            'error' => (string)($activeAll['error'] ?? ''),
            'total' => (int)($activeAll['total'] ?? 0),
            'items' => $activeAll['items'] ?? [],
            'auto_corrections' => $activeAll['auto_corrections'] ?? [],
            'warnings' => $activeAll['warnings'] ?? [],
            'buckets' => $this->classifyMarketPriceBuckets($activeAll['items'] ?? [], $excludeItemId),
        ];
    }

    public function classifyMarketPriceBuckets(array $items, string $excludeItemId = ''): array {
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
            $isAuction = isset($item['listing_type']) && $item['listing_type'] === 'AUCTION';
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

    public function searchSoldItemsScraper(string $keyword, array $options = [], int $marketplace_account_id = 1): array {
        $keyword = trim($keyword);
        $this->log->write('[searchSoldItemsScraper][input] keyword=' . $keyword . ' listing=' . ($options['listing_type'] ?? 'all') . ' condition=' . ($options['condition_type'] ?? 'all') . ' grader=' . ($options['grader'] ?? 'all') . ' grade=' . ($options['grade'] ?? '') . ' site=' . (string)($options['site_id'] ?? 0) . ' page=' . (int)($options['page'] ?? 1) . ' limit=' . (int)($options['limit'] ?? 100));
        if ($keyword === '') {
            $this->log->write('[searchSoldItemsScraper][guard] empty keyword');
            return ['items' => [], 'total' => 0, 'keyword' => $keyword, 'error' => 'Keyword required for sold search'];
        }

        $limit = max(1, (int)($options['limit'] ?? 100));
        $page = max(1, (int)($options['page'] ?? 1));
        $siteId = (int)($options['site_id'] ?? 0);
        $maxSearchResults = $limit <= 60 ? 60 : ($limit <= 120 ? 120 : 240);
        $condFilter = strtolower(trim((string)($options['condition_type'] ?? 'all')));
        $graderFilter = strtoupper(trim((string)($options['grader'] ?? 'all')));
        $gradeFilter = trim((string)($options['grade'] ?? ''));

        $aspects = [];
        if (!empty($options['aspects']) && is_array($options['aspects'])) {
            foreach ($options['aspects'] as $aspect) {
                if (!is_array($aspect)) {
                    continue;
                }
                $aspectName = trim((string)($aspect['name'] ?? ''));
                $aspectValue = trim((string)($aspect['value'] ?? ''));
                if ($aspectName !== '' && $aspectValue !== '') {
                    $aspects[] = ['name' => $aspectName, 'value' => $aspectValue];
                }
            }
        }

        if (empty($aspects)) {
            if ($condFilter === 'raw') {
                $aspects[] = ['name' => 'Card Condition', 'value' => 'Ungraded'];
            } elseif ($condFilter === 'graded') {
                $aspects[] = ['name' => 'Card Condition', 'value' => 'Graded'];
                if ($graderFilter !== '' && $graderFilter !== 'ALL') {
                    $aspects[] = ['name' => 'Professional Grader', 'value' => $graderFilter];
                }
                if ($gradeFilter !== '') {
                    $aspects[] = ['name' => 'Grade', 'value' => $gradeFilter];
                }
            }
        }

        $excludedKeywords = trim((string)($options['excluded_keywords'] ?? ''));
        if ($excludedKeywords === '') {
            $excludedKeywords = trim((string)($options['exclude_keywords'] ?? ''));
        }

        $body = [
            'keywords' => $keyword,
            'excluded_keywords' => $excludedKeywords,
            'max_search_results' => (string)$maxSearchResults,
            'site_id' => (string)$siteId,
            'category_id' => (string)($options['category_id'] ?? '261328'),
            'remove_outliers' => (bool)($options['remove_outliers'] ?? true),
            'aspects' => $aspects,
        ];
        $this->log->write('[searchSoldItemsScraper][request_body] ' . json_encode($body));
        $this->log->write('[searchSoldItemsScraper][curl_example] curl --location --request POST \'https://ebay-sold-items-api.herokuapp.com/findCompletedItems\' --header \'Content-Type: application/json\' --data-raw \'' . json_encode($body) . '\'');

        $decodeJson = static function(string $rawClean): ?array {
            $decoded = json_decode($rawClean, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            if (is_string($decoded)) {
                $decodedString = json_decode($decoded, true);
                if (is_array($decodedString)) {
                    return $decodedString;
                }
            }

            if (preg_match('/(\{.*\}|\[.*\])/s', $rawClean, $m)) {
                $embedded = json_decode($m[1], true);
                if (is_array($embedded)) {
                    return $embedded;
                }
            }

            return null;
        };

        $connectionapi = $this->getApiCredentials($marketplace_account_id);
        $rapidApiKey = trim((string)(
            $connectionapi['rapidapi_key']
            ?? $connectionapi['x_rapidapi_key']
            ?? $connectionapi['connector_application_id']
            ?? $connectionapi['application_id']
            ?? $connectionapi['client_id']
            ?? $options['rapidapi_key']
            ?? $options['x_rapidapi_key']
            ?? getenv('RAPIDAPI_KEY')
            ?? getenv('EBAY_SOLD_RAPIDAPI_KEY')
            ?? ''
        ));

        if ($rapidApiKey === '') {
            $rapidApiKey = '3c4b7299d1mshd9261f0b31e7069p1cdd74jsna50907e62e57';
        }

        $requests = [];
        if ($rapidApiKey !== '') {
            $requests[] = [
                'url' => 'https://real-time-ebay-data.p.rapidapi.com/findCompletedItems',
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'x-rapidapi-host: real-time-ebay-data.p.rapidapi.com',
                    'x-rapidapi-key: ' . $rapidApiKey,
                ],
            ];

            $requests[] = [
                'url' => 'https://ebay-average-selling-price.p.rapidapi.com/findCompletedItems',
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'x-rapidapi-host: ebay-average-selling-price.p.rapidapi.com',
                    'x-rapidapi-key: ' . $rapidApiKey,
                ],
            ];
        }

        $requests[] = [
            'url' => 'https://ebay-sold-items-api.herokuapp.com/findCompletedItems',
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ];

        $data = null;
        $lastError = 'Sold scraper empty response';
        foreach ($requests as $requestConfig) {
            $this->log->write('[searchSoldItemsScraper][request_try] url=' . $requestConfig['url']);
            $raw = $this->makeCurlRequest($requestConfig['url'], $requestConfig['headers'], json_encode($body));
            if (!$raw) {
                $lastError = 'Sold scraper empty response';
                $this->log->write('[searchSoldItemsScraper][response_empty] url=' . $requestConfig['url']);
                continue;
            }

            $rawClean = ltrim((string)$raw, "\xEF\xBB\xBF \t\n\r\0\x0B");
            $this->log->write('[searchSoldItemsScraper][response_preview] url=' . $requestConfig['url'] . ' preview=' . substr(trim(preg_replace('/\s+/', ' ', strip_tags($rawClean))), 0, 280));
            if (stripos($rawClean, 'No such app') !== false) {
                $lastError = 'Sold scraper endpoint unavailable (No such app)';
                $this->log->write('[searchSoldItemsScraper][endpoint_unavailable] url=' . $requestConfig['url']);
                continue;
            }

            $decoded = $decodeJson($rawClean);
            if (is_array($decoded)) {
                // Only accept if it contains products/items — skip "not subscribed" or other error wrappers
                if (isset($decoded['products']) || isset($decoded['items'])) {
                    $data = $decoded;
                    $this->log->write('[searchSoldItemsScraper][decoded_ok] url=' . $requestConfig['url'] . ' keys=' . implode(',', array_slice(array_keys($decoded), 0, 12)));
                    break;
                }
                $lastError = 'API no products key: ' . ($decoded['message'] ?? $decoded['error'] ?? json_encode(array_slice($decoded, 0, 3)));
                $this->log->write('[searchSoldItemsScraper][skip_no_products] url=' . $requestConfig['url'] . ' reason=' . $lastError);
                continue;
            }

            $preview = trim(substr(strip_tags($rawClean), 0, 180));
            $lastError = 'Sold scraper invalid JSON: ' . ($preview !== '' ? $preview : 'empty/non-json response');
            $this->log->write('[searchSoldItemsScraper][decode_fail] url=' . $requestConfig['url'] . ' error=' . $lastError);
        }

        if (!is_array($data)) {
            $this->log->write('[searchSoldItemsScraper][final_error] ' . $lastError);
            return [
                'items' => [],
                'total' => 0,
                'keyword' => $keyword,
                'error' => $lastError,
            ];
        }

        if (!empty($data['error']) && empty($data['products'])) {
            $this->log->write('[searchSoldItemsScraper][api_error] ' . (string)$data['error']);
            return [
                'items' => [],
                'total' => 0,
                'keyword' => $keyword,
                'error' => (string)$data['error'],
            ];
        }
        // $this->log->write("[searchSoldItemsScraper] ebay_output=\n" . print_r($data, true));
        if (isset($data['success']) && !$data['success']) {
            $this->log->write('[searchSoldItemsScraper][success_false] message=' . (string)($data['message'] ?? 'Sold scraper error'));
            return ['items' => [], 'total' => 0, 'keyword' => $keyword, 'error' => (string)($data['message'] ?? 'Sold scraper error')];
        }

        $products = $data['products'] ?? [];
        $this->log->write('[searchSoldItemsScraper][products] count=' . count($products) . ' reported_total=' . (int)($data['results'] ?? 0));

        $items = [];
        $graderCodes = ['PSA', 'BGS', 'BGSX', 'SGC', 'CSA', 'HGA', 'GAI', 'ACE', 'CGC', 'KSA'];
        try {
            $this->load->model('shopmanager/card/card_grading_company');
            $activeCodes = $this->model_shopmanager_card_card_grading_company->getActiveCodes();
            if (!empty($activeCodes)) {
                $graderCodes = $activeCodes;
            }
        } catch (\Throwable $e) {
        }

        $graderRegexParts = [];
        foreach ($graderCodes as $graderCode) {
            $graderCode = strtoupper(trim((string)$graderCode));
            if ($graderCode !== '') {
                $graderRegexParts[] = preg_quote($graderCode, '/');
            }
        }
        $graderRegexAlternation = !empty($graderRegexParts) ? implode('|', $graderRegexParts) : 'PSA|BGS|BGSX|SGC|CSA|HGA|GAI|ACE|CGC|KSA';
        $filteredOut = 0;
        foreach ($products as $product) {
            $title = (string)($product['title'] ?? '');
            $price = (float)($product['sale_price'] ?? 0);
            $url = (string)($product['link'] ?? '');
            $picture = (string)($product['image'] ?? ($product['image_url'] ?? ''));
            $dateSold = (string)($product['date_sold'] ?? '');

            $grader = '';
            $gradeScore = '';
            $grade = '';
            $hasGraderMention = false;
            if (preg_match('/\b(' . $graderRegexAlternation . ')\b/i', $title, $mGrader)) {
                $hasGraderMention = true;
                $grader = strtoupper($mGrader[1]);
            }
            if (preg_match('/\b(' . $graderRegexAlternation . ')\s+(\d{1,2}(?:\.\d)?)\b/i', $title, $m)) {
                $grader = strtoupper($m[1]);
                $gradeScore = $m[2];
                $grade = $grader . ' ' . $gradeScore;
            }

            $conditionText = strtolower(trim((string)($product['condition'] ?? '')));
            $explicitGradedFlag = $this->normalizeGradedFlagValue((string)($product['graded'] ?? ''));
            if ($explicitGradedFlag === null) {
                $explicitGradedFlag = $this->normalizeGradedFlagValue((string)($product['grade'] ?? ''));
            }
            $isConditionUngraded = ($conditionText === 'ungraded');
            $isExplicitUngraded = $isConditionUngraded || stripos($title, 'ungraded') !== false || stripos($title, 'not graded') !== false;
            $hasGraderAndScore = ($grader !== '' && $gradeScore !== '');
            $isConditionGraded = ($conditionText === 'graded');
            $isGraded = !$isExplicitUngraded && ($hasGraderMention || $hasGraderAndScore);
            if ($isConditionGraded && !$hasGraderMention) {
                $isGraded = false;
            }
            if ($explicitGradedFlag !== null) {
                $isGraded = $explicitGradedFlag;
            }

            $condFilter = strtolower(trim((string)($options['condition_type'] ?? 'all')));
            $graderFilter = $condFilter === 'graded' ? strtoupper(trim((string)($options['grader'] ?? 'all'))) : 'ALL';
            $gradeFilter = $condFilter === 'graded' ? trim((string)($options['grade'] ?? '')) : '';
            $gradeFilterNumeric = preg_replace('/[^0-9\.]/', '', $gradeFilter);

            if ($condFilter === 'graded' && !$isGraded) {
                $filteredOut++;
                continue;
            }
            if ($condFilter === 'raw' && $isGraded) {
                $filteredOut++;
                continue;
            }
            if ($graderFilter !== '' && strtoupper($graderFilter) !== 'ALL') {
                if ($grader === '' || strtoupper($grader) !== $graderFilter) {
                    $filteredOut++;
                    continue;
                }
            }
            if ($gradeFilter !== '') {
                if ($gradeFilterNumeric !== '') {
                    $scoreNumeric = preg_replace('/[^0-9\.]/', '', (string)$gradeScore);
                    if ($scoreNumeric === '' || (float)$scoreNumeric !== (float)$gradeFilterNumeric) {
                        $filteredOut++;
                        continue;
                    }
                } elseif (stripos($title, $gradeFilter) === false) {
                    $filteredOut++;
                    continue;
                }
            }

            $items[] = [
                'item_id'      => (string)($product['item_id'] ?? md5($url . $title)),
                'title'        => $title,
                'url'          => $url,
                'picture'      => $picture,
                'gallery'      => $picture,
                'price'        => $price,
                'currency'     => (string)($product['currency'] ?? 'USD'),
                'condition'    => (string)($product['condition'] ?? ''),
                'date_sold'    => $dateSold,
                'date_created' => '',
                'date_ended'   => $dateSold,
                'grade'        => $grade,
                'grader'       => $grader,
                'grade_score'  => $gradeScore,
                'player'       => '',
                'card_number'  => '',
                'set_name'     => '',
                'year'         => '',
                'team'         => '',
                'sport'        => '',
                'is_graded'    => $isGraded,
                'listing_type' => 'SOLD',
                'bid_count'    => 0,
            ];
        }
        $this->log->write('[searchSoldItemsScraper][filtering] kept=' . count($items) . ' filtered_out=' . $filteredOut);

        $sort = (string)($options['sort'] ?? 'price_desc');
        if ($sort === 'price_asc') {
            usort($items, static function(array $a, array $b): int {
                return ($a['price'] <=> $b['price']);
            });
        } elseif ($sort === 'date_desc') {
            usort($items, static function(array $a, array $b): int {
                $ta = strtotime((string)($a['date_ended'] ?? '')) ?: 0;
                $tb = strtotime((string)($b['date_ended'] ?? '')) ?: 0;
                return $tb <=> $ta;
            });
        } else {
            usort($items, static function(array $a, array $b): int {
                return ($b['price'] <=> $a['price']);
            });
        }

        $offset = ($page - 1) * $limit;
        $pagedItems = array_slice($items, $offset, $limit);

        $total = (int)($data['results'] ?? count($products));
        $this->log->write('[searchSoldItemsScraper][output] parsed=' . count($items) . ' paged=' . count($pagedItems) . ' total=' . $total . ' sort=' . $sort . ' page=' . $page . ' limit=' . $limit);
        return ['items' => $pagedItems, 'total' => $total, 'keyword' => $keyword, 'error' => ''];
    }

    public function searchByImageItems(string $imagePath, array $options = [], int $marketplace_account_id = 1): array {
        $limit       = min((int)($options['limit'] ?? 100), 200);
        $page        = max((int)($options['page']  ?? 1), 1);
        $sort        = $options['sort'] ?? 'price_desc';
        $listingType = $options['listing_type'] ?? 'all';
        $siteId      = (int)($options['site_id'] ?? 0);

        // $this->log->write('[searchByImageItems] start listing=' . $listingType . ' condition=' . ($options['condition_type'] ?? 'all') . ' site=' . $siteId . ' page=' . $page . ' limit=' . $limit . ' imagePath=' . $imagePath);

        if (!is_file($imagePath) || !is_readable($imagePath)) {
            return ['items' => [], 'total' => 0, 'keyword' => 'Image search', 'error' => 'Image file not found'];
        }

        $image = file_get_contents($imagePath);
        if ($image === false || $image === '') {
            return ['items' => [], 'total' => 0, 'keyword' => 'Image search', 'error' => 'Image file is empty'];
        }

        $sortMap = ['price_desc' => '-price', 'price_asc' => 'price', 'date_desc' => '-itemCreationDate'];
        $ebaySort = $sortMap[$sort] ?? '-price';

        $filterParts = [];
        if ($listingType === 'FIXED_PRICE') {
            $filterParts[] = 'buyingOptions:%7BFIXED_PRICE%7CBEST_OFFER%7D';
        }

        $headers = $this->getBrowseHeaders($marketplace_account_id, $siteId);
        $loggedHeaders = $headers;
        foreach ($loggedHeaders as &$loggedHeader) {
            if (stripos($loggedHeader, 'Authorization: Bearer ') === 0) {
                $token = substr($loggedHeader, strlen('Authorization: Bearer '));
                $loggedHeader = 'Authorization: Bearer ' . substr($token, 0, 12) . '...';
            }
        }
        unset($loggedHeader);

        $queryParams = [
            'category_ids' => $options['category_id'] ?? '261328',
            'limit'        => min($limit, 200),
            'offset'       => ($page - 1) * $limit,
            'sort'         => $ebaySort,
        ];

        $url = 'https://api.ebay.com/buy/browse/v1/item_summary/search_by_image?' . http_build_query($queryParams);
        if (!empty($filterParts)) {
            $url .= '&filter=' . implode(',', $filterParts);
        }

        // $this->log->write('[searchByImageItems] url=' . $url);
        // $this->log->write('[searchByImageItems] headers=' . json_encode($loggedHeaders));

        $payload = json_encode(['image' => base64_encode($image)], JSON_UNESCAPED_SLASHES);
        $response = $this->makeCurlRequest($url, $headers, $payload);

        if (!$response) {
            return ['items' => [], 'total' => 0, 'keyword' => 'Image search', 'error' => 'Empty response from Browse image API'];
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return ['items' => [], 'total' => 0, 'keyword' => 'Image search', 'error' => 'Invalid JSON from Browse image API'];
        }
        // $this->log->write("[searchByImageItems] ebay_output=\n" . print_r($data, true));
        if (isset($data['errors'])) {
            $msg = $data['errors'][0]['longMessage'] ?? $data['errors'][0]['message'] ?? json_encode($data['errors'][0]);
            // $this->log->write('[searchByImageItems] Browse API error: ' . $msg);
            return ['items' => [], 'total' => 0, 'keyword' => 'Image search', 'error' => $msg];
        }

        $rawItems = $data['itemSummaries'] ?? [];
        $parsedItems = $this->parseBrowseSummaryItems($rawItems, true, $options);
        // $this->log->write('[searchByImageItems] total=' . (int)($data['total'] ?? 0) . ' raw=' . count($rawItems) . ' parsed=' . count($parsedItems));

        return ['items' => $parsedItems, 'total' => (int)($data['total'] ?? 0), 'keyword' => 'Image search', 'error' => ''];
    }

    private function getBrowseHeaders(int $marketplace_account_id = 1, int $siteId = 0): array {
        $connectionapi = $this->getApiCredentials($marketplace_account_id);
        $bearerToken   = $connectionapi['bearer_token'] ?? '';

        $headers = [
            'Authorization: Bearer ' . $bearerToken,
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        $marketplaceMap = [
            0  => 'EBAY_US',
            2  => 'EBAY_CA',
            3  => 'EBAY_GB',
            71 => 'EBAY_FR',
            77 => 'EBAY_DE',
        ];

        if (isset($marketplaceMap[$siteId])) {
            $headers[] = 'X-EBAY-C-MARKETPLACE-ID: ' . $marketplaceMap[$siteId];
        }

        return $headers;
    }

    private function parseBrowseSummaryItems(array $rawItems, bool $excludeZeroBidAuctions = true, array $options = []): array {
        $items = [];

        $condFilter = strtolower(trim((string)($options['condition_type'] ?? 'all')));
        $listingFilter = strtoupper(trim((string)($options['listing_type'] ?? 'all')));
        $graderFilter = $condFilter === 'graded' ? strtoupper(trim((string)($options['grader'] ?? 'all'))) : 'ALL';
        $gradeFilter = $condFilter === 'graded' ? trim((string)($options['grade'] ?? '')) : '';
        $gradeFilterNumeric = preg_replace('/[^0-9\.]/', '', $gradeFilter);

        $graderCodes = ['PSA', 'BGS', 'BGSX', 'SGC', 'CSA', 'HGA', 'GAI', 'ACE', 'CGC', 'KSA'];
        try {
            $this->load->model('shopmanager/card/card_grading_company');
            $activeCodes = $this->model_shopmanager_card_card_grading_company->getActiveCodes();
            if (!empty($activeCodes)) {
                $graderCodes = $activeCodes;
            }
        } catch (\Throwable $e) {
        }

        $graderRegexParts = [];
        foreach ($graderCodes as $graderCode) {
            $graderCode = strtoupper(trim((string)$graderCode));
            if ($graderCode !== '') {
                $graderRegexParts[] = preg_quote($graderCode, '/');
            }
        }
        $graderRegexAlternation = !empty($graderRegexParts) ? implode('|', $graderRegexParts) : 'PSA|BGS|BGSX|SGC|CSA|HGA|GAI|ACE|CGC|KSA';

        foreach ($rawItems as $item) {
            $title         = $item['title'] ?? '';
            $buyingOptions = $item['buyingOptions'] ?? [];
            $hasBuyingOptions = !empty($buyingOptions);
            $isAuction = false;
            foreach ($buyingOptions as $buyingOption) {
                if (stripos((string)$buyingOption, 'AUCTION') !== false) {
                    $isAuction = true;
                    break;
                }
            }

            if ($listingFilter === 'AUCTION' && $hasBuyingOptions && !$isAuction) {
                continue;
            }
            if ($listingFilter === 'FIXED_PRICE' && $isAuction) {
                continue;
            }

            if ($isAuction && isset($item['currentBidPrice']['value'])) {
                $price    = (float)$item['currentBidPrice']['value'];
                $currency = $item['currentBidPrice']['currency'] ?? 'USD';
            } else {
                $price    = (float)($item['price']['value'] ?? 0);
                $currency = $item['price']['currency'] ?? 'USD';
            }

            $shippingCost = (float)($item['shippingOptions'][0]['shippingCost']['value'] ?? 0);

            $picture  = $item['image']['imageUrl'] ?? ($item['thumbnailImages'][0]['imageUrl'] ?? '');
            $itemUrl  = $item['itemWebUrl'] ?? '';
            $bidCount = (int)($item['bidCount'] ?? 0);
            $createdDate = '';
            $endedDate = '';

            if (!empty($item['itemCreationDate'])) {
                try {
                    $createdDate = (new \DateTime($item['itemCreationDate']))->format('Y-m-d H:i');
                } catch (\Exception $e) {
                    $createdDate = '';
                }
            }

            if (!empty($item['itemEndDate'])) {
                try {
                    $endedDate = (new \DateTime($item['itemEndDate']))->format('Y-m-d H:i');
                } catch (\Exception $e) {
                    $endedDate = '';
                }
            }

            if ($excludeZeroBidAuctions && $isAuction && $bidCount <= 0) {
                continue;
            }

            $grader = ''; $gradeScore = ''; $grade = ''; $hasGraderMention = false;
            if (preg_match('/\b(' . $graderRegexAlternation . ')\b/i', $title, $mGrader)) {
                $hasGraderMention = true;
                $grader = strtoupper($mGrader[1]);
            }
            if (preg_match('/\b(' . $graderRegexAlternation . ')\s+(\d{1,2}(?:\.\d)?)\b/i', $title, $m)) {
                $grader = strtoupper($m[1]); $gradeScore = $m[2]; $grade = $grader . ' ' . $gradeScore;
            }

            $conditionText = strtolower(trim((string)($item['condition'] ?? '')));
            $explicitGradedFlag = null;

            if (!empty($item['localizedAspects']) && is_array($item['localizedAspects'])) {
                foreach ($item['localizedAspects'] as $aspect) {
                    $aspectName = strtolower(trim((string)($aspect['name'] ?? '')));
                    if ($aspectName !== 'graded') {
                        continue;
                    }

                    $rawAspectValue = '';
                    if (isset($aspect['value'])) {
                        if (is_array($aspect['value'])) {
                            $rawAspectValue = implode(' ', array_map('strval', $aspect['value']));
                        } else {
                            $rawAspectValue = (string)$aspect['value'];
                        }
                    } elseif (isset($aspect['values']) && is_array($aspect['values'])) {
                        $rawAspectValue = implode(' ', array_map('strval', $aspect['values']));
                    }

                    $explicitGradedFlag = $this->normalizeGradedFlagValue($rawAspectValue);
                    break;
                }
            }

            $isConditionUngraded = ($conditionText === 'ungraded');
            $isExplicitUngraded = $isConditionUngraded || stripos($title, 'ungraded') !== false || stripos($title, 'not graded') !== false;
            $hasGraderAndScore = ($grader !== '' && $gradeScore !== '');
            $isConditionGraded = ($conditionText === 'graded');
            $isGraded = !$isExplicitUngraded && ($hasGraderMention || $hasGraderAndScore);
            if ($isConditionGraded && !$hasGraderMention) {
                $isGraded = false;
            }
            if ($explicitGradedFlag !== null) {
                $isGraded = $explicitGradedFlag;
            }

            if ($condFilter === 'graded' && !$isGraded) {
                continue;
            }
            if ($condFilter === 'raw' && $isGraded) {
                continue;
            }
            if ($graderFilter !== '' && strtoupper($graderFilter) !== 'ALL') {
                if ($grader === '' || strtoupper($grader) !== $graderFilter) {
                    continue;
                }
            }
            if ($gradeFilter !== '') {
                if ($gradeFilterNumeric !== '') {
                    $scoreNumeric = preg_replace('/[^0-9\.]/', '', (string)$gradeScore);
                    if ($scoreNumeric === '' || (float)$scoreNumeric !== (float)$gradeFilterNumeric) {
                        continue;
                    }
                } elseif (stripos($title, $gradeFilter) === false) {
                    continue;
                }
            }

            $items[] = [
                'item_id'      => $item['legacyItemId'] ?? $item['itemId'] ?? '',
                'title'        => $title,
                'url'          => $itemUrl,
                'picture'      => $picture,
                'gallery'      => $picture,
                'price'        => $price,
                'currency'     => $currency,
                'shipping_cost'=> $shippingCost,
                'total_price'  => $price + $shippingCost,
                'condition'    => $item['condition'] ?? '',
                'date_sold'    => '',
                'date_created' => $createdDate,
                'date_ended'   => $endedDate,
                'grade'        => $grade,
                'grader'       => $grader,
                'grade_score'  => $gradeScore,
                'player'       => '',
                'card_number'  => '',
                'set_name'     => '',
                'year'         => '',
                'team'         => '',
                'sport'        => '',
                'is_graded'    => $isGraded,
                'listing_type' => $isAuction ? 'AUCTION' : 'FIXED_PRICE',
                'bid_count'    => $bidCount,
            ];
        }

        return $items;
    }

    private function normalizeGradedFlagValue(string $value): ?bool {
        $text = strtolower(trim($value));
        if ($text === '') {
            return null;
        }

        $compact = preg_replace('/[^a-z0-9]+/', '', $text);
        if ($compact === null || $compact === '') {
            return null;
        }

        if ($compact === 'graded' || $compact === 'yes' || $compact === 'y' || $compact === 'true' || $compact === '1') {
            return true;
        }

        if ($compact === 'ungraded' || $compact === 'notgraded' || $compact === 'no' || $compact === 'n' || $compact === 'false' || $compact === '0' || $compact === 'na' || $compact === 'ny') {
            return false;
        }

        if (strpos($text, 'yes') !== false && strpos($text, 'no') !== false) {
            return false;
        }

        return null;
    }

    /**
     * Finding API – findCompletedItems (SoldItemsOnly=true)
     * Returns the lowest sold price (price only, not incl. shipping) for a keyword on eBay CA.
     * Result: ['price' => float|null, 'currency' => string, 'title' => string, 'error' => string]
     */
    public function findingApiCompletedItems(string $keyword, int $marketplace_account_id = 1): array {
        $connectionapi = $this->getApiCredentials($marketplace_account_id);
        $appId = $connectionapi['client_id'] ?? '';

        $headers = [
            "X-EBAY-SOA-SECURITY-APPNAME: {$appId}",
            "X-EBAY-SOA-OPERATION-NAME: findCompletedItems",
            "X-EBAY-SOA-GLOBAL-ID: EBAY-ENCA",
            "X-EBAY-SOA-SERVICE-VERSION: 1.13.0",
            "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML",
            "X-EBAY-SOA-RESPONSE-DATA-FORMAT: XML",
            "Content-Type: text/xml;charset=utf-8",
        ];

        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>'
            . '<findCompletedItemsRequest xmlns="http://www.ebay.com/marketplace/search/v1/services">'
            . '<keywords>' . htmlspecialchars($keyword, ENT_XML1, 'UTF-8') . '</keywords>'
            . '<itemFilter><name>SoldItemsOnly</name><value>true</value></itemFilter>'
            . '<sortOrder>PricePlusShippingLowest</sortOrder>'
            . '<paginationInput><entriesPerPage>5</entriesPerPage><pageNumber>1</pageNumber></paginationInput>'
            . '</findCompletedItemsRequest>';

        $raw = $this->makeCurlRequest('https://svcs.ebay.com/services/search/FindingService/v1', $headers, $xmlBody);
        if (!$raw) {
            return ['price' => null, 'currency' => 'CAD', 'title' => '', 'error' => 'empty response'];
        }

        $xml = @simplexml_load_string($raw);
        if ($xml === false) {
            return ['price' => null, 'currency' => 'CAD', 'title' => '', 'error' => 'invalid xml'];
        }

        $ack = (string)($xml->ack ?? '');
        if (strtolower($ack) === 'failure') {
            $errMsg = (string)($xml->errorMessage->error->message ?? 'api error');
            return ['price' => null, 'currency' => 'CAD', 'title' => '', 'error' => $errMsg];
        }

        $searchResult = $xml->searchResult ?? null;
        if (!$searchResult || (int)($searchResult['count'] ?? 0) === 0) {
            return ['price' => null, 'currency' => 'CAD', 'title' => '', 'error' => 'no results'];
        }

        $item     = $searchResult->item[0];
        $price    = (float)(string)$item->sellingStatus->currentPrice;
        $currency = (string)$item->sellingStatus->currentPrice['currencyId'] ?: 'USD';
        $title    = (string)($item->title ?? '');

        return ['price' => $price, 'currency' => $currency, 'title' => $title, 'error' => ''];
    }

    /**
     * Finding API – findItemsByKeywords (active listings), sorted by PricePlusShippingLowest
     * Returns the lowest total (price + shipping) for a keyword on eBay CA.
     * Result: ['price' => float|null, 'currency' => string, 'title' => string, 'error' => string]
     */
    public function findingApiActiveItemsPlusShipping(string $keyword, int $marketplace_account_id = 1): array {
        $connectionapi = $this->getApiCredentials($marketplace_account_id);
        $appId = $connectionapi['client_id'] ?? '';

        $headers = [
            "X-EBAY-SOA-SECURITY-APPNAME: {$appId}",
            "X-EBAY-SOA-OPERATION-NAME: findItemsByKeywords",
            "X-EBAY-SOA-GLOBAL-ID: EBAY-ENCA",
            "X-EBAY-SOA-SERVICE-VERSION: 1.13.0",
            "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML",
            "X-EBAY-SOA-RESPONSE-DATA-FORMAT: XML",
            "Content-Type: text/xml;charset=utf-8",
        ];

        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>'
            . '<findItemsByKeywordsRequest xmlns="http://www.ebay.com/marketplace/search/v1/services">'
            . '<keywords>' . htmlspecialchars($keyword, ENT_XML1, 'UTF-8') . '</keywords>'
            . '<sortOrder>PricePlusShippingLowest</sortOrder>'
            . '<paginationInput><entriesPerPage>5</entriesPerPage><pageNumber>1</pageNumber></paginationInput>'
            . '</findItemsByKeywordsRequest>';

        $raw = $this->makeCurlRequest('https://svcs.ebay.com/services/search/FindingService/v1', $headers, $xmlBody);
        if (!$raw) {
            return ['price' => null, 'currency' => 'CAD', 'title' => '', 'error' => 'empty response'];
        }

        $xml = @simplexml_load_string($raw);
        if ($xml === false) {
            return ['price' => null, 'currency' => 'CAD', 'title' => '', 'error' => 'invalid xml'];
        }

        $ack = (string)($xml->ack ?? '');
        if (strtolower($ack) === 'failure') {
            $errMsg = (string)($xml->errorMessage->error->message ?? 'api error');
            return ['price' => null, 'currency' => 'CAD', 'title' => '', 'error' => $errMsg];
        }

        $searchResult = $xml->searchResult ?? null;
        if (!$searchResult || (int)($searchResult['count'] ?? 0) === 0) {
            return ['price' => null, 'currency' => 'CAD', 'title' => '', 'error' => 'no results'];
        }

        $item     = $searchResult->item[0];
        $price    = (float)(string)$item->sellingStatus->currentPrice;
        $currency = (string)$item->sellingStatus->currentPrice['currencyId'] ?: 'USD';
        $title    = (string)($item->title ?? '');

        // Add shipping to get true total cost
        $shipping = 0.0;
        if (isset($item->shippingInfo->shippingServiceCost)) {
            $shipping = (float)(string)$item->shippingInfo->shippingServiceCost;
        }
        $price += $shipping;

        return ['price' => $price, 'currency' => $currency, 'title' => $title, 'error' => ''];
    }

    // Fonction cURL pour effectuer la requête HTTP
    private function makeCurlRequest($url, $headers, $postFields = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);  // 10s max pour établir la connexion
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);          // 30s max total — évite le freeze indéfini
    
        if ($postFields) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }

        $t0 = microtime(true);
        error_log('[makeCurlRequest] START url=' . preg_replace('/^(https?:\/\/[^?]+).*/', '$1', $url));
        // Exécuter la requête cURL
        $response = curl_exec($ch);
        $ms = round((microtime(true) - $t0) * 1000);
        error_log('[makeCurlRequest] END ms=' . $ms . ' errno=' . curl_errno($ch) . ' httpcode=' . curl_getinfo($ch, CURLINFO_HTTP_CODE));
      //echo "<pre>response: " . print_r($response, true) . "</pre>";
        // Afficher les informations de cURL pour le débogage
        $curlInfo = curl_getinfo($ch);
   //echo "<pre>cURL Info: " . print_r($curlInfo, true) . "</pre>";
   //echo "<pre>response json: " . print_r(json_decode($response,true), true) . "</pre>";
    
        // Vérifier s'il y a des erreurs cURL
        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            echo "<pre>cURL Error: " . htmlspecialchars($errorMessage) . "</pre>";
            return json_encode(['error' => 'Curl error: ' . $errorMessage]);
        }
    
    //$responseXml = simplexml_load_string($response);
	//	$responseArray = json_decode(json_encode($responseXml), true);
   /*     if (isset($responseArray['error']['parameter']) && is_array($responseArray['error']['parameter'])) {
          //foreach ($responseArray['error']['parameter'] as $apiName) {
                // Appel de la méthode checkApiLimit pour chaque ApiName trouvé
                $this->checkApiLimit();
        //}
            return $response;
        }else{*/
        //"<pre>" . print_r(json_decode($response,true), true) . "</pre>");
            return $response;
      //}
      
    }


    /**
     * Execute a REST HTTP request (GET / POST / PUT / DELETE).
     * Returns ['body' => array|null, 'httpCode' => int, 'error' => string].
     *
     * @param string     $url
     * @param string     $method   GET, POST, PUT, DELETE
     * @param array      $headers
     * @param array|null $body     Associative array — will be JSON-encoded
     * @param int        $timeout  Seconds
     */
    private function makeCurlRestRequest(string $url, string $method, array $headers, ?array $body = null, int $timeout = 30): array {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $method = strtoupper($method);
        if ($method === 'GET') {
            // default
        } elseif ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($body !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            }
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($body !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            }
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($body !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            }
        }

        $response  = curl_exec($ch);
        $httpCode  = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        

        if ($curlError) {
            return ['body' => null, 'httpCode' => 0, 'error' => $curlError];
        }

        $decoded = json_decode($response, true);
        return ['body' => $decoded, 'httpCode' => $httpCode, 'error' => ''];
    }



/**
 * Migrer les images Google (googleapis.com) vers eBay pour un listing donné.
 * Lit oc_card_image, upload uniquement les images Google, met à jour l'URL dans la DB.
 *
 * @param int   $listing_id  ID du listing (oc_card_listing.listing_id)
 * @return array Statistiques : uploaded, already_on_ebay, failed, skipped
 */
public function migrateImagesToEbay(int $listing_id, $marketplace_account_id = null): array {
    $connectionapi = $this->getApiCredentials($marketplace_account_id);
    $bearerToken   = $connectionapi['bearer_token'] ?? '';
    if (empty($bearerToken)) {
        return ['listing_id' => $listing_id, 'status' => 'error',
                'uploaded' => 0, 'already_on_ebay' => 0, 'failed' => 1,
                'failed_details' => [['error' => 'No eBay OAuth token — try refreshing']]];
    }
    $headers = [
        'Authorization: Bearer ' . $bearerToken,
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    // 1. Récupérer toutes les images Google liées à ce listing via oc_card
    $sql = "SELECT ci.image_id, ci.card_id, ci.image_url
            FROM " . DB_PREFIX . "card_image ci
            INNER JOIN " . DB_PREFIX . "card c ON c.card_id = ci.card_id
            WHERE c.listing_id = '" . (int)$listing_id . "'
              AND ci.image_url LIKE '%googleapis.com%'
            ORDER BY ci.card_id ASC, ci.sort_order ASC";

    $rows = $this->db->query($sql)->rows;

    if (empty($rows)) {
        return [
            'listing_id'     => $listing_id,
            'status'         => 'no_google_images',
            'uploaded'       => 0,
            'already_on_ebay'=> 0,
            'failed'         => 0,
            'failed_details' => []
        ];
    }

    $uploaded_count      = 0;
    $already_on_ebay     = 0;
    $failed_uploads      = [];

    // 2. Pour chaque image Google : upload vers eBay et update DB
    foreach ($rows as $row) {
        $image_id  = (int)$row['image_id'];
        $image_url = $row['image_url'];

        // Sécurité : skip si déjà sur eBay (ne devrait pas arriver avec le filtre SQL)
        if (strpos($image_url, 'ebayimg.com') !== false) {
            $already_on_ebay++;
            continue;
        }

        $upload_result = $this->uploadImageToEbay($image_url, $headers);

        if ($upload_result['success'] && !empty($upload_result['ebay_url'])) {
            // Mettre à jour l'URL dans oc_card_image
            $this->db->query(
                "UPDATE " . DB_PREFIX . "card_image
                 SET image_url = '" . $this->db->escape($upload_result['ebay_url']) . "'
                 WHERE image_id = '" . $image_id . "'"
            );
            $uploaded_count++;
        } else {
            $failed_uploads[] = [
                'image_id' => $image_id,
                'url'      => $image_url,
                'error'    => $upload_result['error'] ?? 'Unknown error'
            ];
        }
    }

    $status = $uploaded_count > 0 ? 'migrated' : ($failed_uploads ? 'failed' : 'no_google_images');

    return [
        'listing_id'     => $listing_id,
        'status'         => $status,
        'uploaded'       => $uploaded_count,
        'already_on_ebay'=> $already_on_ebay,
        'failed'         => count($failed_uploads),
        'failed_details' => $failed_uploads
    ];
}


    /**
     * POST /offer/publish_by_inventory_item_group
     * Publishes ALL unpublished offers in a variation group in one call.
     * This is the correct endpoint for card listings (variation groups).
     * Returns ['listingId' => string|null, 'published' => bool, 'error' => string|null]
     */
    private function publishByInventoryItemGroup(string $groupKey, string $marketplaceId, array $headers): array {
        $url     = 'https://api.ebay.com/sell/inventory/v1/offer/publish_by_inventory_item_group';
        $payload = ['inventoryItemGroupKey' => $groupKey, 'marketplaceId' => $marketplaceId];

        $response = $this->makeCurlRestRequest($url, 'POST', $headers, $payload);
        $httpCode = $response['httpCode'] ?? 0;
        $body     = $response['body']    ?? [];

        //$this->log->write('[publishByInventoryItemGroup] groupKey=' . $groupKey . ' marketplaceId=' . $marketplaceId . ' HTTP=' . $httpCode);

        if ($response['error']) {
            return ['listingId' => null, 'published' => false, 'error' => 'cURL: ' . $response['error']];
        }
        if ($httpCode >= 200 && $httpCode < 300) {
            $listingId = $body['listingId'] ?? null;
            //$this->log->write('[publishByInventoryItemGroup] SUCCESS listingId=' . ($listingId ?? 'n/a'));
            return ['listingId' => $listingId, 'published' => true, 'error' => null];
        }
        $errMsg = '';
        if (!empty($body['errors'])) {
            $e      = $body['errors'][0];
            $errMsg = $e['longMessage'] ?? $e['message'] ?? json_encode($e, JSON_UNESCAPED_UNICODE);
            // Error 25019: listing is part of an active sale — cannot republish.
            // Listing is already live on eBay, treat as soft success.
            if (($e['errorId'] ?? 0) === 25019) {
                //$this->log->write('[publishByInventoryItemGroup] SKIP ' . $groupKey . ' — part of active sale (25019). Listing already live.');
                return ['listingId' => null, 'published' => true, 'error' => null, 'skipped' => true];
            }
        } else {
            $errMsg = 'HTTP ' . $httpCode;
        }
        $this->log->write('[publishByInventoryItemGroup] FAILED error=' . $errMsg);
        return ['listingId' => null, 'published' => false, 'error' => $errMsg];
    }


private function publishInventoryOffer($template_data, $site_setting, $headers, $merchantLocationKey): array {
    //$this->log->write('[publishInventoryOffer] Batch create offers for listing_id=' . $template_data['listing_id'] . ' with ' . count($template_data['variations']) . ' variations');
    
     $countryCode   = $site_setting['Currency']['Country'] ?? 'CA';
    $countryMap    = [
        'CA'   => ['marketplaceId' => 'EBAY_CA', 'currency' => 'CAD'],
        'CAFR' => ['marketplaceId' => 'EBAY_CA', 'currency' => 'CAD'],
        'CAEN' => ['marketplaceId' => 'EBAY_CA', 'currency' => 'CAD'],
        'US'   => ['marketplaceId' => 'EBAY_US', 'currency' => 'USD'],
        'UK'   => ['marketplaceId' => 'EBAY_UK', 'currency' => 'GBP'],
        'DE'   => ['marketplaceId' => 'EBAY_DE', 'currency' => 'EUR'],
        'FR'   => ['marketplaceId' => 'EBAY_FR', 'currency' => 'EUR'],
    ];
    $mktInfo       = $countryMap[$countryCode] ?? ['marketplaceId' => 'EBAY_CA', 'currency' => 'CAD'];
    $marketplaceId = $mktInfo['marketplaceId'];
    $currency      = !empty($site_setting['Currency']['Currency']) ? $site_setting['Currency']['Currency'] : $mktInfo['currency'];

    $description = iconv('UTF-8', 'UTF-8//IGNORE', !empty($template_data['description']) ? $template_data['description'] : ($template_data['raw_description'] ?? ''));
    // eBay Inventory REST API accepts up to 500,000 chars — no truncation needed
    $offerDataBatch  = []; // array of offer payloads (sku => payload)
    $cardBySku       = []; // sku => card_id for DB updates
    $errors          = [];

    foreach ($template_data['variations'] as $variation) {
        $sku = !empty($variation['inventory_key'])
            ? $variation['inventory_key']
            : substr('CA_CARD_' . preg_replace('/[^A-Za-z0-9]/', '', $variation['card_number'] ?? '') . '_' . $variation['card_id'], 0, 50);
        $cardBySku[$sku] = (int)($variation['card_id'] ?? 0);

        $offerDataBatch[] = [
            'sku'                   => $sku,
            'marketplaceId'         => $marketplaceId,
            'format'                => 'FIXED_PRICE',
            'categoryId'            => (string)$template_data['ebay_category_id'],
            'listingDescription'    => $description,
            'merchantLocationKey'   => $merchantLocationKey,
            'listingPolicies'       => [
                'fulfillmentPolicyId' => $template_data['policies']['fulfillmentPolicyId'],
                'paymentPolicyId'     => $template_data['policies']['paymentPolicyId'],
                'returnPolicyId'      => $template_data['policies']['returnPolicyId'],
            ],
            'pricingSummary' => [
                'price' => [
                    'currency' => $currency,
                    'value'    => number_format((float)$variation['price'], 2, '.', ''),
                ]
            ],
            'quantityLimitPerBuyer' => 1,
        ];
    }

    if (empty($offerDataBatch)) {
        return ['offers' => [], 'errors' => [['message' => 'No variations to create offers for']]];
    }

    // --- Step 2: Bulk create offers (batches of 25) ---
    //$this->log->write('[publishInventoryOffer] bulkCreateOffer count=' . count($offerDataBatch));
    $createResults = $this->doBulkCreateOffer($offerDataBatch, $headers);

    $this->load->model('shopmanager/card/card');
    $offers = []; // successfully created: ['sku', 'card_id', 'offerId', 'status']

    foreach ($createResults as $sku => $res) {
        $card_id = $cardBySku[$sku] ?? 0;
        if (!empty($res['offerId'])) {
            $offers[] = [
                'sku'     => $sku,
                'card_id' => $card_id,
                'offerId' => $res['offerId'],
                'status'  => $res['error'] === null ? 'created' : 'existing',
            ];
            //$this->log->write('[publishInventoryOffer] bulkCreate OK sku=' . $sku . ' offerId=' . $res['offerId']);
        } else {
            $errorMsg = $res['error'] ?? 'create failed HTTP ' . $res['statusCode'];
            $this->log->write('[publishInventoryOffer] bulkCreate FAILED sku=' . $sku . ' ' . $errorMsg);
            $errors[] = ['sku' => $sku, 'httpCode' => $res['statusCode'], 'error' => $errorMsg];
            if ($card_id > 0) {
                $this->model_shopmanager_card_card->updateCardOffer($card_id, '', 0,
                    'Offer creation failed: ' . $errorMsg);
            }
        }
    }

    if (empty($offers)) {
        return ['offers' => [], 'errors' => $errors];
    }

    // --- Step 3: Publish all offers via publishOfferByInventoryItemGroup (variation group) ---
    $groupKey = $template_data['group_key'] ?? ($countryCode . '_CARD_LIST_' . ($template_data['batch_name'] ?? 1) . '_' . ($template_data['listing_id'] ?? '0'));
    //$this->log->write('[publishInventoryOffer] publishByInventoryItemGroup groupKey=' . $groupKey . ' offers=' . count($offers));
    $pubRes = $this->publishByInventoryItemGroup($groupKey, $marketplaceId, $headers);

    $isPublished     = $pubRes['published'] ? 1 : 0;
    $errorStr        = $pubRes['error'] ?? '';
    $firstListingId  = $pubRes['listingId'] ?? null;
    $publishedOffers = [];

    foreach ($offers as $offer) {
        $card_id = $offer['card_id'];
        //$this->log->write('[publishInventoryOffer] marking sku=' . $offer['sku'] . ' offerId=' . $offer['offerId'] . ' published=' . $isPublished);
        if ($card_id > 0) {
            $this->model_shopmanager_card_card->updateCardOffer($card_id, $offer['offerId'], $isPublished, $errorStr);
        }
        $publishedOffers[] = [
            'sku'       => $offer['sku'],
            'offerId'   => $offer['offerId'],
            'status'    => $offer['status'],
            'published' => ['status' => $isPublished ? 'published' : 'failed',
                            'listingId' => $firstListingId, 'error' => $errorStr ?: null],
        ];
    }

    return [
        'offers'    => $offers,
        'published' => $publishedOffers,
        'listingId' => $firstListingId,
        'errors'    => $errors,
    ];
}

private function publishOffer($offerId, $headers, $retryCount = 0): array {
    $url = "https://api.ebay.com/sell/inventory/v1/offer/" . $offerId . "/publish";
    
    //echo "<div style='background: #e1bee7; padding: 10px; margin: 10px 10px 10px 30px; border-left: 4px solid #9c27b0;'>";
    //echo "<h4>📢 Publication de l'offre: {$offerId}";
    //if ($retryCount > 0) {
    //    echo " (Tentative #{$retryCount})";
    //}
    //echo "</h4>";
    //echo "<p>POST " . $url . "</p>";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    
    
    //echo "<p><strong>HTTP Code:</strong> {$httpCode}</p>";
    
    if ($curl_error) {
        //echo "<p style='color: red;'>❌ cURL Error: " . htmlspecialchars($curl_error) . "</p></div>";
          //$this->log->write('eBay Offer Publish Failed (cURL error): ' . $offerId . ' - Error: ' . $curl_error);
        return [
            'offerId' => $offerId,
            'error' => 'cURL error: ' . $curl_error,
            'httpCode' => 0
        ];
    }
    
    $responseData = json_decode($response, true);
    
    //echo "<p><strong>Response:</strong></p><pre style='font-size: 11px;'>" . htmlspecialchars($response) . "</pre>";
    
    if ($httpCode >= 200 && $httpCode < 300) {
          //$this->log->write('eBay Offer Published Successfully: ' . $offerId);
        $result = [
            'offerId' => $offerId,
            'listingId' => $responseData['listingId'] ?? null,
            'status' => 'published'
        ];
        //echo "<p style='color: green;'>✅ Offre publiée avec succès!</p>";
        //if (isset($result['listingId'])) {
        //    echo "<p><strong>Listing ID:</strong> {$result['listingId']}</p>";
        //}
        //echo "</div>";
        return $result;
    } else if ($httpCode == 500 && $retryCount < 2) {
          //$this->log->write('eBay Offer Publish Failed with 500 - Retrying: ' . $offerId . ' - Attempt #' . ($retryCount + 1));
        // Retry pour erreur 500 (max 2 retries)
        //echo "<p style='color: orange;'>⚠️ Erreur 500 - Retry dans 3 secondes...</p></div>";
        sleep(3);
        return $this->publishOffer($offerId, $headers, $retryCount + 1);
    } else {
            //$this->log->write('eBay Offer Publish Failed: ' . $offerId . ' - HTTP: ' . $httpCode . ' - Error: ' . json_encode($responseData));
        $result = [
            'offerId' => $offerId,
            'error' => $responseData,
            'httpCode' => $httpCode
        ];
        //echo "<p style='color: red;'>❌ Échec de la publication après " . ($retryCount + 1) . " tentative(s)</p></div>";
        return $result;
    }
}

     public function refreshAccessToken($refreshToken) {
        $client = new \GuzzleHttp\Client();
        $url = 'https://api.ebay.com/identity/v1/oauth2/token';
    
        // Informations d'identification de l'application
        $clientId = 'CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28'; // Remplacez par votre client_id
        $clientSecret = 'PRD-93ff3ada979d-7fcf-4938-be46-ba89'; // Remplacez par votre client_secret
    
        // En-tête Authorization encodé en base64
        $encodedCredentials = base64_encode($clientId . ':' . $clientSecret);
    
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . $encodedCredentials,
        ];
    
        // Paramètres de la requête pour rafraîchir le jeton d'accès
        $body = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
          //'scope' => 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly https://api.ebay.com/oauth/api_scope/sell.stores https://api.ebay.com/oauth/api_scope/sell.stores.readonly'
            'scope' => 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly https://api.ebay.com/oauth/api_scope/sell.stores https://api.ebay.com/oauth/api_scope/sell.stores.readonly'

                //'scope' => 'https://api.ebay.com/oauth/api_scope/buy.marketplace.insights'

            ];
    $this->log->write('[refreshAccessToken] Refreshing eBay access token with refresh_token=' . $refreshToken . '...');  
        try {
            // Effectuer la requête POST pour renouveler le jeton d'accès
            $response = $client->post($url, [
                'headers' => $headers,
                'form_params' => $body
            ]);
    
            // Analyser la réponse
            $responseArray = json_decode($response->getBody()->getContents(), true);
       // print ("<pre>".print_r ('1451:ebay.php',true )."</pre>");
     // print ("<pre>".print_r ($responseArray,true )."</pre>");
            // Vérifier si le nouveau jeton d'accès est obtenu
            if (isset($responseArray['access_token'])) {
                $responseArray['bearer_token']=$responseArray['access_token'];
                if (isset($responseArray['bearer_token'])) {
                    // Définir le cookie AVANT toute sortie HTML
                    setcookie('bearer_token', $responseArray['bearer_token'], time() + 7200, "/"); // Expire dans 2 heures
                  
                }
              
                return $responseArray;
            } else {
            //echo "Erreur lors de l'obtention du nouveau jeton d'accès.\n";
              //print_r($responseArray);
                return null; 
            }
        } catch (\Exception $e) {
            $this->log->write('eBay OAuth ERREUR: refreshAccessToken a échoué — ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Mettre à jour un listing de cartes déjà publié sur eBay.
     *
     * Même pattern que addCardListing mais orienté mise à jour :
     *  - Upsert chaque inventory_item (titre, description, aspects, images)
     *  - Upsert l'inventory_item_group
     *  - Met à jour les offers existants via PUT /offer/{offerId} pour chaque carte qui a un offer_id
     *  - Re-publie via publishByInventoryItemGroup pour appliquer les changements
     *
     * Ne supprime pas les items existants. Traite tous les batches (published ou non).
     *
     * @param int   $listing_id
     * @param array $site_setting
     * @param mixed $marketplace_account_id
     * @return array
     */
    public function editCardListing(int $listing_id, $site_setting = [], $marketplace_account_id = null): array {

        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/card/card');
        $this->load->model('shopmanager/ebaytemplate');

        if (!$listing_id) {
            return ['errors' => [['message' => 'listing_id manquant']]];
        }

        // 1. Données du listing
        $listing_data = $this->model_shopmanager_card_card_listing->getListing($listing_id);
        if (empty($listing_data)) {
            return ['errors' => [['message' => "Listing #{$listing_id} introuvable."]]];
        }

        // Merge exact duplicates before publishing so no duplicate Customized values reach eBay
        $this->model_shopmanager_card_card_listing->mergeAndDeduplicateCards($listing_id);

        // 2. Descriptions par batch (indexées par batch_name)
        $batchDescriptions = $this->model_shopmanager_card_card_listing->getDescriptions($listing_id);
        if (empty($batchDescriptions)) {
            return ['errors' => [['message' => "Aucun batch configuré pour listing #{$listing_id}."]]];
        }
        $totalBatches = count(array_filter(array_keys($batchDescriptions), fn($k) => $k > 0));

        $countryCode = $site_setting['Currency']['Country'] ?? 'CA';

        // 3. Credentials + headers (même pattern que addCardListing)
        $connectionapi   = $this->getApiCredentials($marketplace_account_id);
        $bearerToken     = $connectionapi['bearer_token'];
        $contentLanguage = !empty($site_setting['Language']) ? str_replace('_', '-', $site_setting['Language']) : 'en-US';
        $headers = [
            "Authorization: Bearer " . $bearerToken,
            "Content-Type: application/json",
            "Content-Language: " . $contentLanguage,
            "Accept: application/json",
        ];

        $countryMap = [
            'CA'   => 'EBAY_CA', 'CAFR' => 'EBAY_CA', 'CAEN' => 'EBAY_CA',
            'US'   => 'EBAY_US', 'UK'   => 'EBAY_UK',
            'DE'   => 'EBAY_DE', 'FR'   => 'EBAY_FR',
        ];
        $marketplaceId = $countryMap[$countryCode] ?? 'EBAY_CA';

        $results = ['batches' => [], 'errors' => []];

        try {
            // 4. Inventory location
            $firstDesc = null;
            foreach ($batchDescriptions as $bid => $bd) {
                if ($bid > 0) { $firstDesc = $bd; break; }
            }
            $locationData = array_merge($listing_data, [
                'title'      => $firstDesc['title']    ?? ($listing_data['title'] ?? ''),
                'specifics'  => $firstDesc['specifics'] ?? [],
                'variations' => [],
            ]);
            $locationTemplate    = $this->model_shopmanager_ebaytemplate->getEbayTemplateCardListing($locationData, $site_setting, $marketplace_account_id);
            $merchantLocationKey = $this->ensureInventoryLocation($locationTemplate, $headers);
            sleep(1);

            // 5b. Traiter les pending deletes AVANT le sync (retire les offres mergées de eBay)
            $this->processPendingDeletes($listing_id, $headers);

            // 5. Boucle sur les batches publiés seulement (avec ebay_item_id)
            // group_key vient directement de $batchDesc (getDescriptions() le retourne via JOIN clb)
            foreach ($batchDescriptions as $batchNumber => $batchDesc) {
                if ($batchNumber === 0) continue;

                // Ignorer les batches sans ebay_item_id (jamais publiés)
                if (empty($batchDesc['ebay_item_id'])) {
                    //$this->log->write("[editCardListing] Batch {$batchNumber} ignoré — pas de ebay_item_id");
                    continue;
                }

                // group_key DOIT venir de oc_card_listing_batch — pas de fallback calculé
                $batchLogicalNum = (int)($batchDesc['batch_name'] ?? 0);
                $storedGroupKey  = $batchDesc['group_key'] ?? null;
                if (empty($storedGroupKey)) {
                    $errMsg = "[editCardListing] Batch {$batchNumber} (logical={$batchLogicalNum}) ERREUR: group_key manquant dans oc_card_listing_batch";
                    $this->log->write($errMsg);
                    $results['errors'][] = $errMsg;
                    $results['batches'][$batchNumber] = ['error' => $errMsg];
                    continue;
                }

                // Cartes du batch — filter par batch_id FK (c.batch_id)
                $batchFkId  = (int)($batchDesc['batch_id'] ?? 0);
                $batchCards = $this->model_shopmanager_card_card->getCards([
                    'filter_listing_id' => $listing_id,
                    'filter_batch_name'   => $batchFkId,
                ]);
                if (empty($batchCards)) {
                    //$this->log->write("[editCardListing] Batch {$batchNumber} (batch_id={$batchFkId}) ignoré — aucune carte trouvée");
                    continue;
                }

                // Construire listing_data du batch
                $batchListingData = array_merge($listing_data, [
                    'title'       => $batchDesc['title']       ?? '',
                    'description' => $batchDesc['description'] ?? '',
                    'specifics'   => $batchDesc['specifics']   ?? [],
                    'variations'  => $batchCards,
                ]);

                // Template eBay
                $batchTemplate = $this->model_shopmanager_ebaytemplate->getEbayTemplateCardListing($batchListingData, $site_setting, $marketplace_account_id);
                $batchTemplate['aspects'] = $this->formatAspects($batchTemplate['aspects']);
                foreach ($batchTemplate['variations'] as $i => $v) {
                    $batchTemplate['variations'][$i]['aspects'] = $this->formatAspects($v['aspects']);
                }

                // group_key vient directement de $batchDesc (plus de $storedBatches)
                $batchTemplate['group_key'] = $storedGroupKey;

                // ── Flow edit : upsert items/group + updateOffer ──
                $batchResult = ['items_updated' => 0, 'group_updated' => false, 'offers_updated' => 0, 'errors' => []];

                // ÉTAPE 1 : Upsert chaque inventory_item (PUT — crée ou met à jour)
                foreach ($batchTemplate['variations'] as $index => $variation) {
                    $itemResult = $this->createInventoryItem($variation, $batchTemplate, $headers, $merchantLocationKey, $index, $site_setting);
                    if (!isset($itemResult['error'])) {
                        $batchResult['items_updated']++;
                    } else {
                        $batchResult['errors'][] = $itemResult['error'];
                        $results['errors'][]     = $itemResult['error'];
                    }
                }

                // ÉTAPE 2 : Upsert inventory_item_group (PUT — titre, description, aspects)
                $groupResult = $this->createInventoryItemGroup($batchTemplate, $headers, $site_setting);
                if (!isset($groupResult['error'])) {
                    $batchResult['group_updated'] = true;
                } else {
                    $batchResult['errors'][] = $groupResult['error'];
                    $results['errors'][]     = $groupResult['error'];
                }

                sleep(1);

                // ÉTAPE 3 : Sync offers des cartes avec to_sync = 1
                // — Cartes avec offer_id existant  → updateOffer
                // — Cartes sans offer_id (nouvelles) → bulkCreateOffer (inventory_item déjà créé en ÉTAPE 1)
                $currencyMap    = ['CA' => 'CAD', 'CAFR' => 'CAD', 'CAEN' => 'CAD', 'US' => 'USD', 'UK' => 'GBP', 'DE' => 'EUR', 'FR' => 'EUR'];
                $batchCurrency  = $currencyMap[$countryCode] ?? 'CAD';
                $cardsToCreate  = []; // sku => ['card_id' => X, 'offerData' => [...]]

                foreach ($batchCards as $card) {
                    $card_id  = (int)($card['card_id'] ?? 0);
                    $offer_id = trim($card['offer_id'] ?? '');
                    if ($card_id <= 0) continue;

                    // Skip cards that don't need sync
                    if (empty($card['to_sync'])) {
                        //$this->log->write("[editCardListing] skipOffer card_id={$card_id} — to_sync=0");
                        continue;
                    }

                    if (!empty($offer_id)) {
                        // A) Mise à jour de l'offer existant
                        $updateResult = $this->updateOffer($offer_id, $marketplace_account_id, $card_id);
                        if (!empty($updateResult['error'])) {
                            $errMsg = (string)$updateResult['error'];
                            $this->log->write("[editCardListing] updateOffer FAILED card_id={$card_id} offer_id={$offer_id} error={$errMsg}");
                            $this->model_shopmanager_card_card->updateCardOffer($card_id, $offer_id, 0, 'Update failed: ' . $errMsg);
                            $batchResult['errors'][] = ['card_id' => $card_id, 'error' => $errMsg];
                            $results['errors'][]     = ['card_id' => $card_id, 'error' => $errMsg];
                        } else {
                            $batchResult['offers_updated']++;
                            //$this->log->write("[editCardListing] updateOffer OK card_id={$card_id} offer_id={$offer_id}");
                            $this->model_shopmanager_card_card->clearCardSyncFlag($card_id);
                        }
                    } else {
                        // B) Pas d'offer_id — l'inventory_item existe déjà (ÉTAPE 1) ; créer l'offer
                        $sku = !empty($card['inventory_key'])
                            ? $card['inventory_key']
                            : substr('CA_CARD_' . preg_replace('/[^A-Za-z0-9]/', '', $card['card_number'] ?? '') . '_' . $card_id, 0, 50);
                        $cardsToCreate[$sku] = [
                            'card_id'   => $card_id,
                            'offerData' => [
                                'sku'                   => $sku,
                                'marketplaceId'         => $marketplaceId,
                                'format'                => 'FIXED_PRICE',
                                'categoryId'            => (string)($listing_data['ebay_category_id'] ?? '183050'),
                                'listingDescription'    => iconv('UTF-8', 'UTF-8//IGNORE', $batchTemplate['description'] ?? ''),
                                'merchantLocationKey'   => $merchantLocationKey,
                                'listingPolicies'       => [
                                    'fulfillmentPolicyId' => $site_setting['CardShippingProfile'] ?? '',
                                    'paymentPolicyId'     => $site_setting['CardPaymentProfile']  ?? '',
                                    'returnPolicyId'      => $site_setting['CardReturnProfile']   ?? '',
                                ],
                                'pricingSummary' => [
                                    'price' => [
                                        'currency' => $batchCurrency,
                                        'value'    => number_format((float)($card['price'] ?? 0), 2, '.', ''),
                                    ],
                                ],
                                'quantityLimitPerBuyer' => 1,
                            ],
                        ];
                        //$this->log->write("[editCardListing] queueCreateOffer card_id={$card_id} sku={$sku}");
                    }
                }

                // Bulk-créer les offers pour les nouvelles cartes (inventory_items déjà upsertés en ÉTAPE 1)
                $newlyCreatedOffers = []; // sku => offerId
                if (!empty($cardsToCreate)) {
                    $offerDataBatch = array_column($cardsToCreate, 'offerData');
                    $createResults  = $this->doBulkCreateOffer($offerDataBatch, $headers);
                    foreach ($createResults as $sku => $createRes) {
                        $cid = $cardsToCreate[$sku]['card_id'];
                        if (!empty($createRes['offerId'])) {
                            $newlyCreatedOffers[$sku] = $createRes['offerId'];
                            $batchResult['offers_updated']++;
                            //$this->log->write("[editCardListing] createOffer OK card_id={$cid} sku={$sku} offerId=" . $createRes['offerId']);
                        } else {
                            $errMsg = $createRes['error'] ?? 'createOffer failed HTTP ' . ($createRes['statusCode'] ?? '?');
                            $this->log->write("[editCardListing] createOffer FAILED card_id={$cid} sku={$sku} error={$errMsg}");
                            $this->model_shopmanager_card_card->updateCardOffer($cid, '', 0, $errMsg);
                            $batchResult['errors'][] = ['card_id' => $cid, 'error' => $errMsg];
                            $results['errors'][]     = ['card_id' => $cid, 'error' => $errMsg];
                        }
                    }
                }

                sleep(1);

                // ÉTAPE 4 : Re-publier le groupe pour appliquer tous les changements
                $pubRes    = $this->publishByInventoryItemGroup($batchTemplate['group_key'], $marketplaceId, $headers);
                $listingId = $pubRes['listingId'] ?? null;

                if ($pubRes['published'] && !empty($listingId)) {
                    $this->model_shopmanager_card_card_listing->updateEbayListingId($listing_id, $listingId, 1, $batchFkId);
                    $this->model_shopmanager_card_card_listing->updateBatchPublishedStatus($listing_id, $batchFkId, 1, date('Y-m-d H:i:s'));
                    $batchResult['ebay_item_id']            = $listingId;
                    $results['ebay_item_ids'][$batchNumber] = $listingId;
                } else {
                    $errMsg = $pubRes['error'] ?? 'publishByInventoryItemGroup failed';
                    $this->log->write("[editCardListing] publish FAILED batch={$batchNumber} error={$errMsg}");
                    $this->model_shopmanager_card_card_listing->updateBatchPublishedStatus($listing_id, $batchFkId, 0);
                    $batchResult['errors'][] = ['batch' => $batchNumber, 'error' => $errMsg];
                    $results['errors'][]     = ['batch' => $batchNumber, 'error' => $errMsg];
                }

                // Sauvegarder les offer_ids des cartes nouvellement créées (résultat de la publication ÉTAPE 4)
                if (!empty($newlyCreatedOffers)) {
                    $isPublished = $pubRes['published'] ? 1 : 0;
                    $pubErrStr   = $pubRes['published'] ? '' : ($pubRes['error'] ?? 'publish failed');
                    foreach ($newlyCreatedOffers as $sku => $offerId) {
                        $cid = $cardsToCreate[$sku]['card_id'];
                        $this->model_shopmanager_card_card->updateCardOffer($cid, $offerId, $isPublished, $pubErrStr);
                        //$this->log->write("[editCardListing] saveNewOffer card_id={$cid} sku={$sku} offerId={$offerId} published={$isPublished}");
                    }
                }

                // Sauvegarder group_key en DB
                $this->model_shopmanager_card_card_listing->saveEbayBatch($listing_id, $batchNumber, [
                    'group_key'       => $batchTemplate['group_key'],
                    'variation_count' => count($batchCards),
                ]);

                $results['batches'][$batchNumber] = $batchResult;
                /*
                $this->log->write(
                    "[editCardListing] Batch {$batchNumber}/{$totalBatches}"
                    . ' cards='          . count($batchCards)
                    . ' items_updated='  . $batchResult['items_updated']
                    . ' group_updated='  . ($batchResult['group_updated'] ? 'true' : 'false')
                    . ' offers_updated=' . $batchResult['offers_updated']
                    . ' ebay_item_id='   . ($listingId ?? 'none')
                    . ' groupKey='       . $batchTemplate['group_key']
                );
                */

                sleep(2);
            }

        } catch (\Exception $e) {
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }


    /**
     * Re-publish all cards in a listing that have an offer_id but published=0.
     * Phase 1 (serial): updateInventoryItemImages + updateOffer per card.
     * Phase 2 (bulk):   bulkPublishOffer in batches of 25.
     *
     * @param int   $listing_id
     * @param mixed $marketplace_account_id
     * @return array  Summary: total, published, failed, details[]
     */
    public function republishCardOffers(int $listing_id, $marketplace_account_id = null): array {
        // Headers with Content-Type + Content-Language (required for PUT offer + POST publish)
        $headers = $this->buildRestHeaders($marketplace_account_id, true, true);

        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/card/card');

        // Reconstruct real eBay SKU (same logic as syncCardOffers / addCardListing)
        $connectionapi = $this->getApiCredentials($marketplace_account_id);
        $siteSetting   = is_array($connectionapi['site_setting'] ?? null)
            ? $connectionapi['site_setting']
            : json_decode($connectionapi['site_setting'] ?? '{}', true);
        $country_code  = $siteSetting['Currency']['Country'] ?? 'CA';

        $cards = $this->model_shopmanager_card_card_listing->getCardsUnpublishedWithOfferRows($listing_id);
        if (empty($cards)) {
            return ['listing_id' => $listing_id, 'total' => 0,
                    'published' => 0, 'failed' => 0, 'details' => []];
        }

        $baseResult = [
            'listing_id' => $listing_id,
            'total'      => count($cards),
            'published'  => 0,
            'failed'     => 0,
            'details'    => [],
        ];

        $published_count = 0;
        $failed_count    = 0;
        $details         = [];
        $readyOffers     = []; // offerId => card_id — ready for bulk publish

        // Phase 1 (serial): updateInventoryItemImages + updateOffer per card
        foreach ($cards as $card) {
            $card_id      = (int)$card['card_id'];
            $offer_id     = $card['offer_id'];
            $sku          = !empty($card['inventory_key'])
                ? $card['inventory_key']
                : substr('CA_CARD_' . preg_replace('/[^A-Za-z0-9]/', '', $card['card_number'] ?? '') . '_' . $card_id, 0, 50);

            //$this->log->write('[republishCardOffers] Preparing card_id=' . $card_id . ' offer_id=' . $offer_id . ' sku=' . $sku . ' (db_sku=' . ($card['sku'] ?? '') . ')');

            // Step 0: push latest image URLs to the inventory_item BEFORE anything else
            if (!empty($sku)) {
                $this->updateInventoryItemImages($sku, $card_id, $marketplace_account_id);
            } else {
                //$this->log->write('[republishCardOffers] WARNING: sku empty for card_id=' . $card_id);
            }

            // Step 1: updateOffer (PUT) to refresh offer data and fix image-mix errors
            $refreshResult = $this->updateOffer($offer_id, $marketplace_account_id, $card_id);
            if (!empty($refreshResult['error'])) {
                $errorMsg = (string)$refreshResult['error'];
                $this->log->write('[republishCardOffers] updateOffer FAILED card_id=' . $card_id . ' error=' . $errorMsg);
                $this->model_shopmanager_card_card->updateCardOffer($card_id, $offer_id, 0, 'Refresh failed: ' . $errorMsg);
                $details[] = ['card_id' => $card_id, 'offer_id' => $offer_id,
                              'result' => 'failed', 'error' => 'Refresh failed: ' . $errorMsg];
                $failed_count++;
                continue;
            }

            $readyOffers[$offer_id] = $card_id;
        }

        // Phase 2: publish via publishOfferByInventoryItemGroup — one call per batch
        if (!empty($readyOffers)) {
            $countryMap = [
                'CA'   => 'EBAY_CA', 'CAFR' => 'EBAY_CA', 'CAEN' => 'EBAY_CA',
                'US'   => 'EBAY_US', 'UK'   => 'EBAY_UK',
                'DE'   => 'EBAY_DE', 'FR'   => 'EBAY_FR',
            ];
            $marketplaceId = $countryMap[$country_code] ?? 'EBAY_CA';

            // Load stored batches — fall back to single-batch if none recorded yet
            $dbBatches = $this->model_shopmanager_card_card_listing->getBatches($listing_id);

            if (empty($dbBatches)) {
                // Aucun batch enregistré — impossible de publier sans group_key
                $this->log->write('[republishCardOffers] ERREUR: aucun batch trouvé pour listing ' . $listing_id . ' — appelez recalculateBatches() d\'abord');
                return array_merge($baseResult, ['errors' => 1, 'details' => []]);
            }

            // Index readyOffers by batch_id FK (card_id → batch_id FK via the cards loaded above)
            $cardBatchMap = []; // card_id => batch_id FK
            foreach ($cards as $card) {
                $cardBatchMap[(int)$card['card_id']] = (int)($card['batch_id'] ?? 0);
            }

            // Group readyOffers by batch_id FK
            $offersByBatch = []; // batch_id FK => [ offer_id => card_id ]
            foreach ($readyOffers as $offer_id => $card_id) {
                $bn = $cardBatchMap[$card_id] ?? 0;
                $offersByBatch[$bn][$offer_id] = $card_id;
            }

            foreach ($dbBatches as $batchRow) {
                $batchFkId   = (int)$batchRow['batch_id'];    // FK for DB calls
                $batchNumber = (int)$batchRow['batch_name'];  // logical — for saveEbayBatch + logs
                $groupKey    = $batchRow['group_key'] ?? null;
                $batchOffers = $offersByBatch[$batchFkId] ?? [];

                if (empty($batchOffers)) {
                    continue;
                }

                // group_key DOIT venir de oc_card_listing_batch — pas de fallback
                if (empty($groupKey)) {
                    $this->log->write('[republishCardOffers] ERREUR batch=' . $batchNumber . ' — group_key manquant dans oc_card_listing_batch');
                    foreach ($batchOffers as $offer_id => $card_id) {
                        $this->model_shopmanager_card_card->updateCardOffer($card_id, $offer_id, 0, 'group_key manquant');
                        $details[] = ['card_id' => $card_id, 'offer_id' => $offer_id, 'result' => 'failed', 'error' => 'group_key manquant', 'batch' => $batchNumber];
                        $failed_count++;
                    }
                    continue;
                }

                //$this->log->write('[republishCardOffers] Phase2 batch=' . $batchNumber . ' groupKey=' . $groupKey . ' offers=' . count($batchOffers));
                $pubRes = $this->publishByInventoryItemGroup($groupKey, $marketplaceId, $headers);

                $isPublished = $pubRes['published'] ? 1 : 0;
                $errorMsg    = $pubRes['error'] ?? '';
                $listingId   = $pubRes['listingId'] ?? '';

                // Update DB status for this batch (saveEbayBatch uses logical name; update calls use FK)
                $this->model_shopmanager_card_card_listing->saveEbayBatch($listing_id, $batchNumber, [
                    'group_key' => $groupKey,
                ]);
                if ($isPublished) {
                    $this->model_shopmanager_card_card_listing->updateEbayListingId($listing_id, $listingId, 1, $batchFkId);
                    $this->model_shopmanager_card_card_listing->updateBatchPublishedStatus($listing_id, $batchFkId, 1, date('Y-m-d H:i:s'));
                } else {
                    $this->model_shopmanager_card_card_listing->updateBatchPublishedStatus($listing_id, $batchFkId, 0);
                }

                foreach ($batchOffers as $offer_id => $card_id) {
                    if ($isPublished) {
                        //$this->log->write('[republishCardOffers] SUCCESS card_id=' . $card_id . ' offer_id=' . $offer_id . ' listingId=' . $listingId);
                        $this->model_shopmanager_card_card->updateCardOffer($card_id, $offer_id, 1, '');
                        $details[] = ['card_id' => $card_id, 'offer_id' => $offer_id,
                                      'result' => 'published', 'listing_id' => $listingId, 'batch' => $batchNumber];
                        $published_count++;
                    } else {
                        $this->log->write('[republishCardOffers] FAILED card_id=' . $card_id . ' offer_id=' . $offer_id . ' error=' . $errorMsg);
                        $this->model_shopmanager_card_card->updateCardOffer($card_id, $offer_id, 0, $errorMsg);
                        $details[] = ['card_id' => $card_id, 'offer_id' => $offer_id,
                                      'result' => 'failed', 'error' => $errorMsg, 'batch' => $batchNumber];
                        $failed_count++;
                    }
                }
            }
        }

        return [
            'listing_id' => $listing_id,
            'total'      => count($cards),
            'published'  => $published_count,
            'failed'     => $failed_count,
            'details'    => $details,
        ];
    }



/**
 * Check if a marketplace supports multi-variation listings
 */
private function supportsVariations($countryCode): bool {
    // Sites qui NE supportent PAS les variations (inventory item groups)
    $noVariationSites = ['CAFR']; // eBay Canada Français
    
    return !in_array($countryCode, $noVariationSites);
}


    /**
     * Sync eBay offer_id + published status for cards in a listing.
     *
     * @param string $mode  'missing' (only cards without offer_id, default)
     *                      'all'     (all cards in the listing)
     *                      'none'    (skip sync entirely)
     */
    public function syncCardOffers(int $listing_id, $marketplace_account_id = null, string $mode = 'missing'): array {
        if ($mode === 'none') {
            return ['listing_id' => $listing_id, 'total' => 0, 'synced' => 0,
                    'not_found' => 0, 'failed' => 0, 'skipped' => true, 'details' => []];
        }

        // Credentials + site settings
        $connectionapi = $this->getApiCredentials($marketplace_account_id);
        $siteSetting   = is_array($connectionapi['site_setting'])
            ? $connectionapi['site_setting']
            : json_decode($connectionapi['site_setting'] ?? '{}', true);
        $country_code  = $siteSetting['Currency']['Country'] ?? 'CA';

        // GET-only headers (no body headers)
        $getHeaders   = $this->buildRestHeaders($marketplace_account_id, false, false);
        // Write headers for POST/PUT (create offer)
        $writeHeaders = $this->buildRestHeaders($marketplace_account_id, true, true);

        // Marketplace + currency map
        $countryMap = [
            'CA'   => ['marketplaceId' => 'EBAY_CA', 'currency' => 'CAD'],
            'CAFR' => ['marketplaceId' => 'EBAY_CA', 'currency' => 'CAD'],
            'US'   => ['marketplaceId' => 'EBAY_US', 'currency' => 'USD'],
            'UK'   => ['marketplaceId' => 'EBAY_UK', 'currency' => 'GBP'],
            'DE'   => ['marketplaceId' => 'EBAY_DE', 'currency' => 'EUR'],
        ];
        $mktInfo       = $countryMap[$country_code] ?? ['marketplaceId' => 'EBAY_CA', 'currency' => 'CAD'];
        $marketplaceId = $mktInfo['marketplaceId'];
        $currency      = $mktInfo['currency'];

        // Load listing meta once (category + description + policies) — used when creating missing offers
        $listingMeta = null;
        $lq = $this->db->query("SELECT `ebay_category_id` FROM `" . DB_PREFIX . "card_listing`
                                WHERE `listing_id` = " . (int)$listing_id . " LIMIT 1");
        if ($lq->num_rows > 0) {
            $dq = $this->db->query(
                "SELECT `description` FROM `" . DB_PREFIX . "card_listing_description`
                 WHERE `listing_id` = " . (int)$listing_id . " AND `language_id` = 1 LIMIT 1"
            );
            $listingMeta = [
                'ebay_category_id' => $lq->row['ebay_category_id'] ?: '183050',
                'description'      => $dq->num_rows > 0 ? $dq->row['description'] : '<p>Trading cards in excellent condition.</p>',
                'policies'         => [
                    'fulfillmentPolicyId' => $siteSetting['CardShippingProfile'] ?? '270305103019',
                    'paymentPolicyId'     => $siteSetting['CardPaymentProfile']  ?? '66471759019',
                    'returnPolicyId'      => $siteSetting['CardReturnProfile']   ?? '261009811019',
                ],
            ];
        }

        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/card/card');

        $cards = ($mode === 'all')
            ? $this->model_shopmanager_card_card_listing->getAllCardRows($listing_id)
            : $this->model_shopmanager_card_card_listing->getCardsWithoutOfferRows($listing_id);
        if (empty($cards)) {
            return ['listing_id' => $listing_id, 'total' => 0,
                    'synced' => 0, 'not_found' => 0, 'failed' => 0, 'details' => []];
        }

        $synced    = 0;
        $not_found = 0;
        $failed    = 0;
        $details   = [];
        $toCreate  = []; // cards needing creation, indexed by sku: ['card_id' => ..., 'offerData' => [...]]

        foreach ($cards as $card) {
            $card_id = (int)$card['card_id'];

            // Lire la clé d'inventaire depuis la DB
            $sku = !empty($card['inventory_key'])
                ? $card['inventory_key']
                : substr('CA_CARD_' . preg_replace('/[^A-Za-z0-9]/', '', $card['card_number'] ?? '') . '_' . $card_id, 0, 50);

            $url    = 'https://api.ebay.com/sell/inventory/v1/offer?sku=' . urlencode($sku);
            $result = $this->makeCurlRestRequest($url, 'GET', $getHeaders);

            if ($result['error']) {
                $errorMsg = 'cURL error: ' . $result['error'];
                $this->model_shopmanager_card_card->updateCardOffer($card_id, '', 0, $errorMsg);
                $details[] = ['card_id' => $card_id, 'sku' => $sku, 'result' => 'failed', 'error' => $errorMsg];
                $failed++;
                continue;
            }

            $data     = $result['body'];
            $httpCode = $result['httpCode'];

            if ($httpCode === 200 && !empty($data['offers'])) {
                $offer     = $data['offers'][0];
                $offerId   = $offer['offerId'] ?? '';
                $status    = $offer['status']  ?? '';
                $published = ($status === 'PUBLISHED') ? 1 : 0;

                $this->model_shopmanager_card_card->updateCardOffer($card_id, $offerId, $published, '');
                $details[] = ['card_id' => $card_id, 'sku' => $sku,
                              'offer_id' => $offerId, 'status' => $status,
                              'published' => $published, 'result' => 'synced'];
                $synced++;

            } elseif ($httpCode === 404 || (isset($data['offers']) && count($data['offers']) === 0)) {
                // Offer does not exist yet — queue for bulk create + publish
                if ($listingMeta !== null) {
                    // Upsert the inventory item first (PUT) — createOffer will fail if item doesn't exist on eBay
                    //$this->log->write('[syncCardOffers] NOT_FOUND sku=' . $sku . ' card_id=' . $card_id . ' — upserting inventory item then queuing for bulk create');
                    $this->putInventoryItemForCard($sku, $card_id, 'default_location', $writeHeaders);

                    $toCreate[$sku] = [
                        'card_id'  => $card_id,
                        'batch_id' => (int)($card['batch_id'] ?? 0),  // FK
                        'offerData' => [
                            'sku'                   => $sku,
                            'marketplaceId'         => $marketplaceId,
                            'format'                => 'FIXED_PRICE',
                            'categoryId'            => (string)$listingMeta['ebay_category_id'],
                            'listingDescription'    => iconv('UTF-8', 'UTF-8//IGNORE', !empty($listingMeta['description']) ? $listingMeta['description'] : ($listingMeta['raw_description'] ?? '')),
                            'merchantLocationKey'   => 'default_location',
                            'listingPolicies'       => $listingMeta['policies'],
                            'pricingSummary'        => [
                                'price' => [
                                    'currency' => $currency,
                                    'value'    => number_format((float)($card['price'] ?? 0), 2, '.', ''),
                                ]
                            ],
                            'quantityLimitPerBuyer' => 1,
                        ],
                    ];
                } else {
                    $details[] = ['card_id' => $card_id, 'sku' => $sku,
                                  'result' => 'not_found', 'status' => 'NOT_FOUND'];
                    $not_found++;
                }

            } else {
                $errorMsg = $data['errors'][0]['message'] ?? ('HTTP ' . $httpCode);
                $this->model_shopmanager_card_card->updateCardOffer($card_id, '', 0, $errorMsg);
                $details[] = ['card_id' => $card_id, 'sku' => $sku, 'result' => 'failed', 'error' => $errorMsg];
                $failed++;
            }
        }

        // Phase 2: Bulk create + bulk publish for all 404 cards collected above
        if (!empty($toCreate)) {
            //$this->log->write('[syncCardOffers] bulkCreate count=' . count($toCreate));
            $offerDataBatch = array_column($toCreate, 'offerData');
            $createResults  = $this->doBulkCreateOffer($offerDataBatch, $writeHeaders);

            $offerIdsToPublish = []; // offerId => sku

            foreach ($createResults as $sku => $createRes) {
                $card_id = $toCreate[$sku]['card_id'];
                if (!empty($createRes['offerId'])) {
                    //$this->log->write('[syncCardOffers] bulkCreate OK sku=' . $sku . ' offerId=' . $createRes['offerId']);
                    $offerIdsToPublish[$createRes['offerId']] = $sku;
                } else {
                    $errorMsg = $createRes['error'] ?? 'create failed HTTP ' . $createRes['statusCode'];
                    $this->log->write('[syncCardOffers] bulkCreate FAILED sku=' . $sku . ' card_id=' . $card_id . ' ' . $errorMsg);
                    $this->model_shopmanager_card_card->updateCardOffer($card_id, '', 0, $errorMsg);
                    $details[] = ['card_id' => $card_id, 'sku' => $sku, 'result' => 'failed', 'error' => $errorMsg];
                    $failed++;
                }
            }

            // Publish created offers per batch — one publishByInventoryItemGroup call per batch
            if (!empty($offerIdsToPublish)) {
                // Load stored batches — erreur si aucun batch enregistré
                $dbBatches = $this->model_shopmanager_card_card_listing->getBatches($listing_id);
                if (empty($dbBatches)) {
                    $this->log->write('[syncCardOffers] ERREUR: aucun batch pour listing ' . $listing_id . ' — appelez recalculateBatches() d\'abord');
                    foreach ($offerIdsToPublish as $offerId => $sku) {
                        $card_id = $toCreate[$sku]['card_id'];
                        $this->model_shopmanager_card_card->updateCardOffer($card_id, '', 0, 'batch manquant — recalculateBatches requis');
                        $details[] = ['card_id' => $card_id, 'sku' => $sku, 'result' => 'failed', 'error' => 'batch manquant'];
                        $failed++;
                    }
                } else {

                // Group offerIdsToPublish by batch_id FK
                $offersByBatch = []; // batch_id FK => [ offerId => sku ]
                foreach ($offerIdsToPublish as $offerId => $sku) {
                    $bn = (int)($toCreate[$sku]['batch_id'] ?? 0);
                    $offersByBatch[$bn][$offerId] = $sku;
                }

                foreach ($dbBatches as $batchRow) {
                    $batchFkId    = (int)$batchRow['batch_id'];    // FK for DB calls
                    $batchNumber  = (int)$batchRow['batch_name'];  // logical — for saveEbayBatch + logs
                    $groupKey     = $batchRow['group_key'] ?? null;
                    $batchOffers  = $offersByBatch[$batchFkId] ?? [];

                    if (empty($batchOffers)) {
                        continue;
                    }

                    // group_key DOIT venir de oc_card_listing_batch — pas de fallback
                    if (empty($groupKey)) {
                        $this->log->write('[syncCardOffers] ERREUR batch=' . $batchNumber . ' — group_key manquant dans oc_card_listing_batch');
                        foreach ($batchOffers as $offerId => $sku) {
                            $card_id = $toCreate[$sku]['card_id'];
                            $this->model_shopmanager_card_card->updateCardOffer($card_id, '', 0, 'group_key manquant');
                            $details[] = ['card_id' => $card_id, 'sku' => $sku, 'result' => 'failed', 'error' => 'group_key manquant', 'batch' => $batchNumber];
                            $failed++;
                        }
                        continue;
                    }

                    //$this->log->write('[syncCardOffers] publishByInventoryItemGroup batch=' . $batchNumber . ' groupKey=' . $groupKey . ' offers=' . count($batchOffers));
                    $pubRes      = $this->publishByInventoryItemGroup($groupKey, $marketplaceId, $writeHeaders);
                    $isPublished = $pubRes['published'] ? 1 : 0;
                    $errorStr    = $pubRes['error'] ?? '';
                    $listingId   = $pubRes['listingId'] ?? '';

                    // Persist ebay_item_id for this batch (saveEbayBatch uses logical name; update calls use FK)
                    $this->model_shopmanager_card_card_listing->saveEbayBatch($listing_id, $batchNumber, [
                        'group_key' => $groupKey,
                    ]);
                    if ($isPublished) {
                        $this->model_shopmanager_card_card_listing->updateEbayListingId($listing_id, $listingId, 1, $batchFkId);
                        $this->model_shopmanager_card_card_listing->updateBatchPublishedStatus($listing_id, $batchFkId, 1, date('Y-m-d H:i:s'));
                    } else {
                        $this->model_shopmanager_card_card_listing->updateBatchPublishedStatus($listing_id, $batchFkId, 0);
                    }

                    foreach ($batchOffers as $offerId => $sku) {
                        $card_id = $toCreate[$sku]['card_id'];
                        //$this->log->write('[syncCardOffers] publishByGroup sku=' . $sku . ' card_id=' . $card_id . ' published=' . $isPublished . ' error=' . $errorStr);
                        $this->model_shopmanager_card_card->updateCardOffer($card_id, $offerId, $isPublished, $errorStr);
                        $details[] = [
                            'card_id'   => $card_id,
                            'sku'       => $sku,
                            'offer_id'  => $offerId,
                            'published' => $isPublished,
                            'result'    => 'created',
                            'listingId' => $listingId,
                            'batch'     => $batchNumber,
                        ];
                        if ($isPublished) {
                            $synced++;
                        } else {
                            $failed++;
                        }
                    }
                }
                } // end else (dbBatches non vide)
            }
        }

        return [
            'listing_id' => $listing_id,
            'total'      => count($cards),
            'synced'     => $synced,
            'not_found'  => $not_found,
            'failed'     => $failed,
            'details'    => $details,
        ];
    }

    private function updateCategoryCount(&$categoryCounts, $item) {
    $this->load->model('shopmanager/condition');
  //"<pre>" . print_r(value: '829:ebay') . "</pre>");
    $category_id = $item['leafCategoryIds'][0];
    $categoryName = $item['categories'][0]['categoryName'];
    $site_setting = (isset($item['listingMarketplaceId']) && $item['listingMarketplaceId']=='EBAY-MOTOR')?100:0;
  //"<pre>" . print_r(value: '834:ebay') . "</pre>");
  //"<pre>" . print_r($site_setting, true) . "</pre>");
    // Initialiser la catégorie si elle n'existe pas encore
    if (!isset($categoryCounts[$category_id])) {
        $categoryCounts[$category_id] = [
            'count' => 0,
            'name'  => $categoryName,
            'site_id'  => $site_setting,
            'conditions' => []  // Ajoutez un espace pour les conditions
        ];
    }

    // Incrémenter le compteur de la catégorie
    $categoryCounts[$category_id]['count']++;

    // Récupérer les informations des conditions pour la catégorie
    //"<pre>" . print_r(value: '1173:ebaY.php') . "</pre>");

    $conditions_info = $this->model_shopmanager_condition->getConditionDetails($category_id);

    // Stocker les valeurs condition_marketplace_item_id qui ont des doublons
    $duplicateTracker = [];
    if(!isset($conditions_info[1])){
  //"<pre>" . print_r($conditions_info, true) . "</pre>");
    }
   //"<pre>" . print_r($conditions_info, true). "</pre>");
    // Parcourir les conditions et associer leur id et ebay_value
    if(isset($conditions_info[1])){
        foreach ($conditions_info[1] as $key => $condition) {
            // Obtenir la valeur condition_marketplace_item_id
            $ebay_value = $condition['condition_marketplace_item_id'];

            // Si cette valeur existe déjà, créer un duplicata de la condition
            if (isset($duplicateTracker[$ebay_value])) {
                // Générer une nouvelle clé pour le duplicata
                $duplicateKey = $key . '_duplicate';
                $categoryCounts[$category_id]['conditions'][$duplicateKey] = [
                    'value' => $ebay_value,
                    'original_key' => $key,  // Stocker la clé originale pour traçabilité si nécessaire
                    'condition_name' => $condition['condition_name'],
                    'category_id' => $condition['category_id']
                ];
            } else {
                // Ajouter la première occurrence de la valeur au tableau
                $categoryCounts[$category_id]['conditions'][$key] = [
                    'value' => $ebay_value,
                    'condition_name' => $condition['condition_name'],
                    'category_id' => $condition['category_id']
                ];

                // Marquer cette valeur comme ayant été vue
                $duplicateTracker[$ebay_value] = true;
            }
        }
    }
}


    /**
     * GET /inventory_item/{sku}, remplace product.imageUrls avec les URLs EPS
     * stockées dans oc_card_image, puis PUT /inventory_item/{sku}.
     * Corrige l'erreur 25014 (mélange self-hosted / EPS).
     *
     * @param string $sku                  SKU eBay de la variation
     * @param int    $card_id              ID de la card dans oc_card
     * @param mixed  $marketplace_account_id
     */
    private function updateInventoryItemImages(string $sku, int $card_id, $marketplace_account_id): void {
        // Charger le modèle card pour récupérer les images
        $this->load->model('shopmanager/card/card');
        $imageUrls = $this->model_shopmanager_card_card->getCardImageUrls($card_id);

        //$this->log->write('[updateInventoryItemImages] START sku=' . $sku . ' card_id=' . $card_id . ' images_from_db=' . count($imageUrls));

        if (empty($imageUrls)) {
            //$this->log->write('[updateInventoryItemImages] NO IMAGES for card_id=' . $card_id . ' sku=' . $sku);
            return;
        }

        // eBay limite à 24 images par item
        $imageUrls = array_values(array_slice($imageUrls, 0, 24));

        $url = 'https://api.ebay.com/sell/inventory/v1/inventory_item/' . urlencode($sku);

        // GET l'inventory_item pour préserver tous les autres champs
        $getHeaders = $this->buildRestHeaders($marketplace_account_id, false, false);
        $getResult  = $this->makeCurlRestRequest($url, 'GET', $getHeaders);

        //$this->log->write('[updateInventoryItemImages] GET HTTP=' . $getResult['httpCode'] . ' sku=' . $sku);

        if ($getResult['error'] || $getResult['httpCode'] !== 200 || empty($getResult['body'])) {
            $this->log->write('[updateInventoryItemImages] GET FAILED: error=' . ($getResult['error'] ?: 'HTTP ' . $getResult['httpCode']) . ' sku=' . $sku);
            return;
        }

        $itemData = $getResult['body'];

        // Remplacer les imageUrls
        $itemData['product']['imageUrls'] = $imageUrls;

        // Whitelist : seuls les champs acceptés par PUT /inventory_item/{sku}
        $allowedKeys = ['availability', 'condition', 'conditionDescriptors', 'product', 'packageWeightAndSize', 'locale'];
        $itemData = array_intersect_key($itemData, array_flip($allowedKeys));

        // PUT avec Content-Type + Content-Language
        $putHeaders = $this->buildRestHeaders($marketplace_account_id, true, true);
        $putResult  = $this->makeCurlRestRequest($url, 'PUT', $putHeaders, $itemData);

        //$this->log->write('[updateInventoryItemImages] PUT HTTP=' . $putResult['httpCode'] . ' sku=' . $sku . ' images=' . count($imageUrls));
        if ($putResult['httpCode'] < 200 || $putResult['httpCode'] >= 300) {
            $putErrorIds = array_column($putResult['body']['errors'] ?? [], 'errorId');
            if (in_array(25019, $putErrorIds)) {
                //$this->log->write('[updateInventoryItemImages] SKIP sku=' . $sku . ' — part of active sale (25019), cannot update images.');
                return;
            }
            $this->log->write('[updateInventoryItemImages] PUT ERROR: ' . json_encode($putResult['body'], JSON_UNESCAPED_UNICODE));
        }
    }


    /**
     * PUT (upsert) a minimal inventory item on eBay for a card that doesn't exist yet.
     * Used by syncCardOffers before bulkCreateOffer, since an offer cannot be created
     * for a SKU that has no inventory item on eBay.
     *
     * @param string $sku
     * @param int    $card_id
     * @param string $merchantLocationKey
     * @param array  $headers   Write headers (Content-Type + Content-Language)
     */
    private function putInventoryItemForCard(string $sku, int $card_id, string $merchantLocationKey, array $headers): void {
        $this->load->model('shopmanager/card/card');

        // Fetch the card's title + a first image from DB
        $cardRow = $this->model_shopmanager_card_card->getCard($card_id);
        $rawTitle = $cardRow['title'] ?? ($cardRow['player_name'] ?? 'Trading Card');
        $title    = substr(html_entity_decode($rawTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 0, 80);

        $images = $this->model_shopmanager_card_card->getCardImageUrls($card_id);
        if (empty($images)) {
            $images = ['https://i.ebayimg.com/images/g/placeholder.jpg'];
        }
        $images = array_values(array_slice($images, 0, 24));

        $payload = [
            'availability' => [
                'shipToLocationAvailability' => [
                    'quantity'           => 1,
                    'merchantLocationKey' => $merchantLocationKey,
                ],
            ],
            'condition'            => 'USED_VERY_GOOD',
            'conditionDescriptors' => [
                ['name' => '40001', 'values' => ['400011']],
            ],
            'product' => [
                'title'       => $title,
                'description' => $title,
                'imageUrls'   => $images,
            ],
        ];

        $url    = 'https://api.ebay.com/sell/inventory/v1/inventory_item/' . urlencode($sku);
        $result = $this->makeCurlRestRequest($url, 'PUT', $headers, $payload);

        //$this->log->write('[putInventoryItemForCard] card_id=' . $card_id . ' sku=' . $sku . ' HTTP=' . ($result['httpCode'] ?? 0));
        if (($result['httpCode'] ?? 0) < 200 || ($result['httpCode'] ?? 0) >= 300) {
            $this->log->write('[putInventoryItemForCard] ERROR card_id=' . $card_id . ' ' . json_encode($result['body'] ?? [], JSON_UNESCAPED_UNICODE));
        }
    }


private function updateInventoryOffers($template_data, $existing_listing_id, $site_setting, $headers, $merchantLocationKey): array {
    //$this->log->write('UPDATE INVENTORY OFFERS: Start for listing ' . $existing_listing_id);
    
    $marketplaceId = 'EBAY_CA';
    $currency = 'CAD';
    $countryCode = $site_setting['Currency']['Country'] ?? 'CA';

    if (!empty($site_setting['Currency']['Country'])) {
        $countryMap = [
            'CA' => 'EBAY_CA',
            'CAFR' => 'EBAY_CA',
            'CAEN' => 'EBAY_CA',
            'US' => 'EBAY_US',
            'UK' => 'EBAY_UK',
            'DE' => 'EBAY_DE',
            'FR' => 'EBAY_FR'
        ];
        $marketplaceId = $countryMap[$site_setting['Currency']['Country']] ?? 'EBAY_CA';
    }

    if (!empty($site_setting['Currency']['Currency'])) {
        $currency = $site_setting['Currency']['Currency'];
    }
    
    //$this->log->write('UPDATE INVENTORY OFFERS: Marketplace = ' . $marketplaceId . ', Currency = ' . $currency);

    $description = iconv('UTF-8', 'UTF-8//IGNORE', !empty($template_data['description']) ? $template_data['description'] : ($template_data['raw_description'] ?? ''));
    // eBay Inventory REST API accepts up to 500,000 chars — no truncation needed

    // Get existing offers for this listing
    //$this->log->write('UPDATE INVENTORY OFFERS: Calling getOffersForListing()');
    $existingOffers = $this->getOffersForListing($existing_listing_id, $headers);

    if (isset($existingOffers['error'])) {
        //$this->log->write('UPDATE INVENTORY OFFERS: ERROR - ' . $existingOffers['error']);
        
        // Check if we need to use alternative method with groupKey
        if (isset($existingOffers['needs_groupkey']) && $existingOffers['needs_groupkey']) {
            //$this->log->write('UPDATE INVENTORY OFFERS: Switching to groupKey-based method');
            
            // Build the groupKey (same format as createInventoryItemGroup)
            $groupKey = $template_data['group_key'];
            //$this->log->write('UPDATE INVENTORY OFFERS: Using groupKey: ' . $groupKey);
            
            // Try alternative method with groupKey
            $getHeaders = [];
            foreach ($headers as $header) {
                if (strpos($header, 'Content-Type:') === false) {
                    $getHeaders[] = $header;
                }
            }
            
            $existingOffers = $this->getOffersFromInventoryGroup($groupKey, $getHeaders);
            
            if (isset($existingOffers['error'])) {
                //$this->log->write('UPDATE INVENTORY OFFERS: Alternative method also failed - ' . $existingOffers['error']);
                return ['error' => $existingOffers['error']];
            }
        } else {
            return ['error' => $existingOffers['error']];
        }
    }
    
    //$this->log->write('UPDATE INVENTORY OFFERS: Found ' . count($existingOffers) . ' existing offers');

    $updatedOffers = [];
    $errors = [];

    // Update each existing offer
    foreach ($template_data['variations'] as $variation) {
        // Nettoyer le card_number pour enlever les caractères spéciaux
        $clean_card_number = preg_replace('/[^A-Za-z0-9]/', '', $variation['card_number']);
        
        $sku = $site_setting['Currency']['Country'] . '_CARD_' .$clean_card_number.'_'. $variation['card_id'];
        $sku = substr($sku, 0, 50);
        
        //$this->log->write('UPDATE INVENTORY OFFERS: Processing SKU: ' . $sku);

        // Find existing offer for this SKU
        $existingOffer = null;
        foreach ($existingOffers as $offer) {
            if ($offer['sku'] === $sku) {
                $existingOffer = $offer;
                break;
            }
        }

        if (!$existingOffer) {
            //$this->log->write('UPDATE INVENTORY OFFERS: No existing offer found for SKU ' . $sku);
            $errors[] = [
                'sku' => $sku,
                'error' => 'No existing offer found for this SKU'
            ];
            continue;
        }
        
        //$this->log->write('UPDATE INVENTORY OFFERS: Found offer ID ' . $existingOffer['offerId'] . ' for SKU ' . $sku);

        $offerData = [
            "sku" => $sku,
            "marketplaceId" => $marketplaceId,
            "format" => "FIXED_PRICE",
            "categoryId" => (string)$template_data['ebay_category_id'],
            "listingDescription" => $description,
            "merchantLocationKey" => $merchantLocationKey,
            "listingPolicies" => [
                "fulfillmentPolicyId" => $template_data['policies']['fulfillmentPolicyId'],
                "paymentPolicyId" => $template_data['policies']['paymentPolicyId'],
                "returnPolicyId" => $template_data['policies']['returnPolicyId'],
            ],
            "pricingSummary" => [
                "price" => [
                    "currency" => $currency,
                    "value" => number_format((float)$variation['price'], 2, '.', '')
                ]
            ],
            "quantityLimitPerBuyer" => 1
        ];

        $url = "https://api.ebay.com/sell/inventory/v1/offer/" . $existingOffer['offerId'];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($offerData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $responseData = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            $updatedOffers[] = [
                'sku' => $sku,
                'offerId' => $existingOffer['offerId'],
                'status' => 'updated'
            ];
        } else {
            $errors[] = [
                'sku' => $sku,
                'offerId' => $existingOffer['offerId'],
                'httpCode' => $httpCode,
                'error' => $responseData
            ];
        }
    }

    $result = [
        'updated' => $updatedOffers,
        'errors' => $errors
    ];

    return $result;
}


    /**
     * GET current offer data then PUT it back via eBay updateOffer (PUT /offer/{offerId}).
     * If $card_id > 0, also updates product.imageUrls on the linked inventory_item
     * with the latest EPS URLs from oc_card_image (fixes error 25014).
     * Mirrors the eBay Inventory API method name: updateOffer.
     *
     * @param string $offerId             eBay offer ID
     * @param mixed  $marketplace_account_id
     * @param int    $card_id             (optional) card_id for image update
     */
    private function updateOffer(string $offerId, $marketplace_account_id, int $card_id = 0): array {
        $url = 'https://api.ebay.com/sell/inventory/v1/offer/' . urlencode($offerId);

        // GET — no Content-Type, no Content-Language
        $getHeaders = $this->buildRestHeaders($marketplace_account_id, false, false);
        $getResult  = $this->makeCurlRestRequest($url, 'GET', $getHeaders);

        //$this->log->write('updateOffer getOffer: offerId=' . $offerId . ' HTTP=' . $getResult['httpCode']);

        if ($getResult['error']) {
            return ['error' => 'GET error: ' . $getResult['error']];
        }
        if ($getResult['httpCode'] !== 200) {
            return ['error' => $getResult['body']['errors'][0]['message'] ?? 'GET HTTP ' . $getResult['httpCode']];
        }

        $offerData = $getResult['body'];
        if (empty($offerData)) {
            return ['error' => 'GET returned empty body'];
        }

        // Whitelist: only fields accepted by PUT /offer/{offerId}
        $allowedKeys = [
            'sku', 'marketplaceId', 'format', 'categoryId', 'secondaryCategory',
            'listingDescription', 'listingDuration', 'merchantLocationKey',
            'listingPolicies', 'pricingSummary', 'quantityLimitPerBuyer',
            'storeCategoryNames', 'charity', 'includeCatalogProductDetails',
            'extendedProducerResponsibility', 'regulatory',
        ];
        $offerData = array_intersect_key($offerData, array_flip($allowedKeys));

        // Update inventory_item imageUrls with latest EPS URLs before publishing
        if ($card_id > 0 && !empty($offerData['sku'])) {
            $this->updateInventoryItemImages($offerData['sku'], $card_id, $marketplace_account_id);
        }

        // PUT — with Content-Type + Content-Language (required by eBay)
        $putHeaders = $this->buildRestHeaders($marketplace_account_id, true, true);
        $putResult  = $this->makeCurlRestRequest($url, 'PUT', $putHeaders, $offerData);

        //$this->log->write('updateOffer HTTP=' . $putResult['httpCode']);
        //$this->log->write('updateOffer response: ' . print_r($putResult['body'], true));

        if ($putResult['error']) {
            return ['error' => 'PUT error: ' . $putResult['error']];
        }
        if ($putResult['httpCode'] >= 200 && $putResult['httpCode'] < 300) {
            return ['status' => 'updated'];
        }

        // Error 25019: listing is part of an active sale — price field blocked.
        // Retry without pricingSummary.
        $putErrorIds = array_column($putResult['body']['errors'] ?? [], 'errorId');
        if (in_array(25019, $putErrorIds)) {
            //$this->log->write('updateOffer 25019 (sale active) offer=' . $offerId . ' — retrying without pricingSummary');
            unset($offerData['pricingSummary']);
            $putResult2 = $this->makeCurlRestRequest($url, 'PUT', $putHeaders, $offerData);
            if ($putResult2['httpCode'] >= 200 && $putResult2['httpCode'] < 300) {
                return ['status' => 'updated_no_price'];
            }
            // Still failing — skip
            //$this->log->write('updateOffer still failing after removing pricingSummary offer=' . $offerId . ' HTTP=' . $putResult2['httpCode']);
            return ['status' => 'skipped_sale_active'];
        }

        return ['error' => $putResult['body']['errors'][0]['message'] ?? 'PUT HTTP ' . $putResult['httpCode']];
    }



private function updatePriceVariant(&$pricevariant, $item) {
    $titleLower = strtolower($item['title']);

    // Ne pas traiter si le titre contient "only"
    if (stripos($titleLower, 'only') !== false) {
        return;
    }

    if (!isset($item['condition'])) {
        return; 
    }
if(!isset($item['conditionId'])){
    $condition_id = 'none';
}else{
    $condition_id = $item['conditionId'];
}
   
    if ($condition_id === 'none') {
        // Utiliser le prix actuel (déjà réduit si marketingPrice existe)
        $priceValue = (float)$item['price']['value'];
        $shippingCost = isset($item['shippingOptions']['shippingCost']['value'])
            ? (float)$item['shippingOptions']['shippingCost']['value']
            : 0;

        // Store raw price in the item's original currency (USD from eBay Browse API)
        $totalPrice = $priceValue + $shippingCost;

        if ($totalPrice >= 1.00) {
            $pricevariant[] = [
                'price' => number_format($totalPrice, 2, '.', ''),
                'currency' => $item['price']['currency'] ?? 'USD',
                'marketplace_item_id' => $item['itemId'],
                'title' => $item['title'],
                'url' => "https://www.ebay.com/itm/" . $item['itemId'],
                'condition_name' => $item['condition'] ?? ''
            ];
        }

        // Trier les résultats par prix
        usort($pricevariant, function ($a, $b) {
            return $a['price'] <=> $b['price'];
        });

        return;
    }

    // Sinon, logique existante pour trouver le plus petit prix
    if (isset($pricevariant[$condition_id]) && isset($item['price']['currency'])) {
        $condition_name = $item['condition'] ?? $item['condition_name'];

        // Utiliser le prix actuel (déjà réduit si marketingPrice existe)
        $priceValue = (float)$item['price']['value'];
      
        // Récupérer le coût d'expédition (0 si non disponible)
        $shippingCost = isset($item['shippingOptions'][0]['shippingCost']['value']) 
            ? (float)$item['shippingOptions'][0]['shippingCost']['value'] 
            : 0;

        // Store raw price in the item's original currency (USD from eBay Browse API)
        // Do NOT multiply by OC currency value here — conversion to CAD happens at display time via currency->convert()
        $totalPrice = $priceValue + $shippingCost;

        if ($pricevariant[$condition_id]['price'] > $totalPrice && $totalPrice >= 1.00) {
            $pricevariant[$condition_id]['price'] = number_format($totalPrice, 2, '.', '');
            $pricevariant[$condition_id]['currency'] = $item['price']['currency'] ?? 'USD';
            $pricevariant[$condition_id]['marketplace_item_id'] = $item['itemId'] ?? '';
            $pricevariant[$condition_id]['title'] = $item['title'] ?? '';
            $pricevariant[$condition_id]['url'] = isset($item['itemId']) ? "https://www.ebay.com/itm/" . $item['itemId'] : '';
            $pricevariant[$condition_id]['condition_name'] = $condition_name;
            $pricevariant[$condition_id]['is_calculated'] = false; // Prix réel de l'API
            $pricevariant[$condition_id]['shipping_type'] = $item['shippingOptions'][0]['shippingCostType'] ?? 'UNKNOWN';
        }
    }
}


/**
 * Upload une image sur eBay Picture Services depuis une URL externe
 * Utilise l'API Commerce Media createImageFromUrl
 * https://developer.ebay.com/api-docs/commerce/media/resources/image/methods/createImageFromUrl
 */
private function uploadImageToEbay($imageUrl, $headers): array {
    // L'endpoint correct selon la doc eBay
    $url = "https://apim.ebay.com/commerce/media/v1_beta/image/create_image_from_url";
    
    //echo "<div style='background: #e8f4f8; padding: 10px; margin: 5px; border-left: 4px solid #2196F3;'>";
    //echo "🔄 <strong>Upload image to eBay:</strong> " . htmlspecialchars($imageUrl) . "<br>";
    
    // Payload JSON avec l'URL de l'image - eBay télécharge directement
    $imageData = [
        "imageUrl" => $imageUrl
    ];
    
    $jsonPayload = json_encode($imageData, JSON_UNESCAPED_SLASHES);
    
    //echo "📤 POST " . $url . "<br>";
    //echo "📦 Payload: " . $jsonPayload . "<br>";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true); // Capturer les headers
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $curl_error = curl_error($ch);
    
    
    //echo "📊 HTTP Code: {$http_code}<br>";
    
    if ($curl_error) {
        //echo "❌ cURL Error: " . htmlspecialchars($curl_error) . "<br></div>";
        return ['success' => false, 'error' => 'cURL error: ' . $curl_error];
    }
    
    // Séparer headers et body
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    $result = json_decode($body, true);
   //this->log->write('eBay uploadImageToEbay: HTTP ' . $http_code . ' for imageUrl=' . $imageUrl);
   //$this->log->write('eBay uploadImageToEbay: Response body=' . $body);
   //this->log->write('eBay uploadImageToEbay: Response header=' . $header);
   //this->log->write('eBay uploadImageToEbay: cURL error=' . $curl_error);//
//$this->log->write('eBay uploadImageToEbay: Result=' . json_encode($result));
    //echo "📋 Response Headers:<br><pre style='font-size:10px;'>" . htmlspecialchars($header) . "</pre>";
    //echo "📄 Response Body: <pre style='font-size:10px;'>" . htmlspecialchars($body) . "</pre>";
    
    // HTTP 201 = image créée avec succès
    if ($http_code == 201 || $http_code == 200) {
        // Parser le body JSON - eBay retourne directement imageUrl
        if (isset($result['imageUrl'])) {
            // Transformer l'URL eBay au format optimal
            // De: https://i.ebayimg.com/00/s/MTExOFg4MjE=/z/nGAAAeSwgB5pk8Zc/$_1.JPG
            // À:  https://i.ebayimg.com/images/g/nGAAAeSwgB5pk8Zc/s-l1600.webp
            $ebay_url = $result['imageUrl'];
            
            // Extraire l'identifiant d'image (après /z/)
            if (preg_match('#/z/([^/]+)/#', $ebay_url, $matches)) {
                $image_id = $matches[1];
                // Reconstruire l'URL au format optimal
                $ebay_url = 'https://i.ebayimg.com/images/g/' . $image_id . '/s-l1600.webp';
            } else {
                // Fallback: nettoyer juste les query params si le pattern ne match pas
                $ebay_url = preg_replace('/\?.*$/', '', $ebay_url);
            }
            
            //$this->log->write('eBay uploadImageToEbay: Image uploaded, imageUrl=' . $ebay_url);
            
            // Utiliser directement imageUrl du body (transformée)
            return ['success' => true, 'ebay_url' => $ebay_url];
        } else {
            return ['success' => false, 'error' => 'imageUrl not found in response body'];
        }
    } else {
        //echo "❌ Upload failed with HTTP {$http_code}<br></div>";
        return [
            'success' => false, 
            'error' => $result,
            'http_code' => $http_code,
            'response' => $body
        ];
    }
}

    // ─────────────────────────────────────────────────────────────────────────
    // LOT LISTING METHODS (Trading API – single fixed-price item, qty=1)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Publie un lot de cartes sur eBay (Trading API AddItem, quantité 1).
     * Génère titre, description, mosaïques, puis appelle AddItem.
     *
     * @param int   $listing_id
     * @param array $site_setting        (Language, Currency, Location, …)
     * @param int   $marketplace_account_id
     * @return array ['success', 'ebay_item_id', 'error']
     */
    public function publishLotListing(int $listing_id, array $site_setting = [], int $marketplace_account_id = 1): array {
        $this->load->model('shopmanager/card/card_listing');

        $lot = $this->model_shopmanager_card_card_listing;

        // ── Prix ──────────────────────────────────────────────────────────────
        $lotInfo  = $lot->getLotInfo($listing_id);
        // Utiliser prix manual s'il existe, sinon calculé
        if (!empty($lotInfo['lot_price'])) {
            $price = (float)$lotInfo['lot_price'];
        } else {
            $summary = $lot->getLotPriceSummary($listing_id);
            $price   = $summary['calculated_price'];
        }

        if ($price <= 0) {
            return ['success' => false, 'error' => 'Lot price is zero or negative'];
        }

        // ── Titre + description ───────────────────────────────────────────────
        $title       = $lot->generateLotTitle($listing_id);
        $description = $lot->generateLotDescription($listing_id);

        // ── Catégorie eBay ────────────────────────────────────────────────────
        $listingRow = $this->db->query(
            "SELECT ebay_category_id, condition_id FROM `" . DB_PREFIX . "card_listing`
             WHERE listing_id = " . (int)$listing_id
        )->row;

        $ebay_category_id = $listingRow['ebay_category_id'] ?? '212';  // 212 = Lots - Mixed Lots (Sports trading cards)
        $condition_id     = '3000'; // Used — standard for card lots

        // ── Images (mosaïques) ────────────────────────────────────────────────
        // Générer les mosaïques en local, convertir en URL publiques pour eBay
        $mosaicFiles = $lot->generateMosaicImages($listing_id);
        $pictureXml  = '';
        if (!empty($mosaicFiles)) {
            $pictureXml = '<PictureDetails>';
            foreach (array_slice($mosaicFiles, 0, 12) as $filePath) {
                // Convertir chemin absolu → URL publique
                $relPath = str_replace(DIR_IMAGE, 'image/', $filePath);
                $pubUrl  = rtrim(HTTP_SERVER, '/') . '/' . ltrim($relPath, '/');
                $pictureXml .= '<PictureURL>' . htmlspecialchars($pubUrl) . '</PictureURL>';
            }
            $pictureXml .= '</PictureDetails>';
        }

        // ── Dispatch ──────────────────────────────────────────────────────────
        $language = $site_setting['Language'] ?? 'en_US';
        $currencyBlock = $this->getCurrency(null, $price, $site_setting);
        $locationBlock = $this->getLocation(null, $site_setting);

        // ── Weight & dimensions → eBay units (lbs/oz + inches) ───────────────
        $shippingPkgXml = '';
        $rawWeight      = (float)($lotInfo['lot_weight'] ?? 0);
        $weightClass    = (int)($lotInfo['lot_weight_class_id'] ?? 5);
        $rawLength      = (float)($lotInfo['lot_length'] ?? 0);
        $rawWidth       = (float)($lotInfo['lot_width'] ?? 0);
        $rawHeight      = (float)($lotInfo['lot_height'] ?? 0);
        $lengthClass    = (int)($lotInfo['lot_length_class_id'] ?? 3);

        if ($rawWeight > 0 || $rawLength > 0) {
            // Convert weight to lbs (WeightMajor) + oz (WeightMinor)
            $weightLbs = $rawWeight;
            switch ($weightClass) {
                case 2: $weightLbs = $rawWeight / 1000 * 2.20462; break; // g → lbs
                case 1: $weightLbs = $rawWeight * 2.20462;         break; // kg → lbs
                case 6: $weightLbs = $rawWeight / 16;              break; // oz → lbs
                // 5 = lb default
            }
            $wMajor = (int)floor($weightLbs);
            $wMinor = (int)round(($weightLbs - $wMajor) * 16);

            // Convert dimensions to inches
            $toIn = function (float $v, int $cls): float {
                switch ($cls) {
                    case 1: return round($v / 2.54, 2);   // cm → in
                    case 2: return round($v / 25.4, 2);   // mm → in
                    default: return round($v, 2);          // in
                }
            };
            $lenIn = $toIn($rawLength, $lengthClass);
            $wdIn  = $toIn($rawWidth,  $lengthClass);
            $htIn  = $toIn($rawHeight, $lengthClass);

            $shippingPkgXml = '<ShippingPackageDetails>';
            if ($rawWeight > 0) {
                $shippingPkgXml .= '<WeightMajor unit="lbs">' . $wMajor . '</WeightMajor>';
                $shippingPkgXml .= '<WeightMinor unit="oz">'  . $wMinor . '</WeightMinor>';
            }
            if ($rawLength > 0) $shippingPkgXml .= '<PackageLength unit="in">' . $lenIn . '</PackageLength>';
            if ($rawWidth  > 0) $shippingPkgXml .= '<PackageWidth unit="in">'  . $wdIn  . '</PackageWidth>';
            if ($rawHeight > 0) $shippingPkgXml .= '<PackageDepth unit="in">'  . $htIn  . '</PackageDepth>';
            $shippingPkgXml .= '</ShippingPackageDetails>';
        }

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<AddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
    <ErrorLanguage>' . $language . '</ErrorLanguage>
    <WarningLevel>High</WarningLevel>
    <Item>
        <Title><![CDATA[' . $title . ']]></Title>
        <Description><![CDATA[' . $description . ']]></Description>
        <PrimaryCategory>
            <CategoryID>' . htmlspecialchars($ebay_category_id) . '</CategoryID>
        </PrimaryCategory>
        <ConditionID>' . $condition_id . '</ConditionID>
        ' . $currencyBlock . '
        <Quantity>1</Quantity>
        <ListingType>FixedPriceItem</ListingType>
        <ListingDuration>GTC</ListingDuration>
        ' . $locationBlock . '
        <DispatchTimeMax>3</DispatchTimeMax>
        ' . $shippingPkgXml . '
        ' . $pictureXml . '
    </Item>
</AddItemRequest>';

        $headers  = $this->buildEbayHeaders('AddItem', 1371, $marketplace_account_id);
        $response = $this->makeCurlRequest('https://api.ebay.com/ws/api.dll', $headers, $xml);

        if (!$response) {
            return ['success' => false, 'error' => 'No response from eBay API'];
        }

        $responseXml = simplexml_load_string($response);
        if (!$responseXml) {
            return ['success' => false, 'error' => 'Failed to parse eBay response XML'];
        }

        $resp = json_decode(json_encode($responseXml), true);

        if (isset($resp['Ack']) && in_array($resp['Ack'], ['Success', 'Warning'])) {
            $ebay_item_id = $resp['ItemID'] ?? '';
            // Sauvegarder en DB
            $lot->saveLotInfo($listing_id, $ebay_item_id, $price);
            return ['success' => true, 'ebay_item_id' => $ebay_item_id];
        }

        return ['success' => false, 'error' => json_encode($resp)];
    }

    /**
     * Termine une annonce lot sur eBay (Trading API EndItem).
     *
     * @param int   $listing_id
     * @param array $site_setting
     * @param int   $marketplace_account_id
     * @return array ['success', 'error']
     */
    public function endLotListing(int $listing_id, array $site_setting = [], int $marketplace_account_id = 1): array {
        $this->load->model('shopmanager/card/card_listing');
        $lot     = $this->model_shopmanager_card_card_listing;
        $lotInfo = $lot->getLotInfo($listing_id);

        $ebay_item_id = $lotInfo['lot_ebay_item_id'] ?? '';
        if (empty($ebay_item_id)) {
            return ['success' => false, 'error' => 'No eBay Item ID found for this lot'];
        }

        // Réutilise la logique existante de endCardListing
        $result = $this->endCardListing($ebay_item_id, $marketplace_account_id, $site_setting, $listing_id);

        if (!empty($result['success'])) {
            $lot->clearLotInfo($listing_id);
        }

        return $result;
    }

    /**
     * Get minimum price from item variations (if available)
     * For multi-variant listings, retrieve detailed item data and find lowest price
     * 
     * @param string $itemId eBay legacy item ID
     * @param array $options Options array (site_id, marketplace_account_id)
     * @return array Returns ['price' => float, 'currency' => string] or empty array if no variations
     */
    public function getItemVariationsPrices(string $itemId, array $options = []): array {
        if (empty($itemId)) {
            return [];
        }

        $marketplaceAccountId = (int)($options['marketplace_account_id'] ?? 1);
        $siteId = (int)($options['site_id'] ?? 0);

        // Build URL for Browse API item endpoint
        $url = 'https://api.ebay.com/buy/browse/v1/item/v1|' . urlencode($itemId) . '|0';

        $headers = $this->getBrowseHeaders($marketplaceAccountId, $siteId);

        $response = $this->makeCurlRequest($url, $headers);
        if (!$response) {
            return [];
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return [];
        }

        // Check for errors
        if (isset($data['errors'])) {
            return [];
        }

        // Look for variations in the response
        $variations = $data['variations'] ?? [];
        if (empty($variations)) {
            return [];
        }

        $minPrice = null;
        $minCurrency = 'USD';

        foreach ($variations as $variation) {
            $varPrice = (float)($variation['price']['value'] ?? 0);
            if ($varPrice <= 0) {
                continue;
            }

            $varCurrency = $variation['price']['currency'] ?? 'USD';

            if ($minPrice === null || $varPrice < $minPrice) {
                $minPrice = $varPrice;
                $minCurrency = $varCurrency;
            }
        }

        if ($minPrice === null || $minPrice <= 0) {
            return [];
        }

        return [
            'price' => $minPrice,
            'currency' => $minCurrency,
        ];
    }

    public function getOwnListingPriceSummary(string $itemId, array $options = []): array {
        if ($itemId === '') {
            return [];
        }

        $marketplaceAccountId = (int)($options['marketplace_account_id'] ?? 1);
        $countryCode = (string)($options['country_code'] ?? 'US');
        $zipCode = (string)($options['zip_code'] ?? '12919');
        $product = $this->getProduct($itemId, 1, $zipCode, $countryCode, $marketplaceAccountId);

        if (!is_array($product) || !empty($product['error'])) {
            return [];
        }

        $extractMoney = static function($value): ?float {
            if (is_array($value)) {
                if (isset($value['value'])) {
                    return (float)$value['value'];
                }

                if (isset($value['convertedFromValue'])) {
                    return (float)$value['convertedFromValue'];
                }

                return null;
            }

            if ($value === null || $value === '') {
                return null;
            }

            return (float)$value;
        };

        $extractCurrency = static function(array $node, string $fallback = 'USD'): string {
            if (isset($node['price']['currency'])) {
                return (string)$node['price']['currency'];
            }

            if (isset($node['currentBidPrice']['currency'])) {
                return (string)$node['currentBidPrice']['currency'];
            }

            if (isset($node['discountPriceInfo']['originalRetailPrice']['currency'])) {
                return (string)$node['discountPriceInfo']['originalRetailPrice']['currency'];
            }

            return $fallback;
        };

        $extractOriginalPrice = static function(array $node) use ($extractMoney): ?float {
            $candidates = [
                $node['discountPriceInfo']['originalRetailPrice'] ?? null,
                $node['discountPriceInfo']['originalRetailPrice']['value'] ?? null,
                $node['marketingPrice']['originalPrice']['value'] ?? null,
                $node['marketingPrice']['originalPrice'] ?? null,
                $node['strikethroughPrice']['value'] ?? null,
                $node['strikethroughPrice'] ?? null,
            ];

            foreach ($candidates as $candidate) {
                $value = $extractMoney($candidate);

                if ($value !== null && $value > 0) {
                    return $value;
                }
            }

            return null;
        };

        $extractCurrentPrice = static function(array $node, bool $isAuction) use ($extractMoney): ?float {
            if ($isAuction) {
                $bidValue = $extractMoney($node['currentBidPrice'] ?? null);
                if ($bidValue !== null && $bidValue > 0) {
                    return $bidValue;
                }
            }

            $priceValue = $extractMoney($node['price'] ?? null);
            if ($priceValue !== null && $priceValue > 0) {
                return $priceValue;
            }

            return null;
        };

        $buildSummary = static function(array $node, bool $isAuction, string $fallbackCondition = '') use ($extractCurrentPrice, $extractOriginalPrice, $extractCurrency): array {
            $currentPrice = $extractCurrentPrice($node, $isAuction);

            if ($currentPrice === null || $currentPrice <= 0) {
                return [];
            }

            return [
                'current_price' => $currentPrice,
                'original_price' => $extractOriginalPrice($node),
                'currency' => $extractCurrency($node, 'USD'),
                'condition' => (string)($node['condition'] ?? $fallbackCondition),
                'bid_count' => isset($node['bidCount']) ? (int)$node['bidCount'] : 0,
            ];
        };

        $buyingOptions = array_map('strtoupper', $product['buyingOptions'] ?? []);
        $condition = (string)($product['condition'] ?? '');
        $result = [
            'url' => (string)($product['itemWebUrl'] ?? ('https://www.ebay.com/itm/' . $itemId)),
            'is_variant' => !empty($product['variations']),
            'auction' => [],
            'buy_now' => [],
        ];

        foreach ((array)($product['variations'] ?? []) as $variation) {
            $variationOptions = array_map('strtoupper', $variation['buyingOptions'] ?? $buyingOptions);
            $hasVariationOptions = !empty($variationOptions);

            if (in_array('AUCTION', $variationOptions, true)) {
                $summary = $buildSummary($variation, true, $condition);
                if (!empty($summary) && (empty($result['auction']) || $summary['current_price'] < $result['auction']['current_price'])) {
                    $result['auction'] = $summary;
                }
            }

            if (in_array('FIXED_PRICE', $variationOptions, true) || (!$hasVariationOptions && !in_array('AUCTION', $variationOptions, true))) {
                $summary = $buildSummary($variation, false, $condition);
                if (!empty($summary) && (empty($result['buy_now']) || $summary['current_price'] < $result['buy_now']['current_price'])) {
                    $result['buy_now'] = $summary;
                }
            }
        }

        if (empty($result['auction']) && in_array('AUCTION', $buyingOptions, true)) {
            $result['auction'] = $buildSummary($product, true, $condition);
        }

        if (empty($result['buy_now']) && in_array('FIXED_PRICE', $buyingOptions, true)) {
            $result['buy_now'] = $buildSummary($product, false, $condition);
        }

        return $result;
    }

}