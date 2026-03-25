<? 
//echo (string)$_POST['sku'] ;
ob_start();
// on se connecte à MySQL 
include 'connection.php';

if(isset($_POST['sku']))
	$sku=(string)$_POST['sku'] ;
if(!isset($bgcolor))
	$bgcolor ='ffffff';

$DateNOW = date('Y-m-d');


if(isset($_POST['anc_loc']) && $_POST['anc_loc']!=""){
	$anc_loc_origine=$_POST['anc_loc'];

  $anc_loc= str_replace(' ','',$_POST['anc_loc']);
 // $anc_loc=$_POST['anc_loc'];

}

if(isset($_POST['product_select']))
 {
	//print("<pre>".print_r ($_POST,true )."</pre>");
    if ((isset($_POST['new_loc']) && $_POST['new_loc']!=""))
    {
		
		foreach($_POST['product_select'] as $product_select)  
			{	
             //   $new_loc= str_replace('-','',$_POST['new_loc']);
             //   $new_loc= str_replace(' ','',$new_loc);
			 $new_loc=$_POST['new_loc'];
				$sku=$_POST['sku'.$product_select];
				$sql = 'SELECT *
				FROM oc_product
				where  product_id ='.$product_select;
			//	echo $sql;
				$req = mysqli_query($db,$sql);
				// on fait une boucle qui va faire un tour pour chaque enregistrement 
				$data = mysqli_fetch_assoc($req);
				$quantitysql=' ';
			//	echo $_POST['quantity_hid'.$sku].'<br>';
				if($_POST['quantity_val'.$sku]<>$_POST['quantity_hid'.$sku] && $_POST['quantity_val'.$sku]!="" ){
					$quantitysql=' quantity='.$_POST['quantity_val'.$sku].' , ';
					$sql2 = 'UPDATE `oc_product`SET quantity=quantity+'.($_POST['quantity_val'.$sku]-$_POST['quantity_hid'.$sku]).',ebay_last_check="2020-09-01" WHERE product_id ='.$product_select;
        //		echo $sql2.' IF<br><br>';
					$req2 = mysqli_query($db,$sql2); 
				}
				$sql2 = 'UPDATE `oc_product`SET '.$quantitysql.'location="'.strtoupper($new_loc).'",anc_loc="'.strtoupper($data['location']).'" WHERE product_id ='.$product_select;
        	//	echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);  
				if($quantity!=""){
				//	update_to_ebay($connectionapi,0,($_POST['qtytot'.$product_select]+$_POST['quantity_val'.$sku]-$_POST['quantity_hid'.$sku]),$_POST['marketplace_item_id'.$product_select],$product_select);
			//	revise_ebay_product_inventaire_sku_erreur($connectionapi,$_POST['marketplace_item_id'.$product_select],$product_select,($_POST['qtytot'.$product_select]+$_POST['quantity_val'.$sku]-$_POST['quantity_hid'.$sku]));
				}
				//revise_ebay_product_inventaire_sku_erreur($connectionapi,$_POST['marketplace_item_id'.$product_select],$product_select,($_POST['qtytot'.$product_select]+$_POST['quantity_val'.$sku]-$_POST['quantity_hid'.$sku]));

			}
			if($_POST['createlabel']=="yes"){
				echo '<script>window.open("createlabeltablette.php?tablette='.$new_loc.'","etiquette")</script>';
			}
    }
}

if(isset($_GET['trimmer'])){
    $sql = 'SELECT *
		FROM oc_product 
		where  anc_loc ="" and location !="" order by quantity '; //LIMIT 200 and P.ebay_id=292612778604';  $sql.'<br><br>';/*P.username = "marissa"*/
		// on envoie la requête **** 
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
// merge quantity
$itemcount=0;
	while($data = mysqli_fetch_assoc($req)){
        if($data['quantity']>0){
       //     $new_loc= str_replace('-','',$data['location']);
        //    $new_loc= str_replace(' ','',$new_loc);
		$new_loc= $data['location'];
        }else{
            $new_loc="";
        }
        $sql2 = 'UPDATE `oc_product`SET location="'.strtoupper($new_loc).'",anc_loc="'.$data['location'].'" WHERE product_id ='.$data['product_id'];

		echo $sql2."<br>".$data['quantity']."<br>";

		$req2 = mysqli_query($db,$sql2);
    }
}
$sql = 'SELECT *,PD.name as nameen,P.quantity_anc,
p.unallocated_quantity,P.location AS location_entepot,
P.quantity,P.location AS location_entrepot 
FROM `oc_product` AS P 
LEFT JOIN `oc_product_description` AS PD on P.product_id=PD.product_id 
 

where   PD.language_id=1   and  (P.location like "'.(isset($anc_loc)?$anc_loc:'').'" or P.location like "'.(isset($anc_loc_origine)?$anc_loc_origine:'').'") and  P.location !="" and P.quantity >0 order by P.location '; //LIMIT 200 and P.ebay_id=292612778604';  $sql.'<br><br>';/*P.username = "marissa"*/


	//echo $sql;
