<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr&eacute;e la requête SQL verifier les ordres 
// savoir ledernier id 
if($_POST['variant_id']!=""){
	//echo $_POST['variant_id'];
	header("location: listing.php?sku=".$_POST['variant_id']); 
	exit();
}
if ($_GET['insertion'] != ""){
	$_POST['insertion']=$_GET['insertion'];
	$_POST['etape']=0;
	}
			$sql3 = 'SELECT * FROM `oc_product` where product_id = "'.$_POST['product_id'].'"';
	//echo $sql;
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
if($_GET['etat'])$_POST['etat']=$_GET['etat'];
$etat=explode(",",$_POST['etat']);
$espacenb=3-strlen ($etat[1]);
$espace="";
$_POST['sku']=$data3['sku'];
$_POST['condition_id']=$data3['condition_id'];

//for ($i=1;$i<=$espacenb;$i++){
	$espace.="_";
//}

//echo $espace;
if(isset($_GET['sku'])){
 $pos=strpos($_GET['sku'],"_");
	if ($pos === false) {
		$_POST['sku']=$_GET['sku'];
		//echo $_POST['sku'];
	} else {
		$temp=explode("_",$_GET['sku']);
		$_POST['sku']=$temp[0];
		//echo $_POST['sku'];
	} 
$_POST['product_id']=$_GET['product_id'];
//$_POST['action']=$_GET['action'];
}
if ($_POST['carac']!=""){
	
	$verif="OK";
	if($etat[0]!=$_POST['condition_id']){
		$_POST['condition_id']=$etat[0];
		$_POST['sku']=substr((string)$_POST['sku'] ,0,12).$etat[1];
		$verif="change_etat";
		
	}
	//echo "oui".$sku;
	$product=get_product((string)$_POST['sku']."_".$_POST['carac']);
	if($product[0]['sku']!=""){
		//header("location: listing.php?sku=".$product[0]['sku']); 
		exit();
	}else{
		$product_id=cloner_item ($connectionapi,$verif, $_POST['condition_id'],substr((string)$_POST['sku'] ,0,12).$etat[1]."_".$_POST['carac'],$_POST['product_id'],$db);
		$sku=substr((string)$_POST['sku'] ,0,12).$etat[1]."_".$_POST['carac'];
		//echo "oui".$sku;
		//echo "non";
		//echo $sku."-".$_POST['carac']."-".$_POST['product_id'];
		
		
		header("location: uploadphoto.php?product_id=".$product_id); 
		exit();
	}
		
}

if ($_GET['action']=="clone" && $_POST['carac']==""){
	//echo "allo";
		if($etat[0]!=$_POST['etatorigin']){
			//echo $_POST['sku'];
			
			$_POST['sku']=substr((string)$_POST['sku'] ,0,12).$etat[1];//$etat[1];
			$verif="OK";
			$product=get_product((string)$_POST['sku']);
				if($product[0]['sku']!=""){
					header("location: listing.php?sku=".$product[0]['sku']); 
			//		//echo "exist";
					exit();
				}else{
			//		//echo "a cloner";
					$product_id=cloner_item ($connectionapi,$verif, $etat[0],$_POST['sku'],$_POST['product_id'],$db);
					if($product_id==0){
						//header("location: insertionitem.php?upc=".(string)$_POST['sku']."&action=listing"); 
					}else{
						header("location: uploadphoto.php?product_id=".$product_id); 
					}
					exit();
				}
		}

}
//print("<pre>".print_r ($_POST,true )."</pre>"); 
//echo $espace;

?>


<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />

</head>
<body bgcolor="ffffff">


