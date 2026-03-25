<?php
namespace Opencart\Admin\Model\Shopmanager;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature;



class Ocr extends \Opencart\System\Engine\Model {

    public function recognizeText($imagePath) {

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/ocr.json');
        // Initialiser le client Cloud Vision
        if (!getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
            throw new Exception('GOOGLE_APPLICATION_CREDENTIALS is not set.');
        }

        // Debug: Check if the credentials file exists
        if (!file_exists(getenv('GOOGLE_APPLICATION_CREDENTIALS'))) {
            throw new Exception('Credentials file does not exist: ' . getenv('GOOGLE_APPLICATION_CREDENTIALS'));
        }
        $imageAnnotator = new ImageAnnotatorClient([
            'credentials' => null, // Use ADC
        ]);
        

        try {
            // Charger l'image
            $imageData = file_get_contents($imagePath);

            // Envoyer la requête d'analyse de texte
            $response = $imageAnnotator->textDetection($imageData);
            $textAnnotations = $response->getTextAnnotations();

            // Extraire le texte détecté
            if (!empty($textAnnotations)) {
                $recognizedText = $textAnnotations[0]->getDescription();
            } else {
                $recognizedText = null;
            }

            // Fermer le client
            $imageAnnotator->close();

            return $recognizedText;
        } catch (Exception $e) {
            // Gérer les erreurs
            error_log('Google OCR Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getBestSimilarImage($imageUrl) {
        // Définir les identifiants Google Cloud
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/ocr.json');
    
        if (!file_exists(getenv('GOOGLE_APPLICATION_CREDENTIALS'))) {
            throw new Exception('Google Vision credentials file is missing.');
        }
    
        // Initialiser le client Google Vision
        $imageAnnotator = new ImageAnnotatorClient();
    
        try {
            // Construire la requête pour Web Detection
            $image = new Image();
            $image->setSource((new ImageSource())->setImageUri($imageUrl));
    
            $feature = new Feature();
            $feature->setType(Type::WEB_DETECTION);
    
            $response = $imageAnnotator->annotateImage($image, [$feature]);
            $webDetection = $response->getWebDetection();
    
            $imageData = [];
    
            if (!$webDetection || count($webDetection->getFullMatchingImages()) === 0) {
                $headers = @get_headers($imageUrl, 1);
                if ($headers && strpos($headers[0], '200') !== false) {
                    $size = @getimagesize($imageUrl);
                    if ($size && isset($size[0], $size[1])) {
                        $width = $size[0];
                        $height = $size[1];
                        $resolution = $width * $height;
    
                        $imageData[] = [
                            'url' => $imageUrl,
                            'score' => 100,
                            'resolution' => $resolution,
                            'width' => $width,
                            'height' => $height
                        ];
                    } else {
                        // Format non reconnu
                        return $imageData;
                    }
                } else {
                    // Image inaccessible (401, 403, 404, etc.)
                    return $imageData;
                }
            }
    
            // Images similaires trouvées
            foreach ($webDetection->getFullMatchingImages() as $similarImage) {
                $url = $similarImage->getUrl();
                $score = $similarImage->getScore() ?? 0;
    
                if ($this->isImageUrlAccessible($url)) {

                    $size = $this->getRemoteImageSize($url);
                    if ($size && isset($size[0], $size[1])) {
                        $width = $size[0];
                        $height = $size[1];
                        $resolution = $width * $height;
    
                        $imageData[] = [
                            'url' => $url,
                            'score' => $score,
                            'resolution' => $resolution,
                            'width' => $width,
                            'height' => $height
                        ];
                    } else {
                        // Format non reconnu
                        continue;
                    }
                } else {
                    // Image inaccessible (401, 403, 404, etc.)
                    continue;
                }
            }
    
            // Trier par score, puis résolution
            usort($imageData, function ($a, $b) {
                if ($b['score'] == $a['score']) {
                    return $b['resolution'] - $a['resolution'];
                }
                return $b['score'] - $a['score'];
            });
    
            return $imageData;
    
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
            return [];
        } finally {
            $imageAnnotator->close();
        }
    }
    
    private function isImageUrlAccessible($url) {
        $headers = @get_headers($url, 1);
    
        if (is_array($headers) && isset($headers[0])) {
            return preg_match('/^HTTP\/.*\s(200|301|302)/', $headers[0]);
        }
    
        return false;
    }
    
    private function getRemoteImageSize($url) {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0\r\n"
            ]
        ]);
    
        $imageData = @file_get_contents($url, false, $context);
        if ($imageData === false) {
            return false;
        }
    
        return @getimagesizefromstring($imageData);
    }
    



}