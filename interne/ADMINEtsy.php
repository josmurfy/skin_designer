<?
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
/*add_listing($db,"active");
add_listing($db,"inactive");
add_listing($db,"sold_out");
add_listing($db,"draft");
add_listing($db,"expired");*/
//check_sku_vide($db);
check_quantity($db);
//check_sku_vide($db);
//check_similar($db);
//find_similar($db);
check_quantity_outofstock($db, "oui");
//updateEtsyListing($db, "1587671691", $product_info);
//getEtsyProduct($db, 1566213326);
mysqli_close($db); 


function add_listing($db,$state="active"){
    $limit = 50;
    $offset = 0;

    $result = getEtsyProducts($db, $limit, $offset ,$state  );
    $products =$result['results'];
    // Afficher les produits
    add_etsy_listing($db,$products);
    echo "<br>NB {$state}:". $result['count'];
    if ($result['count'] > 50) {
        $count = $result['count'];

        // Définir la limite et l'offset initiaux
        $limit = 50;
        $offset = 50;

        // Utiliser une boucle while pour traiter tous les enregistrements en lots de 25
        while ($offset <= $count) {
            $result = getEtsyProducts($db, $limit, $offset);
            $products = $result['results'];

            // Afficher les produits
            add_etsy_listing($db, $products);

            // Mettre à jour l'offset pour le prochain lot
            $offset += $limit;
        }
    }
}
function find_similar($db){
   

    $sql = 'SELECT *
    FROM `oc_etsy_products_list` EPL 
    WHERE similar_check <100 group by EPL.product_id order by similar_check ';
            /* 	LEFT JOIN `oc_product_to_category` PC ON (P.product_id=PC.product_id)
    LEFT JOIN `oc_category_description` CD ON (CD.category_id=PC.category_id AND CD.ebayyes=1 AND CD.language_id=1)
*/
    $req = mysqli_query($db,$sql);
    
    if (mysqli_query($db, $sql)) {
        echo "Enregistrement ajouté avec succès<br>";
        echo $sql;
    } else {
        echo "<br>Erreur lors de l'ajout de l'enregistrement: " . mysqli_error($db);
    }
    $result= array();
    while($data = mysqli_fetch_assoc($req))
    {

            $sql2 = 'SELECT * 
            FROM `oc_product_description` PD  
            WHERE name="'.$data['name'].'"';
                    /* 	LEFT JOIN `oc_product_to_category` PC ON (P.product_id=PC.product_id)
            LEFT JOIN `oc_category_description` CD ON (CD.category_id=PC.category_id AND CD.ebayyes=1 AND CD.language_id=1)
        */
            $req2 = mysqli_query($db,$sql2);
            
            if (mysqli_query($db, $sql2)) {
                echo "Enregistrement ajouté avec succès<br>";
                echo $sql2;
            } else {
                echo "<br>Erreur lors de l'ajout de l'enregistrement: " . mysqli_error($db);
            }
            $data2 = mysqli_fetch_assoc($req2);
            echo "<br>****************************************************************************************************************************<br>";

            //print("<pre>".print_r ($data,true )."</pre>");
            echo "<br>******************************************<br>";

            //print("<pre>".print_r ($data2,true )."</pre>");
            echo "<br>****************************************************************************************************************************<br>";

     }
   
    
  //print("<pre>".print_r ($result,true )."</pre>");
}
function check_similar($db){
   

    $sql = 'SELECT EPL.id_etsy_products_list,
    PD.product_id AS product_id, PD.name AS name_en, EPL.name AS name_etsy, EPL.similar_check
    FROM `oc_etsy_products_list` EPL 
    LEFT JOIN `oc_product_description` PD ON (EPL.product_id=PD.product_id) 
    WHERE PD.language_id=1 AND similar_check =0 group by EPL.product_id ';
            /* 	LEFT JOIN `oc_product_to_category` PC ON (P.product_id=PC.product_id)
    LEFT JOIN `oc_category_description` CD ON (CD.category_id=PC.category_id AND CD.ebayyes=1 AND CD.language_id=1)
*/
    $req = mysqli_query($db,$sql);
    
    if (mysqli_query($db, $sql)) {
        echo "Enregistrement ajouté avec succès<br>";
        echo $sql;
    } else {
        echo "<br>Erreur lors de l'ajout de l'enregistrement: " . mysqli_error($db);
    }
    $result= array();
    while($data = mysqli_fetch_assoc($req))
    {
        $similar_check= calculerSimilarite($data['name_en'], $data['name_etsy']);
      
      $sql2="UPDATE `oc_etsy_products_list` SET `similar_check` = '{$similar_check}' WHERE `oc_etsy_products_list`.`id_etsy_products_list` = {$data['id_etsy_products_list']}";
      if (mysqli_query($db, $sql2)) {
        echo "Enregistrement ajouté avec succès<br>";
        echo $sql2;
        } else {
            $error= mysqli_error($db);
            echo "<br>Erreur lors de l'ajout de l'enregistrement: " .$error;
            $sql3="UPDATE `oc_etsy_products_list` SET listing_error='{$error}' WHERE `oc_etsy_products_list`.`id_etsy_products_list` = {$data['id_etsy_products_list']}";
            mysqli_query($db, $sql3);
        }
    }
  //print("<pre>".print_r ($result,true )."</pre>");
}
function check_sku_vide($db){
    $sql = 'SELECT *
    FROM `oc_etsy_products_list` EPL  
    WHERE EPL.name LIKE "%&#39;%"';
    if (mysqli_query($db, $sql)) {
        echo "Enregistrement ajouté avec succès<br>";
        echo $sql;
    } else {
        echo "<br>Erreur lors de l'ajout de l'enregistrement: " . mysqli_error($db);
    }
    $req = mysqli_query($db,$sql);
    while($data = mysqli_fetch_assoc($req))
    {
        $data['name'] = str_replace('&#39;', "\'", $data['name']);
        $sql2="UPDATE `oc_etsy_products_list` SET `name` = '{$data['name']}' WHERE `oc_etsy_products_list`.`id_etsy_products_list` = {$data['id_etsy_products_list']}";
        if (mysqli_query($db, $sql2)) {
          echo "Enregistrement ajouté avec succès<br>";
          echo $sql2;
          } else {
              $error= mysqli_error($db);
              echo "<br>Erreur lors de l'ajout de l'enregistrement: " .$error;
           //   $sql3="UPDATE `oc_etsy_products_list` SET listing_error='{$error}' WHERE `oc_etsy_products_list`.`id_etsy_products_list` = {$data['id_etsy_products_list']}";
          //    mysqli_query($db, $sql3);
          }
    }

    $sql = 'SELECT EPL.id_etsy_products_list, EPL.listing_id AS listing_id, 
    PD.product_id AS product_id, PD.name AS name_en,EPL.name name_etsy
    FROM `oc_etsy_products_list` EPL
    LEFT JOIN  `oc_product_description` PD ON (EPL.name=PD.name) 
    WHERE PD.language_id=1 AND EPL.listing_error LIKE "Pas de SKU"  group by  EPL.listing_id';
            /* 	LEFT JOIN `oc_product_to_category` PC ON (P.product_id=PC.product_id)
    LEFT JOIN `oc_category_description` CD ON (CD.category_id=PC.category_id AND CD.ebayyes=1 AND CD.language_id=1)
*/
    $req = mysqli_query($db,$sql);
    
    if (mysqli_query($db, $sql)) {
        echo "Enregistrement ajouté avec succès<br>";
        echo $sql;
    } else {
        echo "<br>Erreur lors de l'ajout de l'enregistrement: " . mysqli_error($db);
    }

    while($data = mysqli_fetch_assoc($req))
    {
      $sql2="UPDATE `oc_etsy_products_list` SET `product_id` = '{$data['product_id']}', `update_flag` = 1 ,listing_error='' WHERE `oc_etsy_products_list`.`id_etsy_products_list` = {$data['id_etsy_products_list']}";
      if (mysqli_query($db, $sql2)) {
        echo "Enregistrement ajouté avec succès<br>";
        echo $sql2;
        } else {
            $error= mysqli_error($db);
            echo "<br>Erreur lors de l'ajout de l'enregistrement: " .$error;
            $sql3="UPDATE `oc_etsy_products_list` SET listing_error='{$error}' WHERE `oc_etsy_products_list`.`id_etsy_products_list` = {$data['id_etsy_products_list']}";
            mysqli_query($db, $sql3);
        }
    }
  //print("<pre>".print_r ($result,true )."</pre>");
}


