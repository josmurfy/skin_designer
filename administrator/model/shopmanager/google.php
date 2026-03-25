<?php

namespace Opencart\Admin\Model\Shopmanager;

class Google extends \Opencart\System\Engine\Model {
    
    public function get($query) {
        // Configurer le client Google
        $client = new \Google\Client();
        $client->setApplicationName('PhoenixLiquidation');
        $client->setDeveloperKey('AIzaSyBZaktoartb7qp7i3o3iW5O68nV04kZ1lg');
        
        // Créer un service de recherche personnalisée
        $service = new \Google\Service\CustomSearchAPI($client);
        
        // Effectuer la requête de recherche d'images
        try {
            $response = $service->cse->listCse([
                'q' => $query,
                'cx' => '303b42f23c80a4f33',
                'searchType' => 'image',
                'num' => 10,
            ]);
            
            $allImages = [];
            
            if ($response->getItems()) {
                foreach ($response->getItems() as $item) {
                    $imageData = $item->getImage();
                    if ($imageData) {
                        $allImages[] = [
                            'displayLink' => $item->getDisplayLink(),
                            'image' => $item->getLink(),
                            'name' => $item->getTitle(),
                            'url' => $imageData->getContextLink(),
                            'height' => $imageData->getHeight(),
                            'width' => $imageData->getWidth(),
                            'mime' => $item->getMime(),
                            'is_poor' => $this->getImageResolution($imageData->getHeight(), $imageData->getWidth())
                        ];
                    }
                }
            }
            
            $filteredImages = $this->filterImagesBySite($allImages);
            return !empty($filteredImages) ? $filteredImages : null;
            
        } catch (\Exception $e) {
            return ['error' => 'Google API error: ' . $e->getMessage()];
        }
    }

    private function getImageResolution($height, $width, $minWidth = 400, $minHeight = 600) {
        return  ($width < $minWidth || $height < $minHeight);
    }

    public function sortImagesByPriority($images) {
        $priorityOrder = [
            'archambault', 'renaud_bray', 'discogs', 'sunrise_records', 'walmart_ca', 'walmart_com', 'target', 'epid_image',
            'amazon_ca', 'amazon_us', 'amazon_com', 'amazon_eg', 'sears_ca', 'indigo_ca', 'indigo_com',
            'ebay_com', 'ebayimg', 'ebay_ca', 'bigcommerce', 'chicagocostume', 'eBay'
        ];
    
        $sortedImages = [];
    
        // Ajoute d'abord les clés prioritaires
        foreach ($priorityOrder as $key) {
            if (isset($images[$key])) {
                $sortedImages[$key] = $images[$key];
                unset($images[$key]);
            }
        }
    
        // Trie les clés numériques restantes en ordre croissant
        ksort($images, SORT_NUMERIC);
    
        // Fusionne les deux tableaux
        return array_merge($sortedImages, $images);
    }

    private function filterImagesBySite($allImages) {
        // Définir les domaines des sites à rechercher
        $sites = [
            'archambault' => 'archambault.ca',
            'renaud_bray' => 'renaud-bray.com',
            'discogs' => 'discogs.com',
            'sunrise_records' => 'sunriserecords.com',
            'walmart_ca' => 'walmart.ca',
            'walmart_com' => 'walmart.com',
            'target' => 'target.com',
            'amazon_ca' => 'amazon.ca',
            'amazon_com' => 'amazon.com',
            'amazon_eg' => 'amazon.eg',
            'sears_ca' => 'sears.ca',
            'indigo_ca' => 'indigo.ca',
            'indigo_com' => 'indigo.com',
            'ebay_com' => 'ebay.com',
            'ebayimg' => 'ebayimg',
            'ebay_ca' => 'ebay.ca',
            'bigcommerce' =>'bigcommerce',
            'chicagocostume' => 'chicagocostume.com'
        ];
    
        // Filtrer les images par site
        $filteredImages = [];
        foreach ($allImages as $info) {
            foreach ($sites as $key => $site) {
                if (strpos($info['displayLink'], $site) !== false) {
                    $filteredImages[$key][] = $info;
                }
            }
        }
    
        return $filteredImages;
    }
}