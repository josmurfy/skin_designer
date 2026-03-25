<?PHP

namespace Opencart\Admin\Model\Shopmanager;
//use GuzzleHttp\Client;
//use GuzzleHttp\GuzzleHttp\Exception\RequestException;

class Ai extends \Opencart\System\Engine\Model {

    //private $apiKey = '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';
    private $apiKey = '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';

    private function sendOpenAiRequest($user_prompt, $systemPrompt, $maxTokens, $temperature, $output_json_needed = '') {

        //print("<pre>".print_r('13:AI', true)."</pre>");
        //print("<pre>".print_r($user_prompt, true)."</pre>");

        $systemPrompt .= " The output MUST be a valid JSON object with double quotes around all keys and values.";
        $systemPrompt .= " If values include double quotes or single quotes, escape them properly.";
        $systemPrompt .= " DO NOT return any explanation, only the JSON object.";
        $systemPrompt .= !empty($output_json_needed) ? " Return the result as a properly formatted JSON object like " . $output_json_needed : '';
        //print("<pre>".print_r('13:AI', true)."</pre>");
        //print("<pre>".print_r($user_prompt, true)."</pre>");

        $client = new \GuzzleHttp\Client();
        $apiEndpoint = 'https://api.openai.com/v1/chat/completions';

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ];