function check_quantity($db, $zero = ""){
  
    $sql = 'SELECT EPL.id_etsy_products_list, EPL.listing_id AS listing_id, P.quantity_actuel, EPL.quantity AS quantity_etsy,
    P.product_id AS product_id, P.price_with_shipping AS priceretail, EPL.listing_image_id as url,
    P.image AS image_product
    FROM `oc_etsy_products_list`  EPL
    LEFT JOIN `oc_product` P  ON (EPL.product_id=P.product_id) 
    WHERE  EPL.quantity <> P.quantity group by EPL.listing_id '; //AND EPL.quantity <> P.quantity 
            /* 	LEFT JOIN `oc_product_to_category` PC ON (P.product_id=PC.product_id)
    LEFT JOIN `oc_category_description` CD ON (CD.category_id=PC.category_id AND CD.ebayyes=1 AND CD.language_id=1)
*/
    $req = mysqli_query($db,$sql);
    
    if (mysqli_query($db, $sql)) {
        echo "<br>Recherche avec succès<br>";
      //  echo $sql;
    } else {
        echo "<br>Erreur lors de Recherche " . mysqli_error($db);
     //   echo $sql;
    }
  
    while($data = mysqli_fetch_assoc($req))
    {
      //print("<pre>".print_r ($data,true )."</pre>");
      if($data['quantity_actuel']<1){
        echo "<br>OUT of Stock";
        //print("<pre>".print_r ($data,true )."</pre>");
        updateEtsyProductsStatus($db,$data);
      }else{
            unset($product_info);
            $product_info=getListingInventory($db,$data['listing_id']);
            unset($product_info['products'][0]['product_id']);
            unset($product_info['products'][0]['is_deleted']);
            unset($product_info['products'][0]['offerings'][0]['is_deleted']);
            unset($product_info['products'][0]['offerings'][0]['offering_id']);
            $product_info['products'][0]['sku']=$data['product_id'];
            $product_info['products'][0]['offerings'][0]['quantity']=$data['quantity_actuel'];
            $product_info['products'][0]['offerings'][0]['is_enabled']=true;
            unset($product_info['products'][0]['offerings'][0]['price']);
            $product_info['products'][0]['offerings'][0]['price']=$data['priceretail'];
            $product_info['listing']= array(
                "state"=> "active"
            );
            updateEtsyListing($db, $data['listing_id'], $product_info);

      }
     //   updateEtsyProductsStatus($db,$data);
       $sql2="UPDATE `oc_etsy_products_list` SET `quantity` = {$data['quantity_actuel']} ,listing_error='Correction QTY: {$data['quantity_etsy']} pour {$data['quantity_actuel']}' WHERE `oc_etsy_products_list`.`id_etsy_products_list` = {$data['id_etsy_products_list']}";
        if (mysqli_query($db, $sql2)) {
            echo "<br>Enregistrement ajouté avec succès<br>";
            echo $sql2;
            } else {
                $error= mysqli_error($db);
                echo "<br>Erreur lors de l'ajout de l'enregistrement: " .$error;
            //  $sql3="UPDATE `oc_etsy_products_list` SET listing_error='{$error}' WHERE `oc_etsy_products_list`.`id_etsy_products_list` = {$data['id_etsy_products_list']}";
            //  mysqli_query($db, $sql3);
            }
        }
  //print("<pre>".print_r ($result,true )."</pre>");
}
function check_quantity_outofstock($db, $zero = ""){
   
     $sql = 'SELECT EPL.id_etsy_products_list, EPL.listing_id AS listing_id, P.quantity_actuel, EPL.quantity AS quantity_etsy,
     P.product_id AS product_id,  P.price_with_shipping AS priceretail, EPL.listing_image_id as url,
     P.image AS image_product
     
     FROM `oc_etsy_products_list` EPL
    
     LEFT JOIN `oc_product` P  ON (EPL.product_id=P.product_id) 
   
     WHERE P.quantity<1 group by P.product_id '; //AND EPL.quantity <> P.quantity 
             /* 	LEFT JOIN `oc_product_to_category` PC ON (P.product_id=PC.product_id)
     LEFT JOIN `oc_category_description` CD ON (CD.category_id=PC.category_id AND CD.ebayyes=1 AND CD.language_id=1)
 */
     $req = mysqli_query($db,$sql);
     
     if (mysqli_query($db, $sql)) {
         echo "<br>Recherche avec succès<br>";
         echo $sql;
     } else {
         echo "<br>Erreur lors de Recherche " . mysqli_error($db);
        echo $sql;
     }
   
     while($data = mysqli_fetch_assoc($req))
     {
       //print("<pre>".print_r ($data,true )."</pre>");
         updateEtsyProductsStatus($db,$data);
      
      //   updateEtsyProductsStatus($db,$data);
       $sql2="UPDATE `oc_etsy_products_list` SET `quantity` = {$data['quantity_actuel']} ,listing_error='Correction QTY: {$data['quantity_etsy']} pour {$data['quantity_actuel']}' WHERE `oc_etsy_products_list`.`id_etsy_products_list` = {$data['id_etsy_products_list']}";
       if (mysqli_query($db, $sql2)) {
         echo "<br>Enregistrement ajouté avec succès<br>";
         echo $sql2;
         } else {
             $error= mysqli_error($db);
             echo "<br>Erreur lors de l'ajout de l'enregistrement: " .$error;
           //  $sql3="UPDATE `oc_etsy_products_list` SET listing_error='{$error}' WHERE `oc_etsy_products_list`.`id_etsy_products_list` = {$data['id_etsy_products_list']}";
           //  mysqli_query($db, $sql3);
         }
     }
   //print("<pre>".print_r ($result,true )."</pre>");
 }

 function calculerSimilarite($chaine1, $chaine2) {
    // Convertir les chaînes en minuscules
    $chaine1 = strtolower($chaine1);
    $chaine2 = strtolower($chaine2);

    // Calculer la similarité
    similar_text($chaine1, $chaine2, $pourcentageSimilarite);
    
    return $pourcentageSimilarite;
}



