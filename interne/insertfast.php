<?
ob_start();

include 'connection.php';
if (isset($_POST['upc'] ) && (string)$_POST['upc'] !="" && strlen($_POST['upc'])>11 && strlen($_POST['upc'])<14
&& $_POST['category_id']!="" && $_POST['etat']!=""){
	$pos = strpos($_POST['upc'], 'U');
	if ($pos !== false) {
		echo "The string U was found in the UPC";
		echo " and exists at position $pos";
		$_POST['upc']=str_replace('U','',$_POST['upc']);
	}
			//echo $_POST['upc'];	
           // echo $_POST['condition_id'];	
            $etat=explode(",",$_POST['etat']);
            $_POST['condition_id']= $etat[0];
            if($_POST['variant']!=""){
                $variant="_".$_POST['variant'];
            }else{
                $variant="";
            }
            $_POST['sku']=substr((string)$_POST['upc'] ,0,12).$etat[1].$variant;	
			$products=get_product((string)$_POST['sku'])?? [];
			 if(count($products)==0){
				$_POST['weight']=0;
                $_POST['weight2']=4;
                $_POST['manufacturer_id']=0;
                $_POST['length']=7;
                $_POST['width']=4;
                $_POST['height']=1;
                $_POST['model']="none";
                $_POST['price']=9999;
                $_POST['marketplace_item_id']=0;
                $_POST['price_with_shipping']=9999;
                $_POST['quantity']=$_POST['nb'];
                $_POST['product_id']=insert_item_db($connectionapi,$_POST,$db);
					 echo "<br>non existant";
                     $bgcolor="yellow";
					 //exit();
					 if($_POST['location']!=""){
						$sql2 = 'UPDATE `oc_product` SET `location`="'.$_POST['location'].'" where product_id='.$_POST['product_id'];
                        $req2 = mysqli_query($db,$sql2);
						echo $sql2.'<br><br>';
					}
			 }else{
                $_POST['product_id']=$products[0]['product_id'];
					  echo "<br>existant";
                      echo "<br>".$_POST['product_id'];
                      $bgcolor="green";
                      $sql2 = 'UPDATE `oc_product` SET unallocated_quantity=unallocated_quantity+'.$_POST['nb'].',ebay_last_check="2020-09-01" where product_id='.$_POST['product_id'];
                        //echo $sql2.'<br><br>';
                        $req2 = mysqli_query($db,$sql2);

             //           $result=revise_ebay_product_inventaire($connectionapi,$products[0]['marketplace_item_id'],$_POST['product_id'],$products[0]['quantity']+1,$db,"oui");
					 // exit();
			 }
             if($_POST['condition_id']!=9 || ($_POST['condition_id']==9 && strlen($_POST['sku'])>12)){
				echo '<script>window.open("createbarcodetotal.php?all=no&type=both&qtemag=0&qtetot='.$_POST['nb'].'&product_id='.$_POST['product_id'].'&sku='.(string)$_POST['sku'].'","etiquette")</script>';
             }
}else{
	$_POST= array(
			'category_id'=>"" ,
			'etat'=>"",
			'upc'=>"",
			'location'=>"",
			'quantity'=>"1",
			'variant'=>"",
			'etatorigin'=>"",
			'product_id'=>"",
			'action'=>"",
			'insertion'=>"",
			'condition_id'=>"",
	);
    if(isset($_GET['action']) && $_GET['action']=="add"){
        $bgcolor="red";
    }else{
        $bgcolor="ffffff";
    }
}	
?>
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
</head>
<body bgcolor="<? echo $bgcolor;?>">
<form id="form_67341" class="appnitro" action="insertfast.php?action=add" method="post">
<table style="text-align: left; width: 1053px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
      <tr align="center">
        <td colspan="6" rowspan="1" style="vertical-align:  middle; ">
		<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>image/catalog/cie/entetelow.jpg"><br>
        </td>
      </tr>
	     <tr>
        <td colspan="1" style="vertical-align: middle; background-color: #e4bc03;  text-align: center;"><h3><a href="menulisting.php" >Retour au MENU</a></h3>
		<a href="algopix_ajout.php"  style="color:#ff0000"><strong>Ajout Algopix</strong></a> 
	   </td>
        <td colspan="5" style="vertical-align: middle;height: 50; background-color: #030282; color: white;  text-align: center;">
		<?php
if (!empty($_POST['erreur_dimensions_poids'])) {
    echo '<h3><font color="red">' . htmlspecialchars($_POST['erreur_dimensions_poids']) . '</font></h3>';
}
?>        </td>
      </tr>
      <tr>
			 <td colspan="1" style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; ">
			Type produit: 
        </td>
	        <td colspan="5" rowspan="1" style="vertical-align:  middle; text-align: center;height: 50px; ">
			<table style="vertical-align:  middle;">
            <tr>
                <td><input id="category_id_2" class="element radio" type="radio" name="category_id" value="617" <?if ($_POST['category_id']== 617){?>checked<?}?> onchange="change_autofocus()"/> 
                    <label class="choice" for="category_id_2">Movie</label> <br /></td>
                <td><input id="category_id_5" class="element radio" type="radio" name="category_id" value="139973" <?if ($_POST['category_id']== 139973){?>checked<?}?> onchange="change_autofocus()"/> 
                    <label class="choice" for="category_id_4">Game</label> </td>
            </tr>
            </table>
				</td>
	</tr>
	<tr>
