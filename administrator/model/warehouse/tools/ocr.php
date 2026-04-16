<?php
// Original: shopmanager/ocr.php
namespace Opencart\Admin\Model\Shopmanager;

use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\ImageSource;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;



class Ocr extends \Opencart\System\Engine\Model {

    private function createClient(): ImageAnnotatorClient {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/ocr.json');

        if (!file_exists(getenv('GOOGLE_APPLICATION_CREDENTIALS'))) {
            throw new \Exception('Google Vision credentials file is missing: ' . getenv('GOOGLE_APPLICATION_CREDENTIALS'));
        }

        return new ImageAnnotatorClient();
    }

    public function recognizeText($imagePath) {

        $imageAnnotator = $this->createClient();

        try {
            // Charger l'image
            $imageData = file_get_contents($imagePath);

            $image = new Image();
            $image->setContent($imageData);

            $feature = new Feature();
            $feature->setType(Type::TEXT_DETECTION);

            $request = new AnnotateImageRequest();
            $request->setImage($image);
            $request->setFeatures([$feature]);

            $batchRequest = new BatchAnnotateImagesRequest();
            $batchRequest->setRequests([$request]);

            $batchResponse = $imageAnnotator->batchAnnotateImages($batchRequest);
            $responses = $batchResponse->getResponses();

            if (count($responses) > 0) {
                $textAnnotations = $responses[0]->getTextAnnotations();
                if (!empty($textAnnotations) && count($textAnnotations) > 0) {
                    return $textAnnotations[0]->getDescription();
                }
            }

            return null;
        } catch (\Exception $e) {
            // Gérer les erreurs
            error_log('Google OCR Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getBestSimilarImage($imageUrl) {
    
        $imageAnnotator = $this->createClient();
    
        try {
            // Construire la requête pour Web Detection
            $imageSource = new ImageSource();
            $imageSource->setImageUri($imageUrl);

            $image = new Image();
            $image->setSource($imageSource);
    
            $feature = new Feature();
            $feature->setType(Type::WEB_DETECTION);

            $request = new AnnotateImageRequest();
            $request->setImage($image);
            $request->setFeatures([$feature]);

            $batchRequest = new BatchAnnotateImagesRequest();
            $batchRequest->setRequests([$request]);

            $batchResponse = $imageAnnotator->batchAnnotateImages($batchRequest);
            $responses = $batchResponse->getResponses();

            $imageData = [];

            $webDetection = null;
            if (count($responses) > 0) {
                $webDetection = $responses[0]->getWebDetection();
            }
    
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
    
        } catch (\Exception $e) {
            error_log('Google Vision getBestSimilarImage Error: ' . $e->getMessage());
            return [];
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