function add_etsy_listing($db,$products){
  //print("<pre>".print_r ($products,true )."</pre>");
    foreach ($products as $product) {
        $listing_error="";

    if ($product['state']=='active'){
        $status='Listed';
    }elseif ($product['state']=='inactive'){
        $status='Inactive';
    }elseif ($product['state']=='sold_out'){
        $status='Sold Out';
    }elseif ($product['state']=='draft'){
        $status='Draft';
    }elseif ($product['state']=='expired'){
    $status='Expired';
    }elseif ($product['state']=='edit'){
        $status='Inactive';
    }   
    if($product['skus'][0]==""){

        $product['skus'][0]=$product['listing_id'];
        $listing_error="Pas de SKU";
    }
    // Construire la requête SQL
    $product_id_tmp=explode("_",$product['skus'][0]);
    $product_id=$product_id_tmp[0];
    $sql = "INSERT INTO `oc_etsy_products_list` 
            ( `id_etsy_profiles`, `product_id`, sku, `reference`, `id_product_attribute`, `listing_status`, `listing_id`,
             `listing_image_id`, `update_flag`, `renew_flag`, `delete_flag`, `date_added`, `date_listed`, `date_last_renewed`,
             `listing_error`, `error_flag`, `is_disabled`, `sold_flag`, `expiry_date`, `updatedby`, `quantity`, `price`, `name`,similar_check) 
            VALUES (
                '1', 
                '{$product_id}', 
                '{$product['skus'][0]}',
                '{$product['user_id']}', 
                '1', 
                '{$status}', 
                '{$product['listing_id']}', 
                '{$product['url']}', 
                '0', 
                '0', 
                '0', 
                '".date('Y-m-d H:i:s', $product['original_creation_timestamp'])."', 
                '".date('Y-m-d H:i:s', $product['creation_timestamp'])."', 
                '".date('Y-m-d H:i:s', $product['last_modified_timestamp'])."', 
                '{$listing_error}', 
                '0', 
                '0', 
                '0', 
                NULL, 
                '1',
                '{$product['quantity']}',
                ".$product['price']['amount']/$product['price']['divisor'].",
                '{$product['title']}',
                0
            )";
            $listing_error="";
  // echo $sql.'<br>';
    // Exécuter la requête
    if (mysqli_query($db, $sql)) {
        echo "Enregistrement ajouté avec succès<br>";
    } else {
        echo "<br>Erreur lors de l'ajout de l'enregistrement: " . mysqli_error($db);
    }
    }
}

?>