        $postData = [
            'model' => 'gpt-5.4-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $user_prompt]
            ],
            'temperature' => $temperature
        ];

        try {
            $responseData = $client->post($apiEndpoint, [
                'headers' => $headers,
                'json' => $postData
            ]);

            $responseData = json_decode($responseData->getBody(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from OpenAI');
            }

            return $responseData;

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorBody = '';
            if ($e->hasResponse()) {
                $errorBody = $e->getResponse()->getBody()->getContents();
                $logFile = DIR_LOGS . 'openai_error.log';
                file_put_contents($logFile, date('Y-m-d H:i:s') . ' - OpenAI API Error Response: ' . $errorBody . "\n", FILE_APPEND);
            }
            $logFile = DIR_LOGS . 'openai_error.log';
            file_put_contents($logFile, date('Y-m-d H:i:s') . ' - Request failed: ' . $e->getMessage() . "\n", FILE_APPEND);
            throw new \Exception('Request to OpenAI failed: ' . $e->getMessage() . ($errorBody ? ' | Response: ' . substr($errorBody, 0, 200) : ''));
        } catch (\Exception $e) {
            $logFile = DIR_LOGS . 'openai_error.log';
            file_put_contents($logFile, date('Y-m-d H:i:s') . ' - General Error: ' . $e->getMessage() . "\n", FILE_APPEND);
            throw new \Exception('OpenAI API Error: ' . $e->getMessage());
        }
    }
  
    private function extractContent($responseData) {
        if (isset($responseData['choices'][0]['message']['content'])) {
            $content = trim($responseData['choices'][0]['message']['content']);
           // $content = preg_replace('/^```json\s*|\s*```$/', '', $content);
            $content = preg_replace('/^```(json)?\s*/', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
            $content = trim($content);

            // Supprimer les caractères invisibles ou utf8 malformés
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
    
            // Tentative de décodage
            $decodedJson = json_decode($content, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                // ❗ JSON invalide → tenter de le corriger via OpenAI
                $decodedJson = $this->repairInvalidJson($content);
            }
    
            // Si la réparation a fonctionné
            if (is_array($decodedJson)) {
                return $decodedJson;
            } else {
                if (is_array($content)) {
                    return $content;
                } else {
                    return $this->repairByJsonAI($content);
                }
                //print("<pre>".print_r($content, true)."</pre>");   
                //throw new \Exception('❌ Échec de la réparation JSON. Contenu original : ' . $content . "Erreur : " . json_last_error_msg());
            }
        } else {
            throw new \Exception('❌ Aucun contenu trouvé dans la réponse OpenAI.');
        }
    }
    private function repairByJsonAI($content) {
        $system_prompt = "You are a professional JSON fixer. Given a broken or invalid JSON string, your task is to correct it and return a valid JSON object or array. Do not include any explanation or formatting. Just return pure JSON.";
        
        $user_prompt = <<<EOD
    The following JSON is invalid or broken. Fix and return a valid JSON:
    
    $content
    
    - Return only a valid JSON object or array.
    - No explanations, no comments, just clean JSON.
    EOD;
    
        try {
            $response = $this->sendOpenAiRequest($user_prompt, $system_prompt, 1000, 0);
            
            if (isset($response['choices'][0]['message']['content'])) {
                $fixed = trim($response['choices'][0]['message']['content']);
                $fixed = preg_replace('/^```(json)?\s*/', '', $fixed);
                $fixed = preg_replace('/\s*```$/', '', $fixed);
                $fixed = trim($fixed);
    
                // Tentative de décodage
                $decoded = json_decode($fixed, true);
    
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                } else {
                    // Log si JSON invalide
                    //error_log("❌ repairByJsonAI: JSON remains invalid after repair attempt. Erreur: " . json_last_error_msg());
                    //error_log("Contenu réparé tenté: " . $fixed);
                }
            }
        } catch (\Exception $e) {
            error_log("❌ Exception in repairByJsonAI: " . $e->getMessage());
        }
    
        return null; // Rien n’a fonctionné
    }
    
    
    public function prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature, $jsonData = '', $json_name = '') {
        
        $this->load->model('shopmanager/tools');
        $output_json_needed = (!empty($json_name))?$this->model_shopmanager_tools->clearArrayValuesAndReturnPrettyJson($jsonData,  $json_name):''; // Convertit en tableau PHP pour s'assurer que c'est un JSON valide

        $responseData = $this->sendOpenAiRequest($user_prompt, $system_prompt, $max_tokens, $temperature, $output_json_needed);
        //print("<pre>".print_r('74:AI', true)."</pre>");
       //print("<pre>".print_r($responseData, true)."</pre>");
        return $this->extractContent($responseData);
    }

    private function repairInvalidJson($content) {
        $system_prompt = "You are a JSON fixer. Your task is to take malformed JSON and return valid, properly escaped JSON.";
        $user_prompt = "The following JSON is invalid: " . $content . ". Please return a fixed version as a valid JSON object. Do not explain anything, return only the corrected JSON.";
    
        $max_tokens = 1000;
        $temperature = 0;
    
        $repaired = $this->sendOpenAiRequest($user_prompt, $system_prompt, $max_tokens, $temperature);
    
        // On tente de l'extraire
        if (isset($repaired['choices'][0]['message']['content'])) {
            $fixed = trim($repaired['choices'][0]['message']['content']);
            $fixed = preg_replace('/^```json\s*|\s*```$/', '', $fixed);
            $decoded = json_decode($fixed, true);
    
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
    
        return null; // Échec de la réparation
    }
        public function prompt_ai_image($user_prompt) { 
       
        $apiEndpoint = 'https://api.openai.com/v1/images/generations';
 
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . 'Bearer ' . $this->apiKey,
        ];
    
        $postData = [
            'model'=> 'dall-e-3',
            'prompt' => $user_prompt,
            'n' => 1,
            'size' => '1024x1024'
        ];
        
        // Log request for debugging
        $logFile = DIR_LOGS . 'openai_error.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' - DALL-E Request: ' . substr($user_prompt, 0, 200) . "...\n", FILE_APPEND);
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $responseData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            file_put_contents($logFile, date('Y-m-d H:i:s') . ' - DALL-E CURL Error: ' . $error . "\n", FILE_APPEND);
            return null;
        }
    
        $responseData = json_decode($responseData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . ' - DALL-E JSON Error: ' . json_last_error_msg() . "\n", FILE_APPEND);
            return null;
        }
        
        if ($httpCode !== 200) {
            $errorMsg = isset($responseData['error']['message']) ? $responseData['error']['message'] : 'Unknown error';
            $errorCode = isset($responseData['error']['code']) ? $responseData['error']['code'] : 'no_code';
            file_put_contents($logFile, date('Y-m-d H:i:s') . ' - DALL-E API Error (HTTP ' . $httpCode . ', Code: ' . $errorCode . '): ' . $errorMsg . "\n", FILE_APPEND);
            
            // Log full response for debugging
            file_put_contents($logFile, date('Y-m-d H:i:s') . ' - Full Response: ' . json_encode($responseData) . "\n", FILE_APPEND);
            return null;
        }
    
        if (isset($responseData['data'][0]['url'])) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . ' - DALL-E Success: Image generated\n', FILE_APPEND);
            return $responseData['data'][0]['url'];
        } else {
            file_put_contents($logFile, date('Y-m-d H:i:s') . ' - DALL-E No URL in response: ' . json_encode($responseData) . "\n", FILE_APPEND);
            return null;
        }
    }
    


    public function translate_specifics($product_id = null, $product_specific_info = null, $language = null)
    {
        $this->load->model('localisation/language');
        $this->load->model('shopmanager/catalog/product');
        $this->load->model('shopmanager/translate');
        $this->load->model('shopmanager/catalog/product_specific');
      //print("<pre>".print_r ($product_specific_info,true )."</pre>");
        $execution_times = [];
        $n = 0;
        //$start_time = microtime(true);
    
        $exclude = ['Brand', 'Movie/TV Title', 'Record Label', 'Release Title', 'LEGO Set Name', 'Studio', 'Actor', 'Director', 'Franchise', 'Music Artist', 'Producer'];
    
        foreach ($product_specific_info as $key => $data_value) {
            unset($product_specific_info[$key]['specific_info'], $data_value['specific_info']);
    
            // **Traduction du champ 'Name'**
            $translated_term = $this->model_shopmanager_catalog_product_specific->findtranslated_term($data_value['Name'] ?? '');
            $product_specific_info[$key]['Name'] = $translated_term ?? $this->model_shopmanager_translate->translate($data_value['Name'], $language['code']);
    
            //$execution_times[($n++).'_Chargement line:'. __LINE__] = round(microtime(true) - $start_time, 2);
            //$start_time = microtime(true);
    
            // Vérifier si la spécificité doit être traduite
            if (isset($data_value['Name']) && !in_array($data_value['Name'], $exclude)) {
                if (isset($data_value['Value'])) {
                    // **Gestion du cas où 'Value' est un tableau**
                    if (is_array($data_value['Value'])) {
                        if (!(count($data_value['Value']) == 1 && (trim($data_value['Value'][0]) == '' || is_numeric($data_value['Value'][0])))) {
                            $bypassTranslate = $this->model_shopmanager_translate->bypassTranslate($key, json_encode($data_value['Value']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            
                            if (!isset($bypassTranslate)) {
                                $json_translate = $this->model_shopmanager_translate->translate(json_encode($data_value['Value'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $language['code']);
    
                                if (count($data_value['Value']) == 1) {
                                    // **Nettoyage des guillemets français et autres caractères**
                                    $json_translate = str_replace(['«', '»', '[', ']'], '', trim($json_translate));
                                    $json_translate = $this->db->escape($json_translate);
                                    $json_translate = preg_replace('/^\s+|\s+$/u', '', $json_translate);
                                    $json_translate = '["' . $json_translate . '"]'; 
                                }
    
                                $product_specific_info[$key]['Value'] = json_decode(html_entity_decode($json_translate), true);
                            } else {
                                $product_specific_info[$key]['Value'] = json_decode($bypassTranslate, true);
                            }
                        } else {
                            $product_specific_info[$key]['Value'] = $data_value['Value'];
                        }
                    } else {
                        // **Gestion du cas où 'Value' est une chaîne**
                        $bypassTranslate = $this->model_shopmanager_translate->bypassTranslate($key, trim($data_value['Value']));
                        if (!isset($bypassTranslate)) {
                            $product_specific_info[$key]['Value'] = $this->model_shopmanager_translate->translate(trim($data_value['Value']), $language['code']);
                        } else {
                            $product_specific_info[$key]['Value'] = $bypassTranslate;
                        }
                    }
                }
            }
    
            //$execution_times[($n++).'_Chargement line:'. __LINE__] = round(microtime(true) - $start_time, 2);
            //$start_time = microtime(true);
        }
    
        // **Sauvegarde dans la base de données si un product_id est fourni**
        if (isset($product_id)) {
            $this->model_shopmanager_catalog_product->editSpecifics($product_id, json_encode($product_specific_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $language['language_id']);
        }
    
        // **Affichage du temps d'exécution total**
        $total_execution_time = array_sum($execution_times);
        // echo "Temps total d'exécution : " . $total_execution_time . " secondes";
    
        return $product_specific_info;
    }
    

    public function getProductSpecific($product_info = null, $aspectName = null, $aspectValues = null, $aspectConstraint = null, $source_value = null) {
        $this->load->model('shopmanager/tools');
        $this->load->model('shopmanager/catalog/category');
        $this->load->model('shopmanager/catalog/product');
        $product_description = $this->model_shopmanager_catalog_product->getDescriptions($product_info['product_id']);
        //print("<pre>".print_r($product_info, true)."</pre>");
        $language_id=1;
        // Extraction des informations principales
        $title = $product_description[$language_id]['name'] ?? '';
        $category_id = $product_info['category_id'] ?? '';
        $condition_name = isset($product_description[$language_id]['condition_name']) 
        ? ' and condition_name:' . $product_description[$language_id]['condition_name'] : '';

        $condition_supp = isset($product_description[$language_id]['condition_supp']) 
        ? ' and condition_supp:' . $product_description[$language_id]['condition_supp'] : '';

        $manufacturer = isset($product_info['manufacturer']) && !is_array($product_info['manufacturer']) 
        ? ' and manufacturer:' . $product_info['manufacturer'] : '';

        $model = isset($product_info['model']) ? ' and model:' . $product_info['model'] : '';

        $color = isset($product_description[$language_id]['color']) ? ' and color:' . $product_description[$language_id]['color'] : '';
    
        // Traitement de la source value
        if ($source_value) {
            $s_value = json_decode($source_value, true);
            $source_value = isset($s_value[$aspectName]) ? 
                (is_array($s_value[$aspectName]) 
                ? implode(',', $this->model_shopmanager_tools->flattenArray($s_value[$aspectName])) : $s_value[$aspectName]) 
                : null;
            
            $source_title = $source_value ? " ({$aspectName}: " . str_replace('2.35:1', '16:9', $source_value) . ')' : '';
            $title = $source_value ? ($s_value['Movie/TV Title'] ?? $title) : $title;
        } else {
          
            $source_title = '';
        }
    
        // Construction du prompt utilisateur
        if ($category_id == '617') {
            $user_prompt = "Movies Title: ".$title. $source_title." ".$condition_name." ".$condition_supp.". ";
        } elseif ($category_id == '139973') {
            $user_prompt = "Video Games Title: ".$title. $source_title." ".$condition_name." ".$condition_supp.". ";
        }else {
            $user_prompt = "Product title: ".$title." ".$condition_name." ".$color." ".$manufacturer." ".$model." ".$condition_supp.". ";
        }
        if(isset($source_value)){
            $s_value=json_decode($source_value,true);
            $source_value = isset($s_value[$aspectName]) 
            ? (is_array($s_value[$aspectName]) 
                ? implode(',', $this->model_shopmanager_tools->flattenArray($s_value[$aspectName])) 
                : $s_value[$aspectName]) 
            : null;
            $source = isset($source_value)?' '.$source_value. ',  ':'';
          
        }else{
            $source = null;
        }

        if (!empty($aspectValues)) {
            $values = array_map(fn($val) => $val['localizedValue'], $aspectValues);
            $user_prompt .= "Choose the most accurate and concise value for ".$aspectName." from the following options:" . json_encode(array_merge([$source], $values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } else {
            $user_prompt .= "Choose the most accurate and concise value for ".$aspectName.($source?" search accurate in :  ".$source:'').". Reply with 'none' if not applicable.";
        }
    
        // Ajout des contraintes spécifiques eBay
        $specifics_for_prompt = [];
        
        $categories_data = $this->model_shopmanager_catalog_category->getSpecific($category_id, $language_id);
        $specific_info = $categories_data['specifics'] ?? [];
        if (isset($specific_info['specific_info'])) {
            $specifics_for_prompt[] = $this->getAspectConstraint($specific_info, $aspectName, $condition_name);
        }

           // Convertir en texte unique pour le prompt
           $specifics_text = implode(",", $specifics_for_prompt);
             
    
        $user_prompt .= ". Here are the constraints for eBay category specifics: " . $specifics_text .
            ". Ensure the following rules:"
            . "\n- If the aspect mode is 'SELECTION_ONLY', ONLY ONE VALUE ALLOWED inside an array."
            . "\n- If the aspect mode is 'FREE_TEXT' and cardinality is 'SINGLE', ONLY ONE VALUE ALLOWED inside an array."
            . "\n- Remove duplicate and similar values while keeping only the most relevant ones."
            . "\n- For aspects with predefined values, always keep the most relevant ones from the given examples."
            . "\n- Ensure logical consistency: for example, 'Release Year' should contain only one valid year."
            . "\n- Separate concatenated words into properly formatted terms (e.g., 'USBTypeCGen2FastCharge' → ['USB Type-C', 'Gen 2', 'Fast Charge'])."
            . "\n- Do not add new keys, just refine the existing data."
            . ". Return only a valid JSON object with correct formatting and no additional text.";
    
        // Ajout du system prompt
        $system_prompt = "You are an AI specialized in product classification and eBay data refinement.  ";
        $system_prompt .= "Analyze the given product title and determine the most relevant values for the requested aspect.  ";
        $system_prompt .= "Ensure accuracy, conciseness, and relevance.  ";
        $system_prompt .= "Return ONLY a valid JSON object with the refined aspect values, formatted correctly as an array:";
        $system_prompt .= '{ "values": ["Value 1", "Value 2"] }.  ';
    
        // Appel à l'IA
        $temperature = 0.3;
        //$max_tokens = 16385 - 100;
        $max_tokens = $this->countTokensOpenAI($system_prompt) + $this->countTokensOpenAI($user_prompt);  // Adjust token limit as needed
        
        try {
            $responseData = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature);
        } catch (\Exception $e) {
            // Log the error and return 'none' instead of crashing
            error_log('AI API Error in getProductSpecific: ' . $e->getMessage());
            return 'none';
        }
    
        // 🔹 Extraction de la réponse sous forme d'array JSON
        $suggestion = isset($responseData['values']) && is_array($responseData['values']) && !empty($responseData['values']) 
            ? trim($responseData['values'][0])  // Prendre la première valeur uniquement et trim()
            : 'none';
    
        // 🔹 Suppression des phrases parasites
        $unwantedPhrases = [
            'As an AI', 'not provided', 'not applicable', 'Check the product packaging',
            'specific model numbers for products', 'access to real-time databases',
            'The most accurate and concise value for', 'Unit: ', strtolower($aspectName)
        ];
        foreach ($unwantedPhrases as $phrase) {
            if (stripos($suggestion, $phrase) !== false) {
                $suggestion = 'none';
            }
        }
    
        // 🔹 Vérification des contraintes
        if (!empty($aspectValues)) {
            $allowedValues = array_map(fn($val) => $val['localizedValue'], $aspectValues);
            if (!in_array($suggestion, $allowedValues)) {
                $suggestion = 'none';
            }
        }
    
        if (isset($aspectConstraint['aspectMaxLength'])) {
            $suggestion = substr($suggestion, 0, $aspectConstraint['aspectMaxLength']);
        }
    
        if (isset($aspectConstraint['aspectDataType']) && $aspectConstraint['aspectDataType'] === 'NUMBER' && !is_numeric($suggestion)) {
            return 'AI error: invalid number';
            //return 'none';
        }
    
        // 🔹 Cas spécifiques pour la catégorie 617 (DVD/Blu-ray)
        if ($category_id == '617' && in_array(strtolower($aspectName), ['language', 'subtitle language'])) {
            if (!preg_match('/\benglish\b/i', $suggestion)) { // Vérifie "English" en mot entier, insensible à la casse
                $suggestion = ($suggestion === 'none' || $suggestion === '' || empty(trim($suggestion))) ? 'English' : "English, $suggestion";
            }
        }
    
        return trim($suggestion);
    }
    
    


  
   
    public function cleanSpecifics($specifics_to_clean) {
        //print("<pre>".print_r('362')."</pre>");
        //print("<pre>".print_r($specifics_to_clean, true)."</pre>");
        $specifics=[];
        if(isset($specifics_to_clean)){
             foreach($specifics_to_clean as $name=>$specific_info){
                 $specifics[$name]=$specific_info['Value'];
                 
             }
        }
        $specifics_json = json_encode($specifics, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        //print("<pre>".print_r('371')."</pre>");
        //print("<pre>".print_r($specifics, true)."</pre>");
       
       //print("<pre>".print_r('709')."</pre>");
        //print("<pre>".print_r($specifics, true)."</pre>");
       // $tokenCount=$this->countTokensOpenAI(json_encode($specifics));
      //print("<pre>".print_r('tokenCount:'.$tokenCount, true)."</pre>");
     
      
     //print("<pre>" . print_r($specifics_json, true) . "</pre>");
        $system_prompt = "You are an expert in JSON cleaning and data optimization.";
        $user_prompt = "Here is a JSON containing duplicate and similar values: " . 
    $specifics_json . 
    ". Please clean this JSON by removing duplicate and similar values while keeping the most relevant ones."
    . " If multiple values are equally relevant, group them into an ARRAY() before converting back to JSON."
    . " Additionally, if a value contains delimiters such as ',', ';', '/', '|', '-', ':', ' x ', '*', or other common separators, analyze whether it represents multiple distinct and not similare values."
    . " If so, split them into an array, removing any unnecessary spaces or duplicates."
    . " Ensure the output is a valid JSON object with proper double quotes, following the correct JSON format, and nothing else.";

    $user_prompt .= " Always split values containing these delimiters into an array, even if there's only one resulting value.";



    $system_prompt = "You are an expert in JSON cleaning, data optimization, and logical data processing.";

    $user_prompt = "Here is a JSON containing duplicate and similar values: " . 
    $specifics_json . 
    ". Please clean this JSON by removing duplicate and similar values while keeping the most relevant ones."
    . " If multiple values are equally relevant, group them into an ARRAY() before converting back to JSON."
    . " Additionally, if a value contains delimiters such as ',', ';', '/', '|', '-', ':', ' x ', '*', or other common separators, analyze whether it represents multiple distinct and non-similar values."
    . " If so, split them into an array, removing any unnecessary spaces or duplicates."
    . " Ensure the output is a valid JSON object with proper double quotes, following the correct JSON format, and nothing else.";

    $user_prompt .= " Always split values containing these delimiters into an array, even if there's only one resulting value."
    . " Also, detect words that are UNUSUAL and NOT naturally spaced (e.g., 'Dolbydigital2.0mono', 'USBTypeCGen2FastCharge') and separate them into proper terms with correct spacing."
    . " If a term appears to be a combination of multiple words without spaces, analyze its structure and split it accordingly."
    . " Example: 'Dolbydigital2.0mono' should be reformatted as ['Dolby Digital 2.0', 'Mono'], and 'USBTypeCGen2FastCharge' as ['USB Type-C', 'Gen 2', 'Fast Charge']." 
    . " Do not assume a specific industry or domain when applying this rule, but ensure that terms remain logically formatted."
    . " Return only a valid JSON object with proper double quotes, following correct JSON formatting, and nothing else.";


    $user_prompt .= " Furthermore, ensure logical consistency in data interpretation."
    . " For example, 'Release Year' should contain only one valid year, not multiple."
    . " If multiple years are present, choose the most relevant one based on the latest available date or the most frequently occurring value."
    . " Do not return an array for fields that should logically contain a single value."
    . " Maintain logical integrity for all other fields where a single value is expected.";


       $max_tokens = $this->countTokensOpenAI($system_prompt) + $this->countTokensOpenAI($user_prompt);  // Adjust token limit as needed
        $temperature = 0.2;  // Lower temperature for more deterministic results
      
      //print("<pre>" . print_r($max_token_result, true) . "</pre>");
        // Call OpenAI API
        //$cleaned_json = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature);
        //print("<pre>" . print_r($cleaned_json, true) . "</pre>");
        // Ensure response is valid JSON
        $response = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature, $specifics_json, 'specifics');
        //print("<pre>" . print_r($response, true) . "</pre>");
        if (json_last_error() === JSON_ERROR_NONE) {
            if(isset($response['specifics'])){
                //print("<pre>".print_r('401')."</pre>");
                //print("<pre>".print_r($response, true)."</pre>");
                $specifics_cleaned=[];
                foreach($response['specifics'] as $name=>$specific_info){
                    $specifics_cleaned[$name]['Name']=$name;
                    $specifics_cleaned[$name]['Value']=is_string($specific_info)?[$specific_info]:$specific_info;
                    $specifics_cleaned[$name]['VerifiedSource']=isset($specifics_to_clean[$name]['VerifiedSource'])?$specifics_to_clean[$name]['VerifiedSource']:'';
                }
             //print("<pre>".print_r('719')."</pre>");
             //print("<pre>".print_r($specifics_cleaned, true)."</pre>");
            }else{
                $specifics_cleaned=[];
                foreach($response as $name=>$specific_info){
                    $specifics_cleaned[$name]['Name']=$name;
                    $specifics_cleaned[$name]['Value']=is_string($specific_info)?[$specific_info]:$specific_info;
                    $specifics_cleaned[$name]['VerifiedSource']=isset($specifics_to_clean[$name]['VerifiedSource'])?$specifics_to_clean[$name]['VerifiedSource']:'';
                }
            }
            return $specifics_cleaned; // Return cleaned JSON
        } else {
            return NULL;// ["error" => "Invalid response from AI"];
        }
    }

    private function getAspectConstraint($specific_info = [],$specificName = '', $condition = 'Very Good') {

        if (isset($specific_info['specific_info'])) {
                

                $aspectConstraint = $specific_info['specific_info']['aspectConstraint'] ?? [];
                        
                $cardinality = $aspectConstraint['itemToAspectCardinality'] ?? 'SINGLE';
                $aspectMode = isset($aspectConstraint['aspectMode']) ? str_replace('_', ' ', $aspectConstraint['aspectMode']) : 'FREE TEXT';

                $isRequired = !empty($aspectConstraint['aspectRequired']) ? 'and is required.' : '';
            
                // Exclure 'Country/Region of Manufacture' mais expliquer son importance
            /* if ($specificName === 'Country/Region of Manufacture') {
                    $specifics_for_prompt[] = "The aspect '$specificName' contains a large number of country names, so instead of listing all possible values, ensure that it follows the correct format.";
                    continue;
                }*/
            
                // Vérifier si aspectValues existe et est un tableau, sinon générer avec OpenAI
                if (isset($specific_info['specific_info']['aspectValues']) && is_array($specific_info['specific_info']['aspectValues'])) {
                    $aspectValues = array_slice($specific_info['specific_info']['aspectValues'], 0, 15);
                    $valueList = implode(", ", array_map(fn($v) => $v['localizedValue'] ?? '', $aspectValues));
                } else {
                    // OpenAI doit générer une suggestion si aucune valeur prédéfinie n'est trouvée
                // $user_prompt = "The aspect '$specificName' has no predefined values. Based on the context, generate a list of relevant possible values.";
                // $system_prompt = "You are an AI assistant helping to determine missing product specifics. Return only a list of relevant values separated by commas.";
                    //$valueList = $this->prompt_ai($user_prompt, $system_prompt, 50, 0.7);
            
                    // Assurer qu'on obtient une valeur propre
                    /*if (empty($valueList) || strpos(strtolower($valueList), 'unknown') !== false) {
                        $valueList = "No predefined values available.";
                    }*/

                    $valueList = "(has no predefined values. Based on the context, generate a list of relevant possible values.)";
                }
            
                // Générer la description selon les contraintes
                $aspectDetails = trim("$cardinality $aspectMode");
                $specificNameUpper = strtoupper($specificName);
            
            // Définir les règles dynamiques pour chaque terme spécifique
                $specialCases = [
                    'country' => 'full country name',
                    'date' => 'full date in US format (MM-DD-YYYY)',
                    'year' => 'four-digit year',
                    'grading' => 'Grading is '.$condition,
                    'condition' => 'Condition is '.$condition,
                    'season' => 'If this DVD or Bluray is a TV series, the season number must be specified',

                    // Ajoute d'autres règles ici si nécessaire...
                ];

                // Vérifier si le nom de l'aspect contient l'un des termes spéciaux
                $matchingTerm = null;
                foreach ($specialCases as $term => $requirement) {
                    if (strpos(strtolower($specificName), $term) !== false) {
                        $matchingTerm = $requirement;
                        break; // Arrêter dès qu'on trouve une correspondance
                    }
                }

                // Définir la description en fonction du type d'aspect
                if ($aspectDetails === 'SINGLE SELECTION ONLY') {
                    if ($matchingTerm) {
                        $description = "The aspect '$specificNameUpper' requires the $matchingTerm, not abbreviations ONLY ONE VALUE ALLOWED ";
                    } else {
                        $description = "The aspect '$specificNameUpper' requires a single selection from predefined values $isRequired ";
                        $description .= "You must choose one of the following values: [$valueList].";
                    }
                } elseif ($aspectDetails === 'SINGLE FREE TEXT') {
                    if ($matchingTerm) {
                        $description = "The aspect '$specificNameUpper' requires the $matchingTerm as a free-text entry ONLY ONE VALUE ALLOWED ";
                    } else {
                        $description = "The aspect '$specificNameUpper' allows only a single free-text entry $isRequired ";
                    }
                } elseif ($aspectDetails === 'MULTIPLE SELECTION ONLY') {
                    if ($matchingTerm) {
                        $description = "The aspect '$specificNameUpper' requires multiple selections formatted as $matchingTerm. $isRequired ";
                    } else {
                        $description = "The aspect '$specificNameUpper' allows multiple selections from predefined values $isRequired ";
                        $description .= "Examples of valid values: [$valueList].";
                    }
                } elseif ($aspectDetails === 'MULTIPLE FREE TEXT') {
                    if ($matchingTerm) {
                        $description = "The aspect '$specificNameUpper' requires multiple free-text entries formatted as $matchingTerm. $isRequired ";
                    } else {
                        $description = "The aspect '$specificNameUpper' allows multiple free-text entries $isRequired ";
                    }
                } else {
                    $description = "The aspect '$specificNameUpper' has the constraint '$aspectDetails' $isRequired ";
                    $description .= !empty($valueList) ? "Examples of valid values: [$valueList]." : "";
                }

                $description .= isset($aspectConstraint['aspectMaxLength'])?"/n ### Require values MUST be Max Length than {$aspectConstraint['aspectMaxLength']} characters  .":'';
                // Ajouter la description finale au tableau des spécificités
               
                return $description;
            }
    }
    public function feedEmptySpecifics($specifics_to_feed, $feed = null) {
       
       
        $specifics=[];
       
        $specifics_for_prompt = [];
      
        if (isset($specifics_to_feed)) {
            foreach ($specifics_to_feed as $specificName => $specific_info) {
                $specifics[$specificName]=$specific_info['Value'];
                if (isset($specific_info['specific_info'])) {
                    $specifics_for_prompt[] = $this->getAspectConstraint($specific_info,$specificName);
                }
            }

                
        }
        
        // Convertir en texte unique pour le prompt
        $specifics_text = implode(",", $specifics_for_prompt);
        $specifics_json = json_encode($specifics, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
   
    $system_prompt = "You are an expert in JSON feeding, data validation, and optimization.";

$user_prompt = "Here is a JSON containing empty or redundant values: " . $specifics_json . 
    ". Please process this JSON while ensuring the following:"
    . "\n- Remove duplicate and similar values while keeping only the most relevant ones."
    . "\n- If multiple values are equally relevant, group them into an ARRAY()."
    . "\n- Detect values containing delimiters such as ',', ';', '/', '|', '-', ':', ' x ', '*', and analyze whether they represent multiple distinct values."
    . "\n- If they do, split them into an array, removing unnecessary spaces and duplicates."
    . "\n- Always return a properly formatted JSON object with valid double quotes, without adding new keys."
    . ". Use this additional reference data:\n" . $feed . " to refine and enhance the JSON above.";

$user_prompt .= ". Additionally, apply the following transformations:"
    . "\n- Ensure words that are merged without spaces (e.g., 'Dolbydigital2.0mono', 'USBTypeCGen2FastCharge') are split into properly formatted terms."
    . "\n  - Example: 'Dolbydigital2.0mono' → ['Dolby Digital 2.0', 'Mono']"
    . "\n  - Example: 'USBTypeCGen2FastCharge' → ['USB Type-C', 'Gen 2', 'Fast Charge']"
    . "\n  - Do not assume a specific industry but maintain logical formatting."
    . "\n- Fields that should logically contain a single value (e.g., 'Release Year') must not be converted into an array."
    . "\n  - If multiple years are present, choose the most relevant one based on the latest available date or the most frequently occurring value."
    . "\n- Ensure the logical consistency of all fields and do not return unnecessary arrays.";

$user_prompt .= ". Here are the constraints for eBay category specifics: " . $specifics_text .
    ". Ensure the following rules:"
    . "\n- If the aspect mode is 'SELECTION_ONLY', ONLY ONE VALUE ALLOWED inside an array."
    . "\n- If the aspect mode is 'FREE_TEXT' and cardinality is 'SINGLE', ONLY ONE VALUE ALLOWED inside an array."
    //. "\n- Do not include 'Country/Region of Manufacture' values, just format it correctly."
    . "\n- Remove duplicate and similar values while keeping only the most relevant ones."
    . "\n- For aspects with predefined values, always keep the most relevant ones from the given examples."
    . "\n- Ensure logical consistency: for example, 'Release Year' should contain only one valid year."
    . "\n- Separate concatenated words into properly formatted terms (e.g., 'USBTypeCGen2FastCharge' → ['USB Type-C', 'Gen 2', 'Fast Charge'])."
    . "\n- Do not add new keys, just refine the existing data.";
   // . ". Return only a valid JSON object with correct formatting and no additional text.";

    
       $max_tokens = $this->countTokensOpenAI($system_prompt) + $this->countTokensOpenAI($user_prompt);  // Adjust token limit as needed
        $temperature = 0.2;  // Lower temperature for more deterministic results
      
      //print("<pre>" . print_r($max_token_result, true) . "</pre>");
        // Call OpenAI API
        //$cleaned_json = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature);

        $response = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature, $specifics_json, 'specifics');
       //print("<pre>" . print_r($response, true) . "</pre>");
     
        if(isset($response['specifics'])){
            //print("<pre>".print_r('401')."</pre>");
            //print("<pre>".print_r($response, true)."</pre>");
            $specifics_feed=[];
            foreach($response['specifics'] as $name=>$specific_info){
                $specifics_feed[$name]['Name']=$name;
                $specifics_feed[$name]['Value']=is_string($specific_info)?[$specific_info]:$specific_info;
                $specifics_feed[$name]['VerifiedSource']=isset($specifics_to_feed[$name]['VerifiedSource'])?$specifics_to_feed[$name]['VerifiedSource']:'';
                $specifics_feed[$name]['specific_info']= $specifics_to_feed[$name]['specific_info']??'';
            }
        //print("<pre>".print_r('719')."</pre>");
            //print("<pre>".print_r($specifics_feed, true)."</pre>");
        }else{
            foreach($response as $name=>$specific_info){
                $specifics_feed[$name]['Name']=$name;
                $specifics_feed[$name]['Value']=is_string($specific_info)?[$specific_info]:$specific_info;
                $specifics_feed[$name]['VerifiedSource']=isset($specifics_to_feed[$name]['VerifiedSource'])?$specifics_to_feed[$name]['VerifiedSource']:'';
                $specifics_feed[$name]['specific_info']= $specifics_to_feed[$name]['specific_info']??'';
            }
        }
        $cleaned_specifics = [];
        if (isset($specifics_to_feed)) {
            foreach ($specifics_to_feed as $specificName => $specific_info) {
                $cleaned_specifics[$specificName]=$specifics_feed[$specificName];
            }

                
        }
            return $cleaned_specifics; // Return cleaned JSON
     
    }


       public function translate($jsonData, $target_language) {

        $this->load->model('shopmanager/tools');
		//$this->model_shopmanager_tools->debug_function_trace();
        // 🛠️ Vérification initiale
        if (empty($jsonData) || empty($target_language)) {
            return ['error' => 'Invalid input data'];
        }
      // Language name mapping for better translation quality
        $languageNames = [
            'fr' => 'French',
            'es' => 'Spanish',
            'de' => 'German',
            'it' => 'Italian',
            'en' => 'English',
            'pt' => 'Portuguese',
            'nl' => 'Dutch',
            'pl' => 'Polish',
            'ru' => 'Russian',
            'ja' => 'Japanese',
            'zh' => 'Chinese',
            'ko' => 'Korean',
            'ar' => 'Arabic',
            'tr' => 'Turkish',
            'vi' => 'Vietnamese'
        ];


        // 🔹 Construction du prompt
        $system_prompt = "You are an expert in JSON translation. Do not change any HTML tags if they exist.";
        $user_prompt  = "Here is a JSON containing English values: " . $jsonData . 
                        ". Please translate it into " . ($languageNames[$target_language] ?? $target_language) . ".  ";

        // 🔹 Appel à OpenAI
        $max_tokens = $this->countTokensOpenAI($system_prompt) + $this->countTokensOpenAI($user_prompt);
        $temperature = 0.2;  // Basse température pour une traduction plus précise
        $responseData = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature, $jsonData, 'translation');
    
        // 🛠️ Vérification et correction du JSON
        if (is_string($responseData)) {
            $responseData = html_entity_decode($responseData, ENT_QUOTES | ENT_HTML5); // Décode les entités HTML
            $responseData = json_decode($responseData, true); // Convertit en tableau PHP
            if (isset($responseData['translation'])) {
                return $responseData['translation'];
            }
        } elseif (is_array($responseData) && isset($responseData['translation'])) {
            // Si c'est déjà un tableau avec 'translation'
            return $responseData['translation'];
        }
    
        // Vérifier que la réponse est valide
        if (!is_array($responseData)) {
            $errorMsg = "Invalid AI response format translate: " . print_r($responseData, true);
            //error_log("❌ Erreur de traduction : réponse non valide: " . print_r($responseData, true));
            throw new \Exception($errorMsg);
        }
    
        return $responseData;
    }
    
    
public function countTokensOpenAI($json, $model = "gpt-5.4-mini") {
    $client = new \GuzzleHttp\Client();
    
    $apiKey = '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';
    $responseData = $client->post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
            'Authorization' => "Bearer $apiKey",
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'model' => $model,
            'messages' => [['role' => 'system', 'content' => json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)]]
        ]
    ]);

    $body = json_decode($responseData->getBody(), true);
    
    return $body['usage']['prompt_tokens'] ?? "Erreur de comptage";
}
public function getTitleBAD($titles, $category_id, $data = null) {
    $data_to_send = ['titles' => $titles];

    // Convertir en JSON
    $titles_json = json_encode($data_to_send, JSON_UNESCAPED_UNICODE);

    // Définition du `system_prompt` pour garantir une sortie JSON bien formattée
    $system_prompt = "You are an AI assistant. Return only a JSON object with a 'title' key, ensuring that the title is strictly within **80 characters**. Do not exceed this length.";

    // Définition du `keep` qui sera utilisé si le titre est trop long
    $keep = "";

    // Définition du prompt selon la catégorie
    switch ($category_id) {
        case '617': // Films/DVDs
            $user_prompt = "Based on Titles: ".$titles_json.", create an optimized eBay title in this format: 
                        {'title': 'Movie Title (DVD or Blu-ray, Year, Widescreen or Fullscreen)'}";
            $keep = "Keep the format {'title': 'Movie Title (DVD or Blu-ray, Year, Widescreen or Fullscreen)'} when shortening the title.";
            break;

        case '261186': // Livres
            $user_prompt = "Based on Titles: ".$titles_json.", create an optimized eBay title in this format: 
                        {'title': 'Book Title (Author, Publisher, Year, Number of Pages)'}";
            $keep = "Keep the format {'title': 'Book Title (Author, Publisher, Year, Number of Pages)'} when shortening the title.";
            break;

        case '139973': // Jeux vidéo
            $user_prompt = "Based on Titles: ".$titles_json.", create an optimized eBay title in this format: 
                        {'title': 'Video Game Title (Platform like PS4, Xbox, PS3, Nintendo)'}";
            $keep = "Keep the format {'title': 'Video Game Title (Platform like PS4, Xbox, PS3, Nintendo)'} when shortening the title.";
            break;

        default: // Autres catégories
            $manufacturer = (!empty($data['manufacturer']) && !is_array($data['manufacturer'])) ? " Manufacturer: " . $data['manufacturer'] : "";
            $condition_name = !empty($data['condition_name']) ? " Condition: " . $data['condition_name'] : "";
            $model = !empty($data['model']) ? " Model: " . $data['model'] : "";
            $color = !empty($data['color']) ? " Color: " . $data['color'] : "";

            $user_prompt = "Based on Titles: ".$titles_json.",".$condition_name.",".$color.",".$manufacturer.",".$model.", create an optimized product title in this format: 
                        {'title': 'Generated Title'}";
           // $keep = "Keep the format {'title': 'Generated Title'} when shortening the title.";
            break;
    }

    // Limitation des tokens
    $max_tokens = 90;
    $temperature = 0.5;

    // **Demande à l'IA (première requête)**
    $title_data = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature);

    // Vérification et décodage du JSON
  
    $title = isset($title_data['title']) ? trim($title_data['title']) : '';

    // **Si le titre dépasse 80 caractères, appliquer le `keep` et raccourcir avec une nouvelle requête**
    if (strlen($title) > 80) {
       
        $system_prompt = "Return only a JSON object with a title field, max length 80 characters.";

        $keep = "";

        // Définition du prompt selon la catégorie
        switch ($category_id) {
            case '617': // Films/DVDs

                $keep = "Keep the format {'title': 'Movie Title (DVD or Blu-ray, Year, Widescreen or Fullscreen)'} when shortening the title.";
                break;
    
            case '261186': // Livres

                $keep = "Keep the format {'title': 'Book Title (Author, Publisher, Year, Number of Pages)'} when shortening the title.";
                break;
    
            case '139973': // Jeux vidéo

                $keep = "Keep the format {'title': 'Video Game Title (Platform like PS4, Xbox, PS3, Nintendo)'} when shortening the title.";
                break;
    

        }
        $user_prompt = "Shorten this title ".$title." to be **strictly between 70 and 80 characters** while keeping it optimized. $keep";
        // **Deuxième requête pour corriger la longueur**
        $title_data = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature);
    
        $title = isset($title_data['title']) ? substr(trim($title_data['title']), 0, 80) : '';
    }

    // **Génération du `short_title` uniquement si nécessaire**
    if (strlen($title) <= 50) {
        $short_title = $title; // Pas besoin de demander à l'IA
    } else {
        switch ($category_id) {
            case '617': // Films/DVDs
                $user_prompt = "Based on Title: ".$title.", return only a JSON object with {'short_title': 'Movie Title'}";
                break;

            case '139973': // Jeux vidéo
                $user_prompt = "Based on Title: ".$title.", return only a JSON object with {'short_title': 'Video Game Title'}";
                break;

            default: // Autres produits
                $user_prompt = "Based on Title: ".$title.", return only a JSON object with {'short_title': 'Product Name'}";
                break;
        }

        $system_prompt = "Return only a JSON object with {'short_title': '...'}";
        $short_title_data = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature);

        // Vérification et récupération du `short_title`
     
        $short_title = isset($short_title_data['short_title']) ? trim($short_title_data['short_title']) : '';
    }

    return [
        'title' => $title ?? '',
        'short_title' => $short_title ?? ''
    ];
}


