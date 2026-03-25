<?
require_once '/home/n7f9655/public_html/phoenixliquidation/vendor/autoload.php';
use DTS\eBaySDK\Sdk;
use DTS\eBaySDK\Trading\Services\TradingService;
use DTS\eBaySDK\Constants\SiteIds;

function get_products(string $upc){
	include 'connection.php';
	$sql = 'SELECT *,(P.quantity+P.unallocated_quantity) AS quantity_total,
			P.product_id AS product_id,
			PD.name AS name_en, PD.color AS color_en, PD.description_supp AS description_supp_en,PD.condition_supp AS condition_supplementaire,P.price AS priceretail,P.price AS price_magasin,
			PDF.name AS name_fr, PDF.color AS color_fr, PDF.description_supp AS description_supp_fr,P.image AS image_product,
			P.unallocated_quantity,P.quantity, C.name AS condition_name,M.name AS brand,P.location AS location
			FROM `oc_product` P 
			LEFT JOIN `oc_product_description` PD ON (P.product_id=PD.product_id) 
			LEFT JOIN `oc_product_description` PDF ON (P.product_id=PDF.product_id AND PDF.language_id=2)
			LEFT JOIN `oc_manufacturer` M ON (M.manufacturer_id=P.manufacturer_id)
			LEFT JOIN `oc_condition` C ON (P.condition_id=C.condition_id AND C.language_id=2)
			where PD.language_id=1 and P.upc like "'.$upc .'%" order by C.sort_order';
					/* 	LEFT JOIN `oc_product_to_category` PC ON (P.product_id=PC.product_id)
			LEFT JOIN `oc_category_description` CD ON (CD.category_id=PC.category_id AND C.leaf=1 AND CD.language_id=1)
 */
			$req = mysqli_query($db,$sql);
		//	echo $sql;
			$result= array();
			while($data = mysqli_fetch_assoc($req))
			{
				$sql2='SELECT CD.category_id AS category_id,CD.name AS category_name,C.specifics
						FROM `oc_product_to_category` PC 
						LEFT JOIN `oc_category` C ON (PC.category_id=C.category_id)
						LEFT JOIN `oc_category_description` CD ON (C.category_id=CD.category_id AND CD.language_id=1 AND C.leaf=1)
						where PC.product_id="'.$data['product_id'].'" order by CD.category_id DESC limit 1';
						//echo $sql2;
						$req2 = mysqli_query($db,$sql2);
						$data2 = mysqli_fetch_assoc($req2);
						if($data2['category_id']!="" && isset($data2['category_id'])){
							$result[]=array_merge($data,$data2);
						}else{
							$result[]=$data;
						}
			}
			//print("<pre>".print_r ($result,true )."</pre>");
			mysqli_close($db);  
			return $result;
}
function edit_product_detail($connectionapi,$post,$db){
	echo '<br>move_photo:';
			
//	//print("<pre>".print_r ($post,true )."</pre>");
	if($post['product_id'] !=""){
		$data=get_products_by_id($post['product_id']);	
	}elseif($post['sku']!=""){
		$data=get_product($post['sku']);	
	}
//echo $sql.'<br><br>';
		// on envoie la requ�te
		//$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
		//$data = mysqli_fetch_assoc($req);
	//	echo "<br>post46";
//	//print("<pre>".print_r ($data,true )."</pre>");
		if(isset($data)){
			//if($post['name']==""){
					if (isset($post['openpageprix']) && $post['openpageprix']!=1){
						$info_shipping=get_shipping ($connectionapi,$data['weight'],$data['length'],$data['width'],$data['height'],$db,(string)$data['upc'],12919);
						$post['shipping']=$info_shipping['shipping'];
						$post['carrier']=$info_shipping['carrier'];
						$post['other']=$info_shipping['other'];
					}
/* 					} */
					//$post['price']=$data2['price'];
					$post['price_with_shipping']=$data['price_with_shipping'];
					(string)$post['upc']=(string)$data['upc'];
					//get_from_upctemp($data['upc']);
					//echo file_get_contents("https://www.upcitemdb.com/norob/alink/?id=v2u2z2v2v253b464s2&tid=1&seq=1617680392&plt=c35880419e05f3f2fa8fc94c333c297b");
					$post['upcorigine']=(string)$data['upc'];
					$post['price']=$data['price'];
					$post['quantitymagasin_ajouter']=0;
					$post['location_magasin']=capitalizeWords($data['location_magasin']);
					$post['quantityentrepot_ajouter']=0;
					$post['location']=capitalizeWords($data['location']);
					$post['condition_id']=$data['condition_id'];
					$post['product_id']=$data['product_id'];
					$post['ebay_id_old']=$data['ebay_id_old'];
					$post['sku']=$data['sku'];
					$post['upctemp']="";//get_from_upctemp($data['upc']);
					$sqlbrand = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$data['manufacturer_id'];
					// on envoie la requ�te
					$reqbrand = mysqli_query($db,$sqlbrand);
					$databrand = mysqli_fetch_assoc($reqbrand);
					$post['brand']=$databrand['name'];
					$post['model']=$data['model'];
					if($data['color_en']!=""){
						$post['color']=$data['color_en'];
					}
					$post['weight']=$data['weight'];
					$post['length']=$data['length'];
					$post['width']=$data['width'];
					$post['height']=$data['height'];
					$post['skuold']=$data['sku']; 
					$post['remarque_interne']=$data['remarque_interne'];
					$datapricesuggest = new DateTime($data['date_price_upd']);
					$datapricesuggest = $datapricesuggest->format('Y-m-d');
					$datapricesuggest=date_parse ($datapricesuggest);	
					//verification prix neuf
		//	}
					$post['categoryname']=$data['category_name'];
					$post['category_id']=$data['category_id'];
					$sql3 = 'SELECT *,P.price AS price, P.product_id,P.ebay_id,P.sku,name,P.unallocated_quantity,P.image,P.upc,P.price_with_shipping,P.weight,P.length,P.width,P.height,P.date_price_upd,P.condition_id FROM `oc_product` AS P,`oc_product_description` where P.product_id=oc_product_description.product_id  and P.sku = "'.substr ((string)$post['upc'],0,12).'"';
//echo $sql.'<br><br>';
		// on envoie la requ�te
		$req3 = mysqli_query($db,$sql3);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
		$data3 = mysqli_fetch_assoc($req3);					
					$post['pricesuggest']=$data3['price_with_shipping'];
					$post['date_price_upd_ps']=$data3['date_price_upd'];
					$post['ebay_id_refer']=$data3['marketplace_item_id'];
			$dateverification = new DateTime('now');
			$dateverification->modify('-3 month'); // or you can use '-90 day' for deduct
			$dateverification = $dateverification->format('Y-m-d');
			$dateverification=date_parse ($dateverification);
			$datapricesuggest = new DateTime($post['date_price_upd_ps']);
			$datapricesuggest = $datapricesuggest->format('Y-m-d');
			$datapricesuggest=date_parse ($datapricesuggest);	
				if ($post['condition_id']==9){
					$prixconvert=1; 
					$endprix=.95;
				}elseif($post['condition_id']==99 || $post['condition_id']==8){
					$prixconvert=.90;
					$endprix=.95;	
				}elseif($post['condition_id']==22 || $post['condition_id']==7){
					$prixconvert=.80;
					$endprix=.95;
				}elseif($post['condition_id']<7){
					$prixconvert=.75;
					$endprix=.95;
				}
/* 					if(($post['pricesuggest']>0&&($dateverification <= $datapricesuggest))&&($post['price_with_shipping']==0 ||$post['price_with_shipping']=="")){//
						//echo $post['price_with_shipping'];
						//echo "<br>";
						//echo $post['pricesuggest'];
						$post['suggestebay']=$post['pricesuggest']*$prixconvert;
						$price_replace=explode('.',$post['suggestebay']);
						$post['suggestebay']=$price_replace[0]+$endprix;
						$post['price_with_shipping']=$price_replace[0]+$endprix;
						//echo $post['price_with_shipping'];
					//echo "allo";
					} */
			$post['new']=1;
			$post['marketplace_item_id']=$data['marketplace_item_id'];
			$post['name']=$data['name'];
			$wordsearch=str_replace(" ", "+",$data['name']);
			$post['quantity']=$data['quantity'];
			$post['unallocated_quantity']=$data['unallocated_quantity'];
			$post['quantitytotal']=$data['quantity_total'];
			$post['image']=$data['image'];
			$post['product_id']=$data['product_id'];
			(string)$post['sku'] =$data['sku'];
			$post['date_price_upd_magasin']=$data['date_price_upd'];
			$post['date_price_upd']=$data['date_price_upd'];
			$post['ebay_date_relisted']=$data['ebay_date_relisted'];
			$datemagasin = new DateTime($data['date_price_upd']);
			$datemagasin = $datemagasin->format('Y-m-d');
			$datemagasin=date_parse ($datemagasin);
			$datemagasin2 = new DateTime($data['date_price_upd']);
			$datemagasin2 = $datemagasin2->format('Y-m-d');
			$dateretail = new DateTime($data['date_price_upd']);
			$dateretail = $dateretail->format('Y-m-d');
			$dateretail=date_parse ($dateretail);
			$dateretail2 = new DateTime($data['date_price_upd']);
			$dateretail2= $dateretail2->format('Y-m-d');
/* 			if($dateverification > $dateretail)  print_r($dateretail);
			if($dateverification > $datemagasin) print_r($datemagasin); */
			//echo $post['shipping'].'<br><br>';
		}
			//print_r($datapricesuggest);
			//echo "<br><br>";
			//print_r($dateverification);
			$shipping=isset($post['shipping'])?$post['shipping']:0;
				$post['suggest']=(($post['price_with_shipping'])-$shipping);
				$post['suggest']= number_format($post['suggest'], 2,'.', '');
				$price_replace=explode('.',$post['suggest']);
				$post['suggest']=$price_replace[0]+$endprix;
				//$post['price']= number_format($post['price'], 2, '.', '');
				if($post['price']<1){
					$post['price']=number_format(0.00, 2,'.', '');
				}else{
					//$price_replace=explode('.',$post['price']);
					//$post['price']=$price_replace[0]+$endprix;
				}
				if(($post['pricesuggest']>0&&($dateverification <= $datapricesuggest))){//
					$post['suggestebay']=$post['pricesuggest']*$prixconvert;
					$price_replace=explode('.',$post['suggestebay']);
					$post['suggestebay']=$price_replace[0]+$endprix;
					//echo "allo";
				}elseif(($post['findprice']+$post['findshipping'])>0){
					$post['suggestebay']=($post['findprice']+$post['findshipping']-.95)*$prixconvert;
					$price_replace=explode('.',$post['suggestebay']);
					$post['suggestebay']=$price_replace[0]+$endprix;
				}
				if(isset($post['pourverification']) && $post['pourverification']=="oui"){
					$post['price_with_shipping']=0;
					$sql2 = 'UPDATE `oc_product` SET `status`=0,ebay_last_check="2020-09-01",remarque_interne="('.$post['remarque_interne'].')" where product_id='.$post['product_id'];
					//echo $sql2.'<br><br>';
					$req2 = mysqli_query($db,$sql2);
								 unset ($post['product_id']);
								unset ($post['price']);
								unset ($post['price_with_shipping']);
								unset ($post['upc']);
								unset ($post['upcorigine']);
								unset ($post['price']);
								unset ($post['location_magasin']);
								unset ($post['location']);
								unset ($post['condition_id']);
								unset ($post['marketplace_item_id']);
								unset ($post['skuold']);
								unset ($post['new']);
								unset ($post['sku'] );
								unset ($post['unallocated_quantity']); 
								unset ($post['quantitymagasin_ajouter']);
								unset ($post['shipping']);
								unset ($post['remarque_interne']);
				}else{
					//echo $updquantity;
					if($post['marketplace_item_id']>1){
						$resultebay="";
						//$result = json_encode($new); 
						mise_en_page_description($connectionapi,$post['product_id'],$db);
						if (isset($post['quantityentrepot_ajouter'], $post['quantity'], $post['quantitymagasin_ajouter'], $post['unallocated_quantity'])) {
							// Les variables sont définies, on peut faire le calcul
							$totalQuantity = $post['quantityentrepot_ajouter'] + $post['quantity'] + $post['quantitymagasin_ajouter'] + $post['unallocated_quantity'];
							// faire quelque chose avec $totalQuantity...
							$result=revise_ebay_product($connectionapi,$post['marketplace_item_id'],$post['product_id'],$totalQuantity,$db,"oui");
						}
						$json = json_decode($result, true); 
							if(isset($post['showerror']) && $post['showerror']=="oui")
							//	//print("<pre>".print_r ($json,true )."</pre>");
								if($json["Ack"]=="Failure"){
									$resultebay.="ERREUR: ".$json["Errors"]["ShortMessage"];
									//print("<pre>".print_r ($json,true )."</pre>");
								}elseif($json["Ack"]=="Warning"){
									//$resultebay.="WARNING: ".$json["Errors"]["ShortMessage"];
								}
					}
				}
			if(isset($post['processing']) && $post['processing']=="oui"){
					header("location: listing.php"/* ?insert=oui&sku=".(string)$post['upc'] */); 
					exit();
			}
			if(isset($post['marketplace_item_id']) && $post['marketplace_item_id']!=""){
				$post['ebay_id_a_cloner']="";
			}
			return $post;
}
function get_product($sku = null) {
    // Debugging print statement
    // Include the database connection file
    include 'connection.php';
    // Validate the SKU input and construct the SQL WHERE clause accordingly
    if (is_numeric($sku) && strlen($sku) == 13) {
        $upc = 'P.condition_id=9 AND P.upc = "' . mysqli_real_escape_string($db, $sku) . '"';
    } else {
        $upc = 'P.sku = "' . mysqli_real_escape_string($db, $sku) . '"';
    }
    // SQL query to retrieve product details
    $sql = 'SELECT *, (P.quantity+P.unallocated_quantity) AS quantity_total, P.product_id AS product_id, PD.name AS name_en, PD.color AS color_en, 
            PD.description_supp AS description_supp_en, PD.condition_supp AS condition_supplementaire, P.price AS priceretail, 
            P.price AS price_magasin, PDF.name AS name_fr, PDF.color AS color_fr, PDF.description_supp AS description_supp_fr, 
            P.image AS image_product,  P.unallocated_quantity, P.quantity, 
            C.name AS condition_name, M.name AS brand,  P.location AS location
            FROM `oc_product` P 
            LEFT JOIN `oc_product_description` PD ON (P.product_id = PD.product_id) 
            LEFT JOIN `oc_product_description` PDF ON (P.product_id = PDF.product_id AND PDF.language_id = 2)
            
            
            LEFT JOIN `oc_manufacturer` M ON (M.manufacturer_id = P.manufacturer_id)
            LEFT JOIN `oc_condition` C ON (P.condition_id = C.condition_id AND C.language_id = 2)
            WHERE PD.language_id = 1 AND (' . $upc . ') ORDER BY C.sort_order';
    // Execute the query and handle potential errors
    $req = mysqli_query($db, $sql);
    if (!$req) {
        printf("Error: %s\n", mysqli_error($db));
        exit();
    }
    $result = array();
    // Check if the query returned any results
    if (mysqli_num_rows($req) > 0) {
        while ($data = mysqli_fetch_assoc($req)) {
            // SQL query to retrieve category details
            $sql2 = 'SELECT CD.category_id AS category_id, CD.name AS category_name,specifics
                     FROM `oc_product_to_category` PC 
                     LEFT JOIN `oc_category` C ON (PC.category_id = C.category_id)
                     LEFT JOIN `oc_category_description` CD ON (C.category_id = CD.category_id AND CD.language_id = 1 AND C.leaf = 1)
                     WHERE PC.product_id = "' . mysqli_real_escape_string($db, $data['product_id']) . '" 
                     ORDER BY CD.category_id DESC LIMIT 1';
            // Execute the category query and handle potential errors
            $req2 = mysqli_query($db, $sql2);
            if (!$req2) {
                printf("Error: %s\n", mysqli_error($db));
                exit();
            }
            $data2 = mysqli_fetch_assoc($req2);
            if ($data2['category_id'] != "" && isset($data2['category_id'])) {
                $result[] = array_merge($data, $data2);
            } else {
                $result[] = $data;
            }
        }
        // Close the database connection
        mysqli_close($db);
	//	//print("<pre>" . print_r($result, true) . "</pre>");
        // Return the result
        return $result;
    } else {
        // Close the database connection and return null if no results found
        mysqli_close($db);
	//	//print("<pre>" . print_r($result, true) . "</pre>");
        return null;
    }
}
function get_product_by_sku(string $sku){
	include 'connection.php';
	//echo (strlen($sku));
    ajouter_item_com($sku,$db);
		$upc='P.sku = "'.$sku .'"'; //*EPL.listing_id AS etsy_id,*/
	$sql = 'SELECT *,(P.quantity+P.unallocated_quantity) AS quantity_total,
			P.product_id AS product_id,
			PD.name AS name_en, PD.color AS color_en, PD.description_supp AS description_supp_en,PD.condition_supp AS condition_supplementaire,P.price AS priceretail,P.price AS price_magasin,
			PDF.name AS name_fr, PDF.color AS color_fr, PDF.description_supp AS description_supp_fr,P.image AS image_product,
			P.unallocated_quantity,P.quantity, C.name AS condition_name,M.name AS brand,P.location AS location
			FROM `oc_product` P 
			LEFT JOIN `oc_product_description` PD ON (P.product_id=PD.product_id) 
		/*	LEFT JOIN `oc_etsy_products_list` EPL ON (P.product_id=EPL.product_id) */
			LEFT JOIN `oc_product_description` PDF ON (P.product_id=PDF.product_id AND PDF.language_id=1)
			LEFT JOIN `oc_manufacturer` M ON (M.manufacturer_id=P.manufacturer_id)
			LEFT JOIN `oc_condition` C ON (P.condition_id=C.condition_id AND C.language_id=1)
			where PD.language_id=1 and ( '.$upc.') ';//order by C.sort_order
					/* 	LEFT JOIN `oc_product_to_category` PC ON (P.product_id=PC.product_id)
			LEFT JOIN `oc_category_description` CD ON (CD.category_id=PC.category_id AND C.leaf=1 AND CD.language_id=1)
 */
			$req = mysqli_query($db,$sql);
		//	echo $sql;
		//	//print("<pre>".print_r ($req,true )."</pre>");
			$result= array();
			while($data = mysqli_fetch_assoc($req))
			{
				$sql2='SELECT CD.category_id AS category_id,CD.name AS category_name
						FROM `oc_product_to_category` PC 
						LEFT JOIN `oc_category` C ON (PC.category_id=C.category_id)
						LEFT JOIN `oc_category_description` CD ON (C.category_id=CD.category_id AND CD.language_id=1 AND C.leaf=1)
						where PC.product_id="'.$data['product_id'].'" order by CD.category_id DESC limit 1';
					//	echo $sql2;
						$req2 = mysqli_query($db,$sql2);
						$data2 = mysqli_fetch_assoc($req2);
						if($data2['category_id']!="" && isset($data2['category_id'])){
							$result[]=array_merge($data,$data2);
						}else{
							$result[]=$data;
						}
			}
		//	//print("<pre>".print_r ($result,true )."</pre>");
			mysqli_close($db);  
			return $result[0];
}
function get_products_by_id($product_id){
	include 'connection.php';
	$sql = 'SELECT *,(P.quantity+P.unallocated_quantity) AS quantity_total,
			PD.name AS name_en, PD.color AS color_en, PD.description_supp AS description_supp_en,PD.condition_supp AS condition_supplementaire,P.price AS priceretail,P.price AS price_magasin,
			PDF.name AS name_fr, PDF.color AS color_fr, PDF.description_supp AS description_supp_fr,P.image AS image_product,
			P.unallocated_quantity,P.quantity, CO.name AS condition_name,M.name AS brand,P.location AS location,
			CD.category_id AS category_id,CD.name AS category_name,P.date_price_upd AS date_price_upd_magasin, CD.specifics
			FROM `oc_product` P 
			LEFT JOIN `oc_product_description` PD ON (P.product_id=PD.product_id) 
		/*	LEFT JOIN `oc_etsy_products_list` EPL ON (P.product_id=EPL.product_id) */
			LEFT JOIN `oc_product_description` PDF ON (P.product_id=PDF.product_id )
			LEFT JOIN `oc_manufacturer` M ON (M.manufacturer_id=P.manufacturer_id)
			LEFT JOIN `oc_condition` CO ON (P.condition_id=CO.condition_id )
			LEFT JOIN  `oc_product_to_category` PC ON (PC.product_id=P.product_id)
			LEFT JOIN `oc_category` CA ON (PC.category_id=CA.category_id)
			LEFT JOIN `oc_category_description` CD ON (CA.category_id=CD.category_id )
			where PD.language_id=1 AND CD.language_id=1 AND CA.leaf=1 AND PDF.language_id=1 AND CO.language_id=1 
			and P.product_id = "'.$product_id .'"  GROUP BY P.product_id order by CO.sort_order';
					/* 	LEFT JOIN `oc_product_to_category` PC ON (P.product_id=PC.product_id)
			LEFT JOIN `oc_category_description` CD ON (CD.category_id=PC.category_id AND CA.leaf=1 AND CD.language_id=1)
 */
			$req = mysqli_query($db,$sql);
		//	echo $sql;
			$result= array();
			if(mysqli_num_rows($req)>0){
				/*while($data = mysqli_fetch_assoc($req))
				{
					$sql2='SELECT CD.category_id AS category_id,CD.name AS category_name
							FROM `oc_product_to_category` PC 
							LEFT JOIN `oc_category` C ON (PC.category_id=C.category_id)
							LEFT JOIN `oc_category_description` CD ON (C.category_id=CD.category_id AND CD.language_id=1 AND CA.leaf=1)
							where PC.product_id="'.$data['product_id'].'" order by CD.category_id DESC limit 1';
							//echo $sql2;
							$req2 = mysqli_query($db,$sql2);
							$data2 = mysqli_fetch_assoc($req2);
							if($data2['category_id']!="" && isset($data2['category_id'])){
								$result[]=array_merge($data,$data2);
							}else{
								$result[]=$data;
							}
				}
				mysqli_close($db);  */
				$post=mysqli_fetch_assoc($req);
			//	//print("<pre>".print_r ($post,true )."</pre>");
				return $post ;
			}else{
				return "ALLO";
			}
}
function get_products_by_search(string $search){
	include 'connection.php';
	$sql = 'SELECT */*,EPL.listing_id AS etsy_id*/,(P.quantity+P.unallocated_quantity) AS quantity_total,
			PD.name AS name_en, PD.color AS color_en, PD.description_supp AS description_supp_en,PD.condition_supp AS condition_supplementaire,P.price AS priceretail,P.price AS price_magasin,
			PDF.name AS name_fr, PDF.color AS color_fr, PDF.description_supp AS description_supp_fr,P.image AS image_product,
			P.unallocated_quantity,P.quantity, CO.name AS condition_name,M.name AS brand,P.location AS location
			FROM `oc_product` P 
			LEFT JOIN `oc_product_description` PD ON (P.product_id=PD.product_id) 
		/*	LEFT JOIN `oc_etsy_products_list` EPL ON (P.product_id=EPL.product_id) */
			LEFT JOIN `oc_product_description` PDF ON (P.product_id=PDF.product_id AND PDF.language_id=2)
			LEFT JOIN `oc_manufacturer` M ON (M.manufacturer_id=P.manufacturer_id)
			LEFT JOIN `oc_condition` CO ON (P.condition_id=CO.condition_id AND CO.language_id=2)
			where PD.language_id=1 and PD.name like "%'.$search .'%" order by CO.sort_order';
					/* 	LEFT JOIN `oc_product_to_category` PC ON (P.product_id=PC.product_id)
			LEFT JOIN `oc_category_description` CD ON (CD.category_id=PC.category_id AND CA.leaf=1 AND CD.language_id=1)
 */
			$req = mysqli_query($db,$sql);
			//echo $sql;
			$result= array();
			while($data = mysqli_fetch_assoc($req))
			{
				$sql2='SELECT CD.category_id AS category_id,CD.name AS category_name
						FROM `oc_product_to_category` PC 
						LEFT JOIN `oc_category` CA ON (PC.category_id=CA.category_id)
						LEFT JOIN `oc_category_description` CD ON (CA.category_id=CD.category_id AND CD.language_id=1 AND CA.leaf=1)
						where PC.product_id="'.$data['product_id'].'" order by CD.category_id DESC limit 1';
						//echo $sql2;
						$req2 = mysqli_query($db,$sql2);
						$data2 = mysqli_fetch_assoc($req2);
						if($data2['category_id']!="" && isset($data2['category_id'])){
							$result[]=array_merge($data,$data2);
						}else{
							$result[]=$data;
						}
			}
			//print("<pre>".print_r ($result,true )."</pre>");
			mysqli_close($db);  
			return $result;
}
function refresh_token($connectionapi){
	//$authCode = $_GET['code']; 
	$clientID = 'CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28';
	$clientSecret ='PRD-f78dd8ce63e4-212d-4ac1-8aa3-d2ad'; 
	$url = 'https://api.ebay.com/identity/v1/oauth2/token'; 
	$token='v^1.1#i^1#r^0#f^0#p^3#I^3#t^H4sIAAAAAAAAAOVYe2wURRjn2lIkCCSogCDhXFBR2LvZ3bu9vZW7cO21aaGPo1deJaTO7c72hu7tHruzbQ8TrGhIFHxEQEMkiBoUH8FQJTEQ8UlsiBFFTIn/kRg0ERNBDDWCunt9cK0W2h6GJt4/t/PN9/p9830z3wxoL574wJaKLZcmuyYU7G0H7QUuFzMJTCwev3BKYcGs8eNADoNrb/v89qLNhT8uNmFKTYt1yEzrmoncbSlVM8UsMURZhibq0MSmqMEUMkUiifFIdZXIeoCYNnSiS7pKuSujIYrjgC/AS7yPFfyCJPA2VevTWa+HKIkNyFwgoQT8fh5ChrHnTdNClZpJoEZCFAtYhgY+Ggj1wCdyfpEJejiea6DcK5FhYl2zWTyACmfdFbOyRo6v13YVmiYyiK2ECldGyuO1kcpoWU39Ym+OrnBvHOIEEsscOCrVZeReCVULXduMmeUW45YkIdOkvOEeCwOVipE+Z0bhfjbULAwqiOcYhUVIkbgbEsly3UhBcm03HAqWaSXLKiKNYJK5XkDtYCTWI4n0jmpsFZVRt/O33IIqVjAyQlRZSWTNinhZHeWOx2KG3oJlJDtAGQ6AgOBnWYEKp5M60nCbijdYWIZOMCTbA2T02uxR3BvwQUZLdU3GjoTprtFJCbIBoMFhYnPCZDPVarVGRCGOc7l8fF84/XyDs7w962mRpOasMErZHrmzw+svRl9yXE2HG5UenOCX/CiIQJAJCjzfV2lOreeVI2FnmSKxmNfxBSVghk5BoxmRtAolREt2eK0UMrBsq1NYTlAQLfNBhfYFFYVO+GWeZhSEAEKJhBQU/qepQoiBExZB/ekyeCKLN0TFJT2NYrqKpQw1mCW7EfUmR5sZopKEpEWvt7W11dPKeXSjycsCwHhXV1fFpSRKQaqfF1+fmcbZFJGQLWVikWTStjdtdhbaxrUmKswZcgwaJBNHqmoT+nJ4gG/hwdQhQJaq2I5AvW1ibGGs0E2C5LygqXoT1qoRSeryzcDm1PrQ+MqqI5VVecGLpNOVqZRFYEJFlTcF4dDofCAIgsG84Dmbm4ihIhK9GWljL0HrysrryuIVjfW1y8pq8kIaR5KByNhCt54z68FSZDSQ0tVGybLmmqBg4BKjvCmJKqq1TGRZeZ2/Qmq2pFV6KC/w1U14jOWuYJ8gAYYP+ADghwvNqfUhi3SM4SuF2op4Eqdp58N0PmJ1UZqRGYCgwiToYELhYIIV8lpW0zk7xxZuR960FcA09jibi0fSU14d2p2iQ2rMeuweDpPXtM9dT0/fZWv2GAjKuqZmRiM8AhmstdgntW5kRmOwX3gEMlCSdEsjozHXKzoCCcVSFayqTjs2GoM54iNxU4NqhmDJHIlJp9b7zWLNyThzBDbTMJMFKWMz7dTLsCRtmt3WS8hjt9rZK1+/w3lVqYFkbNiNd6Nl4LFVrAM2qZzdim5uI60Wamppywu4E96b1xkODTsWicdX1dZF8wIXRS1j7cwJcAnBF2QhrfgCDO1jAoAW+IRAS/YVmUeszAeCwz5r/xUzhv9JA1X06OujB83wjH0b5v3MsI/SQYScC+o/nim8A18Mw+OyP2az6xDY7DpY4HIBL7iHmQfuLi5cUVR46ywTE3v3gIrHxE0aJJaBPM0ok4bYKCh2tc44vO+DnDfKvevAzP5XyomFzKScJ0tw19WZ8czUGZNZBviAAHycnwk2gHlXZ4uY6UW3b7fu3dN1+IDVNrelAj/TQKXad1WByf1MLtf4cUWbXeOKL5uXf1g05ydX1+nZpzbNnbNzytrZm6bff2La1M7lB16pOTRV6f5s3gW05/w73J8FF+WuKSef3XqqdsO3qc4d+0+c27n/6QUSOrmlQX/31P5O5eGNE9kXOrsjb7xclXkz+mVHx4W3oxp/+Ltbfl77/VuTZe/F2L7PrSTd0by1+cqnZx5B3bsXbZux9aVtIawq65K/nl2y9KNdx8p/K946s6WrccI0/cCDRzrgV64Djy85uubSbUd3dB87+83iDU+8+Px7T35yvGP+hoaPY9GDBz/kv6j6/fId54+8dvw5wi346/3diztO44WN5+b8ccYKr0tY56etfcxo++VriV2yjZ6z/NW9no3UnQ/d99SVqpLtPcv3N7Q7M609FgAA';
	$headers = ['Content-Type: application/x-www-form-urlencoded', 'Authorization: Basic '.base64_encode($clientID.":".$clientSecret)];
	$body = http_build_query([ 'grant_type'=>'refresh_token','refresh_token' =>$token, 'scope'=>'https://api.ebay.com/buy/browse/v1/item_summary/search']); 
	$curl = curl_init(); 
	curl_setopt_array($curl, array( 
		CURLOPT_URL => $url, 
		CURLOPT_RETURNTRANSFER => 'true', 
		CURLOPT_CUSTOMREQUEST => 'POST', 
		CURLOPT_POSTFIELDS => $body, 
		CURLOPT_HTTPHEADER => $headers 
	)); 
	$result = curl_exec($curl); 
	$err = curl_error($curl); 
	curl_close($curl); 
		if ($err) { 
			//echo "cURL Error #:" . $err; 
		} else { 
		//echo $result."\n"; 
		}
	//echo $headers;
	//print("<pre>".print_r ($body,true )."</pre>");
		// Convert xml string into an object 
				//echo $result."\nallo";
				//$result=explode("-api",$result);
				$json = json_decode($result, true);
				//print("<pre>".print_r ($json,true )."</pre>");
}
function find_bestprice_ebay($connectionapi, $q, string $gtin, $sort, $limit, $ebay_id) {
    $response = search_ebay($connectionapi, "", "", $gtin, 1, 5);
    $ebay_id_a_cloner = 0;
    $price = 99999;
    $pricevariant = array(
        '1000' => array('price' => 99999, 'marketplace_item_id' => 0),
        '1500' => array('price' => 99999, 'marketplace_item_id' => 0),
        '1750' => array('price' => 99999, 'marketplace_item_id' => 0),
        '2000' => array('price' => 99999, 'marketplace_item_id' => 0),
        '2010' => array('price' => 99999, 'marketplace_item_id' => 0),
        '2020' => array('price' => 99999, 'marketplace_item_id' => 0),
        '2030' => array('price' => 99999, 'marketplace_item_id' => 0),
        '2500' => array('price' => 99999, 'marketplace_item_id' => 0),
        '2750' => array('price' => 99999, 'marketplace_item_id' => 0),
        '3000' => array('price' => 99999, 'marketplace_item_id' => 0),
        '4000' => array('price' => 99999, 'marketplace_item_id' => 0),
        '5000' => array('price' => 99999, 'marketplace_item_id' => 0),
        '6000' => array('price' => 99999, 'marketplace_item_id' => 0),
        '7000' => array('price' => 99999, 'marketplace_item_id' => 0)
    );
    $name = "";
    $nbItemspecific = 0;
    $weight = 0;
    $length = 0;
    $width = 0;
    $height = 0;
    $weightmajor = 0;
    $weightminor = 0;
    $model = "";
    $brand = "";
    $color = "";
    $image = "";
	$category_id=0;
	$catname ='';
    $json = json_decode($response, true);
    if ($json['searchResult']['@attributes']['count'] > 0) {
        if ($json['searchResult']['@attributes']['count'] == 1) {
            $items = array($json['searchResult']['item']);
        } else {
            $items = $json['searchResult']['item'];
        }
	
        foreach ($items as $item) {
            $item_result = get_ebay_product($connectionapi, $item['itemId']);
            $json2 = json_decode($item_result, true);
			if (isset($json2['Item']['PictureDetails']['PictureURL'][0]) && strncmp('http', $json2['Item']['PictureDetails']['PictureURL'][0], 4) === 0) {
				$image = $json2['Item']['PictureDetails']['PictureURL'][0];
			} elseif (isset($json2['Item']['PictureDetails']['PictureURL']) && strncmp('http', $json2['Item']['PictureDetails']['PictureURL'], 4) === 0) {
				$image = $json2['Item']['PictureDetails']['PictureURL'];
			} else {
				$image = "";
			}
			
           
		//	//print("<pre>".print_r ($image,true )."</pre>");
			$category_id = $item['primaryCategory']['categoryId'];
			$catname = $item['primaryCategory']['categoryName'];
		if(isset($json2['Item']['ItemSpecifics']['NameValueList'])){	
            if ($nbItemspecific < count($json2['Item']['ItemSpecifics']['NameValueList'])) {
                $ebay_id_a_cloner = $item['itemId'];
                $nbItemspecific = count($json2['Item']['ItemSpecifics']['NameValueList']);
               
                if (isset($item['ProductListingDetails']['BrandMPN']['Brand']))
                    $brand = $item['ProductListingDetails']['BrandMPN']['Brand'];
                foreach ($json2['Item']['ItemSpecifics']['NameValueList'] as $spec) {
                    if ($spec['Name'] == "Model" || $spec['Name'] == "model")
                        $model = $spec['Value'];
                    if ($spec['Name'] == "Color" || $spec['Name'] == "color")
                        $color = $spec['Value'];
                    if ($spec['Name'] == "Studio" || $spec['Name'] == "studio")
                        $brand .= "@" . $spec['Value'];
                }
                if ($model == "" && isset($item['ProductListingDetails']['BrandMPN']['Name'])) {
                    $model = $item['ProductListingDetails']['BrandMPN']['MPN'];
                }
            }
		}
            $nametmp = str_replace("new", "", strtolower($json2['Item']['Title']));
            $nametmp = str_replace("sealed", "", $nametmp);
            $nametmp = str_replace("bilingual", "", $nametmp);
            $nametmp = str_replace("free shipping", "", $nametmp);
            $nametmp = str_replace("()", "", $nametmp);
            if (strlen($name) < strlen($nametmp)) {
                $name = $nametmp;
            }
         //   $weighttmp = $json2['Item']['ShippingPackageDetails']['WeightMajor'] + ($json2['Item']['ShippingPackageDetails']['WeightMinor'] / 16);
		//print("<pre>".print_r ($item,true )."</pre>");
			 if ($pricevariant[$item['condition']['conditionId']]['price'] > (($item['sellingStatus']['currentPrice'] + (isset($item['shippingInfo']['shippingServiceCost'])?$item['shippingInfo']['shippingServiceCost']:0)))) {
                $pricevariant[$item['condition']['conditionId']]['price'] = number_format(($item['sellingStatus']['currentPrice'] + (isset($item['shippingInfo']['shippingServiceCost'])?$item['shippingInfo']['shippingServiceCost']:0)), 2, '.', '');
                $pricevariant[$item['condition']['conditionId']]['marketplace_item_id'] = $item['itemId'];
            }
            sleep(1);
        }
    }
    // ... (les autres blocs de code restent inchangés)
    $result = array(
        'ebay_id_a_cloner' => $ebay_id_a_cloner,
        'price_with_shipping' => $price,
        'pricevariant' => $pricevariant,
        'category_id' => $category_id,
        'categoryname' => $catname,
        'name' => $name,
        'weight' => $weightmajor,
        'weight2' => $weightminor,
        'length' => $length,
        'width' => $width,
        'height' => $height,
        'model' => $model,
        'brand' => $brand,
        'color' => $color,
        'image' => $image
    );
    return $result;
}
function find_bestprice_ebayOLDGPT($connectionapi,$q,string $gtin,$sort,$limit,$ebay_id){
//echo "Conditon:".$condition_id;
	$response=search_ebay($connectionapi,"","",$gtin,1,5);
		$ebay_id_a_cloner=0;
		$price=99999;
		$pricevariant= array(
			'1000'=> array ('price'=>99999,'marketplace_item_id'=>0), 
			'1500'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'1750'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'2000'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'2010'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'2020'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'2030'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'2500'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'2750'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'3000'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'4000'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'5000'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'6000'=> array ('price'=>99999,'marketplace_item_id'=>0),
			'7000'=> array ('price'=>99999,'marketplace_item_id'=>0)
		);
		$name="";
		$nbItemspecific=0;
		$weight=0;
		$length=0;
		$width=0;
		$height=0;
		$weightmajor=0;
		$weightminor=0;
		$model="";
		$brand="";
		$color="";
		$image="";
		$json = json_decode($response, true);
				if($json['searchResult']['@attributes']['count']>0){ 
					//echo $json['searchResult']['@attributes']['count']."ALLO";
					if ($json['searchResult']['@attributes']['count']==1){
						$items=array($json['searchResult']['item']);
					}else{
						$items=$json['searchResult']['item']; 
					}
						foreach($items as $item){
							$item_result=get_ebay_product($connectionapi,$item['itemId']);
							$json2 = json_decode($item_result, true);
							$image=$json2['Item']['galleryURL'];
							if($nbItemspecific<count($json2['Item']['ItemSpecifics']['NameValueList'])){
								$ebay_id_a_cloner=$item['itemId'];
								$nbItemspecific=count($json2['Item']['ItemSpecifics']['NameValueList']);
								$category_id=$item['primaryCategory']['categoryId'];
								$catname=$item['primaryCategory']['categoryName'];
								if(isset($item['ProductListingDetails']['BrandMPN']['Brand']))
									$brand=$item['ProductListingDetails']['BrandMPN']['Brand'];
								foreach($json2['Item']['ItemSpecifics']['NameValueList'] as $spec){
									if($spec['Name']=="Model" || $spec['Name']=="model")
										$model=$spec['Value'];
									if($spec['Name']=="Color" || $spec['Name']=="color")
										$color=$spec['Value'];
									if($spec['Name']=="Studio" || $spec['Name']=="studio")
										$brand.="@".$spec['Value'];
								}
								if($model=="" && isset($item['ProductListingDetails']['BrandMPN']['Name'])){
									$model=$item['ProductListingDetails']['BrandMPN']['MPN'];
								}
							}
							$nametmp=str_replace("new","",strtolower($json2['Item']['Title']));
							$nametmp=str_replace("sealed","",$nametmp);
							$nametmp=str_replace("bilingual","",$nametmp);
							$nametmp=str_replace("free shipping","",$nametmp);
							$nametmp=str_replace("()","",$nametmp);
							if(strlen($name)<strlen($nametmp)){
								$name=$nametmp;
							}
							$weighttmp=$json2['Item']['ShippingPackageDetails']['WeightMajor']+($json2['Item']['ShippingPackageDetails']['WeightMinor']/16);						
								if($pricevariant[$item['condition']['conditionId']]['price']>(($item['sellingStatus']['currentPrice']+$item['shippingInfo']['shippingServiceCost']))){
								//echo "<br>Plus Haut".$price;
								$pricevariant[$item['condition']['conditionId']]['price']=number_format(($item['sellingStatus']['currentPrice']+$item['shippingInfo']['shippingServiceCost']), 2,'.', ''); 
								$pricevariant[$item['condition']['conditionId']]['marketplace_item_id']=$item['itemId'];
							}
							sleep(1);
						}
					}
				if($json['searchResult']['@attributes']['count']>0){ 
					if ($json['searchResult']['@attributes']['count']==1){
						$items=array($json['searchResult']['item']);
					}else{
						$items=$json['searchResult']['item']; 
					}
					foreach($items as $item){
						$itemID=$item['itemId'];
						$price2=$item['sellingStatus']['currentPrice']+$item['shippingInfo']['shippingServiceCost']; 
						if ($itemID==$ebay_id)$nous='style="background-color:green; color: white;"';
						if (isset($item['sellingStatus']['bidCount']))$bid="(Auction) ";
									$nous="";
									$bid="";
					}
				}
			// Vendu	
			$response=search_ebay($connectionapi,"vendu",$q,"",1,5);
			$json = json_decode($response, true);
				if($json['searchResult']['@attributes']['count']>0){ 
					if ($json['searchResult']['@attributes']['count']==1){
						$items=array($json['searchResult']['item']);
					}else{
						$items=$json['searchResult']['item']; 
					}
						foreach($items as $item){
							$item_result=get_ebay_product($connectionapi,$item['itemId']);
							$json2 = json_decode($item_result, true);
							if($nbItemspecific<count($json2['Item']['ItemSpecifics']['NameValueList'])){
								$ebay_id_a_cloner=$item['itemId'];
								$nbItemspecific=count($json2['Item']['ItemSpecifics']['NameValueList']);
								$category_id=$item['primaryCategory']['categoryId'];
								$catname=$item['primaryCategory']['categoryName'];
								if(isset($item['ProductListingDetails']['BrandMPN']['Brand']))
									$brand=$item['ProductListingDetails']['BrandMPN']['Brand'];
								foreach($json2['Item']['ItemSpecifics']['NameValueList'] as $spec){
									if($spec['Name']=="Model" || $spec['Name']=="model")
										$model=$spec['Value'];
									if($spec['Name']=="Color" || $spec['Name']=="color")
										$color=$spec['Value'];
									if($spec['Name']=="Studio" || $spec['Name']=="studio")
										$brand.="@".$spec['Value'];
								}
								if($model=="" && isset($item['ProductListingDetails']['BrandMPN']['Name'])){
									$model=$item['ProductListingDetails']['BrandMPN']['MPN'];
								}
							}
							$nametmp=str_replace("new","",strtolower($json2['Item']['Title']));
							$nametmp=str_replace("sealed","",$nametmp);
							$nametmp=str_replace("bilingual","",$nametmp);
							$nametmp=str_replace("free shipping","",$nametmp);
							$nametmp=str_replace("()","",$nametmp);
							if(strlen($name)<strlen($nametmp)){
								$name=$nametmp;
							}
							$weighttmp=$json2['Item']['ShippingPackageDetails']['WeightMajor']+($json2['Item']['ShippingPackageDetails']['WeightMinor']/16);
							if($weight<$weighttmp){
								$weight=$weighttmp;
								$weightmajor=$json2['Item']['ShippingPackageDetails']['WeightMajor'];
								$weightminor=$json2['Item']['ShippingPackageDetails']['WeightMinor'];
							}
							if(($length*$width*$height)<($json2['Item']['ShippingPackageDetails']['PackageDepth']*$json2['Item']['ShippingPackageDetails']['PackageLength']*$json2['Item']['ShippingPackageDetails']['PackageWidth'])){
								$length=$json2['Item']['ShippingPackageDetails']['PackageLength'];
								$width=$json2['Item']['ShippingPackageDetails']['PackageWidth'];
								$height=$json2['Item']['ShippingPackageDetails']['PackageDepth'];
							}
							if($pricevariant[$item['condition']['conditionId']]['price']>(($item['sellingStatus']['currentPrice']+$item['shippingInfo']['shippingServiceCost']))){
								$pricevariant[$item['condition']['conditionId']]['price']=number_format(($item['sellingStatus']['currentPrice']+$item['shippingInfo']['shippingServiceCost']), 2,'.', ''); 
								$pricevariant[$item['condition']['conditionId']]['marketplace_item_id']=$item['itemId'];
							}
						sleep(1);
						}
					}
			// par nom
			$response=search_ebay($connectionapi,"","",$gtin,1,5);
			$json = json_decode($response, true);
				if($json['searchResult']['@attributes']['count']>0){ 
					if ($json['searchResult']['@attributes']['count']==1){
						$items=array($json['searchResult']['item']);
					}else{
						$items=$json['searchResult']['item']; 
					}
						foreach($items as $item){
							$item_result=get_ebay_product($connectionapi,$item['itemId']);
							$json2 = json_decode($item_result, true);
							if($nbItemspecific<count($json2['Item']['ItemSpecifics']['NameValueList'])){
								$ebay_id_a_cloner=$item['itemId'];
								$nbItemspecific=count($json2['Item']['ItemSpecifics']['NameValueList']);
								$category_id=$item['primaryCategory']['categoryId'];
								$catname=$item['primaryCategory']['categoryName'];
								if(isset($item['ProductListingDetails']['BrandMPN']['Brand']))
									$brand=$item['ProductListingDetails']['BrandMPN']['Brand'];
								foreach($json2['Item']['ItemSpecifics']['NameValueList'] as $spec){
									if($spec['Name']=="Model" || $spec['Name']=="model")
										$model=$spec['Value'];
									if($spec['Name']=="Color" || $spec['Name']=="color")
										$color=$spec['Value'];
									if($spec['Name']=="Studio" || $spec['Name']=="studio")
										$brand.="@".$spec['Value'];
								}
								if($model=="" && isset($item['ProductListingDetails']['BrandMPN']['Name'])){
									$model=$item['ProductListingDetails']['BrandMPN']['MPN'];
								}
							}
							$nametmp=str_replace("new","",strtolower($json2['Item']['Title']));
							$nametmp=str_replace("sealed","",$nametmp);
							$nametmp=str_replace("bilingual","",$nametmp);
							$nametmp=str_replace("free shipping","",$nametmp);
							$nametmp=str_replace("()","",$nametmp);
							if(strlen($name)<strlen($nametmp)){
								$name=$nametmp;
							}
							$weighttmp=$json2['Item']['ShippingPackageDetails']['WeightMajor']+($json2['Item']['ShippingPackageDetails']['WeightMinor']/16);
							if($weight<$weighttmp){
								$weight=$weighttmp;
								$weightmajor=$json2['Item']['ShippingPackageDetails']['WeightMajor'];
								$weightminor=$json2['Item']['ShippingPackageDetails']['WeightMinor'];
							}
							if(($length*$width*$height)<($json2['Item']['ShippingPackageDetails']['PackageDepth']*$json2['Item']['ShippingPackageDetails']['PackageLength']*$json2['Item']['ShippingPackageDetails']['PackageWidth'])){
								$length=$json2['Item']['ShippingPackageDetails']['PackageLength'];
								$width=$json2['Item']['ShippingPackageDetails']['PackageWidth'];
								$height=$json2['Item']['ShippingPackageDetails']['PackageDepth'];
							}
							if($pricevariant[$item['condition']['conditionId']]['price']>(($item['sellingStatus']['currentPrice']+$item['shippingInfo']['shippingServiceCost']))){
								$pricevariant[$item['condition']['conditionId']]['price']=number_format(($item['sellingStatus']['currentPrice']+$item['shippingInfo']['shippingServiceCost']), 2,'.', ''); 
								//echo "<br>Plus Bas2".$price;
								$pricevariant[$item['condition']['conditionId']]['marketplace_item_id']=$item['itemId'];
							}
							sleep(1);
						}
					}
				if($json['searchResult']['@attributes']['count']>0){
					if ($json['searchResult']['@attributes']['count']==1){
						$items=array($json['searchResult']['item']);
					}else{
						$items=$json['searchResult']['item'];
					}
					foreach($items as $item){
						$itemID=$item['itemId'];
						$price2=$item['sellingStatus']['currentPrice']+$item['shippingInfo']['shippingServiceCost']; 
						if ($itemID==$ebay_id)$nous='style="background-color:green; color: white;"';
						if (isset($item['sellingStatus']['bidCount']))$bid="(Auction) ";
									$nous="";
									$bid="";
					}
				}
			$result = array(
				'ebay_id_a_cloner'		=>$ebay_id_a_cloner,
				'price_with_shipping'			=>$price,
				'pricevariant'			=>$pricevariant,
				'category_id'				=>$category_id,
				'categoryname'			=>$catname,
				'name'					=>$name,
				'weight'				=>$weightmajor,
				'weight2'				=>$weightminor,
				'length'				=>$length,
				'width'					=>$width,
				'height'				=>$height,
				'model'					=>$model,
				'brand'					=>$brand,
				'color'					=>$color,
				'image'					=>$image/*,
				'html'					=>$html*/
			);
			//print("<pre>".print_r ($result,true )."</pre>");
			return $result;
}
function search_ebay($connectionapi, $vendu, $q, string $gtin, $sort, $limit) {
    if (!isset($limit)) $limit = 10;
    $sold = "";
    $order = "";
    $keyword = "";
    if ($gtin != "") {
        $keyword = $gtin;
    } else if ($q != "") {
        $keyword = $q;
    }
    if ($vendu) {
        $sold = '<itemFilter>
                        <name>SoldItemsOnly</name>
                        <value>true</value>
                </itemFilter>';
    } else {
        $order = '<sortOrder>PricePlusShippingLowest</sortOrder>';
    }
    $post = '<?xml version="1.0" encoding="utf-8"?>
                <findItemsByKeywordsRequest xmlns="http://www.ebay.com/marketplace/search/v1/services">
                    '.$order.'
                    <itemFilter>
                        <name>ListingType</name>
                        <value>FixedPrice</value>
                    </itemFilter>'.$sold.'
                    <keywords><![CDATA['.$keyword.']]></keywords>
                    <paginationInput>
                        <entriesPerPage>'.$limit.'</entriesPerPage>
                    </paginationInput>
                    <buyerPostalCode>12919</buyerPostalCode>
                </findItemsByKeywordsRequest>';
    $headers = array(
        "X-EBAY-SOA-SECURITY-APPNAME:CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28",
        "X-EBAY-SOA-OPERATION-NAME:findItemsByKeywords"
    );
    $connection = curl_init();
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($connection, CURLOPT_URL, "https://svcs.ebay.com/services/search/FindingService/v1");
    curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($connection, CURLOPT_POST, 1);
    curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($connection);
    curl_close($connection);
    $new = simplexml_load_string($response);
    $response = json_encode($new);
    $json = json_decode($response, true);
//	//print("<pre>".print_r ($json,true )."</pre>");
    return $response;
}
function search_ebay_all_results($connectionapi, $vendu, $q, string $gtin, $sort, $limit) {
    if (!isset($limit)) $limit = 10;
    $sold = "";
    $order = "";
    $keyword = "";
    if ($gtin != "") {
        $keyword = $gtin;
    } else if ($q != "") {
        $keyword = $q;
    }
    if ($vendu) {
        $sold = '<itemFilter>
                        <name>SoldItemsOnly</name>
                        <value>true</value>
                </itemFilter>';
    } else {
        $order = '<sortOrder>PricePlusShippingLowest</sortOrder>';
    }
    $post = '<?xml version="1.0" encoding="utf-8"?>
                <findItemsByKeywordsRequest xmlns="http://www.ebay.com/marketplace/search/v1/services">
                   '.$order.'
                    '.$sold.'
                    <keywords><![CDATA['.$keyword.']]></keywords>
                    <paginationInput>
                        <entriesPerPage>'.$limit.'</entriesPerPage>
                    </paginationInput>
                    <buyerPostalCode>12919</buyerPostalCode>
                </findItemsByKeywordsRequest>';
    $headers = array(
        "X-EBAY-SOA-SECURITY-APPNAME:CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28",
        "X-EBAY-SOA-OPERATION-NAME:findItemsByKeywords"
    );
    $connection = curl_init();
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($connection, CURLOPT_URL, "https://svcs.ebay.com/services/search/FindingService/v1");
    curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($connection, CURLOPT_POST, 1);
    curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($connection);
    curl_close($connection);
    $new = simplexml_load_string($response);
    $response = json_encode($new);
    $json = json_decode($response, true);
	//print("<pre>".print_r ($json,true )."</pre>");
    return $response;
}
function search_ebayOLDGPT($connectionapi,$vendu,$q,string $gtin,$sort,$limit){
	if (!isset($limit)) $limit=10;
/* 			if($q!=""){
				$endpoint.="q=".$q."&";
			} */
			$sold="";
			$order="";
			if($gtin!=""){
				$keyword=$gtin;
			}else if($q!=""){
				//echo $q;
				$keyword=$q;
			}
			if($vendu){
				$sold=" <itemFilter>
									<name>SoldItemsOnly</name>
									<value>true</value>
							</itemFilter>";
			}else{
				$order='<sortOrder>PricePlusShippingLowest</sortOrder>';
			}
			// if($sort!=""){
				// $endpoint.="sort=".$sort."&";
			// }
	$post = '<?xml version="1.0" encoding="utf-8"?>
				<findItemsByKeywordsRequest xmlns="http://www.ebay.com/marketplace/search/v1/services">
					'.$order.
				 /* <itemFilter>
					<name>Condition</name>
					<value>1000</value>
				  </itemFilter>*/
'
				  <itemFilter>
					<name>ListingType</name>
					<value>FixedPrice</value>
				  </itemFilter>				 
					'.$sold.'
				   <keywords><![CDATA['.$keyword.']]></keywords>
				     <paginationInput>
					<entriesPerPage>'.$limit.'</entriesPerPage>
				  </paginationInput>
				   <buyerPostalCode>12919</buyerPostalCode>
				</findItemsByKeywordsRequest>'; //	'.$infoprice.'
		$headers = array(
					"X-EBAY-SOA-SECURITY-APPNAME:CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28",
					"X-EBAY-SOA-OPERATION-NAME:findItemsByKeywords"// 3 for UK
		);
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($connection, CURLOPT_URL, "https://svcs.ebay.com/services/search/FindingService/v1");
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($connection);
		curl_close($connection);
				//echo $textoutput."\nallo"."<br>";
				$new = simplexml_load_string($response);  
				// Convert into json 
				$response = json_encode($new); 
				$json = json_decode($response, true);
			//	//print("<pre>".print_r ($post,true )."</pre>");
		//	//print("<pre>".print_r ($json,true )."</pre>");
		return $response;
}
function search_ebay_sold($connectionapi, $vendu, $q, string $gtin, $sort, $limit) {
    if (!isset($limit)) $limit = 10;
    $sold = "";
    $order = "";
    $keyword = "";
    if ($gtin != "") {
        $keyword = $gtin;
    } else if ($q != "") {
        $keyword = $q;
    }
    if ($vendu) {
        $sold = '<itemFilter>
                        <name>SoldItemsOnly</name>
                        <value>true</value>
                </itemFilter>';
    } else {
        $order = '<sortOrder>PricePlusShippingLowest</sortOrder>';
    }
    $access_token = $connectionapi['EBAYTOKEN'];
    $curl = curl_init();
    $url = "https://api.ebay.com/buy/marketplace_insights/v1_beta/item_sales/search?";
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url. "gtin=" . $keyword,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer ".$access_token,
            "X-EBAY-C-MARKETPLACE-ID: EBAY-US",
            "Content-Type: application/json"
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $json = json_decode($response, true);
    return $response;
}
function search_ebay_soldOLDGPT($connectionapi,$vendu,$q,string $gtin,$sort,$limit){
	if (!isset($limit)) $limit=10;
/* 			if($q!=""){
				$endpoint.="q=".$q."&";
			} */
			$sold="";
			$order="";
			if($gtin!=""){
				$keyword=$gtin;
			}else if($q!=""){
				//echo $q;
				$keyword=$q;
			}
			if($vendu){
				$sold=" <itemFilter>
									<name>SoldItemsOnly</name>
									<value>true</value>
							</itemFilter>";
			}else{
				$order='<sortOrder>PricePlusShippingLowest</sortOrder>';
			}
			// if($sort!=""){
				// $endpoint.="sort=".$sort."&";
			// }
	$post = '<?xml version="1.0" encoding="utf-8"?>
				<findItemsByKeywordsRequest xmlns="http://www.ebay.com/marketplace/search/v1/services">
					'.$order.
				 /* <itemFilter>
					<name>Condition</name>
					<value>1000</value>
				  </itemFilter>*/
'
				  <itemFilter>
					<name>ListingType</name>
					<value>FixedPrice</value>
				  </itemFilter>				 
					'.$sold.'
				   <keywords><![CDATA['.$keyword.']]></keywords>
				     <paginationInput>
					<entriesPerPage>'.$limit.'</entriesPerPage>
				  </paginationInput>
				   <buyerPostalCode>12919</buyerPostalCode>
				</findItemsByKeywordsRequest>'; //	'.$infoprice.'
		$headers = array(
					"X-EBAY-SOA-SECURITY-APPNAME:CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28",
					"X-EBAY-SOA-OPERATION-NAME:findItemsByKeywords"// 3 for UK
		);
		$access_token=$connectionapi['EBAYTOKEN'];
		$access_token='v^1.1#i^1#p^3#f^0#r^0#I^3#t^H4sIAAAAAAAAAOVZf2wbVx2P47QorC1dOwYqCDIPJEp19rsfPt/dam9O4jQmceLGTtpmK967u3fxa853l3vvklgwSDPWTh2j4ocYY2Iqv6SAgPFDYhKUTQwErTZpgw3KJkEEgrE/hrQypsHWrdw5P+oG9UfiTrXE/eN7776/Pt9fz+89MLO+/cOHeg+9ujH0ttZjM2CmNRRirwHt69ft2BRu3bauBdQRhI7NfGCmbTb8wk4CK6ajDCHi2BZBHdMV0yJKbTIZ8VxLsSHBRLFgBRGFakohnetXuChQHNemtmabkY5sdzKSQDqvCYImI0MVJCT7s9aSzKKdjIgyJ/GQFROCluBYpPnfCfFQ1iIUWjQZ4QDHMyDOAK7IyorAKoIYlXgwGukYQS7BtuWTREEkVTNXqfG6dbZe3FRICHKpLySSyqZ7CoPpbHdmoLgzVicrteiHAoXUI+ePumwddYxA00MXV0Nq1ErB0zRESCSWWtBwvlAlvWTMGsyvuVoX47KQEHQ+gSRNhPEr4soe261AenE7ghmsM0aNVEEWxbR6KY/63lAPII0ujgZ8EdnujuBntwdNbGDkJiOZzvS+4UJmKNJRyOddexLrSA+QsjwACSnOcVIk5ZRtZOFpE094WIeBNzTfAuQu6lwQvOjxFUq7bEvHAQfpGLBpJ/IBoJVu4uvc5BMNWoNu2qCBcXV0HLvkTk4cDeK7EFCPlq0gxKjiW9RRG146GEvZcS4frlR+8JIEJAB1VRB5SUaL6RHUekMpkgqilM7nY4EpSIVVpgLdcUQdE2qI0XzvehXkYl3h4wbHSwZidFE2GEE2DEaN6yLDGggBhFRVk6X/00yh1MWqR9Fytqz8UMObjBQ020F528RaNbKSpNaIFnNjmiQjZUodJRabmpqKTvFR2x2LcQCwsb25/oJWRhUYWabFlyZmcC1FND9rfHqFVh3fmmk/CX3l1lgkxbt6Hrq0WkCm6U8spfB5tqVWzl4AZJeJfQ8UfRXNhbHXJhTpDUHT0STWUAnrVwdZUOsXQsexogAkVogDn7UhkKY9hq0comX7KsG8EMRMLp3tbwia300hbS5Q9U2IW2xCCTHBgIQCQENg6ZRtwGaMY3FouFDMdJe6MyPZrkxDGNOOk61UPApVE2WbDKYAZCDLa4UX1HoNYrAkKxgaCrXHkdV8bXUo0zOUKfSWioN9mYGGgjmEDBeRcjHA2WzBTO9OZ9P+k8vn90xYmtc/Mu2K+7IZ0DvolCcdzekfS3SSquVNwD7JrFT7RmCxb2JgomuwrE1OYtvboarjBxI5kk4mG3JSAWkuarIedoAnRfAR5I7Srr1uZ9/4gCy5uNPtGSuj3pxVTff1DMV7tXFP22M3Bj43drWW3rd02V2u9WJzlri7UJilWgcq+aOGgpgZ85otiqrqb7kFGGelBIAACizSNVmGumEgHfGq1hBep+ngZoddzwAFUnS4hlffJoPWBa3hQhk7TPBCgpf8UDfD6ixA0GBVRlYNHqqc1BBuEmzj1og7qPW3CHvAT3wB0MHR4F9DVLMrMRt6tBxMlWpWd1wOUYz428DowjGALznqIqjbllldC/MqeLA16W8cbbe6FoXLzKvggZpmexZdi7pF1lVwGJ5pYNMMTgfWorCOfTVmWtCsUqyRNanEVpBtZBUsDqzWAOqYOEG9XBanP1dBroaiWF84fFylscv8lk2xgbXa2VCUeCrRXOzUjtyukJxlwy6vffi1Pn+hnSjSsYs0WvJc3MQdtK6VMuPTdMpDY5PTDfXOwOfNuCnNpwuFPYND3Q2B60aTzbYgJnhVEmQOMoaQYBmBTQBGElWJ0ZCMRMTpYkIWG8LcdCcqrCjxIC7GgXC5uFZM1J3i/s9Rfuz8a7VUS+1hZ0OPgdnQI62hENgJPsjeCG5YHx5uC2/YRjD1+xo0ogSPWZB6LoqOo6oDsdu6teVV8PcHtBd7v3Vk/M2piedvuqOl/lbv2H7w7uV7vfYwe03dJR9477kv69h3vGsj5yMGHCsLrCCOghvPfW1jr2+77i+7XswdvP1j+VPzp9SjXxXkgyCtg43LRKHQupa22VDLxKdPPPryibu2f/b+P//j3s+//K+jn7h5//aZN46/tPWGH9z8xobZu7v+MBc+s/3kh1z09mdu2fvQ2S+XbnsNVMubH2aePDH/4/veN3PtT+8cPbl1y/yfbjvSdlT/wsm/KQ9fl5OuP7XvPV//mvDC4Wvt/eVbY7/hbzKf+lzkV69sCp++5czmr7wU/tK/n3jksU99szRzNrzrk/85/Uvv9fY/nnr/yb8e3zb//Z9Xj56Z+8w9d32xcN/c05vox3f+fs9z37t/+DlFPLx9esO+EZwa2DW3+9vkwR2//e53Dp1+/I57p8/OyR9tf3NL8tnf3fpKD/3GJG7LPXT618/Ov/bg8Z88tav9h5tvv+fIoz/K//Nnr//iiQfeeXA0dnj47JY7R55fiOV/AbJxVJ1vHQAA';
	//	v^1.1#i^1#r^0#I^3#f^0#p^3#t^H4sIAAAAAAAAAOVZa2zbxh23/EibpXHRZWhXLwNUJkWHFpSOpB4kZ2mlLdlWYtmKJCee0dY9kkfpbIpkyKNkBRjiJVhR7EtaDGhadGuDIUXbpXtiyIcMK9B0H4ruw5p1xYZtGLK1iLMlRZdtWD61Gyk5juIhD1spImD8It3x//r9X8e7A4sbNj74xNgTFzcHbus+sggWuwMBZhPYuKHvof6e7oG+LtBCEDiyuH2x90DP2UEHVnRLzCPHMg0HBRcquuGIjckE5dqGaEIHO6IBK8gRiSIWpOy4yIaAaNkmMRVTp4KZVIJiGJ7lUESNxWUUg6rmzRqXZBbNBCVHOagwcS3Gx2RVQMh77zguyhgOgQZJUCxgORpEacAWGV7kOJFlQzwrzFDB3ch2sGl4JCFAJRvmig1eu8XWa5sKHQfZxBNCJTPSSGFSyqTSE8XBcIus5LIfCgQS17lyNGyqKLgb6i66thqnQS0WXEVBjkOFk00NVwoVpUvGrMP8hqsFoDC8ijgugmIxhYneFFeOmHYFkmvb4c9gldYapCIyCCb163nU84Y8hxSyPJrwRGRSQf9nlwt1rGFkJ6j0kPTVqUI6TwULuZxtVrGK1EZScQDE+SjL8lTSKpvIwAs63utiFfreUDwLkL2ssyl42eOrlA6bhop9Dic4YZIh5AFAq93EtrjJI5o0Jm1JI75xLXQsWHFndMaPbzOgLikbfohRxbMo2BhePxiXsuNyPtys/PBKLcJofCwC5DgXj3PN/PBrvb0cSfphknK5sG8LkmGdrkB7HhFLhwqiFc+9bgXZWBW5qMZyvIZoNSZodETQNFqOqjGa0RACCMmyIvD/p6lCiI1ll6CVdFn9ooE3QRUU00I5U8dKnVpN0uhEy8mx4CSoMiGWGA7XarVQjQuZdinMAsCEp7PjBaWMKpBaocXXJ6ZxI0UUr0F79CKpW541C14WesqNEpXkbDUHbVIvIF33Ji7l8BW2JVfPXgXksI49DxQ9FZ2Fccx0CFLbgqaiKlbQLFZvCTK/1q+KjmW8zsAzkSjwWNsCqZslbGQRKZu3BuZVIaazUma8LWheO4Wks0C1NiFmuQnFhRgN4iIAbYElNVODnRjHYn6qUEynZlPp3ZnhdFsYJcvKVCougbKOMh0GMwIEIAjrhOfXehOivySLGGoiMeeR0XltNZ8eyacLY7PFyZ3pibaCmUeajZxy0cfZacGUdkkZyXuyqakqJNNFe/dcoV4qFCEqz2mcYj9Ut6YJP8YPhcO4PDpkSZI6PrpvVK9ODo9KEzvGQXV8j12NxtRaItGWkwpIsVGH9bA5zimCHcieIcPT9tDO+QmBt/GQPVIqo7GsUZd2juSjY8q8q+wx2wOfLd2ipfdTXXYv13qxM0vcbhbmbKMDzXqjtoKYLrmdFkVZRrwSgVGGjwMIYIRBqiIIUNU05O3IZaUtvFbHwc1M2a4GCk7RYttefTsM2jA0pgplbNH+H8f/k8unaEZlAIIaI9OCrHFQZvm2cDv+Nm59uP1a/7Sw+/yOJwBaOOR/NYQUsxI2oUvK/tRsw+rgjRCFHW8bGGoeA3iSQzaCqmno9fUwr4EHG1Vv42ja9fUoXGFeAw9UFNM1yHrULbOugUNzdQ3run86sB6FLexrMdOAep1gxVmXSmz42easgcWC9QZAFTuWXy83xOnNVZCtoBBWm6ePazR2hd8wCdaw0jgbCjmu7Cg2thpnbjdJzophN9Q+vFo/fdWdKFKxjRQy69q4gztoSyul5xdIzUWl6kJbvdP3eSduSnNSobBnMp9qC1wKVTttQYxzMh8RWEhrkThDR5g4oPmYzNMKElAMsWosLsTawtxxJypMjOdAlI3c+Pf4qomWU9z/OcsPX3mvluxqPMyBwElwIPB6dyAABsH9zDZw34aeqd6eOwYcTLy+BrWQg0sGJK6NQvOobkFsd2/pugiWnlfOj736zflPanvPfPlrXa3XekceBZ9fudjb2MNsarnlA1svv+lj7rxnM+shBizDcxzLzoBtl9/2Mnf3fk75cNe5Q7/e8czShcTLm98bvKvvkfkvgM0rRIFAX1fvgUAX9fH5BwdO3faHfz918mX70XrPPXApfd9/zrLHjmT2/2noonFy4oOHD5968+DS74+PP3lH9wdLp77/WE/6XPjJF/5IPb7lV1tnhOyJSOTdf76xd2f/Yn5OPXvmH3+7l/rJK+//OV96+7Wvv/3S488eOjrAn3y3/q9ypP+d2uv3bz/8bfYHX/rd9PYX7zx+4SNh3/c2ffctsOW19551xZGnnvn46M9jf9+/7y8/3Prbvv09vVJwNPfIA7MHo09v//A8/csHbv/sK8fg6cOHpOeeCz6/deYzb534a+xE9ugx4/R3BqLb3vnKG2fu+gRv+zFLJvdt2vOz+MPTB/tfGOzl3//F6Y82/uax2ye+2H/uGy8e/9G9d7+56wKXrv30W81Y/hdEHz77cB0AAA==';
		$curl = curl_init();
		$url = "https://api.ebay.com/buy/marketplace_insights/v1_beta/item_sales/search?";
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url. "gtin=" . $keyword,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
			  "Authorization: Bearer ".$access_token,
			  "X-EBAY-C-MARKETPLACE-ID: EBAY-US",
			  "Content-Type: application/json"
			),
		  ));
		$response = curl_exec($curl);
		curl_close($curl);
				//echo $textoutput."\nallo"."<br>";
				//$new = simplexml_load_string($response);  
				// Convert into json 
				//$response = json_encode($new); 
				$json = json_decode($response, true);
			//	//print("<pre>".print_r ($post,true )."</pre>");
		//	//print("<pre>".print_r ($json,true )."</pre>");
		return $response;
}
function browse_ebay($connectionapi,$q,string $gtin,$sort,$limit,$db,$ebay_id){
		$response=search_ebay($connectionapi,"","",$gtin,$sort,$limit);
		$json = json_decode($response, true);
		//print("<pre>".print_r ($json,true )."</pre>");
		//echo $response."allo"; 
				if($json['searchResult']['@attributes']['count']>0){ 
					//echo $json['searchResult']['@attributes']['count']."ALLO";
					if ($json['searchResult']['@attributes']['count']==1){
						$items=array($json['searchResult']['item']);
					}else{
						$items=$json['searchResult']['item']; 
					}
					//print("<pre>".print_r ($items,true )."</pre>");
					$html='Info eBay par UPC <a href="https://www.ebay.com/sch/i.html?_nkw='.$gtin.'&LH_PrefLoc=1&LH_ItemCondition=3&LH_BIN=1&rt=nc" target="ebay2">Recherchez</a>
							<div class="divTable blueTable">
								<div class="divTableHeading">
									<div class="divTableRow">
										<div class="divTableHead">ItemID</div>
										<div class="divTableHead">Photo</div>
										<div class="divTableHead">Etat</div>
										<div class="divTableHead">Titre</div>
										<div class="divTableHead">Prix</div>
										<div class="divTableHead">Shipping</div>
										<div class="divTableHead">Total</div>
									</div>
								</div>
								<div class="divTableBody">';
					foreach($items as $item){
						$itemID=$item['itemId'];
						$price=$item['sellingStatus']['currentPrice']+$item['shippingInfo']['shippingServiceCost']; 
						if ($itemID==$ebay_id)$nous='style="background-color:green; color: white;"';
						if (isset($item['sellingStatus']['bidCount']))$bid="(Auction) ";
							$html.='<div class="divTableRow" >
										<div class="divTableCell" '.$nous.'>'.$itemID.'</div>
										<div class="divTableCell" '.$nous.'><img style="height: 40px;" alt="" src="'.$item['galleryURL'].'"></div>
										<div class="divTableCell" '.$nous.'>'.$item['condition']['conditionDisplayName'].'</div>
										<div class="divTableCell" '.$nous.'>'.$item['title'].'<br>CATEGORIE:'.$item['primaryCategory']['categoryId'].'=='.$item['primaryCategory']['categoryName'].'</div>
										<div class="divTableCell" '.$nous.'>'.$bid.$item['sellingStatus']['currentPrice'].'</div>';
										if($item['shippingInfo']['shippingServiceCost']=="")$item['shippingInfo']['shippingServiceCost']="A VERIFIER";
										$html.='<div class="divTableCell" '.$nous.'><a href="'.$item['viewItemURL'].'" target="ebay">'.$item['shippingInfo']['shippingServiceCost'].'</a></div>
										<div class="divTableCell" '.$nous.'>'.$price.'</div>
									</div>';
									$nous="";
									$bid="";
					}
					$html.='	</div>
							</div>
							';
				}
			$response=search_ebay($connectionapi,"",$q,"",$sort,$limit);
			$json = json_decode($response, true);
			//print("<pre>".print_r ($json,true )."</pre>");
				if($json['searchResult']['@attributes']['count']>0){
					if ($json['searchResult']['@attributes']['count']==1){
						$items=array($json['searchResult']['item']);
					}else{
						$items=$json['searchResult']['item'];
					}
					$html.='<br>Info eBay par nom <a href="https://www.ebay.com/sch/i.html?_nkw='.$q.'&LH_PrefLoc=1&LH_ItemCondition=3&LH_BIN=1&rt=nc" target="ebay2">Recherchez</a>	
							<div class="divTable blueTable">
								<div class="divTableHeading">
									<div class="divTableRow">
										<div class="divTableHead">ItemID</div>
										<div class="divTableHead">Photo</div>
										<div class="divTableHead">Etat</div>
										<div class="divTableHead">Titre</div>
										<div class="divTableHead">Prix</div>
										<div class="divTableHead">Shipping</div>
										<div class="divTableHead">Total</div>
									</div>
								</div>
								<div class="divTableBody">';
					foreach($items as $item){
						$itemID=$item['itemId'];
						$price=$item['sellingStatus']['currentPrice']+$item['shippingInfo']['shippingServiceCost']; 
						if ($itemID==$ebay_id)$nous='style="background-color:green; color: white;"';
						if (isset($item['sellingStatus']['bidCount']))$bid="(Auction) ";
							$html.='<div class="divTableRow" >
										<div class="divTableCell" '.$nous.'>'.$itemID.'</div>
										<div class="divTableCell" '.$nous.'><img style="height: 40px;" alt="" src="'.$item['galleryURL'].'"></div>
										<div class="divTableCell" '.$nous.'>'.$item['condition']['conditionDisplayName'].'</div>
										<div class="divTableCell" '.$nous.'>'.$item['title'].'<br>CATEGORIE:'.$item['primaryCategory']['categoryId'].'=='.$item['primaryCategory']['categoryName'].'</div>
										<div class="divTableCell" '.$nous.'>'.$bid.$item['sellingStatus']['currentPrice'].'</div>
										<div class="divTableCell" '.$nous.'><a href="'.$item['viewItemURL'].'" target="ebay">'.$item['shippingInfo']['shippingServiceCost'].'</a></div>
										<div class="divTableCell" '.$nous.'>'.$price.'</div>
									</div>';
									$nous="";
									$bid="";
					}
					$html.='	</div>
							</div>
							';
				}
			return $html;
}
function browse_ebay_old($connectionapi,$q,string $gtin,$sort,$limit,$db,$ebay_id){
			$sql = 'SELECT * FROM `oc_api` where api_id = "2" ';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req); 
			$token=$data['key'];
			$endpoint = 'https://api.ebay.com/buy/browse/v1/item_summary/search?';
/* 			if($q!=""){
				$endpoint.="q=".$q."&";
			} */
			if($gtin!=""){
				$endpoint.="gtin=".$gtin."&";
			}else{
				$endpoint.="q=".$q."&";
			}
			// if($sort!=""){
				// $endpoint.="sort=".$sort."&";
			// }
			if($limit!=""){
				$endpoint.="limit=".$limit."&";
			}$endpoint.="filter=buyingOptions:{FIXED_PRICE|BEST_OFFER}&sort=price&filter=searchInDescription:true&filter=conditionIds:{1000|1500}";
$endpoint=str_replace(" ","%20",$endpoint)		;
//echo $endpoint;
			$ch = curl_init();
			/* if your client is old and doesn't have our CA certs
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);*/
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			  "Authorization:Bearer ".$token,
			  "Content-Type:application/json",
			  "X-EBAY-C-MARKETPLACE-ID:EBAY_US",
			  "X-EBAY-C-ENDUSERCTX:affiliateCampaignId=<ePNCampaignId>,affiliateReferenceId=<referenceId>"
			));
			// HTTP GET
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_URL, $endpoint);
			$result = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			// proceed with other queries
			
			if ($httpcode != 200){
			  //echo "<br><br>error status $httpcode...\n";
			}else {
		// Convert xml string into an object 
				//echo $result."\nallo";
				$result=explode("-api",$result);
				$json = json_decode($result[1], true);
				//print("<pre>".print_r ($json['itemSummaries'],true )."</pre>");
				if(count($json['itemSummaries'])>0){
					$html='Info eBay
							<div class="divTable blueTable">
								<div class="divTableHeading">
									<div class="divTableRow">
										<div class="divTableHead">ItemID</div>
										<div class="divTableHead">Photo</div>
										<div class="divTableHead">Etat</div>
										<div class="divTableHead">Titre</div>
										<div class="divTableHead">Prix</div>
										<div class="divTableHead">Shipping</div>
										<div class="divTableHead">Total</div>
									</div>
								</div>
								<div class="divTableBody">';
					foreach($json['itemSummaries'] as $item){
						$itemID=explode("|",$item['itemId']);
						$price=$item['price']['value']+$item['shippingOptions'][0]['shippingCost']['value'];
							$html.='<div class="divTableRow">
										<div class="divTableCell">'.$itemID[1].'</div>
										<div class="divTableCell"><img style="height: 80px;" alt="" src="'.$item['thumbnailImages'][0]['imageUrl'].'"></div>
										<div class="divTableCell">'.$item['condition'].'</div>
										<div class="divTableCell">'.$item['title'].'</div>
										<div class="divTableCell">'.$item['price']['value'].'</div>
										<div class="divTableCell">'.$item['shippingOptions'][0]['shippingCost']['value'].'</div>
										<div class="divTableCell">'.$price.'</div>
									</div>';
					}
					$html.='	</div>
							</div>
							';
				}else{
					browse_ebay($connectionapi,$q,"",$sort,$limit,$db,$ebay_id);
				}
			}
			return $html;
}
function browse_ebay_standard($connectionapi,$q,string $gtin,$sort,$limit){
			$q=str_replace(" ","%20",$q);
			$endpoint = 'https://svcs.ebay.com/services/search/FindingService/v1?SECURITY-APPNAME=CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28&OPERATION-NAME=findItemsByKeywords&SERVICE-VERSION=1.0.0&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&keywords='.$gtin.'&paginationInput.entriesPerPage=3&GLOBAL-ID=EBAY-US&siteid=0';
//$endpoint ='https://svcs.ebay.com/services/search/FindingService/v1?SECURITY-APPNAME=CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28&OPERATION-NAME=findItemsByKeywords&SERVICE-VERSION=1.0.0&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&keywords=Samsung%20-%20S-view%20Flip%20Cover%20For%20Samsung%20Galaxy%20S4%20Cell%20Phones%20-%20White&paginationInput.entriesPerPage=6&GLOBAL-ID=EBAY-US&siteid=0';
/* 			if($q!=""){
				$endpoint.="q=".$q."&";
			}
			if($gtin!=""){
				$endpoint.="gtin=".$gtin."&";
			}
			if($sort!=""){
				$endpoint.="sort=".$sort."&";
			}
			if($limit!=""){
				$endpoint.="limit=".$limit;
			} */
				//echo $endpoint;
			// HTTP GET
				$result=file_get_contents($endpoint);
				$json = json_decode($result, true);
				//print("<pre>".print_r ($json,true )."</pre>");
			return $json;
	}
function delete_photo($product_id,$product_image_id,$db){
		//echo"Images suprimés<br>"; 
		if($product_image_id=="" || $product_image_id=="principal"){
	 		$sql = 'SELECT * FROM `oc_product` where product_id = "'.$product_id.'" ';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req); 
			//echo $data['image']."<br>";
			if(isset($data['image']))
				unlink($GLOBALS['SITE_ROOT'].'image/' . $data['image']);
			if($product_image_id==""){
				$sql = 'SELECT * FROM `oc_product_image` where product_id = "'.$product_id.'" ';
				//echo $sql."<br>";
				$req = mysqli_query($db,$sql);
				while($data = mysqli_fetch_assoc($req)){
					//echo $data['image']."<br>";
					unlink($GLOBALS['SITE_ROOT'].'image/' . $data['image']);
				}	
				$sql = 'DELETE FROM `oc_product_image` where product_id = "'.$product_id.'" ';	
				//echo $sql."<br>";
				$req = mysqli_query($db,$sql);
			}
		}elseif($product_image_id!="" && $product_image_id!="principal"){
			$sql = 'SELECT * FROM `oc_product_image` where product_image_id = "'.$product_image_id.'" ';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			while($data = mysqli_fetch_assoc($req)){
				//echo $data['image']."<br>";
				unlink($GLOBALS['SITE_ROOT'].'image/' . $data['image']);
			}	
			$sql = 'DELETE FROM `oc_product_image` where product_image_id = "'.$product_image_id.'" ';	
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
		}
}
function move_photo($connectionapi,$product_id,$ebay_id,$db){
	echo '<br>move_photo:';
		mkdir($GLOBALS['SITE_ROOT']."image/catalog/product/".$product_id."/", 0755, true);
		//echo "MKDIR$GLOBALS['SITE_ROOT'].'image/catalog/product/".$product_id."/"."<br>"; 
		$sqldir = 'catalog/product';
	 		$sql = 'SELECT * FROM `oc_product` where product_id = "'.$product_id.'" ';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req); 
			//print("<pre>".print_r ($data,true )."</pre>");
			//echo $data['image']."<br>";
			$data['image']=str_replace('catalog/product/','',$data['image']);
			rename($GLOBALS['SITE_ROOT'].'image/catalog/product/' . $data['image'], $GLOBALS['SITE_ROOT'].'image/catalog/product/' . $product_id.'/'.$data['image']);
			//echo '<br>RENAME:$GLOBALS['SITE_ROOT'].'image/catalog/product/' . $product_id.'/'.$data['image']."<br>";
			$sql2="UPDATE `oc_product` SET image ='".$sqldir."/".$product_id."/".$data['image']."' where product_id=".$product_id;
			//echo "<br>".$sql2."<br>";
			$req2=mysqli_query($db,$sql2);
				$sql = 'SELECT * FROM `oc_product_image` where product_id = "'.$product_id.'" ';
				//echo $sql."<br>";
				$req = mysqli_query($db,$sql);
				while($data = mysqli_fetch_assoc($req)){
					//echo $data['image']."<br>";
					$data['image']=str_replace('catalog/product/','',$data['image']);
					rename($GLOBALS['SITE_ROOT'].'image/catalog/product/' . $data['image'], $GLOBALS['SITE_ROOT'].'image/catalog/product/' . $product_id.'/'.$data['image']);
				//echo '<br>RENAME:$GLOBALS['SITE_ROOT'].'image/catalog/product/' . $product_id.'/'.$data['image']."<br>";
						$sql2="UPDATE `oc_product` SET image ='".$sqldir."/".$product_id."/".$data['image']."' where product_image_id=".$data['product_image_id'];
		//echo $sql2."<br>";
						$req2=mysqli_query($db,$sql2);
				}	
			mise_en_page_description($connectionapi,$product_id,$db); 
										//echo $post['marketplace_item_id'];
										/* if ($post['processing']=="oui"){ */
			if($ebay_id>1)
			{
				$result=revise_ebay_product($connectionapi,$ebay_id,$product_id,"non",$db,"non");
				//$result = json_encode($new); 
				//$result=revise_ebay_product($connectionapi,$post['marketplace_item_id'],$post['product_id'],$updquantity,$db);
				$json = json_decode($result, true); 
				//echo "<br>mise a jour<br>";
				$resultebay="";
				//print("<pre>".print_r ($json,true )."</pre>");
				if($json["Ack"]=="Failure"){
					$resultebay.="ERREUR: ".$json["Errors"]["ShortMessage"];
				//print("<pre>".print_r ($json,true )."</pre>");
				}elseif($json["Ack"]=="Warning"){
					//$resultebay.="WARNING: ".$json["Errors"]["ShortMessage"];
				}
			}
}
function check_product_on_ebay($connectionapi,$product_id,$db){
		 	$sql = 'SELECT * FROM `oc_product` where product_id = "'.$product_id.'" ';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			$result=get_ebay_product($connectionapi,$data['marketplace_item_id']);
			$result=json_decode($result, true);
			//print("<pre>".print_r ($result,true )."</pre>");
			if($result['Item']['ItemID']==""){
				$sql2 = 'UPDATE `oc_product` SET ebay_id="" ,ebay_last_check="2020-09-01",error_ebay="Plus dans ebay: '.$data['marketplace_item_id'].'" where product_id='.$product_id;
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				return "";
			}else{
				return $result['Item']['ItemID'];
			}
			//print_r($result)."<br>";
}
function get_image_link_for_ebay($connectionapi,$ebay_id_a_cloner,$codesource,$db){
	$imagexml="";
	if($codesource=="")$url_import=file_get_contents((string)$ebay_id_a_cloner);
	if(($codesource)!=""){
		$url_import_export=explode('<script>window.__PRELOADED_STATE__=',$ebay_id_a_cloner);
		$url_import_export2=explode('<script id="item" class="tb-optimized" type="application/json">',$ebay_id_a_cloner);
		$url_import_export3=explode("<meta property='og:image' content=",$ebay_id_a_cloner);
		$url_import_export4=explode("var data = {",$ebay_id_a_cloner);
		if(count($url_import_export)>1){
			//echo " Amazon.Com<br>";
			//echo count($url_import_export)."Deuxieme<br>";
			$url_import_export=explode(';</script><script>',$url_import_export[1]);
					$json = json_decode($url_import_export[0],true);	
					$id=$json ['product']['activeSkuId'];
					$brand= $json  ['entities']['skus'][$id]['brand']['name'] ;			
					$features=$json  ['entities']['skus'][$id]['featuresSpecifications'];
					$model= $json  ['entities']['skus'][$id]['modelNumber'] ;
					$name=$json['entities']['skus'][$id]['name'];			
					$description= $json ['entities']['skus'][$id]['longDescription'] ;
					$images=$json ['entities']['skus'][$id]['images'];
					//echo "<br><br>ALLO<br><br>";
	 				//print("<pre>".print_r ($json ['product']['activeSkuId'],true )."</pre>");//[labelContent]['longDescription']['product']['item']['images']); ['items']   ['variantNames'] [facets][labelContent]
					//var_dump($json);
					/*$imagexml='
						$imagexml.='<PictureURL><![CDATA['.$image_principal.']]></PictureURL>';			 
					$imagexml.=*/
					//var_dump(json_decode($json, true));
			//echo $url_import_export[0];[large]
				$imagexml='<PictureDetails><GalleryType>Gallery</GalleryType>
						<GalleryURL><![CDATA['.$images[0]['large']['url'].']]></GalleryURL>';
				//echo '<br>'.$imageprincipal;
				$i=1;
				$j=count($images);
				if($j>10)$j=10;
				//echo"alloavant";
				for($i=1;$i<=$j;$i++){
					$imagexml.='<PictureURL><![CDATA['.$images[$i]['large']['url'].']]></PictureURL>';
					//echo $image['large']['url'].'<br>'.$imagesecondaire;
					//echo"allo";
				}
				$imagexml.='</PictureDetails>';
		}elseif(count($url_import_export2)>1){
			//echo " Amazon.CA<br>";
		$url_import_export=explode('<script id="item" class="tb-optimized" type="application/json">',$ebay_id_a_cloner);
		$url_import_export=explode('</script>',$url_import_export[1]);
		//echo $url_import_export[0];
		$json = json_decode($url_import_export[0],true);
					$brand= $json['item']['product']['buyBox']['products'][0]['brandName'];			
					$features="";
					$model= $json['item']['product']['buyBox']['products'][0]['otherInfoValue'] ;
					$name=$json['item']['product']['buyBox']['products'][0]['productName'];			
					$description=$json['item']['product']['buyBox']['products'][0]['detailedDescription'] ;
					$images= $json['item']['product']['buyBox']['products'][0]['images'];
	 				//print("<pre>".print_r ($json['item']['product']['buyBox']['products'],true )."</pre>");
					/*//echo "<br><br>ALLO<br><br>";
					//echo '<br><br>$brand='.$brand;
					//echo '<br><br>'.$features;
					//echo '<br><br>$model='.$model;
					//echo '<br>$name='.$name;
					//echo '<br>$description='.$description; */
					//var_dump(json_decode($json, true));
			//echo $url_import_export[0];
				//$imagetmp=explode("?",$json["Item"]["PictureDetails"]["GalleryURL"]);
				//print_r($json['item']['product']['buyBox']['products'][0]['images']);
				$imagexml='<PictureDetails> 
						<GalleryType>Gallery</GalleryType>
						<GalleryURL><![CDATA['.$images[0]['url'].']]></GalleryURL>';
				//echo '<br>'.$imageprincipal;
				$i=1;
				$j=count($images);
				if($j>10)$j=10;
				//echo"alloavant2";
				for($i=1;$i<=$j;$i++){
					$imagexml.='<PictureURL><![CDATA['.$images[$i]['url'].']]></PictureURL>';
					//echo $image['url'].'<br>'.$imagesecondaire;
				}
				$imagexml.='</PictureDetails>';
		}elseif(count($url_import_export4)>1){
			//echo "AMAZON.COM";
			$temp=str_replace("'colorImages': { 'initial': [","",$url_import_export4[1]);
			//echo "ALLO<br><br><br>".count($temp)."<br>";
			$temp=explode("colorToAsin",$temp);
			//echo strlen($temp[0]);
			$temp2=substr($temp[0],0 ,strlen($temp[0])-5);
			$temp3=explode('{"hiRes":"https://images-na.ssl-images-amazon.com/images/',$temp2);
			//echo "est".strlen($temp3[0])."tets";
			if(strlen($temp3[0])<10){
				unset($temp3[0]);
			}
			$i=1;
			foreach ($temp3 as $image){
				$temp3[$i]='{"hiRes":"https://images-na.ssl-images-amazon.com/images/'.substr($image,0,strlen($image)-1);	
				$i++;
			}
			$json = json_decode($temp3[1],true);
			if($json['hiRes']=="" || !isset($json['hiRes'])){
				$image_principal=$json['Large'];
			}else{
				$image_principal=$json['hiRes'];
			}
			$imagexml='<PictureDetails> 
						<GalleryType>Gallery</GalleryType>
						<GalleryURL><![CDATA['.$image_principal.']]></GalleryURL>';
			unset($temp3[1]);
			foreach ($temp3 as $image){
				$json = json_decode($image,true);
				$imagexml.='<PictureURL><![CDATA['.$json['hiRes'].']]></PictureURL>';
				//print("<pre>".print_r ($json,true )."</pre>");
			}
			$imagexml.='</PictureDetails>';
		}
	}elseif($url_import != ""){
				//echo " non ebay<br>";
				//	 upload_from_link($product_id,$ebay_id_a_cloner,1,$db);	
	}else{
			//echo " ebay<br>";
				$result=get_ebay_product($connectionapi,$ebay_id_a_cloner);
				$json = json_decode($result, true);
				//print("<pre>".print_r ($json,true )."</pre>");
				$imagetmp=explode("?",$json["Item"]["PictureDetails"]["GalleryURL"]);
				if(count($imagetmp)>1){
					$imagexml='<PictureDetails> 
						<GalleryType>Gallery</GalleryType>
						<GalleryURL><![CDATA['.$imagetmp[0].']]></GalleryURL>';
				//echo '<br>'.$imageprincipal;
					$i=0;
					$j=count($imagetmp);
					foreach  ($json["Item"]["PictureDetails"]["PictureURL"] as $image){
						$imagetmp=explode("?",$image);
						$imagexml.='<PictureURL><![CDATA['.$imagetmp[0].']]></PictureURL>';
					//echo '<br>'.$imagesecondaire;
						$i++;
					}
				}else{
					$imagexml='<PictureDetails> 
						<GalleryType>Gallery</GalleryType>
						<GalleryURL><![CDATA['.$ebay_id_a_cloner.']]></GalleryURL>';
						$imagexml.='<PictureURL><![CDATA['.$ebay_id_a_cloner.']]></PictureURL>';
				}
				$imagexml.='</PictureDetails>';
	}
}
function link_to_download($connectionapi,$product_id,$html_source,$codesource,$db){
	//echo " walmart.COM<br>";
	//echo $html_source;
	if(($codesource)!=""){
	//	echo $html_source;
		if($codesource!="sourcecodenew"){
			delete_photo($product_id,"",$db);
		}/* else{
			//echo "allo";
			$html_source=file_get_contents($html_source);	
			//echo $html_source;
		} */ 
		//echo $html_source; 
		//print_r( explode('?odnHeight=160",',$html_source));
		$url_import_export=explode('<link href="//i5.walmartimages.ca" rel="dns-prefetch"/>',$html_source);
		if (empty($url_import_export6)) {
			$regex = '/hidden" src="https:\/\/(.*)\?odnHeight=612"/U';
			$url_import_export6 = search_image_to_extract($html_source, 'walmartimages.com', $regex);
		}
			$url_import_export2=array();//explode('<script id="item" class="tb-optimized" type="application/json">',$html_source);
		$url_import_export5=explode('odnHeight=160',$html_source);
//echo count($url_import_export5);	
		$url_import_export3=explode("<meta property='og:image' content=",$html_source);
		$url_import_export4=explode("var data = {",$html_source); 
		$url_import_export7=explode('<script type="application/ld+json">',$html_source); 
		if (empty($url_import_export6)) {
			$url_import_export6=search_image_to_extract($html_source,'holtrenfrew','/class="pdp-carousel__main-img js-carousel-img" src="\/\/(.+?)" alt="/');
		}
		/*echo "<br>url_import_export:<br>";
		//print("<pre>".print_r ($url_import_export,true )."</pre>");
		echo "<br>url_import_export2:<br>";
		//print("<pre>".print_r ($url_import_export2,true )."</pre>");
		echo "<br>url_import_export3:<br>";
		//print("<pre>".print_r ($url_import_export3,true )."</pre>");
		echo "<br>url_import_export4:<br>";
		//print("<pre>".print_r ($url_import_export4,true )."</pre>");
		echo "<br>url_import_export5:<br>";
		//print("<pre>".print_r ($url_import_export5,true )."</pre>");
		echo "<br>url_import_export6:<br>";
		//print("<pre>".print_r ($url_import_export6,true )."</pre>");*/
if(count($url_import_export7)>1){
	echo " toyrus.CA<br>";
			//echo count($url_import_export)."Deuxieme<br>";
			$dom = new DOMDocument;
// Charger le HTML dans le DOMDocument
$dom->loadHTML($html_source);
// Récupérer tous les éléments img
$images = $dom->getElementsByTagName('script');
// Tableau pour stocker les URLs uniques des images
$imageUrls = array();
// Parcourir les éléments img et ajouter les URLs au tableau
$scripts = $dom->getElementsByTagName('script');
// Parcourir les éléments script
foreach ($scripts as $script) {
	// Vérifier le type de script
	if ($script->getAttribute('type') === 'application/ld+json') {
		// Extraire le contenu JSON
		$json_content = $script->nodeValue;
		// Décoder le JSON en tableau PHP
		$data = json_decode($json_content, true);
		// Vérifier si le type est "Product" et qu'il contient des images
		if (isset($data['@type']) && $data['@type'] === 'Product' && isset($data['image'])) {
			$imageUrls= $data['image'];
		}
	}
}
	//	//print("<pre>".print_r ($imageUrls,true )."</pre>");
			//	$imageprincipal=explode("?",$images[1]);
				upload_from_ebay($product_id,$imageUrls[0],1,$db);
				//echo '<br>'.$imageUrls[0];
				$i=1;
			//	$j=$nbimage-1;
				unset($imageUrls[0]);
			//	if($j>10)$j=10;
				//echo"alloavant";
				foreach($imageUrls as $image){
			//		$image=explode("?",$images[$i]);
					upload_from_ebay($product_id,$image,0,$db);
				//	echo '<br>'.$image;
					//echo"allo";
				}
}
		if(count($url_import_export)>1){
			//echo " walmart.CA<br>";
			//echo count($url_import_export)."Deuxieme<br>";
			$dom = new DOMDocument;
// Charger le HTML dans le DOMDocument
$dom->loadHTML($html_source);
// Récupérer tous les éléments img
$images = $dom->getElementsByTagName('img');
// Tableau pour stocker les URLs uniques des images
$imageUrls = array();
// Parcourir les éléments img et ajouter les URLs au tableau
foreach ($images as $image) {
    $srcset = $image->getAttribute('srcset');
    $urls = explode(',', $srcset);
    foreach ($urls as $url) {
        $url = trim($url);
        // Ajouter uniquement les URLs non vides et non présentes dans le tableau
        if (!empty($url)) {
            // Supprimer les paramètres après le point d'interrogation
            $url = strtok($url, '?');
            // Ajouter l'URL modifiée au tableau
            if (!empty($url) && !in_array($url, $imageUrls)) {
                $imageUrls[] = $url;
            }
        }
    }
} 
			//	$imageprincipal=explode("?",$images[1]);
				upload_from_ebay($product_id,$imageUrls[0],1,$db);
				//echo '<br>'.$imageUrls[0];
				$i=1;
			//	$j=$nbimage-1;
				unset($imageUrls[0]);
			//	if($j>10)$j=10;
				//echo"alloavant";
				foreach($imageUrls as $image){
			//		$image=explode("?",$images[$i]);
					upload_from_ebay($product_id,$image,0,$db);
				//	echo '<br>'.$image;
					//echo"allo";
				}
		//	}
		}elseif(count($url_import_export2)>1){
		//echo " walmart.COM<br>";
		$url_import_export=explode('<script id="item" class="tb-optimized" type="application/json">',$html_source);
		$url_import_export=explode('</script>',$url_import_export[1]);
		//echo $url_import_export[0];
		$json = json_decode($url_import_export[0],true);
					$brand= $json['item']['product']['buyBox']['products'][0]['brandName'];			
					$features="";
					$model= $json['item']['product']['buyBox']['products'][0]['otherInfoValue'] ;
					$name=$json['item']['product']['buyBox']['products'][0]['productName'];			
					$description=$json['item']['product']['buyBox']['products'][0]['detailedDescription'] ;
					$images= $json['item']['product']['buyBox']['products'][0]['images'];
	 				//print("<pre>".print_r ($json['item']['product']['buyBox']['products'],true )."</pre>");
					/*//echo "<br><br>ALLO<br><br>";
					//echo '<br><br>$brand='.$brand;
					//echo '<br><br>'.$features;
					//echo '<br><br>$model='.$model;
					//echo '<br>$name='.$name;
					//echo '<br>$description='.$description; */
					//var_dump(json_decode($json, true));
			//echo $url_import_export[0];
				//$imagetmp=explode("?",$json["Item"]["PictureDetails"]["GalleryURL"]);
				//print_r($json['item']['product']['buyBox']['products'][0]['images']);
				$imageprincipal=upload_from_link_website($product_id,$images[0]['url'],1,$db);
				//echo '<br>'.$imageprincipal;
				$i=1;
				$j=count($images);
				if($j>10)$j=10;
				//echo"alloavant2";
				for($i=1;$i<=$j;$i++){
					$imagesecondaire=upload_from_link_website($product_id,$images[$i]['url'],0,$db);
					//echo $image['url'].'<br>'.$imagesecondaire;
				}
		}elseif(count($url_import_export5)>1){
			//echo " walmart.COM2<br>";
				//$url_import_export=explode('odnHeight=160",',$html_source);
				$url_import_export2=explode('i5.walmartimages.com/asr/',$url_import_export5[0]);
		//echo $url_import_export[0];
				//echo count($url_import_export5);
				//$url_import_export2[1]=str_replace('"/>',$url_import_export2[1]);
				$url_import_export3=explode('","name":',$url_import_export2[2]);
				//$images=substr($url_import_export2[2],0,-20);
				//echo 'https://i5.walmartimages.com/asr/'.$url_import_export3[0].'<br>';
				$imageprincipal=upload_from_link_website($product_id,'https://i5.walmartimages.com/asr/'.$url_import_export3[0],1,$db);
				//echo '<br>'.$imageprincipal;
				//$i=1;
				$j=count($url_import_export5);
				//echo "Count".$j.'<br>';
				if($j>10)$j=10;
				//echo"<br>alloavant2".$j;
				for($i=2;$i<$j;$i++){
					//echo $url_import_export5[$i]."ALLO<br>";
					$url_import_export2=explode('i5.walmartimages.com/asr/',$url_import_export5[$i]);
					//echo $url_import_export2[1];
					$url_import_export3=explode('?',$url_import_export2[1]);
					//echo $url_import_export3;
					//echo 'https://i5.walmartimages.com/asr/'.$url_import_export3[0].'<br>';
					$imagesecondaire=upload_from_link_website($product_id,'https://i5.walmartimages.com/asr/'.$url_import_export3[0],0,$db);
					//echo $image['url'].'<br>'.$imagesecondaire;
				}
		}elseif(count($url_import_export3)>1){
		//echo "archambault";
			//print("<pre>".print_r ($url_import_export3[1],true )."</pre>");
			$temp=explode("?404=default' />",$url_import_export3[1]);
			$image_principal=substr($temp[0],1 ,strlen($temp[0]));
			//print("<pre>".print_r ($image_principal,true )."</pre>"); 
			upload_from_link_website($product_id,$image_principal,1,$db);
		}elseif(count($url_import_export4)>1){
		//echo "AMAZON.COM";
			$temp=str_replace("'colorImages': { 'initial': [","",$url_import_export4[1]);
			//echo "ALLO<br><br><br>".count($temp)."<br>";
			$temp=explode("colorToAsin",$temp);
			//echo strlen($temp[0]);
			$temp2=substr($temp[0],0 ,strlen($temp[0])-5);
			$temp3=explode('{"hiRes":"https://images-na.ssl-images-amazon.com/images/',$temp2);
			//echo "est".strlen($temp3[0])."tets";
			if(strlen($temp3[0])<10){
				unset($temp3[0]);
			}
			$i=1;
			foreach ($temp3 as $image){
				$temp3[$i]='{"hiRes":"https://images-na.ssl-images-amazon.com/images/'.substr($image,0,strlen($image)-1);	
				$i++;
			}
			$json = json_decode($temp3[1],true);
			if($json['hiRes']=="" || !isset($json['hiRes'])){
				$image_principal=$json['Large'];
			}else{
				$image_principal=$json['hiRes'];
			}
			upload_from_link_website($product_id,$image_principal,1,$db);
			unset($temp3[1]);
			foreach ($temp3 as $image){
				$json = json_decode($image,true);
				upload_from_link_website($product_id,$json['hiRes'],0,$db);
				//print("<pre>".print_r ($json,true )."</pre>");
			}
		}elseif(!empty($url_import_export6)){
		//	//print("<pre>".print_r ($url_import_export6,true )."</pre>");	
			foreach ($url_import_export6 as $key => $value) {
				// Test si l'élément actuel est le premier élément du tableau
				if ($key === 0) {
					upload_from_link_website($product_id,$value,1,$db);
				  // Si c'est le cas, effectue une opération différente
				  //echo "<br>Opération différente pour le premier élément : " . $value . "\n";
				} else {
					upload_from_link_website($product_id,$value,0,$db);
				  // Sinon, effectue l'opération normale
				  //echo "<br>Opération normale pour les autres éléments : " . $value . "\n";
				}
			  }
		}else{
				echo " Autre Site<br>";
				//echo count($url_import_export)."Deuxieme<br>";
				$dom = new DOMDocument;
	// Charger le HTML dans le DOMDocument
	$dom->loadHTML($html_source);
	// Récupérer tous les éléments img
	$images = $dom->getElementsByTagName('img');
	// Tableau pour stocker les URLs uniques des images
	$imageUrls = array();
	// Parcourir les éléments img et ajouter les URLs au tableau
	foreach ($images as $image) {
		$srcset = $image->getAttribute('srcset');
		$urls = explode(',', $srcset);
		foreach ($urls as $url) {
			$url = trim($url);
			// Ajouter uniquement les URLs non vides et non présentes dans le tableau
			if (!empty($url)) {
				// Supprimer les paramètres après le point d'interrogation
				$url = strtok($url, '?');
				// Ajouter l'URL modifiée au tableau
				if (!empty($url) && !in_array($url, $imageUrls)) {
					$imageUrls[] = $url;
				}
			}
		}
	} 
				//	$imageprincipal=explode("?",$images[1]);
					upload_from_ebay($product_id,$imageUrls[0],1,$db);
					//echo '<br>'.$imageUrls[0];
					$i=1;
				//	$j=$nbimage-1;
					unset($imageUrls[0]);
				//	if($j>10)$j=10;
					//echo"alloavant";
					foreach($imageUrls as $image){
				//		$image=explode("?",$images[$i]);
						upload_from_ebay($product_id,$image,0,$db);
					//	echo '<br>'.$image;
						//echo"allo";
					}
			//	}
		}
	}
	if($codesource=="" && strlen($html_source)>14){
			//echo " non ebay<br>";
					 upload_from_link($product_id,$html_source,1,$db);	
	}elseif($codesource==""){
				//echo " ebay<br>";
				import_ebay($connectionapi,$html_source,$product_id,$db);
	}
}
function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } else if (is_string ($mixed)) {
        return mb_convert_encoding($mixed, 'UTF-8', 'ISO-8859-1');
    }
    return $mixed;
}
function import_ebay($connectionapi,$ebay_id_a_cloner,$product_id,$db){
	// Vérifier si $ebay_id_a_cloner est égal à 0
    if ($ebay_id_a_cloner == 0) {
        return; // Sortir de la fonction si $ebay_id_a_cloner est 0
    }
					//			
				$homepage=file_get_contents('https://www.ebay.com/itm/'.$ebay_id_a_cloner);
		
				$tmp=explode('"zoomImg":{"_type":"Image","title":"',$homepage);
		
				$j=((count($tmp)));
					$tmp2=explode('"ZOOM_GUID","URL":"',$tmp[1]);
					$tmp2=explode('","originalSize":',$tmp2[1]);
					$tmp3=str_replace('\u002F','/',$tmp2[0]); 

				$imageprincipal=upload_from_ebay($product_id,$tmp3,1,$db);
				for($i=2;$i<$j;$i++){
					$tmp2=explode('"ZOOM_GUID","URL":"',$tmp[$i]);
					$tmp2=explode('","originalSize":',$tmp2[1]);
					$tmp3=str_replace('\u002F','/',$tmp2[0]); 
					upload_from_ebay($product_id,$tmp3,0,$db);
		
				}	
}
function search_image_to_extract($html,$websitename,$pattern){
	//echo "<br>html:".$html;
	if (strpos($html, $websitename) !== false) {
		//$data=array();
		// Le mot est présent dans le code source
		//$pattern = '/class="pdp-carousel__main-img js-carousel-img" src="([^"]*)"/';
		// Exécution de la recherche de correspondance avec la chaîne de caractères
		if (preg_match_all($pattern, $html, $matches)) {
		//echo "<br>websitename:".$websitename;
	//echo "<br>pattern:".$pattern;
	//	//print("<pre>".print_r ($matches,true )."</pre>");
			// Boucle pour parcourir toutes les valeurs de l'attribut src
			return $matches[1];
		  } else {
			// Si aucune correspondance n'est trouvée, retourne null
			return null;
		  }
	  }else{
		return null;
	  }
}
function link_to_download_with_existing_picture($connectionapi,$product_id,$ebay_id_a_cloner,$db){
					//echo " ebay<br>";
				$result=get_ebay_product($connectionapi,$ebay_id_a_cloner);
				$json = json_decode($result, true);
				//print("<pre>".print_r ($json,true )."</pre>");
				$imagetmp=explode("?",$json["Item"]["PictureDetails"]["GalleryURL"]);
				/* if(count($imagetmp)==1){ */
					//$imageprincipal=upload_from_ebay($product_id,$imagetmp[0],1,$db);
					//echo '<br>'.$imageprincipal;
					$i=0;
					if(is_array ( $json["Item"]["PictureDetails"]["PictureURL"] )){
						$j=count($json["Item"]["PictureDetails"]["PictureURL"]);
					}else{
						$j=0;
					}
					if($j>1){
						unset ($json["Item"]["PictureDetails"]["PictureURL"][0]);
						foreach  ($json["Item"]["PictureDetails"]["PictureURL"] as $image){
							$imagetmp=explode("?",$image);
							$imagesecondaire[$i]=upload_from_ebay($product_id,$imagetmp[0],0,$db);
							//echo '<br>'.$imagesecondaire;
							$i++;
						}
					}
				/* }else{
					upload_from_link($product_id,$ebay_id_a_cloner,1,$db);	
					//echo "<br>1 photo";
				} */
}
function ajouter_item ($connectionapi,$skuachanger,$db) {
	//echo "ALLO2";
 			$sql = 'SELECT * FROM `oc_product` where sku = "'.$skuachanger.'" ';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req); 
			$product_id=$data['product_id'];

			$rowverif= mysqli_affected_rows($db);
		
}
function ajouter_item_com ($skuachanger,$db) {
	//echo "ALLO2";
 			$sql = 'SELECT * FROM `oc_product` where sku = "'.$skuachanger.'" ';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req); 
			$product_id=$data['product_id'];
			$location=$data['location'];
			$quantity=$data['quantity'];
		
}
function update_to_ebay($connectionapi,$price,$updquantity,$ebay_id,$product_id) {
		$infoprice='';
		if($price>0)$infoprice= '<StartPrice> '.$price.' </StartPrice>';
		if($GLOBALS['NAME_CIE']=='PhoenixLiquidation'){
			if($price>4999)
				$updquantity=0;
		}
		if($GLOBALS['NAME_CIE']=='PhoenixSupplies'){
			$SKU='<SKU>COM_'.$product_id.'</SKU>';
		}else{
			$SKU='';
		}
		//echo $updquantity."allo".$ebay_id;
		$post = '<?xml version="1.0" encoding="utf-8"?>
				<ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">
					
					<ErrorLanguage>en_US</ErrorLanguage>
					<WarningLevel>High</WarningLevel>
					<InventoryStatus>
					<ItemID>'.$ebay_id.'</ItemID>
					'.$SKU.'
					<Quantity>'.$updquantity.'</Quantity>
					'.$infoprice.'
					</InventoryStatus>
				</ReviseInventoryStatusRequest>'; //	'.$infoprice.'
			//	//print("<pre>".print_r ($post,true )."</pre>");
		$headers = array(
					"X-EBAY-API-COMPATIBILITY-LEVEL: 1149",
					"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
					"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
					"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
					"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
					"X-EBAY-API-CALL-NAME: ReviseInventoryStatus",
					"X-EBAY-API-SITEID: 0" // 3 for UK
		);
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($connection);
		curl_close($connection);
		$xml = new SimpleXMLElement($response);
	//	//print("<pre>".print_r ($xml,true )."</pre>");
		//echo $response."allo"; 
	}
function add_ebay_item($connectionapi,$result,$post,$db){
	//print("<pre>".print_r ($post,true )."</pre>");
	//$result=get_from_upctemp($post['upc']);
	//$result_upctemp=json_decode($result, true);
/* 	if($post['image_principal']!=""){
		$image_principal=$post['image_principal'];
	}elseif($post['lien_a_cloner']!=""){
		$image_principal=$post['lien_a_cloner'];
	}else{ */
		$image_principal=$GLOBALS['WEBSITE']."image/data/tempo.jpg";
/* 	} */
			$imagexml='<PictureDetails> 
						<GalleryType>Gallery</GalleryType>
						<GalleryURL><![CDATA['.$image_principal.']]></GalleryURL>';
						$imagexml.='<PictureURL><![CDATA['.$image_principal.']]></PictureURL>';			
					$imagexml.='</PictureDetails>';
		//echo $imagexml;
/* 		 if(isset($post['lien_a_cloner'])&&$post['lien_a_cloner']!=""){
			//echo $post['ebay_id_a_cloner'];
			//echo "link_to_download";
			$imagexml=get_image_link_for_ebay($connectionapi,$post['lien_a_cloner'],"",$db);
		} */
	$json = json_decode($result, true);
	//print("<pre>".print_r ($json,true )."</pre>"); 
			if($post['model']=="")$post['model']="n/a";
			unset ($json['Timestamp'])	;
			unset ($json['Ack'])	;	
			unset ($json['Version'])	;
			unset ($json['Build'])	;	
			unset ($json['Item']['GiftIcon'])	;
			unset ($json['Item']['AutoPay'])	;
			unset ($json['Item']['BuyerProtection'])	;
			unset ($json['Item']['ApplicationData'])	;
			unset ($json['Item']['SubTitle'])	;
			unset ($json['Item']['ShipToLocations']);
			unset ($json['Item']['HideFromSearch']);
			unset ($json['Item']['ReasonHideFromSearch']);
			unset ($json['Item']['BuyerResponsibleForShipping']);
			unset ($json['Item']['ConditionID']);
			unset ($json['Item']['Storefront']);
			unset ($json['Item']['ConditionDisplayName']);
			unset ($json['Item']['InventoryTrackingMethod']);
			unset ($json['Item']['PaymentAllowedSite']);
			unset ($json['Item']['Description']);
			unset ($json['Item']['Title']);
			unset ($json['Item']['HitCounter'])	;
			unset ($json['Item']['ItemID'])	;
			unset ($json['Item']['ListingDesigner'])	;
			unset ($json['Item']['ListingDetails']);	
			unset ($json['Item']['BuyItNowPrice']);	
			unset ($json['Item']['ConditionDescription']);
			unset ($json['Item']['ProductListingDetails']['BrandMPN']['MPN']);
			$json['Item']['ListingDuration']="GTC";	
			$json['Item']['ListingType']="FixedPriceItem";	
			$json['Item']['Location']="Champlain, New York";	
			unset ($json['Item']['PrimaryCategory']['CategoryName']);
			unset ($json['Item']['PrivateListing']);
			if(isset($post['pas_upc']) && $post['pas_upc']=="does not apply"){
				$json['Item']['ProductListingDetails']['UPC']=$post['pas_upc'];
			}elseif(isset($post['upc'])){
				$json['Item']['ProductListingDetails']['UPC']=$post['upc'];
			}
			$json['Item']['ProductListingDetails']['IncludeeBayProductDetails']="true";
			unset ($json['Item']['UUID']);
			//unset($json['Item']['ProductListingDetails']['BrandMPN']);
			$json['Item']['BuyerResponsibleForShipping']="false";
/* 			if(!isset($json['Item']['ProductListingDetails']['BrandMPN']['MPN'])){
				if($result_upctemp['items'][0]['model']!=""){ */
					//$json['Item']['ProductListingDetails']['BrandMPN']['MPN']=$post['model'];
					$json['Item']['ProductListingDetails']['BrandMPN']['Brand']=$post['brand'];
					$json['Item']['ProductListingDetails']['BrandMPN']['MPN']=$post['model'];
					//$json['Item']['ProductListingDetails']['ProductIdentifierUnavailableText']=0;
		/* 		}else{
				}
			} */
			unset($json['Item']['PaymentMethods']);
			unset($json['Item']['PayPalEmailAddress']);
			/* $json['Item']['PaymentMethods']="PayPal";	
			$json['Item']['PayPalEmailAddress']="gervais.jonathan@phoenixsupplies.ca";	 */
			unset ($json['Item']['PrivateListing']);
			$json['Item']['Quantity'] = 1;
			$json['Item']['Country']="US";
			$json['Item']['Currency']="USD";
			unset ($json['Item']['ReviseStatus']);
			unset ($json['Item']['ReservePrice']);
			unset ($json['Item']['Seller']) ;
			unset ($json['Item']['SellingStatus']) ; 
			unset ($json['Item']['ShippingDetails']) ;
			//$json['Item']['ShippingDetails']=;
			$json['Item']['Site'] = "US";
            $json['Item']['StartPrice'] = $post['price_with_shipping'];
			unset ($json['Item']['Storefront']) ;
			unset ($json['Item']['OutOfStockControl']);
			unset ($json['Item']['Title']);
            $json['Item']['Storefront'] = Array
                (
                    'StoreCategoryID' => 1,
                    'StoreCategory2ID' => 0,
                    'StoreURL' => "https://www.ebay.com/str/phoenixdepotdotcom"
                );
			unset ($json['Item']['TimeLeft'])	;
			unset ($json['Item']['HitCount'])	;
			unset ($json['Item']['BestOfferDetails'])	;
			$json['Item']['BestOfferDetails']= Array
                (
                   // ['BestOfferCount'] => 0,
                    'BestOfferEnabled' => 'true'
                   // ['NewBestOffer'] => false
                );
			unset ($json['Item']['SKU']);
			$json['Item']['PostalCode']="12919";
			unset($json['Item']['DispatchTimeMax']);
			unset($json['Item']['ProxyItem']);
			unset($json['Item']['BuyerGuaranteePrice']);
			unset($json['Item']['BuyerRequirementDetails']);
			$json['Item']['Title']=addslashes(substr($post['name'],0,80));
			unset($json['Item']['IntangibleItem']);
			unset ($json['Item']['ReturnPolicy']);
			//unset ($json['Item']['ItemSpecifics']);
			if($post['category_id']=='177666'){
				$shippingname='ShippingVeRO';
				$shipping_id='242937796019';
			}elseif($post['category_id']=='117414'){
				$shippingname='Shipping_Calculated_Heavy';
				$shipping_id='251932372019';
				$post['price_with_shipping'] = $post['price'];
			}else{
				$shippingname='Shipping';
				$shipping_id='244970865019';
			}
			if($post['category_id']=='212'){
				$returnname='No_Return';
				$return_id='246806570019';
			}elseif($post['category_id']=='117414'){
				$returnname='Return_Buyer_Pay';
				$return_id='233511458019';
			}else{
				$returnname='Return';
				$return_id='244801165019';
			}
			//shipping calculer par ebay
			$json['Item']['SellerProfiles'] = Array
                (
                    'SellerShippingProfile' => Array
                        (
                            'ShippingProfileID' => $shipping_id,
                            'ShippingProfileName' => $shippingname
                        ),
                    'SellerReturnProfile' => Array
                        (
                            'ReturnProfileID' => $return_id,
                            'ReturnProfileName' => $returnname
                        ) ,
                    'SellerPaymentProfile' => Array
                        (
                            'PaymentProfileID' => '135483622019',
                            'PaymentProfileName' => 'PayPal'
                        ) 
                )
			;
			unset ($json['Item']['TopRatedListing'])	;
			unset ($json['Item']['LocationDefaulted'])	;
			unset ($json['Item']['GetItFast'])	;
			unset ($json['Item']['eBayPlus'])	;
			unset ($json['Item']['eBayPlusEligible'])	;
			unset ($json['Item']['IsSecureDescription'])	;
			unset ($json['Item']['ProxyItem'])	;
			unset ($json['Item']['BuyerGuaranteePrice'])	;
			unset ($json['Item']['IntangibleItem'])	;
			unset ($json['Item']['RestrictionPerBuyer'])	;
			unset ($json['Item']['ShippingServiceCostOverrideList'])	;
			unset ($json['Item']['DiscountPriceInfo'])	;
			unset ($json['Item']['ConditionDisplayName'])	;
			unset ($json['Item']['QuantityAvailableHint'])	;
			unset ($json['Item']['QuantityThreshold'])	;
			unset ($json['Item']['PostCheckoutExperienceEnabled'])	;
			unset ($json['Item']['ShippingPackageDetails'])	;
			$json['Item']['ShippingPackageDetails']['WeightMajor']=0;
			$json['Item']['ShippingPackageDetails']['WeightMinor']=4;
			unset ($json['Item']['HideFromSearch'])	;
			unset ($json['Item']['ListingDetails'])	;
			unset ($json['Item']['ReservePrice'])	;
			unset ($json['Item']['Charity'])	;
			unset ($json['Item']['PictureDetails']);
				$json['Item']['Description']="Not finish yet to be list DO NOT buy it!!!"	;
				
				$sql2 = 'SELECT C.conditions FROM `oc_condition_ebay_to_category` CC LEFT JOIN `oc_condition_ebay` AS C ON CC.condition_ebay_id=C.condition_ebay_id where CC.category_id= '.$post['category_id'];
			//	echo "<br>".$sql2;
				$req2 = mysqli_query($db,$sql2);
				$data2 = mysqli_fetch_assoc($req2);
             /*   if(count($data2)==0 && $GLOBALS['NAME_CIE']=='PhoenixSupplies'){
					$sql3 = "INSERT INTO `oc_condition_ebay_to_category` (`category_id`, `condition_ebay_id`) VALUES ('".$post['category_id']."', '3')";
					mysqli_query($db,$sql3);
					$data2['conditions']=3;
                }*/
				//echo $post['conditions'];
$sql2 = 'SELECT C.conditions FROM `oc_condition_ebay_to_category` CC LEFT JOIN `oc_condition_ebay` AS C ON CC.condition_ebay_id=C.condition_ebay_id where CC.category_id= '.$post['category_id'];
				//echo "<br>".$sql2;
				$req2 = mysqli_query($db,$sql2);
				$data2 = mysqli_fetch_assoc($req2);
				$conditions = json_decode($data2['conditions'], true);
				//print("<pre>".print_r ($post,true )."</pre>");
			//	$etat=explode(",",$conditions['etat']);
				$json['Item']['ConditionID']=$conditions[$post['condition_id']]['value'];
				unset ($json['Item']['PrimaryCategory']['CategoryID']);
				$json['Item']['PrimaryCategory']['CategoryID']=$post['category_id'];
				$json['Item']['Title']="Temporary listing name ".time();
							/* } */
			//$json['Item']['PictureDetails']['GalleryURL'] =$GLOBALS['WEBSITE']."image/data/tempo.jpg";
			//$json['Item']['PictureDetails']['PictureURL'] =$GLOBALS['WEBSITE']."image/data/tempo.jpg";
			unset ($json['Item']['BuyerRequirementDetails'])	;
			unset ($json['Item']['Variations'])	;
			//si juste 1 dans les item specific
			//echo "itemspeci:".$json['Item']['ItemSpecifics']['NameValueList']['Name'];
			if(isset($json['Item']['ItemSpecifics']['NameValueList']['Name'])){
				$temp=$json['Item']['ItemSpecifics']['NameValueList'];
				unset ($json['Item']['ItemSpecifics']['NameValueList']);
				$json['Item']['ItemSpecifics']['NameValueList'][]=$temp;
			}
			$json['Item']['UPC']=$post['upc'];
			//films
		//	if($post['category_id']=='617'){ //category_id=139973 or pc.category_id=617
		//		$json['Item']['ItemSpecifics']['NameValueList']=get_movie_item_specific($post['name'],$post['brand'],$post['upc']);	
		//	}else{
				$NameValueLists=getCategorySpecifics($connectionapi,$post,$db);
			//	//print("<pre>".print_r ($NameValueLists,true )."</pre>");
		//	}
			$sql = "UPDATE `oc_product` SET `product_specifics` = '" . addslashes(json_encode($NameValueLists,true)). "' WHERE `product_id` = '" . $post['product_id'] . "'";
			mysqli_query($db, $sql);
			$json['Item']['ItemSpecifics']['NameValueList'][]=  Array 
								(
									'Name' => 'Model',
									'Value' => $post['model']
								);
		//	//print("<pre>".print_r ($post,true )."</pre>");
			if($post['coloren']!=""){
			//	//print("<pre>".print_r ($post,true )."</pre>");
				$json['Item']['ItemSpecifics']['NameValueList'][]=  Array 
								(
									'Name' => 'Color',
									'Value' => $post['coloren']
								);
			}
			$json['Item']['ItemSpecifics']['NameValueList'][]=  Array 
								(
									'Name' => 'MPN',
									'Value' => $post['model']
								);
			//print("<pre>".print_r ($json['Item'],true )."</pre>");
			$xml=array2xml($json['Item'], false);
			/**/
			//print_r($xml);
			//print("<pre>".print_r ($xml,true )."</pre>");
			$post = '<?xml version="1.0" encoding="utf-8"?>
			<AddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				
				 ';
			$xml=str_replace('<?xml version="1.0"?>',$post,$xml);
			$xml=str_replace('<StartPrice','<StartPrice currencyID="USD"',$xml);
			$xml=str_replace('</Item>',"",$xml);
				 $post =$xml.$imagexml.'</Item>
				<ErrorLanguage>en_US</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
			</AddItemRequest >';
		//	$post = escape_special_chars($post);
// unlink($GLOBALS['SITE_ROOT'].'interne/test/AddItemRequest.txt');
//link($GLOBALS['SITE_ROOT'].'interne/test/AddItemRequest.txt',$GLOBALS['SITE_ROOT'].'interne/test/AddItemRequest.txt');
//$fp = fopen($GLOBALS['SITE_ROOT'].'interne/test/AddItemRequest.txt', 'w');
//fwrite($fp, $post); 
			$headers = array(
						"X-EBAY-API-COMPATIBILITY-LEVEL: 1157",
						"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
						"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
						"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
						"X-EBAY-API-CALL-NAME: AddItem",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			//$result = json_encode($post); 
			//echo "<br>post2481:";
		//	//print("<pre>".print_r ($post,true )."</pre>");
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$result2 = curl_exec($connection);
			$err = curl_error($connection);
			curl_close($connection);
			if ($err) {
				//echo "cURL Error #:" . $err;
			} else {
				// Convert xml string into an object  
				//echo $result."\nallo";
				$new = simplexml_load_string($result2);  
				// Convert into json 
				$result2 = json_encode($new); 
				$textoutput=str_replace("}","<br><==<br>",$result2);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				//echo $textoutput."\nallo"."<br>";
				$json = json_decode($result2, true);
			//print("<pre>".print_r ($json,true )."</pre>");
				//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
				//$ebay_quantity=$json["Item"]["Quantity"];
				//$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
			//	$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
				//echo $ebay_quantity."---".$Quantity_sold; 
				//$encodedSesssionIDString =rawurlencode ($sessionId);
				//echo $encodedSesssionIDString;
			}
			if($json["Ack"]=="Failure"){
				return $json;//array_merge($json,json_decode($result, true));
			}else{
				return $json;
			} 
} 
function get_movie_item_specific($title,$studio,$upc){
	$namevaluelist=array();
	$title_exp=array();
//	$title=$post['name'];

//	$title_exp= explode('(',$title);

	// Vérifier la présence de '(' ou '[' dans le titre
	$pos_parenthesis = strpos($title, '(');
	$pos_bracket = strpos($title, '[');

	// Si '(' est trouvé avant '[', ou si '(' est trouvé mais pas '['
	if ($pos_parenthesis !== false && ($pos_bracket === false || $pos_parenthesis < $pos_bracket)) {
		// Extraire la partie avant '('
		$title_exp = explode('(', $title);
	//	$title = trim($title_exp[0]);
	}
	// Si '[' est trouvé avant '(', ou si '[' est trouvé mais pas '('
	elseif ($pos_bracket !== false && ($pos_parenthesis === false || $pos_bracket < $pos_parenthesis)) {
		// Extraire la partie avant '['
		$title_exp = explode('[', $title);
	//	$title = trim($title_exp[0]);
	} else {
		// Si aucun des deux n'est trouvé, prendre les 80 premiers caractères
		$title_exp[0] = substr($title, 0, 65);
		 
	}
	$title= strtolower($title);
	  // Check for release year in the title
	  $release_year = '';
	  if (preg_match('/\b(19|20)\d{2}\b/', $title, $matches)) {
		  $release_year = $matches[0];
	  }
  
	  if (!empty($release_year)) {
		  $namevaluelist['Release Year'] = array(
			  'Name' => 'Release Year',
			  'Value' => $release_year
		  );
	  }
	
	  $movie_details = fetch_movie_details ($title.' ('.$release_year.')');
	//print("<pre>".print_r ($movie_details,true )."</pre>");
	if (is_array($movie_details)) {
		foreach ($movie_details as $key => $value) {
			$key_search=strtolower($key);
			if (strpos($key_search, 'studio') !== false) {
				$key = 'Studio';
			}
			if (strpos($key_search, 'production') !== false) {
				$key = 'Producer';
			}
			if (strpos($key_search, 'duration') !== false) {
				$key = 'Run Time';
			}
			if (strpos($key_search, 'sub') !== false) {
				$key = 'Sub-Genre';
			}
			if (strpos($key_search, 'cast') !== false) {
				$key = 'Actor';
			}
			if (strpos($key_search, 'actor') !== false) {
				$key = 'Actor';
			}
			if (strpos($key_search, 'music') !== false) {
				$key = 'Music Artist';
			}
			if (strpos($key_search, 'country') !== false) {
				$key = 'Country/Region of Manufacture';
			}
			if (strpos($key_search, 'release') !== false) {
				$key = 'Release Year';
			}
			if (strpos($key_search, 'year') !== false) {
				$key = 'Release Year';
			}
			if (strpos($key_search, 'screen') !== false) {
				$key = 'Screen Format';
			}
			if (strpos($key_search, 'title') !== false) {
				$key = 'Movie/TV Title';
			}
			
			if (!is_array($value) && $key != 'Studio'  && $key != 'Country/Region of Manufacture') {
				$value_ck=strtolower($value);
				if($value_ck=='n/a' || (strpos($value, 'http') !== false)){
					$value="";
				}
				if (strpos($value, ',') !== false) {
					$value = explode(',', $value);
				} elseif ((strpos($value, '/') !== false) && strtolower($value)!='n/a') {
					$value = explode('/', $value);
				}
				
			}
			
			$namevaluelist[$key] = array(
				'Name' => ucwords(str_replace('_', ' ', $key)),
				'Value' => $value
			);
		}
	}

	//echo 2425;//print("<pre>".print_r ($namevaluelist,true )."</pre>");
	$namevaluelist['Movie/TV Title']=  Array 
		(
			'Name' => 'Movie/TV Title',
			'Value' => ucwords($title_exp[0])
		);
	$namevaluelist['UPC']=  Array 
		(
			'Name' => 'UPC',
			'Value' => $upc
		);	
	if(!isset($namevaluelist['Studio']))
		$namevaluelist['Studio']=  Array 
			(
				'Name' => 'Studio',
				'Value' => $studio
			);
					// Normalize the title by removing spaces and special characters
	$normalized_title = preg_replace('/[^a-z0-9]/', '', $title);
		  // Check for season number in the title
		  $season_number = '';
		  if (preg_match('/season\s+(\d+|one|two|three|four|five|six|seven|eight|nine|ten|first|second|third|fourth|fifth|sixth|seventh|eighth|ninth|tenth)/i', $title, $matches)) {
			  $season_number = $matches[1];
			  
			  // Convert text numbers to digits
			  $numbers = array(
				  'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				  'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9, 'ten' => 10,
				  'first' => 1, 'second' => 2, 'third' => 3, 'fourth' => 4, 'fifth' => 5,
				  'sixth' => 6, 'seventh' => 7, 'eighth' => 8, 'ninth' => 9, 'tenth' => 10
			  );
			  if (isset($numbers[$season_number])) {
				  $season_number = $numbers[$season_number];
			  }
		  }
  
		  if (!empty($season_number)) {
			if(!isset($namevaluelist['Season']))
			  $namevaluelist['Season'] = Array(
				  'Name' => 'Season',
				  'Value' => $season_number
			  );
		  }
			// Check for edition in the title
			$edition = '';
			if (strpos($normalized_title, 'standard') !== false) {
				$edition = 'Standard Edition';
			} elseif (strpos($normalized_title, 'special') !== false) {
				$edition = 'Special Edition';
			} elseif (strpos($normalized_title, 'collector') !== false) {
				$edition = 'Collector\'s Edition';
			} else{
			  $edition = 'Standard Edition'; 
			}
		
			if (!empty($edition)) {
				if(!isset($namevaluelist['Edition']))
				$namevaluelist['Edition'] = Array(
					'Name' => 'Edition',
					'Value' => $edition
				);
			}
	// Check for widescreen or fullscreen in the normalized title
	$aspect_ratio = '';
	$features = '';
	if (strpos($normalized_title, 'widescreen') !== false || strpos($normalized_title, '169')  !== false || strpos($normalized_title, 'ws') !== false) {
		$aspect_ratio = '16:9';
		$features = 'Widescreen';
	} elseif (strpos($normalized_title, 'fullscreen') !== false || strpos($normalized_title, '43') !== false) {
		$aspect_ratio = '4:3';
		$features = 'Full Screen';
	}

	if (!empty($aspect_ratio)) {
		$namevaluelist['Aspect Ratio'] = Array(
			'Name' => 'Aspect Ratio',
			'Value' => $aspect_ratio
		);
	}
	if (!empty($features)) {
		$namevaluelist['Features'] = Array(
			'Name' => 'Features',
			'Value' => $features
		);
	}
	
		// Check for DVD, Blu-ray or 4K in the normalized title
		$format = '';
		$region = '';
		if (strpos($normalized_title, '4k') !== false) {
			$format = '4K UHD Blu-ray';
			$region = 'Blu-ray: Region Free';
		}elseif (strpos($normalized_title, 'bluray') !== false) {
			$format = 'Blu-ray';
			$region = 'Blu-ray: Region Free';
		}elseif (strpos($normalized_title, 'dvd') !== false) {
			$format = 'DVD';
			$region = 'DVD: 1 (US, Canada...)';
		}else{
			$format = 'DVD';
			$region = 'DVD: 1 (US, Canada...)';
		}
	
		if (!empty($region)) {
			$namevaluelist['Region Code'] = Array(
				'Name' => 'Region Code',
				'Value' => $region
			);
		}
		if (!empty($format)) {
			$namevaluelist['Format'] = Array(
				'Name' => 'Format',
				'Value' => $format
			);
		}
		 // Check for movie or series in the normalized title
		$type = '';
		if (strpos($normalized_title, 'movie') !== false) {
			$type = 'Movie';
		} elseif (strpos($normalized_title, 'series') !== false) {
			$type = 'Series';
		} else{
			$type = 'Movie';
		}

		if (!empty($type)) {
			$namevaluelist['Type'] = Array(
				'Name' => 'Type',
				'Value' => $type
			);
		}

		$namevaluelist['Language'] = Array(
			'Name' => 'Language',
			'Value' => 'English'
		);
		$namevaluelist['Subtitle Language'] = Array(
			'Name' => 'Subtitle Language',
			'Value' => 'English'
		);
		if(!isset($namevaluelist['Country/Region of Manufacture']))
		$namevaluelist['Country/Region of Manufacture'] = Array(
			'Name' => 'Country/Region of Manufacture',
			'Value' => 'United State'
		);
		$namevaluelist['Video Format'] = Array(
			'Name' => 'Video Format',
			'Value' => 'NTSC'
		);
		$namevaluelist['Case Type'] = Array(
			'Name' => 'Case Type',
			'Value' => 'Tall/DVD Case'
		);
		$namevaluelist['Unit Quantity'] = Array(
			'Name' => 'Unit Quantity',
			'Value' => '1'
		);
		$namevaluelist['Unit Type'] = Array(
			'Name' => 'Unit Type',
			'Value' => 'Unit'
		);
		//				echo"2580";//print("<pre>".print_r ($namevaluelist,true )."</pre>");

		return $namevaluelist;
}


function add_etsy_item_old($connectionapi,$result,$post,$db){		
	$image_principal=$GLOBALS['WEBSITE']."image/data/tempo.jpg";
			$imagexml='<PictureDetails> 
						<GalleryType>Gallery</GalleryType>
						<GalleryURL><![CDATA['.$image_principal.']]></GalleryURL>';
						$imagexml.='<PictureURL><![CDATA['.$image_principal.']]></PictureURL>';			
					$imagexml.='</PictureDetails>';
					$json = json_decode($result, true);
			if($post['model']=="")
				$post['model']="n/a";
			$json['Item']['ListingDuration']="GTC";	
			$json['Item']['ListingType']="FixedPriceItem";	
			$json['Item']['Location']="Champlain, New York";	
			if($post['pas_upc']=="does not apply"){
				$json['Item']['ProductListingDetails']['UPC']=$post['pas_upc'];
			}else{
				$json['Item']['ProductListingDetails']['UPC']=$post['upc'];
			}
			$json['Item']['ProductListingDetails']['IncludeeBayProductDetails']="true";
			$json['Item']['BuyerResponsibleForShipping']="false";
			$json['Item']['ProductListingDetails']['BrandMPN']['Brand']=$post['brand'];
			$json['Item']['ProductListingDetails']['BrandMPN']['MPN']=$post['model'];
			$json['Item']['Quantity'] = 1;
			$json['Item']['Country']="US";
			$json['Item']['Currency']="USD";
			$json['Item']['Site'] = "US";
            $json['Item']['StartPrice'] = $post['price_with_shipping'];
            $json['Item']['Storefront'] = Array
                (
                    'StoreCategoryID' => 1,
                    'StoreCategory2ID' => 0,
                    'StoreURL' => "https://www.ebay.com/str/phoenixdepotdotcom"
                );
			$json['Item']['BestOfferDetails']= Array
                (
                    'BestOfferEnabled' => 'true'
                );
			$json['Item']['PostalCode']="12919";
			if($post['category_id']=='177666'){
				$shippingname='ShippingVeRO';
				$shipping_id='242937796019';
			}else{
				$shippingname='Shipping';
				$shipping_id='244970865019';
			}
			$json['Item']['SellerProfiles'] = Array
                (
                    'SellerShippingProfile' => Array
                        (
                            'ShippingProfileID' => $shipping_id,
                            'ShippingProfileName' => 'Shipping'
                        ),
                    'SellerReturnProfile' => Array
                        (
                            'ReturnProfileID' => '244801165019',
                            'ReturnProfileName' => 'Return'
                        ) ,
                    'SellerPaymentProfile' => Array
                        (
                            'PaymentProfileID' => '135483622019',
                            'PaymentProfileName' => 'PayPal'
                        ) 
                )
			;
			$json['Item']['ShippingPackageDetails']['WeightMajor']=0;
			$json['Item']['ShippingPackageDetails']['WeightMinor']=4;
				$json['Item']['Description']="Not finish yet to be list DO NOT buy it!!!"	;
			
				//$req2 = mysqli_query($db,$sql2);
				//$data2 = mysqli_fetch_assoc($req2);
                if( $GLOBALS['NAME_CIE']=='PhoenixSupplies'){//count($data2)==0 &&
					$sql3 = "INSERT INTO `oc_condition_ebay_to_category` (`category_id`, `condition_ebay_id`) VALUES ('".$post['category_id']."', '3')";
					$req3 = mysqli_query($db,$sql3);
					$data2['conditions']=3;
                }
				//echo $post['conditions'];
				$conditions = json_decode($data2['conditions'], true);
				//print("<pre>".print_r ($conditions,true )."</pre>");
				$etat=explode(",",$conditions['etat']);
				$json['Item']['ConditionID']=$conditions[$post['condition_id']]['value'];
				$json['Item']['Title']="Temporary listing name ".time();
			$xml=array2xml($json['Item'], false);
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.etsy.com/v3/application/shops/phoenixdepotdotcom/listings',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => 'quantity=5&title=Vintage%20Duncan%20Toys%20Butterfly%20Yo-Yo%2C%20Red&description=Vintage%20Duncan%20Yo-Yo%20from%201976%20with%20string%2C%20steel%20axle%2C%20and%20plastic%20body.&price=1000&who_made=someone_else&when_made=1970s&taxonomy_id=1&image_ids=378848%2C238298%2C030076',			
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/x-www-form-urlencoded',
					'x-api-key: blzucicwp76i6css25t2rrsx',
					'Authorization: Bearer phoenixdepotdotcom.jKBPLnOiYt7vpWlsny_lDKqINn4Ny_jwH89hA4IZgggyzqmV_bmQHGJ3HOHH2DmZxOJn5V1qQFnVP9bCn9jnrggCRz'
				),
			));
			$response = curl_exec($curl);
			curl_close($curl);
			//echo $response;
 //unlink($GLOBALS['SITE_ROOT'].'interne/test/AddItemRequest.txt');
//link($GLOBALS['SITE_ROOT'].'interne/test/AddItemRequest.txt',$GLOBALS['SITE_ROOT'].'interne/test/AddItemRequest.txt');
//$fp = fopen($GLOBALS['SITE_ROOT'].'interne/test/AddItemRequest.txt', 'w');
//fwrite($fp, $post); 
			$headers = array(
						"X-EBAY-API-COMPATIBILITY-LEVEL: 1157",
						"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
						"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
						"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
						"X-EBAY-API-CALL-NAME: AddItem",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			//$result = json_encode($post); 
			//print("<pre>".print_r ($post,true )."</pre>");
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$result2 = curl_exec($connection);
			$err = curl_error($connection);
			curl_close($connection);
			if ($err) {
				//echo "cURL Error #:" . $err;
			} else {
				// Convert xml string into an object  
				//echo $result."\nallo";
				$new = simplexml_load_string($result2);  
				// Convert into json 
				$result2 = json_encode($new); 
				$textoutput=str_replace("}","<br><==<br>",$result2);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				//echo $textoutput."\nallo"."<br>";
				$json = json_decode($result2, true);
			//print("<pre>".print_r ($json,true )."</pre>");
				//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
				$ebay_quantity=$json["Item"]["Quantity"];
				$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
				$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
				//echo $ebay_quantity."---".$Quantity_sold; 
				//$encodedSesssionIDString =rawurlencode ($sessionId);
				//echo $encodedSesssionIDString;
			}
			if($json["Ack"]=="Failure"){
				return $json;//array_merge($json,json_decode($result, true));
			}else{
				return $json;
			} 
} 
function add_ebay_our_item($connectionapi,$result,$db){
	//print("<pre>".print_r ($post,true )."</pre>");
	//$result=get_from_upctemp($post['upc']);
	//$result_upctemp=json_decode($result, true);
/* 	if($post['image_principal']!=""){
		$image_principal=$post['image_principal'];
	}elseif($post['lien_a_cloner']!=""){
		$image_principal=$post['lien_a_cloner'];
	}else{ */
		$image_principal=$GLOBALS['WEBSITE']."/image/data/tempo.jpg";
/* 	} */
			$imagexml='<PictureDetails> 
						<GalleryType>Gallery</GalleryType>
						<GalleryURL><![CDATA['.$image_principal.']]></GalleryURL>';
						$imagexml.='<PictureURL><![CDATA['.$image_principal.']]></PictureURL>';			
					$imagexml.='</PictureDetails>';
		//echo $imagexml;
/* 		 if(isset($post['lien_a_cloner'])&&$post['lien_a_cloner']!=""){
			//echo $post['ebay_id_a_cloner'];
			//echo "link_to_download";
			$imagexml=get_image_link_for_ebay($connectionapi,$post['lien_a_cloner'],"",$db);
		} */
	$json = json_decode($result, true);
	unset($json['Item']['PictureDetails']);
			//$json['Item']['PictureDetails']['GalleryURL'] =$GLOBALS['WEBSITE']."image/data/tempo.jpg";
		//	$json['Item']['PictureDetails']['PictureURL'] =$GLOBALS['WEBSITE']."image/data/tempo.jpg";
			$post = '<?xml version="1.0" encoding="utf-8"?>
			<AddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				
				 ';
			$xml="";
			$xml=str_replace('<?xml version="1.0"?>',$post,$xml);
			$xml=str_replace('<StartPrice','<StartPrice currencyID="USD"',$xml);
			$xml=str_replace('</Item>',"",$xml);
				 $post =$xml.$imagexml.'</Item>
				<ErrorLanguage>en_US</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
			</AddItemRequest >';
// unlink($GLOBALS['SITE_ROOT'].'interne/test/AddItemRequest.txt');
//link($GLOBALS['SITE_ROOT'].'interne/test/AddItemRequest.txt','AddItemRequest.txt');
//$fp = fopen($GLOBALS['SITE_ROOT'].'interne/test/AddItemRequest.txt', 'w');
//fwrite($fp, $post); 
			//$post = escape_special_chars($post);
			$headers = array(
						"X-EBAY-API-COMPATIBILITY-LEVEL: 1157",
						"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
						"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
						"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
						"X-EBAY-API-CALL-NAME: AddItem",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			//$result = json_encode($post); 
			//print("<pre>".print_r ($post,true )."</pre>");
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$result2 = curl_exec($connection);
			$err = curl_error($connection);
			curl_close($connection);
			if ($err) {
				//echo "cURL Error #:" . $err;
			} else {
				// Convert xml string into an object  

				$new = simplexml_load_string($result2);  
				// Convert into json 
				$result2 = json_encode($new); 
				$textoutput=str_replace("}","<br><==<br>",$result2);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);

				$json = json_decode($result2, true);
			//print("<pre>".print_r ($json,true )."</pre>");

				$ebay_quantity=$json["Item"]["Quantity"];
				$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
				$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
				//echo $ebay_quantity."---".$Quantity_sold; 
				//$encodedSesssionIDString =rawurlencode ($sessionId);
				//echo $encodedSesssionIDString;
			}
			if($json["Ack"]=="Failure"){
				return $json;//array_merge($json,json_decode($result, true));
			}else{
				return $json;
			} 
} 	
function getEbayAccessToken($clientId, $clientSecret) {
    $url = 'https://api.ebay.com/identity/v1/oauth2/token';
    $headers = [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret),
    ];
    $body = http_build_query([
        'grant_type' => 'client_credentials',
        'scope' => 'https://api.ebay.com/oauth/api_scope'
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    }
    

    $responseData = json_decode($response, true);
//	//print("<pre>".print_r ($responseData,true )."</pre>");
    if (isset($responseData['access_token'])) {
        return $responseData['access_token'];
    } else {
        return json_encode(['error' => 'Error obtaining access token', 'response' => $response]);
    }
}
function escape_special_chars($xml_string) {
    $replacements = array(
        '&' => '&amp;',
    
    );

    return str_replace(array_keys($replacements), array_values($replacements), $xml_string);
}

function getBestAspectValueFromChatGPT($product, $aspectName, $aspectValues, $aspectConstraint) {
	$title = $product['name_description'];
	$brand = $product['brand'];
	$category_id = $product['category_id'];
	$model = $product['model'];
	$color = $product['color'];
	$condition_supp = $product['condition_supp'];
	$condition_name = $product['condition_name'];
	$upc = $product['upc'];
	$normalized_title = preg_replace('/[^a-z0-9]/', '', $title);
	$apiEndpoint = 'https://api.openai.com/v1/chat/completions';

	$apiKey = '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';

	if ($category_id == '617') {
		$messageContent = "Title: \"$title\" \"$condition_name\" \"$condition_supp\". Movie or TV title. Provide the best result without repeating the question.";
	} else {
		$messageContent = "Product title: \"$title\" \"$condition_name\" \"$color\" \"$condition_supp\" and brand: \"$brand\" and model: \"$model\".";
	}
	//$messageContent .= " UPC if it could help but not based on it: \"$upc\".";
	if (!empty($aspectValues)) {
		$values = implode(', ', array_map(function($val) { return $val['localizedValue']; }, $aspectValues));
		$messageContent .= " Choose the most accurate and concise value for \"$aspectName\" from the following options: $values. Reply with the exact values only.";
	} else {
		$messageContent .= " Provide the most accurate and concise values for \"$aspectName\". Reply with 'none' if not applicable.";
	}

	if (isset($aspectConstraint['aspectDataType'])) {
		if ($aspectConstraint['aspectDataType'] === 'STRING' && isset($aspectConstraint['aspectMaxLength'])) {
			$messageContent .= " The response should be a string with a maximum length of {$aspectConstraint['aspectMaxLength']} characters.";
		} elseif ($aspectConstraint['aspectDataType'] === 'NUMBER') {
			$messageContent .= " The response should be a number.";
		}
	}

	if (isset($aspectConstraint['itemToAspectCardinality'])) {
		if ($aspectConstraint['itemToAspectCardinality'] === 'MULTI') {
			$messageContent .= ' Multiple values can be provided in between ","';
		} else {
			$messageContent .= " Only a single value is allowed.";
		}
	}

	$maxTokens = 16385 - 100;
	$currentTokens = ceil(strlen($messageContent) / 3);

	if ($currentTokens > $maxTokens) {
		$messageContent = substr($messageContent, 0, $maxTokens * 3);
	}
	$data = [
		'model' => 'gpt-3.5-turbo',
		'messages' => [
			['role' => 'system', 'content' => 'Provide concise and accurate responses.'],
			['role' => 'user', 'content' => $messageContent]
		],
		'max_tokens' => 50,
		'temperature' => 0.3
	];

	$headers = [
		'Content-Type: application/json',
		'Authorization: Bearer ' . $apiKey,
	];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	if (curl_errno($ch)) {
		return json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
	}
	

	$responseData = json_decode($response, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		return json_encode(['error' => 'Error parsing JSON response', 'response' => $response]);
	}

	if (isset($responseData['choices'][0]['message']['content'])) {
		$suggestion = trim($responseData['choices'][0]['message']['content']);

		$aspectNamelower = strtolower($aspectName);
		$unwantedPhrases = [
			'As an AI', 'not provided', 'not applicable', 
			'Check the product packaging', 'specific model numbers for products', 
			'access to real-time databases', 'The most accurate and concise value for',
			"$aspectNamelower"
		];
		foreach ($unwantedPhrases as $phrase) {
			if (stripos($suggestion, $phrase) !== false) {
				$suggestion = 'none';
			}
		}
		$unwantedPhrases = ["$aspectName: "];
		foreach ($unwantedPhrases as $phrase) {
			if (stripos($suggestion, $phrase) !== false) {
				$suggestion = str_replace($phrase, '', $suggestion);
			}
		}

		$suggestion = trim($suggestion);

		if ($aspectConstraint['itemToAspectCardinality'] === 'MULTI') {
			$suggestionArray = json_decode($suggestion, true);
			
		//	//print("<pre>".print_r ($suggestionArray,true )."</pre>");
			if (json_last_error() === JSON_ERROR_NONE) {
				
				if ($category_id == '617') {
					//print("<pre>".print_r ($aspectName,true )."</pre>");
					if ($aspectName === 'Edition') {
							if (strpos($normalized_title, 'standard') !== false) {
								$suggestionArray[] = 'Standard Edition';
							} elseif (strpos($normalized_title, 'special') !== false) {
								$suggestionArray[] = 'Special Edition';
							} elseif (strpos($normalized_title, 'collector') !== false) {
								$suggestionArray[] = 'Collector\'s Edition';
							} else{
							$suggestionArray[] = 'Standard Edition'; 
							}
					}
				}
				$suggestion= $suggestionArray;
			}
		}

		if (!empty($aspectValues)) {
			$allowedValues = array_map(function($val) { return $val['localizedValue']; }, $aspectValues);
			if (!in_array($suggestion, $allowedValues)) {
				//return $suggestion;
			}
		}

		if (isset($aspectConstraint['aspectMaxLength']) && strlen($suggestion) > $aspectConstraint['aspectMaxLength']) {
			$suggestion = substr($suggestion, 0, $aspectConstraint['aspectMaxLength']);
		}

		if (isset($aspectConstraint['aspectDataType']) && $aspectConstraint['aspectDataType'] === 'NUMBER' && !is_numeric($suggestion)) {
			return 'none';
		}

		// Ensure "English" is included for specific aspects and category
		if ($category_id == '617') {
			if ($aspectName === 'Language' || $aspectName === 'Subtitle Language') {
				if (stripos($suggestion, 'English') === false) {
					$suggestion = 'English, ' . $suggestion;
				}
			}
			if ($aspectName === 'Case Type') {
				$suggestion = 'Tall/DVD Case or Blu-ray Case';
				$suggestion = checkGPT($product, $aspectName, $suggestion);
			}
			if ($aspectName === 'Edition') {
				if (strpos($normalized_title, 'standard') !== false) {
					$suggestion = 'Standard Edition';
				} elseif (strpos($normalized_title, 'special') !== false) {
					$suggestion = 'Special Edition';
				} elseif (strpos($normalized_title, 'collector') !== false) {
					$suggestion = 'Collector\'s Edition';
				} else{
				$suggestion = 'Standard Edition'; 
				}
		}
		}
		return $suggestion;
	/*	if ($suggestion != 'none') {
			
			
			if (isset($suggestionArray)) {
				$suggestionArray = checkGPT($product, $aspectName, $suggestionArray);
				$suggestionArray = explode(',', $suggestionArray);
				
				if (is_array($suggestionArray)) {
					return $suggestionArray;
				} else {
					return $suggestion;
				}
			} else {
				return checkGPT($product, $aspectName, $suggestion);
			}
		} else {
			return 'none';
		}*/
	}
}
function checkGPT($product, $aspectName, $suggestion) {
    $title = $product['name_description'];
    $brand = $product['brand'];
    $category_id = $product['category_id'];
    $model = $product['model'];
    $color = $product['color'];
    $condition_supp = $product['condition_supp'];
    $condition_name = $product['condition_name'];
    $upc = $product['upc'];
    $apiEndpoint = 'https://api.openai.com/v1/chat/completions';

    $apiKey = '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';

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ];

    if ($suggestion == 'Tall/DVD Case or Blu-ray Case') {
      //  $validationMessageContent = "Verify if the following value for \"$aspectName\" = \"$suggestion\". Return 'Tall/DVD Case' if it is a DVD or 'Blu-ray Case' if it is a Blu-Ray. Only one value allowed.";
		//$validationMessageContent = "for \"$title\" If the \"$aspectName\" is \"Tall/DVD Case or Blu-ray Case\", return 'Tall/DVD Case' if it is a DVD or 'Blu-ray Case' if it is a Blu-Ray. Only one value allowed.";
		//$validationMessageContent = "Based on the product title \"$title\", determine if the \"$aspectName\" should be 'Tall/DVD Case' or 'Blu-ray Case'. Return 'Tall/DVD Case' if it is a DVD, or 'Blu-ray Case' if it is a Blu-Ray. Only one value allowed.";
     //   $validationMessageContent = "Based on the product title \"$title\", determine if the \"$aspectName\" should be 'Tall/DVD Case' or 'Blu-ray Case'. Return 'Tall/DVD Case' if it is a DVD, or 'Blu-ray Case' if it is a Blu-Ray. Only one value allowed.";
		 $validationMessageContent = "Based on the product title \"$title\", determine if the case type should be 'Tall/DVD Case' or 'Blu-ray Case'. Return 'Tall/DVD Case' if it is a DVD, or 'Blu-ray Case' if it is a Blu-Ray. Only one value allowed.";


    } else {
      //  $validationMessageContent = "Verify if the following value for \"$aspectName\" = \"$suggestion\" is accurate for \"$title\". Reply ONLY with \"$suggestion\" if it is accurate, if NOT return your accurate suggested value. If more than one, put a comma between each value.";
	//	$validationMessageContent = "Verify if the following value for \"$aspectName\" = \"$suggestion\" is accurate for the product titled \"$title\". Reply ONLY with \"$suggestion\" if it is accurate. If NOT, return your accurate suggested value. If more than one, put a comma between each value.";
        $validationMessageContent = "Verify if the following value for \"$aspectName\" : \"$suggestion\" is accurate for the product titled \"$title\". If it is accurate, reply ONLY with \"$suggestion\". If NOT return your correct value without any additional text.";
        $validationMessageContent = "Verify if the following value for \"$aspectName\" : \"$suggestion\" is accurate for the product titled \"$title\". If it is accurate, reply ONLY with \"$suggestion\". or Provide the most accurate and concise values for \"$aspectName\" correct value,otherwise return none ";

	}

    $validationData = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'Provide concise and accurate responses.'],
            ['role' => 'user', 'content' => $validationMessageContent]
        ],
        'max_tokens' => 50,
        'temperature' => 0.3
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($validationData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $validationResponse = curl_exec($ch);
    if (curl_errno($ch)) {
        return json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    }
    

    $validationResponseData = json_decode($validationResponse, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return json_encode(['error' => 'Error parsing JSON response', 'response' => $validationResponse]);
    }

    if (isset($validationResponseData['choices'][0]['message']['content'])) {
	//	//print("<pre>".print_r ($validationResponseData['choices'][0]['message']['content'],true )."</pre>");
        $validationSuggestion = trim($validationResponseData['choices'][0]['message']['content']);
        if ($validationSuggestion === $suggestion) {
            return $suggestion;
        } else {
            return str_replace(array('"', "'"), '', $validationSuggestion);
        }
    } else {
        return 'none';
    }
}

function getBestAspectValueFromChatGPTOLD($product, $aspectName, $aspectValues, $aspectConstraint) {
	//	//print("<pre>".print_r ($product['name_en'],true )."</pre>");
//	//print("<pre>".print_r ($product['brand'],true )."</pre>");
	$title=$product['name_description'];
	$brand=$product['brand'];
	$category_id=$product['category_id'];
	$model=$product['model'];
	$color=$product['color'];
	$condition_supp=$product['condition_supp'];
	$condition_name=$product['condition_name'];
	$upc=$product['upc'];
	//$upc=$product['upc'];
	$apiEndpoint = 'https://api.openai.com/v1/chat/completions';
 
	$apiKey = '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';

	if ($category_id == '617') {
        $messageContent = "Title: \"$title\" \"$condition_name\" \"$condition_supp\" .  movie or TV title. Provide the bestresult without repeating the question.";
    } else {
	   $messageContent = "Product title: \"$title\" \"$condition_name\" \"$color\" \"$condition_supp\" and brand: \"$brand\" and model: \"$model\". ";
	}
	 $messageContent .="UPC if it could help but not based on it: \"$upc\".";
	   if (!empty($aspectValues)) {
		   $values = implode(', ', array_map(function($val) { return $val['localizedValue']; }, $aspectValues));
		   $messageContent .= "Choose the most accurate and concise value for \"$aspectName\" from the following options: $values. Reply with the exact values only.";
	   } else {
		   $messageContent .= "Provide the most accurate and concise values for \"$aspectName\". Reply with 'none' if not applicable.";
	   }
	
		// Include specific constraints in the prompt
		if (isset($aspectConstraint['aspectDataType'])) {
			if ($aspectConstraint['aspectDataType'] === 'STRING' && isset($aspectConstraint['aspectMaxLength'])) {
				$messageContent .= " The response should be a string with a maximum length of {$aspectConstraint['aspectMaxLength']} characters.";
			} elseif ($aspectConstraint['aspectDataType'] === 'NUMBER') {
				$messageContent .= " The response should be a number.";
			}
		}

		if (isset($aspectConstraint['itemToAspectCardinality'])) {
			if ($aspectConstraint['itemToAspectCardinality'] === 'MULTI') {
				$messageContent .= ' Multiple values can be provided in between ","';
			} else {
				$messageContent .= " Only a single value is allowed.";
			}
		}
	//print("<pre>".print_r ($messageContent,true )."</pre>");
	$maxTokens = 16385 - 100;
    $currentTokens =ceil(strlen($messageContent) / 3); 
	//print("<pre>".print_r ($currentTokens,true )."</pre>");

    if ($currentTokens > $maxTokens) {
        $messageContent = substr($messageContent, 0, $maxTokens * 3);
    }
    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'Provide concise and accurate responses.'],
            ['role' => 'user', 'content' => $messageContent]
        ],
        'max_tokens' => 50,
        'temperature' => 0.3
    ];

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    }
    

    $responseData = json_decode($response, true);
	//print("<pre>".print_r ($responseData,true )."</pre>");
    if (json_last_error() !== JSON_ERROR_NONE) {
        return json_encode(['error' => 'Error parsing JSON response', 'response' => $response]);
    }

	if (isset($responseData['choices'][0]['message']['content'])) {
        $suggestion = trim($responseData['choices'][0]['message']['content']);

        // Remove any leading labels and unwanted phrases
		$aspectNamelower=strtolower($aspectName);
        $unwantedPhrases = [
            'As an AI', 'not provided', 'not applicable', 
            'Check the product packaging', 'specific model numbers for products', 
            'access to real-time databases', 'The most accurate and concise value for',
			"$aspectNamelower"
        ];
        foreach ($unwantedPhrases as $phrase) {
            if (stripos($suggestion, $phrase) !== false) {
                $suggestion = 'none';
            }
        }
		$unwantedPhrases = [
            "$aspectName: ",
        ];
        foreach ($unwantedPhrases as $phrase) {
            if (stripos($suggestion, $phrase) !== false) {
                $suggestion = str_replace($phrase, '', $suggestion);
            }
        }

        // Trim any extraneous whitespace
        $suggestion = trim($suggestion);

        // Process JSON response if multiple values are allowed
        if ($aspectConstraint['itemToAspectCardinality'] === 'MULTI') {
            $suggestionArray = json_decode($suggestion, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $suggestionArray;
            }
        }

        // Verify if the suggestion is within the provided values for predefined options
        if (!empty($aspectValues)) {
            $allowedValues = array_map(function($val) { return $val['localizedValue']; }, $aspectValues);
            if (!in_array($suggestion, $allowedValues)) {
                // If suggestion not valid, use GPT's suggestion rather than defaulting to 'none'
                return $suggestion;
            }
        }

        // Validate response length if max length is specified
        if (isset($aspectConstraint['aspectMaxLength']) && strlen($suggestion) > $aspectConstraint['aspectMaxLength']) {
            return 'none';
        }

        // Ensure the response matches the aspect data type
        if (isset($aspectConstraint['aspectDataType']) && $aspectConstraint['aspectDataType'] === 'NUMBER' && !is_numeric($suggestion)) {
            return 'none';
        }

        return $suggestion;
    } else {
        return 'none';
    }
}
function cleanResponse($response) {
	echo 3038;
		//print("<pre>".print_r ($response,true )."</pre>");

    $cleaned = [];
    foreach ($response as $key => $value) {
        if (is_array($value)) {
            $cleaned[$key] = cleanResponse($value);
        } else {
            $value = trim($value, '"'); // Remove leading and trailing quotes
            // Handle cases where nested aspect name appears in the value
            if (preg_match('/^{\s*"' . $key . '":\s*"(.+?)"\s*}$/', $value, $matches)) {
                $cleaned[$key] = $matches[1];
            } else {
                $cleaned[$key] = $value;
            }
        }
    }
    return $cleaned;
}
function getBestAspectValues($title, $brand, $categorySpecifics) {
    $bestValues = [];

    foreach ($categorySpecifics as $aspect) {
        $aspectName = $aspect['localizedAspectName'];
        $aspectValues = $aspect['aspectValues'] ?? [];

        if (!empty($aspectValues)) {
            $bestValue = getBestAspectValueFromChatGPT($title, $brand, $aspectName, $aspectValues);
            $bestValues[$aspectName] = $bestValue;
        } else {
            $bestValues[$aspectName] = 'Free text';
        }
    }

    return $bestValues;
}
function getCategorySpecifics($connectionapi,$product,$db,$categoryTreeId = 0) {

	//	//print("<pre>".print_r ($product,true )."</pre>");
		$categoryId=$product['category_id'];
	if(!isset($product['specifics'])){
		//print("<pre>".print_r (3088,true )."</pre>");
		$accessToken = getEbayAccessToken($connectionapi['APICLIENTID'], $connectionapi['APICLIENTSECRET']);

		$apiEndpoint = "https://api.ebay.com/commerce/taxonomy/v1/category_tree/$categoryTreeId/get_item_aspects_for_category?category_id=$categoryId";
	//echo $apiEndpoint;
		$headers = [
			"Authorization: Bearer $accessToken",
			'Content-Type: application/json',
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			return json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
		}
		

		$responseDataAspect = json_decode($response, true);
		//$responseData=$responseDataAspect['aspects'];
		
		//$responseData=array();
		if(!isset($responseDataAspect['aspects']) && $categoryTreeId==100){
			return null;
		}elseif(!isset($responseDataAspect['aspects'])){
			getCategorySpecifics($connectionapi,$product,$db,100);
		}
		//print("<pre>".print_r ($responseDataAspect,true )."</pre>");
		if(isset($responseDataAspect['aspects'])){
			foreach($responseDataAspect['aspects'] as $data){
				$reponseFormat[$data['localizedAspectName']]=$data;
			}
			
		//	//print("<pre>".print_r ($reponseFormat,true )."</pre>");
			$reponseFormat=json_encode($reponseFormat,true);
		//	//print("<pre>".print_r ($reponseFormat,true )."</pre>");
			if (json_last_error() !== JSON_ERROR_NONE) {
				return json_encode(['error' => 'Error parsing JSON response', 'response' => $response]);
			//	//print("<pre>".print_r ($reponseFormat,true )."</pre>");
			}
			$sql2="UPDATE `oc_category` SET ebay=1,`specifics` = '".addslashes($reponseFormat)."' WHERE `oc_category`.`category_id` = '".$categoryId."'";
	//		//print("<pre>".print_r ($sql2,true )."</pre>");
			mysqli_query($db,$sql2);
			$returndata= $reponseFormat;
		}
	}elseif(isset($product['specifics'])){
	//	//print("<pre>".print_r (3121,true )."</pre>");
		$responseData = json_decode($product['specifics'], true);
		//print("<pre>".print_r ($responseData,true )."</pre>");
		//print("<pre>".print_r ($responseData,true )."</pre>");
		if (json_last_error() !== JSON_ERROR_NONE) {
			return json_encode(['error' => 'Error parsing JSON response', 'response' => $product['specifics']]);
		//	//print("<pre>".print_r ($responseData,true )."</pre>");
		}
		
		$returndata= $responseData;

	}
	
	if (isset($returndata) && (isset($product['marketplace_item_id']) && $product['marketplace_item_id']>0)) {
	//	//print("<pre>".print_r (3132,true )."</pre>");
		$categorySpecifics = [];
     foreach ($responseData as $aspect) {
		$aspect['aspectValues']=(isset($aspect['aspectValues']))?$aspect['aspectValues']:array();
		//if(isset($aspect['aspectValues'])){
       		$categorySpecifics[$aspect['localizedAspectName']] = getBestAspectValueFromChatGPT($product,$aspect['localizedAspectName'],$aspect['aspectValues'], $aspect['aspectConstraint']);
		//}
		}
	//$categorySpecifics= cleanResponse($categorySpecifics);
   		//print("<pre>".print_r ($categorySpecifics,true )."</pre>");
//	//print("<pre>".print_r ($responseData,true )."</pre>");
//print("<pre>".print_r ($categorySpecifics,true )."</pre>");
		$resultspecifics=array();
		foreach ($categorySpecifics as $key=>$categorySpecific) {
			$categorySpecific= (strtolower ($key)=='unit quantity' && strtolower ($categorySpecific)=='none')?1:$categorySpecific;
			$categorySpecific= (strtolower ($key)=='unit type' && strtolower ($categorySpecific)=='none')?'Unit':$categorySpecific;
			$categorySpecific= (strtolower ($key)=='brand' && strtolower ($categorySpecific)=='none')?$product['brand']:$categorySpecific;
			if(strtolower ($key)=='mpn' ){
				unset($categorySpecifics['mpn']);
			}
			$categorySpecific= (strtolower ($categorySpecific)!='none')?$categorySpecific:'';
			$categorySpecific=str_replace('"','',$categorySpecific);
			if(strtolower ($key)!='region code'){
				$categorySpecific=str_replace(',','@@',$categorySpecific);
			}
			$resultspecifics[$key]=array (
				'Name' 	=> escape_special_chars($key),
				'Value' => explode('@@',escape_special_chars($categorySpecific))
			);

		}

		$returndata= $resultspecifics;
		
	}else{
	//	if(isset($product['specifics'])){
	//		return $product['specifics'];
	//	}else{
		$returndata='';
	//	}
		
	}
	return $returndata;

}



function array2xml($array, $xml = false){
    if($xml === false){
        $xml = new SimpleXMLElement('<Item/>');
    }
    foreach($array as $key => $value){
        if(is_array($value)){
         //   array2xml($value, $xml->addChild($key));
			if($key=="NameValueList"){
				//array2xml($value, $xml->addChild($key['NameValueList']));
				//echo $key."<br>";
				foreach($value as $key=>$value1){
					if(isset($value1['Source']))unset($value1['Source']);
					//echo $value1['Name'];
					/* if($value1['Name']!="MPN"){ */
						if(is_array($value1['Value'])){
							foreach($value1['Value'] as $key=>$value2){
									$value3['Name']=$value1['Name'];
									$value3['Value']=$value2;
									array2xml($value3, $xml->addChild("NameValueList"));
							}
						}else{
							array2xml($value1, $xml->addChild("NameValueList"));
						}
					/* } */
				}
			}else{
				array2xml($value, $xml->addChild($key));
			} 
        } else {
/* 			if($key =="ItemSpecifics"){
				foreach($array2 as $key => $value){
			} */
            $xml->addChild($key, convert_smart_quotes($value));
        }
    }
    return $xml->asXML();
}	
function get_from_upctemp($upc){
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
    if ($httpcode != 200){
        // Handle error
    } else {
        $response = explode('"items":[', $response);
        $json = json_decode('{"items":['.$response[1], true);
        sleep(2);
        
    }
    $i = 0;
    $pricehigh = 0;
    if(isset($json['items'][0]['offers'])){
        foreach($json['items'][0]['offers'] as $offer){
            if($offer['domain'] != "walmart.com" && $offer['domain'] != "walmart.ca"){
                unset($json['items'][0]['offers'][$i]);
            } else {
                if($pricehigh < $offer['price']) $pricehigh = $offer['price'];
            }
            $i++;
        }
    }
//	//print("<pre>".print_r ($json,true )."</pre>");
	if(count($json['items'])>0){
  		  $json['items'][0]['highest_recorded_price'] = $pricehigh;
   		
   		 return json_encode($json);
	}else{
		return null;
	}
}
function get_from_upctempOLDGPT($upc){
			//$user_key = 'eaac1efcfa72479a29bb9a4a588fd9fb';
			//$endpoint = 'https://api.upcitemdb.com/prod/v1/lookup';
				$endpoint = 'https://api.upcitemdb.com/prod/trial/lookup';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			//  "user_key: $user_key",
			 // "key_type: 3scale",
			  //"Content-Type: application/json",
			  "Accept: application/json"/* ,
			   "Accept-Encoding: gzip,deflate" */
			));
			// HTTP GET
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_URL, $endpoint.'?upc='.$upc);
			$response  = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			// proceed with other queries
			//
			if ($httpcode != 200){
			  //echo "<br><br>error status $httpcode...\n";
			}else {
		// Convert xml string into an object 
				$response =explode('"items":[',$response);
				$json =json_decode('{"items":['.$response[1], true);
				sleep(2);
				
				}
				$i=0;
				$pricehigh=0;
				if(isset($json['items'][0]['offers'])){
					foreach($json['items'][0]['offers'] as $offer){
						if($offer['domain']!="walmart.com" && $offer['domain']!="walmart.ca"){
							unset($json['items'][0]['offers'][$i]);
						}else{
							if($pricehigh<$offer['price'])$pricehigh=$offer['price'];
						}
						$i++;
					}
				}
				$json['items'][0]['highest_recorded_price']=$pricehigh;
				$result=json_encode($json);
			return $result;
	}
function fetchUrl($uri) {
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $uri);
    curl_setopt($handle, CURLOPT_POST, false);
   // curl_setopt($handle, CURLOPT_BINARYTRANSFER, false);
	curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($handle, CURLOPT_HEADER, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
    $response = curl_exec($handle);
    $hlength  = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    $body     = substr($response, $hlength);
	//print("<pre>".print_r ($response,true )."</pre>"); 
    // If HTTP response is not 200, throw exception
    if ($httpCode != 200) {
        throw new Exception($httpCode);
    }
    return $body;
}
function get_myeBay_selling($connectionapi,$page) {
			//print_r($connectionapi);
					$post = '<?xml version="1.0" encoding="utf-8"?>
			<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				
				  <ActiveList>
					<Sort>TimeLeft</Sort>
					<Pagination>
					  <EntriesPerPage>200</EntriesPerPage>
					  <PageNumber>1</PageNumber>
					</Pagination> 
					</ActiveList>
				<ErrorLanguage>en_US</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
			</GetMyeBaySellingRequest>';
			$headers = array(
						"X-EBAY-API-COMPATIBILITY-LEVEL: 1157",
						"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
						"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
						"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
						"X-EBAY-API-CALL-NAME: GetMyeBaySelling",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			//$result = json_encode($post); 
			 //print("<pre>".print_r ($post,true )."</pre>");
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($connection);
			$err = curl_error($connection);
			 curl_close($connection);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('**', '', $result);
			$result = str_replace("\r\n", '', $result);
			$result = str_replace('\"', '"', $result);
			if ($err) {
				//echo "cURL Error #:" . $err;
			} else {
				// Convert xml string into an object 
				//echo $result."\nallo";
				$new = simplexml_load_string($result);  
				// Convert into json 
				$result = json_encode($new); 
				$textoutput=str_replace("}","<br><==<br>",$result);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				//echo $textoutput."\nallo"."<br>";
				$json = json_decode($result, true);
				//print("<pre>".print_r ($json,true )."</pre>");
				//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
				$ebay_quantity=$json["Item"]["Quantity"];
				$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
				$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
				//echo $ebay_quantity."---".$Quantity_sold;
				//$encodedSesssionIDString =rawurlencode ($sessionId);
				//echo $encodedSesssionIDString;
			}
			return $result;
	}
function get_ebay_product($connectionapi,$ebay_id) {
			//print_r($connectionapi);
					$post = '<?xml version="1.0" encoding="utf-8"?>
			<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
			
				 <IncludeItemCompatibilityList>true</IncludeItemCompatibilityList>
				<IncludeItemSpecifics>true</IncludeItemSpecifics>
				 <DetailLevel>ReturnAll</DetailLevel>
				<ErrorLanguage>en_US</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
				<ItemID>'.$ebay_id.'</ItemID>
			</GetItemRequest>';
			$headers = array(
						"X-EBAY-API-COMPATIBILITY-LEVEL: 1157",
						"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
						"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
						"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
						"X-EBAY-API-CALL-NAME: GetItem",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			//$result = json_encode($post); 
			 //print("<pre>".print_r ($post,true )."</pre>");
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($connection);
			$err = curl_error($connection);
			 curl_close($connection);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('**', '', $result);
			$result = str_replace("\r\n", '', $result);
			$result = str_replace('\"', '"', $result);
			if ($err) {
				//echo "cURL Error #:" . $err;
			} else {
				// Convert xml string into an object 
				//echo $result."\nallo";
				$new = simplexml_load_string($result);  
				// Convert into json 
				$result = json_encode($new); 
				$textoutput=str_replace("}","<br><==<br>",$result);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				//echo $textoutput."\nallo"."<br>";
			//	$json = json_decode($result, true);
				//print("<pre>".print_r ($json,true )."</pre>");
				//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
			//	$ebay_quantity=$json["Item"]["Quantity"];
			//	$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
			//	$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
				//echo $ebay_quantity."---".$Quantity_sold;
				//$encodedSesssionIDString =rawurlencode ($sessionId);
				//echo $encodedSesssionIDString;
			}
			return $result;
	}
	function get_ebay_product_upc($connectionapi, $ebay_id) {
	//	//print("<pre>".print_r ($ebay_id,true )."</pre>");
		$post = '<?xml version="1.0" encoding="utf-8"?>
			<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
			
				<IncludeItemCompatibilityList>true</IncludeItemCompatibilityList>
				<IncludeItemSpecifics>true</IncludeItemSpecifics>
				<DetailLevel>ReturnAll</DetailLevel>
				<ErrorLanguage>en_US</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
				<ItemID>'.$ebay_id.'</ItemID>
			</GetItemRequest>';
		$headers = array(
			"X-EBAY-API-COMPATIBILITY-LEVEL: 1157",
			"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
			"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
			"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
			"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
			"X-EBAY-API-CALL-NAME: GetItem",
			"X-EBAY-API-SITEID: 0" // 3 for UK
		);
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($connection);
		$err = curl_error($connection);
		curl_close($connection);
		if ($err) {
			// Gérer l'erreur ici
		} else {
			$new = simplexml_load_string($result);
			$result = json_encode($new);
			$json = json_decode($result, true);
		//	//print("<pre>".print_r ($json,true )."</pre>");
			// Récupération du UPC
			$upc = (string) $json["Item"]["ProductListingDetails"]["UPC"];
		}
		return $upc;
	}
	function get_ebay_multiple_products($connectionapi,$ebay_ids) {
		//print_r($connectionapi);
		//print("<pre>".print_r ($ebay_ids,true )."</pre>");1
		$item_id= "";
		foreach ($ebay_ids as $ebay_id){
			$item_id.='<ItemID>'.$ebay_id.'</ItemID>';
		}
				$post = '<?xml version="1.0" encoding="utf-8"?>
		<GetMultipleItemsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
			<IncludeSelector> Details </IncludeSelector>
			'.$item_id.'
		</GetMultipleItemsRequest>';
		$headers = array(
					"X-EBAY-API-IAF-TOKEN: Bearer v^1.1#i^1#f^0#I^3#p^1#r^0#t^H4sIAAAAAAAAAOVYf2wTVRxv124ExyAqQZzMlBvyB9rru15/3bk1dCtbO8ZWaDdgguR6fbcebe8u9965NYSwFCSKiZE/UCNLnCSgoEQg6vzDgAEVg5pIgpAlJCzKH8RAZogJC4bgXVtGNwkga+IS+0/vfd/3fd/n83nf73vvDgxUzV62M7TzRo15VsXQABioMJupajC7qvL5uZaK2koTKHEwDw0sGbDmLFcaEJdJK+waiBRZQtDWn0lLiM0bGwlNlViZQyJiJS4DEYt5NhpY1c46ScAqqoxlXk4TtnCwkeCAG3h9tCDQgHHxAqNbpTsxY3Ij4fQwjE8QfIwPepwup96NkAbDEsKchPVu4HTZgccO6BjlYd0u1u0lgdfZQ9i6oYpEWdJdSED482jZ/Fi1BOr9kXIIQRXrQQh/ONAS7QyEgys6Yg2Oklj+ogxRzGENTW41ywlo6+bSGrz/NCjvzUY1nocIEQ5/YYbJQdnAHTCPAD+vtCvhA26Gommai3N0WYRskdUMh++PwrCICbuQd2WhhEWcfZCeuhbxzZDHxVaHHiIctBl/qzUuLQoiVBuJFU2B9YFIhPA3c1JXNCkqduMBGQ+RNUE7laAA5AQqbmfigk7Z6StOVIhWFHnKTM2ylBANyZCtQ8ZNUEcNp2rjKtFGd+qUOtWAgA1EpX6+Oxp6fD3GkhbWUMNJyVhVmNGFsOWbD16BidEYq2Jcw3AiwtSOvER6TSmKmCCmduYzsZg8/aiRSGKssA5HX18f2UeTstrrcAJAOdatao/ySZjhiIKvUeu6v/jgAXYxT4WH+kgksjir6Fj69UzVAUi9hN8FGMAwRd0nw/JPtf7DUMLZMbkeylUfPi/0cj7I84CHDCUI5agQfzFJHQYOGOey9gynpiBW0hwP7byeZ1oGqmKCpd2Ck/YJ0J7wMILdxQiCPe5OeOyUACGAMB7nGd//qVAeNtWjkFchLleulyfPN9MoBtqg2oOb16lNK1MdjE8Vm9SW3iQMrZKygZUta9whPqXxa+XGh62Ge5JvTou6MjF9/jIJYNR6mUQIyQjDxLToRXlZgRE5LfLZmbXAtJqIcCrORmE6rRumRTKgKOGy7dXlofcvt4lH413WM+q/OJ/uyQoZKTuzWBnjkR6AU0TSOIFIXs44ZE4zah0nDfOmPOpp8Rb1e+uMYq2TLLAVE4UrJ6lTxkkSvcKTKkSypup3bbLTuIHF5BSU9PMMq3I6DdVuatr1nMlomIun4Uwr7DIkuMjNsMOW8lJeF017GO+0ePH5o3TTTNuSyrQVW4OPcq12TH7D95vyPypnPgly5uMVZjNoAM9R9WBxlaXLaplTi0QMSZETSCT2SvqbqwrJFMwqnKhWPGm6vm9PqLl2Refby7bEsj/vPW2aU/KBYWgjWDjxiWG2haou+d4AFt3tqaTmPVXjdAEPoCmP2+X29oD6u71WaoF1/ufbW7q2kjvWfxh+Y+kHV+tOmtaO7QA1E05mc6XJmjObWi+ffW/Mxz3R9np/16yNDU//Maxd2XlI+Grf+tGqS7cfw+PercqxWospuqQr2XsA5Lb9prQPbvlGDr5169zNngWxM8dGN9QdvUDuOt11uvqzmmj37ROp/h8PxXeFRsZC343uOrJcsxLRCxetc3NfjH5y/t3RWe1XMnXV528Nt57Y/dJ2qXXx4IaF+1wjBz/63VL/69Azv1w+E179WmReav+n12/eOHzt+sHvj49fGn/xatP4y20fj3z95WBrcOn7dYvqzRefjcZemD+U2718zy2+/U2uZcwV3Xsk8njbuXX1iz0/8Jt/GvZHjh6ODONv95+lDo6ceke7uGDHBfuBG38tPLX12qU/X12+7RBTWMu/ATfcB+P6EQAA",
					"X-EBAY-API-SITEID: 0",
					"X-EBAY-API-VERSION: 863",
					"X-EBAY-API-CALL-NAME: GetMultipleItems",
					"X-EBAY-API-REQUEST-ENCODING:xml"
					 // 3 for UK
		);
		//$result = json_encode($post); 
		//print("<pre>".print_r ($post,true )."</pre>");
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($connection, CURLOPT_URL, "https://open.api.ebay.com/shopping");
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($connection);
		$err = curl_error($connection);
		 curl_close($connection);
		$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
		$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
		$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
		$result = str_replace('**', '', $result);
		$result = str_replace("\r\n", '', $result);
		$result = str_replace('\"', '"', $result);
		if ($err) {
			//echo "cURL Error #:" . $err;
		} else {
			// Convert xml string into an object 
			//echo $result."\nallo";
			$new = simplexml_load_string($result);  
			// Convert into json 
			$result = json_encode($new); 
			$textoutput=str_replace("}","<br><==<br>",$result);
			$textoutput=str_replace("{","<br>==><br>",$textoutput);
			//echo $textoutput."\nallo"."<br>";
			$json = json_decode($result, true);
		//	//print("<pre>".print_r ($json,true )."</pre>");
			//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
			$ebay_quantity=$json["Item"]["Quantity"];
			$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
			$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
			//echo $ebay_quantity."---".$Quantity_sold;
			//$encodedSesssionIDString =rawurlencode ($sessionId);
			//echo $encodedSesssionIDString;
		}
		return $result;
}
function get_ebay_inventory($connectionapi,$StartTimeFrom,$StartTimeTo,$limit,$page) {
	//print_r($connectionapi);
	//print("<pre>".print_r ($ebay_ids,true )."</pre>");1
	//$limit= 4;
			$post = '<?xml version="1.0" encoding="utf-8"?>
	<GetSellerListRequest  xmlns="urn:ebay:apis:eBLBaseComponents">
	
		<ErrorLanguage>en_US</ErrorLanguage>
		<WarningLevel>High</WarningLevel>
		<GranularityLevel>Coarse</GranularityLevel> 
		<StartTimeFrom>'.$StartTimeFrom.'T00:00:00.005Z</StartTimeFrom> 
		<StartTimeTo>'.$StartTimeTo.'T23:59:59.005Z</StartTimeTo> 
		<IncludeWatchCount>true</IncludeWatchCount> 
		<Pagination>
			<EntriesPerPage>'.$limit.'</EntriesPerPage>
			<PageNumber>'.$page.'</PageNumber>
	  	</Pagination>
	</GetSellerListRequest>';
//echo '/home/n7f9655/public_html/phoenixliquidation/interne/test/GetSellerListRequest.txt';
	unlink('/home/n7f9655/public_html/phoenixliquidation/interne/test/GetSellerListRequest.txt');
	link('/home/n7f9655/public_html/phoenixliquidation/interne/test/GetSellerListRequest.txt','/home/n7f9655/public_html/phoenixliquidation/interne/test/GetSellerListRequest.txt');
	$fp = fopen('/home/n7f9655/public_html/phoenixliquidation/interne/test/GetSellerListRequest.txt', 'w');
	fwrite($fp, $post); 	
$headers = array(
		"X-EBAY-API-COMPATIBILITY-LEVEL: 1257",
		"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
		"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
		"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
		"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
		"X-EBAY-API-CALL-NAME: GetSellerList",
		"X-EBAY-API-SITEID: 0" // 3 for UK
);
$connection = curl_init();
curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
curl_setopt($connection, CURLOPT_POST, 1);
curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($connection);
$err = curl_error($connection);
	 curl_close($connection);
	$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
	$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
	$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
	$result = str_replace('**', '', $result);
	$result = str_replace("\r\n", '', $result);
	$result = str_replace('\"', '"', $result);
	if ($err) {
		//echo "cURL Error #:" . $err;
	} else {
		// Convert xml string into an object 
		//echo $result."\nallo";
		$new = simplexml_load_string($result);  
		// Convert into json 
		$result = json_encode($new); 
		$textoutput=str_replace("}","<br><==<br>",$result);
		$textoutput=str_replace("{","<br>==><br>",$textoutput);
		//echo $textoutput."\nallo"."<br>";
		$json = json_decode($result, true);
		//print("<pre>".print_r ($json,true )."</pre>");
		//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
		$ebay_quantity=$json["Item"]["Quantity"];
		$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
		$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
		//echo $ebay_quantity."---".$Quantity_sold;
		//$encodedSesssionIDString =rawurlencode ($sessionId);
		//echo $encodedSesssionIDString;
	}
	return $result;
}
function upload_image_from_product_id($product_id,$product_id_cloner,$db){
				$sqldir = 'catalog/product';
						if (!file_exists($GLOBALS['SITE_ROOT']."image/catalog/product/".$product_id."/")) {
							mkdir($GLOBALS['SITE_ROOT']."image/catalog/product/".$product_id."/", 0755, true);
						}
					$sql3 = 'SELECT * FROM `oc_product` where product_id = "'.$product_id_cloner.'"';
					//echo $sql3."<br>";
					$req3 = mysqli_query($db,$sql3);
					$data3 = mysqli_fetch_assoc($req3);
							$piclink=$GLOBALS['WEBSITE']."image/".$data3['image'];
							$imagetmp= explode(".",$data3['image']);
							//$piclink=$imagetmp[0];
							//echo $piclink;
							$pos=".".$imagetmp[1];
							$uploads_dir = 'image/catalog/product';
							$rdproduct_id="pri".mt_rand ( 1 , 99 );
							$dir_name=$GLOBALS['SITE_ROOT'].$uploads_dir."/".$product_id."/".$product_id.$rdproduct_id.$pos;
							$imagepath=$GLOBALS['SITE_ROOT'].'image/catalog/product/'.$product_id."/".$product_id.$rdproduct_id.$pos;
							$error=save_image($piclink, $imagepath);
							//echo $imagepath;
							if($error==""){
								delete_photo($product_id,"principal",$db);
								$sql2="UPDATE `oc_product` SET image ='".$sqldir."/".$product_id."/".$product_id.$rdproduct_id.$pos."' where product_id=".$product_id;
								mysqli_query($db,$sql2);
							}else{
								//echo "<br>ERROR:".$error;
							}
					$sql3 = 'SELECT * FROM `oc_product_image` where product_id = "'.$product_id_cloner.'"';
					//echo $sql3."<br>";
					$req3 = mysqli_query($db,$sql3);
					while($data3 = mysqli_fetch_assoc($req3)){
							$piclink=$GLOBALS['WEBSITE']."image/".$data3['image'];
							$imagetmp= explode(".",$data3['image']);
							//$piclink=$imagetmp[0];
							$pos=".".$imagetmp[1];
							$rdproduct_id="sec".mt_rand ( 1 , 99 );
							$dir_name=$GLOBALS['SITE_ROOT'].$uploads_dir."/".$product_id."/".$product_id.$rdproduct_id.$pos;
							$imagepath=$GLOBALS['SITE_ROOT'].'image/catalog/product/'.$product_id."/".$product_id.$rdproduct_id.$pos;
							$error=save_image($piclink, $imagepath);
							if($error==""){
								$sql2="INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$sqldir."/".$product_id."/".$product_id.$rdproduct_id.$pos."')";
								mysqli_query($db,$sql2);
							}else{
								//echo "<br>ERROR:".$error;
							}
				//echo $sql7."<br>";
					}
		//echo $sql2."<br>";
		//echo '<br>'.$sql2;
}
function revise_ebay_product_inventaire($connectionapi,$ebay_id,$product_id,$updquantity,$db) {
	$result2= product_description_ebay($connectionapi,$product_id,$db,"oui");
	echo "<br>result2 3289:".$result2;
	//echo $updquantity."allo";
			if (is_numeric($updquantity))
			{
				//echo $updquantity."allo";
				$quantity='<Quantity>'.$updquantity.'</Quantity>';
				}
			$post = '<?xml version="1.0" encoding="utf-8"?>
					<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
					  
						<ErrorLanguage>en_US</ErrorLanguage>
						<WarningLevel>High</WarningLevel>
					  <Item> 
						<ItemID>'.$ebay_id.'</ItemID>
						'. escape_special_chars($result2).$quantity.'
						</Item>
					</ReviseItemRequest>';
			//$post = escape_special_chars($post);
//print("<pre>".print_r ($post,true )."</pre>");
	$headers = array(
				"X-EBAY-API-COMPATIBILITY-LEVEL: 1157",
				"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
				"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
				"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
				"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
				"X-EBAY-API-CALL-NAME: ReviseItem",
				"X-EBAY-API-SITEID: 0" // 3 for UK
	);
	$connection = curl_init();
	curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
	curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($connection, CURLOPT_POST, 1);
	curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
	curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($connection);
	$err = curl_error($connection);
	$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
	$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
	$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
	$result = str_replace('**', '', $result);
	$result = str_replace("\r\n", '', $result);
	$result = str_replace('\"', '"', $result);
	if ($err) {
		//echo "cURL Error #:" . $err;
	} else {
		// Convert xml string into an object 
		//echo $result."\nallo";
		$new = simplexml_load_string($result);  
		// Convert into json 
		$result = json_encode($new); 
		$textoutput=str_replace("}","<br><==<br>",$result);
		$textoutput=str_replace("{","<br>==><br>",$textoutput);
		//echo $textoutput."\nallo"."<br>"; 
		$json = json_decode($result, true);
		//print("<pre>".print_r ($json,true )."</pre>");
		//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
	//	$ebay_quantity=$json["Item"]["Quantity"];
	//	$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
	//	$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
		//echo $ebay_quantity."---".$Quantity_sold;
		//$encodedSesssionIDString =rawurlencode ($sessionId);
		//echo $encodedSesssionIDString;
	}
//unlink($GLOBALS['SITE_ROOT'].'/interne/test/ReviseItemRequest2.txt');
//link($GLOBALS['SITE_ROOT'].'/interne/test/ReviseItemRequest2.txt','ReviseItemRequest2.txt');
//$fp = fopen($GLOBALS['SITE_ROOT'].'/interne/test/ReviseItemRequest2.txt', 'w');
//fwrite($fp, $post); 
	return $result;
}
function upload_image_from_old_website($product_id,$db){
						if (!file_exists($GLOBALS['SITE_ROOT']."image/catalog/product/".$product_id."/")) {
							mkdir($GLOBALS['SITE_ROOT']."image/catalog/product/".$product_id."/", 0755, true);
						}
					$sql3 = 'SELECT * FROM `oc_product` where product_id = "'.$product_id.'"';
					//echo $sql3."<br>";
					$req3 = mysqli_query($db,$sql3);
					$data3 = mysqli_fetch_assoc($req3);
							$piclink=$GLOBALS['WEBSITE']."/image/".$data3['image'];
							$uploads_dir = 'image/catalog/product';
							$dir_name=$GLOBALS['SITE_ROOT'].$uploads_dir."/".$product_id."/".$data3['image'];
							$imagepath=$GLOBALS['SITE_ROOT'].'image/'.$data3['image'];
							$error=save_image($piclink, $imagepath);
							//echo $imagepath;
							if($error!=""){
								//echo "<br>ERROR:".$error;
							}
					$sql3 = 'SELECT * FROM `oc_product_image` where product_id = "'.$product_id.'"';
					//echo $sql3."<br>";
					$req3 = mysqli_query($db,$sql3);
					while($data3 = mysqli_fetch_assoc($req3)){
							$piclink=$GLOBALS['WEBSITE']."/image/".$data3['image'];
							$dir_name=$GLOBALS['SITE_ROOT'].$uploads_dir."/".$product_id."/".$data3['image'];
							$imagepath=$GLOBALS['SITE_ROOT'].'image/'.$data3['image'];
							$error=save_image($piclink, $imagepath);
							//echo $imagepath;
							if($error!=""){
								//echo "<br>ERROR:".$error;
							}
				//echo $sql7."<br>";
					}
		//echo $sql2."<br>";
		//echo '<br>'.$sql2;
}
function upload_image($product_id,$principal,$db){
	//$GLOBALS['SITE_ROOT']=$_SERVER['DOCUMENT_ROOT'];
	if (isset($_FILES['imageprincipale'])) {
		$file=$_FILES['imageprincipale']['tmp_name'];
		//$image= addslashes(file_get_contents($_FILES['imageprincipale']['tmp_name']));
		$image_name= addslashes($_FILES['imageprincipale']['name']);
			//print_r($_FILES);
			$uploads_dir = 'image/catalog/product';
			$sqldir = 'catalog/product';
			//$uploads_dir = 'upload';
				if ($_FILES['imageprincipale']['error'] == 0) {
					if (is_uploaded_file($_FILES['imageprincipale']['tmp_name']))
					{
						$tmp_name = $_FILES['imageprincipale']['tmp_name'];
						// basename() may prevent filesystem traversal attacks;
						// further validation/sanitation of the filename may be appropriate
						$name = basename($_FILES['imageprincipale']['name']);
						$path=explode(".",basename($_FILES['imageprincipale']['name']));
						//echo "allo";
						$rdproduct_id="";
						if (!file_exists($GLOBALS['SITE_ROOT']."image/catalog/product/".$product_id."/")) {
							mkdir($GLOBALS['SITE_ROOT']."image/catalog/product/".$product_id."/", 0755, true);
						}
						if($principal==1) {
							delete_photo($product_id,"principal",$db);
							if($product_id>0)$rdproduct_id="pri".mt_rand ( 1 , 99 );
							$dir_name=$GLOBALS['SITE_ROOT'].$uploads_dir."/".$product_id."/".$product_id.$rdproduct_id.".".$path[1];
							$sql2="UPDATE `oc_product` SET image ='".$sqldir."/".$product_id."/".$product_id.$rdproduct_id.".".$path[1]."' where product_id=".$product_id;
							//echo $sql2;
						}else{
							if($product_id>0)$rdproduct_id="sec".mt_rand ( 1 , 99 );
							$dir_name=$GLOBALS['SITE_ROOT'].$uploads_dir."/".$product_id."/".$product_id.$rdproduct_id.".".$path[1];
							$sql2="INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$sqldir."/".$product_id."/".$product_id.$rdproduct_id.".".$path[1]."')";
						}
						mysqli_query($db,$sql2);
						move_uploaded_file($tmp_name, $dir_name);
						//echo $sql2;
					}
				}
	}
	if (isset($_FILES['image']['tmp_name'])) {
		//
		$file=$_FILES['image']['tmp_name'];
		//$image= addslashes(file_get_contents($_FILES['image']['tmp_name']));
		//$image_name= addslashes($_FILES['image']['name']);
			//print_r($_FILES);
			$uploads_dir = 'image/catalog/product';
			$sqldir = 'catalog/product';
			//$uploads_dir = 'upload';
			foreach ($_FILES['image']['error'] as $key => $error) {
				if ($error == 0) {
					if (is_uploaded_file($_FILES['image']['tmp_name'][$key]))
					{
						//echo "allo2";
						$tmp_name = $_FILES['image']['tmp_name'][$key];
						// basename() may prevent filesystem traversal attacks;
						// further validation/sanitation of the filename may be appropriate
						$name = basename($_FILES['image']['name'][$key]);
						$path=explode(".",basename($_FILES['image']['name'][$key]));
						$rdproduct_id="sec".mt_rand ( 1 , 99 );
						$dir_name=$GLOBALS['SITE_ROOT'].$uploads_dir."/".$product_id."/".$product_id.$rdproduct_id.".".$path[1];
						move_uploaded_file($tmp_name, $dir_name);
						//echo $_SERVER['DOCUMENT_ROOT'].$uploads_dir."/108_".$key.".".$path[1];  
						$sql2="INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$sqldir."/".$product_id."/".$product_id.$rdproduct_id.".".$path[1]."')";
						$req2=mysqli_query($db,$sql2);
						//echo $sql2;
					}
				}
			}
		}
	return $GLOBALS['WEBSITE'].$sqldir."/".$product_id.$rdproduct_id.".".$path[1];
}
	function copy_photo_dans_db($product_id,$product_id_princ,$db){
			$sql = 'SELECT * FROM `oc_product` where product_id = "'.$product_id_princ.'"';
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			$sql2="UPDATE `oc_product` SET image ='".$data['image']."' where product_id=".$product_id;
			$req2=mysqli_query($db,$sql2);
			$sql = "SELECT * FROM oc_product_image where product_id='".$product_id_princ."'";
			$req= mysqli_query($db,$sql); 
			while($data = mysqli_fetch_assoc($req))
			{
				$sql2="INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$data['image']."')";
				$req2=mysqli_query($db,$sql2);
			}
	}
function upload_from_ebay($product_id,$piclink,$principal,$db){
	if(strpos($piclink,"jpeg")>0)$pos=".jpeg";
	if(strpos($piclink,"JPEG")>0)$pos=".jpeg";
	if(strpos($piclink,"png")>0)$pos=".png";
	if(strpos($piclink,"PNG")>0)$pos=".png";
	if(strpos($piclink,"jpg")>0)$pos=".jpg";
	if(strpos($piclink,"JPG")>0)$pos=".jpg";
	if(strpos($piclink,"gif")>0)$pos=".gif";
	if(strpos($piclink,"GIF")>0)$pos=".gif";
	//echo "upload_from_ebay".$pos."<br>";
	$uploads_dir = 'image/catalog/product';
	$sqldir = 'catalog/product';
	if ($piclink!="")$picexterne2=$piclink;	 
		//echo $picexterne2;
		 $picexterne2=str_replace(array("\r","\n", " "),"",$picexterne2);
		 $filterimage=explode("?",basename($picexterne2));
		 $path=explode($pos,basename($filterimage[0]));
		$ext=count($path)-1;
	 if(($piclink!="") ){ //|| $piclink!=""
		$image = file_get_contents($picexterne2);
//echo $GLOBALS['SITE_ROOT'].'image/catalog/product/'.$product_id."/";
						if (!file_exists($GLOBALS['SITE_ROOT']."image/catalog/product/".$product_id."/")) {
							mkdir($GLOBALS['SITE_ROOT']."image/catalog/product/".$product_id."/", 0755, true);
						}
		//file_put_contents($imagepath, $image); //Where to save the image on your server
						if($principal==1) {
							$rdproduct_id="pri".mt_rand ( 1 , 99 );
							$dir_name=$GLOBALS['SITE_ROOT'].$uploads_dir."/".$product_id."/".$product_id.$rdproduct_id.$pos;
							$imagepath=$GLOBALS['SITE_ROOT'].'image/catalog/product/'.$product_id."/".$product_id.$rdproduct_id.$pos;
							$error=save_image($piclink, $imagepath);
							if($error==""){
								delete_photo($product_id,"principal",$db);
								$sql2="UPDATE `oc_product` SET image ='".$sqldir."/".$product_id."/".$product_id.$rdproduct_id.$pos."' where product_id=".$product_id;
								$req2=mysqli_query($db,$sql2);
							}
						}else{
							$rdproduct_id="sec".mt_rand ( 1 , 99 );
							$dir_name=$GLOBALS['SITE_ROOT'].$uploads_dir."/".$product_id."/".$product_id.$rdproduct_id.$pos;
							$imagepath=$GLOBALS['SITE_ROOT'].'image/catalog/product/'.$product_id."/".$product_id.$rdproduct_id.$pos;
							$error=save_image($piclink, $imagepath);
							if($error==""){
								$sql2="INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$sqldir."/".$product_id."/".$product_id.$rdproduct_id.$pos."')";
								$req2=mysqli_query($db,$sql2);
							}
						}
		//echo $sql2."<br>";
		//echo '<br>'.$sql2;
	 }
	 return "catalog/product/".$product_id.$rdproduct_id.".".$path[$ext];
}
function upload_from_link_website($product_id,$piclink,$principal,$db){
	if ($piclink != "") {
		//echo "<br>piclink_upload_from_link_website:".$piclink;
		// Vérification de l'extension de l'image dans le lien
		if (strpos($piclink, "webp") > 0 || strpos($piclink, "WEBP") > 0) {
			$pos = ".jpg"; // Si l'extension est ".webp", on ajoute ".jpg" par défaut
			$extension_old = ".webp";
		} elseif (strpos($piclink, "jpeg") > 0 || strpos($piclink, "JPEG") > 0) {
			$pos = ".jpeg";
			$extension_old = ".jpg";
		} elseif (strpos($piclink, "png") > 0 || strpos($piclink, "PNG") > 0) {
			$pos = ".png";
			$extension_old = $pos;
		} elseif (strpos($piclink, "jpg") > 0 || strpos($piclink, "JPG") > 0) {
			$pos = ".jpg";
			$extension_old = $pos;
		} elseif (strpos($piclink, "gif") > 0 || strpos($piclink, "GIF") > 0) {
			$pos = ".gif";
			$extension_old = $pos;
		} else {
			// Si l'extension n'est pas trouvée, on ajoute ".jpg" par défaut
			$pos = ".jpg";
			$extension_old = "";
		}
		if(strpos($piclink, "http://") === false && strpos($piclink, "https://") === false) {
			$piclink = "https://" . $piclink;
		}
		// Extraction du nom de fichier et de l'extension de l'image
	//	if ($extension_old != "" && $extension_old != ".webp") {
		//	$picexterne2 = $piclink;
		//	$filterimage = explode("?", basename($picexterne2));
			//$path = explode($extension_old, basename($filterimage[0]));
			$extension_new = $pos;
	//	} else {
		//	$path = array(basename($piclink));
	//		$extension_new = "";
	//	}
	}
	if ($piclink != "") {
		if (!file_exists($GLOBALS['SITE_ROOT'] . "image/catalog/product/" . $product_id . "/")) {
			mkdir($GLOBALS['SITE_ROOT'] . "image/catalog/product/" . $product_id . "/", 0755, true);
		}
        $randomid=mt_rand(1, 999);
		if ($principal == 1) {
			$rdproduct_id = "pri" . mt_rand(1, 99);
			$imagepath = $GLOBALS['SITE_ROOT'] . 'image/catalog/product/' . $product_id . "/" . $product_id . $rdproduct_id . $extension_new;
			$error = save_image($piclink, $imagepath);
			if ($error == "") {//" . $sqldir . "
				delete_photo($product_id, "principal", $db);
				$sql2 = "UPDATE `oc_product` SET image ='catalog/product/" . $product_id . "/" . $product_id . $rdproduct_id . $extension_new . "' where product_id=" . $product_id;
				mysqli_query($db, $sql2);
				//echo "<br><br>".$sql2;
			}else{
				//echo "<br>ERROR:".$error;
			}
		} else {
			$error="";
			$rdproduct_id = "sec" . $randomid;
			$randomid++;
			$imagepath = $GLOBALS['SITE_ROOT'] . 'image/catalog/product/' . $product_id . "/" . $product_id . $rdproduct_id . $extension_new;
			$error = save_image($piclink, $imagepath);
			if ($error == "") { //" . $sqldir . "
				$sql2 = "INSERT INTO oc_product_image (product_id, image) VALUES ('" . $product_id . "','catalog/product/" . $product_id . "/" . $product_id . $rdproduct_id . $extension_new . "')";
				mysqli_query($db, $sql2);
				//echo "<br><br>".$sql2;
			}else{
				//echo "<br>ERROR:".$error;
			}
		}
	}
	return "catalog/product/" . $product_id . $rdproduct_id . $extension_new;
}
function save_image($inPath, $outPath) {
	//echo "<br>inPath:".$inPath;
	//echo "<br>outPath:".$outPath;
	$in = fopen($inPath, "rb");
	$out = fopen($outPath, "wb");
	$error = "";
	if ($in) {
		while (!feof($in)) {
			$read = fread($in, 8192000);
			fwrite($out, $read);
		}
	} else {
		//echo "erreur voir JO";
		$error = error_get_last()['message'];
	}
	fclose($in);
	fclose($out);
	return $error;
}
function upload_from_link($product_id,$piclink,$principal,$db){
	if ($piclink != "") {
		//echo "<br>piclink:".$piclink;
		// Vérification de l'extension de l'image dans le lien
		if (strpos($piclink, "webp") > 0 || strpos($piclink, "WEBP") > 0) {
			$pos = ".jpg"; // Si l'extension est ".webp", on ajoute ".jpg" par défaut
			$extension_old = ".webp";
		} elseif (strpos($piclink, "jpeg") > 0 || strpos($piclink, "JPEG") > 0) {
			$pos = ".jpeg";
			$extension_old = ".jpg";
		} elseif (strpos($piclink, "png") > 0 || strpos($piclink, "PNG") > 0) {
			$pos = ".png";
			$extension_old = $pos;
		} elseif (strpos($piclink, "jpg") > 0 || strpos($piclink, "JPG") > 0) {
			$pos = ".jpg";
			$extension_old = $pos;
		} elseif (strpos($piclink, "gif") > 0 || strpos($piclink, "GIF") > 0) {
			$pos = ".gif";
			$extension_old = $pos;
		} else {
			// Si l'extension n'est pas trouvée, on ajoute ".jpg" par défaut
			$pos = ".jpg";
			$extension_old = "";
		}
		if(strpos($piclink, "http://") === false && strpos($piclink, "https://") === false) {
			$piclink = "https://" . $piclink;
			//echo "<br>https absent";
		}
		// Extraction du nom de fichier et de l'extension de l'image
	//	if ($extension_old != "" && $extension_old != ".webp") {
		//	$picexterne2 = $piclink;
		//	$filterimage = explode("?", basename($picexterne2));
			//$path = explode($extension_old, basename($filterimage[0]));
			$extension_new = $pos;
	//	} else {
		//	$path = array(basename($piclink));
	//		$extension_new = "";
	//	}
	$uploads_dir = 'image/catalog/product';
	$sqldir = 'catalog/product';
	if (strpos($piclink, "?") === true){
		$picexterne2=$piclink;	 
		 $picexterne2=str_replace(array("\r","\n", " "),"",$picexterne2);
		  $filterimage=explode("?",basename($picexterne2));
		$piclink= $filterimage[0];
	}
	//|| $piclink!=""
		//$image = file_get_contents($picexterne2);
						if (!file_exists($GLOBALS['SITE_ROOT']."image/catalog/product/".$product_id."/")) {
							mkdir($GLOBALS['SITE_ROOT']."image/catalog/product/".$product_id."/", 0755, true);
						}
						if($principal==1) {
							$rdproduct_id="pri".mt_rand ( 1 , 99 );
							//$dir_name=$GLOBALS['SITE_ROOT'].$uploads_dir."/".$product_id."/".$product_id.$rdproduct_id.$extension_new;
							$imagepath=$GLOBALS['SITE_ROOT'].'image/catalog/product/'.$product_id."/".$product_id.$rdproduct_id.$extension_new;
							$error=save_image($piclink, $imagepath);
							if($error==""){
								delete_photo($product_id,"principal",$db);
								$sql2="UPDATE `oc_product` SET image ='".$sqldir."/".$product_id."/".$product_id.$rdproduct_id.$extension_new."' where product_id=".$product_id;
								mysqli_query($db,$sql2);
								//echo "<br>sql2".$sql2;
							}else{
								//echo "<br>ERROR:".$error;
							}
						}else{
							$rdproduct_id="sec".mt_rand ( 1 , 99 );
							//$dir_name=$GLOBALS['SITE_ROOT'].$uploads_dir."/".$product_id."/".$product_id.$rdproduct_id.$extension_new;
							$imagepath=$GLOBALS['SITE_ROOT'].'image/catalog/product/'.$product_id."/".$product_id.$rdproduct_id.$extension_new;
							$error=save_image($piclink, $imagepath);
							if($error==""){
								$sql2="INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$sqldir."/".$product_id."/".$product_id.$rdproduct_id.$extension_new."')";
								mysqli_query($db,$sql2);
								//echo "<br>sql2".$sql2;
							}else{
								//echo "<br>ERROR:".$error;
							}
						}
		//echo $sql2;
	}
	 return "catalog/product/".$product_id.$rdproduct_id.$extension_new;
}
/* function delete_image($product_image_id,$image){
			$sql = 'delete from `oc_product_image` where product_image_id='.$product_image_id;
	//echo $sql;
			$req = mysqli_query($db,$sql);
			unlink($GLOBALS['SITE_ROOT'].'image/'.$image);
} */
function insert_currency_exchange($db){
	$uploads_dir = 'interne/admin/data';
	$dir_name="data/FXUSDCAD.json";
	$string = file_get_contents($dir_name);
	if ($string === false) {
		//echo "erreur";
	}
	$currencies = json_decode($string, true);
	if ($currencies === null) {
		//echo "erreur2";
	}
	//print("<pre>".print_r ($currencies['observations'],true )."</pre>");
 	foreach ($currencies['observations'] as $currency) {
		//echo $currency['d']."<br>";
		//echo $currency['FXUSDCAD']['v']."<br>";
		$sql = 'INSERT INTO `admin_fxusdcad` (`date_info`, `rate`) ';
		$sql = $sql.'VALUES ( "'.$currency['d'].'", "'.$currency['FXUSDCAD']['v'].'")';
		$req = mysqli_query($db,$sql); 
	} 
}
function mise_en_page_description($connectionapi,$product_id,$db)
{
	//echo '<br>mise_en_page_description:';
			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where language_id=1 and oc_product.product_id=oc_product_description.product_id and oc_product.product_id="'.$product_id.'"';
		//	echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$calcitem=0;
			$data = mysqli_fetch_assoc($req);
			$name=$data['name'];
			$calcitem=$calcitem+1;
			$sql8 = "SELECT * FROM oc_product_description where language_id=2 and product_id=".$product_id;
			$req8= mysqli_query($db,$sql8); 
			//echo $sql2."<br>";
			$data8 = mysqli_fetch_assoc($req8);
			$namefr=$data8['name'];
			/*$sql9 = 'SELECT * FROM `oc_product_special` where product_id='.$product_id;
			// on envoie la requête
			//echo $sql5;
			$req9 = mysqli_query($db,$sql9);
			$data9 = mysqli_fetch_assoc($req9);*/
			$prixcad=number_format($data['price']*1.34, 2, '.', '');
			$prixusd=number_format($data['price'], 2, '.', '');
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$data['manufacturer_id'];
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$sql6 = 'SELECT * FROM `oc_product_to_category` ptc
					LEFT JOIN `oc_category` c ON (ptc.category_id=c.category_id) 
					LEFT JOIN `oc_category_description` cd ON (ptc.category_id=cd.category_id AND cd.language_id=1 ) 
					where ptc.product_id="'.$product_id.'" and c.leaf=1';
			//echo $sql;
			$req6 = mysqli_query($db,$sql6);
			$data6 = mysqli_fetch_assoc($req6);
			$categoryname=$data6['name'];
			$sql6 = 'SELECT * FROM `oc_product_to_category` ptc
					LEFT JOIN `oc_category` c ON (ptc.category_id=c.category_id) 
					LEFT JOIN `oc_category_description` cd ON (ptc.category_id=cd.category_id AND cd.language_id=2 ) 
					where ptc.product_id="'.$product_id.'" and c.leaf=1';
			$req6 = mysqli_query($db,$sql6);
			$data6 = mysqli_fetch_assoc($req6);
			$categorynamefr=$data6['name'];
			$category_id=$data6['category_id'];
			$brand=$data2['name'];
			//Anglais
			$description='<h2>Description :</h2>';
			$descriptionf='<h2>Description :</h2>';
			$modele="Mod&egravele";
			// averifer
			//if($data['andescription']!='')$description.=$data['andescription'].'<br><br>';
			$description.='<strong>Title : </strong>'.htmlspecialchars($name, ENT_QUOTES, 'UTF-8').'<br><strong>Model : </strong>'.capitalizeWords(htmlspecialchars($data['model'], ENT_QUOTES, 'UTF-8'));
			$descriptionf.='<strong>Titre : </strong>'.htmlspecialchars($namefr, ENT_QUOTES, 'iso-8859-1').'<br><strong>'.$modele.' : </strong>'.htmlspecialchars($data['model'], ENT_QUOTES, 'iso-8859-1');
			$description.='<br><strong>Brand : </strong>'.capitalizeWords(htmlspecialchars($brand, ENT_QUOTES, 'UTF-8')).'<br>';
			$descriptionf.='<br><strong>Marque : </strong>'.htmlspecialchars($brand, ENT_QUOTES, 'iso-8859-1').'<br>';
			if($data['color']=="")$data['color']="N/A";
			$description.='<strong>Color : </strong>'.capitalizeWords(htmlspecialchars($data['color'], ENT_QUOTES, 'UTF-8')).'<br>';
			$descriptionf.='<strong>Couleur : </strong>'.htmlspecialchars($data8['color'], ENT_QUOTES, 'iso-8859-1').'<br>';
			$description.='<strong>UPC : </strong>'.(string)$data['upc'].'<br>';
			$descriptionf.='<strong>UPC : </strong>'.(string)$data['upc'].'<br>';
			$description.='<strong>Package Dimension : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$descriptionf.='<strong>Dimension Boite : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$description.='<strong>Weight : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			$descriptionf.='<strong>Poids : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			//condition_supp pour DVD
			if(($category_id==617 || $category_id==139973)&& $data['condition_id']==99){
				$data['condition_id']=8;
				$sqlcond = 'UPDATE `oc_product` SET condition_id="'.$data['condition_id'].'" WHERE `product_id` ='.$product_id;
				$recond = mysqli_query($db,$sqlcond);
			}
			if(($category_id==617 || $category_id==139973) && $data['condition_id']==22){
				$data['condition_id']=7;
				$sqlcond = 'UPDATE `oc_product` SET condition_id="'.$data['condition_id'].'" WHERE `product_id` ='.$product_id;
				$recond = mysqli_query($db,$sqlcond);
			}
			$sql2 = 'SELECT * FROM `oc_condition` where language_id=1 and condition_id='.$data['condition_id'];
//echo $sql2;
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$description.='<h2>Conditions :</h2><p>- <strong>'.capitalizeWords($data2['name']).'</strong><br>';
		//	$description.=addslashes(capitalizeWords($data2['condition_supp'])."<br>");
			//print("<pre>".print_r ($data2,true )."</pre>");
			//echo capitalizeWords($data2['condition_supp'])."<br>";
			$conditionname=capitalizeWords($data2['name']);
			/*	if($data['condition_supp']!="" && $data['condition_supp']!=","){
					$description.='<b>Conditions:</b><br>';
					if(strpos($data['condition_supp'],",")===FALSE){
						$description.=addslashes('<font color="red"><strong>- '.htmlspecialchars($data['condition_supp'], ENT_QUOTES, 'UTF-8').'</strong></font><br>');
					}else{
						$conditionsupp=explode(',', $data['condition_supp']);
							foreach($conditionsupp as $conditioncheck){
								if($conditioncheck!=""){
									$description.=addslashes('<font color="red"><strong>- '.htmlspecialchars($conditioncheck, ENT_QUOTES, 'UTF-8').'</strong></font><br>');
									//echo $i;		
								}
							}	
					}
				}
				if($data['accessory']!="" && $data['accessory']!=","){
					$description.='<b>Accessories Included :</b><br>';
					if(strpos($data['accessory'],",")===FALSE){
						$description.='- '.$data['accessory'].'<br>';
					}else{
						$conditionsupp=explode(',', $data['accessory']);
							foreach($conditionsupp as $conditioncheck){
								if( $conditioncheck!=""){
									$description.='- '.$conditioncheck.'<br>';
									//echo $i;		
								}
							}	
					}		
				}				
				//echo $data['test'];		
				if($data['test']!="" && $data['test']!=","){
					$description.='<b>Tests - Repairs Done :</b><br>';
					if(strpos($data['test'],",")===FALSE){
						$description.='- '.$data['test'].'<br>';
					}else{
						$conditionsupp=explode(',', $data['test']);
						foreach($conditionsupp as $conditioncheck){
							if($conditioncheck!=""){
								$description.='- '.$conditioncheck.'<br>';
								//echo $i;		
							}
						}
					}
				}	*/			
//francais
			$sql3 = 'SELECT * FROM `oc_condition` where language_id=2 and condition_id='.$data['condition_id']; 
//echo $sql3;
			// on envoie la requête
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
			$descriptionf.='<h2>Conditions :</h2><p>- <strong>'.$data3['name'].'</strong><br>';
		//	$descriptionf.=addslashes($data3['condition_supp']."<br>");
			$conditionnamefr=$data3['name'];
			/*	if($data8['condition_supp']!="" && $data8['condition_supp']!=","){
					$descriptionf.='<b>Conditions Supplémentaire:</b><br>';
					if(strpos($data8['condition_supp'],",")===FALSE){
						$descriptionf.=addslashes('<font color="red"><strong>- '.htmlspecialchars($data8['condition_supp'], ENT_QUOTES, 'iso-8859-1').'</strong></font><br>');
					}else{
						$conditionsupp=explode(',', $data8['condition_supp']);
							foreach($conditionsupp as $conditioncheck){
								if($conditioncheck!=""){
									$descriptionf.=addslashes('<font color="red"><strong>- '.htmlspecialchars($conditioncheck, ENT_QUOTES, 'iso-8859-1').'</strong></font><br>');
									//echo $i;		
								}
							}	
					}
				}
				if($data8['accessory']!="" && $data8['accessory']!=","){
					$descriptionf.='<b>Accessoires supplémentaires inclus :</b><br>';
					if(strpos($data8['accessory'],",")===FALSE){
						$descriptionf.='- '.$data8['accessory'].'<br>';
					}else{
						$conditionsupp=explode(',', $data8['accessory']);
							foreach($conditionsupp as $conditioncheck){
								if( $conditioncheck!=""){
									$descriptionf.='- '.$conditioncheck.'<br>';
									//echo $i;		
								}
							}	
					}		
				}				
				//echo $data['test'];		
				if($data8['test']!="" && $data8['test']!=","){
					$descriptionf.='<b>Tests effectués :</b><br>';
					if(strpos($data8['test'],",")===FALSE){
						$descriptionf.='- '.$data8['test'].'<br>';
					}else{
						$conditionsupp=explode(',', $data8['test']);
						foreach($conditionsupp as $conditioncheck){
							if($conditioncheck!=""){
								$descriptionf.='- '.$conditioncheck.'<br>';
								//echo $i;		
							}
						}
					}
				}	*/
//echo $descriptionf;
			$description.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			$descriptionf.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"'.$GLOBALS['WEBSITE'].'image/'.$data['image'].'\" width=\"450\"</td></tr>';
			$descriptionf.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"'.$GLOBALS['WEBSITE'].'image/'.$data['image'].'\" width=\"450\"</td></tr>';
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$product_id;
			$req2= mysqli_query($db,$sql2); 
			//echo $sql2."<br>";
			$i=0;
			while($data2 = mysqli_fetch_assoc($req2))
			{
				if($i<13){
					$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"'.$GLOBALS['WEBSITE'].'image/'.$data2['image'].'\" width=\"450\"</td></tr>';
					$descriptionf.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"'.$GLOBALS['WEBSITE'].'image/'.$data2['image'].'\" width=\"450\"</td></tr>';
				$i++;
				}
			}
			$description.='</tbody></table><br>';
			$descriptionf.='</tbody></table><br>';
			//$description=htmlspecialchars_decode($description, ENT_QUOTES);
			//$description=addslashes($description);
			//$descriptionf=htmlspecialchars_decode($descriptionf, ENT_QUOTES);
			//$descriptionf=addslashes($descriptionf);
			$sql2 = 'UPDATE `oc_product_description` SET tag="'.$conditionname.','. str_replace(' ', ',', addslashes(($name))).','.(string)$data['upc'].'",meta_keyword="'.$conditionname.','. str_replace(' ', ',', addslashes(capitalizeWords($name))).','.(string)$data['upc'].'", meta_title="'.$conditionname." ".addslashes(capitalizeWords($name)).'",name="'.addslashes(capitalizeWords($name)).'", description="'.$description.'", meta_description="Liquidation '.addslashes(capitalizeWords($categoryname.' '.$name)).' '.$conditionname.' at the lowest price of $'.$prixcad.' CAD or $'.$prixusd.' USD Model: '.$data['model'].' UPC:'.(string)$data['upc'].'" WHERE language_id=1 and `product_id` ='.$product_id;
//echo $sql2."<br>";
			$req2 = mysqli_query($db,$sql2);
			//printf("Erreur : %s\n", mysqli_error($db));
			//echo "<br>";
			$sql2 = 'UPDATE `oc_product_description` SET tag="'.$conditionnamefr.','. str_replace(' ', ',', addslashes($namefr)).','.(string)$data['upc'].'",meta_keyword="'.$conditionnamefr.','. str_replace(' ', ',', addslashes($namefr)).','.(string)$data['upc'].'", meta_title="'.$conditionnamefr." ".addslashes($namefr).'",name="'.addslashes($namefr).'", description="'.$descriptionf.'", meta_description="Liquidation '.addslashes($categorynamefr.' '.$namefr).' '.$conditionnamefr.' au meilleur prix de $'.$prixcad.' CAD ou $'.$prixusd.' USD Modele: '.$data['model'].' UPC:'.(string)$data['upc'].'"  WHERE language_id=2 and `product_id` ='.$product_id;
//echo $sql2."<br>";			
			$req2 = mysqli_query($db,$sql2,MYSQLI_USE_RESULT);
			//printf("Erreur : %s\n", mysqli_error($db));
}
function revise_ebay_product($connectionapi,$ebay_id,$product_id,$updquantity,$db,$export_photo_to_ebay,$shorty = "") {
	//echo "OUI".$shorty;
	if($shorty==""){
			$result2= product_description_ebay($connectionapi,$product_id,$db,$export_photo_to_ebay,$shorty);
		//	echo "OUI";
	}elseif($shorty=="oui"){
			$result2= product_description_ebay($connectionapi,$product_id,$db,$export_photo_to_ebay,$shorty);
		//	echo "OUI";
	}else{
		$result2= "";
	//	echo "OUI";
	}
	$quantity='';
		//	echo "<br>result24015:".$result2;
					if (is_numeric($updquantity))
					{
						//echo "oui";
						$quantity='<Quantity>'.$updquantity.'</Quantity>';  
						}
					if($GLOBALS['NAME_CIE']=='PhoenixSupplies'){
						$SKU='<SKU>COM_'.$product_id.'</SKU>';
					}else{
						$SKU='<SKU>'.$product_id.'</SKU>';
					}
					$post = '<?xml version="1.0" encoding="utf-8"?>
							<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
							
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
							  	<Item> 
									<ItemID>'.$ebay_id.'</ItemID>
									'. $result2
										
									.$quantity
									.$SKU
									.'
							 	</Item>
							</ReviseItemRequest>';
					//echo "<br>post 4041:";
					//	//print("<pre>".print_r ($post,true )."</pre>");
							//echo "allo";
//unlink($GLOBALS['SITE_ROOT'].'/interne/test/ReviseItemRequest2.txt');
//link($GLOBALS['SITE_ROOT'].'/interne/test/ReviseItemRequest2.txt','ReviseItemRequest2.txt');
//$fp = fopen($GLOBALS['SITE_ROOT'].'/interne/test/ReviseItemRequest2.txt', 'w');
//fwrite($fp, $post); 
			//$post = escape_special_chars($post);
			$headers = array(
						"X-EBAY-API-COMPATIBILITY-LEVEL: 1149",
						"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'], 
						"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
						"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
						"X-EBAY-API-CALL-NAME: ReviseItem",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			//curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, FALSE);
			$result = curl_exec($connection);
			$err = curl_error($connection);
			 curl_close($connection);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('**', '', $result);
			$result = str_replace("\r\n", '', $result);
			$result = str_replace('\"', '"', $result);
			if ($err) {
				//echo "cURL Error #:" . $err;
			} else {
				// Convert xml string into an object 
				//echo $result."\nallo";
				//print_r($response);
				$new = simplexml_load_string($result);  
				// Convert into json 
				$result = json_encode($new); 
				$textoutput=str_replace("}","<br><==<br>",$result);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				//echo $textoutput."\nallo"."<br>";  
				$json = json_decode($result, true); 
			//print("<pre>".print_r ($json,true )."</pre>");
				//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
				//echo $ebay_quantity."---".$Quantity_sold;
				//$encodedSesssionIDString =rawurlencode ($sessionId);
				//echo $encodedSesssionIDString;
			}
			return $result;
	}
function revise_ebay_product_inventaire_sku_erreur($connectionapi,$ebay_id,$product_id,$updquantity) {
	//	$result2= product_description_ebay($connectionapi,$product_id,$db,$export_photo_to_ebay);
	//	echo "<br>result24015:".$result2;
				if (is_numeric($updquantity))
				{
					//echo "oui";
					$quantity='<Quantity>'.$updquantity.'</Quantity>';  
					}
				if($GLOBALS['NAME_CIE']=='PhoenixSupplies'){
					$SKU='<SKU>COM_'.$product_id.'</SKU>';
				}else{
					$SKU='<SKU>'.$product_id.'</SKU>';
				}
				$post = '<?xml version="1.0" encoding="utf-8"?>
						<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
						
							<ErrorLanguage>en_US</ErrorLanguage>
							<WarningLevel>High</WarningLevel>
							  <Item> 
								<ItemID>'.$ebay_id.'</ItemID>
								'
								.$quantity
								.$SKU
								.'
							 </Item>
						</ReviseItemRequest>';
					//	$post = escape_special_chars($post);
			//		echo "<br>post 4041:";
				//	//print("<pre>".print_r ($post,true )."</pre>");
						//echo "allo";
//unlink($GLOBALS['SITE_ROOT'].'/interne/test/ReviseItemRequest2.txt');
//link($GLOBALS['SITE_ROOT'].'/interne/test/ReviseItemRequest2.txt','ReviseItemRequest2.txt');
//$fp = fopen($GLOBALS['SITE_ROOT'].'/interne/test/ReviseItemRequest2.txt', 'w');
//fwrite($fp, $post); 
		$headers = array(
					"X-EBAY-API-COMPATIBILITY-LEVEL: 1149",
					"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'], 
					"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
					"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
					"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
					"X-EBAY-API-CALL-NAME: ReviseItem",
					"X-EBAY-API-SITEID: 0" // 3 for UK
		);
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
		//curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($connection);
		$err = curl_error($connection);
		 curl_close($connection);
		$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
		$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
		$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
		$result = str_replace('**', '', $result);
		$result = str_replace("\r\n", '', $result);
		$result = str_replace('\"', '"', $result);
		if ($err) {
			//echo "cURL Error #:" . $err;
		} else {
			// Convert xml string into an object 
			//echo $result."\nallo";
			//print_r($response);
			$new = simplexml_load_string($result);  
			// Convert into json 
			$result = json_encode($new); 
			$textoutput=str_replace("}","<br><==<br>",$result);
			$textoutput=str_replace("{","<br>==><br>",$textoutput);
			//echo $textoutput."\nallo"."<br>";  
			$json = json_decode($result, true); 
		//	//print("<pre>".print_r ($json,true )."</pre>");
			//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
			//echo $ebay_quantity."---".$Quantity_sold;
			//$encodedSesssionIDString =rawurlencode ($sessionId);
			//echo $encodedSesssionIDString;
		}
		return $result;
}
function capitalizeWords($string) {
	return preg_replace_callback('/\b\w+\b/', function($matches) {
		return ucfirst(strtolower($matches[0]));
	}, $string);
}
function product_description_ebay($connectionapi,$product_id,$db,$export_photo_to_ebay,$shorty=""){
		//pour alimenter la description du listing ebay
					$desc1= $GLOBALS['DESC1'];
					$desc2= '<p><b>US CUSTOMERS :</b><br>
					We use USPS in most cases or UPS when the item is heavier than 1 to 2 pounds.<br> 
					We ONLY ship from our Champlain, NY, USA location.
					<p><b>CANADIAN CUSTOMERS :</b> <br>
					We use different carriers with tracking (CanPar, Dicom, UPS, Purolator or FEDEX).<br>
					We know that shipping costs are expensive in Canada. But we\'ve cut our shipping costs a lot by paying the difference to provide good products to all Canadians.<br>
					<br>
					Please allow us 1-2 business days before see the status of the tracking number, because we normally take 1 business day to process your order.<br>
					<p><b>HOURS:</b>
					<br>Monday to Friday : 9am to 5pm EST (Eastern Standard Time)<br>
					Saturday to Sunday: Closed<br><br>
					<b>Please note that we are closed on Weekends.</b> <br>All messages sent during the weekend will be answered by the next business day. </p>
					<p><b>'.$GLOBALS['NAME_CIE'].' LLC 
					(USA):</b> <br>100 Walnut ST, <br>Champlain, New York, USA, 12919
					<br><b>'.$GLOBALS['NAME_CIE'].' INC. 
					(CANADA):</b> <br>659 Boulevard Jean-Paul Vincent<br>Quebec, Canada, J4G 1R3</p>
					';
					$desc3= '<p><b>USA &amp; CANADIAN CUSTOMERS</b>- We offer a 30 days of the eBay Money Back Guarantee. If you have any problem regarding the item, please CONTACT US DIRECTLY. We will explain to you how to return it or find the best solution for you. 
					</p><p><strong>INTERNATIONAL CUSTOMERS</strong>- We love our international customers, but due to the high costs of international shipping and because of the long distance items have to travel, ALL ITEMS SHIPPED OUTSIDE of the USA and Canada ARE SOLD AS IS WITH NO GUARANTEES. All sales are FINAL. No refunds or returns for international customers.
					</p>';
					$desc4= '<p>All statements regarding products and their configurations are made to the best of our ability and with the assumption that the buyer is knowledgeable and proficient in the use of this type of product.</p>
					 <p>REPLACEMENT, REFUNDS OR RETURNS will be allowed unless specified in the description. We offer a 30 days of the eBay Money Back Guarantee.</p>
					 <p> It is the buyers responsibility to understand the terms of the sale and the nature of the product offered.</p>';
					$desc5= '<p>Paypal, CreditCard and DebitCard.
					</p><p><b>CANADIAN CUSTOMERS:</b>- We ship from our Canadian Warehouse, Canadians Taxes may apply.
					<br><b>US CUSTOMERS:</b> We do not charge taxes because eBay does. If you have any issue please contact eBay.
					</p>';
					$list1= '<style type="text/css">
					#SuperWrapper {
					width: 800px;
					margin-left: auto;
					margin-right: auto;
					font-family: arial, Helvetica, sans-serif;
					font-size: 12px;
					}
					#SuperWrapper p {
					margin: 0px;
					padding: 0px 0px 15px 0px;
					line-height: 20px;
					}
					#SuperWrapper h1 {
					padding: 5px 0px 15px 0px;
					margin: 0px;
					font-size: 26px;
					letter-spacing: -1px;
					color: #000000;
					}
					#SuperWrapper a {
					text-decoration: underline;
					color: #990000;
					}
					#SuperWrapper a:hover {
					text-decoration:none;
					}
					#SuperHeader {
					width:800px;
					height: 240px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usHeader.jpg);
					}
					#SuperHeaderLogo {
					padding: 60px 0px 59px 50px;
					font-family: arial, Helvetica, sans-serif;
					font-size: 50px;
					letter-spacing: -3px;
					margin: 0px;
					color: #FFFFFF;
					text-shadow: 1px 1px 1px #000;
					}
					#SuperHeaderMenu {
					margin: 0px;
					}
					#SuperHeaderMenu ul.navi{
					padding: 0px;
					margin: 0px 0px 0px 0px;
					width: 800px;
					text-align: center;
					position: relative;
					}
					#SuperHeaderMenu ul.navi li{
					height: 22px;
					padding: 0 10px 0 10px;
					margin: 0px;
					display: inline;
					}
					#SuperHeaderMenu ul.navi li a{
					padding: 0px 8px 0px 8px;
					font: 18px arial, Helvetica, sans-serif;
					color: #FFFFFF;
					text-decoration: none;
					text-indent: 0px;
					margin: 0;
					width: inherit;
					letter-spacing: -1px;
					line-height: 30px;
					}
					#SuperHeaderMenu ul.navi li a:hover{
					color: #DEE4ED;
					}
					#SuperContentsWrapper {
					width: 800px;
					background-image: url('.$GLOBALS['WEBSITE'].'/ebay/usContents.jpg);
					}
					#SuperContents {
					width: 800px;
					background-image: url('.$GLOBALS['WEBSITE'].'/ebay/usContentsTop.jpg);
					background-repeat: no-repeat;
					}
					#SuperContentsSub {
					padding: 30px 40px 0px 40px;
					}
					#SuperFooter {
					width: 800px;
					height: 44px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usFooter.jpg);
					}
					#SuperFooterLink {
					width: 800px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usBG.jpg);
					height: 100px;
					}
					#SuperBoxContents {
					padding: 0px 80px 0px 80px;
					margin: 0px;
					}
					#SuperBoxContents p {
					padding: 0px 0px 10px 0px;
					margin: 0px;
					line-height: 20px;
					}
					#SuperBoxContents ul {
					padding: 0px 0px 0px 28px;
					margin: 0px;
					list-style-type: disc;
					}
					#SuperBoxContents li {
					line-height: 20px;
					}
					#SuperPayment {
					width: 800px;
					}
					#SuperPaymentTop {
					width: 800px;
					height: 83px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usPaymentPolicyTop.jpg);
					}
					#SuperPaymentContents {
					width: 800px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usPaymentPolicyContents.jpg);
					}
					#SuperPaymentBottom {
					width: 800px;
					height: 53px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usPaymentPolicyBottom.jpg);
					}
					#SuperShipping {
					width: 800px;
					}
					#SuperShippingTop {
					width: 800px;
					height: 83px;
					background-image: url('.$GLOBALS['WEBSITE'].'/ebay/usShippingPolicyTop.jpg);
					}
					#SuperAboutTop {
					width: 800px;
					height: 83px;
					background-image: url('.$GLOBALS['WEBSITE'].'/ebay/usAboutPolicyTop.jpg);
					}
					#SuperTermTop {
					width: 800px;
					height: 83px;
					background-image: url('.$GLOBALS['WEBSITE'].'/ebay/usTermPolicyTop.jpg);
					}
					#SuperShippingContents {
					width: 800px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usShippingPolicyContents.jpg);
					}
					#SuperShippingBottom {
					width: 800px;
					height: 53px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usShippingPolicyBottom.jpg);
					}
					#SuperContacts {
					width: 800px;
					}
					#SuperContactsTop {
					width: 800px;
					height: 83px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usContactsTop.jpg);
					}
					#SuperContactsContents {
					width: 800px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usContactsContents.jpg);
					}
					#SuperContactsBottom {
					width: 800px;
					height: 53px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usContactsBottom.jpg);
					}
					#SuperReturns {
					width: 800px;
					}
					#SuperReturnsTop {
					width: 800px;
					height: 83px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usReturnsTop.jpg);
					}
					#SuperReturnsContents {
					width: 800px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usReturnsContents.jpg);
					}
					#SuperReturnsBottom {
					width: 800px;
					height: 53px;
					background-image:url('.$GLOBALS['WEBSITE'].'/ebay/usReturnsBottom.jpg);
					}
					/* HTML5 ELEMENTS */
					/* sub images > thumbnail list */
					ul#SuperThumbs, ul#SuperThumbs li {
					margin: 0;
					padding: 0;
					list-style: none;
					}
					ul#SuperThumbs li {
					float: left;
					background: #ffffff;
					border: 1px solid #cccccc;
					margin: 0px 0px 10px 10px;
					padding: 8px;
					-moz-border-radius: 10px;
					border-radius: 10px;
					}
					ul#SuperThumbs a {
					float: left;
					display: block;
					width: 150px;
					height: 150px;
					line-height: 100px;
					overflow: hidden;
					position: relative;
					z-index: 1;
					}
					ul#SuperThumbs a img {
					float: left;
					width: 100%;
					height: 100%;
					border: 0px;
					}
					/* sub images > mouse over */
					ul#SuperThumbs a:hover {
					overflow: visible;
					z-index: 1000;
					border: none;
					}
					ul#SuperThumbs a:hover img {
					background: #ffffff;
					border: 1px solid #cccccc;
					padding: 10px;
					-moz-border-radius: 10px;
					border-radius: 10px;
					position: absolute;
					top:-20px;
					left:-50px;
					width: auto;
					height: auto;
					}
					/* sub images > clearing floats */
					ul#SuperThumbs:after, li#SuperThumbs:after {
					content: ".";
					display: block;
					height: 0;
					clear: both;
					visibility: hidden;
					}
					ul#SuperThumbs, li#SuperThumbs {
					display: block;
					}
					ul#SuperThumbs, li#SuperThumbs {
					min-height: 1%;
					}
					* html ul#SuperThumbs, * html li#SuperThumbs {
					height: 1%;
					}
					</style>
					<div id="SuperWrapper">
					<div id="SuperHeader">
					<div id="SuperHeaderLogo"><br></div>
					<div id="SuperHeaderMenu">
					<ul class="navi">
					<li><a href="https://www.ebay.com/str/phoenixdepotdotcom">Other Items</a></li>
					<li><a href="https://feedback.ebay.com/ws/eBayISAPI.dll?ViewFeedback2&userid=phoenixliquidationcenter&ftab=AllFeedback&myworld=true&rt=nc&_trksid=p2545226.m2531.l4585">Feedbacks</a></li>
					<li><a href="https://members.ebay.com/ws/eBayISAPI.dll?ViewUserPage&amp;userid=phoenixliquidationcenter">About Us</a></li>
					<li><a href="https://contact.ebay.com/ws/eBayISAPI.dll?FindAnswers&frm=284&requested=phoenixliquidationcenter&iid=-1">Contact Us</a></li>
					<li><a href="https://my.ebay.com/ws/eBayISAPI.dll?AcceptSavedSeller&amp;ru=http%3A//cgi.ebay.com/ws/eBayISAPI.dll?ViewItemNext&amp;item=330478824623&amp;mode=0&amp;ssPageName=STRK:MEFS:ADDVI&amp;SellerId=phoenixliquidationcenter&amp;preference=0&amp;selectedMailingList_4487562=false">Add To Favorites</a></li>
					</ul>
					</div>
					</div>
					<div id="SuperContentsWrapper">
					<div id="SuperContents">
					<div id="SuperContentsSub">';
					$list2= '</div>
					<div id="SuperShipping">
					<div id="SuperAboutTop"></div>
					<div id="SuperShippingContents">
					<div id="SuperBoxContents">';
					$list3= '
					</div>
					</div>
					<div id="SuperShipping">
					<div id="SuperShippingTop"></div>
					<div id="SuperShippingContents">
					<div id="SuperBoxContents">';
					$list4= '</div>
					</div>
					<div id="SuperShippingBottom"></div>
					</div>
					<div id="SuperContacts">
					<div id="SuperContactsTop"></div>
					<div id="SuperContactsContents">
					<div id="SuperBoxContents">';
					$list5= '</div>
					</div>
					<div id="SuperShippingBottom"></div>
					</div>
					<div id="SuperContacts">
					<div id="SuperTermTop"></div>
					<div id="SuperContactsContents">
					<div id="SuperBoxContents">';
					$list6= '</div>
					</div>
					<div id="SuperContactsBottom"></div>
					</div>
					<div id="SuperReturns">
					<div id="SuperReturnsTop"></div>
					<div id="SuperReturnsContents">
					<div id="SuperBoxContents">';
					$list7= '</div>
					</div>
					<div id="SuperReturnsBottom"></div>
					</div>
					</div>
					</div>
					<div id="SuperFooter"></div>
					<div id="SuperFooterLink">
					<p align="center">
					</p>
					</div>
					</div>'; 	
// on crée la requête SQL
 $sql = 'SELECT PD.color AS color,P.image as image_princ,P.sku, P.condition_id,
 P.location AS location,P.unallocated_quantity, P.mpn, P.upc, P.ean, P.model, P.weight, P.height, M.manufacturer_id,
 M.name as brand, P.width, P.length,PD.specifics,P.ebay_id,CON.name AS condition_name,
 P.product_id, P.location, PD.name AS name_description, PD.description,CO.conditions, P.price_with_shipping,CD.specifics,PD.condition_supp,
 P.quantity,P.price AS priceretail,P.price AS price_magasin,P.date_price_upd AS date_price_upd_magasin,C.category_id AS category_id 
 FROM `oc_product` AS P LEFT JOIN 	oc_product_to_category AS PC ON PC.product_id=P.product_id 
 LEFT JOIN `oc_category` AS C ON (C.category_id=PC.category_id AND C.leaf=1)
 LEFT JOIN `oc_category_description` AS CD ON (CD.language_id=1  and C.category_id=CD.category_id )
 LEFT JOIN `oc_condition_ebay_to_category` AS CC ON CC.category_id=C.category_id
 LEFT JOIN `oc_condition_ebay` AS CO ON CC.condition_ebay_id=CO.condition_ebay_id
 LEFT JOIN `oc_condition` CON ON (P.condition_id=CON.condition_id AND CON.language_id=1)
 
 LEFT JOIN `oc_product_description`AS PD ON P.product_id=PD.product_id 
LEFT JOIN `oc_manufacturer`AS M ON P.manufacturer_id=M.manufacturer_id 
 where  P.product_id="'.$product_id.'" group by  P.product_id';// limit 2';// and product_id=1312';// ';
//echo $sql;
// on envoie la requête
$req = mysqli_query($db,$sql);
$data = mysqli_fetch_assoc($req);	
$product = $data;			
		//		$Part_Mfg=$data['mpn'];		
				$Price=($data['price_with_shipping']/.75);// 
				$PriceSite=($data['price_with_shipping']);
				$PriceWithShipping=$data['price_with_shipping'];
				$PriceNoShipping=$data['priceretail'];
				$Weight=$data['weight'];	
				$Height=$data['height'];	
			//	$manufacturer_id=$data['manufacturer_id'];
				$brand=$data['brand'];
				$Width=$data['width'];	
				$Depth=$data['length'];	
				$CustomLabel=$data['product_id'];	
				$Name=Stripslashes($data['name_description']);
				$upc=$data['upc'];	
				$CategoryID=$data['category_id'];
				$conditions = json_decode($data['conditions'], true);
				//print("<pre>".print_r ($conditions,true )."</pre>");
				$condition_id=$data['condition_id'];
				
				if(isset($data['product_specifics'])){
					$NameValueLists=json_decode($data['product_specifics'],true);
				}else{
					$NameValueLists=null;
				}
				//$NameValueLists=null;
		/*if($data['quantity_total']<($data['quantity']+$data['unallocated_quantity'])){
			$updquantity=$data['unallocated_quantity'];
		}else{
			$updquantity=$data['quantity']+$data['unallocated_quantity'];
		}*/
				$Image_1=''.$GLOBALS['WEBSITE'].'/image/'.$data['image_princ'];
				$line=$data['description'];
				//echo $CategoryID;
if($CategoryID==20349 || $CategoryID==178893 || $CategoryID==182066 || $CategoryID==123417 || $CategoryID==112529||
	$CategoryID==58540 || $CategoryID==33602 || $CategoryID==146496 || $CategoryID==48619 || 
	$CategoryID==20357 || $CategoryID==80077 || $CategoryID==123422 || $CategoryID==96991 || $CategoryID==35190 || $CategoryID==48677 || $CategoryID==182068 || $CategoryID==42425) {
	$desc1="<p><b>".$GLOBALS['NAME_CIE']." </b>is a business based in USA and in CANADA that resells products acquired from liquidation center, primarily to American and Canadian buyers. </p>
					<p><b>OUR GOAL: </b><br>Offer very good products, sold at the BEST PRICE and thus make you happy!</p>";
}
				$listing_description=$list1.$line.$list2.$desc1.$list3.$desc2.$list4.$desc3.$list5.$desc4.$list6.$desc5.$list7;
				$listing_description=str_replace(array("\r","\n"),"",$listing_description);
				//$listing_description=html_entity_decode($listing_description, ENT_QUOTES); ERROR DEPUIS CHANGEMENT SITE
				$listing_description=urldecode($listing_description);
				$listing_description="<![CDATA[" .convert_smart_quotes($listing_description). "]]>";
				//echo $listing_description;
				//
								$Price=number_format($Price,2); // augmenter le prix
								$sql2 = "SELECT * FROM oc_product_image where product_id='".$product_id."'";
								$req2= mysqli_query($db,$sql2); 
								$i=1;
				$WeightTot=array();
				$Weight=floatval($Weight);
				$WeightTot=explode('.', $Weight);
				if($Weight<.25){
					$WeightOZ=4;
				}else{
					$WeightOZ=intval(($Weight-$WeightTot[0])*16);
				}
				$poids_total = poidsVolumiqueNucleaire($Depth, $Width, $Height,$Weight);
				//echo $Name;
				$result="";
			//	if($shorty==""){
					$result.="<Title><![CDATA[".escape_special_chars(convert_smart_quotes($Name))."]]></Title>
					";
					$result.='<ConditionID>'.$conditions[$condition_id]['value'].'</ConditionID>
					';
					$result.='<UPC>'.$upc.'</UPC>
					';
					//print("<pre>".print_r ($product,true )."</pre>");
					if(!isset($product['product_specifics'])){
						//print("<pre>".print_r ($product,true )."</pre>");
					//	if($product['category_id']=='617'){ //category_id=139973 or pc.category_id=617
					//		$NameValueLists=get_movie_item_specific($Name,$brand,$upc);
							//print("<pre>".print_r ($NameValueLists,true )."</pre>");
					//	}else{
							$NameValueLists=getCategorySpecifics($connectionapi,$product,$db);
						//	//print("<pre>".print_r ($NameValueLists,true )."</pre>");
					//	}
						$sql = "UPDATE `oc_product` SET `product_specifics` = '" . addslashes(json_encode($NameValueLists,true)). "' WHERE `product_id` = '" . $product['product_id'] . "'";
						mysqli_query($db, $sql);
					//	//print("<pre>".print_r ($sql,true )."</pre>");
					}
					if(isset($NameValueLists)){
						//print("<pre>".print_r ($NameValueLists,true )."</pre>");
						$result.='
										<ItemSpecifics>';
						foreach($NameValueLists as $NameValueList){
					//		if (is_array($NameValueList['Value'])) {
					//			$NameValueList['Value'] = implode(', ', $NameValueList['Value']);
					//		}
						if (is_array($NameValueList['Value'])) {
							$result.='
							<NameValueList>
							<Name>'.$NameValueList['Name'].'</Name>';
							foreach($NameValueList['Value'] as $value){
								if (is_array($value)) {
									$result.='	
										<Value>'.((isset($value['name']))?$value['name']:($value['Name'])).'</Value>';
								}else{
									$result.='	
										<Value>'.($value).'</Value>';
								}
								
							}
							$result.='</NameValueList>';
						}else{
								if($NameValueList['Value']!="" ){
									$result.='
													<NameValueList>
														<Name>'.$NameValueList['Name'].'</Name>
														<Value>'.($NameValueList['Value']).'</Value>
													</NameValueList>';
								}
							}
						}
					
						$result.='		
						</ItemSpecifics>
								';
					}
				//	if($export_photo_to_ebay==""){ //<GalleryURL>'.addslashes($Image_1).'</GalleryURL>
					// <PhotoDisplay>PicturePack</PhotoDisplay>
						$result.='<PictureDetails>
						<GalleryType>Gallery</GalleryType>
										  <PictureURL>'.addslashes($Image_1).'</PictureURL>';
										while($data2 = mysqli_fetch_assoc($req2))
										{
											if($i<13){
												$result.=addslashes('<PictureURL>'.$GLOBALS['WEBSITE'].'/image/'.$data2['image'].'</PictureURL>');//Image_'.$j.'>';
											$i++;
											}
										}
						$result.='
										</PictureDetails>';
				//	}
				/*	$sqlman = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$manufacturer_id;
					// on envoie la requête
					$reqman = mysqli_query($db,$sqlman);
					mysqli_fetch_assoc($reqman);*/
					$result.='<Description>'.$listing_description.'</Description>';
					if($GLOBALS['NAME_CIE']!='PhoenixLiquidation'){
						/*$result.='<ProductListingDetails>
							<BrandMPN>
								<MPN>'.$Part_Mfg.'</MPN>
								<Brand>'.$dataman['name'].'</Brand>
							</BrandMPN>
						</ProductListingDetails>';*/
						}
			//	}else
				if($shorty=="oui"){
					if($data['category_id']=='29585' || $data['category_id']=='177666' || $data['category_id']=='11848' || $data['category_id']=='67500' 
					|| $data['category_id']=='21205' || $data['category_id']=='177764' || $data['category_id']=='177765' || $data['category_id']=='21022'){
						$shippingname='ShippingVeRO';
						$shipping_id='242937796019';
						$PriceSite=$PriceNoShipping;
					}elseif($data['category_id']=='117414' ){
						$shippingname='Shipping_Calculated_Heavy';
						$shipping_id='251932372019';
						$PriceSite=$PriceNoShipping;
					}elseif(($data['category_id']=='617' || $data['category_id']=='51071' || $data['category_id']=='176984'
							|| $data['category_id']=='261186' || $data['category_id']=='280' || $data['category_id']=='80135'
							|| $data['category_id']=='73329' 
							|| $data['category_id']=='149960'  || $data['category_id']=='14962' || $data['category_id']=='149959'
							|| $data['category_id']=='175718' || $data['category_id']=='15050'|| $data['category_id']=='25409') and $Weight<4
					){
						$shippingname='Shipping_Calculated_Media';
						$shipping_id='251955965019';
						$PriceSite=$PriceNoShipping;
					}elseif(($data['category_id']=='617' || $data['category_id']=='51071' || $data['category_id']=='176984'
					|| $data['category_id']=='261186' || $data['category_id']=='280' || $data['category_id']=='80135'
					|| $data['category_id']=='73329' 
					|| $data['category_id']=='149960'  || $data['category_id']=='14962' || $data['category_id']=='149959'
					|| $data['category_id']=='175718' || $data['category_id']=='15050'|| $data['category_id']=='25409') and $Weight>4
					){
						$shippingname='Shipping_Calculated_Media_Over_4lbs';
						$shipping_id='251970896019';
						$PriceSite=$PriceNoShipping;
					}elseif($Weight<1 && (verifierDimensions($Depth,$Height,$Width)===true)){
						//product light
						$shippingname='Shipping_Calculated_Light';
						$shipping_id='251955553019';
						$PriceSite=$PriceNoShipping;
					}elseif($Weight<1 && (verifierDimensions($Depth,$Height,$Width)===false)){
						//product light
						$shippingname='Shipping_Calculated_medium_25LBS';
						$shipping_id='251955711019';
						$PriceSite=$PriceNoShipping;
					}elseif($poids_total<25){
						//product light
						$shippingname='Shipping_Calculated_medium_25LBS';
						$shipping_id='251955711019';
						$PriceSite=$PriceNoShipping;
					}elseif($poids_total<70 ){
						$shippingname='Shipping_Calculated_Heavy_70LBS';
						$shipping_id='251932372019';
						$PriceSite=$PriceNoShipping;
					}elseif($poids_total<150 ){
						$shippingname='Shipping_Calculated_Heavy_150LBS';
						$shipping_id='251967474019';
						$PriceSite=$PriceNoShipping;
					}elseif($poids_total>=150 ){
						$shippingname='Shipping_Freight';
						$shipping_id='251967489019';
					}else{
						$shippingname='Shipping';
						$shipping_id='244970865019';
					}
					if($data['category_id']=='212' || $data['category_id']=='261332' || $condition_id==1){
						$returnname='No_Return';
						$return_id='246806570019';
					}elseif($data['category_id']=='117414'){
						$returnname='Return_Buyer_Pay';
						$return_id='233511458019';
					}elseif($poids_total<25){
						$returnname='Return';
						$return_id='244801165019';
					}elseif($poids_total<70 ){
						$returnname='Return_Buyer_Pay';
						$return_id='233511458019';
					}elseif($poids_total<150 && $poids_total>=70 ){
						$returnname='Return_Buyer_Pay';
						$return_id='233511458019';
					}elseif($poids_total>=150 ){
						$returnname='Return_Buyer_Pay';
						$return_id='233511458019';
					}else{
						$returnname='Return';
						$return_id='244801165019';
					}
					//shipping calculer par ebay
					if($PriceSite<0){
						$PriceSite=$PriceWithShipping;
					}elseif($PriceSite==0 || $PriceSite==""){
						$PriceSite=9999.99;
					}elseif($PriceSite<.99){
						$PriceSite=.99;
					}
					$result.='<StartPrice currencyID="USD">'.$PriceSite.'</StartPrice>
					<SellerProfiles>
									<SellerShippingProfile>
										<ShippingProfileID>'.$shipping_id.'</ShippingProfileID>
										<ShippingProfileName>'.$shippingname.'</ShippingProfileName>
									</SellerShippingProfile>
									<SellerReturnProfile>
										<ReturnProfileID>'.$return_id.'</ReturnProfileID>
										<ReturnProfileName>'.$returnname.'</ReturnProfileName>
									</SellerReturnProfile>
									<SellerPaymentProfile>
										<PaymentProfileID>135483622019</PaymentProfileID>
										<PaymentProfileName>PayPal</PaymentProfileName>
									</SellerPaymentProfile>
								</SellerProfiles>';
				}

				if($GLOBALS['NAME_CIE']=='PhoenixSupplies'){
					$com="COM_";
				}else{
					$com="";
				}
		//	echo 'Shorty:'.$shorty;
			//	$tot_check_weight=$Depth+$Height+$Width;
			//	echo "Le poids tot chek est de : " . $poids_total . " lbs VS ".$tot_check_weight."<br>";
			//	echo "Le poids volumique nucléaire est de : " . $poids_total . " lbs VS ".$Weight."<br>";
								$result.='
								<ShippingPackageDetails>
								  <MeasurementUnit>English</MeasurementUnit>
				 				  <PackageDepth>'.$Depth.'</PackageDepth>
								  <PackageLength>'.$Height.'</PackageLength>
								  <PackageWidth>'.$Width.'</PackageWidth>
								  <WeightMajor>'.$WeightTot[0].'</WeightMajor>
								  <WeightMinor>'.$WeightOZ.'</WeightMinor> 
								</ShippingPackageDetails>';				
								//$result.=$xmlSellerProfiles;
								$result.='<SKU>'.$com.$CustomLabel.'</SKU>
								';
			//	echo $result;
		return $result;
		//print("<pre>".print_r ($result,true )."</pre>");
	}
function convert_smart_quotes($string) 
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

function get_ups_rate($connectionapi,$weight,$length,$width,$height,$zipdestination){
		  $access = 'DD9F9AE20FFC7DD5';
		  $userid = 'jonathangervais';
		  $passwd = 'jnthngrvs01$$';
		  $wsdl ="/home/n7f9655/public_html/phoenixliquidation/interne/RateWS.wsdl";
		  $operation = "ProcessRate";
		  $endpointurl = 'https://onlinetools.ups.com/webservices/Rate';
		 // $outputFileName = "XOLTResult.xml";
		 // $connectionapi['APIUPSURL']='https://www.ups.com/ups.app/xml/Rate'; 
					$weight = ($weight < 0.1 ? 0.1 : $weight);
					$pounds = floor($weight);
					$ounces = round(16 * ($weight - $pounds), 2);
	  $option['RequestOption'] = 'Shop';
      $request['Request'] = $option;
//echo $weight;
      $pickuptype['Code'] = '01';
      $pickuptype['Description'] = 'Daily Pickup';
      $request['PickupType'] = $pickuptype;
      $customerclassification['Code'] = '01';
      $customerclassification['Description'] = 'Classfication';
      $request['CustomerClassification'] = $customerclassification;
      $shipper['Name'] = 'PhoenixLiquidation';
      //$shipper['ShipperNumber'] = '222006';
      $address['AddressLine'] = array
      (
          '100 Walnut ST'
      );
      $address['City'] = 'Champlain';
      $address['StateProvinceCode'] = 'NY';
      $address['PostalCode'] = '12919';
      $address['CountryCode'] = 'US';
      $shipper['Address'] = $address;
      $shipment['Shipper'] = $shipper;
      $shipto['Name'] = 'PhoenixLiquidation';
/*       $addressTo['AddressLine'] = '1647 E 53rd St';
      $addressTo['City'] = 'Los Angeles';
      $addressTo['StateProvinceCode'] = 'CA'; */
     // $addressTo['PostalCode'] = '90011';
	  $addressTo['PostalCode'] = $zipdestination;
      $addressTo['CountryCode'] = 'US';
      $addressTo['ResidentialAddressIndicator'] = '';
      $shipto['Address'] = $addressTo;
      $shipment['ShipTo'] = $shipto;
      $shipfrom['Name'] = 'PhoenixLiquidation';
      $addressFrom['AddressLine'] = array
      (
          '100 Walnut ST'
      );
      $addressFrom['City'] = 'Champlain';
      $addressFrom['StateProvinceCode'] = 'NY';
      $addressFrom['PostalCode'] = '12919';
      $addressFrom['CountryCode'] = 'US';
      $shipfrom['Address'] = $addressFrom;
      $shipment['ShipFrom'] = $shipfrom;
      $service['Code'] = '03';
      $service['Description'] = 'Service Code';
      $shipment['Service'] = $service;
      $packaging1['Code'] = '02';
      $packaging1['Description'] = 'Rate';
      $package1['PackagingType'] = $packaging1;
      $dunit1['Code'] = 'IN';
      $dunit1['Description'] = 'inches';
      $dimensions1['Length'] = intval($length) ;
      $dimensions1['Width'] = intval($width);
      $dimensions1['Height'] = intval($height);
      $dimensions1['UnitOfMeasurement'] = $dunit1;
      $package1['Dimensions'] = $dimensions1;
      $punit1['Code'] = 'LBS';
      $punit1['Description'] = 'Pounds';
      $packageweight1['Weight'] = $weight;
      $packageweight1['UnitOfMeasurement'] = $punit1;
      $package1['PackageWeight'] = $packageweight1;
      $shipment['Package'] = array(	$package1 /* , $package2 */ );
      $shipment['ShipmentServiceOptions'] = '';
      $shipment['LargePackageIndicator'] = '';
      $request['Shipment'] = $shipment;
    //echo "Request.......\n";
     // print_r($request);
	  //print("<pre>".print_r ($request,true )."</pre>");
     //echo "\n\n";
     // return $request;
  try
  {
    $mode = array
    (
         'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
         'trace' => 1
    );
    // initialize soap client
  	$client = new SoapClient($wsdl , $mode);
  	//set endpoint url
  	$client->__setLocation($endpointurl);    //create soap header
    $usernameToken['Username'] = $userid;
    $usernameToken['Password'] = $passwd;
    $serviceAccessLicense['AccessLicenseNumber'] = $access;
    $upss['UsernameToken'] = $usernameToken;
    $upss['ServiceAccessToken'] = $serviceAccessLicense;
    $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$upss);
    $client->__setSoapHeaders($header);    //get response
  	$resp = $client->__soapCall($operation ,array($request));
	$rated_shipments = $resp->RatedShipment;
	//print("<pre>".print_r ($resp,true )."</pre>");
			//		$cost=0;
					foreach ($rated_shipments as $key =>$value) {
						if($value->Service->Code == '03'){
						//	//print("<pre>".print_r ($resp,true )."</pre>");
							$Postagecom = number_format($value->TransportationCharges->MonetaryValue * 0.73, 2, '.', '');
						}
					}
  }
  catch(Exception $ex)
  {
  	print_r ($ex);
	//print("<pre>".print_r ($ex,true )."</pre>");
  }
  return $Postagecom;
}
function get_shipping($connectionapi, $weight, $length, $width, $height, $db, $upc, $zipdestination,$category = "",$data_exist = null) {
    $status = true;
	if(isset($data_exist)){
		$Postagecom=$data_exist['UPS_com'];
		$PostageUSPS=$data_exist['USPS'];
		$PostagecomUSPS=$data_exist['USPS_com'];
	//	echo "Deja Shipping <br>";
	}else{
		if (!isset($zipdestination))
			$zipdestination = 90011;
		$Postagecom = get_ups_rate($connectionapi, $weight, $length, $width, $height, $zipdestination);
		$status = true;
		if ($weight > 70) {
			$status = false;
		}
		$quote_data = array();
		if ($status) {
		// $service = ($weight >= 1) ? '<Service>Priority Commercial</Service>' : '<Service>Ground Advantage</Service>';
			$service ='<Service>GROUND ADVANTAGE COMMERCIAL</Service>';
			$weight = ($weight < 0.1 ? 0.1 : $weight);
			$pounds = floor($weight);
			$ounces = round(16 * ($weight - $pounds), 2);
			$postcode = str_replace(' ', '', 12919);
			$xml  = '<RateV4Request USERID="' . $connectionapi['APIUSPSUSERID'] . '">';
			$xml .= '    <Package ID="1">';
			$xml .= $service;
			$xml .= '        <ZipOrigination>12919</ZipOrigination>';
			$xml .= '        <ZipDestination>' . $zipdestination . '</ZipDestination>';
			$xml .= '        <Pounds>' . $pounds . '</Pounds>';
			$xml .= '        <Ounces>' . $ounces . '</Ounces>';
			$xml .= '        <Container>VARIABLE</Container>';
			$xml .= '        <Size>Regular</Size>';
			$xml .= '        <Width>' . $width . '</Width>';
			$xml .= '        <Length>' . $length . '</Length>';
			$xml .= '        <Height>' . $height . '</Height>';
			$xml .= '        <Girth></Girth>';
			$xml .= '        <Machinable>false</Machinable>';
			$xml .= '    </Package>';
			$xml .= '</RateV4Request>';
			$request = 'API=RateV4&XML=' . urlencode($xml);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $connectionapi['APIUSPSURL'] . $request);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
				// Handle cURL error
			} else {
				$new = simplexml_load_string($result);
				$result = json_encode($new);
				$json = json_decode($result, true);
				$PostageUSPS = $json["Package"]["Postage"]["CommercialRate"];
			//    $PostagecomUSPS = (isset($json["Package"]["Postage"]["CommercialRate"]))? $json["Package"]["Postage"]["CommercialRate"] : null;
			//	//print("<pre>".print_r ($json,true )."</pre>");
			}
			if ($category=='617' || $category=='51071' || $category=='176984'
						|| $category=='261186' || $category=='280' || $category=='80135'
						|| $category=='73329' 
						|| $category=='149960'  || $category=='14962' || $category=='149959'
						|| $category=='175718') {
				$service = '<Service>Media Mail</Service>';
				$carrier2 = 'USPS Media Mail';
			} else {
				$service = '<Service>Priority Commercial</Service>';
				$carrier2 = 'USPS Priority Commercial';
			}
		//	$service ='<Service>Priority Commercial</Service>' ;
			$weight = ($weight < 0.1 ? 0.1 : $weight);
			$pounds = floor($weight);
			$ounces = round(16 * ($weight - $pounds), 2);
			$postcode = str_replace(' ', '', 12919);
			$xml  = '<RateV4Request USERID="' . $connectionapi['APIUSPSUSERID'] . '">';
			$xml .= '    <Package ID="1">';
			$xml .= $service;
			$xml .= '        <ZipOrigination>12919</ZipOrigination>';
			$xml .= '        <ZipDestination>' . $zipdestination . '</ZipDestination>';
			$xml .= '        <Pounds>' . $pounds . '</Pounds>';
			$xml .= '        <Ounces>' . $ounces . '</Ounces>';
			$xml .= '        <Container>VARIABLE</Container>';
			$xml .= '        <Size>Regular</Size>';
			$xml .= '        <Width>' . $width . '</Width>';
			$xml .= '        <Length>' . $length . '</Length>';
			$xml .= '        <Height>' . $height . '</Height>';
			$xml .= '        <Girth></Girth>';
			$xml .= '        <Machinable>false</Machinable>';
			$xml .= '    </Package>';
			$xml .= '</RateV4Request>';
			$request = 'API=RateV4&XML=' . urlencode($xml);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $connectionapi['APIUSPSURL'] . $request);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
				// Handle cURL error
			} else {
				$new = simplexml_load_string($result);
				$result = json_encode($new);
				$json = json_decode($result, true);
				if($service == '<Service>Media Mail</Service>'){
					$PostagecomUSPS = $json["Package"]["Postage"]["Rate"];
				}else{
					$PostagecomUSPS = (isset($json["Package"]["Postage"]["CommercialRate"]))? $json["Package"]["Postage"]["CommercialRate"] : null;
				}
			//	//print("<pre>".print_r ($json,true )."</pre>");
			}
		}
		$sql = "UPDATE `oc_product` SET `USPS` = '" . ($PostageUSPS + 0) . "',`USPS_com` = '" . ($PostagecomUSPS + 0) . "',`UPS_com` = '" . ($Postagecom + 0) . "'  WHERE `upc` LIKE '" . $upc . "'";
		$req = mysqli_query($db, $sql);
		if ($Postagecom == "") $Postagecom = 9999;
		if ($PostagecomUSPS == "") $PostagecomUSPS = 9999;
}
    $frais_shipping = 9999;
    $carrier = '';
    $other = '';
    if ($PostageUSPS > 0 && $PostageUSPS < $PostagecomUSPS && ($PostageUSPS < $Postagecom)) {
        $frais_shipping = $PostageUSPS;
        $carrier = 'USPS Ground ADV';
        $other = $Postagecom;
    } elseif ($Postagecom == 9999 && $PostagecomUSPS == 9999) {
        $frais_shipping = 9999;
    } elseif ($PostagecomUSPS > 0 && ($PostagecomUSPS < $PostageUSPS)) {
        $frais_shipping = $PostagecomUSPS;
        $carrier = $carrier2;
        $other = $Postagecom;
    } elseif ($PostageUSPS > 0 && ($PostageUSPS < $Postagecom)) {
        $frais_shipping = $PostageUSPS;
        $carrier = 'USPS';
        $other = $Postagecom;
    } elseif ($PostagecomUSPS > 0 && ($PostagecomUSPS < $Postagecom)) {
        $frais_shipping = $PostagecomUSPS;
        $carrier = 'USPS2';
        $other = $Postagecom;
    } elseif ($PostagecomUSPS > 0 && $Postagecom < $PostagecomUSPS) {
        $frais_shipping = $Postagecom;
        $carrier = 'UPS';
        $other = $PostagecomUSPS;
    }
    return array(
        'shipping' => $frais_shipping,
        'carrier' => $carrier,
        'other' => $other
    );
}
function get_shippingOLDGPT ($connectionapi,$weight,$length,$width,$height,$db,$upc,$zipdestination) {
	//trouver frais UPS
					$status = true;
					if(!isset($zipdestination))
							$zipdestination=90011;
					//'90011' california
			//echo $weight;
				// 70 pound limit
			//echo $status;
			$Postagecom=get_ups_rate($connectionapi,$weight,$length,$width,$height,$zipdestination);
		//print("<pre>".print_r ($resp,true )."</pre>");
		//calcul frais USPS
				//$weight = .75;
				$status = true;
			//echo $weight;
				// 70 pound limit
				if ($weight > 70) {
					$status = false;
				}
			//echo $status;
				$method_data = array();
				if ($status) {
//echo "allo";
					$quote_data = array(); 
					if($weight >= 1) {
						$service='		<Service>Priority Commercial</Service>';
					}else{
						$service='		<Service>First Class Commercial</Service>
										<FirstClassMailType>PACKAGE SERVICE RETAIL</FirstClassMailType>';
					}
					$weight = ($weight < 0.1 ? 0.1 : $weight);
					$pounds = floor($weight);
					$ounces = round(16 * ($weight - $pounds), 2); // max 5 digits
					//echo $weight."<br>";
					$postcode = str_replace(' ', '', 12919);
						$xml  = '<RateV4Request USERID="'.$connectionapi['APIUSPSUSERID'].'">';
						$xml .= '	<Package ID="1">';
						//$xml .=	'		<Service>First-Class Package Service - Retail</Service>';
						$xml .=	$service;
						$xml .=	'		<ZipOrigination>12919</ZipOrigination>';
						$xml .=	'		<ZipDestination>'.$zipdestination.'</ZipDestination>';
						$xml .=	'		<Pounds>' . $pounds . '</Pounds>';
						$xml .=	'		<Ounces>' . $ounces . '</Ounces>';
						$xml .=	'		<Container>VARIABLE</Container>';
						$xml .=	'		<Size>Regular</Size>';
						$xml .= '		<Width>' . $width . '</Width>';
						$xml .= '		<Length>' . $length . '</Length>';
						$xml .= '		<Height>' . $height . '</Height>';
						// Calculate girth based on usps calculation
						$xml .= '		<Girth></Girth>';
						$xml .=	'		<Machinable>false</Machinable>';
						$xml .=	'	</Package>';
						$xml .= '</RateV4Request>';
						//echo $xml."<br><br>";
						$request = 'API=RateV4&XML=' . urlencode($xml);
						$curl = curl_init();
						curl_setopt($curl, CURLOPT_URL, $connectionapi['APIUSPSURL'] . $request);
						curl_setopt($curl, CURLOPT_HEADER, 0);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
						$result = curl_exec($curl);
						$err = curl_error($curl);
						curl_close($curl);
/* 						$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
						$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
						$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
						$result = str_replace('**', '', $result);
						$result = str_replace("\r\n", '', $result);
						$result = str_replace('\"', '"', $result); */
						if ($err) {
							//echo "cURL Error #:" . $err;
						} else {
							// Convert xml string into an object 
							//echo $result."\nallo";
							$new = simplexml_load_string($result);   
							// Convert into json 
							$result = json_encode($new); 
							//echo $result."\nallo"."<br>";
							$json = json_decode($result, true);
							//echo (round(((float)$width + ((float)$length * 2) + (float)$height * 2), 1));
							//print("<pre>".print_r ($json,true )."</pre>");
							//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
							$PostageUSPS=$json["Package"]["Postage"]["Rate"];
							$PostagecomUSPS=$json["Package"]["Postage"]["CommercialRate"];
							//$encodedSesssionIDString =rawurlencode ($sessionId);
							//echo $encodedSesssionIDString;
						}
				}
				//`UPS` = '0".$Postage."',
		$sql = "UPDATE `oc_product` SET `USPS` = '".($PostageUSPS+0)."',`USPS_com` = '".($PostagecomUSPS+0)."',`UPS_com` = '".($Postagecom+0)."'  WHERE `upc` LIKE '".$upc."'";
		//echo $sql."<br>"."<br>";
		$req = mysqli_query($db,$sql) ;
		if($Postagecom=="")$Postagecom=9999;
		if($PostagecomUSPS=="")$PostagecomUSPS=9999;
		//if($Postage=="")$Postage=9999;
		//echo "<br>USPCOM:".$Postagecom;
		//echo "<br>USPS:".$PostagecomUSPS;
			if($PostageUSPS>0 && $PostageUSPS< $PostagecomUSPS && ($PostageUSPS< $Postagecom)){
				$frais_shipping=$PostageUSPS;
			}elseif($Postagecom==9999 && $PostagecomUSPS==9999){
				$frais_shipping=9999;
			}elseif($PostagecomUSPS>0 && ($PostagecomUSPS< $PostageUSPS)){
				$frais_shipping=$PostagecomUSPS;
				$carrier='USPS_COM';
				$other=$Postagecom;
			}elseif($PostageUSPS>0 && ($PostageUSPS< $Postagecom)){
				$frais_shipping=$PostageUSPS;
				$carrier='USPS';
				$other=$Postagecom;
			}elseif($PostagecomUSPS>0 && ($PostagecomUSPS< $Postagecom)){
				$frais_shipping=$PostagecomUSPS;
				$carrier='USPS2';
				$other=$Postagecom;
			}elseif($PostagecomUSPS>0 && $Postagecom< $PostagecomUSPS){
				$frais_shipping=$Postagecom;
				$carrier='UPS';
				$other=$PostagecomUSPS;
			}
	return array(
	'shipping' =>$frais_shipping,
	'carrier' 		=>$carrier,
	'other'			=>$other
	);
}
function end_to_ebay($connectionapi,$ebay_id) {
		//echo $updquantity."allo".$ebay_id;
		$post = '<?xml version="1.0" encoding="utf-8"?>
				<EndItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
					
					<ErrorLanguage>en_US</ErrorLanguage>
					<WarningLevel>High</WarningLevel>
					<EndingReason>Incorrect</EndingReason>
					<ItemID>'.$ebay_id.'</ItemID>
				</EndItemRequest>'; 
		$headers = array(
					"X-EBAY-API-COMPATIBILITY-LEVEL: 1077",
					"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
					"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
					"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
					"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
					"X-EBAY-API-CALL-NAME: EndItem",
					"X-EBAY-API-SITEID: 0" // 3 for UK
		);
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1); 
		$response = curl_exec($connection);
		curl_close($connection);
		$xml = new SimpleXMLElement($response);
		//echo $post."allo"; 
	}
	function relist_to_ebay($connectionapi,$product_id,$ebay_id,$db) {
		$Date = date('Y-m-d');
		//echo $ebay_id;
		//echo $updquantity."allo".$ebay_id;
		$post = '<?xml version="1.0" encoding="utf-8"?>
				<RelistItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
					
					<ErrorLanguage>en_US</ErrorLanguage>
					<WarningLevel>High</WarningLevel>
					<Item>
					<ItemID>'.$ebay_id.'</ItemID>
					</Item>
				</RelistItemRequest>'; //<EndingReason>NotAvailable</EndingReason>
		$headers = array(
					"X-EBAY-API-COMPATIBILITY-LEVEL: 1077",
					"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
					"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
					"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
					"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
					"X-EBAY-API-CALL-NAME: RelistItem",
					"X-EBAY-API-SITEID: 0" // 3 for UK
		);
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($connection);
			$err = curl_error($connection);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('**', '', $result);
			$result = str_replace("\r\n", '', $result);
			$result = str_replace('\"', '"', $result);
			if ($err) {
				//echo "cURL Error #:" . $err;
			} else {
				// Convert xml string into an object 
				//echo $result."\nallo";
				$new = simplexml_load_string($result);  
				// Convert into json 
				$result = json_encode($new); 
				$textoutput=str_replace("}","<br><==<br>",$result);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				//echo $textoutput."\nallo"."<br>";
				$json = json_decode($result, true);
			//print("<pre>".print_r ($json,true )."</pre>");
				$ebay_quantity=$json["Item"]["Quantity"];
				$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
				$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
				//echo $ebay_quantity."---".$Quantity_sold;
				//$encodedSesssionIDString =rawurlencode ($sessionId);
				//echo $encodedSesssionIDString;
			}
			$error_ebay="";
			if($json["Ack"]=="Failure"){
				foreach($json["Errors"] as $error){
					if($error['SeverityCode']=="Error"){
						$error_ebay.=$error['ShortMessage']."<br>";
					}
				}
				$ebayresult=get_ebay_product($connectionapi,$ebay_id);
	//print("<pre>".print_r ($ebayresult,true )."</pre>");
	//$result_upctemp=get_from_upctemp($post['upc']);
			}
				$ebay_id_old=$ebay_id;
				$ebay_id=$json["ItemID"];
			//echo $ebay_id;
				$sql2 = 'UPDATE `oc_product`SET ebay_id="'.$ebay_id.'",ebay_id_old="'.$ebay_id_old.'",ebay_date_relisted="'.
				$Date.'",error_ebay="'.$error_ebay.'" ,ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ="'.$product_id.'"';
			//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
	}
function update_item_db($connectionapi,$post,$db){
//	echo '<br>update_item_db:';
	$ebay_id_t='';
//	//print("<pre>".print_r ($post,true )."</pre>");

	//	if ($post['manufacturer_id']!="" && $post['manufacturersupp']=="")$post['manufacturer_id']=$post['manufacturer_id'];
 		if (isset($post['manufacturersupp']) && $post['manufacturersupp']!=""){
			$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.capitalizeWords($post['manufacturersupp']).'")';
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
			$post['manufacturer_id']= mysqli_insert_id($db);
			$sql2 = 'INSERT INTO `oc_manufacturer_to_store` (`manufacturer_id`) VALUES ("'.$post['manufacturer_id'].'")';
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
			//echo $post['manufacturer_id'];
			$post['manufacturersupp']="";
		}
		if ($post['manufacturer_recom']!=""){
			$post['manufacturer_id']=$post['manufacturer_recom'];
		} 
 			if (isset($post['category_id']) && $post['category_id']!=""){
					//echo "allo";
/* 					$sql3 = 'SELECT product_id FROM `oc_product` where product_id = "'.$post['product_id'] .'"';
					$req3 = mysqli_query($db,$sql3);
					while($data3 = mysqli_fetch_assoc($req3)){ */
								$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category_description.language_id=1 and oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$post['category_id'];
					//echo $sql."<br>";
								$req = mysqli_query($db,$sql);
								$data = mysqli_fetch_assoc($req);
							//	$categoryname=$data['name'];
								//echo $categoryname;
								//echo $data['parent_id'];
								if($data['parent_id']!=""){
									$sql5 = 'delete FROM `oc_product_to_category` where product_id = "'.$post['product_id'] .'"';
									mysqli_query($db,$sql5);
									while($data['parent_id']>0){
										$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$post['product_id']."', '".$data['category_id']."')";
										//echo $sql2."<br>";
										$req2 = mysqli_query($db,$sql2);
										$parent_id=$data['parent_id'];
										$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category_description.language_id=1 and oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$data['parent_id'];
							//echo $sql."<br>";
										$req = mysqli_query($db,$sql);
										$data = mysqli_fetch_assoc($req);
									}
									if($data['category_id']!=''){
										$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$post['product_id']."', '".$data['category_id']."')";
									//echo $sql2."<br>";
										$req2 = mysqli_query($db,$sql2);
									}else{
										header("location: ajoutcategorie.php?product_id=".$post['product_id']."&numcat=".$parent_id."&primary_cat=".$post['category_id']."&hhour=".$post['hhour']."&hmin=".$post['hmin']."&hsec=".$post['hsec']);  
										exit();
									}
								}else{
									header("location: ajoutcategorie.php?product_id=".$post['product_id']."&numcat=".$post['category_id']."&hhour=".$post['hhour']."&hmin=".$post['hmin']."&hsec=".$post['hsec']);  
									exit();
								}
						
			} 
			
										$sql2 = 'UPDATE `oc_product_description` SET `description_supp`="'.addslashes($post['description_suppen']).'",`color`="'.addslashes(capitalizeWords($post['coloren'])).'",`description_mod`=1,`name`="'.addslashes(capitalizeWords(strtolower($post['nameen']))).'",`condition_supp`="'.addslashes($post['condition_suppen']).'",`accessory`="'.addslashes($post['accessoryen']).'",`test`="'.addslashes($post['testen']).'" WHERE `language_id`=1 and `product_id` ='.$post['product_id'];
									//echo $sql2."<br>";
										$req2 = mysqli_query($db,$sql2); 
										$sql2 = 'UPDATE `oc_product_description` SET `description_supp`="'.addslashes($post['description_suppfr']).'",`color`="'.addslashes(capitalizeWords($post['colorfr'])).'",`description_mod`=1,`name`="'.addslashes(capitalizeWords(strtolower($post['namefr']))).'",`condition_supp`="'.addslashes($post['condition_suppfr']).'",`accessory`="'.addslashes($post['accessoryfr']).'",`test`="'.addslashes($post['testfr']).'" WHERE `language_id`=2 and `product_id` ='.$post['product_id'];
                                 //   echo $sql2."<br>"; 
										$req2 = mysqli_query($db,$sql2); 
										mise_en_page_description($connectionapi,$post['product_id'],$db); 
										//echo $post['marketplace_item_id'];
										/* if ($post['processing']=="oui"){ */
										/*	if($post['marketplace_item_id']>1)
											{
												$result=revise_ebay_product($connectionapi,$post['marketplace_item_id'],$post['product_id'],"non",$db,"non");
												//$result = json_encode($new); 
											//echo "allo";
												//$result=revise_ebay_product($connectionapi,$post['marketplace_item_id'],$post['product_id'],$updquantity,$db);
												$json = json_decode($result, true); 
												//echo "<br>mise a jour<br>";
												$resultebay="";
												if($post['showerror']=="oui")//print("<pre>".print_r ($json,true )."</pre>");
												if($json["Ack"]=="Failure"){
													$resultebay.="ERREUR: ".$json["Errors"]["ShortMessage"];
													//print("<pre>".print_r ($json,true )."</pre>");
												}elseif($json["Ack"]=="Warning"){
													//$resultebay.="WARNING: ".$json["Errors"]["ShortMessage"];
												}
											}*/
											//print("<pre>".print_r ($post,true )."</pre>");
											/* if($post['processing']=="oui"){
													header("location: listing.php?sku=".$post['sku']."&hhour=".$post['hhour']."&hmin=".$post['hmin']."&hsec=".$post['hsec']);  
													exit();
											} */
										/* } */
		if($post['weight2']<16&&$post['weight']>0){
			$weight=$post['weight']+($post['weight2']/16);
		}elseif($post['weight2']==16){
			$weight=.999999;
		}else{
			$weight=$post['weight2']/16;
		}
		if($post['manufacturer_id']=="")
			$post['manufacturer_id']=0;
		if($post['marketplace_item_id']>0)
			$ebay_id_t=',`ebay_id`="'.$post['marketplace_item_id'].'"';
		if($post['price_with_shipping']>0){
			$price_with_shipping_t=',`price_with_shipping`="'.$post['price_with_shipping'].'"';
			$price_t=',`price`="0'.$post['price'].'"';
			}
		//echo $weight;
		$sql2 = 'UPDATE `oc_product` SET `model`="'.htmlspecialchars_decode(capitalizeWords($post['model'])).'", `upc`="'.(string)$post['upc'].'", `sku`="'.(string)$post['sku'].'"';
		$sql2 .=', `mpn` = "'.htmlspecialchars_decode(capitalizeWords($post['model'])).'",`manufacturer_id` = "'.$post['manufacturer_id'].'"';
		$sql2 .=', `weight`="'.$weight.'",`height`="'.$post['height'].'", `condition_id`="'.$post['condition_id'].'"';
		$sql2 .=', `width`="'.$post['width'].'",`length`="'.$post['length'].'"'.$ebay_id_t.$price_with_shipping_t.$price_t.',`remarque_interne`="'.htmlspecialchars_decode($post['remarque_interne']);
	//	//print("<pre>".print_r ($ebay_id_t,true )."</pre>");

		/* 		if($_GET['clone']==""){
			$sql2 .='" WHERE `oc_product`.`sku` ="'.substr((string)$post['sku'] ,0,12).'" or `oc_product`.`sku` ="'.substr((string)$post['sku'] ,0,12).'no" or `oc_product`.`sku` ="'.substr((string)$post['sku'] ,0,12).'r"';
		}else{ */
			$sql2 .='" ,ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ="'.$post['product_id'] .'"';
/* 		} */
//echo $sql2.'<br><br>';
		//echo $sql2.'<br><br>';	  
		$req2 = mysqli_query($db,$sql2); 
		//mettre a jour la description recu de algopix
}
function insert_item_db($connectionapi,$post,$db) 
//($result,$result_upctemp,$post,$db);
//($post['upc'],,$result,$result_upctemp,$post['marketplace_item_id'],,$condition_id,$post['name'],$post,$db) 
	{
	//	echo '<br>insert_item_db:';
		//print("<pre>".print_r ($post,true )."</pre>");
		if($post['condition_id']==""){
			$etat=explode(",",$post['etat']);
			$condition_id=$etat[0];
		}else{
			$condition_id=$post['condition_id'];
		}
		if(isset($post['sku'])){
			$sku=$post['sku'];
		}else{
			$sku_fin=$etat[1];
			$sku=substr ((string)$post['upc'],0,12).$sku_fin;
		}
		if(!isset($post['upc'])){
			$post['upc']="DoesNotApply";
		}
		//print_r($post);
			$category_id=$post['category_id'];
			//if ($post['name']=="")$post['name']=addslashes($json["Item"]["Title"]);
			$weight=$post['weight'];
			$weight2=$post['weight2'];
			$weight=$weight+($weight2/16);
			//$result=get_from_upctemp($post['upc']);
			$model=$post['model'];
			$brand=$post['manufacturer_id'];
		$ebay_date_relisted = new DateTime('now');
		$ebay_date_relisted = $ebay_date_relisted->format('Y-m-d');
		$price=0;
		$price_with_shipping=0;
		//$post['price']=0;
		//$post['priceusd']=0;
		if (count($post)>0){
			if($post['price']>0){
				$price=number_format($post['price']/1.34, 2,'.', '');
			}else{
				$price=$post['priceusd'];
			}
			if($post['price_with_shipping']>0)
				$price_with_shipping=$post['price_with_shipping'];
		}
		if(isset($post['quantity'])){
			$quantity=$post['quantity'];
		
		}else{
			$quantity=0;
			$location="";
		}
		$sql2 = 'INSERT INTO `oc_product` SET 
				`stock_status_id` = 7,
				`tax_class_id` = 9,
				`date_available` = NOW(),
				`ebay_id` = "'.$post['marketplace_item_id'].'",
				`ebay_date_relisted` = "'.$ebay_date_relisted.'",
				`model` = "'.$model.'",
				`upc` = "'.(string)$post['upc'].'",
				`mpn` = "'.$model.'",
				`quantity` = "'.$quantity.'",
				`manufacturer_id` = "'.$post['manufacturer_id'].'",
				`status` = 0,
				`condition_id` = "'.$condition_id.'",
				`weight` = "'.$weight.'",
				`weight_class_id` = 5,
				`price` = "'.$post['price'].'",
				`price_with_shipping` = "'.$post['price_with_shipping'].'",
				`length` = "'.$post['length'].'",
				`width` = "'.$post['width'].'",
				`height` = "'.$post['height'].'",
				`length_class_id` = 3,
				`location` = "'.$location.'";
			';
//	echo $sql2.'<br><br>';  
		$req2 = mysqli_query($db,$sql2);
		$product_id= mysqli_insert_id($db);
 		
		//echo $post['categoryarbonum'];
		// entree les category_id
			$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$category_id;
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			//$post['name']=$data['name'];
			//echo $data['parent_id'];
			while($data['parent_id']>0){
				$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$product_id."', '".$data['category_id']."')";
				//echo $sql2."<br>";
				$req2 = mysqli_query($db,$sql2);
				$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$data['parent_id'];
	//echo $sql;
				$req = mysqli_query($db,$sql);
				$data = mysqli_fetch_assoc($req);
			}
				$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$product_id."', '".$data['category_id']."')";
				//echo $sql2."<br>";
				$req2 = mysqli_query($db,$sql2);
		//echo $sql."<br>";
		$nameen = isset($post['nameen']) ? addslashes(capitalizeWords($post['nameen'])) : '';
		$coloren = isset($post['coloren']) ? $post['coloren'] : '';
		$description_adden = isset($post['description_adden']) ? addslashes($post['description_adden']) : '';
		$condition_suppen = isset($post['condition_suppen']) ? addslashes($post['condition_suppen']) : '';

		$namefr = isset($post['namefr']) ? addslashes(capitalizeWords($post['namefr'])) : '';
		$colorfr = isset($post['colorfr']) ? $post['colorfr'] : '';
		$description_addfr = isset($post['description_addfr']) ? addslashes($post['description_addfr']) : '';
		$condition_suppfr = isset($post['condition_suppfr']) ? addslashes($post['condition_suppfr']) : '';
		$codesource =  isset($post['codesource']) ? addslashes($post['codesource']) : '';

		// Insert English product description
		$sql = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title`, `language_id`, `color`, `description_supp`, `condition_supp`)
				VALUES ('$product_id', '$nameen', '$nameen', '1', '$coloren', '$description_adden', '$condition_suppen')";
		$req = mysqli_query($db, $sql);
		if (!$req) {
			printf("Error: %s\n", mysqli_error($db));
		}

		// Insert French product description
		$sql = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title`, `language_id`, `color`, `description_supp`, `condition_supp`)
				VALUES ('$product_id', '$namefr', '$namefr', '2', '$colorfr', '$description_addfr', '$condition_suppfr')";
		$req = mysqli_query($db, $sql);
		if (!$req) {
			printf("Error: %s\n", mysqli_error($db));
		}
		//echo $sql."<br>";
		$sql = "INSERT INTO `oc_product_to_store` (`product_id`) VALUES ('".$product_id."')";
		$req = mysqli_query($db,$sql);
		//echo $sql."<br>";
		link_to_download($connectionapi,$product_id,$post['marketplace_item_id'],$codesource,$db);
	//echo "<br>dans insert_item_db:".$product_id."---".$post['marketplace_item_id'];
		mise_en_page_description($connectionapi,$product_id,$db);
		return $product_id;
}
function insert_item($upc,$sku_fin,$result,$result_upctemp,$ebay_id,$product_id_princ,$condition_supp,$name,$post,$db) 
	{
		//print_r($post);
			$json = json_decode($result, true);
			$result_upctemp=json_decode($result_upctemp, true);
			$category_id=$json["Item"]["PrimaryCategory"]["CategoryID"];
			if ($name=="")$name=addslashes($json["Item"]["Title"]);
			$weight=$json["Item"]["ShippingDetails"]["CalculatedShippingRate"]["WeightMajor"];
			$weight2=$json["Item"]["ShippingDetails"]["CalculatedShippingRate"]["WeightMinor"];
			$weight=$weight+($weight2/16);
			//$result=get_from_upctemp($upc);
			$model=$result_upctemp['items'][0]['model'];
			$brand=$result_upctemp['items'][0]['brand'];
			$description_add=$result_upctemp['items'][0]['description'];
			$color=$result_upctemp['items'][0]['color'];
			if($brand!=""){
				$sql2 = 'SELECT * FROM `oc_manufacturer` where name like "%'.$brand.'%"';
				// on envoie la requête
				$req2 = mysqli_query($db,$sql2);
				$data2 = mysqli_fetch_assoc($req2);
				if($data2['manufacturer_id']=="")$data2['manufacturer_id']=0;
				$manufacturer_id=$data2['manufacturer_id'];
			}else{
				$manufacturer_id=0;
			}
		$ebay_date_relisted = new DateTime('now');
		$ebay_date_relisted = $ebay_date_relisted->format('Y-m-d');
		$price=0;
		$price_with_shipping=0;
		if (count($post)>0){
			if($post['price']>0){
				$price=number_format($post['price']/1.34, 2,'.', '');
			}else{
				$price=$post['priceusd'];
			}
			if($post['price_with_shipping']>0)$price_with_shipping=$post['price_with_shipping'];
		}
		$sql2 = 'INSERT INTO `oc_product` (`stock_status_id`,`usa`,tax_class_id,date_available,ebay_id,ebay_date_relisted,`model`, `sku`, `upc`, `mpn`,`quantity`,`manufacturer_id`, ';
		$sql2 .='`status`, `condition_id`,weight,weight_class_id,price,price_with_shipping)';
		$sql2 .=' VALUES ("7","1","9","2001-01-01","'.$ebay_id.'","'.$ebay_date_relisted.'","'.$model.'", "'.substr ((string)$upc,0,12).$sku_fin.'", "'.(string)$upc.'", "'.$model.'","0","'.$manufacturer_id.'",';
		$sql2 .='"0", "'.$condition_supp.'","'.$weight.'","5","'.$price.'","'.$price_with_shipping.'");';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$product_id= mysqli_insert_id($db);
/* 		if($test!=""){ */
		//echo $product_id."<br>";
// image
		if($product_id_princ==0){
				$imagetmp=explode("?",$json["Item"]["PictureDetails"]["GalleryURL"]);
				$imageprincipal=upload_from_link($product_id,$imagetmp[0],1,$db);
				//echo '<br>'.$imageprincipal;
				$i=0;
				foreach  ($json["Item"]["PictureDetails"]["PictureURL"] as $image){
					$imagetmp=explode("?",$image);
					$imagesecondaire[$i]=upload_from_link($product_id,$imagetmp[0],0,$db);
					//echo '<br>'.$imagesecondaire;
					$i++;
				}
		}else{
			copy_photo_dans_db($product_id,$product_id_princ,$db);
		}
 		
		/*$sql = "INSERT INTO `oc_product_special` (`product_id`, `customer_group_id`, `priority` ,`price`,`date_start`,`date_end`) VALUES ('".$product_id."', '1', '1','0', '2018-09-01','2218-09-01')"	;	
				//echo $sql.'<br><br>';
		$req = mysqli_query($db,$sql);*/			
		//echo $post['categoryarbonum'];
		// entree les category_id
			$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$category_id;
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			//$name=$data['name'];
			//echo $data['parent_id'];
			while($data['parent_id']>0){
				$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$product_id."', '".$data['category_id']."')";
				//echo $sql2."<br>";
				$req2 = mysqli_query($db,$sql2);
				$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$data['parent_id'];
	//echo $sql;
				$req = mysqli_query($db,$sql);
				$data = mysqli_fetch_assoc($req);
			}
				$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$product_id."', '".$data['category_id']."')";
				//echo $sql2."<br>";
				$req2 = mysqli_query($db,$sql2);
/* 		$categorynametab=explode('>', $post['categoryarbonum']);
		foreach($categorynametab as $categoryname) 
		{
			//echo $categoryname;
			if ($categoryname !=""){
			$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$product_id."', '".$categoryname."')";
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			}
		} */
		//echo $sql."<br>";
		$sql = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`,`color`,`description_supp`) VALUES ('".$product_id."', '".addslashes(capitalizeWords($name))."', '".addslashes(capitalizeWords($name))."', '1','".$color."','".addslashes($description_add)."')";
		$req = mysqli_query($db,$sql);	
		//echo $sql."<br>";
		$sql = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`) VALUES ('".$product_id."', '', '', '2')";
		$req = mysqli_query($db,$sql);	
		//echo $sql."<br>";
		$sql = "INSERT INTO `oc_product_to_store` (`product_id`) VALUES ('".$product_id."')";
		$req = mysqli_query($db,$sql);
		//echo $sql."<br>";
		return $product_id;
}
function modifier_item($connectionapi,$skuachanger,$default_product_id,$typeetat,$numetat,$db,$retailprice) {
	echo '<br>modifier_item:';
			//echo $default_product_id;
			if($default_product_id=="")$default_product_id=$skuachanger;
			//info de item a modifier
 			$sql8 = "SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and `oc_product`.sku = '".$skuachanger."'";
			echo '<br>Modifier:';
			echo $sql8.'<br>';
			$req8 = mysqli_query($db,$sql8);
			$data8 = mysqli_fetch_assoc($req8); 
			$product_id= $data8['product_id'];
			$ebay_id=$data8['marketplace_item_id'];
			if($product_id==""){
				$sql6 = 'INSERT INTO `oc_product` (`weight_class_id`,`length_class_id`,`usa`,`model`, `sku`, `upc`, `mpn`,`quantity`,`manufacturer_id`, ';
				$sql6 .='`weight`, `height`, `length`,`width`,`color`,`ean`,`asin`,`tax_class_id`, `status`, `condition_id`,`invoice`,`image`,ebay_id)';
				$sql6 .=' VALUES ("7","3","1","", "'.$skuachanger.'", "", "",0,0,';
				$sql6 .='0, 0, 0, 0, "", "","",9, 0, "'.$numetat.'","","",0);';
				//echo $sql6.'<br><br>';
				$req6 = mysqli_query($db,$sql6);
				$product_id= mysqli_insert_id($db);
				//echo $product_id;
				$sql7 = "INSERT INTO `oc_product_description` (`product_id`,name,description,tag,language_id) VALUES ('".$product_id."','','','',1)";
				$req7 = mysqli_query($db,$sql7);	
				$sql7 = "INSERT INTO `oc_product_description` (`product_id`,name,description,tag,language_id) VALUES ('".$product_id."','','','',2)";
				$req7 = mysqli_query($db,$sql7);	
				//echo $sql7."<br>";
				$sql7 = "INSERT INTO `oc_product_to_store` (`product_id`) VALUES ('".$product_id."')";
				$req7 = mysqli_query($db,$sql7);	
				//echo $sql7."<br>";
			
				/*$sql8 = "INSERT INTO `oc_product_special` (`product_id`, `customer_group_id`, `priority` ,`price`,`date_start`,`date_end`) VALUES ('".$product_id."', '1', '1','0', '2018-09-01','2218-09-01')"	;	
						//echo $sql8.'<br><br>';
				$req8 = mysqli_query($db,$sql8);*/	
			}
			//$product_id=cloner_item ("OK", $typeetat,$numetat,substr($skuachanger,0,12),$default_product_id,$db);
			//info a prendre
 			$sql = "SELECT * FROM `oc_product`,`oc_product_description` where language_id=1 and oc_product.product_id=oc_product_description.product_id and `oc_product`.product_id = '".$default_product_id."'";
			//echo $sql."<br>"; 
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req); 
			$name=$data['name'];
			$sql7 = "SELECT * FROM `oc_product`,`oc_product_description` where language_id=2 and oc_product.product_id=oc_product_description.product_id and `oc_product`.product_id = '".$default_product_id."'";
			//echo $sql."<br>"; 
			$req7 = mysqli_query($db,$sql7);
			$data7 = mysqli_fetch_assoc($req7); 
			$namefr=$data7['name'];	
			if ($retailprice>0)$data["price"]=$retailprice;
			$sql2 = "UPDATE `oc_product` SET price='".$data["price"]."',`model`='".capitalizeWords($data["model"])."',`color`='".capitalizeWords($data["color"])."'";
			$sql2 .=", `mpn` = '".capitalizeWords($data["model"])."',`manufacturer_id` = '".$data["manufacturer_id"]."', image='".$data["image"]."'";
			$sql2 .=", `weight`='".$data["weight"]."',`height`='".$data["height"]."'";
			$sql2 .=", `width`='".$data["width"]."',`length`='".$data["length"];
			$sql2 .="' ,ebay_last_check='2020-09-01' WHERE product_id=".$product_id;
			//echo $sql2.'<br>';
			$req2 = mysqli_query($db,$sql2);
			$sql2 = "UPDATE `oc_product_description` SET name='".capitalizeWords(strtolower($name))."'";
			$sql2 .=" WHERE language_id=1 and product_id=".$product_id;
			//echo $sql2.'<br>';
			$req2 = mysqli_query($db,$sql2);
			$sql2 = "UPDATE `oc_product_description` SET name='".capitalizeWords(strtolower($namefr))."'";
			$sql2 .=" WHERE language_id=2 and product_id=".$product_id;
			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);
/* 			$sql7 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '183630')";
			$req7 = mysqli_query($db,$sql7); */
			//echo $sql7.'<br><br>';
			// enlever les photos
			$sql3 = 'DELETE FROM `oc_product_image` where product_id = "'.$product_id.'"';
			//echo $sql3."<br>";
			$req3 = mysqli_query($db,$sql3);
			$sql3 = 'SELECT * FROM `oc_product_image` where product_id = "'.$default_product_id.'"';
			//echo $sql3."<br>";
			$req3 = mysqli_query($db,$sql3);			while($data3 = mysqli_fetch_assoc($req3)){
				$sql7 = "INSERT INTO `oc_product_image` (`product_id`, `image`) VALUES ('".$product_id."', '".$data3['image']."')";
				$req7 = mysqli_query($db,$sql7);
				//echo $sql7."<br>";
			}
// enlever les categories
/* 			$sql3 = 'DELETE FROM `oc_product_to_category` where product_id = "'.$product_id.'"';
			//echo $sql3."<br>";
			$req3 = mysqli_query($db,$sql3); */ 
			$sql3 = 'SELECT * FROM `oc_product_to_category` where product_id = "'.$default_product_id.'"';
			//echo $sql3."<br>";
			$req3 = mysqli_query($db,$sql3);
			while($data3 = mysqli_fetch_assoc($req3)){
				$sql7 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '".$data3['category_id']."')";
				$req7 = mysqli_query($db,$sql7);
				//echo $sql7."<br>";
			}
			mise_en_page_description($connectionapi,$product_id,$db);
			if($ebay_id>1)revise_ebay_product($connectionapi,$ebay_id,$product_id,"non",$db,"non");
			//mise_en_page_description($connectionapi,$pro duct_id,$db);
}
function cloner_item ($connectionapi,$rowverif, $numetat,$skuachanger,$default_product_id,$db) {
		if($rowverif=="change_etat"){
			$requete='`p`.sku = "'.$skuachanger.'" AND condition_id="'.$numetat.'" ORDER by p.product_id LIMIT 1';
			$rowverif="OK";
		}elseif($rowverif=="OK"){
			$requete='`p`.product_id = "'.$default_product_id.'"';
		}else{
			$rowverif="erreur";
		}
		if( $rowverif=="OK" ){
			$sql = 'SELECT *,pd.color as coloren,pd.name as nameen,m.name as brand 
					FROM `oc_product` p
					JOIN `oc_product_description` pd ON (p.product_id=pd.product_id) 
					JOIN `oc_manufacturer` m ON (p.manufacturer_id=m.manufacturer_id)
					where pd.language_id=1 and '.$requete;
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req); 
			//echo $sql;
			//if($data['length']==0 || $data['height']==0 || $data['width']==0 || $data['weight']==0)$erreurvide="***Champs vide***";
			$ebayresult=get_ebay_product($connectionapi,$data['marketplace_item_id']);
			$json2 = json_decode($ebayresult, true);
			//print("<pre>".print_r ($json2,true )."</pre>");
			$post=$data;
			$post['nameen']=$data['nameen'];
			$post['name']=$data['nameen'];
			$post['sku']=$skuachanger;
			$post['coloren']=$data['coloren'];
			$post['condition_suppen']=$data['condition_supp'];
			$post['description_adden']=$data['description_add'];
			$post['manufacturer_id']=$data['manufacturer_id'];
			//echo $skuachanger;
			$sql2 = 'SELECT pc.category_id  
					FROM `oc_product_to_category` pc 
					JOIN `oc_category` c ON (c.category_id=pc.category_id AND c.leaf = 1 AND cd.language_id=1)
					where pc.product_id='.$data['product_id'];
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2); 
			//echo $sql2;
			$post['category_id']=$data2['category_id'];
		//echo $rowverif."-".$requete."-".$numetat;
			$post['condition_id']=$numetat;
			$post['quantity']=0;
			$post['unallocated_quantity']=0;
			$post['quantity_entreprot']=0;
			$post['price']=0;
			$post['priceusd']=$post['price'];
			//echo $json2['Item']['PrimaryCategory']['CategoryID'];
			//print("<pre>".print_r ($post,true )."</pre>");
			$json=add_ebay_item($connectionapi,$ebayresult,$post,$db); 
			//upload_image_from_product_id($product_id,$data['product_id'],$db);
			$post['marketplace_item_id']=$json['ItemID'];
			$post['price']=0;
			$product_id=insert_item_db($connectionapi,$post,$db);
			link_to_download($connectionapi,$product_id,$data['marketplace_item_id'],"",$db);
		}
		return $product_id;
}
/*function change_extension($directory, $ext1, $ext2, $verbose = false) {
  $num = 0;
  if($curdir = opendir($directory)) {
   while($file = readdir($curdir)) {
     if($file != '.' && $file != '..') {
       $srcfile = $directory . '/' . $file;
       $string  = "$file";
       $str     = strlen($ext1);
       $str++;
       $newfile = substr($string, 0, -$str);
       $newfile = $newfile.'.'.$ext2;
       $dstfile = $directory . '/' . $newfile;
       if (eregi("\.$ext1",$file)) { # Look at only files with a pre-defined extension
       $fileHand = fopen($srcfile, 'r');
       fclose($fileHand);
       rename($srcfile, $dstfile );
       }
       if(is_dir($srcfile)) {
         $num += change_extension($srcfile, $ext1, $ext2, $verbose);
       }
     }
   }
   closedir($curdir);
  }
  return $num;
}*/
function getRandomSku($db){
	$value=mt_rand(111111111111,999999999999);
	$sql = 'SELECT sku FROM `oc_product`
			where sku = "'.$value .'"';
			//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			//print("<pre>".print_r ($data,true )."</pre>");
			if(is_null($data)){
				return $value;
			}else{
				getRandomSku($db);
			}
}
function refresh_token_etsy($db){
	$sql="SELECT * FROM `oc_etsy_accounts` WHERE `id` = 1";
	$req = mysqli_query($db,$sql);
	$data = mysqli_fetch_assoc($req);
	$refresh_token = $data['refresh_token'];
	//print("<pre>".print_r ($data,true )."</pre>");
	$client_id = $data['client_id'];
	$client_secret = $data['client_secret'];
	$token_url = 'https://api.etsy.com/v3/public/oauth/token?';
	$query = array(
		'grant_type' => 'refresh_token',
		'client_id' => $client_id,
		'client_secret' => $client_secret,
		'refresh_token' => $refresh_token
	);
	// Set the cURL options
	$curl = curl_init($token_url);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($query));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	// Send the request and get the response
	$token_response = curl_exec($curl);
	curl_close($curl);
	// Parse the JSON response
	$token_data = json_decode($token_response, true);
//	echo "<br>token_data152:";
	//print("<pre>".print_r ($token_data,true )."</pre>");
	// Extract the new access token and refresh token
	$new_access_token = $token_data['access_token'];
	$new_refresh_token = $token_data['refresh_token'];
	if(isset($token_data['access_token'])){
	//	echo "<br>data156:";
//	//print("<pre>".print_r ($data,true )."</pre>");
		$sql="UPDATE `oc_etsy_accounts` SET `access_token` = '".$new_access_token."', `refresh_token` = '".$new_refresh_token."' WHERE `oc_etsy_accounts`.`id` = 1";
		$req = mysqli_query($db,$sql);
		echo "Success";
		return $data;
	}else{
		return null;
	}
}
	// Autoloader
/*if (file_exists(DIR_SYSTEM . 'startup.php')) {
    require_once(DIR_SYSTEM . 'startup.php');
} else {
    exit('Error: Could not load system startup file!');
}*/
function getEtsyProducts($db, $limit = 10, $offset = 0,$state = "active") {
    // Assumez que la fonction refresh_token_etsy est correctement implémentée pour actualiser le jeton d'accès si nécessaire
    refresh_token_etsy($db);
    $sql = "SELECT * FROM `oc_etsy_accounts` WHERE `id` = 1";
    $req = mysqli_query($db, $sql);
    $data = mysqli_fetch_assoc($req);
    $access_token = $data['access_token'];
    $shop_id = $data['shop_id'];
	$client_id = $data['client_id'];
    $url = "https://openapi.etsy.com/v3/application/shops/{$shop_id}/listings";
	$url .= "?limit={$limit}&offset={$offset}&state={$state}"; // Utiliser api_key comme paramètre
//$url .= "?limit=2&offset=2"; // Utiliser api_key comme paramètre
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
		'x-api-key: ' . $client_id ,
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    try {
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        $data = json_decode($response, true);
		//print("<pre>".print_r ($data['results'],true )."</pre>"); 
        if (isset($data['results'])) {
            return $data;
        } else {
            return [];
        }
    } catch (Exception $e) {
        // Gérer les erreurs
        echo 'Erreur de requête: ' . $e->getMessage();
        return [];
    } finally {
        
    }
}
function getEtsyProduct($db, $listing_id) {
    // Assumez que la fonction refresh_token_etsy est correctement implémentée pour actualiser le jeton d'accès si nécessaire
    refresh_token_etsy($db);
    $sql = "SELECT * FROM `oc_etsy_accounts` WHERE `id` = 1";
    $req = mysqli_query($db, $sql);
    $data = mysqli_fetch_assoc($req);
    $access_token = $data['access_token'];
    $shop_id = $data['shop_id'];
	$client_id = $data['client_id'];
    $url = "https://openapi.etsy.com/v3/application/listings/{$listing_id}";
	$url .= "?listing_id={$listing_id}"; // Utiliser api_key comme paramètre
//$url .= "?limit=2&offset=2"; // Utiliser api_key comme paramètre
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
		'x-api-key: ' . $client_id ,
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    try {
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        $data = json_decode($response, true);
		//print("<pre>".print_r ($data,true )."</pre>"); 
        if (isset($data['results'])) {
            return $data;
        } else {
            return [];
        }
    } catch (Exception $e) {
        // Gérer les erreurs
        echo 'Erreur de requête: ' . $e->getMessage();
        return [];
    } finally {
        
    }
}
function getListingInventory($db,$listingId) {
	refresh_token_etsy($db);
	$sql="SELECT * FROM `oc_etsy_accounts` WHERE `id` = 1";
	$req = mysqli_query($db,$sql);
	$data = mysqli_fetch_assoc($req);
	//print("<pre>".print_r ($data,true )."</pre>");
	$client_secret = $data['client_secret'];
	$access_token = $data['access_token'];
	$shop_id = $data['shop_id'];
	$client_id = $data['client_id'];
    $url = "https://openapi.etsy.com/v3/application/listings/{$listingId}/inventory";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token,
        'x-api-key: ' . $client_id,
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        echo 'Erreur cURL : ' . curl_error($ch);
    } else {
        $response = json_decode($response, true);
        if (isset($response['error'])) {
            echo 'Erreur API Etsy : ' . $response['error']['message'];
        } else {
            // La réponse contient les informations sur l'inventaire du listing
            return $response;
        }
    }
    
}
function updateEtsyListing($db, $listingId,$product_info) {
  // Assumez que la fonction refresh_token_etsy est correctement implémentée pour actualiser le jeton d'accès si nécessaire
  refresh_token_etsy($db);
  $sql="SELECT * FROM `oc_etsy_accounts` WHERE `id` = 1";
  $req = mysqli_query($db,$sql);
  $data = mysqli_fetch_assoc($req);
  //print("<pre>".print_r ($data,true )."</pre>");
  $client_secret = $data['client_secret'];
  $access_token = $data['access_token'];
  $shop_id = $data['shop_id'];
  $client_id = $data['client_id'];
    $url = "https://openapi.etsy.com/v3/application/listings/{$listingId}/inventory";
	//print("<pre>".print_r ($product_info,true )."</pre>");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($product_info));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token,
        'x-api-key: ' . $client_id,
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        echo 'Erreur cURL : ' . curl_error($ch);
    } else {
        $response = json_decode($response, true);
        if (isset($response['error'])) {
            echo 'Erreur API Etsy : ' . $response['error']['message'];
			//print("<pre>".print_r ($response,true )."</pre>");
        } else {
            echo 'Quantité du listing Etsy mise à jour avec succès';
        }
    }
    
}
function updateEtsyProductsStatus($db, $product) {
    // Assumez que la fonction refresh_token_etsy est correctement implémentée pour actualiser le jeton d'accès si nécessaire
    refresh_token_etsy($db);
	$sql="SELECT * FROM `oc_etsy_accounts` WHERE `id` = 1";
	$req = mysqli_query($db,$sql);
	$data = mysqli_fetch_assoc($req);
	//print("<pre>".print_r ($product,true )."</pre>");
	$client_secret = $data['client_secret'];
	$access_token = $data['access_token'];
    $shop_id = $data['shop_id'];
	$client_id = $data['client_id'];
	if($product['quantity_total']<=0){
		$state="inactive";
	}else{
		$state="active";
	}
    $data = array(
			'quantity' =>  $product['quantity_total'],
			'state' => $state,
			"price"=> array (
				"amount"=> $product['priceretail']*100,
				"divisor"=> 100,
				"currency_code"=> "USD"
			),
    );
	//print("<pre>".print_r ($data,true )."</pre>");
   // $url = "https://openapi.etsy.com/v3/application/shops/{$shop_id}/listings/{$product['listing_id']}";
	$url = "https://openapi.etsy.com/v3/application/shops/{$shop_id}/listings/{$product['listing_id']}";
//	$url .= "?limit={$limit}&offset={$offset}"; // Utiliser api_key comme paramètre
//$url .= "?limit=2&offset=2"; // Utiliser api_key comme paramètre
    $ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token,
		'x-api-key: ' . $client_id ,
    ));
	$response = curl_exec($ch);
    if ($response === false) {
        echo 'Erreur cURL : ' . curl_error($ch);
    } else {
        $response = json_decode($response, true);
        if (isset($response['error'])) {
            echo '<br>Erreur API Etsy : ' ;
			//print("<pre>".print_r ($response,true )."</pre>");
        } else {
            echo '<br>Inventaire de la liste Etsy mis à jour avec succès';
			//print("<pre>".print_r ($response,true )."</pre>");
        }
    }
    
}
function add_etsy_item($db,$product_id) {
	refresh_token_etsy($db);
	$sql="SELECT * FROM `oc_etsy_accounts` WHERE `id` = 1";
	$req = mysqli_query($db,$sql);
	$data = mysqli_fetch_assoc($req);
	$listing_data=get_products_by_id($product_id);
	//print("<pre>".print_r ($data,true )."</pre>");
	$access_token = $data['access_token'];
//	//print("<pre>".print_r ($data,true )."</pre>");
	$client_id = $data['client_id'];
	$shop_id = $data['shop_id'];
	$client_secret = $data['client_secret'];
    $url = "https://openapi.etsy.com/v3/application/shops/{$shop_id}/listings";
    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
		'x-api-key: ' . $client_id ,
    ];
	$listing_data['description_en']="test";
    $payload = [
        'title' => $listing_data['name_en'],
        'description' => $listing_data['description_en'],
        'price' => $listing_data['price_with_shipping'],
        'quantity' => $listing_data['quantity_total'],
        'taxonomy_id' => 6,
		'who_made' => "i_did",
		'when_made'=> "2020_2023",
		'shipping_profile_id'=>"176860401619",
        // Ajoutez d'autres champs requis ici
    ];
//	//print("<pre>".print_r ($payload,true )."</pre>");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($http_code == 201) {
        echo "Listing ajouté avec succès !\n";
	//	//print("<pre>".print_r (json_decode($response, true),true )."</pre>");
		return json_decode($response, true);
    } else {
        echo "Erreur lors de l'ajout du listing. Code d'erreur : " . $http_code . "\n";
	//	//print("<pre>".print_r (json_decode($response, true),true )."</pre>");
        return null;
    }
}
function poidsVolumiqueNucleaire($longueur, $largeur, $hauteur, $poids) {
    // Vérification que les dimensions ne sont pas nulles dans l'une des trois mesures
	if ($longueur == 0) {
        return "Erreur : La longueur ne peut pas être nulle.";
    }
    if ($largeur == 0) {
        return "Erreur : La largeur ne peut pas être nulle.";
    }
    if ($hauteur == 0) {
        return "Erreur : La hauteur ne peut pas être nulle.";
    }
    // Conversion des dimensions en pouces en pieds cubes
    $volume_pieds_cubes = ($longueur * $largeur * $hauteur) /1728;
    // Poids volumique nucléaire en livres par pied cube (ex. valeur arbitraire pour l'exemple)
    $poids_volumique_lbs_pied_cube = 12.4; 
    // Calcul du poids volumique nucléaire total en livres
    $poids_total_lbs = $volume_pieds_cubes * $poids_volumique_lbs_pied_cube;
    // Arrondi à deux décimales
    $poids_total_lbs_arrondi = round($poids_total_lbs, 2);
    // Retourne la plus haute valeur entre le poids calculé et le poids donné
    return max($poids_total_lbs_arrondi, $poids);
}
function verifierDimensions($longueur, $largeur, $hauteur) {
    // Calculer la somme totale des dimensions
    $total = $longueur + $largeur + $hauteur;
    // Vérifier si la somme totale dépasse 36 pouces
    if ($total > 36) {
        return false;
    }
    // Vérifier si au moins l'une des dimensions dépasse 24 pouces
    if ($longueur > 24 || $largeur > 24 || $hauteur > 24) {
        return false;
    }
    // Si les conditions précédentes ne sont pas remplies, alors les dimensions sont acceptables
    return true;
}
function callChatGPTAPI($prompt) {
	$apiKey = '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';

    $url = 'https://api.openai.com/v1/engines/gpt-3.5-turbo-instruct/completions';
    
    $data = [
        'prompt' => $prompt,
        'max_tokens' => 500
    ];
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n" .
                        "Authorization: Bearer $apiKey\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
        ],
    ];
    
    $context = stream_context_create($options);
	try {
        $result = file_get_contents($url, false, $context);
	//	echo "try".$result;
        if ($result === FALSE) {
            throw new Exception('Error fetching data from API');
        }
    } catch (Exception $e) {
        $error = error_get_last();
        echo "Error: " . $e->getMessage() . "\n";
        echo "Details: " . $error['message'] . "\n";
        return null;
    }
    
    return json_decode($result, true);
}
function fetch_movie_details ($movie_title){


//	$url = 'https://api.openai.com/v1/completions';
	
	//$prompt = "Provide the following information about the movie '$movie_title' in JSON format: Sub-Genre, Genre, Season, Studio, Actor, Rating, Director, Franchise, Country/region of manufacture, Music Artist, Cinematic Movement, Producer, Run time.";
 //   $prompt = "Provide detailed information about the movie '$movie_title' in JSON format. The information should include: Sub-Genre, Genre, Season, Production Company, Distributor, Film Studio, Actor, Rating, Director, Franchise, Country/region of manufacture, Music Artist, Cinematic Movement, Producer, Duration.";
	$prompt = "Provide detailed information about the movie '$movie_title' in JSON format. The information should include: Sub-Genre, Genre, Season, Production Company, Distributor, Film Studio, Actor, Rating, Director, Franchise, Country/region of manufacture, Music Artist, Cinematic Movement, Producer, Duration. Ensure the information is accurate and cite reliable sources such as IMDb, Rotten Tomatoes, or official movie websites.";

	$response_data = callChatGPTAPI($prompt);
	//print("<pre>".print_r ($response_data,true )."</pre>");
	if ($response_data !== null) {
		$completion = $response_data['choices'][0]['text'];
	
		// Nettoyer la réponse en supprimant tout ce qui suit l'accolade fermante
		$cleaned_json = clean_json($completion);
	
		$decoded_response = json_decode($cleaned_json, true);
	
		// Vérifier les erreurs de json_decode
		if (json_last_error() !== JSON_ERROR_NONE) {
			fetch_movie_details ($movie_title);
			echo "JSON Decode Error: " . json_last_error_msg() . "\n";
			//print("<pre>".print_r ($cleaned_json,true )."</pre>");
		} else {
			
			return $decoded_response;
		//	echo "<pre>" . print_r($cleaned_json, true) . "</pre>";
		}
	}
}
function clean_json($json_str) {
 // Supprimer les sources
 if (strpos($json_str, 'Sources:') !== false) {
	// Trouver la position de la dernière accolade fermante
	$last_brace_pos = strrpos($json_str, '}');
	if ($last_brace_pos !== false) {
		// Supprimer tout ce qui suit la dernière accolade fermante
		$json_str = substr($json_str, 0, $last_brace_pos + 1);
	}
}

 // Nettoyer la chaîne en supprimant les espaces en début et fin de chaîne
 $cleaned_str = trim($json_str);

 // Supprimer les virgules superflues au début de la chaîne
 $cleaned_str = preg_replace('/^,/', '', $cleaned_str);

 // Supprimer les virgules superflues après les accolades ouvrantes et avant les accolades fermantes
 $cleaned_str = preg_replace('/,\s*(\})/', '$1', $cleaned_str);
 $cleaned_str = preg_replace('/(\{)\s*,/', '$1', $cleaned_str);

 // Supprimer les virgules superflues avant les crochets fermants
 $cleaned_str = preg_replace('/,\s*(\])/', '$1', $cleaned_str);

 // Compter le nombre d'accolades ouvrantes et fermantes
 $open_braces = substr_count($cleaned_str, '{');
 $close_braces = substr_count($cleaned_str, '}');

 // Compter le nombre de crochets ouvrants et fermants
 $open_brackets = substr_count($cleaned_str, '[');
 $close_brackets = substr_count($cleaned_str, ']');

 // Ajuster les accolades fermantes manquantes ou en trop
 if ($open_braces > $close_braces) {
	 $cleaned_str .= str_repeat('}', $open_braces - $close_braces);
 } elseif ($open_braces < $close_braces) {
	 $cleaned_str = substr($cleaned_str, 0, strrpos($cleaned_str, '}') + 1);
 }

 // Ajuster les crochets fermants manquants ou en trop
 if ($open_brackets > $close_brackets) {
	 $cleaned_str .= str_repeat(']', $open_brackets - $close_brackets);
 } elseif ($open_brackets < $close_brackets) {
	 $cleaned_str = substr($cleaned_str, 0, strrpos($cleaned_str, ']') + 1);
 }

 return $cleaned_str;
}
function generateOptimizedTitle_CHATGPT($keywords) {
	// Requête à ChatGPT pour générer le titre optimisé
	// Vous devez utiliser votre propre clé API pour accéder à l'API ChatGPT
	$apiKey = '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';
	$url = 'https://api.openai.com/v1/completions';
    $data = array(
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'Based on this : ( '.implode(', ', $keywords).") create a max 80 characters title for ebay listing",
		'max_tokens' => 30,
		"temperature"=> 0,
	//	'n' => 3,
	//	'temperature' => 0.1, // Rendre les prédictions plus fiables en réduisant la température
    );
	//print("<pre>".print_r ($data,true )."</pre>");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ));
    $response = curl_exec($ch);
    
    $responseData = json_decode($response, true);
   //print("<pre>".print_r ($responseData,true )."</pre>");
    if (isset($responseData['choices'][0]['text'])) {
        return str_replace('"', '',$responseData['choices'][0]['text']);
    } else {
        return "Error generating optimized title.";
    }
}
function generateOptimizedTitle_CHATGPT_UPC($keywords) {
	// Requête à ChatGPT pour générer le titre optimisé
	// Vous devez utiliser votre propre clé API pour accéder à l'API ChatGPT
	$apiKey = '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';
	$url = 'https://api.openai.com/v1/completions';
    $data = array(
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'Based on UPC : ( '.implode(', ', $keywords).") create a max 80 characters product name title for ebay listing do not include UPC in title",
		'max_tokens' => 30,
		"temperature"=> 0,
	//	'n' => 3,
	//	'temperature' => 0.1, // Rendre les prédictions plus fiables en réduisant la température
    );
	//print("<pre>".print_r ($data,true )."</pre>");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ));
    $response = curl_exec($ch);
    
    $responseData = json_decode($response, true);
   //print("<pre>".print_r ($responseData,true )."</pre>");
    if (isset($responseData['choices'][0]['text'])) {
		//echo "resultat:".str_replace('"', '',$responseData['choices'][0]['text']);
		$resultformat=str_replace('"', '',$responseData['choices'][0]['text']);
		$resultformat=str_replace("'", '',$resultformat);
        return $resultformat;
    } else {
        return "***Error generating optimized title.***";
    }
}

function generateOptimizedTitle_CHATGPT_UPC_movie($upc, $name) {
	// Requête à ChatGPT pour générer le titre optimisé
	// Vous devez utiliser votre propre clé API pour accéder à l'API ChatGPT
	$apiKey = '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';
	$url = 'https://api.openai.com/v1/completions';
    $data = array(
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'Based on UPC : '. $upc." and '.$name.' create ebay title with a maximum of 80 characters (not exeed 80 characters) use this format: movie title (dvd or bluray, years of the movie, widescreen or full screen) other good info, actors or productor ,  production type BUT IMPORTANT keep the number of disc set and if its a canadian version ",
		'max_tokens' => 30,
		"temperature"=> 0,
	//	'n' => 3,
	//	'temperature' => 0.1, // Rendre les prédictions plus fiables en réduisant la température
    );
	//print("<pre>".print_r ($data,true )."</pre>");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ));
    $response = curl_exec($ch);
    
    $responseData = json_decode($response, true);
   //print("<pre>".print_r ($responseData,true )."</pre>");
    if (isset($responseData['choices'][0]['text'])) {
		//echo "resultat:".str_replace('"', '',$responseData['choices'][0]['text']);
		$resultformat=str_replace('"', '',$responseData['choices'][0]['text']);
		$resultformat=str_replace("'", '',$resultformat);
        return $resultformat;
    } else {
        return "***Error generating optimized title.***";
    }
}
function getCategoriesRequest($connectionapi,$SITEID = 0) {
    // En-têtes de la requête
    $headers = [
		"X-EBAY-API-COMPATIBILITY-LEVEL: 1019",
		"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
		"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
		"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
		"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
        "X-EBAY-API-CALL-NAME: GetCategories",
       "X-EBAY-API-SITEID: ".$SITEID."",
	//	"X-EBAY-API-SITEID: 100",
    ];
	//print("<pre>".print_r ($headers,true )."</pre>");
    // Corps de la requête SOAP
    $requestBody = '<?xml version="1.0" encoding="utf-8"?>'
                 . '<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">'
                 . '   ' 
               //  . ' <CategorySiteID>0</CategorySiteID>
			  // <LevelLimit>7</LevelLimit>
				 . ' 		<DetailLevel>ReturnAll</DetailLevel>
						<ViewAllNodes>true</ViewAllNodes>'
                 . '</GetCategoriesRequest>';
//echo $requestBody;
    // Initialisation de cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,'https://api.ebay.com/ws/api.dll');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Exécution de la requête
    $result = curl_exec($ch);
    // Vérification des erreurs cURL
    if (curl_errno($ch)) {
        echo 'Erreur cURL : ' . curl_error($ch);
        return null;
    }
    // Fermeture de cURL
    
	$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('**', '', $result);
			$result = str_replace("\r\n", '', $result);
			$result = str_replace('\"', '"', $result);
		//	if ($err) {
				//echo "cURL Error #:" . $err;
		//	} else {
				// Convert xml string into an object 
				//echo $result."\nallo";
				$new = simplexml_load_string($result);  
				// Convert into json 
				$result = json_encode($new); 
				$textoutput=str_replace("}","<br><==<br>",$result);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				//echo $textoutput."\nallo"."<br>";
				$json = json_decode($result, true);
			//	//print("<pre>".print_r ($json,true )."</pre>");
		//	}
    // Retour de la réponse
    return $json;
}
function ImageisPoorResolution($imageUrl, $minWidth = 400, $minHeight = 600) {
    // Vérifier si l'URL est valide et accessible
    if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        return true; // URL invalide considérée comme mauvaise
    }
    // Utiliser getimagesize pour obtenir les dimensions de l'image
    $imageSize = @getimagesize($imageUrl);
    if ($imageSize === false) {
        return true; // Impossible d'obtenir les dimensions, image considérée comme mauvaise
    }
    list($height,$width ) = $imageSize;
//	echo "<br>size width: ".$width;
//	echo " size height: ".$height;
    return ($width < $minWidth || $height < $minHeight);
}
function format_string($str) {
    // Mettre la première lettre en majuscule
    $str = ucfirst(strtolower($str));
    // Mettre en majuscule les mots importants et les mots qualificatifs
    $important_words = array('a', 'an', 'the', 'and', 'but', 'or', 'for', 'nor', 'on', 'at', 'to', 'by', 'with', 'in', 'of', 'as','blu-ray', 'dvd');
    $qualifying_words = array(
		'new', 
		'used', 
		'free', 
		'discounted', 
		'pre-owned', 
		'refurbished', 
		'limited edition', 
		'special edition',
		'premium',
		'handcrafted',
		'authentic',
		'vintage',
		'eco-friendly',
		'artisan',
		'deluxe',
		'luxury',
		'collectible',
		'exclusive',
		'original',
		'unique',
		'rare',
		'antique',
		'modern',
		'classic',
		'custom',
		'handmade',
		'bespoke',
		'genuine',
		'renewed',
		'one-of-a-kind',
		'high-quality',
		'designer',
		'artisanal',
		'bespoke',
		'vintage-inspired',
		'eco-conscious',
		'limited-time',
		'premium-quality',
		'timeless',
		'signature',
		'sustainable',
		'made-to-order',
		'vintage-style',
		'retro',
		'chic',
		'trendy',
		'innovative',
		'contemporary',
		'modernist',
		'fashionable',
		'heritage',
		'crafted',
		'exquisite',
		'unique',
		'personalized',
		'bespoke',
		'trendsetting',
		'one-of-a-kind',
		'elite',
		'boutique',
		'exceptional',
		'glamorous',
		'luxurious',
		'prestigious',
		'cutting-edge',
		'state-of-the-art',
		'fine',
		'rugged',
		'sleek',
		'durable',
		'adaptable',
		'dependable',
		'versatile',
		'elegant',
		'sophisticated',
		'refined',
		'opulent',
		'lavish',
		'grand',
		'sumptuous',
		'opulent',
		'sumptuous',
		'lavish',
		'extravagant',
		'pampering',
		'grandiose',
		'majestic',
		'stunning',
		'splendid',
		'imposing',
		'majestic',
		'impressive',
		'awe-inspiring',
		'striking',
		'spectacular',
		'breathtaking',
		'magnificent',
		'glorious',
		'resplendent',
		'marvelous',
		'sublime',
		'superb',
		'divine',
		'wonderful',
		'fantastic',
		'fabulous',
		'outstanding',
		'phenomenal',
		'stellar',
		'remarkable',
		'extraordinary',
		'exceptional',
		'incredible',
		'unparalleled',
		'unprecedented',
		'matchless',
		'unequaled',
		'peerless',
		'top-notch',
		'world-class',
		'first-class',
		'premium-grade',
		'limited Stock'
		// Ajoutez d'autres mots qualificatifs selon vos besoins
	);
    $words = explode(' ', $str);
    foreach ($words as $key => $word) {
        $lowercase_word = strtolower($word);
        if (!in_array($lowercase_word, $important_words) && !in_array($lowercase_word, $qualifying_words)) {
            $words[$key] = ucfirst($lowercase_word);
        } else {
            $words[$key] = capitalizeWords($lowercase_word); // Mettre en majuscules les mots qualificatifs
        }
    }
    $str = implode(' ', $words);
    // Enlever les doubles espaces
    $str = preg_replace('/\s+/', ' ', $str);
    return $str;
}
function MultiSort($data, $sortCriteria, $caseInSensitive = true)
{
  if( !is_array($data) || !is_array($sortCriteria))
    return false;      
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
function MultiSortMerge($data, $sortCriteria, $caseInsensitive = true)
{
    if (!is_array($data) || !is_array($sortCriteria)) {
        return false;
    }
	if($data['sku']=='627735386850')//print("<pre>".print_r ($data,true )."</pre>");
	//print("<pre>".print_r ($data,true )."</pre>");
    // Initialize an associative array to store quantities based on SKU
    $skuQuantities = array();

    $i = 0;
    foreach ($sortCriteria as $sortColumn => $sortAttributes) {
        $colList = array();

        foreach ($data as $key => $row) {
            $convertToLower = $caseInsensitive && (in_array(SORT_STRING, $sortAttributes) || in_array(SORT_REGULAR, $sortAttributes));
            $rowData = $convertToLower ? strtolower($row[$sortColumn]) : $row[$sortColumn];
            $colList[$key] = $rowData;

            // Add quantities for identical SKUs
            if ($sortColumn === 'sku') {
                $sku = $rowData;
                if (isset($skuQuantities[$sku])) {
                    $data[$key]['Quantity'] += $skuQuantities[$sku];
                    // Update the quantity in the associative array for future additions
                    $skuQuantities[$sku] = $data[$key]['Quantity'];
                } else {
                    $skuQuantities[$sku] = $data[$key]['Quantity'];
                }
            }
        }

        $args[] = &$colList;

        foreach ($sortAttributes as $sortAttribute) {
            $tmp[$i] = $sortAttribute;
            $args[] = &$tmp[$i];
            $i++;
        }
    }

    $args[] = &$data;
    call_user_func_array('array_multisort', $args);

    return end($args);
}

 function refreshAccessTokenOLD($refreshToken) {
	$client = new GuzzleHttp\Client();
	$url = 'https://api.ebay.com/identity/v1/oauth2/token';

	// Informations d'identification de l'application
	$clientId = 'CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28'; // Remplacez par votre client_id
	$clientSecret = 'PRD-93ff3ada979d-7fcf-4938-be46-ba89'; // Remplacez par votre client_secret

	// En-tête Authorization encodé en base64
	$encodedCredentials = base64_encode($clientId . ':' . $clientSecret);

	$headers = [
		'Content-Type' => 'application/x-www-form-urlencoded',
		'Authorization' => 'Basic ' . $encodedCredentials,
	];

	// Paramètres de la requête pour rafraîchir le jeton d'accès
	$body = [
		'grant_type' => 'refresh_token',
		'refresh_token' => $refreshToken,
		'scope' => 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly https://api.ebay.com/oauth/api_scope/sell.stores https://api.ebay.com/oauth/api_scope/sell.stores.readonly'
	];

	try {
		// Effectuer la requête POST pour renouveler le jeton d'accès
		$response = $client->post($url, [
			'headers' => $headers,
			'form_params' => $body
		]);

		// Analyser la réponse
		$responseArray = json_decode($response->getBody()->getContents(), true);
//print("<pre>".print_r ('1441:ebay.php',true )."</pre>");
 //print("<pre>".print_r ($responseArray,true )."</pre>");
		// Vérifier si le nouveau jeton d'accès est obtenu
		if (isset($responseArray['access_token'])) {
			$responseArray['bearer_token']=$responseArray['access_token'];
			if (isset($responseArray['bearer_token'])) {
				// Définir le cookie AVANT toute sortie HTML
				setcookie('bearer_token', $responseArray['bearer_token'], time() + 7200, "/"); // Expire dans 2 heures
			  
			}
		  
			return $responseArray;
		} else {
		//    echo "Erreur lors de l'obtention du nouveau jeton d'accès.\n";
		  //  print_r($responseArray);
			return null;
		}
	} catch (\Exception $e) {
		echo "Erreur pendant l'obtention du nouveau jeton d'accès : " . $e->getMessage();
		return null;
	}
}


