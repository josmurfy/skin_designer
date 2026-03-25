<? 
//print_r($_POST['accesoires']);
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
//$connect = mysqli_connect('localhost', 'n7f9655_store', 'Caro1FX2Skimo', 'n7f9655_storeliquidation');
// on s�lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
$j=0;

if($_POST['verifer']){
 
//print_r ($_POST[verifer]);
		foreach($_POST['verifer'] as $verifer)  
			{	
				
				$sql2 = 'UPDATE `oc_product` SET prixverif=1,ebay_last_check="2020-09-01" where product_id='.$verifer;
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				
			}

				$sql2 = 'UPDATE `oc_product` SET `location`="",ebay_last_check="2020-09-01" where quantity=0';
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>

<script type="text/javascript">
    function selectAll() {
        var items = document.getElementsByName('verifer[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAll() {
        var items = document.getElementsByName('verifer[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }
    function selectAllex() {
        var items = document.getElementsByName('excluremagasin[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAllex() {
        var items = document.getElementsByName('excluremagasin[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }		
</script>
<link href="stylesheet.css" rel="stylesheet">



</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="rapportinventaire.php" method="post">
<div class="form_description">


<?
		$nbitem=0;
		global $ebayoutputnametab;
		unset($ebayoutputnametab);

			//$sql = 'SELECT * FROM `oc_product`,`oc_product_description` WHERE `oc_product`.product_id=`oc_product_description`.product_id and `location` NOT LIKE "%magasin%" and `location` NOT LIKE "" AND `quantity` > 0 and excluremagasin=1 group by `oc_product_description`.product_id ORDER BY `oc_product`.`location` DESC ';  
			
			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` WHERE `oc_product`.product_id=`oc_product_description`.product_id and `location` like "%magasin%" and prixverif!=1 group by `oc_product`.upc order by oc_product.price DESC limit 55 ';  
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$j=0;
			while ($data = mysqli_fetch_assoc($req)){
					$sql3 = 'SELECT * FROM `oc_product_special` WHERE `oc_product_special`.product_id='.$data['product_id'];  
					//echo $sql."<br>"; 
					$req3 = mysqli_query($db,$sql3);
					$data3 = mysqli_fetch_assoc($req3);

				$ebayoutputnametab[$j]['location']=$data['location'];
				$ebayoutputnametab[$j]['name']=$data['name'];
				$ebayoutputnametab[$j]['product_id']=$data['product_id']; 
				$ebayoutputnametab[$j]['price']=$data3['price']*1.34;				
				$ebayoutputnametab[$j]['priceretail']=$data['price']*1.34;
				
				$ebayoutputnametab[$j]['sku']=$data['sku'];
				$ebayoutputnametab[$j]['upc']=(string)$data['upc'];
				$ebayoutputnametab[$j]['poids']=$data['weight'];
				$ebayoutputnametab[$j]['dimension']=number_format($data['length'], 1, '.', '')."x".number_format($data['width'], 1, '.', '')."x".number_format($data['height'], 1, '.', '');
				$ebayoutputnametab[$j]['quantiterestant']=$data['quantity'];
				if($data['image']!="") 	$ebayoutputnametab[$j]['image']='<img height="50" src="'.$GLOBALS['WEBSITE'].'image/'.$data['image'].'"/>';
				//print_r($ebayoutputnametab)."<br>"; 
				$j++;
			}


		//echo $sql."<br>";
				
//exit;
?>


<?
		sort($ebayoutputnametab);
			foreach($ebayoutputnametab as $ebayoutputname) 
			{	 
			if($nbitem==8)$nbitem=0;
			if($nbitem==0){
				echo '</table><h1>RAPPORT Verification PRIX pour magasin</h1><script language="JavaScript">

</script>
<table border="1" width="100%">
	<tr>
	<th bgcolor="ff6251">
	
	<input type="button" onclick="selectAll()" value="Verifier"/><br>
	<input type="button" onclick="UnSelectAll()" value="Verifier Non"/>
	</th>

		<th bgcolor="ff6251">
UPC
	</th>
		<th bgcolor="ff6251">
	SKU
	</th>

		<th bgcolor="ff6251">
	Titre
	</th>
	<th bgcolor="ff6251">
	Prix RETAIL
	</th>
	<th bgcolor="ff6251">
	Prix
	</th>
	<th bgcolor="ff6251">
	Location
	</th>
	<th bgcolor="ff6251">
	Qte Restant
	</th>
	</tr>';
			}
			$nbitem++;
			$k++;
			//echo "allo";
				if ($bgcolor=="ffffff"){
					$bgcolor="c0c0c0";
				}else{
					$bgcolor="ffffff";
				}
?>
					<tr>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['image'];?>
					Verifier:<input type="checkbox" name="verifer[]" value="<?echo $ebayoutputname['product_id'];?>"/>

					</td>
					<td bgcolor="<?echo $bgcolor;?>">

											<svg class="barcode"
				
					jsbarcode-value="<?echo $ebayoutputname['upc'];?>"
					jsbarcode-textmargin="0"
					jsbarcode-height="20"
					jsbarcode-width="2"
					jsbarcode-fontoptions="bold"
					jsbarcode-fontsize="12">	
				</svg>
				<script>
				JsBarcode(".barcode").init();
				</script>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">

						<?echo $ebayoutputname['sku'];?>
					</td>

					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['name'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['priceretail'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['price'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['location'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['quantiterestant'];?>
					</td>
					</tr>
		<?
		
	//$k++;
	//echo $k;
	}
		?>
</table>
		


		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />

		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
</form>
</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>