public function getTitle($titles, $category_id, $data = null) {
    $data_to_send = [
        'titles' => $titles,
       // 'max_length' => 80  // Limite de longueur du titre
    ];

    // Convertir en JSON
    $titles_json = json_encode($data_to_send, JSON_UNESCAPED_UNICODE);

    //$system_prompt = "You are an AI assistant. Return only a JSON object containing a title field (max 80 characters).";
    $system_prompt = "You are an AI assistant. Return only a JSON object with a 'title' key, ensuring that the title is strictly **within 80 characters**. Do not exceed this length.";


    // Définition du prompt selon la catégorie
    switch ($category_id) {
        case '617': // Films/DVDs
            $user_prompt = "Based on Titles: (".$titles_json."), create an optimized title in this format: 
                        {'title': 'Movie Title (DVD or Blu-ray, Year, Widescreen or Fullscreen), Other Info, Actors or Producer, Production Type, Disc Set'}";
            $keep = "keep the format {'title': 'Movie Title (DVD or Blu-ray, Year, Widescreen or Fullscreen)} when shortening the title ";
            break;

        case '261186': // Livres
            $user_prompt = "Based on Titles: (".$titles_json."), create an optimized title in this format: 
                        {'title': 'Book Title (Author, Publisher, Year, Number of Pages), Other Info'}";
            $keep = "keep the format {'title': 'Book Title (Author, Publisher, Year, Number of Pages)} when shortening the title ";
            break;

        case '176984': // CD
            $user_prompt = "Based on Titles: (".$titles_json."), create an optimized title in this format: 
                        {'title': 'Music CD Title (Author, Publisher, Year, Number of tracks), Other Info'}";
            $keep = "keep the format {'title': 'Music CD Title (Author, Publisher, Year, Number of tracks)} when shortening the title ";
            break;

        case '176985': // Vinyl
            $user_prompt = "Based on Titles: (".$titles_json."), create an optimized title in this format: 
                        {'title': 'Music Vinyl Title (Author, Publisher, Year, Number of tracks), Other Info'}";
            $keep = "keep the format {'title': 'Music Vinyl Title (Author, Publisher, Year, Number of tracks)} when shortening the title ";
            break;

            

        case '139973': // Jeux vidéo
            $user_prompt = "Based on Titles: (".$titles_json."), create an optimized eBay title in this format: 
                        {'title': 'Video Game Title (Platform like PS4, Xbox, PS3, Nintendo), Other Info'}";
            $keep = "keep the format {'title': 'Video Game Title (Platform like PS4, Xbox, PS3, Nintendo)} when shortening the title ";
            break;

        default: // Autres catégories
            $manufacturer = (!empty($data['manufacturer']) && !is_array($data['manufacturer'])) ? " Manufacturer: " . $data['manufacturer'] : "";
            $condition_name = !empty($data['condition_name']) ? " Condition: " . $data['condition_name'] : "";
            $model = !empty($data['model']) ? " Model: " . $data['model'] : "";
            $color = !empty($data['color']) ? " Color: " . $data['color'] : "";

            $user_prompt = "Based on Titles: ".$titles_json.",".$condition_name.",".$color.",".$manufacturer.",".$model.", 
                            create an optimized product title in this format: {'title': Generated Title }";
            $keep = "";
            break;
    }

    // Limitation des tokens
    $max_tokens = min($this->countTokensOpenAI($user_prompt), 80);
    //$max_tokens = 90;
    $temperature = 0.8;

    // Demande à l'IA
    $title_data = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature, '', 'title');

    // Vérification et décodage du JSON
    if (is_string($title_data)) {
        $title_data = json_decode($title_data, true);
    }
    
    if (is_array($title_data) && isset($title_data['title']) && is_string($title_data['title'])) {
        $title = str_replace('\\', '', $title_data['title']);
    
    } elseif (is_array($title_data) && isset($title_data[0]['title']) && is_string($title_data[0]['title'])) {
        $title = str_replace('\\', '', $title_data[0]['title']);
    
    } else {
        // Tentative de réparation via AI
        $repaired = $this->repairByJsonAI(json_encode($title_data));
    
        if ($repaired) {
            if (isset($repaired['title']) && is_string($repaired['title'])) {
                $title = str_replace('\\', '', $repaired['title']);
            } elseif (isset($repaired[0]['title']) && is_string($repaired[0]['title'])) {
                $title = str_replace('\\', '', $repaired[0]['title']);
            } else {
                throw new \Exception("⛔️ Impossible de récupérer un titre valide même après réparation : " . print_r($repaired, true));
            }
        } else {
            throw new \Exception("⛔️ Données titre invalides et échec de la réparation : " . print_r($title_data, true));
        }
    }
    
    
  
    // Vérification de la longueur
    $title_clean = str_replace('\\', '', $title);

    if (strlen($title_clean) > 80) {
        $user_prompt = "Shorten this title (".$title_clean.") to MUST be between 70 and 80 characters while keeping it optimized.";
            $user_prompt .= $keep;
        $system_prompt = "Return only a JSON object like in this format: {'title': with a title field, max length 80 characters. } ";
        $title_data = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature, '', 'title');
        if (is_string($title_data)) {
            $title_data = json_decode($title_data, true);
        }
        
        if (is_array($title_data) && array_key_exists('title', $title_data)) {
            //print("<pre>1116:ai</pre>");
            //print("<pre>" . print_r($title_data, true) . "</pre>");
            $title = str_replace('\\', '', $title_data['title']);
            //print("<pre>1119:ai</pre>");
            //print("<pre>" . print_r($title, true) . "</pre>");
        } else {
            //print("<pre>1122:ai</pre>");
            //print("<pre>" . print_r($title_data, true) . "</pre>");
        }
    }


    $short_title = $this->getShortTitle($title, $category_id);

    return [
        'title' => isset($title) ? mb_substr(stripslashes($title), 0, 80) : '',
        'short_title' => stripslashes(str_replace('\\', '', $short_title)) ?? ''
    ];
}

    public function getCategoryID($category_name){
    
    $this->load->model('shopmanager/ai');
 
    $this->load->model('shopmanager/catalog/category');
 
  //  unset($product_info['category_id']);
        if (isset($category_name)) {
      
    //     $categories1 = $this->model_shopmanager_catalog_category->getCategoriesLeaf(1);
            $data_value['filter_leaf']='1';
         
            
        //  $extract_leaf_category = preg_split('/\s*>\s*/', $category_name);
        //    print_r($extract_leaf_category);
    //       $nb_array= count($extract_leaf_category)-1;
        //  $category_name = "Media > DVDs & Videos";
            $extract_leaf_category = explode('&gt;', $category_name);

            // Initialisation de l'array final
            $final_category_array = [];

            foreach($extract_leaf_category as $leaf_category){
                // Sépare chaque sous-chaîne par '&'
                $extract_leaf_category2 = explode('&amp;', $leaf_category);
                
                // Parcourt chaque sous-élément et enlève le dernier 's' s'il existe
                foreach($extract_leaf_category2 as &$item) {
                    $item = trim($item); // Supprime les espaces
                    if (substr($item, -1) === 's') {
                        $item = substr($item, 0, -1); // Enlève le dernier 's'
                    }
                    $item=strtolower($item);
                }
                
                // Fusionne les résultats dans le tableau final
                $final_category_array = array_merge($final_category_array, $extract_leaf_category2);
            }
        
            $all_results = [];

            foreach ($final_category_array as $item) {
                // Définit le filtre avec l'élément actuel
                $data_value['filter_name'] = $item;
            
                // Récupère les catégories filtrées
                $categories_data = $this->model_shopmanager_catalog_category->getCategories($data_value);
            
                // Ajoute les résultats au tableau final
                $all_results = array_merge($all_results, $categories_data);
            }
            
            // Affiche les résultats finaux
        //print("<pre>" . print_r($all_results, true) . "</pre>");
        //    echo '<br>'.strlen(serialize($categories_data));
            $categories = [];

            foreach($all_results as $row){
                $categories[$row['category_id']]=$row['name'];
            // $categories[$row['category_id']]=$row['leaf'];
            }

    //print("<pre>" . print_r($categories, true) . "</pre>");
    //      echo '<br>'.strlen(serialize($categories));
    //     echo '<br>'.strlen(serialize(json_encode($categories)));
    //print("<pre>" . print_r($categories, true) . "</pre>");


            $user_prompt = "find category_id for category's ".$category_name.".  Based on json manufacturers database:  ".json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).".  ";
                //print("<pre>" . print_r($user_prompt, true) . "</pre>");
            // Set up parameters for the AI request
            $system_prompt = " return exact information on json format {
                                    category_id: XXX
                                
                                } or  {
                                    category_id: 0
                                }";
                                
            $max_tokens = $this->countTokensOpenAI($system_prompt) + $this->countTokensOpenAI($user_prompt);  
            $temperature = 0.3; 
        
            $responseArray = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature,'','category_id');

            if (is_array($responseArray) && !empty($responseArray['category_id'])) {
                $category_id = trim($responseArray['category_id']);
            
                // Vérifie si category_id est numérique (meilleure pratique)
                if (is_numeric($category_id)) {
                    $category_detail = $this->model_shopmanager_catalog_category->getCategoryDetails($category_id);
            
                    // Vérifie si les détails existent bien
                    if ($category_detail) {
                        $category_info = [
                            'category_id'   => $category_id,
                            'category_name' => $category_detail['name']
                        ];
                        return $category_info;
                    }
                }
            }
            
            // En cas d'erreur ou de valeur invalide
            return null;

        
           
    
        }else{
            return null;
        }

    }

    public function getManufacturer($manufacturers = [],$manufacturer = '') {
        // Si plusieurs fabricants sont trouvés, appel à OpenAI pour déterminer le bon
      
        //print("<pre>" . print_r('1326 ai', true) . "</pre>");
        //print("<pre>" . print_r($manufacturers, true) . "</pre>");
        //print("<pre>" . print_r($manufacturer, true) . "</pre>");
        $database_manufacturers = json_encode($manufacturers, JSON_UNESCAPED_UNICODE);
        $user_prompt = "Find the manufacturer ID for ".$manufacturer." based on this JSON database: $database_manufacturers.";
        $system_prompt = "Return a JSON object with {'manufacturer_id': XXX} or {'manufacturer_id': 0}.";

        $max_tokens = $this->countTokensOpenAI($user_prompt);
        $temperature = 0.5;
        $responseData = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature, '', 'manufacturer_id');

       
        if (isset($responseData['manufacturer_id']) && is_numeric($responseData['manufacturer_id']) && $responseData['manufacturer_id'] != 0) {
            return [
                'manufacturer_id' => $responseData['manufacturer_id'],
                'name' => $manufacturer
            ];
        }
    }
    public function getDescriptionSupp($formdata, $title, $category_id) {
        // Vérification du titre requis
        if (empty($title)) {
            throw new \Exception("Title is required");
        }
    
        // Définition du prompt basé sur la catégorie
        if ($category_id == 617) {
            $system_prompt = "Provide a general synopsis for a movie. Focus only on the movie's plot, main themes, and key characters. Do not include any information related to the product's condition, format, or technical specifications.";
            $user_prompt = "Provide a general synopsis for the movie ".$title." The synopsis should help the customer understand what the movie is about. With the following details: " . json_encode($formdata, JSON_UNESCAPED_UNICODE);
        } elseif ($category_id == 139973) {
            $system_prompt = "Provide a general synopsis for a video game. Focus only on the game's plot, gameplay mechanics, and features. Do not include any information related to the product's condition, color, or technical specifications.";
            $user_prompt = "Provide a general synopsis for the video game ".$title." The synopsis should help the customer understand what the video game is about. With the following details: " . json_encode($formdata, JSON_UNESCAPED_UNICODE);
        } else {
            $system_prompt = "Find a general description for a product. Focus on the product's purpose, features, and functionality. Do not include any information related to the product's condition, color, or technical specifications.";
            $user_prompt = "Find a general description for a product named ".$title." The description should help the customer understand what it is. With the following details: " . json_encode($formdata, JSON_UNESCAPED_UNICODE);
        }
        $system_prompt .= "Return a valid JSON object with a key 'description' containing the properly escaped formatted text.  ";
        // Ajout de la directive pour fusionner toutes les informations dans une seule chaîne
        $system_prompt .= "MUST MERGE ALL INFORMATION INTO A SINGLE STRING IN JSON FORMAT.";
        
        // Appel à l'IA pour générer la description
        $max_tokens = 1000;
        $temperature = 0.7;
        $responseData = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature, '', "description");
        //print("<pre>" . print_r('1272', true) . "</pre>");
        //print("<pre>" . print_r($responseData, true) . "</pre>");
        // Vérification et correction du JSON retourné
        if (is_string($responseData)) {
            $responseData = json_decode($responseData, true);
        }
        
        if (!isset($responseData['description']) || !is_string($responseData['description'])) {
            throw new \Exception("Invalid AI response format: " . print_r($responseData, true));
        }
    
        // Appel pour formatter en HTML
        return $this->getFormattedText($responseData['description']);
    }
    public function getFormattedText($description_text) {
        // Vérification que la description est bien une chaîne
       
       /* if (!is_string($description_text) || empty(trim($description_text))) {
            throw new Exception("Invalid description input for formatting.");
        }*/
    
        // Construire le prompt
        $user_prompt = "Format the following text with HTML tags for bold, italics, and paragraphs where appropriate: ";
        $user_prompt .= " ".$description_text.".";
    
        //$max_tokens = $this->countTokensOpenAI($user_prompt);
    
        $system_prompt = "You are an expert in HTML formatting for product descriptions. MAKE IT BEAUTIFUL.";
        $system_prompt .= 'Return a valid JSON object with a key "html" containing the properly escaped formatted text.  ';
        $system_prompt .= "Use appropriate HTML tags to improve readability, persuasion and emphasis:.";
        $system_prompt .= "you can USE bold, italic, UPPERCASE etc.. .  ";
        $system_prompt .= "- <p> to separate paragraphs.  ";
        $system_prompt .= "- <strong> to highlight important words to emphase the product.  ";
        $system_prompt .= "- <em> for subtle emphasis.  ";
        $system_prompt .= "- <ul> and <li> for bullet lists (features, contents, benefits).  ";
        $system_prompt .= "- <br> for line breaks within paragraphs.  ";
        $system_prompt .= '- <span style="color:#HEX"> to add color when necessary (e.g. <span style="color:red">Urgent</span>).  ';
        $system_prompt .= "- <h2> or <h3> for headings if useful.  ";
        $system_prompt .= "Avoid using <div>, <script>, <iframe>, or any layout-related tags.  ";
        $system_prompt .= "Avoid using inline styles except for <span style='color'>.  ";
        $system_prompt .= "Ensure that the text remains clean, professional, and easy to read for online shoppers.  ";
        $system_prompt .= "Escape all necessary characters so the JSON is valid.  ";
     
        $system_prompt .= 'Example: {"html":  formatted text }.';
        $max_tokens = 2000;
        $temperature = 0.7;
        // Appel à l'IA
        $responseData = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature, '', 'html');
        //print("<pre>" . print_r('1309', true) . "</pre>");
        //print("<pre>" . print_r($responseData, true) . "</pre>");
        // Vérification et correction du JSON retourné
       /* if (is_string($responseData)) {
            $responseData = json_decode($responseData, true);
        }*/
    
        if (!isset($responseData['html']) || !is_string($responseData['html'])) {
            // Dernier recours : essayer de réparer avec l'IA
            $raw_content = print_r($responseData, true);
            $fixed = $this->repairByJsonAI($raw_content);
            
        
            if ($fixed && isset($fixed['html']) && is_string($fixed['html'])) {
                $responseData = $fixed; // remplacement par version réparée
            } else {
                throw new \Exception("Invalid AI response format and repair failed: " . print_r($responseData, true));
            }
        }
        
    
        return ['html' => $responseData['html']];
    }
    

    
    public function getSpecificKey($specifics_key_names, $category_specifics){
      
        $aspect_name_array= [];
     //print("<pre>" . print_r($category_specifics, true) . "</pre>");
        foreach($category_specifics as $key=>$specific){
            $value=stripslashes($key);

            $aspect_name_array[]= $value;
        }
         
            $user_prompt = "Given the following list of valid key names: " . json_encode($aspect_name_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ", find the most accurate and semantically similar key name to replace the key name '" . $specifics_key_names . "'. If there is no close match, return '0'. Please focus on accuracy and context relevance.";

            $system_prompt = "You are an AI specialized in data matching and semantic analysis.  ";
            $system_prompt .= "Your task is to compare the given key name against a list of valid key names.";
            $system_prompt .= "and determine the most accurate and semantically similar match.  ";
            $system_prompt .= "Prioritize accuracy and context relevance when selecting the best match.  ";
            $system_prompt .= "If no close match is found, return '0'. ";
            $system_prompt .= "Return the exact database key name in JSON format:";
            $system_prompt .= '{    "key name": "XXX"} ';
            $system_prompt .= "or";
            $system_prompt .= '{    "key name": "0"}.';

            $max_tokens = $this->countTokensOpenAI($system_prompt) + $this->countTokensOpenAI($user_prompt);  
            $temperature = 0.3; 

            $responseData = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature,'','suggest_key_name');

            // Vérification que la réponse est bien un tableau et non vide
            if (is_array($responseData) && !empty($responseData['suggest_key_name'])) {

                $suggest_key_name = trim(html_entity_decode($responseData['suggest_key_name']));

                // Si la valeur est "0", retourne null
                if ($suggest_key_name === "0") {
                    return null;
                }

                // Si numérique, retourne la valeur correspondante dans l'array
                if (is_numeric($suggest_key_name)) {
                    return $aspect_name_array[$suggest_key_name] ?? null;
                } else {
                    // Sinon, construit un tableau associatif avec la clé obtenue
                    $product_info_value_target[$suggest_key_name] = $value ?? '';
                    return $product_info_value_target;
                }
            }

            // En cas d'erreur ou réponse invalide, retourne null
            return null;  
    }

    
        

    public function reduceArrayValue($array, $name){
        $this->load->model('shopmanager/ai'); 
        $json= json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $user_prompt = "Reduce this json (".$json.")to include only the top 10 most relevant ".$name." values. Return the reduced array with a maximum of 10 values in JSON format.";
        
    //  $suggestion = $this->prompt_ai($user_prompt, 'Provide in JSON php format like without anything else.', 100, 0.3);
        $system_prompt = "You are an AI specialized in extracting the most relevant values from a dataset. ";
        $system_prompt .= "Your task is to analyze the given JSON and select only the top 10 most relevant values for the specified category. ";
        $system_prompt .= "Prioritize values that provide the most meaningful insights while ensuring diversity and minimizing redundancy.  ";
        $system_prompt .= "Return the result strictly as a JSON array with a maximum of 10 values, preserving the original data format.";

        $max_tokens = $this->countTokensOpenAI($user_prompt);
        $temperature = 0.3; 
        $response = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature, '', 'suggestion');
        return $response['suggestion'] ?? null;

    }
    public function getShortTitle($product_name, $category_id = 617, $language = 'english') {
    // Génération du `short_title` basé sur la catégorie
    switch ($category_id) {
        case '617': // Films/DVDs
            $user_prompt = "Based on Title: ".$product_name.", return only the Movie Name " . ($language!= '' ? " in $language" : "") ." a JSON object with {'short_title': 'Movie Title'}";
            break;

        case '139973': // Jeux vidéo
            $user_prompt = "Based on Title: ".$product_name.", return only the Video Game Name " . ($language!= '' ? " in $language" : "") ."  a JSON object with {'short_title': 'Video Game Title'}";
            break;

        default: // Autres produits
            $user_prompt = "Based on Title: ".$product_name.", return only  " . ($language!= '' ? " in $language" : "") ." a JSON object with {'short_title': 'Product Name'}";
            break;
    }

    $system_prompt = "Return only a JSON object with {'short_title': '...'}";
    $max_tokens = $this->countTokensOpenAI($user_prompt);
    $temperature = 0.5;
    $short_title_data = $this->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature,'','short_title');
    
    // Vérification et récupération du `short_title`
  
    return isset($short_title_data['short_title']) ? $short_title_data['short_title'] : null;

    }

    /**
     * Translate text using OpenAI API
     * 
     * @param string $text_field Text to translate
     * @param string $targetLanguage Target language code (e.g., 'fr', 'es', 'de', 'it', 'en')
     * @return string|null Translated text or null on error
     */
    public function translateNOT_USED($text_field, $targetLanguage) {
        if (empty($text_field) || empty($targetLanguage)) {
            return null;
        }

        // Language name mapping for better translation quality
        $languageNames = [
            'fr' => 'French',
            'es' => 'Spanish',
            'de' => 'German',
            'it' => 'Italian',
            'en' => 'English',
            'pt' => 'Portuguese',
            'nl' => 'Dutch',
            'pl' => 'Polish',
            'ru' => 'Russian',
            'ja' => 'Japanese',
            'zh' => 'Chinese',
            'ko' => 'Korean',
            'ar' => 'Arabic',
            'tr' => 'Turkish',
            'vi' => 'Vietnamese'
        ];

        // Get language name or use the provided language code
        $languageName = $languageNames[$targetLanguage] ?? $targetLanguage;

        $system_prompt = "You are a professional translator. Translate the provided text to $languageName. "
                        . "Maintain the original meaning, tone, and formatting. "
                        . "Return only the translated text, no explanations or additional content.";

        $user_prompt = "Translate this text to $languageName:\n\n" . $text_field;

        // Calculate tokens for the prompts
        $max_tokens = $this->countTokensOpenAI($system_prompt) + $this->countTokensOpenAI($user_prompt);
        $temperature = 0.3; // Lower temperature for more consistent translations

        try {
            $responseData = $this->sendOpenAiRequest($user_prompt, $system_prompt, $max_tokens, $temperature);
            
            if (isset($responseData['choices'][0]['message']['content'])) {
                $translated_text = trim($responseData['choices'][0]['message']['content']);
                
                // Clean up any markdown code blocks if present
                $translated_text = preg_replace('/^```.*?\n?/', '', $translated_text);
                $translated_text = preg_replace('/\n?```$/', '', $translated_text);
                $translated_text = trim($translated_text);

                return $translated_text;
            }
        } catch (\Exception $e) {
            error_log('translateWithOpenAI error: ' . $e->getMessage());
            return null;
        }

        return null;
    }

    public function getMadeInCountry($product_info = null) {
        $this->load->model('shopmanager/catalog/product');
        
        // Get product description
        $product_description = $this->model_shopmanager_catalog_product->getDescriptions($product_info['product_id']);
        $language_id = 1;
        
        // Extract relevant product information
        $title = $product_description[$language_id]['name'] ?? '';
        $description = $product_description[$language_id]['description'] ?? '';
        $manufacturer = isset($product_info['manufacturer']) && !is_array($product_info['manufacturer']) 
            ? $product_info['manufacturer'] : '';
        $model = $product_info['model'] ?? '';
        $upc = $product_info['upc'] ?? '';
        $sku = $product_info['sku'] ?? '';
        $mpn = $product_info['mpn'] ?? '';
        
        // Build user prompt with all available product data
        $user_prompt = "Analyze the following product information and determine the country of manufacture (Made In Country):\n\n";
        $user_prompt .= "Product Name: " . $title . "\n";
        
        if (!empty($manufacturer)) {
            $user_prompt .= "Manufacturer/Brand: " . $manufacturer . "\n";
        }
        
        if (!empty($model)) {
            $user_prompt .= "Model: " . $model . "\n";
        }
        
        if (!empty($upc)) {
            $user_prompt .= "UPC: " . $upc . "\n";
        }
        
        if (!empty($mpn)) {
            $user_prompt .= "MPN: " . $mpn . "\n";
        }
        
        if (!empty($description)) {
            // Limit description to first 500 characters
            $desc_excerpt = substr(strip_tags($description), 0, 500);
            $user_prompt .= "Description: " . $desc_excerpt . "...\n";
        }
        
        $user_prompt .= "\nBased on this information, determine the most likely country of manufacture. ";
        $user_prompt .= "Consider:\n";
        $user_prompt .= "- Brand origin and typical manufacturing locations\n";
        $user_prompt .= "- Product type and industry standards\n";
        $user_prompt .= "- UPC/MPN country codes if available\n";
        $user_prompt .= "- Any explicit mentions in the name or description\n\n";
        $user_prompt .= "Return ONLY the country ID from this list:\n";
        $user_prompt .= "38=Canada, 44=China, 138=Mexico, 223=United States, ";
        $user_prompt .= "107=Japan, 81=Germany, 113=South Korea, 206=Taiwan, ";
        $user_prompt .= "99=India, 215=Turkey, 209=Thailand, 230=Vietnam\n\n";
        $user_prompt .= "If you cannot determine with confidence, return 0.\n";
        
        // System prompt for AI
        $system_prompt = "You are an expert in product manufacturing and global supply chains. ";
        $system_prompt .= "Your task is to analyze product information and determine the country of manufacture. ";
        $system_prompt .= "Be logical and use your knowledge of brand origins, industry practices, and product types. ";
        $system_prompt .= "Return ONLY a JSON object with the format: {\"country_id\": <number>, \"confidence\": \"high|medium|low\", \"reasoning\": \"brief explanation\"}";
        
        // Call OpenAI
        $max_tokens = 500;
        $temperature = 0.3; // Low temperature for more deterministic results
        
        try {
            //error_log('getMadeInCountry: Calling OpenAI with prompt length: ' . strlen($user_prompt));
            $response = $this->sendOpenAiRequest($user_prompt, $system_prompt, $max_tokens, $temperature);
            //error_log('getMadeInCountry: OpenAI response received: ' . json_encode($response));
            
            $result = $this->extractContent($response);
            //error_log('getMadeInCountry: Extracted content: ' . json_encode($result));
            
            if (isset($result['country_id'])) {
                return $result;
            }
            
            //error_log('getMadeInCountry: No country_id in result');
            return null;
        } catch (\Exception $e) {
            error_log('getMadeInCountry error: ' . $e->getMessage());
            return null;
        }
    }

}
