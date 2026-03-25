<?


//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 


$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$','n7f9655_canuship_prod');
// on sélectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',"",$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
$transaction_id="";
//print_r($_FILES['file_import']);
if(isset($_POST['month_year'])){
	$month_year=explode("-",$_POST['month_year']);
	$_POST['month']=$month_year[0];
	$_POST['year']=$month_year[1];
	$_POST['month_year']=strval($_POST['month_year']);
	//echo $_POST['month_year'];
}
echo $_POST['year'];
echo $_POST['month'];

if(isset($_GET['month'])){
	$_POST['month']=$_GET['month'];
}elseif(isset($_POST['month'])){
	$_POST['month']=$_POST['month'];
}else{
	$_POST['month']="11";
}

if(isset($_GET['tout'])){
	$_POST['tout']=$_GET['tout'];
}elseif(isset($_POST['tout'])){
	$_POST['tout']=$_POST['tout'];
}else{
	$_POST['tout']="";
}
if(isset ($_GET['year'])){
	$_POST['year']=$_GET['year'];
}elseif(isset($_POST['year'])){
	$_POST['year']=$_POST['year'];
}else{
	$_POST['year']="2020";
}
if(isset($_POST['month_year'])){
	$month_year=explode("-",$_POST['month_year']);
	$_POST['month']=$month_year[0];
	$_POST['year']=$month_year[1];
	$_POST['month_year']=strval($_POST['month_year']);
	//echo $_POST['month_year'];
}
//echo $_POST['year'];
//echo $_POST['month'];
$sql2 = 'SELECT date_added as date_transaction FROM `oc_order` order by date_added DESC limit 1';
$req2 = mysqli_query($db,$sql2); 
$data2 = mysqli_fetch_assoc($req2);
/* $verfi_date_tmp=explode(" ",$data2['date_transaction']);
$verfi_date_tmp=explode("-",$verfi_date_tmp[0]);
echo $verfi_date_tmp[0]."   ".$verfi_date_tmp[1]; */
$date_select=$_POST['year']."-".($_POST['month']+1)."-1 00:00:00";
if(date('Y-m-d', strtotime($date_select))>date('Y-m-d', strtotime($data2['date_transaction']))){// ||($_POST['year']<=$verfi_date_tmp[0] && $_POST['month']>$verfi_date_tmp[1])){
	$erreur="<br><p style=\"color:red\">Donnee manquante (".$data2['date_transaction'].")</p>";
}else{
	$erreur="Derniere mise a jour (".$data2['date_transaction'].")<br>";
}
	
			?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="../stylesheet.css" rel="stylesheet">

</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="month_canuship.php?month=<?echo $_POST['month'];?>&year=<?echo $_POST['year']?>&tout=<?echo $_GET['tout']?>" method="post" enctype="multipart/form-data">
<div class="form_description">
Information SITEWEB <?echo $erreur;
				  $yearnow=date("Y");
				  $monthnow=date("m");
				   $monthnow= $monthnow+0;
				  
				 // echo $yearnow."+++".$monthnow;?>
                  <select name="month_year" id="month_year" class="form-control">
				  <?
				  $init=0;
				  $stop=0;
				  for ($i=2018;$i<$yearnow+1;$i++){
					  $select=strval("11-".($i-1));
					  if($init==0){?>
					  <option value="11-<?echo $i-1;?>" <?php if (11 == $_POST['month'] && $i-1 == $_POST['year'] ) {  $_POST['namemonth']="Novembre (".($i-1).")";?>selected="selected"<?}?>>Novembre <?echo $i-1;?></option>
					  <option value="12-<?echo $i-1;?>" <?php if (12 == $_POST['month'] && $i-1 == $_POST['year'] ) {  $_POST['namemonth']="December (".($i-1).")";?>selected="selected"<?}?>>Decembre <?echo $i-1;?></option>
					  <?}
					  $sql2 = 'SELECT * FROM `admin_month`';
							$req2 = mysqli_query($db,$sql2); 
					while ($data2 = mysqli_fetch_assoc($req2)){
						if($stop<1){
						if($i.$data2['id']!=$yearnow.$monthnow){?>
					<option value="<?echo $data2['id'];?>-<?echo $i;?>" <?php if ($data2['id'] == $_POST['month'] && $i == $_POST['year'] ) {  $_POST['namemonth']=$data2['name']." (".($i).")";?>selected="selected"<?}?>><?echo $data2['name']." ".$i;?></option>
					<?}else{?>
					<? $stop=1;	
					}}
				 }
				  $init=1;}?>
                 </select>	
				 <input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />





