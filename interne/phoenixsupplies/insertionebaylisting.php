<? 
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 

if(isset($_POST['ebayinput']))
{
		$_POST['ebayinput']=str_replace(array("\&quot"),"",$_POST['ebayinput']);
		$ebayinputnametab=explode("\n", $_POST['ebayinput']);

		foreach($ebayinputnametab as $ebayinputname) 
		{
			$ebayinputnameline=explode(",", $ebayinputname);
			if ($ebayinputnameline[7]!="" || $ebayinputnameline[34]!=""){
				

				$sql = 'INSERT INTO `oc_ebay_listing` (variant,status,ebay_item_id,product_id)';
				$sql = $sql.'values (9,1,"'.$ebayinputnameline[7].'","'.$ebayinputnameline[34].'")';
						$i=0;
				//echo $ebayinputnameline[0]."<br>";		

		echo $sql."<br>";
				$req = mysqli_query($db,$sql);
				//$data = mysqli_fetch_assoc($req);
			}
//exit;
		}

}?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="a8c6fe">
<form id="form_67341" class="appnitro" action="insertionebaylisting.php" method="post">
<div class="form_description">
<h1>Insertion Ebay listing</h1>

<h3><label class="description" for="categorie">Ebay listing :</label></h3>

<textarea id="ebayinput" name="ebayinput" rows="5" cols="50"></textarea> <br>

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>

</form>
<p id="footer">�
</body>
</html>
<? echo 'FINI'; // on ferme la connexion � mysql 
mysqli_close($db); ?>