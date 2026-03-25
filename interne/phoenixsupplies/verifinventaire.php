<? 
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
$j=0;

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="a8c6fe">
<form id="form_67341" class="appnitro" action="verifinventaire.php" method="post">
<div class="form_description">
<h1>Insertion Ebay listing</h1>
USA <input type="checkbox" name="usa" value="1"/>
<h3><label class="description" for="categorie">Ebay listing UPDATE:</label></h3>

<textarea id="ebayinput" name="ebayinput" rows="5" cols="50"></textarea> <br>

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>

</form>
<?if(isset($_POST['ebayinput']))
{
		$_POST['ebayinput']=str_replace(array("\&quot"),"",$_POST['ebayinput']);
		$ebayinputnametab=explode("\n", $_POST['ebayinput']);


//exit;
?>
<table border="1">
	<tr>
	<th bgcolor="#1a1d5b">
	</th>
	<th bgcolor="#1a1d5b">
	SKU
	</th>
	<th bgcolor="#1a1d5b">
	Titre
	</th>

	<th bgcolor="#1a1d5b">
	Location
	</th>
	<th bgcolor="#1a1d5b">
	Quantite
	</th>
	</tr>

<?
		$sql = 'select * from oc_product,oc_product_description where oc_product.product_id=oc_product_description.product_id and quantity>0 and (oc_product.product_id=0'; 

		foreach($ebayinputnametab as $ebayinputname) 
		{	
			
			$ebayinputnameline=explode("\t", $ebayinputname);
			
			$test=strlen($ebayinputnameline[0]);
			if ($test==4){
			$sql.=' or oc_product.product_id='.$ebayinputnameline[0];
			//echo $sql."<br>";   
			}
			$j++;
		}
		$sql=$sql.') order by location asc';
		//echo $sql."<br>";
				$req = mysqli_query($db,$sql);
				while ($data = mysqli_fetch_assoc($req)){
				 
			$test=strlen($data['product_id']);
			if ($test==4){
			//echo "allo";
				if ($bgcolor=="ffffff"){
					$bgcolor="c0c0c0";
				}else{
					$bgcolor="ffffff";
				}
?>
					<tr>
					<td bgcolor="<?echo $bgcolor;?>">
					<img height="50" src="http://www.phoenixsupplies.ca/image/<?echo $data['image'];?>"/>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
						<?if ($data['sku']!=""){?>
							<svg class="barcode"
							jsbarcode-value="<?echo $data['sku'];?>"
							jsbarcode-textmargin="0"
							jsbarcode-height="24"
							jsbarcode-fontoptions="bold"
							jsbarcode-fontsize="12">
							</svg>
						<script>
					
						JsBarcode(".barcode").init();
						</script>
						<?}?>

					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $data['name'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $data['location'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $data['quantity'];?>
					</td>
					</tr>
		<?}
		}?>
</table>
		

<?}?>
</body>
</html>
<? echo 'FINI'; // on ferme la connexion � mysql 
mysqli_close($db); ?>