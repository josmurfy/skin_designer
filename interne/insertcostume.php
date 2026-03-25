<?

//print_r($_POST['accesoires']);



// on se connecte à MySQL 

include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");

// on s&eacute;lectionne la base 

//mysqli_select_db('n7f9655_storeliquidation',$db); 

// on cr&eacute;e la requête SQL verifier les ordres 

// savoir ledernier id 





//echo $espace;

if (isset($_POST['sourcecode'] ) && (string)$_POST['sourcecode'] !="" 

&& $_POST['category_id']!="" && $_POST['etat']!=""){

	//echo (strlen($_POST['upc']));
	$pos = strpos($_POST['upc'], 'U');

	// Note our use of ===.  Simply == would not work as expected
	// because the position of 'a' was the 0th (first) character.
	
		$findupc= explode('"upc":"',$_POST['sourcecode']);
        $findupc= explode('"',$findupc[1]);
        $_POST['upc']=$findupc[0];

       

       
      //  echo $_POST['upc'];
   // 
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

            echo $_POST['sku']; 

            echo "<br>".$_POST['condition_id'];	

			//$products=get_products(substr((string)$_POST['upc'] ,0,12));

			$products=get_product((string)$_POST['sku']);

			//echo (strlen($_POST['upc']));

		

			 if(count($products)==0){
                $findretailprice=explode('"wasPrice":{"price":',$_POST['sourcecode']);
                    $findretailprice= explode(',',$findretailprice[1]);
                    $_POST['priceretail']=$findretailprice[0];
                    $findcurrencyUnit=explode('"currencyUnit":"',$_POST['sourcecode']);
                    $findcurrencyUnit= explode('"',$findcurrencyUnit[1]);
                    $_POST['currencyUnit']=$findcurrencyUnit[0];
                    

                    if(strtolower($_POST['currencyUnit'])=="usd"){
                        $_POST['currency']=1;
                    }else{
                        $_POST['currency']=1.35;
                    }
                    $_POST['price_with_shipping']=($_POST['priceretail']/$_POST['currency'])+9005;

                    $findnameen=explode('"productName":"',$_POST['sourcecode']);
                    $findnameen= explode('"',$findnameen[1]);
                    $_POST['nameen']=$findnameen[0];
                    $findcondition_suppen=explode('"value":"Includes:',$_POST['sourcecode']);
                    $findcondition_suppen= explode('"',$findcondition_suppen[1]);
                    $_POST['condition_suppen']=$findcondition_suppen[0];
                    $_POST['condition_suppen']=str_replace(",","@",$_POST['condition_suppen']);

                    $findsize=explode('"Size","value":"',$_POST['sourcecode']);
                    $findsize= explode('"',$findsize[1]);
                    $_POST['size']=$findsize[0];
                    $_POST['nameen'].=" ".$_POST['size'];
                    //$finddescription_suppen=explode('"value":"Includes:',$_POST['sourcecode']);
                    //$finddescription_suppen= explode('"',$finddescription_suppen[1]);
                   //$_POST['condition_suppen']=$finddescription_suppen[0];
                   // $_POST['description_suppen'];
				$_POST['weight']=0;

               $_POST['weight2']=0;

                $_POST['manufacturer_id']=0;

                $_POST['length']=7;

                $_POST['width']=4;

                $_POST['height']=2;
                $findmodel=explode('Part Number","value":"',$_POST['sourcecode']);
                $findmodel= explode('"',$findmodel[1]);
                $_POST['model']=$findmodel[0];

                $findcoloren=explode('"Color","value":"',$_POST['sourcecode']);
                $findcoloren= explode('"',$findcoloren[1]);
                $_POST['coloren']=$findcoloren[0];
               

                $_POST['price']=$_POST['priceretail'];

                $_POST['marketplace_item_id']=0;

             //   $_POST['price_with_shipping']=9999;

                $_POST['quantity']=$_POST['nb'];
               // 
                $_POST['product_id']=insert_item_db($connectionapi,$_POST,$db);

                //print("<pre>".print_r ($_POST,true )."</pre>");

					 echo "<br>non existant";

                     $bgcolor="yellow";

					 //exit();
					 if($_POST['location']!=""){
						$sql2 = 'UPDATE `oc_product` SET `location`="'.$_POST['location'].'" where product_id='.$_POST['product_id'];
                        $req2 = mysqli_query($db,$sql2);
						echo $sql2.'<br><br>';

					}
                link_to_download($connectionapi,$_POST['product_id'],$_POST['sourcecode'],"sourcecodenew",$db);
                

			 }else{

                $_POST['product_id']=$products[0]['product_id'];

					  echo "<br>existant";

                      echo "<br>".$_POST['product_id'];

                      $bgcolor="green";

                      $sql2 = 'UPDATE `oc_product` SET unallocated_quantity=unallocated_quantity+'.$_POST['nb'].',ebay_last_check="2020-09-01" where product_id='.$_POST['product_id'];

                        //echo $sql2.'<br><br>';

                        $req2 = mysqli_query($db,$sql2);


                        $result=revise_ebay_product_inventaire($connectionapi,$products[0]['marketplace_item_id'],$_POST['product_id'],$products[0]['quantity']+1,$db,"oui");

					

					 // exit();

			 }

             if($_POST['condition_id']!=9 || ($_POST['condition_id']==9 && strlen($_POST['sku'])>12)){

				echo '<script>window.open("createbarcodetotal.php?all=no&type=both&qtemag=0&qtetot='.$_POST['nb'].'&product_id='.$_POST['product_id'].'&sku='.(string)$_POST['sku'].'","etiquette")</script>';

             }
             unset($_POST['sourcecode']);
	

}else{

    $_POST['category_id']=="" ;

    $_POST['etat']=="";

    if($_GET['action']=="add"){

        $bgcolor="red";

    }else{

        $bgcolor="ffffff";

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

<body bgcolor="<? echo $bgcolor;?>">





<form id="form_67341" class="appnitro" action="insertcostume.php?action=add" method="post">

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

		<h1>FAST Ajouter Halloween</h1><?if ($_POST['erreur_dimensions_poids']!="") echo '<h3><font color="red">'.$_POST['erreur_dimensions_poids'].'</font></h3>'; ?>

        </td>

      </tr>

      <tr>

			 <td colspan="1" style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; ">

			Type produit: 

        </td>

	        <td colspan="5" rowspan="1" style="vertical-align:  middle; text-align: center;height: 50px; ">

			<table style="vertical-align:  middle;">

            <tr>

                <td><input id="category_id_2" class="element radio" type="radio" name="category_id" value="52762" <?if ($_POST['category_id']== 52762){?>checked<?}?> onchange="change_autofocus()"/> 

                    <label class="choice" for="category_id_2">Costume Homme</label> <br /></td>
               
                    <td><input id="category_id_2" class="element radio" type="radio" name="category_id" value="53369" <?if ($_POST['category_id']== 53369){?>checked<?}?> onchange="change_autofocus()"/> 

<label class="choice" for="category_id_2">Costume Femme</label> <br /></td>
<td><input id="category_id_2" class="element radio" type="radio" name="category_id" value="90635" <?if ($_POST['category_id']== 90635){?>checked<?}?> onchange="change_autofocus()"/> 

<label class="choice" for="category_id_2">Costume Toddler</label> <br /></td>
<td><input id="category_id_2" class="element radio" type="radio" name="category_id" value="80913" <?if ($_POST['category_id']== 80913){?>checked<?}?> onchange="change_autofocus()"/> 

<label class="choice" for="category_id_2">Costume Boys</label> <br /></td>
<td><input id="category_id_2" class="element radio" type="radio" name="category_id" value="80914" <?if ($_POST['category_id']== 80914){?>checked<?}?> onchange="change_autofocus()"/> 

<label class="choice" for="category_id_2">Costume Girls</label> <br /></td>
<td><input id="category_id_2" class="element radio" type="radio" name="category_id" value="86207" <?if ($_POST['category_id']== 86207){?>checked<?}?> onchange="change_autofocus()"/> 

<label class="choice" for="category_id_2">Costume Unisex</label> <br /></td>

                <td><input id="category_id_5" class="element radio" type="radio" name="category_id" value="156812" <?if ($_POST['category_id']== 156812){?>checked<?}?> onchange="change_autofocus()"/> 

                    <label class="choice" for="category_id_4">Gonflabe Outdoor</label> </td>
                    <td><input id="category_id_5" class="element radio" type="radio" name="category_id" value="116724" <?if ($_POST['category_id']== 116724){?>checked<?}?> onchange="change_autofocus()"/> 

<label class="choice" for="category_id_4">Masks</label> </td>
<td><input id="category_id_5" class="element radio" type="radio" name="category_id" value="155350" <?if ($_POST['category_id']== 155350){?>checked<?}?> onchange="change_autofocus()"/> 

<label class="choice" for="category_id_4">Wigs</label> </td>
<td><input id="category_id_5" class="element radio" type="radio" name="category_id" value="82161" <?if ($_POST['category_id']== 82161){?>checked<?}?> onchange="change_autofocus()"/> 

<label class="choice" for="category_id_4">Other Acc</label> </td>

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

    Code Source:

    

</td>

<td colspan="5" rowspan="1" style="vertical-align:  middle;text-align: center; height: 50px; ">

<textarea name="sourcecode" rows="5" cols="5" placeholder="copiez le code source des images a télécharger" id="sourcecode" class="form-control"><?echo $_POST['sourcecode']?></textarea>

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

		

		<input type="hidden" name="etatorigin" value="<?echo $_GET['etat'];?>" />

		<input type="hidden" name="product_id" value="<?echo $_GET['product_id'];?>" />

		<input type="hidden" name="action" value="<?echo $_GET['action'];?>" />

	

		<input type="hidden" name="insertion" value="<?echo $_POST['insertion'];?>" />

		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />

</form>



<script>

function change_autofocus(){

	var b = document.getElementById("upc");

	//var c = document.getElementById("variant");

  //  document.getElementById("variant").value="";

	//c.removeAttribute("autofocus","");

	b.setAttribute("autofocus", "");

	b.focus();

}

</script>

</body>

</html>

<? // on ferme la connexion à mysql /* z */



mysqli_close($db); ?>