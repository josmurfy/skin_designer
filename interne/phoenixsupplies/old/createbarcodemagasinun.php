<?
//echo $_POST['sku'];
//$sku=$_POST['sku'];
// on se connecte � MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s�lectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 

if ($_POST['sku']=="" || $_GET['sku']==""){
	$new=0;
}
//echo $sku;
//echo $_GET['sku'];

		$sql = 'SELECT oc_product.product_id,sku,name,price FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and oc_product.sku = "'.$_GET['sku'].$_POST['sku'].'" group by sku order by sku';
//echo $sql;
		// on envoie la requ�te
		$req = mysql_query($sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 

		//echo $data['product_id'];



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>receipt</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
  <link rel="stylesheet" href="https://www.phoenixsupplies.ca/admin/staff/paper.css">
      <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
  <style>
    @page { size: 76mm 25mm margin: 0mm 0mm 0mm 0mm;} /* output size */
    body.receipt .sheet { width: 76mm; height: 25mm } /* sheet size */
    @media print { body.receipt { width: 76mm } } /* fix for Chrome */
  </style>
</head>

<body class="receipt">
<?
		while($data = mysql_fetch_assoc($req)){
			$sql2 = 'SELECT * FROM `oc_product_special` where product_id = '.$data['product_id'];
	//echo $sql2;
			// on envoie la requ�te
			$req2 = mysql_query($sql2);
			// on fait une boucle qui va faire un tour pour chaque enregistrement
			$data2 = mysql_fetch_assoc($req2);
			$new=1;

?>
  <section class="sheet padding-0mm">
	<table cellpadding="0" cellspacing="0">  
			<tr>
			<tr>
			<td align="center"  valign="top" width="50%">
				
				<svg class="barcode"
				
					jsbarcode-value="<?echo $data['sku'];?>"
					jsbarcode-textmargin="0"
					jsbarcode-height="20"
					jsbarcode-width="1"
					jsbarcode-fontoptions="bold"
					jsbarcode-fontsize="12">	
				</svg>
				<script>
				JsBarcode(".barcode").init();
				</script>
				<?echo "<font size=1><br>".$data['name']."</>";?>
			</td>

			<td align="right"  valign="top" >
			
				<?echo "<font size=2>PDSF:</> <br><font size=4 >$<STRIKE>".number_format($data['price']*1.34*1.14975,2)."</STRIKE></>";?>
				<?echo "<br><font size=2><strong>Votre Prix:</> <br><font size=5 >$".number_format($data2['price']*1.34*1.14975,2)."</strong></><font size=4 ></> ";?>
			</td>
			</tr>
	</table>
  </section>
		<?}?>

<script type="text/javascript">
	window.//print();
	window.open("https://www.phoenixsupplies.ca/admin/staff/etiquetteprix.php", "_self");
</script>
</body>
</html>
<? // on ferme la connexion � mysql mysql_close(); 
mysql_close();?>