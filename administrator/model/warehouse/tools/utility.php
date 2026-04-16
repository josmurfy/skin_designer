<?php
// Original: warehouse/tools/utility.php
namespace Opencart\Admin\Model\Warehouse\Tools;

class Utility extends \Opencart\System\Engine\Model {

  
    // Fonction pour retourner la chaîne la plus longue dans un tableau
    private function getLongestString($array) {
        usort($array, function($a, $b) {
            return strlen($b) - strlen($a); // Trier par longueur décroissante
        });
        
        return $array[0]; // Retourner la chaîne la plus longue
    }
    
    public function cleanArray($array) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($this->isAssociativeArray($value)) {
                    // Si c'est un tableau associatif, nettoyer récursivement
                    $array[$key] = $this->cleanArray($value);
                } else {
                    // Si c'est un tableau simple, supprimer les doublons et nettoyer
                    $array[$key] = $this->removeArrayDuplicates($value);
                }
            }
        }
        return $array;
    }
    
    public function removeArrayDuplicates($array = []) {
        $uniqueArray = [];
        if(is_array($array)){
            
            foreach ($array as $item) {
                if (is_array($item)) {
                    $item = json_encode($item, JSON_UNESCAPED_UNICODE); // Préserve les caractères spéciaux
                }
        
                // Nettoyer la chaîne en conservant certains caractères spéciaux utiles
                $cleanedItem = $this->cleanString($item);
        
                if (!in_array($cleanedItem, $uniqueArray, true)) {
                    $uniqueArray[] = $cleanedItem;
                }
            }
            
            return $uniqueArray;
        }else{
            return $array;
        }
    }
    
    // Vérifier si un tableau est associatif
    private function isAssociativeArray(array $array) {
        return array_keys($array) !== range(0, count($array) - 1);
    }
    
    // Nettoyage des chaînes sans supprimer les caractères spéciaux nécessaires
    public function cleanString($string) {
        // Conserver certains caractères spéciaux nécessaires, tels que Ω, °, ©, ®, ™
        return preg_replace('/\s+/', ' ', preg_replace('/[^a-zA-Z0-9Ω°©®™.\-\s]/u', '', trim($string)));
    }
    public function cleanStringValue($string) {
        // Remplace tous les caractères qui ne sont pas des lettres, des chiffres, des espaces, des apostrophes, des deux-points ou des points-virgules.
        return $this->replaceSeparatorsWithComma(preg_replace("/[^a-zA-Z0-9';:\s]/", '', $string));
    }
    
    private function replaceSeparatorsWithComma($string) {
        // Remplace les caractères de séparation (sauf `;` et `:`) par une virgule.
        return preg_replace("/[\/|]/", ',', $string);
    }


    
    public function convertToYear($input) {
        // Vérifier si l'entrée est une date valide
        $timestamp = strtotime($input); 
    
        // Si l'entrée est une date, retourner uniquement l'année (format YYYY)
        if ($timestamp !== false) {
            return date('Y', $timestamp);  // Retourne l'année seulement
        }
    
        // Si l'entrée n'est pas une date, retourner l'entrée d'origine
        return $input;
    }

    function convertJsonValuesToArray($product_info_value) {
        $product_info_value_target = [];
    
        foreach ($product_info_value as $key => $value) {
            // Vérifier si la valeur est une chaîne JSON qui commence par [
            if (is_string($value) && strpos($value, '[') === 0) {
                // Décoder la chaîne JSON en tableau PHP
                $decoded_value = json_decode($value, true);
    
                // Vérifier que le JSON a été correctement décodé et est un tableau
                if (is_array($decoded_value)) {
                    $product_info_value_target[$key] = $decoded_value;
                } else {
                    // Si le décodage échoue, conserver la valeur d'origine
                    $product_info_value_target[$key] = $value;
                }
            } else {
                // Si ce n'est pas un JSON ou ne commence pas par [, conserver la valeur d'origine
                $product_info_value_target[$key] = $value;
            }
        }
    
        return $product_info_value_target;
    }

    
    public function convert_one_array_to_string($value) {
      //$this->debug_function_trace();

        //print("<pre>".print_r ($value, true )."</pre>");

        // Vérifier si la valeur est une chaîne avant d'utiliser strpos
        if (is_array($value) && !empty($value)) {
            $value = reset($value); // Prend la première valeur du tableau, quelle que soit la clé
        }

        return  $value;
    }

    private function convertOneArrayToString($value) {
        if (is_array($value) && count($value) === 1) {
            $value = $value[0]; // Récupérer la première et seule valeur du tableau
        }
        return $value;
    }
    
    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    public function removeEmptyKeys($inputArray) {
        // Parcourir le tableau et supprimer les clés dont les valeurs sont vides et qui ne sont pas des tableaux
        foreach ($inputArray as $key => $value) {
            if (!is_array($value) && (is_null($value) || $value === '')) {
                unset($inputArray[$key]); // Supprimer la clé si elle est vide et non un tableau
            }
        }
        return $inputArray;
    }

    
public function flattenArrayEbay($array) {
    $flatArray = [];

    // Parcourir chaque élément du tableau
    foreach ($array as $item) {
        // Si l'élément est un tableau, appeler récursivement la fonction
        if (is_array($item)) {
            $flatArray = array_merge($flatArray, $this->flattenArray($item));
        } else {
            // Sinon, ajouter l'élément au tableau final
            $flatArray[] = $item;
        }
    }
   //print("<pre>".print_r (1730,true )."</pre>");
  //print("<pre>".print_r ($flatArray,true )."</pre>");
    return $flatArray;
}

public function splitNamesEbay($names) {
    $delimiters = ['^',  ';', ':',  '/'];

    // Remplacer les guillemets simples et doubles par des chaînes vides
    $names = str_replace(["'", '"'], '', $names);

    // Par défaut, s'il n'y a pas de délimiteur, on garde le nom comme un seul élément
    $namesArray = [$names];

    // Vérifier quel délimiteur est présent et séparer les noms
    foreach ($delimiters as $delimiter) {
        if (strpos($names, $delimiter) !== false) {
            $namesArray = explode($delimiter, $names);
            break;
        }
    }

    // Si après la première séparation, un élément contient encore des délimiteurs, on effectue une nouvelle séparation
    foreach ($namesArray as $key => $value) {
        foreach ($delimiters as $delimiter) {
            if (strpos($value, $delimiter) !== false) {
                // Séparation et remplacement dans l'élément d'origine
                $newElements = explode($delimiter, $value);
                array_splice($namesArray, $key, 1, $newElements);
                break 2;  // Sortir des deux boucles pour recommencer la vérification globale
            }
        }
    }

    // Si le tableau contient exactement 2 éléments, on les inverse et on retourne une chaîne concaténée
    if (count($namesArray) === 2 && strpos($namesArray[0],' ')=== False &&  strpos($namesArray[1],' ')=== False) {
        $namesArray = array_reverse($namesArray);  // Inverser les mots
        $returnArray = [];

        // Supprimer les espaces supplémentaires et concaténer (pas de ucwords pour préserver Unicode)
        $returnArray[] = trim($namesArray[0]) . ' ' . trim($namesArray[1]);
        return $returnArray;  // Retourner le tableau contenant la chaîne inversée
    }

    // Appliquer trim et format de titre avec ucwords à chaque élément du tableau
    $namesArray = array_map(function($item) use ($delimiters) {
        // Vérifier et re-split si d'autres délimiteurs existent encore dans l'élément
        foreach ($delimiters as $delimiter) {
            if (strpos($item, $delimiter) !== false) {
                $itemArray = explode($delimiter, $item);
                $itemArray = array_map(function($subItem) {
                    return trim($subItem);
                }, $itemArray);
                return implode(' ', $itemArray);  // Recréer une chaîne propre à partir de l'élément splitté
            }
        }
        // Si pas d'autres délimiteurs, juste trim l'élément
        return trim($item);
    }, $namesArray);

    // Aplatir le tableau s'il contient des sous-tableaux
    return $this->flattenArray($namesArray);
}

