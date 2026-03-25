<?php

class ControllerShopmanagerTools extends Controller {


    public function uploadImagesFiles() {
        $this->load->model('shopmanager/product');
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
                //print("<pre>".print_r ($imageUrls,true )."</pre>");
          //print("<pre>" . print_r($imageUrlsReceived, true) . "</pre>");
                if (isset($imageUrlsReceived)) {
    
                //print("<pre>" . print_r($imageUrls, true) . "</pre>");
                    $image_temp=$imageUrlsReceived[0];
         //print("<pre>" . print_r($image_temp, true) . "</pre>");
                
                      $imageUrls['primary'] = $this->model_shopmanager_tools->uploadImages($image_temp,$product_id,'pri');
                    $this->model_shopmanager_product->updateProductImage($product_id, $imageUrls['primary']);
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
                            $this->model_shopmanager_product->insertProductImage($product_id, $target_file);
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
    
            }else{
    
                // Télécharger l'image principale
                if($this->request->get['type'] == 'pri'){
                    if (!empty($this->request->files['imageprincipal']['tmp_name'])) {
                        $this->model_shopmanager_tools->deleteProductImages($product_id, 'pri');
                        $imageUrls['primary'] = $this->model_shopmanager_tools->addProductImage($product_id, $this->request->files['imageprincipal'],'pri');
                    }
                    $json['product_images'] = array(
                        'primary' => array(
                            'image' => $imageUrls['primary'] ?? null,
                            'thumb' => isset($imageUrls['primary']) ? $this->model_tool_image->resize($imageUrls['primary'], 100, 100) : null
                        ),
                        'secondary' => array()
                    );
                }
    
                // Télécharger les images supplémentaires
    
            //print("<pre>" . print_r($this->request->files['imagesecondary']['tmp_name'], true) . "</pre>");
            //	if (!empty($this->request->files['imagesecondary']['tmp_name'][0])) {
            //		$this->model_shopmanager_tools->deleteProductImages($product_id, 'sec');
            //	}
    
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
public function deleteProductImage() {

$this->load->model('tool/image');
$this->load->model('shopmanager/catalog/category');
$this->load->model('shopmanager/product');
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

public function create_labelOLDOLD($sku = '', $upc = '', $quantity = 1) {
    ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    $this->load->model('shopmanager/product');
    $this->load->model('shopmanager/tools');

    // Récupérer les données de l'URL ou du POST
    $sku = $this->request->get['sku'] ?? $sku;
    $upc = $this->request->get['upc'] ?? $upc;
    $quantity = $this->request->get['quantity'] ?? $quantity;

    // Définir le répertoire temporaire
    $tempDir = DIR_IMAGE . 'temp/';
    
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    // Générer le QR Code pour le SKU
    
    require_once(DIR_SYSTEM . 'library/phpqrcode/qrlib.php'); // Inclure la bibliothèque QR Code
    $qrCodeFile = $tempDir . 'qrcode_' . time() . '.png';
    QRcode::png($sku, $qrCodeFile, QR_ECLEVEL_H, 3); // Créer un fichier PNG avec le QR Code

    // Charger et rendre le template
    $data['sku'] = $sku;
    $data['upc'] = $upc;
    $data['quantity'] = $quantity;
    $data['qr_code'] = base64_encode(file_get_contents($qrCodeFile));


}



public function create_labelOLD($sku = '', $upc ='', $quantity = 1) {
    require_once(DIR_SYSTEM . 'library/fpdf/fpdf.php'); // Inclure FPDF
    require_once(DIR_SYSTEM . 'library/phpqrcode/qrlib.php'); // Inclure la bibliothèque QR Code


    //print("<pre>" . print_r( $this->request->get, true) . "</pre>");

    $this->load->model('shopmanager/product');
    $this->load->model('shopmanager/tools');
    // Récupérer les données de l'URL ou du POST
    $sku = $this->request->get['sku'] ?? $sku;
	$upc = $this->request->get['upc'] ?? $upc;
    $quantity = $this->request->get['quantity'] ?? $quantity;

    // Définir le répertoire temporaire
    $tempDir = DIR_IMAGE . 'temp/';

    // Vérifier si le répertoire existe, sinon le créer
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    // FPDF pour générer le PDF (3 pouces par 1 pouce, soit 76.2mm x 25.4mm)
    $pdf = new FPDF('L', 'mm', array(76.2, 25.4)); // Paysage avec 3 pouces x 1 pouce

    // Largeur et hauteur maximales de l'étiquette
    $maxLabelWidth = 76.2; // Largeur maximale de l'étiquette (en mm)
    $maxLabelHeight = 25.4; // Hauteur maximale de l'étiquette (en mm)

    // Taille et position du QR code
    $qrSize = 24; // Taille du QR code (24x24mm)
    $qrPositionX = 2; // Position X du QR code (2mm de la gauche)
    $qrPositionY = ($maxLabelHeight - $qrSize) / 2; // Centrer verticalement le QR code

    for ($i = 1; $i <= $quantity; $i++) {
        // Générer le QR Code pour l'emplacement
        $qrCodeFile = $tempDir . 'qrcode_' . time() . '.png';
        QRcode::png($sku, $qrCodeFile, QR_ECLEVEL_H, 3); // Créer un fichier PNG avec le QR Code

        $pdf->SetAutoPageBreak(false); // Désactiver le saut de page automatique
        $pdf->AddPage(); // Ajouter une nouvelle page

        // Ajouter le QR Code à gauche
        $pdf->Image($qrCodeFile, $qrPositionX, $qrPositionY, $qrSize, $qrSize); // Positionner le QR code

        // Calculer la largeur disponible pour le texte (espace total - QR code - espace entre QR et texte)
        $textAvailableWidth = $maxLabelWidth - ($qrSize + $qrPositionX + 4); // 4 mm d'espace entre le QR code et le texte

        // Taille de police initiale (en points)
        $fontSize = 64; // Taille initiale de la police

        // Définir la police initiale
        $pdf->SetFont('Arial', 'B', $fontSize);

        // Calculer la largeur de la chaîne avec la taille de police actuelle
        $textWidth = $pdf->GetStringWidth($sku);

        // Ajuster la taille de la police jusqu'à ce que le texte tienne dans la largeur maximale
        while ($textWidth > $textAvailableWidth && $fontSize > 10) {
            $fontSize -= 1; // Réduire la taille de la police
            $pdf->SetFont('Arial', 'B', $fontSize); // Définir la nouvelle taille de police
            $textWidth = $pdf->GetStringWidth($sku); // Recalculer la largeur du texte
        }

        // Calculer la position du texte pour être centré horizontalement par rapport au QR code
      //  $textPositionX = $qrPositionX + $qrSize + ($textAvailableWidth - $textWidth) / 2;
		// Utiliser `FontSizePt` pour obtenir la taille de la police
		$fontSizeInPoints = $fontSize; // Car FPDF utilise la taille définie
		$textHeight = $fontSizeInPoints / 2.83; // Convertir la taille en mm (1pt = 1/72 inches, 1 inch = 25.4mm => 1pt = 25.4/72 = 0.3527 mm)

		// Calculer la position verticale pour centrer le texte sur l'étiquette par rapport au QR code
	//	$textPositionY = ($maxLabelHeight - $textHeight) / 2;
	$textPositionX = 24; // Positionner le texte à droite du QR code
	$textPositionY =(!empty($upc) && $upc != 'null')?10.5:13.5; // Centrer le texte verticalement

	// Afficher le texte principal
	$pdf->SetXY($textPositionX, $textPositionY);
		// Définir la position du texte
		//$pdf->SetXY($textPositionX, $textPositionY);
//		$pdf->SetXY(24, 13.5);
		// Afficher le texte sur le PDF avec la taille de police ajustée
		$pdf->Cell(0, 0, $sku, 0, 0, 'L'); // Afficher le texte aligné à gauche, mais centré par rapport au QR code
	
			// Afficher le texte UPC en dessous si défini
		  // Afficher le texte UPC en dessous si défini
		  if (!empty($upc) && $upc != 'null') {
            // Définir une taille de police plus petite pour l'UPC
            $upcFontSize = 12; // Taille de police pour l'UPC
            $pdf->SetFont('Arial', '', $upcFontSize);

            // Calculer la position de l'UPC sous le texte principal
            $upcPositionY = $textPositionY + $textPositionY; // Ajouter un petit décalage pour l'UPC

            // Afficher l'UPC sous le texte principal
            $pdf->SetXY($textPositionX, $upcPositionY);
            $pdf->Cell(0, 0, "UPC: " . $upc, 0, 0, 'L');
        }

		// Supprimer le fichier QR code temporaire
		unlink($qrCodeFile);
    }

    // Sortie du PDF pour affichage dans le navigateur
    $pdf->Output('text_label.pdf', 'I'); // 'I' pour afficher directement dans le navigateur
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
        QRcode::png($sku, DIR_IMAGE . $tempDir . $qrCodeFile, QR_ECLEVEL_H, 3); // Créer un fichier PNG avec le QR Code

    // Charger et rendre le template

    $data['sku'] = $sku;
    $data['upc'] = $upc;
    $data['quantity'] = $quantity;
    $data['qrCodeFile'] = HTTPS_CATALOG . 'image/'.$tempDir .$qrCodeFile;//base64_encode(file_get_contents($qrCodeFile));

    //print("<pre>" . print_r( $data, true) . "</pre>");
    // Sortie du PDF pour affichage dans le navigateur
    //$json['url'] = 
    $this->response->setOutput($this->load->view('shopmanager/create_label', $data));
    //unlink($qrCodeFile);
    //$this->response->addHeader('Content-Type: application/json');
   // $this->response->setOutput(json_encode($json));
   }
}