$req = mysqli_query($db,$sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement 
echo "<br>Nombre restant: ". mysqli_num_rows($req)."<br>";
// merge quantity
$itemcount=0;
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title>
	 
	<script type="text/javascript">

function toggleSelectAll(mainCheckbox) {
		var items = document.getElementsByName('product_select[]');
		for (var i = 0; i < items.length; i++) {
			if (items[i].type == 'checkbox') {
				items[i].checked = mainCheckbox.checked;
			}
		}
	}		
</script>
<title>receipt</title>



<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
<link href="stylesheet.css" rel="stylesheet">
<script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>



<script src="<?echo $GLOBALS['WEBSITE'];?>/catalog/view/javascript/qr/qrcode.js" type="text/javascript"></script>


</head>
<body bgcolor="ffffff">
	<?if(isset($_POST['new_loc'])){?>
PRODUCT SKU______<input id="sku"  type="text" name="sku" value="" size="30" autofocus onchange="checked_sku()" />
<?}?>
<form id="form_67341" class="appnitro" action="trf_inventaire_location.php" method="post" enctype="multipart/form-data">
ANCIENNE LOCATION <input id="anc_loc"  type="text" name="anc_loc" value="<? echo (isset($_POST['anc_loc'])?$_POST['anc_loc']:'');?>" size="30" <?if (!isset($_POST['new_loc'])){?>autofocus<?}?>/>
<br>NOUVELLE LOCATION <input id="new_loc"  type="text" name="new_loc" value="" size="30" />
<br>
LABEL TABLETTE <input type="checkbox" id="createlabel" name="createlabel" value="yes"/>
<br>
<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
<div class="form_description">
<?if(!isset($dir_name)){
	date_default_timezone_set("America/New_York"); 
	$pagenu=1;$itemcount=0;?>
<?
		if($pagenu&1){
			echo '<p class="single_record"> </p>';
			//echo "odd";
		}else{
			echo '<p class="single_record"> </p><br><p class="single_record"> </p>';
			//echo "even";
		}
		$pagenu=1;$itemcount=0;?>


<table border="1" width="100%">
	<tr>

		<th bgcolor="ff6251">
		<input type="checkbox" onclick="toggleSelectAll(this)" value="Select All"> 


	</th>

	<th bgcolor="ff6251">
SKU
	</th>
		<th bgcolor="ff6251">
	Titre
	</th>
    <th bgcolor="ff6251">TOT
	
	</th>
	<th bgcolor="ff6251">STB
	
	</th>
		<th bgcolor="ff6251">
LOC
	
	</th>
    <th bgcolor="ff6251">ENT
	
	</th>
		<th bgcolor="ff6251">
VAL
	
	</th>
	<th bgcolor="ff6251">
LOC
	
	</th>
	<th bgcolor="ff6251">
	Ebay-ID
	
	</th>
	</tr>
	<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
<?

	while($data = mysqli_fetch_assoc($req)){
		
		//if((($data['quantity']+$data['unallocated_quantity'])<>$data['ebay_quantity'])||($data['quantity']>$data['quantity_anc']&&$data['location_entepot']=="" || strpos($data['location_entrepot'],"agasin") ||strpos($data['location_entrepot'],"MAGASIN"))){
	?>
						<tr id="tr<?echo $data['sku'];?>" <?if($data['marketplace_item_id']==0){?>style="background-color: red; "<?}?>>
						<td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
								<input type="checkbox" id="<?echo $data['sku'];?>" name="product_select[]" value="<?echo $data['product_id'];?>"/><?echo '<img height="80" src="'.$GLOBALS['WEBSITE'].'image/'.$data['image'].'"/>';?>


						</td>
						<td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
					<table align="center">
                        <tr><td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
                        <br> <div id="qrcode<?echo (string)$data['sku'] ;?>">									
<script type="text/javascript">
var qrcode = new QRCode("qrcode<?echo (string)$data['sku'] ;?>", {
text: "<?echo (string)$data['sku'] ;?>",
width: 80, 
height: 80,
colorDark : "#000000",
colorLight : "#ffffff",
correctLevel : QRCode.CorrectLevel.H
});	
$("#qrcode<?echo (string)$data['sku'] ;?> > img").css({"margin":"auto"});
</script>
<br>
<br><a href="listing.php?anc_loc=<?echo (isset($anc_loc_origine)?$anc_loc_origine:'');?>&pagesource=trf_inventaire_location.php&changeupc=oui&product_id=<? echo $data['product_id'];?>">Corriger SKU</a>
		
</td></tr>
<tr><td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
<input id="sku<?echo $data['product_id'];?>"  type="hidden" name="sku<?echo $data['product_id'];?>"  value="<?echo $data['sku'];?>" />
<input id="ebay_id<?echo $data['product_id'];?>"  type="hidden" name="ebay_id<?echo $data['product_id'];?>"  value="<?echo $data['marketplace_item_id'];?>" />
<input id="upc<?echo $data['product_id'];?>"  type="hidden" name="upc<?echo $data['product_id'];?>"  value="<?echo $data['upc'];?>" />

<div  style="text-align: center;font-size: 15px; "><strong><?echo (string)$data['sku'] ;?></strong></div>
</td></tr>
</table>
							
						</td>


						<td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
						
			
						<a href="<?echo $GLOBALS['WEBSITE'];?>/interne/pretlister.php?product_id=<?echo $data['product_id'];?>&action=listing" target='listing' ><?echo $data['nameen'];?></a>
						</td>

						<td >
						<?echo $data['quantity_anc'];?>
						<input id="qtytot<?echo $data['product_id'];?>"  type="hidden" name="qtytot<?echo $data['product_id'];?>"  value="<?echo $data['quantity_anc'];?>" />
						</td>
                        <td >
						
						<?echo $data['unallocated_quantity'];?>

						</td>
						<td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
						<?echo $data['location_magasin'];?>
						</td>
                        <td >
						
						<?echo $data['quantity'];?>

						</td>
						
						<td class="text-center" id="entrepot_valtab<?php echo  $data['sku']; ?>" >					
									
									<div id="entrepot_valedit<?php echo  $data['sku']; ?>"><i  class="pentrepot_val" rel="<?php echo  $data['sku']; ?>" rel1="0"> 0</i>
									</div>
									
								
									<script type="text/javascript">

									$('#entrepot_valedit<?php echo  $data['sku']; ?>').change(function () { 
										//alert(document.getElementById("entrepot_val").value); 
										rel='<?php echo  $data['sku']; ?>';
										rel1=document.getElementById("entrepot_val<?php echo  $data['sku']; ?>").value;
										editEntrepot_val(rel,rel1);
										html ='<i  class="pentrepot_val" rel="'+ rel +'" rel1="'+ rel1 +'"> '+ rel1 +' </i>';
										$('#entrepot_valedit'+'<?php echo  $data['sku']; ?>').html(html);
									});

									</script>	
						<input id="quantity_val<?echo $data['sku'];?>"  type="hidden" name="quantity_val<?echo $data['sku'];?>" value="" />
						<input id="quantity_hid<?echo $data['sku'];?>" type="hidden" name="quantity_hid<?echo $data['sku'];?>" value="<?echo $data['quantity'];?>"/>


						</td>
						<td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
						<?echo $data['location_entrepot'];?>
						</td>	
						<td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
						<a href="https://www.ebay.com/itm/<?echo $data['marketplace_item_id'];?>" target='ebay2' ><?echo $data['marketplace_item_id'];?></a>
						
						</td> 

				
			<?
			$itemcount++;
	//	}			
		//$j++;
		//echo $j;
		
		
	}
		?>
</table>
<?
echo "NB a verifier:".$itemcount;
}
?>
		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />


</form>
<script>
	$(document).on('click','.pentrepot_val',function() {
    rel=$(this).attr('rel');
    rel1=$(this).attr('rel1');
  	 html ='<input type="text" id="entrepot_val'+rel+'" name="entrepot_val" class="form-control entrepot_val'+rel+'" value="" />';
    $('#entrepot_valedit'+rel).html(html);
	document.getElementById('entrepot_val'+rel).autofocus = true
  });
     function checked_sku(sku) {
        item = document.getElementById('sku').value;
      
        try {
        // code that we will 'try' to run
            document.getElementById(item).checked=true;
            document.getElementById('sku').value="";
			if(document.getElementById('quantity_val' + item).value){
				qteval=parseInt(document.getElementById('quantity_val' + item).value);
			}else{
				qteval=0;
			}
			qteactuel=parseInt(document.getElementById('quantity_hid' + item).value);
			if(qteval+1==qteactuel){
            	document.getElementById('tr' + item).style.backgroundColor='green';
			}else{
				document.getElementById('tr' + item).style.backgroundColor='yellow';
			}
			
			document.getElementById('quantity_val' + item).value=qteval+1;
			//html='<i class="pentrepot_val" rel="065935828921VG" rel1="0"> 0</i>
			html='<i  class="pentrepot_val" rel="' +item+'" rel1="' + parseInt(qteval+1) +'"> ' + parseInt(qteval+1) +'</i>';
			$('#entrepot_valedit' + item).html(html);
        } catch(error) {
        // code to run if there are any problems
            //error.textContent = "Please enter a valid number"
           // error.style.color = "red"
            alert('PAS DANS LOCATION');
            document.getElementById('sku').value="";
        }
       
    }	
	function editEntrepot_val(sku,entrepot_val) {
		qteactuel=parseInt(document.getElementById('quantity_hid' + sku).value);
		if(entrepot_val==qteactuel){
            	document.getElementById('tr' + sku).style.backgroundColor='green';
			}else{
				document.getElementById('tr' + sku).style.backgroundColor='yellow';
			}
			
			document.getElementById('quantity_val' + sku).value=entrepot_val;
 	
			//document.getElementById('entrepot_valtab'+sku).style.backgroundColor='green';
			//document.getElementById('entrepot_valtab'+sku).style.color='white';
      
	}
</script>
</body>
</html>
<?  // on ferme la connexion à 

mysqli_close($db); 
ob_end_flush();?>