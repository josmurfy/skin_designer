<? 
//echo (string)$_POST['upc'] ;
if(isset($_GET['anc_loc'])){
	$_POST['anc_loc']=$_GET['anc_loc'];
}
$upc=(string)$_POST['upc'] ;
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
//migrateinventoryebay(293548139731);
//getebayproduct($connectionapi,372807463310,3527);
// on sélectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
//recherche des produit lister
$DateNOW = date('Y-m-d');



//print("<pre>".print_r ($_POST,true )."</pre>");
$createlabel=$_POST['createlabel'];
$location=strtoupper($_POST['location']);
if(isset($_POST['product_select']) && $_POST['product_select']!="")
 {
  
		
               			
				$sql2 = 'UPDATE `oc_product_defect`SET quantity=quantity+'.($_POST['quantity']).' WHERE product_defect_id ='.$_POST['product_select'];
        	//	echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);  
				
			
	unset ($_POST);		

}elseif((isset($_POST['need_repair']) && $_POST['need_repair']!="" && $_POST['location']!="") || (isset($_POST['need_part']) && $_POST['need_part']!="" && $_POST['location']!="")){
    $sql2 = " INSERT INTO oc_product_defect (upc, name, part, location, quantity, need_repair, need_part)
VALUES ('".$_POST['upc']."', '".addslashes($_POST['name'])."', 0".$_POST['part'].", '".strtoupper($_POST['location'])."', ".$_POST['quantity'].", '".$_POST['need_repair']."', '".$_POST['need_part']."')";
$req2 = mysqli_query($db,$sql2);  
//echo $sql2.'<br><br>';
    unset ($_POST);	
}
if($createlabel=="yes"){
    echo '<script>window.open("createlabeltablette.php?tablette='.$location.'","etiquette")</script>';
}

if(isset($_POST['upc']) && $_POST['upc']!=""){
	$sql = 'SELECT *
		FROM `oc_product_defect` 
		where upc="'.$_POST['upc'].'"'; //LIMIT 200 and P.ebay_id=292612778604';  $sql.'<br><br>';/*P.username = "marissa"*/
	//	echo $sql;
		$req = mysqli_query($db,$sql);
    $sql2 = 'SELECT `name`, `image`
		FROM  `oc_product` AS P
        LEFT JOIN `oc_product_description` AS PD ON (P.product_id=PD.product_id)
		where upc="'.$_POST['upc'].'"'; //LIMIT 200 and P.ebay_id=292612778604';  $sql.'<br><br>';/*P.username = "marissa"*/
		//echo $sql2;
	$req2 = mysqli_query($db,$sql2);
    $data2 = mysqli_fetch_assoc($req2);
    
    if(!isset($_POST['name']) || $_POST['name']==""){
        $_POST['name']=$data2['name'];
    }

    if($_POST['name']==""){
        $_POST['upctemp']=get_from_upctemp($_POST['upc']);
        $result_upctemp=json_decode($_POST['upctemp'],true);
        $_POST['name']=($result_upctemp['items'][0]['title']!="")?$result_upctemp['items'][0]['title']:"PAS TROUVE";
    }
}elseif(isset($_POST['name']) && $_POST['name']!=""){
    $sql = 'SELECT `name`, `image`, upc
		FROM  `oc_product` AS P
        LEFT JOIN `oc_product_description` AS PD ON (P.product_id=PD.product_id)
		where `name` like "%'.$_POST['name'].'%" OR  `model` like "%'.$_POST['name'].'%" OR  `upc` like "%'.$_POST['name'].'%"'; //LIMIT 200 and P.ebay_id=292612778604';  $sql.'<br><br>';/*P.username = "marissa"*/
		//echo $sql2;
	$req = mysqli_query($db,$sql);
    $data = mysqli_fetch_assoc($req);
 
}

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title>

<title>receipt</title>



<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
<link href="stylesheet.css" rel="stylesheet">
<script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>





</head>
<body bgcolor="ffffff">
	
<form id="form_67341" class="appnitro" action="ADMIN_inventaire_costume_manquant.php" method="post" enctype="multipart/form-data">
<table border="1" width="100%">

<tr>
    <td>
   
PRODUCT UPC______
</td><td><input id="upc"  type="text" name="upc" value="<?echo $_POST['upc'];?>" size="30" autofocus  />
</td>
</tr>
<tr>
    <td>
   
IMAGE
</td><td><?if(isset($data2['image'])){
    echo '<img height="80" src="'.$GLOBALS['WEBSITE'].'image/'.$data2['image'].'"/>';}?>
</td>
</tr>
<tr>
    <td>
NAME 
</td><td><input id="name"  type="text" name="name" value="<?echo $_POST['name'];?>" size="100" />
</td>
</tr>
<tr>
    <td>
QTY 
</td><td><input id="quantity"  type="text" name="quantity" value="1" size="3" />
</td>
</tr>

