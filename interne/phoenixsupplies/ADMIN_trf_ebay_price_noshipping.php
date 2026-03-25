<?
include 'connection.php';
$sql="SELECT * FROM `oc_product` where (verif_fait<6 or verif_fait is null) and (ebay_id !='0' and ebay_id is not null and ebay_id!='') and quantity >0 order by price_with_shipping DESC limit 100";//limit 50
//$sql="SELECT * FROM `oc_product` where (ebay_id !='0' and ebay_id is not null and ebay_id!='') and quantity >0 and weight>=15 order by price_with_shipping DESC limit 100";//limit 50

if(isset($_GET['product_id'])){
   
    $sql="SELECT * FROM `oc_product` where product_id='".$_GET['product_id']."'";//limit 50
    $req = mysqli_query($db,$sql); 
    $data = mysqli_fetch_assoc($req);
    end_to_ebay($connectionapi,$data['marketplace_item_id']);
    relist_to_ebay($connectionapi,$_GET['product_id'],$data['marketplace_item_id'],$db);
    $sql="SELECT * FROM `oc_product` where product_id='".$_GET['product_id']."'";//limit 50
}
$req = mysqli_query($db,$sql); 
echo $sql."<br>";
while ($data = mysqli_fetch_assoc($req)){
//print("<pre>".print_r ($data,true )."</pre>");
    $sql4 = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$data['product_id'].'" and ebayyes=1';
	//		echo $sql4."<br>";
					$req4 = mysqli_query($db,$sql4);
					$data4 = mysqli_fetch_assoc($req4);
				
                    if($data['quantity']==0)	{		
                        $info_shipping=get_shipping ($connectionapi,$data['weight'],$data['length'],$data['width'],$data['height'],$db,(string)$data['upc'],12919,$data4['category_id']);
                    }else{
                        $info_shipping=get_shipping ($connectionapi,$data['weight'],$data['length'],$data['width'],$data['height'],$db,(string)$data['upc'],12919,$data4['category_id'],$data);
                    }
    $data['price']=(($data['price_with_shipping'])-$info_shipping['shipping']);
    $data['price']= number_format( $data['price'], 2,'.', '');
			//	$price_replace=explode('.',$_POST['suggest']);
			//	$_POST['suggest']=$price_replace[0]+$endprix;
				// $data['price']= number_format( $data['price'], 2, '.', '');
            //print("<pre>".print_r ($data,true )."</pre>");
                $Weight=$data['weight'];	
				$Height=$data['height'];	
				$Width=$data['width'];	
				$Depth=$data['length'];	
                $poids_total = poidsVolumiqueNucleaire($Depth, $Width, $Height,$Weight);
				if( $data['price']<0.50){
				//	 $data['price']=number_format(0.00, 2,'.', '');
                     $sql2 = 'UPDATE `oc_product` SET `price` =  "'.$data['price'].'",`verif_fait` = 9,weight_volume="'.$poids_total.'" WHERE `oc_product`.`product_id` ='.$data['product_id'];
                     $req2 = mysqli_query($db,$sql2); 
                 }else{
                    $sql2 = 'UPDATE `oc_product` SET `price` =  "'.$data['price'].'",`verif_fait` = 6,weight_volume="'.$poids_total.'" WHERE `oc_product`.`product_id` ='.$data['product_id'];
                    $req2 = mysqli_query($db,$sql2); 
                    $result=revise_ebay_product($connectionapi,$data['marketplace_item_id'],$data['product_id'],$data['quantity'],$db,"oui","oui");
                }


   

			//	echo $sql2.'<br><br>';

	}

mysqli_close($db); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page avec rafraîchissement automatique</title>
</head>
<body>
    <h1>Cette page sera rafraîchie automatiquement</h1>
    
    <!-- Ajoutez le script JavaScript -->
    <script>
        // Définit la fonction pour rafraîchir la page
        function refreshPage() {
            location.reload(); // Recharge la page
        }

        // Définit l'intervalle de rafraîchissement (5 minutes et 30 secondes en millisecondes)
        const interval = 5 * 60 * 1000 + 30 * 1000;
      //  const interval =  30 * 1000;
        // Démarre le temporisateur pour rafraîchir la page après l'intervalle spécifié
        setTimeout(refreshPage, interval);
    </script>
</body>
</html>
