<? 
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte � MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s�lectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
$j=0;

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<style> 
input[type=text] {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}
textarea  {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}

select {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}
select:focus {
    border: 3px solid #555;
}

input[type=text]:focus {
    border: 3px solid #555;
}
textarea:focus {
    border: 3px solid #555;
}
</style>
</head>
<body bgcolor="a8c6fe">
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
				$req = mysql_query($sql);
				while ($data = mysql_fetch_assoc($req)){
				 
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
mysql_close(); ?>