<tr>
    <td>
FOR PARTS 
</td><td>
<input id="part" class="element radio" type="radio" name="part" value="1"/> 
				<label class="choice" for="part">YES</label>
                <input id="part" class="element radio" type="radio" name="part" value="0"/> 
				<label class="choice" for="part">NO</label>
              
</td>
</tr>
<tr>
    <td>
NEED REPAIR 
</td><td><input id="need_repair"  type="text" name="need_repair" value="<?echo $_POST['need_repair'];?>" size="256" />
</td>
</tr>
<tr>
    <td>
NEED PARTS 
</td><td><input id="need_part"  type="text" name="need_part" value="<?echo $_POST['need_part'];?>" size="256" />
</td>
</tr>
<tr>
    <td>
LOCATION 
</td><td><input id="location"  type="text" name="location" value="<?echo $_POST['location'];?>" size="30" />
</td>
</tr>
<tr>
    <td>
LABEL TABLETTE <input type="checkbox" id="createlabel" name="createlabel" value="yes"/>

</td>
</tr>
</table>
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
		

	</th>

	<th bgcolor="ff6251">
UPC
	</th>
		<th bgcolor="ff6251">
	NAME
	</th>
    <th bgcolor="ff6251">PART?
	
	</th>
	<th bgcolor="ff6251">LOC
	
	</th>
		<th bgcolor="ff6251">
QTY
	
	</th>
    <th bgcolor="ff6251">REPAIR
	
	</th>
		<th bgcolor="ff6251">
PARTS
	
	</th>
	
	</tr>
	<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
<?
	
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
// merge quantity
$itemcount=0;
	while($data = mysqli_fetch_assoc($req)){
	//	//print("<pre>".print_r ($data,true )."</pre>");
	?>
						<tr id="tr<?echo $data['upc'];?>" >
						<td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
                        <input id="part" class="element radio" type="radio" name="product_select" value="<?echo $data['product_defect_id'];?>"/> 
							


						</td>
						<td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
					<table align="center">
                        <tr><td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
                       								

		
</td></tr>
<tr><td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">


<div  style="text-align: center;font-size: 15px; "><strong><?echo (string)$data['upc'] ;?></strong></div>
</td></tr>
</table>
							
						</td>


						<td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
						
			
						<a href="<?echo $GLOBALS['WEBSITE'];?>/interne/pretlister.php?product_id=<?echo $data['product_id'];?>&action=listing" target='listing' ><?echo $data['name'];?></a>
						</td>

						<td >
						<?if( $data['part']==1){
                                echo "YES";
                            }else{
                                echo "NO";
                            }?>
						<input id="qtytot<?echo $data['product_id'];?>"  type="hidden" name="qtytot<?echo $data['product_id'];?>"  value="<?echo $data['part'];?>" />
						</td>
                        <td >
						
						<?echo $data['location'];?>

						</td>
						<td style="vertical-align: middle; background-color: <?echo $bgcolor;?>;  text-align: center;">
						<?echo $data['quantity'];?>
						</td>
                        <td >
						
						<?echo $data['need_repair'];?>

						</td>
						
						<td class="text-center" id="entrepot_valtab<?php echo  $data['upc']; ?>" >					
									
                        <?echo $data['need_part'];?>
									
								
									

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
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />
<?if ($_GET['imp']==""){?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interneusa.php" >Retour au MENU</a></h1>
		
<?}?>

</form>
<script>
	$(document).on('click','.pentrepot_val',function() {
    rel=$(this).attr('rel');
    rel1=$(this).attr('rel1');
  	 html ='<input type="text" id="entrepot_val'+rel+'" name="entrepot_val" class="form-control entrepot_val'+rel+'" value="" />';
    $('#entrepot_valedit'+rel).html(html);
	document.getElementById('entrepot_val'+rel).autofocus = true
  });
     function checked_sku(upc) {
        item = document.getElementById('upc').value;
      
        try {
        // code that we will 'try' to run
            document.getElementById(item).checked=true;
            document.getElementById('upc').value="";
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
            document.getElementById('upc').value="";
        }
       
    }	
	function editEntrepot_val(upc,entrepot_val) {
		qteactuel=parseInt(document.getElementById('quantity_hid' + upc).value);
		if(entrepot_val==qteactuel){
            	document.getElementById('tr' + upc).style.backgroundColor='green';
			}else{
				document.getElementById('tr' + upc).style.backgroundColor='yellow';
			}
			
			document.getElementById('quantity_val' + upc).value=entrepot_val;
 	
			//document.getElementById('entrepot_valtab'+upc).style.backgroundColor='green';
			//document.getElementById('entrepot_valtab'+upc).style.color='white';
      
	}
</script>
</body>
</html>
<?  // on ferme la connexion à 

mysqli_close($db); ?>