public function custom_merge_recursive($array1, $array2) {
    foreach ($array2 as $key => $value) {
        if (is_array($value) && isset($array1[$key]) && is_array($array1[$key])) {
            $array1[$key] = $this->custom_merge_recursive($array1[$key], $value);
        } else {
            if ($key === 'Name' && is_array($value) && is_array($array1[$key])) {
                $array1[$key] = array_unique(array_merge($array1[$key], $value));
            } else {
                $array1[$key] = $value;
            }
        }
    }
    return $array1;
}
public function uploadImages($piclink, $product_id = null, $type = 'pri') {
    // Validate piclink is not empty
    if (empty($piclink)) {
        //error_log('uploadImages() called with empty piclink');
        return null;
    }
    
    //error_log("uploadImages() called: piclink=$piclink, product_id=$product_id, type=$type");
    
    // Calculer le sous-dossier à partir des deux premiers chiffres du product_id
    $sub_dir = $product_id !== null ? str_pad(substr($product_id, 0, 2), 2, '0', STR_PAD_LEFT) : 'temp';
    $uploads_dir = 'catalog/product/' . $sub_dir . '/' . ($product_id ?? 'temp');

    // Déterminer l'extension d'origine
    $extension_new = $this->getImageExtension($piclink);

    // Créer un nom de fichier unique
    $unique_id = ($product_id ?? 'temp') . $type . mt_rand(1, 99999);
    $filename = $unique_id . $extension_new;
    $imagepath = DIR_IMAGE . '/' . $uploads_dir . '/' . $filename;

    // Créer le dossier s'il n'existe pas
    if (!file_exists(DIR_IMAGE . '/' . $uploads_dir)) {
        mkdir(DIR_IMAGE . '/' . $uploads_dir, 0755, true);
    }

    // Télécharger l'image
    if ($this->saveImage($piclink, $imagepath)) {
        //error_log("uploadImages() image saved successfully to: $imagepath");
        
        // Si c'est déjà une image .webp, ne pas convertir
        if (strtolower($extension_new) === '.webp') {
            //error_log("uploadImages() returning (already webp): " . $uploads_dir . '/' . $filename);
            return $uploads_dir . '/' . $filename;
        }

        // Sinon, convertir en webp
        $webp_path = DIR_IMAGE . '/' . $uploads_dir . '/' . $unique_id . '.webp';
        if ($this->convertToWebp($imagepath, $webp_path)) {
            // Supprimer le fichier original après conversion réussie
            if (file_exists($imagepath)) {
                unlink($imagepath);
                //error_log("uploadImages() deleted original file: $imagepath");
            }
            //error_log("uploadImages() converted to webp, returning: " . $uploads_dir . '/' . $unique_id . '.webp');
            return $uploads_dir . '/' . $unique_id . '.webp';
        } else {
            //error_log("uploadImages() webp conversion failed, returning original: " . $uploads_dir . '/' . $filename);
            return $uploads_dir . '/' . $filename;
        }
    }
    
    //error_log("uploadImages() saveImage failed for: $piclink");
    return null; // Return null if saveImage failed
}


public function transferTempImages($product_id, $data) {
    // Calculer le sous-dossier
    $sub_dir = str_pad(substr($product_id, 0, 2), 2, '0', STR_PAD_LEFT);
    $product_dir = DIR_IMAGE . 'catalog/product/' . $sub_dir . '/' . $product_id . '/';

    // Créer le répertoire du produit s'il n'existe pas
    if (!file_exists($product_dir)) {
        mkdir($product_dir, 0755, true);
    }

    // Transférer les images de $data['product_image_temp']
    if (isset($data['product_image_temp'])) {
        $sort_order = 0;
        foreach ($data['product_image_temp'] as $product_image) {
            $temp_image_path = DIR_IMAGE . '/' . $product_image;

            $webp_image_path = str_replace('.jpg', '.webp', $temp_image_path);
            if (file_exists($webp_image_path)) {
                $temp_image_path = $webp_image_path;
            }

            $new_filename = str_replace('temp', $product_id, basename($temp_image_path));
            $new_image_path = $product_dir . '/' . $new_filename;

            if (file_exists($temp_image_path)) {
                rename($temp_image_path, $new_image_path);
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape('catalog/product/' . $sub_dir . '/' . $product_id . '/' . $new_filename) . "', sort_order = '" . (int)$sort_order . "'");
                $data['product_image'][] = $this->db->escape('catalog/product/' . $sub_dir . '/' . $product_id . '/' . $new_filename);
            }
            $sort_order++;
        }
    }

    if (isset($data['image_temp'])) {
        $temp_image_path = DIR_IMAGE . '/' . $data['image_temp'];
        $webp_image_path = str_replace('.jpg', '.webp', $temp_image_path);
        if (file_exists($webp_image_path)) {
            $temp_image_path = $webp_image_path;
        }

        $new_filename = str_replace('temp', $product_id, basename($temp_image_path));
        $new_image_path = $product_dir . '/' . $new_filename;

        if (file_exists($temp_image_path)) {
            rename($temp_image_path, $new_image_path);
            $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape('catalog/product/' . $sub_dir . '/' . $product_id . '/' . $new_filename) . "' WHERE product_id = '" . (int)$product_id . "'");
            $data['image'] = $this->db->escape('catalog/product/' . $sub_dir . '/' . $product_id . '/' . $new_filename);
        }
    }

    $this->clearTempImages();

    return $data;
}

public function convertToWebp($sourceImagePath, $destinationWebpPath, $quality = 80) {
    // Définir le chemin du fichier de log
    $logFilePath = DIR_IMAGE . 'error.log';

    // Vérifier si Imagick est disponible
    if (!extension_loaded('imagick')) {
        $errorMessage = "Imagick extension is not loaded.\n";
        file_put_contents($logFilePath, $errorMessage, FILE_APPEND);
        return false;
    }

    // Vérifier si le fichier source existe
    if (!file_exists($sourceImagePath)) {
        $errorMessage = "Source file does not exist: $sourceImagePath\n";
        file_put_contents($logFilePath, $errorMessage, FILE_APPEND);
        return false;
    }

    try {
        // Créer une nouvelle instance d'Imagick (classe globale PHP)
        $image = new \Imagick($sourceImagePath);

        // Convertir l'image en WebP
        $image->setImageFormat('webp');
        $image->setImageCompressionQuality($quality);

        // Sauvegarder l'image convertie
        $writeResult = $image->writeImage($destinationWebpPath);

        // Libérer la mémoire
        $image->clear();
        $image->destroy();

        // Vérifier si le fichier existe vraiment après l'écriture
        if (file_exists($destinationWebpPath)) {
            $successMessage = "Conversion to WebP successful for file: $sourceImagePath -> $destinationWebpPath (size: " . filesize($destinationWebpPath) . " bytes)\n";
            file_put_contents($logFilePath, $successMessage, FILE_APPEND);
            return true;
        } else {
            $errorMessage = "WebP file not found after conversion: $destinationWebpPath\n";
            file_put_contents($logFilePath, $errorMessage, FILE_APPEND);
            return false;
        }
    } catch (\ImagickException $e) {
        // Enregistrer l'erreur dans le fichier error.log
        $errorMessage = "Failed to convert to WebP for file $sourceImagePath: " . $e->getMessage() . "\n";
        file_put_contents($logFilePath, $errorMessage, FILE_APPEND);

        return false;
    }
}

