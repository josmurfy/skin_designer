<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr&eacute;e la requête SQL verifier les ordres 
// savoir ledernier id 
if ($_GET['insertion'] != ""){
	$_POST['insertion']=$_GET['insertion'];
	$_POST['etape']=0;
	}
			$sql3 = 'SELECT * FROM `oc_product` where product_id = "'.$_POST['product_id'].'"'; 
	//echo $sql;
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);


if ($_POST['carac']!=""){
	//echo "allo";
	if($_POST['insertion']==""){
		$test=cloner_item ("OK", $typeetat,$data3['condition_id'],$data3['sku'].$_POST['carac'],$_POST['product_id'],$db);
		$sku=$data3['sku'].$_POST['carac'];
		//echo $sku."-".$_POST['carac']."-".$_POST['product_id'];
		header("location: listing.php?sku=".$sku); 
	}else{
		header("location: insertionitemusa.php?upc=".$_POST['upc']."&condition_insert=".$_POST['carac']);
	
	}
}?>


<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff">
<h1>Item a cloner </h1>
<h3>Sku a cloner: (<?echo $_GET['sku'];?>)</h3>
<form id="form_67341" class="appnitro" action="clonerusa.php?action=<?echo $_GET['action'] ?>" method="post">

		
		
		<td>Max 5 caracteres : <input id="carac"  type="text" name="carac" value="" size="5" /></td>
		<input type="hidden" name="product_id" value="<?echo $_GET['product_id'];?>" />
		<input type="hidden" name="action" value="<?echo $_GET['action'];?>" />
		<input type="hidden" name="insertion" value="<?echo $_POST['insertion'];?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
</form>

 <h1><a href="interneusa.php" >Retour au MENU</a></h1>

</body>
</html>
<? // on ferme la connexion à mysql /* z */

mysqli_close($db); ?>