<?PHP
include '../connection.php';include '../functionload.php';
// on sélectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 

$sql2 = 'select * from `oc_product` where quantity>0 and ebay_id>0 and maj=7 ';//and product_id>3510 

					//echo $sql2.'<br><br>';
$req2 = mysqli_query($db,$sql2);
//$data2 = mysqli_fetch_assoc($req2);
//print("<pre>".print_r ($data2,true )."</pre>");
$i=0;
    while($data2 = mysqli_fetch_assoc($req2)){
       
        $frais_shipping=0;
        if($data2['USPS_com']=="")$data2['USPS_com']=9999;
        
       
        if($data2['UPS_com']==9999 && $data2['USPS_com']==9999){
            $frais_shipping=0;
            echo "0".$frais_shipping;
        }elseif($data2['USPS_com']>0 && ($data2['USPS_com']< $data2['UPS_com'])){
            $frais_shipping=$data2['USPS_com'];
           // echo "USPS_com".$frais_shipping;
        }elseif($data2['USPS']>0 && ($data2['USPS']< $data2['UPS_com'])){
            $frais_shipping=$data2['USPS'];
            $carrier='USPS';
            $other=$Postagecom;
        
        }elseif($data2['USPS_com']>0 && $data2['UPS_com']< $data2['USPS_com']){
            $frais_shipping=$data2['UPS_com'];
           // echo "UPS_com".$frais_shipping;
           
        }
       // echo $frais_shipping."<br>";
        $verif=ceil(($data2['price_with_shipping']-$frais_shipping)/$data2['price_with_shipping']*100);
        if($frais_shipping>0){
            if($verif>=20){
                $sql1 = 'UPDATE `oc_product`SET maj=2 WHERE `oc_product`.`product_id` ="'.$data2['product_id'].'"';
            $req1 = mysqli_query($db,$sql1);	
                 $sql1 = 'UPDATE `oc_product`SET priceebay="'.($data2['price_with_shipping']-$frais_shipping).'",maj=2 WHERE `oc_product`.`product_id` ="'.$data2['product_id'].'"';
		       //     echo "price_with_shipping :".$data2['price_with_shipping']." --Shipping:".$frais_shipping."-------".$sql1.'<br><br>';
                //$req1 = mysqli_query($db,$sql1);	
            }elseif($verif<20){
                $sql1 = 'UPDATE `oc_product`SET maj=3 WHERE `oc_product`.`product_id` ="'.$data2['product_id'].'"';
            $req1 = mysqli_query($db,$sql1);	
              //  echo "ERREUR2: ".$verif."%--- price_with_shipping:".$data2['price_with_shipping']." --Shipping:".$frais_shipping."-------".$sql1.'<br><br>';
            }
        }else{
            $sql1 = 'UPDATE `oc_product`SET maj=7 WHERE `oc_product`.`product_id` ="'.$data2['product_id'].'"';
            $req1 = mysqli_query($db,$sql1);	
           //print("<pre>".print_r ($data2,true )."</pre>");
           // echo $frais_shipping."ERREUR:".$data2['product_id'].'<br><br>';
            echo "ERREUR:".$data2['weight'].",".$data2['length'].",".$data2['width'].",".$data2['height'].",,".$data2['upc'].'<br><br>';
            get_shipping($connectionapi,$data2['weight'],$data2['length'],$data2['width'],$data2['height'],$db,$data2['upc'],12919);
            $i++;
        }
		
    }
    echo $i.'<br><br>';
?>