public function manualResize($filename, $width, $height) {
    // Créer le chemin du cache manuellement
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $cache_filename = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
    $source_path = DIR_IMAGE . $filename;
    $cache_path = DIR_IMAGE . $cache_filename;
    
    // Créer le répertoire cache si nécessaire
    $cache_dir = dirname($cache_path);
    if (!is_dir($cache_dir)) {
        mkdir($cache_dir, 0755, true);
    }
    
    // Si le cache existe déjà, le retourner
    if (file_exists($cache_path)) {
        return HTTP_CATALOG . 'image/' . $cache_filename;
    }
    
    // Vérifier le type d'image
    $info = @getimagesize($source_path);
    if (!$info) {
        return HTTP_CATALOG . 'image/' . $filename;
    }
    
    // Si c'est AVIF (Type 19), utiliser avifdec + convert
    if ($info[2] == 19) {
        $temp_png = sys_get_temp_dir() . '/' . uniqid('avif_') . '.png';
        
        // Décoder AVIF en PNG
        exec("avifdec " . escapeshellarg($source_path) . " " . escapeshellarg($temp_png) . " 2>&1", $output, $return_code);
        
        if ($return_code === 0 && file_exists($temp_png)) {
            // Redimensionner et convertir en WebP
            exec("convert " . escapeshellarg($temp_png) . " -resize " . $width . "x" . $height . " " . escapeshellarg($cache_path) . " 2>&1", $output2, $return_code2);
            
            @unlink($temp_png);
            
            if ($return_code2 === 0 && file_exists($cache_path)) {
                return HTTP_CATALOG . 'image/' . $cache_filename;
            }
        }
        
        // Si la conversion échoue, retourner l'original
        return HTTP_CATALOG . 'image/' . $filename;
    }
    
    // Pour les autres formats (JPEG, PNG, GIF, WebP), utiliser GD
    try {
        $source_image = null;
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $source_image = imagecreatefromjpeg($source_path);
                break;
            case IMAGETYPE_PNG:
                $source_image = imagecreatefrompng($source_path);
                break;
            case IMAGETYPE_GIF:
                $source_image = imagecreatefromgif($source_path);
                break;
            case IMAGETYPE_WEBP:
                $source_image = imagecreatefromwebp($source_path);
                break;
            default:
                return HTTP_CATALOG . 'image/' . $filename;
        }
        
        if (!$source_image) {
            return HTTP_CATALOG . 'image/' . $filename;
        }
        
        // Créer l'image redimensionnée
        $dest_image = imagecreatetruecolor($width, $height);
        
        // Préserver la transparence pour PNG
        if ($info[2] == IMAGETYPE_PNG) {
            imagealphablending($dest_image, false);
            imagesavealpha($dest_image, true);
        }
        
        imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
        
        // Sauvegarder selon l'extension
        if ($extension === 'webp') {
            imagewebp($dest_image, $cache_path, 80);
        } elseif ($extension === 'png') {
            imagepng($dest_image, $cache_path);
        } else {
            imagejpeg($dest_image, $cache_path, 80);
        }
        
        imagedestroy($source_image);
        imagedestroy($dest_image);
        
        return HTTP_CATALOG . 'image/' . $cache_filename;
    } catch (\Exception $e) {
        return HTTP_CATALOG . 'image/' . $filename;
    }
}

   
public function getImageExtension($piclink) {
    if (empty($piclink)) {
        return '.jpg'; // Default extension for empty input
    }
    $extensions = ['.webp', '.jpeg', '.png', '.jpg', '.gif'];
    foreach ($extensions as $ext) {
        if (strpos(strtolower($piclink), $ext) !== false) {
            return '' . $ext;
        }
    }
    return '.jpg'; // Default to .jpg if no known extension is found
}

private function saveImage($piclink, $imagepath) {
    // Validate piclink is not empty
    if (empty($piclink)) {
        return false;
    }
    
    // Initialiser cURL
    $ch = curl_init($piclink);
    
    // Configurer les options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Suivre les redirections
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Pour les URLs HTTPS
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout de 30 secondes
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'); // User-Agent pour éviter le blocage eBay
    curl_setopt($ch, CURLOPT_REFERER, 'https://www.ebay.com/'); // Referer pour eBay

    // Exécuter la requête
    $imageContent = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Vérifier les erreurs cURL
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        
        //error_log('cURL error downloading image from ' . $piclink . ': ' . $error);
        return false;
    }

    // Fermer cURL
    

    // Vérifier le code HTTP
    if ($httpCode !== 200) {
        //error_log('HTTP error ' . $httpCode . ' downloading image from ' . $piclink);
        return false;
    }

    // Sauvegarder l'image sur le disque
    if ($imageContent !== false && !empty($imageContent)) {
        $result = file_put_contents($imagepath, $imageContent);
        if ($result === false) {
            //error_log('Failed to write image to ' . $imagepath);
            return false;
        }
        return $result;
    }
    return false;
}

private function clearTempImages() {
    $temp_dir = DIR_IMAGE . 'catalog/product/temp/';
    
    // Parcourir les fichiers dans le répertoire temporaire
    $files = glob($temp_dir . '/*');
    
    // Supprimer chaque fichier dans le répertoire temporaire
    if (!empty($files)) {
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file); // Supprimer physiquement l'image
            }
        }
    //	echo "Toutes les images temporaires ont été supprimées.";
    } else {
    //	echo "Aucune image temporaire à supprimer.";
    }
}

/**
 * Fonction pour extraire les URLs d'images à partir de divers formats de contenu (HTML, JSON).
 *
 * @param string $htmlContent Le contenu HTML duquel extraire les URLs.
 * @return array $imageUrls Tableau des URLs d'images extraites.
 */
