<? 
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
$j=0;

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="verifinventairenonsynchro.php" method="post">
<div class="form_description">
<h1>Verif inventaire non sync avec ebay</h1>

<h3><label class="description" for="categorie">:</label></h3>

<textarea id="ebayinput" name="ebayinput" rows="5" cols="50"></textarea> <br>

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" >Retour au MENU</a></h1>

</form>
<?if(isset($_POST['ebayinput']))
{
		$_POST['ebayinput']=str_replace(array("\&quot"),"",$_POST['ebayinput']);
		$ebayinputnametab=explode("\n", $_POST['ebayinput']);


//exit;
?>
<table border="1">
	<tr>
	<th bgcolor="ff6251">
	</th>
	<th bgcolor="ff6251">
	SKU
	</th>
	<th bgcolor="ff6251">
	Titre
	</th>

	<th bgcolor="ff6251">
	Location
	</th>
	<th bgcolor="ff6251">
	Quantite
	</th>
	</tr>

<?
		$sql = 'select * from oc_product,oc_product_description where oc_product.product_id=oc_product_description.product_id and quantity<5 and (oc_product.product_id=0'; 

		foreach($ebayinputnametab as $ebayinputname) 
		{	
			
			$ebayinputnameline=explode("\t", $ebayinputname);
			
			$test=strlen($ebayinputnameline[1]);
			if ($test==4){
			$sql.=' or oc_product.product_id='.$ebayinputnameline[1];
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
					<img height="50" src="<?echo $GLOBALS['WEBSITE'];?>image/<?echo $data['image'];?>"/>
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