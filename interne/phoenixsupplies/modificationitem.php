<?
//print_r($_POST['accesoires']);
//print("<pre>".print_r ($_POST,true )."</pre>");
if (isset($_POST['sku']))
	$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache"); 
//include '$GLOBALS['SITE_ROOT'].'interne/translatenew.php'; 
include '../translatenew.php';
if($_GET['hsec']){
	$_POST['hsec']=$_GET['hsec'];
	$_POST['hmin']=$_GET['hmin'];
	$_POST['hhour']=$_GET['hhour'];
}elseif(!isset($_POST['hsec'])){  
//	echo "allo";
	$_POST['hsec']=0;
	$_POST['hmin']=0;
	$_POST['hhour']=0;
}
if ($_GET['product_id']!=""){
	$_POST['product_id'] =$_GET['product_id'];
	
//echo "allo".$_GET['clone'];
}

if ($_GET['sku']!=""){
	(string)$_POST['sku'] =$_GET['sku'];
	
//echo "allo".$_GET['clone'];
}
//echo $_GET['clone'];
if ($_GET['sku']!="" && $_GET['category_id']!=""){
	(string)$_POST['sku'] =$_GET['sku'];
	$_POST['category_id']=$_GET['category_id'];
	//echo "allo";
}
if ((string)$_POST['sku'] =="" && $_POST['skucheck']==""){
	$new=0;
	//echo 'allo';
}
if($_POST['skucheck']!="")(string)$_POST['sku'] =$_POST['skucheck'];
if($_POST['new_ebay_id']>0){
		$sql2 = 'UPDATE `oc_product`SET ebay_id="'.$_POST['new_ebay_id'].'",ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$_POST['new_ebay_listing']="yes";
		
}