public function extractImageUrls($htmlContent) {
    // Initialiser un tableau pour stocker les URLs des images
    $imageUrls = [];
	
	if(is_numeric($htmlContent)){
		$this->load->model('warehouse/marketplace/ebay/api');
		$imageUrls=$this->model_warehouse_marketplace_ebay_api->getProductImages(trim($htmlContent)); 
      //print("<pre>" . print_r(448, true) . "</pre>");
	  //print("<pre>" . print_r($imageUrls, true) . "</pre>");
      return $imageUrls;
	}else{
		// Si c'est une URL (pas du HTML), récupérer le contenu de la page via cURL
		$trimmed = trim($htmlContent);
		if (preg_match('/^https?:\/\/[^\s<>"]+$/i', $trimmed) && !preg_match('/<[a-z][\s\S]*>/i', $trimmed)) {
			$fetchedHtml = $this->fetchUrlContent($trimmed);
			if ($fetchedHtml) {
				$htmlContent = $fetchedHtml;
			}
		}

		$htmlContent = html_entity_decode($htmlContent);
		// Première tentative : Utiliser une expression régulière pour chercher les URLs avec ProductImage
		preg_match_all('/https:\/\/[^\s"]*ProductImage\/[^\s"]+\.(jpg|jpeg|png|gif)/', $htmlContent, $matches);
	//	//print("<pre>" . print_r($matches, true) . "</pre>");

		// Si des images sont trouvées, les ajouter au tableau
		if (!empty($matches[0])) {
           //print("<pre>" . print_r(value: 459) . "</pre>");
			$imageUrls = $matches[0];
		}

		// Si aucune image n'est trouvée, essayer de chercher dans les scripts <script type="application/ld+json">
	
		if (empty($imageUrls)) {
			$imageUrls = $this->get_images_walmart_com($htmlContent);
      //print("<pre>" . print_r(467) . "</pre>");
      //print("<pre>" . print_r($imageUrls, true) . "</pre>");

		}

		if (empty($imageUrls)) {
        //print("<pre>" . print_r(473) . "</pre>");
			$imageUrls = $this->get_images_walmart_ca($htmlContent);
		}
		if (empty($imageUrls)) {
       //print("<pre>" . print_r(value: 477) . "</pre>");
			$imageUrls = $this->get_images_amazon($htmlContent);
		}
		if (empty($imageUrls)) {
       //print("<pre>" . print_r(value: 481) . "</pre>");
			$imageUrls = $this->get_images_toys_r_com($htmlContent);
		}
		if (empty($imageUrls)) {
         //print("<pre>" . print_r(485) . "</pre>");
			$imageUrls = $this->get_images_toys_r_ca($htmlContent);
		//    $imageUrls = array_merge($imageUrls, $jsonImageUrls);
			
		}
	}
    // Nettoyer les URLs pour enlever les paramètres (par exemple ?sw=767&sh=767&sm=fit)
    foreach ($imageUrls as $key => $url) {
        $imageUrls[$key] = strtok($url, '?'); // Enlever tout ce qui vient après le '?'
    }
	//$imageUrls = $this->removeArrayDuplicates($imageUrls);
    return $imageUrls;
}

/**
 * Récupère le contenu HTML d'une URL via cURL
 * Utilisé quand l'utilisateur colle un lien au lieu du code source
 */
private function fetchUrlContent(string $url): string|false {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_ENCODING, ''); // Accept all encodings
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($content && $httpCode >= 200 && $httpCode < 400) {
        return $content;
    }

    return false;
}

private function get_images_toys_r_ca($htmlContent) {
    // Initialiser un tableau pour stocker les URLs d'images
    $imageUrls = [];

    // Utiliser une expression régulière pour capturer les URLs d'images dans les attributs 'image' des balises JSON
    preg_match_all('/"image"\s*:\s*\[\s*"(https:\/\/[^\s"]+\.(jpg|jpeg|png|gif))"(?:\s*,\s*"(https:\/\/[^\s"]+\.(jpg|jpeg|png|gif))")*\s*\]/i', $htmlContent, $matches);

    // Parcourir les correspondances trouvées
    if (!empty($matches[1])) {
        foreach ($matches[0] as $match) {
            // Extraire toutes les URL entre guillemets dans la correspondance
            preg_match_all('/https:\/\/[^\s"]+\.(jpg|jpeg|png|gif)/i', $match, $imgUrls);
            if (!empty($imgUrls[0])) {
                $imageUrls = array_merge($imageUrls, $imgUrls[0]);
            }
        }
    }

    // Nettoyer les URLs pour enlever les paramètres (par exemple ?sw=767&sh=767&sm=fit)
    foreach ($imageUrls as $key => $url) {
        $imageUrls[$key] = strtok($url, '?'); // Enlever tout ce qui vient après le '?'
    }
	//$imageUrls = $this->removeArrayDuplicates($imageUrls);
    return $imageUrls;
}


private function get_images_toys_r_com($htmlContent) {
    $imageUrls = [];
   

  
	
	  // Utiliser une expression régulière pour trouver les balises <img> dans le <div class="product__media media media--transparent">
	//  preg_match_all('/<div class="product__media media media--transparent">.*?<img[^>]+src="(\/\/[^\s"]+\.(jpg|jpeg|png|gif))"/is', $htmlContent, $matches);
	  preg_match_all('/<div class="product__media media media--transparent">.*?<\/div>/is', $htmlContent, $matches);

	//print("<pre>" . print_r($matches, true) . "</pre>");
	  // Vérifier si des correspondances ont été trouvées
	  if (!empty($matches[0])) {
        // Pour chaque div trouvé, rechercher les balises <img> et extraire les URLs
        foreach ($matches[0] as $divContent) {
			$imgMatches=[];
            // Utiliser une expression régulière pour trouver les URLs dans les balises <img> à l'intérieur de chaque div
           // preg_match_all('/<img[^>]+src="(\/\/[^\s"]+\.(jpg|jpeg|png|gif))"/i', $divContent, $imgMatches);
			preg_match_all('/<img[^>]+src="(\/\/[^\s"]+\.(jpg|jpeg|png|gif))(?=\?|")/i', $divContent, $imgMatches);

		//	//print("<pre>" . print_r($imgMatches, true) . "</pre>");
            if (!empty($imgMatches[1])) {
                foreach ($imgMatches[1] as $url) {
                    // Ajouter le préfixe "https:" pour compléter l'URL et supprimer les paramètres après le ?
                    $cleanUrl = 'https:' . strtok($url, '?');
                    $imageUrls[] = $cleanUrl;
                }
            }
        }
    }
	//$imageUrls = $this->removeArrayDuplicates($imageUrls);
    return $imageUrls;
}



private function get_images_amazon($htmlContent) {
    $imageUrls = [];

    // Décoder les entités HTML au cas où
    $htmlContent = html_entity_decode($htmlContent);

    // Rechercher toutes les occurrences de la structure 'hiRes' avec l'URL correspondante
    preg_match_all('/"hiRes":"(https:\/\/[^\s"]+\.(jpg|jpeg|png|gif))"/i', $htmlContent, $matches);
//	//print("<pre>" . print_r($matches, true) . "</pre>");
    if (!empty($matches[1])) {
        foreach ($matches[1] as $url) {
            // Nettoyer l'URL et ajouter au tableau
            $cleanUrl = strtok($url, '?'); // Enlever les paramètres après le ?
            $imageUrls[] = $cleanUrl;
        }
    }
	//$imageUrls = $this->removeArrayDuplicates($imageUrls);


    return $imageUrls;
}

