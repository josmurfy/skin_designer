<?php



include_once(DIR_SYSTEM . 'library/jgetsy/KbOAuth.php');

include_once(DIR_SYSTEM . 'library/jgetsy/EtsyApi.php');

include_once(DIR_SYSTEM . 'library/jgetsy/RequestValidator.php');

include_once(DIR_SYSTEM . 'library/jgetsy/EtsyMain.php');

include_once(DIR_SYSTEM . 'library/jgetsy/oauth_client.php');

include_once(DIR_SYSTEM . 'library/jgetsy/http.php');



class ControllerJgetsyProduct extends Controller {



    private $syncType = 'SyncProducts';

    private $syncMethod = '';

    private $syncError = false;

    private $syncLimit = 100; // change



    public function index() {

        @ini_set('memory_limit', -1);

        @ini_set('max_execution_time', -1);

        @set_time_limit(0);



        if ($this->config->get('jgetsy_demo_flag') == 0) {

            $this->load->model('jgetsy/cron');

            $this->load->model('jgetsy/product');

            $this->load->model('jgetsy/profile');

            $this->load->model('jgetsy/shipping_profile');



            $secure_key = $this->config->get('jgetsy_secure_key');



            if (!empty($this->request->get['secure_key']) && $secure_key == $this->request->get['secure_key']) {

                if (!empty($this->request->get['limit'])) {

                    $this->syncLimit = $this->request->get['limit'];

                }



                $this->model_jgetsy_cron->auditLogEntry("Product Sync Started", $this->syncType);

                if (!empty($this->request->get['local'])) {

                    $this->syncLocal();

                } else if (!empty($this->request->get['update'])) {

                    $this->syncUpdateListing();

                } else if (!empty($this->request->get['status'])) {

                    $this->syncListingsStatus();

                } else {

                    $this->syncProductsListing();

                }

                $this->model_jgetsy_cron->auditLogEntry("Product Sync Completed", $this->syncType);

            } else {

                echo "Secure key not matched.";

            }

        } else {

            echo "Sorry!!! You are not allowed to perform this action the demo version.";

        }

        die();

    }



    private function syncLocal() {

        if ($this->model_jgetsy_product->updateProductStatusByProfileStatus()) {

            $this->prepareProductToList();

            echo "Success1";

        }

    }



    private function syncProductsListing() {

        if ($this->model_jgetsy_product->updateProductStatusByProfileStatus()) {

            $this->prepareProductToList();

            $this->createListings();

            if ($this->syncError == true) {

                echo "Success with some error(s). Refer to audit log for the details of the error.";

            } else {

                echo "Success2"; 

            }

        }

    }



    private function syncUpdateListing() {

        if ($this->model_jgetsy_product->updateProductStatusByProfileStatus()) {

            $this->prepareProductToList();

            $this->updateListings();

            if ($this->syncError == true) {

                echo "Success with some error(s). Refer to audit log for the details of the error.";

            } else {

                echo "Success3";

            }

        }

    }



    private function syncListingsStatus() {

        $this->getListingsStatus();

        if ($this->syncError == true) {

            echo "Success with some error(s). Refer to audit log for the details of the error.";

        } else {

            echo "Success4";

        }

    }



    private function prepareProductToList() {

        $this->model_jgetsy_product->syncProductsToModule();

    }



    private function createListings() {

        $this->syncMethod = 'SyncListingOCToEtsy';

        $logEntry = 'Sync Products on Etsy Started';

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);



        $settings = $this->config->get('etsy_general_settings');

        $language_id = $settings['etsy_default_language'] != '' ? $settings['etsy_default_language'] : $this->config->get('config_language_id');



        $listingArray = array();

        $product_id = "";

        if (!empty($this->request->get['etsy_product_id'])) {

            $product_id = $this->request->get['etsy_product_id'];

        }

        $productsList = $this->model_jgetsy_product->getProductsToListOnEtsy($product_id, $this->syncLimit);


       //print("<pre>".print_r ($productsList,true )."</pre>");


        if (isset($productsList) && $productsList) {

            $listing = array();

            $i = 0;

            foreach ($productsList as $productList) {

                $listing = $this->prepareArrayToSyncOnEtsy($productList, $language_id);
               //print("<pre>".print_r ($listing,true )."</pre>");
                if ($i > 0) {

                    //    continue;

                }

                if (!empty($listing)) {

                    $listingArray[] = $listing; 

                }

                $i++;

            }

            //print("<pre>".print_r ($listingArray,true )."</pre>");

        }



        if (isset($listingArray) && count($listingArray) > 0) {

            $this->etsyCreateListings($listingArray);

        }