<?
$datas=get_website_transaction($_POST['year'],$_POST['year'],$_POST['month'],$_POST['month'],"currency_code='CAD' AND ",$db);
//print("<pre>".print_r ($datas,true )."</pre>");
//echo count($datas);
$sales=0;
$shipping=0;
$mdiscount=0;
$sub_total=0;
$sales_elavon=0;
$sales_paypal=0;
$sales_square=0;
$sales_cash=0;
$sales_credit=0;
$sales_debit=0;

$fee=0;
$taxes=0;
$txqc=0;
$txon=0;
$txbc=0;
$txab=0;
$txsk=0;
$txmb=0;
$txns=0;
$txnb=0;
$txyk=0;
$txpei=0;
$txnwt=0;
$txnl=0;

?>
<br><br>
TransporT
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">Date</div>
<div class="divTableHead">Client</div>
<div class="divTableHead">Type payment</div>
<div class="divTableHead">Nu transaction</div>
<div class="divTableHead">Sous Total</div>
<div class="divTableHead">Rabais</div>
<div class="divTableHead">Shipping</div>
<div class="divTableHead">Taxes</div>
<div class="divTableHead">Vente Total</div>
<div class="divTableHead">Province</div>
</div>
</div>
<div class="divTableBody">

<?
foreach($datas as $data){
	$date_tmp=explode(" ",$data['date_transaction']);
	$date_info=$date_tmp[0];
//	$rate=get_fxrate($date_info,$db)*1.04;
	
?>
<div class="divTableRow">
<div class="divTableCell"><?echo $data['date_transaction'];?></div>
<div class="divTableCell"><?echo $data['firstname']." ".$data['lastname'];?></div>
<div class="divTableCell"><?echo $data['payment_code'];?></div>
<div class="divTableCell"><?echo $data['order_id'];?></div>
<div class="divTableCell"><?if($data['currency_code']=="CAD"){echo "$ ".number_format($data['sub_total']['value'],2);}else{echo "$ ".number_format($data['sub_total']['value'],2);}?></div>
<div class="divTableCell"><?if($data['currency_code']=="CAD"){echo "$ ".number_format($data['mdiscount']['value'],2);}else{echo "$ ".number_format($data['mdiscount']['value'],2);}?></div>
<div class="divTableCell"><?if($data['currency_code']=="CAD"){echo "$ ".number_format($data['shipping']['value'],2);}else{echo "$ ".number_format($data['shipping']['value'],2);}?></div>
<div class="divTableCell"><?if($data['currency_code']=="CAD")echo "$ ".number_format($data['tax']['value'],2);?></div>
<div class="divTableCell"><?if($data['currency_code']=="CAD"){echo "$ ".number_format($data['total']['value'],2);}else{echo "$ ".number_format($data['total']['value'],2);}?></div>
<div class="divTableCell"><?if($data['currency_code']=="CAD")echo $data['payment_zone'];?></div>

</div>
<?
//if($data['currency_code']=="CAD"){
//	$sales=$sales+$data['total'];}else{
$sales=$sales+$data['total']['value'];
$shipping+=$data['shipping']['value'];
$mdiscount+=$data['mdiscount']['value'];
$sub_total+=$data['sub_total']['value'];

if($data['payment_country_id']=="38")$taxes=$taxes+($data['tax']['value']);
if($data['payment_zone_id']=="612")$txqc=$txqc+($data['tax']['value']);
if($data['payment_zone_id']=="610")$txon=$txon+($data['tax']['value']);
if($data['payment_zone_id']=="603")$txbc=$txbc+($data['tax']['value']);
if($data['payment_zone_id']=="602")$txab=$txab+($data['tax']['value']);
if($data['payment_zone_id']=="613")$txsk=$txsk+($data['tax']['value']);
if($data['payment_zone_id']=="604")$txmb=$txmb+($data['tax']['value']);
if($data['payment_zone_id']=="608")$txns=$txns+($data['tax']['value']);
if($data['payment_zone_id']=="605")$txnb=$txnb+($data['tax']['value']);
if($data['payment_zone_id']=="606")$txnl=$txnl+($data['tax']['value']);
if($data['payment_zone_id']=="614")$txyk=$txyk+($data['tax']['value']);
if($data['payment_zone_id']=="611")$txpei=$txpei+($data['tax']['value']);
if($data['payment_zone_id']=="607")$txnwt=$txnwt+($data['tax']['value']);

if($data['payment_code']=="virtualmerchant")$sales_elavon+=$data['total']['value'];
if($data['payment_code']=="pp_standard")$sales_paypal+=$data['total']['value'];
if($data['payment_code']=="squareup")$sales_square+=$data['total']['value'];
if($data['payment_code']=="Cash" || $data['payment_code']=="Comptant")$sales_cash+=$data['total']['value'];
if($data['payment_code']=="Credit" || $data['payment_code']=="Crédit")$sales_credit+=$data['total']['value'];
if($data['payment_code']=="Debit" || $data['payment_code']=="Débit")$sales_debit+=$data['total']['value'];


}
?>
<div class="divTableRow">
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell" style="color: red" ><?echo "$ ".number_format($sub_total,2);?> CAD</div>
<div class="divTableCell" style="color: red" ><?echo "$ ".number_format($mdiscount,2);?> CAD</div>
<div class="divTableCell" style="color: red" ><?echo "$ ".number_format($shipping,2);?> CAD</div>
<div class="divTableCell" style="color: red"><?echo "$ ".number_format($taxes,2);?> CAD</div>
<div class="divTableCell" style="color: red" ><?echo "$ ".number_format($sales,2);?> CAD</div>
<div class="divTableCell"></div>

