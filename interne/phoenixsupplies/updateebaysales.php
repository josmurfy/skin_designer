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
/* 		foreach($_POST[vendu] as $vendu) 
			{	
				$itemvendu=explode(",", $vendu);
				$sql2 = 'UPDATE `oc_product` SET quantity=quantity-'.$itemvendu[1].' where product_id='.$itemvendu[0];
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				
			} */
	$sql2 = 'UPDATE `oc_product` SET `location`="",ebay_last_check="2020-09-01" where quantity=0';
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
<form id="form_67341" class="appnitro" action="updateebaysales.php" method="post">
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
		//verifer format


		foreach($ebayinputnametab as $ebayinputname) 
		{	

			
				
				
				$ebayinputnameline=explode("\t", $ebayinputname);
				if((ctype_digit($ebayinputnameline[12]) && ctype_digit($ebayinputnameline[13]) || ctype_digit($ebayinputnameline[11])) && (ctype_digit($ebayinputnameline[39]) || $ebayinputnameline[39]=="") )
				{
					if(is_numeric($ebayinputnameline[0])){
							
						$test=strlen($ebayinputnameline[31]);
						
						$sql = 'select * from oc_product,oc_product_description where oc_product.product_id=oc_product_description.product_id and (oc_product.product_id='.$ebayinputnameline[31].')'; 
						//echo $sql."<br>";
						$req = mysqli_query($db,$sql);
						$data = mysqli_fetch_assoc($req);

						for ($i = 23; $i <= 26; $i++) {
								$ebayinputnamelinedate=explode("-", $ebayinputnameline[$i]);
								$ebayinputnameline[$i]="20".$ebayinputnamelinedate[2]."-";
								
								switch ($ebayinputnamelinedate[0]) {
								case "Jan":
									$ebayinputnameline[$i].="01";
									break;
								case "Feb":
									$ebayinputnameline[$i].="02";
									break;
								case "Mar":
									$ebayinputnameline[$i].="03";
									break;
								case "Apr":
									$ebayinputnameline[$i].="04";
									break;
								case "May":
									$ebayinputnameline[$i].="05";
									break;
								case "Jun":
									$ebayinputnameline[$i].="06";
									break;
								case "Jul":
									$ebayinputnameline[$i].="07";
									break;
								case "Aug":
									$ebayinputnameline[$i].="08";
									break;
								case "Sep":
									$ebayinputnameline[$i].="09";
									break;
								case "Oct":
									$ebayinputnameline[$i].="10";
									break;
								case "Nov":
									$ebayinputnameline[$i].="11";
									break;
								case "Dec":
									$ebayinputnameline[$i].="12";
									break;
								}
								$ebayinputnameline[$i].="-".$ebayinputnamelinedate[1]." 00:00:00";
								//echo "<br>".$ebayinputnameline[$i];
						}


						
						
						$sql = "INSERT INTO `oc_ebay_sales` (`Sales_Record_Number`, `User_Id`, `Buyer_Fullname`, `Buyer_Phone_Number`, `Buyer_Email`, `Buyer_Address_1`,
						`Buyer_Address_2`, `Buyer_City`, `Buyer_State`, `Buyer_Zip`, `Buyer_Country`, `Order_ID`, `Item_ID`, `Transaction_ID`, `Item_Title`, `Quantity`, 
						`Sale_Price`, `Shipping_And_Handling`, `Sales_Tax`, `Insurance`, `Total_Price`, `Payment_Method`, `PayPal_Transaction_ID`, `Sale_Date`, 
						`Checkout_Date`, `Paid_on_Date`, `Shipped_on_Date`, `Shipping_Service`, `Feedback_Left`, `Feedback_Received`, `Notes_to_Yourself`, `Custom_Label`,
						`Listed_On`, `Sold_On`, `Private_Notes`, `Product_ID_Type`, `Product_ID_Value`, `Product_ID_Value_2`, `Variation_Details`, `Product_Reference_ID`, 
						`Tracking_Number`, `Phone`, `Location_Onsite`, `Sku_Onsite`, `Qty_Onsite`)
						VALUES ('".$ebayinputnameline[0]."', '".$ebayinputnameline[1]."', '".$ebayinputnameline[2]."', '".$ebayinputnameline[3]."', '".$ebayinputnameline[4]."'
							  , '".$ebayinputnameline[5]."', '".$ebayinputnameline[6]."', '".$ebayinputnameline[7]."', '".$ebayinputnameline[8]."', '".$ebayinputnameline[9]."'
							  , '".$ebayinputnameline[10]."', '".$ebayinputnameline[11]."', '".$ebayinputnameline[12]."', '".$ebayinputnameline[13]."', '".$ebayinputnameline[14]."'
							  , '".$ebayinputnameline[15]."', '".str_replace("$","",$ebayinputnameline[16])."', '".str_replace("$","",$ebayinputnameline[17])."', '".str_replace("$","",$ebayinputnameline[18])."', '".str_replace("$","",$ebayinputnameline[19])."'
							  , '".str_replace("$","",$ebayinputnameline[20])."', '".$ebayinputnameline[21]."', '".$ebayinputnameline[22]."', '".$ebayinputnameline[23]."', '".$ebayinputnameline[24]."'
							  , '".$ebayinputnameline[25]."', '".$ebayinputnameline[26]."', '".$ebayinputnameline[27]."', '".$ebayinputnameline[28]."', '".$ebayinputnameline[29]."'
							  , '".$ebayinputnameline[30]."', '".$ebayinputnameline[31]."', '".$ebayinputnameline[32]."', '".$ebayinputnameline[33]."', '".$ebayinputnameline[34]."'
							  , '".$ebayinputnameline[35]."', '".$ebayinputnameline[36]."', '".$ebayinputnameline[37]."', '".$ebayinputnameline[38]."', '".$ebayinputnameline[39]."'
							  , '".$ebayinputnameline[40]."', '".$ebayinputnameline[41]."', '".$data['location']."', '".$data['sku']."', '".$data['quantity']."')"; 
						echo $sql."<br>";
						$req = mysqli_query($db,$sql);
						}
				}elseif(is_numeric($ebayinputnameline[11])){
					echo "ALLALLALAWDFDSDFADFADSFD";
				}else{
					if(is_numeric($ebayinputnameline[0])){
						echo "LE FORMAT DU FICHIER EST NON NUMERIC";
						echo $ebayinputnameline[0];
						break;
					}

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