<td  colspan="1" style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; ">
	Location:
</td>
<td colspan="5" rowspan="1" style="vertical-align:  middle;text-align: center; height: 50px; ">
<input id="location"  type="text" name="location" value="<?echo $_POST['location'];?>" size="10"  />
</td>
</tr>
	<tr>
<td  colspan="1" style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; ">
	Nombre a ajouter:
</td>
<td colspan="5" rowspan="1" style="vertical-align:  middle;text-align: center; height: 50px; ">
<input id="nb"  type="text" name="nb" value="1" size="3"  />
</td>
</tr>	
		<tr>
        <td  colspan="1" style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; ">
			UPC a ajouter:
		</td>
        <td colspan="5" rowspan="1" style="vertical-align:  middle;text-align: center; height: 50px; ">
        <input id="upc"  type="text" name="upc" value="" size="13" autofocus />
<br>
        <input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</td>
	</tr>	
    <tr>
<td  colspan="1" style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; ">
    Variant:
</td>
<td colspan="5" rowspan="1" style="vertical-align:  middle;text-align: center; height: 50px; ">
<input id="variant"  type="text" name="variant" value="<?echo $_POST['variant']?>" size="3" onchange="change_autofocus()" />
</td>
</tr>
	<tr>
			 <td colspan="1" style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; ">
			Condition: 
        </td>
	        <td colspan="5" rowspan="1" style="vertical-align:  middle; text-align: center;height: 50px; ">
			<table style="vertical-align:  middle;">
		  <tr>
			<td><?/*echo strpos($_POST['etat'],"9,");*/?>
            <input id="etat_1" class="element radio" type="radio" name="etat" value="9," <?if (strpos($_POST['etat'],"9,")==0){?>checked<?}?> onchange="change_autofocus()"/> 
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
			<td><input id="etat_2" class="element radio" type="radio" name="etat" value="99,NO" <?if (strpos($_POST['etat'],"99,NO")!== false){?>checked<?}?> onchange="change_autofocus()"/> 
				<label class="choice" for="etat_2">Grade A</label> <br /></td>
			<td><input id="etat_5" class="element radio" type="radio" name="etat" value="8,LN" <?if (strpos($_POST['etat'],"8,LN")!== false){?>checked<?}?> onchange="change_autofocus()"/> 
				<label class="choice" for="etat_4">Grade A (Film voir JO pour utiliser) - Like New</label> </td>
		  </tr>
		  <tr>
			<td><input id="etat_4" class="element radio" type="radio" name="etat" value="22,R" <?if (strpos($_POST['etat'],"22,R")!== false){?>checked<?}?> onchange="change_autofocus()"/> 
				<label class="choice" for="etat_3">Grade B - Minor Marks (Excellent Condtion)</label> <br /> </td>
			<td><input id="etat_6" class="element radio" type="radio" name="etat" value="7,VG" <?if (strpos($_POST['etat'],"7,VG")!== false){?>checked<?}?> onchange="change_autofocus()"/> 
				<label class="choice" for="etat_5">Grade C - Some Marks but (Very Good Condition)</label><br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_7" class="element radio" type="radio" name="etat" value="6,G" <?if (strpos($_POST['etat'],"6,G")!== false){?>checked<?}?> onchange="change_autofocus()"/> 
				<label class="choice" for="etat_6">Grade D - Many Marks but (Good Condition)</label> </td>
			<td><input id="etat_8" class="element radio" type="radio" name="etat" value="5,P" <?if (strpos($_POST['etat'],"5,P")!== false){?>checked<?}?> onchange="change_autofocus()"/> 
				<label class="choice" for="etat_7">Grade E - Real Used item with TOO much Marks (Poor Condition)</label><br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_9" class="element radio" type="radio" name="etat" value="1,FP" <?if (strpos($_POST['etat'],"1,FP")!== false){?>checked<?}?> onchange="change_autofocus()"/> 
				<label class="choice" for="etat_8">Defectueux pour piece ou a reparer</label> </td>
			<td><input id="etat_3" class="element radio" type="radio" name="etat" value="2,SR" <?if (strpos($_POST['etat'],"2,SR")!== false){?>checked<?}?> onchange="change_autofocus()"/> 
				<label class="choice" for="etat_3">Manufacturer Refurbished</label></td>
		  </tr>
		</table>
				</td>
	</tr>
  </table>		

		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />
</form>



<script>
function change_autofocus(){
	var b = document.getElementById("upc");
	b.setAttribute("autofocus", "");
	b.focus();
}
</script>
</body>
</html>
<? // on ferme la connexion à mysql /* z */
mysqli_close($db);

// Flush the output buffer
ob_end_flush();
?>