private function get_images_walmart_ca($htmlContent) {
    // Initialiser un tableau pour stocker les URLs des images
    $imageUrls = [];

    // Utiliser une expression régulière pour capturer les URLs d'images dans les balises <link> ayant l'attribut as="image"
  //  preg_match_all('/<link[^>]+as="image"[^>]+href="(https:\/\/[^\s"]+\.(jpg|jpeg|png|gif))"/i', $htmlContent, $matches);
//	preg_match_all('/<link[^>]+as="image"[^>]+href="(https:\/\/[^"]*Enlarge[^\s"]+\.(jpg|jpeg|png|gif))"/i', $htmlContent, $matches);
preg_match_all('/<img[^>]+srcset="([^"]*Enlarge[^"]*)"/i', $htmlContent, $matches);

	//print("<pre>" . print_r($matches, true) . "</pre>");

    // Si des correspondances sont trouvées
    if (!empty($matches[1])) {
        foreach ($matches[1] as $url) {
            // Ajouter l'URL au tableau des images
            $imageUrls[] = $url;
        }
    }

    // Nettoyer les URLs pour enlever les paramètres (par exemple ?odnHeight=612&odnWidth=612&odnBg=FFFFFF)
    foreach ($imageUrls as $key => $url) {
        $imageUrls[$key] = strtok($url, '?'); // Enlever tout ce qui vient après le '?'
    }
//	$imageUrls = $this->removeArrayDuplicates($imageUrls);
    return $imageUrls;
}

private function get_images_walmart_com($htmlContent) {
    // Initialiser un tableau pour stocker les URLs des images
    $imageUrls = [];

    // Utiliser une expression régulière pour capturer les URLs d'images dans les balises <img> ayant l'attribut "srcset"
    // Les URL doivent contenir soit "asr" soit "seo"
   // preg_match_all('/<img[^>]+srcset="([^"]*(asr|seo)[^"]*)"/i', $htmlContent, $matches);
  //  preg_match_all('/<img[^>]+srcset="([^"]*)"/i', $htmlContent, $matches);
  //  preg_match_all('/<img[^>]+(?:src|srcset)="([^"]*\.(?:jpg|jpeg|png|gif|webp|svg))"/i', $htmlContent, $matches);
   // preg_match_all('/"url":"(https?:\/\/[^"]*\.(?:jpg|jpeg|png|gif|webp|svg))"/i', $htmlContent, $matches);
    preg_match_all('/"url":"(https?:\/\/[^"]*\.(?:jpg|jpeg|png|gif|webp|svg))","zoomable":true/i', $htmlContent, $matches);

    // Affiche les URL des images trouvées
 //   print_r($matches[1]);
    

	//print("<pre>" . print_r($matches, true) . "</pre>");
    // Si des correspondances sont trouvées dans le "srcset"
    if (!empty($matches[1])) {
        foreach ($matches[1] as $srcset) {
            // Séparer les différentes URLs contenues dans l'attribut "srcset" en utilisant la virgule comme délimiteur
            $urls = explode(',', $srcset);
			
            foreach ($urls as $url) {
                // Nettoyer chaque URL pour enlever les attributs additionnels après le premier espace et après le '?'
                $cleanUrl = strtok(trim($url), ' ');
                $cleanUrl = strtok($cleanUrl, '?'); // Enlever tout ce qui vient après le '?'
                
                // Ajouter l'URL nettoyée au tableau
                $imageUrls[] = $cleanUrl;
            }
        }
		//print("<pre>" . print_r($imageUrls, true) . "</pre>");
    }

    // Utiliser une deuxième expression régulière pour capturer les URLs d'images dans l'attribut "src" des balises <img> contenant "asr" ou "seo"
    preg_match_all('/<img[^>]+src="([^"]*(asr|seo)[^"]*)"/i', $htmlContent, $srcMatches);

    // Si des correspondances sont trouvées dans l'attribut "src"
    if (!empty($srcMatches[1])) {
        foreach ($srcMatches[1] as $url) {
            // Nettoyer l'URL pour enlever les attributs additionnels
            $cleanUrl = strtok($url, '?'); // Enlever tout ce qui vient après le '?'
            
            // Ajouter l'URL nettoyée au tableau
            $imageUrls[] = $cleanUrl;
        }
    }

    // Supprimer les doublons éventuels
    $imageUrls = array_unique($imageUrls);
	//$imageUrls = $this->removeArrayDuplicates($imageUrls);
    return $imageUrls;
}

public function escape_special_chars($xml_string) {
    $replacements = array(
        '&' => '&amp;',
    
    );

    return str_replace(array_keys($replacements), array_values($replacements), $xml_string);
}
private function flattenArrayDUPLI($array) {
    $flatArray = [];

    foreach ($array as $item) {
        if (is_array($item)) {
            // Appel récursif pour aplatir les tableaux imbriqués
            $flatArray = array_merge($flatArray, $this->flattenArray($item));
        } else {
            $flatArray[] = $item;
        }
    }

    return $flatArray;
}

public function convert_smart_quotes($string) 
        { 
            $search = array(chr(145), 
                            chr(146), 
                            chr(147), 
                            chr(148), 
                            chr(151)); 
            $replace = array("'", 
                             "'", 
                             '"', 
                             '"', 
                             '-'); 
            return str_replace($search, $replace, $string); 
        }

       
public function countUppercase($input) {
 

        // Vérifier que l'entrée est bien une chaîne de caractères
        if (!is_string($input)) {
            // Affiche un message pour indiquer l'erreur et la valeur de l'entrée
       //print("<pre>" . print_r(('786:tools.php'), true) . "</pre>");
      //print("<pre>" . print_r($input, true) . "</pre>");
      //print("<pre>" . print_r(count($input), true) . "</pre>");
            return 0; // Retourne 0 ou une valeur par défaut si ce n'est pas une chaîne
        }
    
        // Compte le nombre de lettres majuscules dans la chaîne
        preg_match_all('/[A-Z]/', $input, $matches);
        return count($matches[0]);
    }
    

public function countWords($string) {
    return str_word_count($string);
}




public function generateKeywordPermutations($keywordsString) {
    // Vérifiez si l'entrée est un tableau, et si oui, combinez les éléments en une chaîne
    if (is_array($keywordsString)) {
        $keywordsString = implode(' ', $keywordsString);
    }

    // S'assurer que c'est bien une chaîne de caractères
    if (!is_string($keywordsString)) {
        throw new \InvalidArgumentException('Le paramètre keywordsString doit être une chaîne de caractères ou un tableau.');
    }

    // Supprimer les caractères inutiles (par exemple, doubles espaces)
    $keywordsString = trim($this->cleanString( $keywordsString));
  // echo 'allo';
    // Séparer les mots-clés
    $keywordsArray = explode(' ', $keywordsString);

    // Filtrer les mots-clés : conserver ceux avec 4 caractères ou plus,
    // ou ceux qui contiennent des chiffres ou des caractères spéciaux.
    $filteredKeywords = array_filter($keywordsArray, function ($word) {
        return strlen($word) >= 4 || $this->cleanString( $word);
       // echo 'allo';
    });

    $permutations = [];

    // Générer des groupes de 3 ou 4 mots-clés
    for ($i = 0; $i < count($filteredKeywords); $i++) {

          // Permuter 1 mots
          $group1 = array_slice($filteredKeywords, $i, 1);
          if (count($group1) === 1 && strlen($group1[0]) > 4) {
              $permutations[] = implode(' ', $group1);
          }
      
          // Permuter 2 mots
          $group2 = array_slice($filteredKeywords, $i, 2);
          if (count($group2) === 2 && $this->allWordsGreaterThan($group2, 4)) {
              $permutations[] = implode(' ', $group2);
          }
        // Permuter 3 mots
        $group3 = array_slice($filteredKeywords, $i, 3);
        if (count($group3) === 3) {
            $permutations[] = implode(' ', $group3);
        }

        // Permuter 4 mots
        $group4 = array_slice($filteredKeywords, $i, 4);
        if (count($group4) === 4) {
            $permutations[] = implode(' ', $group4);
        }

       
    }

    // Supprimer les doublons pour éviter des recherches redondantes
    $permutations = array_unique($permutations);
 //print("<pre>".print_r (871,true )."</pre>");
//print("<pre>".print_r ($permutations,true )."</pre>");   
    return $permutations;
}
public function extractUPC($sku) {
    // Utilise une expression régulière pour ne garder que les chiffres
    return preg_replace('/\D/', '', $sku);
}





