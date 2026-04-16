<?php
// Original: warehouse/tools/upctmp.php
namespace Opencart\Admin\Model\Warehouse\Tools;

class Upctmp extends \Opencart\System\Engine\Model {
    private ?int $lastRequestTime = null;

    public function get(string $upc) {
        $endpoint = 'https://api.upcitemdb.com/prod/trial/lookup';
    
        // Vérifier le délai entre les requêtes
        $this->throttleRequests();
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json"
        ));
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_URL, $endpoint . '?upc=' . $upc);
    
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headers = curl_getinfo($ch);
    
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            
            return ['error' => 'cURL error: ' . $error_msg];
        }
    
        
    
        if ($httpcode == 200) {
            $responseData = json_decode($response, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => 'Invalid JSON response'];
            }
    
            if (isset($responseData['items']) && !empty($responseData['items'])) {
                return $this->processProductData($responseData['items'][0]);
            } else {
                return NULL;
            }
        } elseif ($httpcode == 429) {
            // Gestion des limites de burst
            $retryAfter = isset($headers['Retry-After']) ? (int)$headers['Retry-After'] : 10;
            sleep($retryAfter);
            return $this->get($upc); // Réessayer après la pause
        } else {
            return ['error' => 'Failed to fetch data. HTTP Status Code: ' . $httpcode];
        }
    }
    
    private function throttleRequests() {
        $minInterval = 10; // 10 secondes entre les requêtes
    
        if ($this->lastRequestTime !== null) {
            $timeSinceLastRequest = time() - $this->lastRequestTime;
    
            if ($timeSinceLastRequest < $minInterval) {
                sleep($minInterval - $timeSinceLastRequest);
            }
        }
    
        $this->lastRequestTime = time();
    }
    
    public function getOLD($upc) {

      //  $user_key = '0f7ef6d74cc72d7e82ab8738f3769844';
     //   $endpoint = 'https://api.upcitemdb.com/prod/v1/lookup';
        $endpoint = 'https://api.upcitemdb.com/prod/trial/lookup';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json"
        ));
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_URL, $endpoint.'?upc='.$upc);
        $response  = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    
        // Vérifier s'il y a des erreurs cURL
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            
            return ['error' => 'cURL error: ' . $error_msg];
        }
    
        // Obtenir le code de réponse HTTP
       
        
    //print("<pre>".print_r ($response,true )."</pre>");
        // Vérifier la réponse HTTP
        if ($httpcode == 200) {
            // Décoder la réponse JSON
            $response_data = explode(",offset",$response);
            // Vérifier la réponse HTTP
         //print("<pre>".print_r ($response_data,true )."</pre>");
                // Décoder la réponse JSON
                $responseData = json_decode($response_data[1], true);
          //  $responseData = json_decode($response, true);
       //print("<pre>".print_r ($responseData,true )."</pre>");
            // Vérifier la présence de données dans la réponse
            if (isset($responseData['items'][0]) && !empty($responseData['items'])) {
                return $this->processProductData($responseData['items'][0]);
            } else {
                return null;
            }
        } else {
            // Retourner un message d'erreur en cas d'échec de la requête
            return ['error' => 'Failed to fetch data. HTTP Status Code: ' . $httpcode];
        }
    
       // return null; // Retourner null si aucune information n'est trouvée
    }


    public function search($s) {
        // URL de l'API
        $endpoint = 'https://api.upcitemdb.com/prod/trial/search';
    
        // Encodage de la chaîne de recherche pour s'assurer que les espaces et autres caractères sont correctement gérés
        $encodedSearch = urlencode($s);
    
        // Initialisation de la requête cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json"
        ));
        curl_setopt($ch, CURLOPT_POST, 0);
    
        // Définir l'URL avec le paramètre de recherche encodé
        curl_setopt($ch, CURLOPT_URL, $endpoint . '?s=' . $encodedSearch);
    
        // Exécution de la requête
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        // Vérifier s'il y a des erreurs cURL
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            
            return ['error' => 'cURL error: ' . $error_msg];
        }
    
        // Fermeture de la connexion cURL
        
    
        // Vérifier la réponse HTTP
        if ($httpcode == 200) {
            // Séparer la réponse pour obtenir le contenu JSON (si nécessaire)
            $response_data = explode(",offset", $response);
            
            // Décoder la réponse JSON en utilisant le bon index
            $responseData = json_decode($response_data[1], true);
         //print("<pre>".print_r ($responseData,true )."</pre>");
            // Vérifier la présence de données dans la réponse
            if (isset($responseData['items'][0]) && !empty($responseData['items'])) {
                return $this->processProductData($responseData['items'][0]);
            } else {
                return ['error' => 'No items found'];
            }
        } else {
            // Retourner un message d'erreur en cas d'échec de la requête
            return ['error' => 'Failed to fetch data. HTTP Status Code: ' . $httpcode];
        }
    
       // return null; // Retourner null si aucune information n'est trouvée
    }
    
    private function processProductData($item) {
        // Extraire les informations clés du produit
        foreach ($item['images'] as $key=>$image){
            $item['images'][$key]= str_replace('Large','Enlarge',$image);
            $item['images'][$key]= str_replace('$thumbnail$', '$Enlarge$',$image);
          
        }
        $productData = [
            'name' => $item['title'] ?? '',
            'description_supp' => $item['description'] ?? '',
            'upc' => $item['upc'] ?? '',
            'brand' => $item['brand'] ?? '',
            'model' => $item['model'] ?? '',
            'color' => $item['color'] ?? '',
            'size' => $item['size'] ?? '',
            'dimension' => $item['dimension'] ?? '',
            'weight' => $item['weight'] ?? '',
            'currency' => $item['currency'] ?? '',
            'category_name' => $item['category'] ?? '',
            'lowest_recorded_price' => $item['lowest_recorded_price'] ?? '',
            'highest_recorded_price' => $item['highest_recorded_price'] ?? '',
            'images' => $item['images'] ?? [],
            'offers' => $this->processOffers($item['offers'] ?? [])
        ];

        return $productData;
    }
    private function processOffers($offers) {
        $processedOffers = [];
        
        foreach ($offers as $offer) {
            $processedOffers[] = [
                'merchant' => $offer['merchant'] ?? '',
                'price' => $offer['price'] ?? 0,
                'condition_name' => $offer['condition'] ?? '',
                'shipping' => (is_numeric($offer['shipping'])) ?$offer['shipping']: 0,
                'price_with_shipping' => (($offer['price']) ?? 0) + (is_numeric($offer['shipping']) ?$offer['shipping']: 0),
                'updated_t' => $offer['updated_t'] ?? '',
                'name' => $offer['title'] ?? '',
                'currency' => $offer['currency'] ?? '',
                'availability' => $offer['availability'] ?? '',
                'url' => $offer['link'] ?? ''
            ];
        }

        return $processedOffers;
    }
}


?>