</div>
</div>

</div>
</div>
</div>
<div class="blueTable outerTableFooter">
<div class="tableFootStyle">
</div>
</div>
Ventes par Methode de payment SITEWEB CAD
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">Paypal</div>
<div class="divTableHead">Elavon</div>
<div class="divTableHead">Square</div>

</div>
</div>
<div class="divTableBody">
<div class="divTableRow">
<div class="divTableCell"<?if($sales_paypal>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($sales_paypal,2);?> CAD</div>
<div class="divTableCell"<?if($sales_elavon>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($sales_elavon,2);?> CAD</div>
<div class="divTableCell"<?if($sales_square>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($sales_square,2);?> CAD</div>

</div>
</div>

</div>
</div>
</div>
Ventes par Methode de payment MAGASIN CAD
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">Cash</div>
<div class="divTableHead">Debit</div>
<div class="divTableHead">Credit</div>
</div>
</div>
<div class="divTableBody">
<div class="divTableRow">

<div class="divTableCell"<?if($sales_cash>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($sales_cash,2);?> CAD</div>
<div class="divTableCell"<?if($sales_credit>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($sales_credit,2);?> CAD</div>
<div class="divTableCell"<?if($sales_debit>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($sales_debit,2);?> CAD</div>
</div>
</div>

</div>
</div>
</div>
<div class="blueTable outerTableFooter">
<div class="tableFootStyle">
<p style="color:red">VERIFICATION: (<?echo number_format(($sales-$sales_paypal-$sales_elavon-$sales_square-$sales_cash-$sales_credit-$sales_debit),2);?>)</p>
</div>
</div>
<div class="blueTable outerTableFooter">
<div class="tableFootStyle">
</div>
</div>
Taxes par provinces en CAD
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead"></div>
<div class="divTableHead">QC</div>
<div class="divTableHead">ON</div>
<div class="divTableHead">BC</div>
<div class="divTableHead">AB</div>
<div class="divTableHead">SK</div>
<div class="divTableHead">MB</div>
<div class="divTableHead">NS</div>
<div class="divTableHead">NB</div>
<div class="divTableHead">IPE</div>
<div class="divTableHead">NL</div>
<div class="divTableHead">YK</div>
<div class="divTableHead">NWT</div>
</div>
</div>
<div class="divTableBody">
<div class="divTableRow">
<div class="divTableCell">Total</div>
<div class="divTableCell"<?if($txqc>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txqc,2);?> CAD</div>
<div class="divTableCell"<?if($txon>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txon,2);?> CAD</div>
<div class="divTableCell"<?if($txbc>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txbc,2);?> CAD</div>
<div class="divTableCell"<?if($txab>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txab,2);?> CAD</div>
<div class="divTableCell"<?if($txsk>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txsk,2);?> CAD</div>
<div class="divTableCell"<?if($txmb>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txmb,2);?> CAD</div>
<div class="divTableCell"<?if($txns>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txns,2);?> CAD</div>
<div class="divTableCell"<?if($txnb>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txnb,2);?> CAD</div>
<div class="divTableCell"<?if($txpei>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txpei,2);?> CAD</div>
<div class="divTableCell"<?if($txnl>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txnl,2);?> CAD</div> 
<div class="divTableCell"<?if($txyk>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txyk,2);?> CAD</div>
<div class="divTableCell"<?if($txnwt>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txnwt,2);?> CAD</div>
</div>
<div class="divTableRow">
<div class="divTableCell">TPS</div>
<div class="divTableCell"<?if($txqc>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format(($txqc/0.14975*.05),2);?> CAD</div>
<div class="divTableCell"></div>
<div class="divTableCell"<?if($txbc>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txbc,2);?> CAD</div>
<div class="divTableCell"<?if($txab>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txab,2);?> CAD</div>
<div class="divTableCell"<?if($txsk>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txsk,2);?> CAD</div>
<div class="divTableCell"<?if($txmb>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txmb,2);?> CAD</div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"<?if($txyk>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txyk,2);?> CAD</div>
<div class="divTableCell"<?if($txnwt>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txnwt,2);?> CAD</div>
</div>
<div class="divTableRow">
<div class="divTableCell">TVH</div>
<div class="divTableCell"></div>
<div class="divTableCell"<?if($txon>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txon,2);?> CAD</div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"<?if($txns>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txns,2);?> CAD</div>
<div class="divTableCell"<?if($txnb>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txnb,2);?> CAD</div>
<div class="divTableCell"<?if($txpei>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txpei,2);?> CAD</div>
<div class="divTableCell"<?if($txnl>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($txnl,2);?> CAD</div> 
<div class="divTableCell"></div>
<div class="divTableCell"></div>
</div>
<div class="divTableRow">
<div class="divTableCell">TVQ</div>
<div class="divTableCell"<?if($txqc>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format(($txqc/0.14975*.09975),2);?> CAD</div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
</div>
</div>

</div>
</div>
</div>
<div class="blueTable outerTableFooter">
<div class="tableFootStyle">
<p style="color:red">VERIFICATION: (<?echo number_format(($taxes-$txqc-$txon-$txbc-$txab-$txsk-$txmb-$txns-$txnb-$txyk-$txpei-$txnwt-$txnl),2);?>)</p>
</div>
</div>
<br><br>
		<p class="buttons">
		

		<input type="hidden" name="month" value="<?echo $_POST['month'];?>" />
		<input type="hidden" name="year" value="<?echo $_POST['year'];?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" >Retour au MENU</a></h1>
</form>
<p id="footer"> 
</body>
</html>
<?  // on ferme la connexion à mysql 
mysqli_close($db); 

function get_website_transaction($yeardeb,$yearfin,$monthdeb,$monthfin,$where,$db){

	$return=array();
	$sql="SELECT * FROM `oc_order` o LEFT JOIN `oc_order_history` oh ON (oh.order_id=o.order_id) LEFT JOIN `oc_order_status` os ON (oh.order_status_id=os.order_status_id && oh.order_status_id=5)";
	$sql.="WHERE o.date_added  BETWEEN '".$yeardeb."-".$monthdeb."-01 00:00:00' AND '".$yearfin."-".$monthfin."-31 23:59:59' group by o.order_id order by oh.date_added ";
//	echo "<br>".$sql;

	$req = mysqli_query($db,$sql); 
	$i=0;
	$sum=0;
	while($result= mysqli_fetch_assoc($req)) {
		$sql2="SELECT * FROM `oc_order_total` ot WHERE (ot.order_id=".$result['order_id'].")";
		$req2 = mysqli_query($db,$sql2); 
		$return[$i] = $result;
		while($result2= mysqli_fetch_assoc($req2)){
			$return[$i][$result2['code']] = $result2;
		}
		$i++;
		$sum+=$result2['Total'];
	}
//	//print("<pre>".print_r ($return,true )."</pre>");
	return $return;
}
?>