<form id="form_67341" class="appnitro" action="clonerproduct.php?action=clone" method="post">
<table style="text-align: left; width: 1053px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">


      <tr align="center">
        <td colspan="6" rowspan="1" style="vertical-align:  middle; ">
		<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>image/catalog/cie/entetelow.jpg"><br>
        </td>
      </tr>
      
	     <tr>
        <td colspan="1" style="vertical-align: middle; background-color: #e4bc03;  text-align: center;"><h3><a href="menulisting.php" >Retour au MENU</a></h3>
        </td>
        <td colspan="5" style="vertical-align: middle;height: 50; background-color: #030282; color: white;  text-align: center;">
		<h1>Cloner un Produit</h1><?if ($_POST['erreur_dimensions_poids']!="") echo '<h3><font color="red">'.$_POST['erreur_dimensions_poids'].'</font></h3>'; ?>
        </td>
      </tr>

		<tr>

        <td  colspan="1" style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; ">
			Sku a cloner:
			
		</td>
        <td colspan="5" rowspan="1" style="vertical-align:  middle;text-align: center; height: 50px; ">
			<?echo $_POST['sku'];?>
		</td>
	</tr>	
	<tr>
			 <td colspan="1" style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; ">
			Condition: 
        </td>
	        <td colspan="5" rowspan="1" style="vertical-align:  middle; text-align: center;height: 50px; ">
			<table style="vertical-align:  middle;">
		  <tr>
			<td><?/*echo strpos($_POST['etat'],"9,");*/?><input id="etat_1" class="element radio" type="radio" name="etat" value="9," <?if (strpos($_POST['etat'],"9,")==0){?>checked<?}?>/> 
				<label class="choice" for="etat_1">New (Sealed)</label></td>
				<td></td>
						  </tr>
						  	<tr>
			<td>____________________</td>
			<td></td>
		  </tr>	
				<tr>
			<td>***OPENBOX Section***</td>
			<td></td>
		  </tr>		  
		  <tr>
			<td><input id="etat_2" class="element radio" type="radio" name="etat" value="99,NO" <?if (strpos($_POST['etat'],"99,NO")!== false){?>checked<?}?>/> 
				<label class="choice" for="etat_2">Grade A</label> <br /></td>
			<td><input id="etat_5" class="element radio" type="radio" name="etat" value="8,LN" <?if (strpos($_POST['etat'],"8,LN")!== false){?>checked<?}?>/> 
				<label class="choice" for="etat_4">Grade A (Film voir JO pour utiliser) - Like New</label> </td>
			
	
		  </tr>
		  <tr>
			<td><input id="etat_4" class="element radio" type="radio" name="etat" value="22,R" <?if (strpos($_POST['etat'],"22,R")!== false){?>checked<?}?>/> 
				<label class="choice" for="etat_3">Grade B - Minor Marks (Excellent Condtion)</label> <br /> </td>
			<td><input id="etat_6" class="element radio" type="radio" name="etat" value="7,VG" <?if (strpos($_POST['etat'],"7,VG")!== false){?>checked<?}?>/> 
				<label class="choice" for="etat_5">Grade C - Some Marks but (Very Good Condition)</label><br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_7" class="element radio" type="radio" name="etat" value="6,G" <?if (strpos($_POST['etat'],"6,G")!== false){?>checked<?}?>/> 
				<label class="choice" for="etat_6">Grade D - Many Marks but (Good Condition)</label> </td>
			<td><input id="etat_8" class="element radio" type="radio" name="etat" value="5,P" <?if (strpos($_POST['etat'],"5,P")!== false){?>checked<?}?>/> 
				<label class="choice" for="etat_7">Grade E - Real Used item with TOO much Marks (Poor Condition)</label><br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_9" class="element radio" type="radio" name="etat" value="1,FP" <?if (strpos($_POST['etat'],"1,FP")!== false){?>checked<?}?>/> 
				<label class="choice" for="etat_8">Defectueux pour piece ou a reparer</label> </td>
			<td><input id="etat_3" class="element radio" type="radio" name="etat" value="2,SR" <?if (strpos($_POST['etat'],"2,SR")!== false){?>checked<?}?>/> 
				<label class="choice" for="etat_3">Manufacturer Refurbished</label></td>
		  </tr>
		</table>
				</td>
	</tr>
	<tr>
<?
			$products=get_products(substr((string)$_POST['sku'] ,0,12));


?>
        <td colspan="1" style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; ">
			 Variants Disponibles:
		</td>
        <td colspan="5" rowspan="1" style="vertical-align:  middle; height: 50px; ">
		<select id="id_select2_example" name="variant_id">
			<option value="" selected></option>
		<?			foreach($products as $product){
?>
		<option data-img_src="<? echo $GLOBALS['WEBSITE'];?>/image/<?echo $product['image_product'];?>" value="<?echo $product['sku'];?>" ><?echo "(".$product['product_id'].")"." TITRE:".$product['name']." ----- SKU:".$product['sku']." ----- QTY:".$product['quantity'];?></option>
<?
			}?>
			</select>
			<!-- scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js"></script>
<script type="text/javascript">
    function custom_template(obj){
            var data = $(obj.element).data();
            var text = $(obj.element).text();
            if(data && data['img_src']){
                img_src = data['img_src'];
                template = $("<div><img src=\"" + img_src + "\" style=\"height:80px;\"/>" + text + "</div>");
                return template;
            }
        }
    var options = {
        'templateSelection': custom_template,
        'templateResult': custom_template,
    }
    $('#id_select2_example').select2(options);
    $('.select2-container--default .select2-selection--single').css({'height': '100px'});

</script>
							<input type="hidden" name="variant_id_old" value="<?echo $_POST['variant_id'];?>" />
		</td>
	</tr>	
	<tr>
	<td colspan="6" style="vertical-align:  middle;  text-align: center; background-color: red; color: white; height: 0px; ">
				*** Utiliser UNIQUEMENT si l'article est DIFFERENT ou LISTER EN LOT sinon ne rien indiquer dans le champ ci-dessous ***
				  </td>
		</tr>
		<tr>

        <td colspan="1" style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; ">
			 (Max 2 caracteres):
		</td>
        <td colspan="5" rowspan="1" style="vertical-align:  middle; height: 50px; ">
			<input id="carac"  type="text" name="carac" value="" size="4" autofocus /><input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</td>
	</tr>	
  
  </table>		
		
		<input type="hidden" name="etatorigin" value="<?echo $_GET['etat'];?>" />
		<input type="hidden" name="product_id" value="<?echo $_GET['product_id'];?>" />
		<input type="hidden" name="action" value="<?echo $_GET['action'];?>" />
		<input type="hidden" name="sku" value="<?echo $_POST['sku'];?>" />
		<input type="hidden" name="insertion" value="<?echo $_POST['insertion'];?>" />
		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />
</form>


</body>
</html>
<? // on ferme la connexion à mysql /* z */

mysqli_close($db); ?>