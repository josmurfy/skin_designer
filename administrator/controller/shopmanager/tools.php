<?php
namespace Opencart\Admin\Controller\Shopmanager;

class Tools extends \Opencart\System\Engine\Controller {


    public function uploadImagesFiles() {
        $this->load->model('shopmanager/catalog/product');
        $this->load->model('shopmanager/tools');
    //    $this->request->post['product_id']=27300;
 //print("<pre>" . print_r( $this->request->post['sourcecode'], true) . "</pre>");
        if (isset($this->request->post['product_id']) || isset($this->request->get['product_id'])) {
          
        //    $this->request->post['sourcecode']=387804375750;
         
            $product_id = $this->request->post['product_id']??$this->request->get['product_id'];
    
            $this->load->model('tool/image');
    
            $imageUrls = array();
            $imageUrls['secondary']=[];
           
            if(isset( $this->request->post['sourcecode']) && !empty( $this->request->post['sourcecode'])){
                $this->model_shopmanager_tools->deleteProductImages($product_id, 'all');
                $imageUrlsReceived = $this->model_shopmanager_tools->extractImageUrls($this->request->post['sourcecode']);
                //print("<pre>" . print_r($imageUrlsReceived, true) . "</pre>");
                if (isset($imageUrlsReceived) && is_array($imageUrlsReceived) && count($imageUrlsReceived) > 0) {
    
                //print("<pre>" . print_r($imageUrls, true) . "</pre>");
                    $image_temp=$imageUrlsReceived[0];
         //print("<pre>" . print_r($image_temp, true) . "</pre>");
                
                      $imageUrls['primary'] = $this->model_shopmanager_tools->uploadImages($image_temp,$product_id,'pri');
                    $this->model_shopmanager_catalog_product->updateProductImage($product_id, $imageUrls['primary']);
                    $json['product_images'] = array(
                        'primary' => array(
                            'image' => $imageUrls['primary'] ?? null,
                            'thumb' => isset($imageUrls['primary']) ? $this->model_tool_image->resize($imageUrls['primary'], 100, 100) : null
                        ),
                        'secondary' => array()
                    );
                    if(count($imageUrlsReceived)>1){
                        unset($imageUrlsReceived[0]);
                        foreach($imageUrlsReceived as $key=>$image ){
                            $image=$imageUrlsReceived[$key];
                        //	$product_search['product_image_temp'][] = ;
                            $target_file = $this->db->escape($this->model_shopmanager_tools->uploadImages($image,$product_id,'sec')) ;
                            $imageUrls['secondary'][] = $target_file;
                            $this->model_shopmanager_catalog_product->insertProductImage($product_id, $target_file);
                            $json['product_images']['secondary'][] = array(
                                'image' => $target_file,
                                'thumb' => $this->model_tool_image->resize($target_file, 100, 100),
                                'sort_order' => 0
                            );
                        }
                     
                    }
                  
                //	$this->model_shopmanager_tools->clearProductIDImages($product_id);
                //	$this->model_shopmanager_tools->transferTempImages($product_id,$product_search);
                }

                // Marquer le produit pour mise à jour sur eBay (sourcecode upload)
                $this->load->model('shopmanager/marketplace');
                $this->model_shopmanager_marketplace->setToUpdate((int)$product_id);
    
            }else{
    
                // Télécharger l'image principale
                if($this->request->get['type'] == 'pri'){
                    if (!empty($this->request->files['imageprincipal']['tmp_name'])) {
                        $this->model_shopmanager_tools->deleteProductImages($product_id, 'pri');
                        $imageUrls['primary'] = $this->model_shopmanager_tools->addProductImage($product_id, $this->request->files['imageprincipal'],'pri');
                        $this->model_shopmanager_catalog_product->updateProductImage($product_id, $imageUrls['primary']);
                        
                        // S'assurer que le fichier est écrit sur le disque avant de resize
                        clearstatcache();
                        
                        // Générer les miniatures (100x100 pour thumbnail, fullsize pour preview)
                        $thumb = null;
                        $fullsize = null;
                        if (isset($imageUrls['primary']) && !empty($imageUrls['primary'])) {
                            $thumb = $this->model_tool_image->resize($imageUrls['primary'], 100, 100);
                            
                            // Si resize a échoué (retourne l'originale), créer manuellement avec Imagick
                            if (strpos($thumb, '/cache/') === false) {
                                $thumb = $this->model_shopmanager_tools->manualResize($imageUrls['primary'], 100, 100);
                            }
                            
                            // Construire l'URL complète pour fullsize
                            $fullsize = HTTP_CATALOG . 'image/' . $imageUrls['primary'];
                        }
                        
                        $json['product_images'] = array(
                            'primary' => array(
                                'image' => $imageUrls['primary'] ?? null,
                                'thumb' => $thumb,
                                'fullsize' => $fullsize
                            ),
                            'secondary' => array()
                        );

                        // Marquer le produit pour mise à jour sur eBay
                        $this->load->model('shopmanager/marketplace');
                        $this->model_shopmanager_marketplace->setToUpdate((int)$product_id);
                    } else {
                        $json['error'] = 'No file uploaded';
                    }
                }
    
                // Télécharger les images supplémentaires
                if($this->request->get['type'] == 'sec'){
                    if (!empty($this->request->files['imagesecondary']['tmp_name'][0])) {	
                        foreach ($this->request->files['imagesecondary']['tmp_name'] as $key => $tmp_name) {
                            if (!empty($tmp_name)) {
                                $file = array(
                                    'name'     => $this->request->files['imagesecondary']['name'][$key],
                                    'type'     => $this->request->files['imagesecondary']['type'][$key],
                                    'tmp_name' => $tmp_name,
                                    'error'    => $this->request->files['imagesecondary']['error'][$key],
                                    'size'     => $this->request->files['imagesecondary']['size'][$key]
                                );
    
                                $imageUrls['secondary'][] = $this->model_shopmanager_tools->addProductImage($product_id, $file,'sec');
                            }
                        }
                    }
                    foreach ($imageUrls['secondary'] as $image) {
                        $json['product_images']['secondary'][] = array(
                            'image' => $image,
                            'thumb' => $this->model_tool_image->resize($image, 100, 100),
                            'sort_order' => 0
                        );
                        $json['product_images']['primary'] = null;
                    }
                }
            }
    
           // Construire la réponse JSON
          
    
        
    
        $json['success'] = 'Les images ont été mises à jour avec succès!';
    }
    
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
    
    }

