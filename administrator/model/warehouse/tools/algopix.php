<?php
// Original: warehouse/tools/algopix.php
namespace Opencart\Admin\Model\Warehouse\Tools;


class Algopix extends \Opencart\System\Engine\Model {


  
    public function get($productId, $productIdType = 'UPC', $languages = ['ENGLISH_US']) { //'ENGLISH_CA',
        $api_key = 'Y04rRHoNLjQhYhZPZsxUpirm9GXhbmJXaRw9maf7'; // Remplacez par votre clé API Algopix
        $app_id = 'GOL7sZmM5rDqnMjS19tVaf'; // Remplacez par votre App ID Algopix
        $endpoint = 'https://api.algopix.com/v4/products/details';
        
        $mergedResults = [];
        $waitTime = 0;
     //print("<pre>".print_r ($productId,true )."</pre>");
        // Boucler à travers chaque langue
        foreach ($languages as $language) {
            // Construction de l'URL pour chaque langue
            $url = $endpoint . '?productId=' . urlencode($productId) . '&productIdType=' . urlencode($productIdType) . '&language=' . urlencode($language);
            $error_msg='';
            // Initialiser cURL
            $ch = curl_init();
            
            // Configuration des options cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Accept: application/json",
                "X-API-KEY: $api_key",
                "X-APP-ID: $app_id"
            ]);
            
            // URL pour la requête GET
            curl_setopt($ch, CURLOPT_URL, $url);
            
            // Exécuter la requête cURL
            $response = curl_exec($ch);
            
            // Vérifier les erreurs cURL
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                
                return ['error' => 'cURL error: ' . $error_msg];
            }
            
            // Obtenir le code de réponse HTTP
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
           //print("<pre>".print_r ('48_ALGOPIX',true )."</pre>");
            //print("<pre>".print_r (json_decode($response, true),true )."</pre>");
            //print("<pre>".print_r (json_decode($error_msg, true),true )."</pre>");
            
      //print("<pre>".print_r (json_decode($response, true),true )."</pre>");
            // Vérifier la réponse HTTP
            if ($httpcode == 200) {
                // Décoder la réponse JSON
               

                $responseData = json_decode($response, true);
                //print("<pre>".print_r ('59:algopix',true )."</pre>");
                //print("<pre>".print_r ($responseData,true )."</pre>");
                // Vérifier la présence de données dans la réponse
                if (isset($responseData['result']) && !empty($responseData['result'])) {
                    // Fusionner et comparer les résultats
                 //   $mergedResults = array_merge_recursive($mergedResults, $responseData['result']['result']);
                   
                    $mergedResults = $this->mergeAndCleanArray($mergedResults, $responseData['result']['result']);
                    //print("<pre>".print_r ('66:algopix',true )."</pre>");
                    //print("<pre>".print_r ($mergedResults,true )."</pre>");
                } 
            }else{
                //print("<pre>".print_r (json_decode($httpcode, true),true )."</pre>");
            } 
            sleep($waitTime);
        }
        if(!empty($mergedResults)){
            return $mergedResults; // Retourner les résultats fusionnés et sans doublons
        } else {
            return null;
        }
       
    }
    
    private function mergeAndCleanArray($existingResults, $newResults) {
        foreach ($newResults as $key => $value) {
            if (isset($existingResults[$key])) {
                if (is_array($existingResults[$key])) {
                    if (is_array($value)) {
                        // Si la clé est un tableau dans les deux ensembles, effectuer une fusion récursive
                        $existingResults[$key] = $this->mergeAndCleanArray($existingResults[$key], $value);
                    } else {
                        // Ajouter une valeur unique au tableau existant s'il ne s'agit pas d'un tableau
                        if (!in_array($value, $existingResults[$key])) {
                            $existingResults[$key][] = $value;
                        }
                    }
                } else {
                    // Si l'une des valeurs est simple, combiner les deux en un tableau si elles diffèrent
                    if ($existingResults[$key] !== $value) {
                        $existingResults[$key] = [$existingResults[$key], $value];
                    }
                }
            } else {
                // Ajouter la nouvelle clé et sa valeur si elle n'existe pas encore
                $existingResults[$key] = $value;
            }
        }
    
        // Suppression des doublons et nettoyage des tableaux contenant une seule valeur
        return $this->cleanArray($existingResults);
    }
    public function cleanArray($array) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($this->isAssociativeArray($value)) {
                    // Si c'est un tableau associatif, nettoyer récursivement
                    $array[$key] = $this->cleanArray($value);
                } else {
                    // Si c'est un tableau simple, supprimer les doublons
                    $array[$key] = $this->removeArrayDuplicates($value);
                    if (count($array[$key]) === 1) {
                        $array[$key] = $array[$key][0];
                    }
                }
            }
        }
        return $array;
    }

         
    // Fonction utilitaire pour vérifier si un tableau est associatif
    private function isAssociativeArray(array $array) {
        return array_keys($array) !== range(0, count($array) - 1);
    }  

    private function removeArrayDuplicates($array) {
        $uniqueArray = [];
        
        foreach ($array as $item) {
            if (is_array($item)) {
                $item = json_encode($item); // Convertir en chaîne pour la comparaison
            }
            if (!in_array($item, $uniqueArray)) {
                $uniqueArray[] = $item;
            }
        }
        
        // Reconvertir les éléments JSON en tableau
        foreach ($uniqueArray as &$item) {
            if (is_string($item) && $this->isJson($item)) {
                $item = json_decode($item, true);
            }
        }
        
        return $uniqueArray;
    }

    // Fonction utilitaire pour vérifier si une chaîne est JSON
    private function isJson($string) {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}


?>