private function allWordsGreaterThan($words, $length) {
    foreach ($words as $word) {
        if (strlen($word) <= $length) {
            return false;
        }
    }
    return true;
}


public function addProductImage($product_id, $file, $type = 'pri') {
    $this->load->model('warehouse/tools/utility');

    // Calculer le sous-dossier à partir des deux premiers chiffres du product_id
    $sub_dir = str_pad(substr($product_id, 0, 2), 2, '0', STR_PAD_LEFT);
    $upload_dir = DIR_IMAGE . 'catalog/product/' . $sub_dir . '/' . $product_id . '/';
    
    // Créer le répertoire du produit s'il n'existe pas
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Détermine l'extension de l'image
    $extension_new = $this->model_warehouse_tools_utility->getImageExtension($file['name']);
    $filename_rand = $product_id . $type . mt_rand(1, 99999);
    $filename = $filename_rand . $extension_new;
    $target_file = $upload_dir . $filename;
    $image_path = null;

    if (move_uploaded_file($file['tmp_name'], $target_file)) {

        // Chemin pour le fichier WebP converti
        $webp_filename = $filename_rand . '.webp';
        $webp_file_path = $upload_dir . $webp_filename;

        // Détecter si c'est un fichier AVIF déguisé en JPG
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $target_file);
        finfo_close($finfo);
        
        $is_avif = ($mime_type === 'image/avif' || strpos($mime_type, 'avif') !== false);

        // Si le fichier est déjà en WebP, pas besoin de convertir
        if (strtolower($extension_new) === '.webp') {
            $filename = $webp_filename;
            // Renommer si nécessaire (si l'extension était en majuscules par exemple)
            if ($target_file !== $webp_file_path) {
                rename($target_file, $webp_file_path);
            }
        } elseif ($is_avif) {
            // AVIF détecté - Renommer en .webp (navigateurs modernes supportent AVIF)
            rename($target_file, $webp_file_path);
            $filename = $webp_filename;
        } else {
            // Convertir l'image en WebP (JPG, PNG, etc.)
            if ($this->model_warehouse_tools_utility->convertToWebp($target_file, $webp_file_path)) {
                // Utiliser le fichier WebP dans la base de données et supprimer l'original
                unlink($target_file);
                $filename = $webp_filename;
            } else {
                // En cas d'échec de conversion, conserver l'image originale
                $filename = basename($target_file);
            }
        }

        // Vérifier si c'est l'image principale ou non
        $image_path = 'catalog/product/' . $sub_dir . '/' . $product_id . '/' . $filename;
        if ($type == 'pri') {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($image_path) . "' WHERE product_id = '" . (int)$product_id . "'");
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($image_path) . "'");
        }
    } else {
        //error_log("Failed to move uploaded file: " . $file['tmp_name'] . " to " . $target_file);
    }

    return $image_path;
}
public function deleteProductImagesFiles($product_id = 0, $type = '') {
    // Calculer le sous-dossier à partir des deux premiers chiffres du product_id
    $sub_dir = str_pad(substr($product_id, 0, 2), 2, '0', STR_PAD_LEFT);
    $product_id_dir = DIR_IMAGE . 'catalog/product/' . $sub_dir . '/' . $product_id . '/';
    
    // Parcourir les fichiers dans le répertoire du produit
    $files = glob($product_id_dir . '/*' . $type);
    
    // Supprimer chaque fichier dans le répertoire du produit
    if (!empty($files)) {
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file); // Supprimer physiquement l'image
            }
        }
    }
}

public function deleteProductImages($product_id, $type = 'all') {
    // Le paramètre $type peut être 'all', 'principal', ou 'secondary'

    if ($type == 'all' || $type == 'pri') {
        // Supprimer l'image principale
		$this->deleteProductImagesFiles($product_id, 'pri*');
        // Mettre à jour la base de données
        $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '' WHERE product_id = '" . (int)$product_id . "'");
    }

    if ($type == 'all' || $type == 'sec') {
        // Supprimer les images supplémentaires
		$this->deleteProductImagesFiles($product_id, 'sec*');
        // Supprimer les entrées de la base de données
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
    }
}

public function deleteProductImage($product_id, $image = '', $type = 'sec') {
    // Le paramètre $type peut être 'all', 'principal', ou 'secondary'

    if ($type == 'pri') {
        // Supprimer l'image principale
		$this->deleteProductImagesFile($image);
        // Mettre à jour la base de données
        $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '' WHERE product_id = '" . (int)$product_id . "' AND image = '".$image."'");
		return true;
    }elseif ($type == 'sec') {
        // Supprimer les images supplémentaires
		$this->deleteProductImagesFile($image);
        // Supprimer les entrées de la base de données
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		return true;
    }else{
		return false;
	}
}


public function deleteProductImagesFile($image='') {
		
		
    if($image!=''){
    
    // Parcourir les fichiers dans le répertoire temporaire
        $files = glob( DIR_IMAGE .$image);
        
        // Supprimer chaque fichier dans le répertoire temporaire
        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file); // Supprimer physiquement l'image
                }
            }
        //	echo "Toutes les images temporaires ont été supprimées.";
        } else {
        //	echo "Aucune image temporaire à supprimer.";
        }
    }
}


public function processCategorySpecifics($source_value, $category_specific_info) {
    $categorySpecifics = [];
    $decoded_string='';
    // Parcourir chaque aspect et sa valeur dans $source_value
    foreach ($source_value as $aspect => $value) {
        // Vérifie si $category_specific_info['specifics'] existe et contient l'aspect
        $category_specific_tocheck = $category_specific_info['specifics'][$aspect] ?? null;

        if ($category_specific_tocheck && isset($category_specific_tocheck['aspectConstraint']['itemToAspectCardinality'])) {
            $cardinality = $category_specific_tocheck['aspectConstraint']['itemToAspectCardinality'];

            // Vérifier et formater en fonction de 'itemToAspectCardinality'
            if ($cardinality == 'SINGLE' && $category_specific_tocheck['aspectConstraint']['aspectMode'] == 'SELECTION_ONLY') {
                $string = is_array($value) ? implode(' ', $value) : str_replace(',', ' ', $value);
                $decoded_string = trim(html_entity_decode($string, ENT_QUOTES | ENT_HTML5));
            } elseif ($cardinality == 'SINGLE' && $category_specific_tocheck['aspectConstraint']['aspectMode'] == 'FREE_TEXT') {
                $string = is_array($value) ? implode('@@', $value) : $value;
                $decoded_string = trim(html_entity_decode($string, ENT_QUOTES | ENT_HTML5));
            } elseif ($cardinality == 'MULTI') {
                $string = is_array($value) ? implode('@@', $value) : $value;
                $decoded_string = trim(html_entity_decode($string, ENT_QUOTES | ENT_HTML5));
            }
        } else {
            // Si aucun aspectConstraint n'est défini, traiter comme un texte simple
            $string = is_array($value) ? implode(',', $value) : $value;
            $decoded_string = trim(html_entity_decode($string, ENT_QUOTES | ENT_HTML5));
        }

        // Si le string décodé n'est pas vide, l'ajouter à categorySpecifics
        if (!empty($decoded_string)) {
            $categorySpecifics[$aspect] = $decoded_string;
        } else {
            // Supprime l'aspect si la valeur est vide
            unset($source_value[$aspect]);
        }
    }

    return $categorySpecifics;
}

