<? 
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
$j=0;

//if($_POST[vendu])
 
//print_r ($_POST[vendu]);
		foreach($_POST[vendu] as $vendu) 
			{	
				$itemvendu=explode(",", $vendu);
				$sql2 = 'UPDATE `oc_product` SET quantity=quantity-'.$itemvendu[1].' where product_id='.$itemvendu[0];
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				
			}
				$sql2 = 'UPDATE `oc_product` SET location="" where quantity=0';
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>

<script type="text/javascript">
    function selectAll() {
        var items = document.getElementsByName('vendu[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAll() {
        var items = document.getElementsByName('vendu[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }			
</script>
<link href="stylesheet.css" rel="stylesheet">



</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="updateorder.php" method="post">
<div class="form_description">
<h1>Bon de Commande</h1>
<?if(!isset($_POST['ebayinput'])){?>
<h3><label class="description" for="categorie">Ebay ORDER:</label></h3>

<textarea id="ebayinput" name="ebayinput" rows="5" cols="50" ></textarea> <br>


<?}?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>

<?if(isset($_POST['ebayinput']))
{
		$_POST['ebayinput']=str_replace(array("\&quot"),"",$_POST['ebayinput']);
		$ebayinputnametab=explode("\n", $_POST['ebayinput']);
		$j=0;
		

		foreach($ebayinputnametab as $ebayinputname) 
		{	
			
			$ebayinputnameline=explode("\t", $ebayinputname);
			
			$test=strlen($ebayinputnameline[31]);

			$sql = 'select * from oc_product,oc_product_description where oc_product.product_id=oc_product_description.product_id and (oc_product.product_id='.$ebayinputnameline[31].')'; 
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			
			if($ebayinputnameline[31]!="Custom Label")
			{
				$ebayoutputnametab[$j]['location']=$data['location'];
				$ebayoutputnametab[$j]['name']=$ebayinputnameline[14];
				$ebayoutputnametab[$j]['orderid']=$ebayinputnameline[0]; 
				
				$ebayoutputnametab[$j]['product_id']=$ebayinputnameline[31]; 
				$ebayoutputnametab[$j]['customername']=$ebayinputnameline[2]; 
				
				$ebayoutputnametab[$j]['sku']=$data['sku'];
				$Weight=$data['weight']*$ebayinputnameline[15];
				$WeightTot=array(); 
				$Weight=floatval($Weight);
				$WeightTot=explode('.', $Weight);
				$WeightOZ=intval(($Weight-$WeightTot[0])*16);
				$ebayoutputnametab[$j]['poids']=$WeightTot[0]." lb ".$WeightOZ." oz";
				$ebayoutputnametab[$j]['dimension']=number_format($data['length'], 1, '.', '')."x".number_format($data['width'], 1, '.', '')."x".number_format($data['height'], 1, '.', '');
				$ebayoutputnametab[$j]['quantitevendu']=$ebayinputnameline[15];
				$ebayoutputnametab[$j]['quantiterestant']=$data['quantity'];
				if($data['image']!="") 	$ebayoutputnametab[$j]['image']='<img height="50" src="http://www.phoenixsupplies.ca/image/'.$data['image'].'"/>';
				//print_r($ebayoutputnametab)."<br>"; 
				$j++;
			}
		}

		//echo $sql."<br>";
				
//exit;
?>
<script language="JavaScript">

</script>
<table border="1" width="100%">
	<tr>
	<th bgcolor="#1a1d5b">
	
	<input type="button" onclick='selectAll()' value="Select All"/><br>
	<input type="button" onclick='UnSelectAll()' value="Unselect All"/>
	</th>
	<th bgcolor="#1a1d5b">
	SKU
	</th>
	<th bgcolor="#1a1d5b">
	Order Nu
	</th>
		<th bgcolor="#1a1d5b">
	Customer Name
	</th>
		<th bgcolor="#1a1d5b">
	Titre
	</th>
	<th bgcolor="#1a1d5b">
	Poids
	</th>
	<th bgcolor="#1a1d5b">
	Dimension
	</th>
	<th bgcolor="#1a1d5b">
	Location
	</th>
	<th bgcolor="#1a1d5b">
	Qte Vendu
	</th>
	<th bgcolor="#1a1d5b">
	Qte Restant
	</th>
	</tr>

<?
		sort($ebayoutputnametab);
			foreach($ebayoutputnametab as $ebayoutputname) 
			{	 
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
					<input type="checkbox" name="vendu[]" value="<?echo $ebayoutputname['product_id'].','.$ebayoutputname['quantitevendu'];?>"/>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">

						<?echo $ebayoutputname['sku'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['orderid'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['customername'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['name'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['poids'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['dimension'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['location'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['quantitevendu'];?>
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
		

<?}?>
		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />


</form>
</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>