<?


//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$','n7f9655_phoenixsupplies');
include_once 'functionload.php';
// on sélectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',"",$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
$transaction_id="";
//print_r($_FILES['file_import']);
if(!isset($_POST['year'])){
	$_POST['year']=2021;
}

$sql2 = 'SELECT * FROM `admin_currency_conversion` WHERE currency = "CAD" AND rate!=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
			?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet"> 

</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="summaryebay.php" method="post" enctype="multipart/form-data">
<div class="form_description">
<h1>Sommaire par année</h1>
                  <select name="year" id="input-year" class="form-control">
                    <option value="2018" <?php if ($_POST['year'] == 2018) { ?>selected="selected"<?}?>>2018</option>
                    <option value="2019" <?php if ($_POST['year'] == 2019) { ?>selected="selected"<?}?>>2019</option>
                    <option value="2020" <?php if ($_POST['year'] == 2020) { ?>selected="selected"<?}?>>2020</option>
                    <option value="2021" <?php if ($_POST['year'] == 2021) { ?>selected="selected"<?}?>>2021</option> 
                 </select>
ANNÉE <?echo $_POST['year'];
$i=11;?>
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">Mois</div>
<div class="divTableHead">Vente</div>
<div class="divTableHead">Remboursement</div>
<div class="divTableHead">Taxes</div>
<div class="divTableHead">Frais Paypal</div>
<div class="divTableHead">Achat USD</div>
<div class="divTableHead">Achat CAD</div>
</div>
</div>
<div class="divTableBody">
<?$i=11;?>
<div class="divTableRow">
<div class="divTableCell">Novembre (<?echo $_POST['year']-1;?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i=12;?>
<div class="divTableRow">
<div class="divTableCell">Décembre (<?echo $_POST['year']-1;?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year']-1,$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i=1;?>
<div class="divTableRow">
<div class="divTableCell">Janvier (<?echo $_POST['year'];?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i++;?>
<div class="divTableRow">
<div class="divTableCell">Février (<?echo $_POST['year'];?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i++;?>
<div class="divTableRow">
<div class="divTableCell">Mars (<?echo $_POST['year'];?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i++;?>
<div class="divTableRow">
<div class="divTableCell">Avril (<?echo $_POST['year'];?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i++;?>
<div class="divTableRow">
<div class="divTableCell">Mai (<?echo $_POST['year'];?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i++;?>
<div class="divTableRow">
<div class="divTableCell">Juin (<?echo $_POST['year'];?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i++;?>
<div class="divTableRow">
<div class="divTableCell">Juillet (<?echo $_POST['year'];?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i++;?>
<div class="divTableRow">
<div class="divTableCell">Aout (<?echo $_POST['year'];?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i++;?>
<div class="divTableRow">
<div class="divTableCell">Septembre (<?echo $_POST['year'];?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i++;?>
<div class="divTableRow">
<div class="divTableCell">Octobre (<?echo $_POST['year'];?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year'],$_POST['year'],$i,$i,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
<?$i++;?>
<div class="divTableRow">
<div class="divTableCell">Total (<?echo $_POST['year'];?>)</div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year'],11,10,"ebay_sales","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year'],11,10,"ebay_refunds","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year'],11,10,"ebay_taxes","",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year'],11,10,"paypal_fee","from_order='eBay' AND",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year'],11,10,"purchases_paypal","currency='USD' AND ",$db),2);?></div>
<div class="divTableCell"><?echo "$ ".number_format(get_sum($_POST['year']-1,$_POST['year'],11,10,"purchases_paypal","currency='CAD' AND ",$db),2);?></div>
</div>
</div>
</div>
</div>
<div class="blueTable outerTableFooter">
<div class="tableFootStyle">
</div>
</div>

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" >Retour au MENU</a></h1>
</form>
<p id="footer"> 
</body>
</html>
<?  // on ferme la connexion à mysql 
mysqli_close($db); ?>