public function compareSources($epid_sources, $source_value) {
    $finalArray = [];
  //print("<pre>".print_r ('jo114',true )."</pre>");
 //print("<pre>".print_r ($epid_sources,true )."</pre>");
 //print("<pre>".print_r ($source_value,true )."</pre>");
 $this->load->model('warehouse/tools/utility');
 $this->load->model('warehouse/tools/ai');
    foreach ($source_value as $key => $value) {
        // Si la clé existe dans epid_sources, on priorise sa valeur
        if (isset($epid_sources[$key])) {
            if( is_array($value) && !is_array($epid_sources[$key])){
                if(count($value) >10){
                    // $this->model_warehouse_tools_ai->reduceArrayValue($decoded_value,$key);
                    //$value=$this->model_warehouse_tools_ai->reduceArrayValue($value,$key);
                }
                $finalArray[$key]= $value;
                $finalArray[$key][]=$epid_sources[$key];
            }elseif (is_array($value) && is_array($epid_sources[$key]) && (count($value) <> count($epid_sources[$key]))){
                if(count($value) >10){
                    // $this->model_warehouse_tools_ai->reduceArrayValue($decoded_value,$key);
                    //$value=$this->model_warehouse_tools_ai->reduceArrayValue($value,$key);
                }
                $finalArray[$key] = $this->model_warehouse_tools_utility->cleanArray(array_merge_recursive($epid_sources[$key],$value));
            }else{
                $finalArray[$key] = $epid_sources[$key];
            }
            
         
        } else {
            // Sinon, on utilise la valeur de source_value
            
            if(is_array($value) && count($value) >10){
                // $this->model_warehouse_tools_ai->reduceArrayValue($decoded_value,$key);
                $finalArray[$key]= $value; //$this->model_warehouse_tools_ai->reduceArrayValue($value,$key);
            }else{
                $finalArray[$key] = $value;
            }
        }
        unset($epid_sources[$key]);
    }
 //print("<pre>".print_r (array_merge_recursive($finalArray,$epid_sources),true )."</pre>");
    return array_merge_recursive($finalArray,$epid_sources);
}
public function splitNames($names) {
    $delimiters = ['^', ',', ';', ':', '/'];

    // Remplacer les guillemets simples et doubles par des chaînes vides
    $names = str_replace(["'", '"'], '', $names);

    // Par défaut, s'il n'y a pas de délimiteur, on garde le nom comme un seul élément
    $namesArray = [$names];

    // Vérifier quel délimiteur est présent et séparer les noms
    foreach ($delimiters as $delimiter) {
        if (strpos($names, $delimiter) !== false) {
            $namesArray = explode($delimiter, $names);
            break;
        }
    }

    // Si après la première séparation, un élément contient encore des délimiteurs, on effectue une nouvelle séparation
    foreach ($namesArray as $key => $value) {
        foreach ($delimiters as $delimiter) {
            if (strpos($value, $delimiter) !== false) {
                // Séparation et remplacement dans l'élément d'origine
                $newElements = explode($delimiter, $value);
                array_splice($namesArray, $key, 1, $newElements);
                break 2;  // Sortir des deux boucles pour recommencer la vérification globale
            }
        }
    }

    // Si le tableau contient exactement 2 éléments, on les inverse et on retourne une chaîne concaténée
    if (count($namesArray) === 2 && strpos($namesArray[0],' ')=== False &&  strpos($namesArray[1],' ')=== False) {
        $namesArray = array_reverse($namesArray);  // Inverser les mots
        $returnArray = [];

        // Supprimer les espaces supplémentaires et concaténer (pas de ucwords pour préserver Unicode)
        $returnArray[] = trim($namesArray[0]) . ' ' . trim($namesArray[1]);
        return $returnArray;  // Retourner le tableau contenant la chaîne inversée
    }

    // Appliquer trim et format de titre avec ucwords à chaque élément du tableau
    $namesArray = array_map(function($item) use ($delimiters) {
        // Vérifier et re-split si d'autres délimiteurs existent encore dans l'élément
        foreach ($delimiters as $delimiter) {
            if (strpos($item, $delimiter) !== false) {
                $itemArray = explode($delimiter, $item);
                $itemArray = array_map(function($subItem) {
                    return trim($subItem);
                }, $itemArray);
                return implode(' ', $itemArray);  // Recréer une chaîne propre à partir de l'élément splitté
            }
        }
        // Si pas d'autres délimiteurs, juste trim l'élément
        return trim($item);
    }, $namesArray);

    // Aplatir le tableau s'il contient des sous-tableaux
    return $this->flattenArray($namesArray);
}
public function flattenArray($array) {
    $flatArray = []; 

    // Parcourir chaque élément du tableau
    foreach ($array as $item) {
        // Si l'élément est un tableau, appeler récursivement la fonction
        if (is_array($item)) {
            $flatArray = array_merge($flatArray, $this->flattenArray($item));
        } else {
            // Sinon, ajouter l'élément au tableau final
            $flatArray[] = $item;
        }
    }
   //print("<pre>".print_r (1730,true )."</pre>");
  //print("<pre>".print_r ($flatArray,true )."</pre>");
    return $flatArray;
}

public function replaceImageBackground($sourceImage, $newColor = [200, 200, 200]) {
    $img = imagecreatefrompng(DIR_IMAGE . $sourceImage);
    if (!$img) {
        return $sourceImage; // Retourne l'image d'origine si erreur
    }

    $width = imagesx($img);
    $height = imagesy($img);

    $newImg = imagecreatetruecolor($width, $height);
    $color = imagecolorallocate($newImg, $newColor[0], $newColor[1], $newColor[2]); // Nouvelle couleur de fond

    imagefill($newImg, 0, 0, $color);
    imagecopy($newImg, $img, 0, 0, 0, 0, $width, $height);

    // Sauvegarde temporaire 
    $tempFile = 'cache/custom_' . basename($sourceImage);
    imagepng($newImg, DIR_IMAGE . $tempFile);
    
    imagedestroy($img);
    imagedestroy($newImg);

    return $tempFile;
}

