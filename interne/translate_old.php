<?
//use Google\Cloud\Translate\V2\TranslateClient;
require_once 'vendor/autoload.php';
//use Google\Cloud\Translate\V3\TranslateClient;
use Google\Cloud\Translate\TranslateClient;
if($_GET['phoenixsupplies']=="oui"){
	$db = mysqli_connect("localhost","phoenkv5_store","Vivi1FX2Pixel$$","phoenkv5_store"); 
}elseif($_GET['phoenixsupplies']=="non"){
	$db = mysqli_connect("localhost","phoenkv5_store","Vivi1FX2Pixel$$","phoenkv5_storeliquidation"); 
}

/** Uncomment and populate these variables in your code */
//$text = 'Va chier.';
$_GET['targetLanguage'] = 'fr';  // Language to translate to 


$translate = new TranslateClient();

//print("Source language: $result[source]\n");
//print("Translation: $result[text]\n");
	$sql3 = 'SELECT P.product_id,PD.name as name_en,PD.accessory,PD.condition_supp,PD.test,PD.description_supp,PD.color FROM `oc_product` as P LEFT JOIN oc_product_description as PD on PD.product_id=P.product_id ';

	if($_GET['clone']=="" && $_GET['phoenixsupplies']=="non"){
		$sql3 .=' WHERE PD.language_id=1 and (P.`sku` ="'.substr((string)$_GET['sku'] ,0,12).'" or P.`sku` ="'.substr((string)$_GET['sku'] ,0,12).'no" or P.`sku` ="'.substr((string)$_GET['sku'] ,0,12).'r")';
	}else{
		$sql3 .=' WHERE PD.language_id=1 and P.`sku` ="'.(string)$_GET['sku'] .'"';
	}
				//	echo $sql3;
	$req3 = mysqli_query($db,$sql3);
	$sql_product_id=' and (';
	while($data3 = mysqli_fetch_assoc($req3)){
		$sql4 = 'SELECT P.product_id,PD.name,PD.accessory,PD.condition_supp,PD.test,PD.description_supp,PD.color FROM `oc_product` as P LEFT JOIN oc_product_description as PD on PD.product_id=P.product_id ';

		$sql4 .=' WHERE PD.language_id=2 and PD.`product_id` ="'.$data3['product_id'].'"';	
		$req4 = mysqli_query($db,$sql4);
		$data4 = mysqli_fetch_assoc($req4);		
		//echo $sql4;
/* 				$result = $translate->translateBatch($data3, [
			'target' => $_GET['targetLanguage'],
				]);
 */
//echo $data3['name'];

		if ($data4['accessory']=="" && $data3['accessory']!=""){
			//echo $data4['accessory'];
				$result = $translate->translate(($data3['accessory']), [
			'target' => $_GET['targetLanguage'],
				]);	
			$accessory='`accessory`="'.addslashes($result['text']).'",';
		}
		if ($data4['condition_supp']=="" && $data3['condition_supp']!=""){
				$result = $translate->translate(($data3['condition_supp']), [
			'target' => $_GET['targetLanguage'],
				]);
			$condition='`condition_supp`="'.addslashes($result['text']).'",';
		}
		if ($data4['test']=="" && $data3['test']!=""){
				$result = $translate->translate(($data3['test']), [
			'target' => $_GET['targetLanguage'],
				]);
			$test='`test`="'.addslashes($result['text']).'",';
		}
		if ($data4['description_supp']=="" && $data3['description_supp']!=""){
				$result = $translate->translate(($data3['description_supp']), [
			'target' => $_GET['targetLanguage'],
				]);
			$description_supp=' description_supp="'.addslashes($result['text']).'",';
		}
		if ($data4['color']=="" && $data3['color']!=""){
				$result = $translate->translate(($data3['color']), [
			'target' => $_GET['targetLanguage'],
				]);
			$color=' `color`="'.addslashes(strtoupper($result['text'])).'",';
		}
		if ($data4['name']==""){
				$result = $translate->translate(($data3['name_en']), [
			'target' => $_GET['targetLanguage'],
				]);		
				//echo $data3['name_en'];
				$name=' `name`="'.addslashes($result['text']).'",';  
			
			//$name='`name`="'.$data3['name_en'].'",';
		}
		//print_r($result)."<br>";

			$sql_product_id=$sql_product_id.' product_id="'.$data3['product_id'].'" or ';

	} 
	if($_GET['clone']==""){
		$sql_product_id=$sql_product_id.' product_id="0")';
	}else{
		$sql_product_id='and product_id="'.$_GET['product_id'].'"';
	}
	
		$sql2 = 'UPDATE `oc_product_description` SET '.$description_supp.$name.$color.$condition.$accessory.$test.'description_mod=1 WHERE language_id=2 '.$sql_product_id;
		//$sql2 = 'UPDATE `oc_product_description` SET name="'.htmlspecialchars_decode(addslashes(strtoupper($result['text'])), ENT_QUOTES).'" WHERE language_id=2 and `product_id` ='.$_GET['product_id'];
							//echo $sql2;
		$req2 = mysqli_query($db,$sql2);
		//echo $sql2."<br>"; 
if($_GET['phoenixsupplies']=="oui"){	
	header("location: https://phoenixsupplies.ca/admin/interne/modificationitem.php?clone=".$_GET['clone']."&sku=".(string)$_GET['sku']."&product_id=".$_GET['product_id']."action=listing"); 
}elseif($_GET['phoenixsupplies']=="non"){
	header("location: https://phoenixliquidation.ca/admin/interne/modificationitemusa.php?clone=".$_GET['clone']."&sku=".(string)$_GET['sku']."&product_id=".$_GET['product_id']."action=listingusa&clone=".$_GET['clone']); 	
}


?>