if((string)$_POST['hid_sku_ancien']!=(string)$_POST['sku'] && $_POST['sku']!=""){
	$sql = 'SELECT product_id FROM `oc_product` where sku = "'.$_POST['sku'] .'"';
	$req = mysqli_query($db,$sql);
	
	//echo $sql;
	$data = mysqli_fetch_assoc($req);
	unset($_POST);
	$_POST['product_id']=$data['product_id'];
}elseif((string)$_POST['sku'] !="" && $_POST['product_id']>0 ){//&& $_GET['update']="yes"
	
	update_item_db($connectionapi,$_POST,$db);
	/*$result=revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$_POST['product_id'],"non",$db,"oui","");
		$json = json_decode($result, true); 
		if($_POST['showerror']=="oui")//print("<pre>".print_r ($json,true )."</pre>");
			if($json["Ack"]=="Failure"){
				$resultebay.="ERREUR: ".$json["Errors"]["ShortMessage"];
				//print("<pre>".print_r ($json,true )."</pre>");
			}elseif($json["Ack"]=="Warning"){
				//$resultebay.="WARNING: ".$json["Errors"]["ShortMessage"];
			}*/
	//mise_en_page_description($connectionapi,$_POST['product_id'],$db); 
	if($_POST['processing']=="oui"  ){
	/*	$result=revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$_POST['product_id'],"non",$db,"oui","");
		$json = json_decode($result, true); 
		if($_POST['showerror']=="oui")//print("<pre>".print_r ($json,true )."</pre>");
			if($json["Ack"]=="Failure"){
				$resultebay.="ERREUR: ".$json["Errors"]["ShortMessage"];
				//print("<pre>".print_r ($json,true )."</pre>");
			}elseif($json["Ack"]=="Warning"){
				//$resultebay.="WARNING: ".$json["Errors"]["ShortMessage"];
			}*/
								header("location: listing.php?insert=oui&sku=".$_POST['sku']); 
								exit(); 
						}

}
 if (isset($_POST['product_id'] ) && (string)$_POST['product_id'] !=""){
			
			//echo (string)$_POST['sku'] ;
			//$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where language_id=1 and oc_product.product_id=oc_product_description.product_id and sku = "'.(string)$_POST['sku'] .'"';
			$sql = 'SELECT *,PD.color AS color_item FROM `oc_product` AS P,`oc_product_description` AS PD where PD.language_id=1 and P.product_id=PD.product_id and P.product_id = "'.$_POST['product_id'] .'"';
			$req = mysqli_query($db,$sql);
			
			//echo $sql;
			$data = mysqli_fetch_assoc($req);
			//print("<pre>".print_r ($data,true )."</pre>");
			//echo $data['name']
			$_POST['nameen']=$data['name'];
/* echo <<<EOT
My name is "$data['name']". I am printing some $foo->foo.
Now, I am printing some.
This should print a capital 'A': \x41
EOT;	 */	
/* $name = 'MyName';

echo <<<'EOT'
My name is "$name". I am printing some $foo->foo.
Now, I am printing some {$foo->bar[1]}.
This should not print a capital 'A': \x41
EOT; */

	//echo $sql;
	//echo $sql3;
			//$req3 = mysqli_query($db,$sql3);
			//$_POST['nbvariance']=mysqli_num_rows($req3);
			$sql3 = 'SELECT *,PD.color AS color_item FROM `oc_product` AS P,`oc_product_description` AS PD where PD.language_id=2 and P.product_id=PD.product_id and P.product_id = "'.$_POST['product_id'] .'"';
	//echo $sql3;
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
			$_POST['product_id']=$data3['product_id'];
			//print("<pre>".print_r ($data3,true )."</pre>");
			//print("<pre>".print_r ($data3,true )."</pre>");
			//$_POST['namefr']=htmlspecialchars($data3['name'],ENT_QUOTES,'ISO-8859-1', true);
			$_POST['namefr']=$data3['name'];
			if(mysqli_num_rows($req3)==0){
				$sql7 = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`,`description`) VALUES ('".$data['product_id']."', '', '', '2','')";
				$req7 = mysqli_query($db,$sql7);
				//echo $sql7."<br>";	
			}
			(string)$_POST['sku'] =$data['sku'];
			$_POST['etat']=$data['condition_id'];
			$_POST['condition_id']=$data['condition_id'];
			//echo $_POST['etat'];
			$_POST['model']=$data['model'];
			if($data['color_item']!=""){
				$_POST['coloren']=$data['color_anc'];
			}else{
				$_POST['coloren']=$data['color_item'];
			}
			
			$_POST['marketplace_item_id']=$data['marketplace_item_id'];
			if($_POST['manufacturer_id']=="") $_POST['manufacturer_id']=$data['manufacturer_id'];
			$_POST['new']=1;
			
			//echo $_POST['product_id'];
			(string)$_POST['upc']=(string)$data['upc'];
			$_POST['price']=$data['price'];
			$_POST['priceebaysold']=$data['priceebaysold'];
			$_POST['priceterasold']=$data['priceterasold'];
			$_POST['priceebaynow']=$data['priceebaynow'];
			$_POST['quantityinventaire']=$data['quantity'];
			$_POST['price']=$data['price'];
			$weighttab=explode('.', $data['weight']);
			$_POST['weight']=$weighttab[0];
			$_POST['weight2']=substr($weighttab[1],0,4)*16/10000;
			$_POST['length']=$data['length'];
			$_POST['width']=$data['width'];
			$_POST['height']=$data['height'];
			$_POST['location']=$data['location'];
			$_POST['invoice']=$data['invoice'];
			//$_POST['category_id'] ="";
			//$_POST['categoryarbonum']="";
			$_POST['accessoryen']=$data['accessory'];
			$_POST['condition_suppen']=$data['condition_supp'];
			$_POST['testen']=$data['test'];
			$_POST['description_suppen']=$data['description_supp'];
			$_POST['coloren']=$data['color_item'];
			
			$_POST['accessoryfr']=$data3['accessory'];
			$_POST['condition_suppfr']=$data3['condition_supp'];
			$_POST['testfr']=$data3['test'];
			$_POST['description_suppfr']=$data3['description_supp'];
			$_POST['colorfr']=$data3['color_item'];
			
			$_POST['image']=$data['image'];
			$_POST['remarque_interne']=$data['remarque_interne'];
			$sql4 = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$_POST['product_id'].'" and ebayyes=1 GROUP BY oc_category_description.CATEGORY_ID';
		//	echo $sql4;
			$req4 = mysqli_query($db,$sql4);
			$data4 = mysqli_fetch_assoc($req4);
		
			$nb_cat=mysqli_num_rows($req4);
			
			if($nb_cat==0 && $_POST['category_id']!=""){
				echo "<script>window.open('".$GLOBALS['WEBSITE']."/interne/ajoutcategorie.php?primary_cat=".$_POST['category_id']."');</script>";  
				//echo $_POST['category_id'];
				//echo "Nombre de lignes dans le résultat : " . $num_rows;
							//	exit();
			}elseif($nb_cat==1){
				$_POST['categoryname']=$data4['name'];
				$_POST['category_id']=$data4['category_id'];
			}else{
				while ($data4 = mysqli_fetch_assoc($req4)){
					if($data4['status']==1){
						$_POST['categoryname']=$data4['name'];
						$_POST['category_id']=$data4['category_id'];
					}
				}
				//print("<pre>".print_r ($data4,true )."</pre>");
				update_item_db($connectionapi,$_POST,$db);
			}
			

  if	(($_POST['namefr']==""&& $_POST['nameen']!="")||($_POST['accessoryfr']==""&& $_POST['accessoryen']!="")||($_POST['condition_suppfr']==""&& $_POST['condition_suppen']!="")||($_POST['testfr']==""&& $_POST['testen']!="")||($_POST['colorfr']==""&& $_POST['coloren']!="") )
			{
	/* 			$_POST['nameen']=addslashes($_POST['nameen']);
				$_POST['description_suppen']=addslashes($_POST['description_suppen']);
				$_POST=translate_field($_POST);
				$_POST['nameen']=stripslashes($_POST['nameen']);
				$_POST['description_suppen']=stripslashes($_POST['description_suppen']);
				//print("<pre>".print_r ($_POST,true )."</pre>");
										$sql2 = 'UPDATE `oc_product_description` SET `description_supp`="'.(addslashes($_POST['description_suppen'])).'",`color`="'.(addslashes(strtoupper($_POST['coloren']))).'",`description_mod`=1,`name`="'.(addslashes(strtoupper(strtolower($_POST['nameen'])))).'",`condition_supp`="'.addslashes($_POST['condition_suppen']).'" WHERE `language_id`=1 and `product_id` ='.$_POST['product_id'];
									//echo $sql2;
										$req2 = mysqli_query($db,$sql2); 
										$sql2 = 'UPDATE `oc_product_description` SET `description_supp`="'.(addslashes($_POST['description_suppfr'])).'",`color`="'.(addslashes(strtoupper($_POST['colorfr']))).'",`description_mod`=1,`name`="'.(addslashes(strtoupper(strtolower($_POST['namefr'])))).'",`condition_supp`="'.addslashes($_POST['condition_suppfr']).'" WHERE `language_id`=2 and `product_id` ='.$_POST['product_id'];
															//echo $sql2;
										$req2 = mysqli_query($db,$sql2); 
										mise_en_page_description($connectionapi,$_POST['product_id'],$db);  */
			}
	

			$new=1;
	
} 
	
?>

<html>
<head> 
<?// header('Access-Control-Allow-Origin: *');
//header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title>
		<script type="text/javascript" src="https://phoenixliquidation.ca/admin/view/javascript/jquery/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="https://phoenixliquidation.ca/admin/view/javascript/bootstrap/js/bootstrap.min.js"></script>
		<link href="<?echo $GLOBALS['WEBSITE'];?>

/admin/view/javascript/font-awesome/css/font-awesome.min.css?version=1" type="text/css" rel="stylesheet" />
	<link href="../stylesheet.css" rel="stylesheet">
		<script src="https://phoenixliquidation.ca/admin/view/javascript/common.js" type="text/javascript"></script>  

    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>  

</head>
<body bgcolor="<?if($resultebay){?>red<?}else{?>ffffff<?}?>">


<form id="form-product" class="form-horizontal" action="modificationitem.php?clone=<?echo $_GET['clone'];?>&update=yes&action=<?echo $_GET['action'];?>" method="post">
  <table style="text-align: left; width: 1000px; height: 12%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <td colspan="4" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center;">
			<img style="width: 488px; height: 145px;" alt="" src="https://phoenixsupplies.ca/image/catalog/cie/entetelow.jpg">
		</td>
      </tr>
	 </tbody>
	 </table>
 <table style="text-align: left; width: 1000px; height: 12%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="1">
	 <tr>
	 <td style="vertical-align: text-top;">
	  <table style="text-align: left; width: 200px; height: 1210px; margin-left: auto; margin-right: auto;" border="1" cellpadding="0" cellspacing="0">
<tbody>
<tr>
       <td style=" vertical-align: middle; background-color: #e4bc03;  text-align: center;width: 200px; height: 50px;">
	   <a href="listing.php?sku=<?echo (string)$_POST['sku'];?>" >Menu listing</a> 		
	 

        </td>
</tr>
<tr>
			<td style=" vertical-align: center;  background-color: #030282; color: white; text-align: center;  width: 200px; height: 50px;" >
			Photo: <br>
			</td>
</tr>
	 		<td  style="vertical-align: text-top; text-align: center; height: 200px; width: 200px;">
			<table>
			<tr>
			<td style="text-align: center;">			<?
			if($_POST['image']!="")echo '<img src="https://www.phoenixsupplies.ca/image/'.$_POST['image'].'" width="200">';
			?>
				<br><a href="uploadphoto.php?product_id=<?echo $_POST['product_id'] ?>" >Change Photo</a>
        </td>
</tr>
<tr>
			<td style=" vertical-align: center;  background-color: #030282; color: white; text-align: center;  width: 200px; height: 50px;" >
<?			$i=0;
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$_POST['product_id'];
			$req2= mysqli_query($db,$sql2); 
			if(mysqli_num_rows($req2)>0)echo 'Photos additionelles: ';
			
?>

			</td>
</tr>
	 		<td  style="vertical-align: text-top;  text-align: center; height: 928px; width: 200px;">			

<?   

			
			while($data2 = mysqli_fetch_assoc($req2))
			{

				?>
				<br>
				<img src="https://www.phoenixsupplies.ca/image/<? echo $data2['image'];?>" width="150"><br>
				
				<?
			}
			
?>
<br>
			
			</td>
			</tr>
			</table>
			</td>

</tr>
	 
</tbody>
	 </table>
	 </td>

	 <td>
	  <table style="text-align: top; width: 770px; height: 25%; margin-left: auto; margin-right: auto;" border="1" cellpadding="0" cellspacing="0">
<tbody>
      <tr>
	
        <td colspan="4" style="height: 50; background-color: #030282; color: white;  text-align: center;">
		<h1>Modification Product (<?echo $_POST['product_id']?>)</h1>
		</td>
		</tr>
		<tr>
		 <td colspan="4" style="vertical-align:  middle; height: 50; background-color: green; color: white;  text-align: center; "> 
			<legend><span id="hour" ><?echo $_POST['hhour'];?></span>:<span id="min"><?echo $_POST['hmin'];?></span>:<span id="sec"><?echo $_POST['hsec'];?></span>
			<input type="hidden" name="hhour" id="hhour" value="<?echo $_POST['hhour'];?>" />
			<input type="hidden" name="hmin" id="hmin" value="<?echo $_POST['hmin'];?>" />
			<input type="hidden" name="hsec" id="hsec" value="<?echo $_POST['hsec'];?>" /></legend>
		</td>
     </tr>
	 		<?if($resultebay!=""){?>
			   <tr>
        <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align:center;text-align: center;height: 24px; width: 342px;background-color: 
		
		red
		;color:white">
			<?echo $resultebay;?>
        </td>
 
	  </tr>
		<?}?>
		 <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 50px;text-align:center; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<a href="createbarcodetotal.php?type=both&qtemag=0&qtetot=1&product_id=<?echo $_POST['product_id'];?>&sku=<?echo (string)$_POST['sku'];?>" target="google" style="color:#ff0000"><strong>Creation LABEL</strong></a> 
		
		<input type="checkbox" id="verif"  name="processing" value="oui" />	<label>Proceed </label>
		
		<input type="checkbox" id="verif"  name="showerror" value="oui" />	<label>Show Error</label>

		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</td>

	  </tr>
	 		<tr>
			  <td style="vertical-align:  middle; background-color: #030282; color: white; width: 200px;">
			  <label>Sku</label>
	   </td>
        <td colspan="2" rowspan="1" style="vertical-align:  middle;text-align:center; ">
		<input id="sku" type="text" name="sku" value="<?echo (string)$_POST['sku'] ;?>" maxlength="255" autofocus="">
		<input type="hidden" name="hid_sku_ancien" value="<?echo (string)$_POST['sku'] ;?>"/>
	
		</td>
	        <tr>
        <td style="vertical-align:  middle; background-color: #030282; height: 50px;color: white; width: 200px;">
		<label>UPC:</label>
		</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">
		<?if($_POST['changeupc']=="changer"){?><input id="price"  type="text" name="newupc" value="<?echo $_POST['newupc'];?>" size="30" />
		<input type="hidden" name="upc" value="<?echo $_POST['upc']	;?>"/>	<?}else{?>
		<label><?echo (string) ($_POST['upc']);?></label>
		<br><a href="modifieritem.php?changeupc=oui&product_id_r=<? echo $_POST['product_id_r'];?>&product_id_no=<? echo $_POST['product_id_no'];?>&product_id=<? echo $_POST['product_id'];?>">Corriger UPC de l'item</a>
		<?}?>
		
		
		</td>

		</tr>
	 		<tr>
        <td style="vertical-align:  middle; background-color: #030282; height: 50px; color: white; width: 200px;">
		<label>Nu Ebay:</label>
		</td>
        <?if($_POST['marketplace_item_id']==0||$_POST['marketplace_item_id']==""){?>
		<td colspan="1" rowspan="1" style="background-color:red; color: white;vertical-align:  middle; text-align:center; height: 0px; ">
			<?
				echo $_POST['marketplace_item_id'];
?>

		<?}else{?><td colspan="1" rowspan="1" style="background-color:green; color: white;vertical-align:  middle; text-align:center; height: 0px; ">
			<a href="https://www.ebay.com//lstng?mode=ReviseItem&itemId=<?echo $_POST['marketplace_item_id'];?>&sr=wn&ReturnURL=https://www.ebay.com/sh/lst/active?offset=0" target='ebay' ><?echo $_POST['marketplace_item_id'];?></a>
						
		<?}?> 
		</td>

		</tr>

	<tr>

        <td style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; width: 200px;">
			<label>English Title:</label>
		</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center; <?if($_POST['englishcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
					
					<input id="nameen"  type="text" name="nameen" value="<?echo  htmlspecialchars($_POST['nameen'], ENT_QUOTES, 'UTF-8');?>"  maxlength="80" onchange="getTranslate(document.getElementById('nameen').value,'name','fr')"/>
					
					
		</td>
	</tr>

      <tr>
      <td style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; width: 200px;">
		 <label>French Title: </label>
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center; <?if($_POST['frenchcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
	
	<input id="namefr"  type="text" name="namefr" value="<?echo htmlspecialchars($_POST['namefr'], ENT_QUOTES, 'iso-8859-1');?>" maxlength="255" onchange="getTranslate(document.getElementById('namefr').value,'name','en')"/></h3>   
		</td>

        
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 25px; background-color: #030282; color: white; width: 200px;">
		<label>Model:</label>
		</td>
        <td style="vertical-align:  middle; height: 25px;  text-align:center;<?if($_POST['modelcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
		 <input id="model" type="text" name="model" value="<?echo $_POST['model'];?>" maxlength="20" />
		</td>

      </tr>

      <tr>
        <td style="vertical-align:  middle; background-color: #030282; color: white; width: 200px;">
		<label>Manufacturier:</label>         
		</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center;  <?if($_POST['manufacturercheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
<div class="label">

<select name="manufacturer_id">
			<option value="" selected></option>
<?
			$sql = 'SELECT * FROM `oc_manufacturer` order by name';

			// on envoie la requ�te
			$req = mysqli_query($db,$sql);
			// on fait une boucle qui va faire un tour pour chaque enregistrement
			$brandrecom="";
			while($data = mysqli_fetch_assoc($req))
				{
					$selected="";
					if (isset($_POST['manufacturer_id']) && $_POST['manufacturer_id']!=0){
						$test2=strtolower ($_POST['manufacturer_id']);
						$test1=strtolower ($data['manufacturer_id']);
						if ($test1==$test2) {
							$selected="selected";
						}
						//echo "allo";
					}else{
						$test2=strtolower ($data['name']);
						$test1=strtolower ($_POST['nameen']);
						//echo "allo2";
						if (strpos($test1, $test2) !== false) {
							//$selected="selected";
							//echo 'allo3';
							//$brandrecom[$i]
							$brandrecom=$brandrecom.",".$data['name']."@".$data['manufacturer_id'];
						}
					}
						

							
					?>
								<option value="<?echo $data['manufacturer_id'];?>" <?echo $selected;?>><?echo $data['name'];?></option>
					<?}?>
							</select><br>
							<input type="hidden" name="manufacturer_id_old" value="<?echo $_POST['manufacturer_id'];?>" />
					<?	
					//echo $brandrecom;
					$brandrecomtab=explode(',', $brandrecom);
					foreach($brandrecomtab as $brandrecomtab2){
						
						if($brandrecomtab2!=null ){
							//echo $brandrecomtab2;
							$brandrecomtab3=explode('@', $brandrecomtab2);
							echo '<input id="manufacturer_recom" class="element radio" type="radio" name="manufacturer_recom" value="'.$brandrecomtab3[1].'"/> 
									<label class="choice" for="etat_1">'.$brandrecomtab3[0].'</label><br>';
						}
					}	 
?>		
		<label>Add if not in the list:</label> <input id="manufacturersupp"  type="text" name="manufacturersupp" value="<?echo $_POST['manufacturersupp'];?>" maxlength="80" />
		</div>
        </td>

      </tr>
	  <tr>
        <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;"><label>Dimension:</label>
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center; <?if($_POST['dimensioncheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">

	<table width="100%">
		  <tr>
			<td style="text-align:center;">
			<label>Length</label> <input id="length" class="small_text" type="text" name="length" value="<?echo intval($_POST['length']);?>" maxlength="5" />
				</td>
					</tr>
				<tr>
				<td style="text-align:center;">
			
			<label>Width</label> <input id="width"  class="small_text" type="text" name="width" value="<?echo intval($_POST['width']);?>" maxlength="5" />
				</td>
								</tr>
				<tr>
				<td style="text-align:center;">

			<label>Height</label> <input id="height"  class="small_text" type="text" name="height" value="<?echo intval($_POST['height']);?>" maxlength="5" />
				</td>
			</tr>
			</table>
		</td>	
      </tr>
      <tr>
        <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">
		<label>Weight:</label>		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center; <?if($_POST['poidscheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
<table style="vertical-align:  middle; text-align:center;text-align: center;height: 16px; ">
		  <tr>
			<td style="text-align:center;"><label>Lbs</label> <input id="weight"  class="small_text" type="text" name="weight" value="<?echo $_POST['weight'];?>" maxlength="5" />
				</td>
											</tr>
				<tr>	
		<td style="text-align:center;"><label>Oz</label> <input id="weight2"  class="small_text" type="text" name="weight2" value="<?echo $_POST['weight2'];?>" maxlength="5" />
		</td>

		</tr>
</table>

      </tr>
      <tr>
       <td style="vertical-align:  middle; background-color: #030282; color: white; width: 200px;">
		<label>Categorie:</label>     
		</td>
        <td style="vertical-align:  middle; text-align:center; <?if($_POST['categoriecheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
    <input id="category_id"  type="text" name="category_id" value="<?echo $_POST['category_id'];?>" maxlength="80" /> 
		<?if ($_POST['categoryname']!=""){?><br><label>Categorie found: <span style="color: #ffffff;"><?echo $_POST['categoryname'];?></span></label><?}?>
		</td>

      </tr>
      <tr>
        <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">
		<label>Color</label>
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center;height: 50px;text-align:center; <?if($_POST['colorcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
		<table width="100%">
		  <tr>
		  <td><label>English</label> <input type="text" id="coloren" name="coloren" value="<?echo htmlspecialchars($_POST['coloren'], ENT_QUOTES, 'UTF-8');?>" maxlength="80" onchange="getTranslate(document.getElementById('coloren').value,'color','fr')"/></td>
			
		  </tr>
		  <tr>
			
			<td><label>French</label> <input type="text" id="colorfr" name="colorfr" value="<?echo htmlspecialchars($_POST['colorfr'], ENT_QUOTES, 'iso-8859-1');?>" maxlength="80" onchange="getTranslate(document.getElementById('colorfr').value,'color','en')"/></td>
		  </tr>
		</table>
		</td>

      </tr>

	  	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 50px;text-align:center; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<a href="createbarcodetotal.php?type=both&qtemag=0&qtetot=1&product_id=<?echo $_POST['product_id'];?>&sku=<?echo (string)$_POST['sku'];?>" target="google" style="color:#ff0000"><strong>Creation LABEL</strong></a> 
		<input type="checkbox" id="verif"  name="processing" value="oui" />	<label>Procede </label>
		<input type="checkbox" id="verif"  name="showerror" value="oui" />	<label>Show Error</label>
		<input id="saveForm1" class="button_text" type="submit" name="submit" value="Submit" />
		</td>

	  </tr>

 

	    <tr>
        <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">	
		<label>Extra Info:</label>		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  text-top; height: 50px;text-align:center; <?if($_POST['infosuppcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
	<input type="checkbox" id="verif"  name="infosuppcheck" value="oui" <?if($_POST['infosuppcheck']=="oui")echo "checked";?> <?if($focus=="infosuppcheck")echo "autofocus";?>/> 

		
<br>
		  <label>English</label><br> <textarea  rows="10" cols="50" id="condition_suppen" name="condition_suppen"  onchange="getTranslate(document.getElementById('condition_suppen').value,'condition_supp','fr')"><?echo htmlspecialchars($_POST['condition_suppen'], ENT_QUOTES, 'UTF-8');?></textarea>
		<br>
		  <label>French</label><br> <textarea id="condition_suppfr" rows="10" cols="50" name="condition_suppfr" onchange="getTranslate(document.getElementById('condition_suppfr').value,'condition_supp','en')"><?echo htmlspecialchars($_POST['condition_suppfr'], ENT_QUOTES, 'iso-8859-1');?></textarea>
		</td>


	</tr>
     <tr>
        <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;"><label>Description:</label></td>
        <td colspan="1" rowspan="1" style="vertical-align:  text-top; height: 50px;text-align:center;">
		<label>English</label><br><textarea  name="description_suppen" rows="10" cols="50" placeholder="Description" id="description_suppen"  onchange="getTranslate(document.getElementById('description_suppen').value,'description_supp','fr')"><?echo htmlspecialchars($_POST['description_suppen'], ENT_QUOTES, 'UTF-8');?></textarea>
		<br>
		<label>French</label><br><textarea name="description_suppfr" rows="10" cols="50" placeholder="Description in French" id="description_suppfr"  onchange="getTranslate(document.getElementById('description_suppfr').value,'description_supp','en')"><?echo htmlspecialchars($_POST['description_suppfr'], ENT_QUOTES, 'iso-8859-1');?></textarea>
		
		</td>

      </tr>
	    <tr>
  
		 <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">
		<label>COMMENT:</label> 
		</td>
		<td style="vertical-align:  middle; text-align:center; ">
		<input id="remarque_interne"  type="text" name="remarque_interne" value="<?echo $_POST['remarque_interne'];?>" maxlength="255" />
		</td>
		</tr>
	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 50px;text-align:center; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<a href="createbarcodetotal.php?type=both&qtemag=0&qtetot=1&product_id=<?echo $_POST['product_id'];?>&sku=<?echo (string)$_POST['sku'];?>" target="google" style="color:#ff0000"><strong>Creation LABEL</strong></a> 
		<input type="checkbox" name="processing" value="oui" />	<label>Procede </label>
		<input type="checkbox" name="showerror" value="oui" />	<label>Show Error</label>
		<input id="saveForm2" class="button_text" type="submit" name="submit" value="Submit" />
		</td>

	  </tr> 
	  
    </tbody>
  		
		<input type="hidden" name="form_id" value="67341" />
		<input type="hidden" name="etape" value="<?echo $_POST['etape'];?>" />
		<input type="hidden" name="categoryarbonum" value="<?echo $categoryarbonum;?>" />

		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />
		<input type="hidden" name="upc" value="<?echo (string)$_POST['upc'];?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" /> 
		<input type="hidden" name="efface_ebayid_cloner" value="<?echo $_POST['efface_ebayid_cloner'];?>" />
		<input type="hidden" name="image" value="<?echo $_POST['image'];?>" />
		<input type="hidden" name="etat" value="9" />
		<input type="hidden" name="nbvariance" value="<?echo $_POST['nbvariance'];?>" />
		<input type="hidden" name="categoryname" value="<?echo $_POST['categoryname'];?>" />
		
		<input type="hidden" name="quantitytotal" value="<?echo $_POST['quantitytotal'];?>"/> 
		<input type="hidden" name="ebay_id_refer" value="<?echo $_POST['ebay_id_refer'];?>"/> 
		<input type="hidden" name="new_ebay_listing" value="<?echo $_POST['new_ebay_listing'];?>"/> 
		<input type="hidden" name="ebay_id" value="<?echo $_POST['marketplace_item_id'];?>"/> 
  </table>
  </td>
</tr>
</table>

<script type="text/javascript">
var x;
var startstop = 0;
  var formEl = "#form-product";
  var buttonToDisable = "#saveForm";
  var buttonToDisable1 = "#saveForm1";
  var buttonToDisable2 = "#saveForm2";
  
$( document ).ready(function() {
 //  document.getElementById("alert-message").style.display = "none";
/*    $('#cellbeginedit').hide(); */
	start();
/* 	document.getElementById("saveForm").disabled = true;
	document.getElementById("saveForm1").disabled = true;
	document.getElementById("saveForm2").disabled = true; */
 validate();
  $(formEl + ' input').keyup(function(){
  	validate();
  });
  $(formEl + ' input').change(function(){
  	validate();
  });
  $(formEl + " select").change(function(){
  	validate();
  });
  if(document.getElementById('namefr').value==''){
	  getTranslate(document.getElementById('nameen').value,'name','fr')
  }
  if(document.getElementById('description_suppfr').value==''){
	  getTranslate(document.getElementById('description_suppen').value,'description_supp','fr')
  }
   if(document.getElementById('condition_suppfr').value==''){
	  getTranslate(document.getElementById('condition_suppen').value,'condition_supp','fr')
  }
});
/* $('#name').change(function() {
	alert(document.getElementById("name").value);
	const Reverso = require('../reverso-api/src/reverso.js');

	//const reverso = new Reverso();
	//$name=document.getElementById("name").value;
	reverso.getSpellCheck(document.getElementById("name").value, 'English', (response) => {

		alert(response);

	}).catch(err => {

		console.error(err);

	});
	getTranslate(document.getElementById("name").value,"name","fr");
}); */
$(function(){
    $( "input[type=checkbox]" ).on("change", function(){
        if($(this).is(':checked'))
            $(this).parent().css('background-color', 'green');
        else
            $(this).parent().css('background-color', 'red');
    });
});
 function SelectAll() {
        var items = document.getElementsByName('verif[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAll() {
        var items = document.getElementsByName('verif[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }	
function getTranslate(text_field,field,targetLanguage) {
	//alert(order_status_id_mod);

       $.ajax({
			method: "POST",
			url: '../translate.php',//'+ text_field +'
			data:{targetLanguage:targetLanguage,text_field:text_field},
            cache: false, 
            dataType: "JSON",  
		   
			beforeSend: function() {
						//	$('#button-customer').button('loading');
				/* 	document.getElementById("refresh").classList.remove('fa','fa-refresh');
					//$('#button-customer').button('loading');
					document.getElementById("refresh").classList.add('fa','fa-refresh','fa-spin');
				document.getElementById("alert-message").style.display = "block";	 */
			},
			complete: function() {
			/* 	 $('#button-customer').button('reset');
				 document.getElementById("alert-message").style.display = "none"; */
				 
			}, 
			success:function(json) {
			   // location.reload();//if (text_field!="")
				 //  alert(json['success']);
				  document.getElementById(field+targetLanguage).value=decodeHtml(json['success']);
			   	/* document.getElementById("refresh").classList.remove('fa','fa-refresh','fa-spin');
				document.getElementById("refresh").classList.add('fa','fa-refresh'); */
				//alert("name"+targetLanguage);
			},
			error:function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

      }); 
	  
 }

function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}
function startStop() { /* Toggle StartStop */

  startstop = startstop + 1;

  if (startstop === 1) {
    start();
    document.getElementById("start").innerHTML = "Stop";
  } else if (startstop === 2) {
    document.getElementById("start").innerHTML = "Start";
    startstop = 0;
    stop();
  }

}


function start() {
  x = setInterval(timer, 10);
} /* Start */

function stop() {
  clearInterval(x);
} /* Stop */

var milisec = 0;
var sec = <?echo $_POST['hsec'];?>; /* holds incrementing value */
var min = <?echo $_POST['hmin'];?>;
var hour = <?echo $_POST['hhour'];?>;

/* Contains and outputs returned value of  function checkTime */

var miliSecOut = 0;
var secOut = 0;
var minOut = 0;
var hourOut = 0;

/* Output variable End */

function timer() {
  /* Main Timer */


  miliSecOut = checkTime(milisec);
  secOut = checkTime(sec);
  minOut = checkTime(min);
  hourOut = checkTime(hour);

  milisec = ++milisec;

  if (milisec === 100) {
    milisec = 0;
    sec = ++sec;
  }

  if (sec == 60) {
    min = ++min;
    sec = 0;
  }

  if (min == 60) {
    min = 0;
    hour = ++hour;

  }


 // document.getElementById("milisec").innerHTML = miliSecOut;
  document.getElementById("sec").innerHTML = secOut;
  document.getElementById("min").innerHTML = minOut;
  document.getElementById("hour").innerHTML = hourOut;
  document.getElementById("hsec").value = secOut;
  document.getElementById("hmin").value = minOut;
  document.getElementById("hhour").value = hourOut;
}


/* Adds 0 when value is <10 */


function checkTime(i) {
  if (i < 10) {
    i = "0" + i;
  }
  return i;
}

function reset() {


  /*Reset*/

  milisec = 0;
  sec = 0;
  min = 0
  hour = 0;

  document.getElementById("milisec").innerHTML = "00";
  document.getElementById("sec").innerHTML = "00";
  document.getElementById("min").innerHTML = "00";
  document.getElementById("hour").innerHTML = "00";

}
 
  

function validate() {
  var inputsWithValues = 0;
  
  // get all input fields except for type='submit'
  var inputs = $(formEl + " input");

  inputs.each(function(e) {
    // if it has a value, increment the counter
    if ($(this).val()) {
      inputsWithValues += 1;
    }
  });
  
  if (inputsWithValues == inputs.length && validateCheckbox() && validateSelects()) {
    $(buttonToDisable).removeClass("disabledButton");
	$(buttonToDisable1).removeClass("disabledButton");
	$(buttonToDisable2).removeClass("disabledButton");
  } else {
    $(buttonToDisable).addClass("disabledButton");
	$(buttonToDisable1).addClass("disabledButton");
	$(buttonToDisable2).addClass("disabledButton");
  }
}
  
  function validateCheckbox(){
    var checkboxChecked = false;
  	$(formEl + " [type='checkbox']").each(function(){
    	if($(this).is(":checked")){
        	checkboxChecked = true;
          	return true;
        }
    })
    return checkboxChecked;
  }
  
  function validateSelects(){
  	var selects = $(formEl + " select");
    var selectsWithValues = 0;
    selects.each(function(e) {
      // if it has a value, increment the counter
      if ($(this).val()) {
        selectsWithValues += 1;
      }
    });
    
    if(selectsWithValues == selects.length){
    	return true;
    }else{
    	return false;
    }
  } 	
</script>
</body>
</html>

<? 
//echo $_POST['category_id'];
//echo $_GET['clone'];
// on ferme la connexion &agrave; mysql 
mysqli_close($db); ?>