public function MultiSort($data, $sortCriteria, $caseInSensitive = true)
{
  if( !is_array($data) || !is_array($sortCriteria))
    return false;
  
  // Return early if data is empty
  if (empty($data)) {
    return $data;
  }
      
  $args = array();
  $i = 0;
  foreach($sortCriteria as $sortColumn => $sortAttributes) 
  {
    $colList = array();
    foreach ($data as $key => $row)
    {
      $convertToLower = $caseInSensitive && (in_array(SORT_STRING, $sortAttributes) || in_array(SORT_REGULAR, $sortAttributes));
      $rowData = $convertToLower ? strtolower($row[$sortColumn]) : $row[$sortColumn];
      $colLists[$sortColumn][$key] = $rowData;
    }
    $args[] = &$colLists[$sortColumn];
     
    foreach($sortAttributes as $sortAttribute)
    {     
      $tmp[$i] = $sortAttribute;
      $args[] = &$tmp[$i];
      $i++;     
     }
  }
  $args[] = &$data;
  call_user_func_array('array_multisort', $args);
  return end($args);
}
public function allarrayToString($array, $prefix = '') {
    $result = [];
    $prefix = $prefix ? strtoupper($prefix) : '';
    $delimiters = [',', ';', '/', '|', '-', ':', ' x ', '*'];

    foreach ($array as $key => $value) {
        $key = strtoupper($key);

        if ($key === 'BULLETPOINT') {
            $key = 'PRODUCT FEATURE';
        }
        if ($key === 'SOLEMATERIAL') {
            $key = 'PRODUCT SOLE MATERIAL';
        }
        if ($key === 'PRODUCT DESCRIPTION') {
            $key = 'PRODUCT DESCRIPTION';
        }
        if ($key === 'PARTNUMBER') {
            $key = 'MPM AND MODEL NUMBER';
        }

        if (in_array($key, [
            'TRADEINELIGIBLE', 'SKIPOFFER', 'LISTPRICE', 'BATTERIESINCLUDED','WEBSITEDISPLAYGROUPNAME',
            'RECOMMENDEDBROWSENODES', 'SUBJECT', 'VERIFIEDSOURCE', 'IMAGES',
            'OBJECTTYPE', 'PRODUCTTYPE', 'SUPPLIERDECLAREDDGHZREGULATION',
            'BATTERIESREQUIRED', 'UNSPSCCODE', 'PARENTPRODUCTAIDS', 'RELATIONSHIPS','ITEMCLASSIFICATION','CONTAINSLIQUIDCONTENTS'
        ])) {
            continue;
        }

        if (in_array($key, [
            'ADDITIONALATTRIBUTES', 'CHANNELSPECIFICATTRIBUTES', 'DIMENSIONS',
            'IDENTIFIERS', 'VALUE', 'CONTRIBUTORS', 'COMMONATTRIBUTES', 'NAME'
        ])) {
            if (is_array($value)) {
                $result[] = $this->allarrayToString($value, $prefix);
            } else {
                $result[] = ($value !== $prefix) ? "$prefix: $value" : "$prefix:";
            }
            continue;
        }

        if (is_numeric($key)) {
            if (is_array($value)) {
                $result[] = $this->allarrayToString($value, $prefix);
            } else {
                $result[] = "$prefix: $value";
            }
            continue;
        }

        $fullKey = $prefix ? "$prefix: $key" : $key;

        if (is_array($value)) {
            if (array_keys($value) === range(0, count($value) - 1)) {
                $scalar_values = array_filter($value, 'is_scalar');
                if (count($scalar_values) === count($value)) {
                    $splitValues = [];
                    foreach ($value as $val) {
                        $splitValues = array_merge($splitValues, preg_split('/,|;|\/|\||-|:| x |\*/', $val));
                    }
                    $splitValues = array_filter(array_map('trim', $splitValues));

                    if (count($splitValues) > 1 && $key !== 'BRAND' && substr($fullKey, -1) !== 'S') {
                        $fullKey .= 'S';
                    }

                    $result[] = "$fullKey: " . implode(', ', array_unique($splitValues));
                } else {
                    $result[] = $this->allarrayToString($value, $fullKey);
                }
            } else {
                $result[] = $this->allarrayToString($value, $fullKey);
            }
        } else {
            if (strpbrk($value, implode('', $delimiters))) {
                $splitValue = array_filter(array_map('trim', preg_split('/,|;|\/|\||-|:| x |\*/', $value)));
                if (count($splitValue) > 1 && $key !== 'BRAND' && substr($fullKey, -1) !== 'S') {
                    $fullKey .= 'S';
                }
                $result[] = "$fullKey: " . implode(', ', array_unique($splitValue));
            } else {
                $result[] = "$fullKey: $value";
            }
        }
    }

    $filtered_result = [];
    foreach ($result as $line) {
        if (strpos($line, ': ') !== false) {
            [$key, $val] = explode(': ', $line, 2) + [null, null];
            if (!isset($filtered_result[$key])) {
                $filtered_result[$key] = [];
            }
            if (!in_array($val, $filtered_result[$key])) {
                $filtered_result[$key][] = $val;
            }
        } else {
            $filtered_result[$line] = null;
        }
    }

    return implode("\n", array_map(
        fn($k, $v) => $v ? "$k: " . implode(', ', array_unique($v)) : "$k:",
        array_keys($filtered_result),
        $filtered_result
    ));

}  
    public function getHighestResolutionImage($imagesArray) {

        //print("<pre>" . print_r($imagesArray, true) . "</pre>");
    if (!isset($imagesArray['imageUrl']) || !is_array($imagesArray['imageUrl'])) {
        return null; // cas où le tableau n'est pas valide
    }

    $highestRes = 0;
    $bestImageUrl = '';

    foreach ($imagesArray['imageUrl'] as $url) {
        if (preg_match('/s-l(\d+)\.jpg$/', $url, $matches)) {
            $resolution = (int)$matches[1];

            if ($resolution > $highestRes) {
                $highestRes = $resolution;
                $bestImageUrl = $url;
            }
        }
    }

    return $bestImageUrl;
}


public function debug_function_trace() {
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    echo "<pre>";
    foreach ($trace as $entry) {
        if (!isset($entry['file'])) continue;
        // Afficher uniquement les fichiers contenant "warehouse"
        if (strpos($entry['file'], 'warehouse') !== false) {
            echo "Fichier : " . $entry['file'] . " - Ligne : " . $entry['line'] . "\n";
        }
    }
    echo "</pre>";
    exit();
}

public function clearArrayValuesAndReturnPrettyJsonOLD($jsonData, $main_array='') {

    $array=json_decode($jsonData,true);
    array_walk_recursive($array, function (&$value) {
        $value = "";
    });

    $output_json[$main_array] =$array;
    return json_encode($output_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
public function clearArrayValuesAndReturnPrettyJson($jsonData, $main_array = '') {
    $array = json_decode($jsonData, true);

    $clearValues = function ($item) use (&$clearValues) {
        if (is_array($item)) {
            $result = [];
            foreach ($item as $key => $value) {
                // On garde la structure des clés intacte
                $result[$key] = $clearValues($value);
            }
            return $result;
        } else {
            return ""; // Toutes les valeurs sont effacées
        }
    };

    $cleared = $clearValues($array);

    $output_json = [$main_array => $cleared];

    return json_encode($output_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

public function cleanArrayForSql($array) {
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = $this->cleanArrayForSql($value);
        } elseif (is_string($value)) {
            // 1. Décode les \u00XX
            $decoded = json_decode('"' . $value . '"', JSON_UNESCAPED_UNICODE);

            // Si échec, on garde la valeur brute
            if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                $decoded = $value;
            }

            // 2. Décode les entités HTML
            $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // 3. NE PAS addslashes — on veut garder les ' et " tels quels
            $array[$key] = $decoded;
        }
    }

    return $array;
}




}