    public function uploadEbayImages() {
        $this->load->model('shopmanager/catalog/product');
        $this->load->model('shopmanager/tools');
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
        $this->load->model('tool/image');
        
        $json = array();
        
        if (isset($this->request->post['product_id'])) {
            $product_id = (int)$this->request->post['product_id'];
            
            try {
                // Get marketplace data for this product using existing function
                $marketplace_data = $this->model_shopmanager_marketplace->getMarketplace([
                    'product_id' => $product_id,
                    'marketplace_id' => 1
                ]);
                
                if (!empty($marketplace_data)) {
                    // Get first marketplace account
                    $first_account = reset($marketplace_data);
                    $marketplace_item_id = $first_account['marketplace_item_id'];
                    $marketplace_account_id = $first_account['marketplace_account_id'];
                    
                    if (!empty($marketplace_item_id)) {
                        // Get eBay item images using existing function
                        $imageUrls = $this->model_shopmanager_ebay->getImages($marketplace_item_id);
                        
                        // DEBUG: Log what we got
                        $json['debug_marketplace_item_id'] = $marketplace_item_id;
                        $json['debug_image_count'] = count($imageUrls);
                        $json['debug_images'] = $imageUrls;
                        
                        if (!empty($imageUrls)) {
                        
                        // Delete existing product images
                        $this->model_shopmanager_tools->deleteProductImages($product_id, 'all');
                        
                        $json['product_images'] = array(
                            'primary' => null,
                            'secondary' => array()
                        );
                        
                        $imported_count = 0;
                        $primary_set = false;

                        foreach ($imageUrls as $image_url) {
                            if (empty($image_url)) {
                                continue;
                            }

                            if (!$primary_set) {
                                $primary_image = $this->model_shopmanager_tools->uploadImages($image_url, $product_id, 'pri');

                                if ($primary_image) {
                                    $this->model_shopmanager_catalog_product->updateProductImage($product_id, $primary_image);
                                    $json['product_images']['primary'] = array(
                                        'image' => $primary_image,
                                        'thumb' => $this->model_tool_image->resize($primary_image, 100, 100)
                                    );
                                    $primary_set = true;
                                    $imported_count++;
                                }

                                continue;
                            }

                            $secondary_image = $this->model_shopmanager_tools->uploadImages($image_url, $product_id, 'sec');

                            if ($secondary_image) {
                                $this->model_shopmanager_catalog_product->insertProductImage($product_id, $secondary_image);

                                $json['product_images']['secondary'][] = array(
                                    'image' => $secondary_image,
                                    'thumb' => $this->model_tool_image->resize($secondary_image, 100, 100),
                                    'sort_order' => 0
                                );
                                $imported_count++;
                            }
                        }

                        if ($primary_set) {
                            $json['success'] = 'eBay images imported successfully! (' . $imported_count . ' images)';
                        } else {
                            $json['error'] = 'No eBay image could be imported as primary image';
                        }
                        } else {
                            $json['error'] = 'No images found in eBay listing';
                        }
                    } else {
                        $json['error'] = 'No eBay listing found for this product (empty marketplace_item_id)';
                    }
                } else {
                    $json['error'] = 'No eBay listing found for this product';
                }
            } catch (\Exception $e) {
                $json['error'] = 'Error importing eBay images: ' . $e->getMessage();
            }
        } else {
            $json['error'] = 'Product ID missing';
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function deleteProductImagePermanent() {
        $this->load->model('shopmanager/catalog/product');
        
        $json = array();
        
        if (isset($this->request->post['image_path']) && isset($this->request->post['product_image_id'])) {
            $image_path = $this->request->post['image_path'];
            $product_image_id = (int)$this->request->post['product_image_id'];
            
            try {
                // Delete from database
                $this->model_shopmanager_catalog_product->deleteProductImageById($product_image_id);
                
                // Delete physical file
                $full_path = DIR_IMAGE . $image_path;
                if (file_exists($full_path)) {
                    unlink($full_path);
                    
                    // Also delete .jpg version if exists
                    $jpg_path = str_replace('.webp', '.jpg', $full_path);
                    if (file_exists($jpg_path)) {
                        unlink($jpg_path);
                    }
                }
                
                $json['success'] = 'Image deleted successfully';
            } catch (\Exception $e) {
                $json['error'] = 'Error deleting image: ' . $e->getMessage();
            }
        } else {
            $json['error'] = 'Missing required parameters';
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

public function deleteProductImage() {

$this->load->model('tool/image');
$this->load->model('shopmanager/catalog/category');
$this->load->model('shopmanager/catalog/product');
$this->load->model('shopmanager/tools');
$json = array();

if (isset($this->request->post['product_id']) && isset($this->request->post['image']) && isset($this->request->post['type'])) {
    $product_id = $this->request->post['product_id'];
    $image = $this->request->post['image'];
    $type = $this->request->post['type'];


    // Appeler la méthode du modèle pour supprimer l'image
    $result = $this->model_shopmanager_tools->deleteProductImage($product_id, $image, $type);

    if ($result === true) {
        $json['success'] = 'Image supprimée avec succès.';
    } else {
        $json['error'] = 'Échec de la suppression de l\'image.';
    }
} else {
    $json['error'] = 'Données manquantes pour supprimer l\'image.';
}

$this->response->addHeader('Content-Type: application/json');
$this->response->setOutput(json_encode($json));
}







public function create_label($sku = '', $upc ='', $quantity = 1) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once(DIR_SYSTEM . 'library/phpqrcode/qrlib.php'); // Inclure la bibliothèque QR Code


    //print("<pre>" . print_r( $this->request->get, true) . "</pre>");

    $this->load->model('shopmanager/tools');
    // Récupérer les données de l'URL ou du POST
    $sku = '';
    if (!empty($this->request->get['sku']) && $this->request->get['sku'] != 'null') {
        $sku = $this->request->get['sku'];
    } elseif (!empty($this->request->get['location'])) {
        $sku = strtoupper($this->request->get['location']);
    } elseif (!empty($sku)) {
        // $sku déjà fourni dans l'appel de la fonction
        // on le garde tel quel
    } else {
        $sku = '';
    }

    if (!empty($this->request->get['upc']) || $this->request->get['upc'] != 'null') {
        $upc = $this->request->get['upc'];
    }
   
    $quantity = isset($this->request->get['quantity']) ? (int)$this->request->get['quantity'] : (int)$quantity;
    //print("<pre>" . print_r(  $this->request->get['quantity'], true) . "</pre>");
    //print("<pre>" . print_r(  $this->request->get['upc'], true) . "</pre>");
    //print("<pre>" . print_r(  $this->request->get['sku'], true) . "</pre>");
    //print("<pre>" . print_r(  $this->request->get['location'], true) . "</pre>");
    //print("<pre>" . print_r(  $sku, true) . "</pre>");

    // Définir le répertoire temporaire
    $tempDir =  'temp/';

    // Vérifier si le répertoire existe, sinon le créer
    if (!is_dir(DIR_IMAGE . $tempDir)) {
        mkdir(DIR_IMAGE . $tempDir, 0755, true);
    }

    // FPDF pour générer le PDF (3 pouces par 1 pouce, soit 76.2mm x 25.4mm)
   


        // Générer le QR Code pour l'emplacement
        $qrCodeFile =  'qrcode_' . time() . '.png';
        \QRcode::png($sku, DIR_IMAGE . $tempDir . $qrCodeFile, QR_ECLEVEL_H, 3); // Créer un fichier PNG avec le QR Code

    // Charger et rendre le template
    $name = '';
    if (!empty($this->request->get['name']) && $this->request->get['name'] != 'null') {
        $name = $this->request->get['name'];
    }

    $data['sku']        = $sku;
    $data['name']       = $name;
    $data['upc']        = $upc;
    $data['quantity']   = $quantity;
    $data['qrCodeFile'] = \HTTP_CATALOG . 'image/'.$tempDir .$qrCodeFile;

    //print("<pre>" . print_r( $data, true) . "</pre>");
    // Sortie du PDF pour affichage dans le navigateur
    //$json['url'] = 
    $this->response->setOutput($this->load->view('shopmanager/create_label', $data));
    //unlink($qrCodeFile);
    //$this->response->addHeader('Content-Type: application/json');
   // $this->response->setOutput(json_encode($json));
   }

    /**
     * Rotate an image file 90°/180°/270° clockwise on disk — generic tool.
     *
     * POST  path    Path to the image, relative to DIR_OPENCART
     *               (e.g.  "image/catalog/product/12/1234/file.jpg"
     *                or    "image_backup/data/product/12/1234/file.jpg")
     *       degrees 90 | 180 | 270  (default: 90)
     */
    public function rotateImage(): void {
        ini_set('display_errors', '0');
        $this->response->addHeader('Content-Type: application/json');
        $json = [];
        try {
            $rel_path = $this->request->post['path'] ?? '';
            $degrees  = (int)($this->request->post['degrees'] ?? 90);
            if (!in_array($degrees, [90, 180, 270])) $degrees = 90;

            // Security: no path traversal, must stay within DIR_OPENCART
            $rel_path = ltrim(str_replace('..', '', $rel_path), '/');
            if (empty($rel_path)) {
                $json['error'] = 'Missing path';
                $this->response->setOutput(json_encode($json));
                return;
            }
            $abs_path = rtrim(DIR_OPENCART, '/') . '/' . $rel_path;

            if (!file_exists($abs_path) || !is_readable($abs_path) || !is_writable($abs_path)) {
                $json['error'] = 'File not found or not writable: ' . $rel_path;
                $this->response->setOutput(json_encode($json));
                return;
            }

            $ext = strtolower(pathinfo($abs_path, PATHINFO_EXTENSION));
            set_error_handler(function() { return true; });
            $img = null;
            switch ($ext) {
                case 'jpg': case 'jpeg': $img = imagecreatefromjpeg($abs_path); break;
                case 'png':  $img = imagecreatefrompng($abs_path);  break;
                case 'webp': $img = imagecreatefromwebp($abs_path); break;
                case 'gif':  $img = imagecreatefromgif($abs_path);  break;
            }
            restore_error_handler();

            if (!$img) {
                $json['error'] = 'Cannot read image (unsupported format or corrupt)';
                $this->response->setOutput(json_encode($json));
                return;
            }

            // GD imagerotate rotates counter-clockwise; invert for clockwise
            $rotated = imagerotate($img, 360 - $degrees, 0);
            imagedestroy($img);

            if (!$rotated) {
                $json['error'] = 'Rotation failed';
                $this->response->setOutput(json_encode($json));
                return;
            }

            $saved = false;
            switch ($ext) {
                case 'jpg': case 'jpeg': $saved = imagejpeg($rotated, $abs_path, 92); break;
                case 'png':
                    imagealphablending($rotated, false);
                    imagesavealpha($rotated, true);
                    $saved = imagepng($rotated, $abs_path);
                    break;
                case 'webp': $saved = imagewebp($rotated, $abs_path, 85); break;
                case 'gif':  $saved = imagegif($rotated, $abs_path);       break;
            }
            imagedestroy($rotated);

            if (!$saved) {
                $json['error'] = 'Failed to save rotated image';
                $this->response->setOutput(json_encode($json));
                return;
            }

            $json['success'] = true;
            $json['message'] = 'Image rotated ' . $degrees . '°';

            // Delete stale thumbnail cache and regenerate a fresh one
            if (strpos($rel_path, 'image/') === 0) {
                $image_rel  = substr($rel_path, strlen('image/')); // e.g. catalog/product/.../file.webp
                $cache_base = DIR_OPENCART . 'image/cache/' . pathinfo($image_rel, PATHINFO_DIRNAME);
                $basename   = pathinfo($image_rel, PATHINFO_FILENAME);
                if (is_dir($cache_base)) {
                    foreach (glob($cache_base . '/' . $basename . '-*') as $stale) {
                        @unlink($stale);
                    }
                }
                // Flush PHP's file stat cache so resize() sees the deleted files and recreates them
                clearstatcache();
                // Regenerate thumbnail at default product dimensions and return new URL
                $this->load->model('tool/image');
                $w = (int)$this->config->get('config_image_default_width')  ?: 300;
                $h = (int)$this->config->get('config_image_default_height') ?: 300;
                $json['thumb_url'] = $this->model_tool_image->resize($image_rel, $w, $h);
            }
        } catch (\Exception $e) {
            $json['error'] = 'rotateImage: ' . $e->getMessage();
        }
        $this->response->setOutput(json_encode($json));
    }
}