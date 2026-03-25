<? 
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte � MySQL 
//$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s�lectionne la base 
//mysql_select_db('phoenkv5_store',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
$j=0;

//if($_POST[vendu])

//print_r ($_POST[vendu]);
		foreach($_POST['vendu'] as $vendu) 
			{	
				$itemvendu=explode(",", $vendu);
				$sql2 = 'UPDATE `oc_product` SET quantity=quantity-'.$itemvendu[1].',ebay_last_check="2020-09-01" where product_id='.$itemvendu[0];
				//echo $sql2.'<br><br>';
				$req2 = mysql_query($sql2);
			}

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
<form id="form_67341" class="appnitro" action="updateorder3.php" method="post">
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
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			
			if($ebayinputnameline[31]!="Custom Label" && strlen($ebayinputnameline[31])==4)
			{
				$ebayoutputnametab[$j]['customername']=$ebayinputnameline[2]; 
				$ebayoutputnametab[$j]['orderid']=$ebayinputnameline[0];
				$ebayoutputnametab[$j]['sku']=$data['sku'];
				$ebayoutputnametab[$j]['location']=$data['location'];
				$ebayoutputnametab[$j]['product_id']=$ebayinputnameline[31]; 
				
				$ebayoutputnametab[$j]['name']=$ebayinputnameline[14];
				
				$ebayoutputnametab[$j]['poids']=$data['weight'];
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
	<th bgcolor="ff6251">
	
	<input type="button" onclick='selectAll()' value="Select All"/><br>
	<input type="button" onclick='UnSelectAll()' value="Unselect All"/>
	</th>
	<th bgcolor="ff6251">
	SKU
	</th>
	<th bgcolor="ff6251">
	Order Nu
	</th>
		<th bgcolor="ff6251">
	Customer Name
	</th>
		<th bgcolor="ff6251">
	Titre
	</th>
	<th bgcolor="ff6251">
	Poids
	</th>
	<th bgcolor="ff6251">
	Dimension
	</th>
	<th bgcolor="ff6251">
	Location
	</th>
	<th bgcolor="ff6251">
	Qte Vendu
	</th>
	<th bgcolor="ff6251">
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
						<?if ($ebayoutputname['sku']=="12345"){?>
							<svg class="barcode"
							jsbarcode-value="<?echo $ebayoutputname['sku'];?>"
							jsbarcode-textmargin="0"
							jsbarcode-height="18"
							jsbarcode-fontoptions="bold"
							jsbarcode-fontsize="10">
							</svg>
						<script>
					
						JsBarcode(".barcode").init();
						</script>
						<?}?>
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
					<?echo number_format($ebayoutputname['poids'], 2, '.', '');?>
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
mysql_close(); ?>