        return true;

    }



    /** Function to sync selected etsy attribute on the Etsy */

    public function syncEtsyAttribute($listing_id) {



        $this->load->model('jgetsy/cron');



        $etsyAttributes = $this->db->query("SELECT eam.* FROM `" . DB_PREFIX . "etsy_products_list` pl INNER JOIN `" . DB_PREFIX . "etsy_attribute_option_mapping` eam ON pl.id_etsy_profiles = eam.id_etsy_profiles WHERE listing_id = '" . $listing_id . "' AND id_product_attribute = '0'");

        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();

        if ($etsyAttributes->num_rows > 0) {

            foreach ($etsyAttributes->rows as $etsyAttribute) {

                if ($etsyAttribute['id_attribute_group'] != "") {

                    $etsyRequestURI = '/listings/' . $listing_id . '/attributes/' . $etsyAttribute['property_id'];

                    //$etsyRequestMethod = 'PUT';

                    //$attr_group = explode(",", $etsyAttribute['id_attribute_group']);



                    $etsyQueryString = array("value_ids" => $etsyAttribute['id_attribute_group']);

                    $args = array('uri' => $etsyRequestURI, "params" => $etsyQueryString, "data" => $etsyQueryString,);







                    $settings = $this->config->get('etsy_general_settings');

                    $access_token = $this->config->get('etsy_access_token');

                    $access_token_secret = $this->config->get('etsy_access_token_secret');

                    $etsyClient = new oauth_client_class;

                    $etsyClient->server = 'Etsy';

                    $etsyClient->debug = false;

                    $etsyClient->debug_http = true;



                    $etsyClient->client_id = $settings['etsy_api_key'];

                    $etsyClient->client_secret = $settings['etsy_api_secret'];

                    $etsyClient->scope = 'email_r listings_w listings_r transactions_r transactions_w';



                    $etsyClient->access_token = $access_token;

                    $etsyClient->access_token_secret = $access_token_secret;

                    $etsyRequestMethod = 'PUT';

                    $etsyResponse = '';

                    if ($etsySuccess = $etsyClient->Initialize()) {

                        $etsySuccess = $etsyClient->CallAPI('https://openapi.etsy.com/v3/application' . $etsyRequestURI, $etsyRequestMethod, $etsyQueryString, array('FailOnAccessError' => true), $etsyResponse);

                        $etsySuccess = $etsyClient->Finalize($etsySuccess);

                    }

                    $etsyResponse = json_decode(json_encode($etsyResponse), true);

                }

            }

        }

        return true;

    }



    //Function definition to send request on Etsy to Create Products Listings

    private function etsyCreateListings($listingArray = array()) {

        ini_set("display_errors", "1");

        error_reporting(E_ALL);

        $this->syncMethod = 'SyncListingOCToEtsy';

        $logEntry = 'Sending products to Etsy. Number of Products:' . count($listingArray);

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

        if (!empty($listingArray) && count($listingArray) > 0) {



            $etsyMain = $this->model_jgetsy_cron->createEtsyObject();

            foreach ($listingArray as $listing) {

                if (isset($listing['id_product'])) {

                    //Prepare parameters to send request

                    $product_id = $listing['id_product'];

                    unset($listing['id_product']);

                    $result = $etsyMain->sendRequest("createDraftListing", array("data" => $listing));

                  //  print array_shift(array_keys($result));

                   //  echo count($result)."MEssage".$result;

                    if (count($result)==1){//(isset($result['error'])) 

                        $this->syncError = true;

                        $this->model_jgetsy_cron->auditLogEntry(array_shift(array_keys($result)), $this->syncMethod);

                        $this->model_jgetsy_product->updateListingAddErrorStatus($product_id, array_shift(array_keys($result)));

          

                      //  $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);

                       // $this->model_jgetsy_product->updateListingAddErrorStatus($product_id, $result['error']);

                    } elseif (isset($result['results'])) {

                        $this->model_jgetsy_product->updateListingAddStatus($result['results'][0], $product_id);

                        $this->updateListingVariation($product_id, $result['results'][0]['listing_id']);

                        $this->syncEtsyAttribute($result['results'][0]['listing_id']);

                        $this->syncImages($product_id, $result['results'][0]['listing_id']);

                        /* Sync error is being updated in each function call i.e. syncImage, UpdateLisitng Variation etc so we need to add if syncerror condition sepratley for syncImages & Transation */

                        $this->syncTransations($product_id, $result['results'][0]['listing_id']);

                    }

                }

                echo "sleep 1 sec<br>";

                sleep(1);

            }

        }

        return true;

    }



    private function updateListings() {

        $settings = $this->config->get('etsy_general_settings');

        $language_id = $settings['etsy_default_language'] != '' ? $settings['etsy_default_language'] : $this->config->get('config_language_id');



        $listingArray = array();

        $product_id = "";

        if (!empty($this->request->get['etsy_product_id'])) {

            $product_id = $this->request->get['etsy_product_id'];

        }

        $productsList = $this->model_jgetsy_product->getProductsToUpdateOnEtsy($product_id, $this->syncLimit);



        if (isset($productsList) && $productsList) {

            foreach ($productsList as $product) {

                $listing = $this->prepareArrayToSyncOnEtsy($product, $language_id);

                if (!empty($listing)) {

                    $listingArray[] = $listing;

                }

            }

        }



        if (isset($listingArray) && count($listingArray) > 0) {

            $this->etsyUpdateListings($listingArray);

        }

        return true;

    }



    private function prepareArrayToSyncOnEtsy($product = array(), $language_id = '1') {

        $this->load->model('jgetsy/datatype');

        $occasions = $this->model_jgetsy_datatype->getDataType('Occasion');

        $recipients = $this->model_jgetsy_datatype->getDataType('Recipient');

        //"<pre>".print_r ($product,true )."</pre>");

        $listingArray = array();

        $this->load->model('localisation/language');

        $this->load->model('localisation/currency');

        $settings = $this->config->get('etsy_general_settings');



        if (isset($product) && count($product) > 0 && !empty($language_id)) {

            //Get Profile Details
echo "allo";
            $profileDetails = $this->model_jgetsy_profile->getEtsyProfileDetails($product['id_etsy_profiles']);

            //Get Product Title & Description

            $productDetails = $this->model_jgetsy_product->getProductByProductId($product['id_product'], $language_id);



            //Condition to check that all mandatory parameters are available

            if (!empty($productDetails)) {

                //Get Shipping Profile ID
                //echo "allo2";
                $shippingProfileID = $this->model_jgetsy_shipping_profile->getShippingProfileById($profileDetails['id_etsy_shipping_profiles']);



                if (!empty($shippingProfileID['shipping_profile_id']) && $shippingProfileID['shipping_profile_id'] != '0') {

                    //Etsy do not accept quantity more than 999

                    if ($productDetails['quantity'] > 999) {

                        $productDetails['quantity'] = "999";

                    }



                    $tagArrayUpdated = $this->productTags($productDetails['tag']);



                    /* OC default currency */

                    $default_currency = $this->model_jgetsy_product->getDefaultCurrency();



                    /* Caculate etsy currency conversion in comparsion to OC default currency */

                    //$currency_conversion = 1;

                    //if($default_currency) {

                    //    /* If OC default currency & etsy default currency are same then conversion rate will be 1 */

                    //    if($default_currency['code'] == $settings['currency']) {

                    //        $currency_conversion = 1;

                    //    } else {

                    //        $etsy_default_currency = $this->model_localisation_currency->getCurrencyByCode($settings['currency']);

                    //        if($etsy_default_currency) {

                    //            $currency_conversion = $etsy_default_currency['value'];

                    //        }

                    //    }

                    //} 

                    /* Above code commented as using currency conversion fuction to convert the price in etsy currency from OC default currency */



                    $price = $productDetails['price_with_shipping'];



                    /* If Price Type is Special */

                    if ($profileDetails['price_type'] == 1) {

                        $price = $this->model_jgetsy_product->specialPrice($product['id_product']);

                        if ($price == "") {

                            $price = $productDetails['price_with_shipping'];

                        }

                    }



                    /* If Price Management is Yes */

                    $price_change_amount = 0;

                    if ($profileDetails['price_management'] == 1) {

                        /* If percentage_fixed: 1 - Percentage, 0 - Fixed */

                        if ($profileDetails['percentage_fixed'] == 1) {

                            $price_change_amount = ($price * $profileDetails['product_price']) / 100;

                        } else {

                            $price_change_amount = $profileDetails['product_price'];

                        }



                        if ($profileDetails['increase_decrease'] == 1) {

                            $price = $price + $price_change_amount;

                        } else {

                            $price = $price - $price_change_amount;

                        }

                    }



                    if ($price < 0) {

                        $price = 0;

                    }



                    $lang_data = $this->model_localisation_language->getLanguage($language_id);

                    if (isset($lang_data['code']) && $lang_data['code'] != "") {

                        // FOR OC2.2, languge code in en, Language code in OC2.3 & 3.0 is like en_US.

                        $lang_code_array = explode("-", $lang_data['code']);

                        $language_code = $lang_code_array[0];

                    } else {

                        $language_code = 'en';

                    }



                    if (isset($occasions[$language_code][$profileDetails['occassion']])) {

                        $occasion_details = $occasions[$language_code][$profileDetails['occassion']];

                    } else {

                        $occasion_details = '';

                    }



                    if (isset($recipients[$language_code][$profileDetails['recipient']])) {

                        $recipient_details = $recipients[$language_code][$profileDetails['recipient']];

                    } else {

                        $recipient_details = "";

                    }

                    //echo ucwords($productDetails['name']);

                    $listingArray = array(

                        'id_product' => $product['id_product'],

                        'quantity' => $productDetails['quantity'],

                        'title' => ucwords(strtolower($productDetails['name'])),

                        'description' => $this->filterDescription($productDetails['description']),

                        'price' => $this->currency->convert($price, $default_currency['code'], $settings['currency']),

                        'shipping_profile_id' => $shippingProfileID['shipping_profile_id'],

                        'state' => 'active', //Added Temporarily

                        'taxonomy_id' => $profileDetails['etsy_category_code'],

                        'shop_section_id' => $profileDetails['shop_section_id'],

                        'who_made' => $profileDetails['who_made'],//'someone_else',

                        'is_supply' => $profileDetails['is_supply'],

                        'when_made' => $profileDetails['when_made'],

                        'recipient' => $recipient_details,

                        'occasion' => $occasion_details,

                        'should_auto_renew' => (boolean) $profileDetails['auto_renew'],

                        'item_weight' => (float) number_format((float) $productDetails['weight'], 2, '.', ''),

                        'item_weight_unit' => $productDetails['weight_class'],

                        'item_length' => (float) number_format((float) $productDetails['length'], 2, '.', ''),

                        'item_width' => (float) number_format((float) $productDetails['width'], 2, '.', ''),

                        'item_height' => (float) number_format((float) $productDetails['height'], 2, '.', ''),

                        'item_dimensions_unit' => $productDetails['length_class'],

                        'language' => $language_code

                    );



                    if ($profileDetails['is_customizable'] == 1) {

                        $listingArray['is_customizable'] = $profileDetails['is_customizable']; //Removed as it is only accepted for custom orders

                    }



                    if ($profileDetails['shop_section_id'] != "") {

                        $listingArray['shop_section_id'] = $profileDetails['shop_section_id'];

                    }



                    if ($product['update_flag'] == 1) {

                        $listingArray['update_flag'] = 1;

                    }



                    if ($product['renew_flag'] == 1) {

                        $listingArray['renew_flag'] = 1;

                    }



                    if ($product['delete_flag'] == 1) {

                        $listingArray['delete_flag'] = 1;

                    }



                    if (!empty($tagArrayUpdated)) {

                        $listingArray['tags'] = implode(',', $tagArrayUpdated);

                    }

                    //

                    //In case recipient is not provided

                    if (empty($profileDetails['recipient'])) {

                        unset($listingArray['recipient']);

                    }



                    //In case occasion is not provided

                    if (empty($profileDetails['occassion'])) {

                        unset($listingArray['occasion']);

                    }



                    if ($product['listing_status'] == 'Draft') {

                        $listingArray['state'] = 'draft';

                    }



                    if ($product['update_flag'] == 1 || $product['renew_flag'] == 1) {

                        //Check if product has variations then unset "Price" option as prices are set by Variations

                        //$checkVariationExistence = $this->model_jgetsy_product->checkVariations($product['id_product']);

                        //if ($checkVariationExistence) {

                        //unset($listingArray['price']);

                        //}

                        /* Above lines not required as it will be handled at etsyUpdateListings function */

                    }



                    if ($product['update_flag'] == 1) {

                        $listingArray['listing_id'] = $product['listing_id'];

                    }



                    if ($product['renew_flag'] == 1) {

                        $listingArray['listing_id'] = $product['listing_id'];

                        $listingArray['renew'] = "1";

                        $listingArray['state'] = 'active';

                    }

                }

            }

        }
       //print("<pre>".print_r ($listingArray,true )."</pre>");

        return $listingArray;

    }



    private function productTags($tags) {

       // echo $tags;

        $tagArrayUpdated = array();

        if (trim($tags) != "") {

            $tagArray = explode(",", $tags);

            if (count($tagArray)) {

                foreach ($tagArray as $tagToSend) {

                    $tagToSend = str_replace('-', ' ',$tagToSend);

                    $tagToSend = preg_replace('/[^A-Za-z0-9 ]/', '', $tagToSend);

                    $tagToSend = trim(substr($tagToSend, 0, 20));

                    $tagArrayUpdated[$tagToSend] = $tagToSend;

                    

                    if (count($tagArrayUpdated) > 12) {

                        break;

                    }

                }

              //  echo count($tagArrayUpdated) ;

            }

        }

        return $tagArrayUpdated;

    }



    private function filterDescription($description) {

        $description = html_entity_decode($description);

        $description = str_replace('Description :','',$description);

        $description = str_replace('Photos :','',$description);

        $description = str_replace('<br>',' ',$description);

        

        $clear = strip_tags($description);

        // Clean up things like &amp;

        $clear = html_entity_decode($clear);

        // Strip out any url-encoded stuff

        $clear = urldecode($clear);

        // Replace non-AlNum characters with space

        //$clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);

        // Replace Multiple spaces with single space

        $clear = preg_replace('/ +/', ' ', $clear);

        // Trim the string of leading/trailing space

        $clear = trim($clear);

        return $clear;

    }



    private function updateListingVariation($product_id, $listing_id) {

        $this->syncMethod = 'SyncListingVariation';

        $logEntry = 'Sync Product Variation Started. Product ID:' . $product_id;

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);



        $settings = $this->config->get('etsy_general_settings');

        $language_id = $settings['etsy_default_language'] != '' ? $settings['etsy_default_language'] : $this->config->get('config_language_id');

        $option_data = $this->prepareArrayToListVariationOnEtsy($product_id, $language_id);

        $updateVariationResult = true;

        if (!empty($option_data)) {

            $option_data['listing_id'] = (string) $listing_id;

            $updateVariationResult = $this->etsyUpdateVariation($product_id, $option_data);

        }

        $logEntry = 'Sync Product Variation Completed. Product ID:' . $product_id;

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

        if ($updateVariationResult == false) {

            return false;

        } else {

            return true;

        }

    }



    private function etsyUpdateVariation($product_id, $option_data = array()) {

        sleep(1);

        $this->syncMethod = 'SyncListingVariation';

        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();

        $result = $etsyMain->sendRequest("updateListingInventory", array('data' => $option_data, 'params' => $option_data));

        if (isset($result['error'])) {

            $this->syncError = true;

            $this->model_jgetsy_product->updateListingAddErrorStatus($product_id, $result['error']);

            $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);

            return false;

        } else {

            return true;

        }

    }



    public function test() {

        ini_set("display_errors", "1");

        error_reporting(E_ALL);



        $this->load->model('jgetsy/cron');

        $this->load->model('jgetsy/product');

        $this->load->model('jgetsy/profile');

        $this->load->model('jgetsy/shipping_profile');

        //$this->prepareArrayToListVariationOnEtsy(42, 1);

        //$this->etsyImageListings(33, 660833083);

        //$this->getLanguagesToSync();

        //$this->syncTransation(33, 660833083);

        //echo "AAA";

        //$this->updateListingVariation(902,679117702);

        //$this->updateListingVariation(903,679125714);

        //$etsyMain = $this->model_jgetsy_cron->createEtsyObject();

//        $etsyQueryString = array(

//            'listing_id' => "679117702"

//        );

        //$getInventoryResult = $etsyMain->sendRequest("getListingInventory", array("data" => $etsyQueryString, "params" => $etsyQueryString));

        //print_r($getInventoryResult);

        //        $this->syncImages(902,679117702);

        //$this->syncTransations(902,679117702);

    }



    private function prepareArrayToListVariationOnEtsy($product_id, $language_id = '') {

        $etsyQueryString = array();

        $products = array();



        /* Get All Variations of a product */

        $variations = $this->model_jgetsy_product->getVariations($product_id);

        if (!empty($variations)) {

            $product_details = $this->model_jgetsy_product->getProduct($product_id);

            $profile_details = $this->model_jgetsy_profile->getEtsyProfileByProduct($product_id);



            $productOptions = array();



            foreach ($variations as $variation) {

                $productOptions['option_' . $variation['option_id']][] = $variation['option_value_id'];

            }



            /* Generate Combinations from the Variations */

            $variations_combinations = $this->model_jgetsy_product->get_combinations($productOptions);



            $key = 0;

            if ($variations_combinations) {

                $propertyIds = array();

                foreach ($variations_combinations as $key_option_id => $variation) {



                    $i = 0;

                    $price = $product_details['price'];



                    /* If Price Type is Special */

                    if ($profile_details['price_type'] == 1) {

                        $price = $this->model_jgetsy_product->specialPrice($product_id);

                        if ($price == "") {

                            $price = $product_details['price'];

                        }

                    }



                    $quantity = 0;

                    $zero_quantity_flag = false;

                    /* For ALL options of a Combinations  (i.e. Size: Red And Color: Green) */

                    foreach ($variation as $key_option_id => $option_value_id) {

                        $option_id = str_replace("option_", "", $key_option_id);



                        $option_details = $this->model_jgetsy_product->getAttributeMapping($product_id, $option_id, $option_value_id, $language_id);



                        $properties[$i] = array(

                            'property_id' => $option_details['property_id'],

                            'values' => array($option_details['name']),

                            'property_name' => $option_details['property_title']

                        );

                        //'scale_id' => 28,

                        //'scale_name' => 'DE'



                        $propertyIds[$option_details['property_id']] = $option_details['property_id'];



                        /* Get qunatity which is lesser i.e. if Red quantity is less than take red qusntltu otherwise size i.e. Small quantity */

                        if ($option_details['quantity'] == 0) {

                            $zero_quantity_flag = true;

                        }

                        if ($i == 0) {

                            $quantity = $option_details['quantity'];

                        } else if ($option_details['quantity'] < $quantity && $quantity > 0) {

                            $quantity = $option_details['quantity'];

                        }



                        if ($quantity > 999) {

                            $quantity = 999;

                        }



                        /* Add Price of Optons combination in the price to get the final price */

                        if ($option_details['price_prefix'] == '+') {

                            $price = $price + $option_details['price'];

                        } else {

                            $price = $price - $option_details['price'];

                        }

                        $i++;

                    }





                    /* If Price Management is Yes */

                    $price_change_amount = 0;

                    if ($profile_details['price_management'] == 1) {

                        /* If percentage_fixed: 1 - Percentage, 0 - Fixed */

                        if ($profile_details['percentage_fixed'] == 1) {

                            $price_change_amount = ($price * $profile_details['product_price']) / 100;

                        } else {

                            $price_change_amount = $profile_details['product_price'];

                        }



                        if ($profile_details['increase_decrease'] == 1) {

                            $price = $price + $price_change_amount;

                        } else {

                            $price = $price - $price_change_amount;

                        }

                    }



                    if ($price < 0) {

                        $price = 0;

                    }



                    /* If zero quantity flag than set the product quantity as zero */

                    if ($zero_quantity_flag == true) {

                        $quantity = 0;

                    }

                    //if ($value['delete_flag'] == 1) {

                    //    $is_enabled = 0;

                    //} else {

                    $is_enabled = 1;

                    //}



                    $settings = $this->config->get('etsy_general_settings');

                    $default_currency = $this->model_jgetsy_product->getDefaultCurrency();



                    $products[$key]['property_values'] = $properties;

                    $products[$key]['sku'] = '';

                    $products[$key]['offerings'] = array(array(

                            'price' => $this->currency->convert($price, $default_currency['code'], $settings['currency']),

                            'quantity' => $quantity,

                            'is_enabled' => $is_enabled

                    ));

                    $key++;

                }



                if ($products) {

                    $etsyQueryString = array(

                        'products' => json_encode($products),

                        'price_on_property' => implode(",", $propertyIds),

                        'quantity_on_property' => implode(",", $propertyIds),

                        'sku_on_property' => ''

                    );

                }

                return $etsyQueryString;

            } else {

                return false;

            }

        } else {

            return false;

        }

    }



    private function etsyUpdateListings($listingArray = array(), $deleteListing = false, $renewListing = false) {

        $this->syncMethod = 'UpdateListingOnEtsy';



        $listingsUpdated = 0;

        $listingsDeleted = 0;

        $listingsRenewed = 0;


        
           
        $logEntry = 'Update Listing on Etsy Started.';

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);



        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();

       

        if (!empty($listingArray) && count($listingArray) > 0) {

            foreach ($listingArray as $listing) {

                

                //Prepare parameters to send request

                $etsyQueryString = $listing;

                unset($etsyQueryString['id_product']);

                //unset($etsyQueryString['quantity']);

                unset($etsyQueryString['price']);

                unset($etsyQueryString['update_flag']);

                unset($etsyQueryString['delete_flag']);

                unset($etsyQueryString['renew_flag']);

                $syncFlag = true;



                /** Update current status of item by requesting product info from etsy. */

                $listing_data = array(

                    'listing_id' => $listing['listing_id'],

                );

              

                $listing_status_data = $etsyMain->sendRequest("getListing", array('params' => $listing_data, 'data' => $listing_data));
                //echo "jo********************";
                /** In case of sold out, Inventory needs to passed so unsettting Inventory in else condition (If item inventory is zero on Etsy) Otherwise Etsy will return the following error i.e. quantity_cannot_be_empty_,_Invalid_edit_attempted_] */

                if ($listing_status_data['results'][0]['state'] == 'sold_out') {

                    if ($listing['quantity'] > 0) {

                        $etsyQueryString['renew'] = "1";

                    } else {

                        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_status = 'Inactive', listing_error = '', error_flag = '0', update_flag = '0' WHERE id_product = '" . (int) $listing['id_product'] . "'");

                        continue;

                    }

                } else {

                    if ($listing_status_data['results'][0]['state'] == 'inactive' || $listing_status_data['results'][0]['state'] == 'edit') {

                        if ($etsyQueryString['quantity'] == 0) {

                            $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_status = 'Inactive', listing_error = '', error_flag = '0', update_flag = '0' WHERE id_product = '" . (int) $listing['id_product'] . "'");

                            continue;

                        } else {

                            $etsyQueryString['renew'] = "1";

                        }

                    } else if ($listing_status_data['results'][0]['state'] == 'expired') {

                        if (empty($listing['renew_flag'])) {

                            $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_status = 'Expired' WHERE id_product = '" . (int) $listing['id_product'] . "'");

                            continue;

                        } else {

                            /* In case of relist as well, It item inventory is zero, then don't do anything */

                            if ($etsyQueryString['quantity'] == 0) {

                                continue;

                            }

                        }

                    }

                    unset($etsyQueryString['quantity']);

                }



                /* In cae of edit, If item is expired, Set the renew flag else remove the renew flag */

                if (date("Y-m-d H:i:s", $listing_status_data['results'][0]['ending_tsz']) > date("Y-m-d H:i:s") && ($listing_status_data['results'][0]['state'] != 'sold_out' && $listing_status_data['results'][0]['state'] != 'inactive' && $listing_status_data['results'][0]['state'] != 'edit')) {

                    unset($etsyQueryString['renew']);

                } else {

                    $etsyQueryString['renew'] = "1";

                }



                /* Parameter to set status as Sold Out in DB in case item is SOLD OUT */

                $sold_out = false;

                if ($listing['quantity'] == 0 && !empty($listing['listing_id'])) {

                    $sold_out = true;

                    /* In case of Sold Out, Set the Status as Inactive on Etsy */

                    $etsyQueryString['state'] = 'inactive';

                }

                try {

                    $result = $etsyMain->sendRequest("updateListing", array("data" => $etsyQueryString, "params" => $etsyQueryString));

                   //print("<pre>".print_r ($result,true )."</pre>");

                    if (count($result)==1) {

                        $this->syncError = true;

                        $this->model_jgetsy_cron->auditLogEntry(array_shift(array_keys($result)) . ' (' . $listing['id_product'] . ') ' . __LINE__, $this->syncMethod);

                        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET update_flag=0,error_flag=1,listing_error = '" . $this->db->escape(array_shift(array_keys($result))) . "' WHERE id_product = '" . $listing['id_product'] . "'");

                    } elseif (isset($result['results'])) {



                        $listingID = $result['results'][0]['listing_id'];

                        /* Check Variation. If no variation, then sync inventory & price */



                        $variations = $this->model_jgetsy_product->getVariations($listing['id_product']);

                        if (empty($variations)) {

                            $etsyQueryString = array(

                                'listing_id' => $listing['listing_id']

                            );

                            $getInventoryResult = $etsyMain->sendRequest("getListingInventory", array("data" => $etsyQueryString, "params" => $etsyQueryString));

                            if (isset($getInventoryResult['error'])) {

                                $this->syncError = true;

                                $this->model_jgetsy_cron->auditLogEntry($getInventoryResult['error'] . ' (' . $listing['id_product'] . ') ' . __LINE__, $this->syncMethod);

                                $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_error = '" . $this->db->escape($getInventoryResult['error']) . "' WHERE id_product = '" . $listing['id_product'] . "'");

                            } elseif (isset($getInventoryResult['results'])) {



                                $getInventoryResult['results']['products'][0]['offerings'][0]['price'] = $listing['price'];

                                $getInventoryResult['results']['products'][0]['offerings'][0]['quantity'] = $listing['quantity'];

                                $etsyQueryString = array(

                                    'listing_id' => (string) $listing['listing_id'],

                                    'products' => json_encode($getInventoryResult['results']['products'])

                                );

                                $updateInventoryResult = $etsyMain->sendRequest("updateListingInventory", array("data" => $etsyQueryString, "params" => $etsyQueryString));



                                if (isset($updateInventoryResult['error'])) {

                                    $this->syncError = true;

                                    $syncFlag = false;

                                    $this->model_jgetsy_cron->auditLogEntry($updateInventoryResult['error'], $this->syncMethod);

                                    $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_error = '" . $this->db->escape($updateInventoryResult['error']) . "' WHERE id_product = '" . $listing['id_product'] . "'");

                                }

                            }

                        } else {

                            /* Update Inventory & Variations If Variation Exist. */

                            if (!$deleteListing) {

                                $syncFlag = $this->updateListingVariation($listing['id_product'], $listing['listing_id']);

                            }

                        }



                        if (!empty($listingID)) {



                            $this->syncEtsyAttribute($listingID);



                            /* If no error then update the status */

                            if ($syncFlag == true) {

                                if ($deleteListing) {

                                    $listingsDeleted++;

                                    $updateSQL = "UPDATE " . DB_PREFIX . "etsy_products_list SET delete_flag = '2', update_flag = '0', renew_flag = '0' listing_error = '' WHERE id_product = '" . $listing['id_product'] . "'";

                                } else if ($renewListing) {

                                    $listingsRenewed++;

                                    $updateSQL = "UPDATE " . DB_PREFIX . "etsy_products_list SET listing_id = '" . $listingID . "', listing_status = 'Listed', renew_flag = '0', update_flag = '0', renew_flag = '0', date_last_renewed = NOW(), listing_error = '' WHERE id_product = '" . $listing['id_product'] . "'";

                                } else {

                                    $listingsUpdated++;

                                    $updateSQL = "UPDATE " . DB_PREFIX . "etsy_products_list SET listing_status = 'Listed', update_flag = '0', renew_flag = '0', listing_error = '' WHERE id_product = '" . $listing['id_product'] . "'";

                                }

                                $this->db->query($updateSQL);



                                /* Update Image & Translation */

                                if (!$deleteListing) {

                                    $this->syncImages($listing['id_product'], $listingID);



                                    //Needs to add syncError Conditions Seprately */

                                    $this->syncTransations($listing['id_product'], $listingID);

                                }

                            }

                        }

                    }

                    sleep(1); //Sleep job to avoid exceed limit rate

                } catch (Exception $e) {

                    $this->syncError = true;

                    $this->model_jgetsy_cron->auditLogEntry($e->getMessage(), $this->syncMethod);

                    $this->model_jgetsy_product->updateListingAddErrorStatus($listing['id_product'], $e->getMessage());

                }

                echo "sleep 1 sec<br>";

                sleep(1);

            }

        }



        $logEntry = 'Update Listing on Etsy Marketplace Completed. <br/> Total Listing Updated: ' . $listingsUpdated . '<br/>Total Listing Deleted: ' . $listingsDeleted . '<br/>Total Listing Renewed: ' . $listingsRenewed;

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

        return true;

    }



    private function getListingsStatus() {

        $this->syncMethod = 'SyncEtsyProductStatus';

        $logEntry = 'Sync Product Status Started';

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);



        $productsList = $this->model_jgetsy_product->getProductsListedOnEtsy();

        $listingArray = array();

        if (isset($productsList) && $productsList) {

            foreach ($productsList as $productsList) {

                $listingArray[] = array(

                    'listing_id' => $productsList['listing_id'],

                );

            }

        }



        if (isset($listingArray) && count($listingArray) > 0) {



            foreach ($listingArray as $listing) {

                //Prepare parameters to send request

                $etsyQueryString = $listing;

                $etsyMain = $this->model_jgetsy_cron->createEtsyObject();

                $result = $etsyMain->sendRequest("getListing", array('params' => $etsyQueryString, 'data' => $etsyQueryString));

                if (isset($result['error'])) {

                    $this->syncError = true;

                    $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);

                    $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_error = '" . $this->db->escape($result['error']) . "' WHERE listing_id = '" . $listing['listing_id'] . "'");

                } elseif (isset($result['results'])) {

                    $listingStatus = $result['results'][0]['state'];

                    if ($listingStatus == 'inactive' || $listingStatus == 'sold_out' || $listingStatus == 'edit') {

                        $listingStatus = 'Inactive';

                        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_status = '" . $this->db->escape($listingStatus) . "', update_flag = '0', renew_flag = '0', delete_flag = '0', is_disabled = '1' WHERE listing_id = '" . $listing['listing_id'] . "'");

                    } else if ($listingStatus == 'expired') {

                        $listingStatus = 'Expired';

                        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_status = '" . $this->db->escape($listingStatus) . "', update_flag = '0', renew_flag = '0', delete_flag = '0', is_disabled = '0' WHERE listing_id = '" . $listing['listing_id'] . "'");

                    } else if ($listingStatus == 'active') {

                        $listingStatus = 'Listed';

                        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_status = '" . $this->db->escape($listingStatus) . "', update_flag = '0', renew_flag = '0', delete_flag = '0', is_disabled = '0' WHERE listing_id = '" . $listing['listing_id'] . "'");

                    } else if ($listingStatus == 'draft') {

                        $listingStatus = 'Draft';

                        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_status = '" . $this->db->escape($listingStatus) . "', update_flag = '0', renew_flag = '0', delete_flag = '0' WHERE listing_id = '" . $listing['listing_id'] . "'");

                    } else if ($listingStatus == 'removed') {

                        $listingStatus = 'Pending';

                        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_status = 'Pending', listing_id = NULL, update_flag = '0', renew_flag = '0', delete_flag = '0', is_disabled = '1' WHERE listing_id = '" . $listing['listing_id'] . "'");

                    } else {

                        $listingStatus = 'Pending';

                    }

                }

            }

        }

        $logEntry = 'Sync Product Status Completed.';

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

        return true;

    }



    private function syncImages($product_id, $listing_id) {

        $this->syncMethod = 'SyncEtsyProductImage';

        $logEntry = 'Sync Product Images Started. Product ID:' . $product_id;

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);



        $productImage = $this->model_jgetsy_product->getImagesByProductId($product_id);

        $i = 1;

        if (!empty($productImage['update'])) {

            usort($productImage['update'], array($this, 'compare_main_image')); /* Sorting of the image on the basis of the main_image flag to keep main image at position 1 */

            foreach ($productImage['update'] as $prodImg) {

                if ($prodImg['update'] == 0) {

                    $etsyQueryString = array(

                        'listing_id' => $listing_id,

                        'image' => DIR_IMAGE . $prodImg['image'],

                        'rank' => $i,

                        'overwrite' => 0

                    );

                } else if ($prodImg['update'] == 1) {

                    $etsyQueryString = array(

                        'listing_id' => $listing_id,

                        'listing_image_id' => $prodImg['etsy_image_id'],

                        'image' => DIR_IMAGE . $prodImg['image'],

                        'rank' => $i,

                        'overwrite' => 1

                    );

                } else {

                    $etsyQueryString = array(

                        'listing_id' => $listing_id,

                        'listing_image_id' => $prodImg['etsy_image_id'],

                        'rank' => $i,

                        'overwrite' => 0

                    );

                }



                $settings = $this->config->get('etsy_general_settings');

                $access_token = $this->config->get('etsy_access_token');

                $access_token_secret = $this->config->get('etsy_access_token_secret');

                $etsyClient = new oauth_client_class;

                $etsyClient->server = 'Etsy';

                $etsyClient->debug = false;

                $etsyClient->debug_http = true;



                $etsyClient->client_id = $settings['etsy_api_key'];

                $etsyClient->client_secret = $settings['etsy_api_secret'];

                $etsyClient->scope = 'email_r listings_w listings_r transactions_r transactions_w';



                $etsyClient->access_token = $access_token;

                $etsyClient->access_token_secret = $access_token_secret;

                $etsyRequestURI = '/listings/' . $listing_id . '/images/';

                $etsyRequestMethod = 'POST';

                $etsyResponse = '';

                if ($etsySuccess = $etsyClient->Initialize()) {

                    $etsySuccess = $etsyClient->CallAPI('https://openapi.etsy.com/v3/application' . $etsyRequestURI, $etsyRequestMethod, $etsyQueryString, array('FailOnAccessError' => true, 'Files' => array('image' => array('ContentType' => 'image/jpeg'))), $etsyResponse);

                    $etsySuccess = $etsyClient->Finalize($etsySuccess);

                }

                $etsyResponse = json_decode(json_encode($etsyResponse), true);



                if (isset($etsyResponse['error'])) {

                    $this->syncError = true;

                    $this->model_jgetsy_cron->auditLogEntry($etsyResponse['error'], $this->syncMethod);

                    $this->model_jgetsy_product->updateListingAddErrorStatus($product_id, $etsyResponse['error']);

                } elseif (isset($etsyResponse['results'])) {

                    $this->db->query("UPDATE " . DB_PREFIX . "etsy_images SET update_flag = '0', etsy_image_id = '" . $etsyResponse['results'][0]['listing_image_id'] . "' WHERE image_id = '" . $prodImg["image_id"] . "'");

                }

                $i++;

            }

            sleep(1); //Sleep job to avoid exceed limit rate

        }



        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();

        if (!empty($productImage['delete'])) {

            foreach ($productImage['delete'] as $image_delete) {

                $etsyQueryString = array(

                    'listing_id' => (string) $listing_id,

                    'listing_image_id' => $image_delete['etsy_image_id']

                );

                $delete_response = $etsyMain->sendRequest("deleteListingImage", array('params' => $etsyQueryString));

                if (isset($delete_response['error'])) {

                    $this->syncError = true;

                    $this->model_jgetsy_cron->auditLogEntry($delete_response['error'], $this->syncMethod);

                } else if (isset($delete_response['results'])) {

                    $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_images WHERE image_id = '" . $image_delete["image_id"] . "'");

                }

            }

        }



        $logEntry = 'Sync Product Images Completed. Product ID:' . $product_id;

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);



        return true;

    }



    private function compare_main_image($a, $b) {

        if ($a == $b) {

            return 0;

        }

        return ($a['main_image'] > $b['main_image']) ? -1 : 1;

    }



    private function syncTransations($product_id, $listing_id) {

        $this->syncMethod = 'SyncTranslation';

        $logEntry = 'Sync Product Translation Started. Product ID:' . $product_id;

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);



        $this->load->model('jgetsy/cron');

        $this->load->model('jgetsy/product');



        $listing_translations = $this->prepareTranslation($product_id, $listing_id);

        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();



        if (!empty($listing_translations)) {

            foreach ($listing_translations as $listing_translation) {

                $get_listing_param = array(

                    "listing_id" => $listing_id,

                    "language" => $listing_translation['language']

                );

                $results = $etsyMain->sendRequest("getListingTranslation", array('params' => $get_listing_param));

                /* if title is blank then create transation otherwise update translation */

                if (isset($results[0]['results']['title']) == "") {

                    $listing_response = $etsyMain->sendRequest("createListingTranslation", array('params' => $listing_translation, "data" => $listing_translation));

                } else {

                    $listing_response = $etsyMain->sendRequest("updateListingTranslation", array('params' => $listing_translation, "data" => $listing_translation));

                }

            }

        }

        $logEntry = 'Sync Product Translation Completed. Product ID:' . $product_id;

        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

    }



    private function prepareTranslation($product_id, $listing_id) {

        $listingArray = array();

        $sync_languages = $this->getLanguagesToSync();

        if (!empty($sync_languages)) {

            $i = 0;

            foreach ($sync_languages as $sync_language) {

                $productDetails = $this->model_jgetsy_product->getProductByProductId($product_id, $sync_language['language_id']);

                $listingArray[$i]['listing_id'] = (string) $listing_id;

                $listingArray[$i]['language'] = $sync_language['code'];

                $listingArray[$i]['title'] = $productDetails['name'];

                $listingArray[$i]['description'] = $this->filterDescription($productDetails['description']);

                $tagArrayUpdated = $this->productTags($productDetails['tag']);

                if (!empty($tagArrayUpdated)) {

                    $listingArray[$i]['tags'] = implode(",", $tagArrayUpdated);

                }

                $i++;

            }

        }

        return $listingArray;

    }



    private function getLanguagesToSync() {

        $sync_langauge = array();

        $this->load->model('localisation/language');

        $settings = $this->config->get('etsy_general_settings');

        if(!empty($settings['etsy_languages_to_sync'])) {

            $etsy_languages_to_sync = $settings['etsy_languages_to_sync'];

            if (count($etsy_languages_to_sync) > 0) {

                foreach ($etsy_languages_to_sync as $etsy_language) {

                    /* Skip the default language */

                    if ($settings['etsy_default_language'] != $etsy_language) {

                        $lang_data = $this->model_localisation_language->getLanguage($etsy_language);

                        $lang_code_array = explode("-", $lang_data['code']);

                        $sync_langauge[] = array("code" => $lang_code_array[0], "language_id" => $lang_data['language_id']);

                    }

                }

            }

        }

        return $sync_langauge;

    }



    public function getActiveListing($request_offset = 0) {

        @ini_set('memory_limit', -1);

        @ini_set('max_execution_time', -1);

        @set_time_limit(0);



        $this->load->model('jgetsy/cron');

        $this->load->model('jgetsy/product');

        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();

        $params = array("shop_id" => "14389944", "limit" => "10", "offset" => (int) $request_offset);

        

        //$result = $etsyMain->sendRequest("findAllActiveListingsByShop", array('params' => $params, 'data' => $params));

        if (isset($result['error'])) {

            

        } else {

            if (!empty($result['pagination']['next_offset'])) {

                //$this->getActiveListing($result['pagination']['next_offset']);

            }

        }

    }



}

