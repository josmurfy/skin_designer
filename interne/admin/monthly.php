<?


//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
//include_once '../connection.php';
$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$','n7f9655_phoenixliquidation');
include_once 'functionload.php';
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
//echo $_POST['year'];
//echo $_POST['month'];

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
	$_POST['year']="2021";
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

$sql2 = 'SELECT date_transaction FROM `admin_paypal` order by date_transaction DESC limit 1';
$req2 = mysqli_query($db,$sql2); 
$data2 = mysqli_fetch_assoc($req2);
/* $verfi_date_tmp=explode(" ",$data2['date_transaction']);
$verfi_date_tmp=explode("-",$verfi_date_tmp[0]);
echo $verfi_date_tmp[0]."   ".$verfi_date_tmp[1]; */
$date_select=$_POST['year']."-".($_POST['month']+1)."-1 00:00:00";
if(date('Y-m-d', strtotime($date_select))>date('Y-m-d', strtotime($data2['date_transaction']))){// ||($_POST['year']<=$verfi_date_tmp[0] && $_POST['month']>$verfi_date_tmp[1])){
	$erreur="<br><p style=\"color:red\">Donnee manquante (".$data2['date_transaction'].")</p>";
}else{
	$erreur="Derniere mise a jour (".$data2['date_transaction'].")<BR><BR>";
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
<form id="form_67341" class="appnitro" action="monthly.php?month=<?echo $_POST['month'];?>&year=<?echo $_POST['year']?>&tout=<?echo $_GET['tout']?>" method="post" enctype="multipart/form-data">
<div class="form_description">
Information  <?echo $erreur;
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
//echo "allo";
$datas=get_transaction($_POST['year'],$_POST['year'],$_POST['month'],$_POST['month'],"ebay_sales","",$db);
//print("<pre>".print_r ($datas,true )."</pre>");

$sales=0;
$fee=0;
$taxes=0;$taxesusd=0;
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
Ventes EBAY
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">Date</div>
<div class="divTableHead">Client</div>
<div class="divTableHead">Devise</div>
<div class="divTableHead">Vente Net(USD)</div>
<div class="divTableHead">Frais Paypal(USD)</div>
<div class="divTableHead">Taxes (USD)</div>
<div class="divTableHead">Province</div>
<div class="divTableHead">FX</div>
</div>
</div>
<div class="divTableBody">

<?
foreach($datas as $data){
	$date_tmp=explode(" ",$data['date_transaction']);
	$date_info=$date_tmp[0];
	$rate=get_fxrate($date_info,$db);
if($_POST['tout']=="oui"){?>
<div class="divTableRow">
<div class="divTableCell"><?echo $data['date_transaction'];?></div>
<div class="divTableCell"><?echo $data['name'];?></div>
<div class="divTableCell"><?echo $data['currency'];?></div>
<div class="divTableCell"><?echo "$ ".number_format($data['net'],2);?></div>
<div class="divTableCell"><?echo "$ ".number_format($data['fee'],2);?></div>
<div class="divTableCell"><?if($data['country']=="CA")echo "$ ".number_format($data['sales_taxes'],2);?></div>
<div class="divTableCell"><?if($data['country']=="CA")echo $data['state_province'];?></div>
<div class="divTableCell"><?echo number_format($rate,2);?></div>
</div>
<?}
$sales=$sales+$data['net'];
$fee=$fee+($data['fee']);
if($data['country']=="CA"){$taxes=$taxes+($data['sales_taxes']*$rate);
//echo "taxes: ".number_format($data['sales_taxes']*$rate,2)."<br>PROV:".$data['state_province']."<br>";
}
if($data['country']=="CA")$taxesusd=$taxesusd+($data['sales_taxes']);

if($data['state_province']=="QC")$txqc=$txqc+($data['sales_taxes']*$rate);
if($data['state_province']=="ON")$txon=$txon+($data['sales_taxes']*$rate);
if($data['state_province']=="BC")$txbc=$txbc+($data['sales_taxes']*$rate);
if($data['state_province']=="AB")$txab=$txab+($data['sales_taxes']*$rate);
if($data['state_province']=="SK")$txsk=$txsk+($data['sales_taxes']*$rate);
if($data['state_province']=="MB")$txmb=$txmb+($data['sales_taxes']*$rate);
if($data['state_province']=="NS")$txns=$txns+($data['sales_taxes']*$rate);
if($data['state_province']=="NB")$txnb=$txnb+($data['sales_taxes']*$rate);
if($data['state_province']=="NL")$txnl=$txnl+($data['sales_taxes']*$rate);
if($data['state_province']=="YK")$txyk=$txyk+($data['sales_taxes']*$rate);
if($data['state_province']=="PEI" || $data['state_province']=="PE")$txpei=$txpei+($data['sales_taxes']*$rate);
if($data['state_province']=="NWT" || $data['state_province']=="NU" || $data['state_province']=="NT")$txnwt=$txnwt+($data['sales_taxes']*$rate);
}?>

<div class="divTableRow">
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell" style="color: red" ><?echo "$ ".number_format($sales,2);?> USD</div>
<div class="divTableCell" style="background-color:green; color: white;"><?echo "$ ".number_format($fee,2);?> USD</div>
<div class="divTableCell" style="color: red"><?echo "$ ".number_format($taxesusd,2);?> USD</div>
<div class="divTableCell"></div>
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
Taxes par provinces en CAD
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">**</div>
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
<div class="divTableHead">Total</div>
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
<div class="divTableCell"<?echo 'style="background-color:green; color: white;"';?>><?echo "$ ".number_format($taxes,2);?> CAD</div>
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
<div class="divTableCell"<?echo 'style="background-color:green; color: white;"';?>><?echo "$ ".number_format($taxes-$txon-$txns-$txnb-$txpei-$txnl-($txqc/0.14975*.09975),2);?> CAD</div>

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
<div class="divTableCell"<?echo 'style="background-color:green; color: white;"';?>><?echo "$ ".number_format($txon+$txns+$txnb+$txpei+$txnl,2);?> CAD</div>

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
<div class="divTableCell"<?echo 'style="background-color:green; color: white;"';?>><?echo "$ ".number_format($txqc/0.14975*.09975,2);?> CAD</div>

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
<?
$datas=get_transaction($_POST['year'],$_POST['year'],$_POST['month'],$_POST['month'],"ebay_refunds","",$db);
//print("<pre>".print_r ($datas,true )."</pre>");
$sales=0;
$fee=0;
$taxes=0;$taxesusd=0;
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
Remboursement EBAY
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">Date</div>
<div class="divTableHead">Client</div>
<div class="divTableHead">Devise</div>
<div class="divTableHead">Vente Net(USD)</div>
<div class="divTableHead">Frais Paypal(USD)</div>
<div class="divTableHead">Taxes</div>
<div class="divTableHead">Province</div>
<div class="divTableHead">FX</div>
</div>
</div>
<div class="divTableBody">

<?
foreach($datas as $data){
	$date_tmp=explode(" ",$data['date_transaction']);
	$date_info=$date_tmp[0];
	$rate=get_fxrate($date_info,$db);
	if($_POST['tout']=="oui"){
?>
<div class="divTableRow">
<div class="divTableCell"><?echo $data['date_transaction'];?></div>
<div class="divTableCell"><?echo $data['name'];?></div>
<div class="divTableCell"><?echo $data['currency'];?></div>
<div class="divTableCell"><?echo "$ ".number_format($data['net'],2);?></div>
<div class="divTableCell"><?echo "$ ".number_format($data['fee'],2);?></div>
<div class="divTableCell"><?if($data['country']=="CA")echo "$ ".number_format($data['sales_taxes'],2);?></div>
<div class="divTableCell"><?if($data['country']=="CA")echo $data['state_province'];?></div>
<div class="divTableCell"><?echo number_format($rate,2);?></div>
</div>
	<?}
$sales=$sales+$data['net'];
$fee=$fee+($data['fee']);
if($data['country']=="CA")$taxes=$taxes+($data['sales_taxes']*$rate);if($data['country']=="CA")$taxesusd=$taxesusd+($data['sales_taxes']);

if($data['state_province']=="QC")$txqc=$txqc+($data['sales_taxes']*$rate);
if($data['state_province']=="ON")$txon=$txon+($data['sales_taxes']*$rate);
if($data['state_province']=="BC")$txbc=$txbc+($data['sales_taxes']*$rate);
if($data['state_province']=="AB")$txab=$txab+($data['sales_taxes']*$rate);
if($data['state_province']=="SK")$txsk=$txsk+($data['sales_taxes']*$rate);
if($data['state_province']=="MB")$txmb=$txmb+($data['sales_taxes']*$rate);
if($data['state_province']=="NS")$txns=$txns+($data['sales_taxes']*$rate);
if($data['state_province']=="NB")$txnb=$txnb+($data['sales_taxes']*$rate);
if($data['state_province']=="NL")$txnl=$txnl+($data['sales_taxes']*$rate);
if($data['state_province']=="YK")$txyk=$txyk+($data['sales_taxes']*$rate);
if($data['state_province']=="PEI" || $data['state_province']=="PE")$txpei=$txpei+($data['sales_taxes']*$rate);
if($data['state_province']=="NWT" || $data['state_province']=="NU")$txnwt=$txnwt+($data['sales_taxes']*$rate);
}?>
<div class="divTableRow">
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell" style="color: red" ><?echo "$ ".number_format($sales,2);?> USD</div>
<div class="divTableCell" style="background-color:green; color: white;"><?echo "$ ".number_format($fee,2);?> USD</div>
<div class="divTableCell" style="color: red"><?echo "$ ".number_format($taxesusd,2);?> USD</div>
<div class="divTableCell"></div>
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
<div class="divTableHead">TOTAL</div>

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
<div class="divTableCell"<?echo 'style="background-color:green; color: white;"';?>><?echo "$ ".number_format($taxes,2);?> CAD</div>

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
<div class="divTableCell"<?echo 'style="background-color:green; color: white;"';?>><?echo "$ ".number_format($taxes-($txon+$txns+$txnb+$txpei+$txnl+($txqc/0.14975*.09975)),2);?> CAD</div>

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
<div class="divTableCell"<?echo 'style="background-color:green; color: white;"';?>><?echo "$ ".number_format($txon+$txns+$txnb+$txpei+$txnl,2);?> CAD</div>

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
<div class="divTableCell"<?echo 'style="background-color:green; color: white;"';?>><?echo "$ ".number_format(($txqc/0.14975*.09975),2);?> CAD</div>

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

<?
$datas=get_transaction($_POST['year'],$_POST['year'],$_POST['month'],$_POST['month'],"bonanza_sales","",$db);
//print("<pre>".print_r ($datas,true )."</pre>");
$sales=0;
$fee=0;
$taxes=0;$taxesusd=0;
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
Ventes BONANZA
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">Date</div>
<div class="divTableHead">Client</div>
<div class="divTableHead">Devise</div>
<div class="divTableHead">Vente Net(USD)</div>
<div class="divTableHead">Frais Paypal(USD)</div>

<div class="divTableHead">FX</div>
</div>
</div>
<div class="divTableBody">

<?
foreach($datas as $data){
	$date_tmp=explode(" ",$data['date_transaction']);
	$date_info=$date_tmp[0];
	$rate=get_fxrate($date_info,$db);
	if($_POST['tout']=="oui"){?>

<div class="divTableRow">
<div class="divTableCell"><?echo $data['date_transaction'];?></div>
<div class="divTableCell"><?echo $data['name'];?></div>
<div class="divTableCell"><?echo $data['currency'];?></div>
<div class="divTableCell"><?echo "$ ".number_format($data['net'],2);?></div>
<div class="divTableCell"><?echo "$ ".number_format($data['fee'],2);?></div>

<div class="divTableCell"><?echo number_format($rate,2);?></div>
</div>
<?
	}
$sales=$sales+$data['net'];
$fee=$fee+($data['fee']);

}?>
<div class="divTableRow">
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell" style="color: red" ><?echo "$ ".number_format($sales,2);?> USD</div>
<div class="divTableCell" style="background-color:green; color: white;"><?echo "$ ".number_format($fee,2);?> USD</div>
<div class="divTableCell" style="color: red"><?echo "$ ".number_format($taxesusd,2);?> USD</div>

</div>
</div>

</div>
</div>
</div>
<div class="blueTable outerTableFooter">
<div class="tableFootStyle">
</div>
</div>


<?
$datas=get_transaction($_POST['year'],$_POST['year'],$_POST['month'],$_POST['month'],"bonanza_refunds","",$db);
//print("<pre>".print_r ($datas,true )."</pre>");
$sales=0;
$fee=0;
$taxes=0;$taxesusd=0;
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
Remboursement Bonanza
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">Date</div>
<div class="divTableHead">Client</div>
<div class="divTableHead">Devise</div>
<div class="divTableHead">Vente Net(USD)</div>
<div class="divTableHead">Frais Paypal(USD)</div>

<div class="divTableHead">FX</div>
</div>
</div>
<div class="divTableBody">

<?
foreach($datas as $data){
	$date_tmp=explode(" ",$data['date_transaction']);
	$date_info=$date_tmp[0];
	$rate=get_fxrate($date_info,$db);
	if($_POST['tout']=="oui"){
?>
<div class="divTableRow">
<div class="divTableCell"><?echo $data['date_transaction'];?></div>
<div class="divTableCell"><?echo $data['name'];?></div>
<div class="divTableCell"><?echo $data['currency'];?></div>
<div class="divTableCell"><?echo "$ ".number_format($data['net'],2);?></div>
<div class="divTableCell"><?if($data['currency']=="CAD"){echo "$ ".number_format($data['fee'],2);}else{echo "$ ".number_format($data['fee'],2);}?></div>

<div class="divTableCell"><?echo number_format($rate,2);?></div>
</div>
<?
	}
$sales=$sales+$data['net'];
$fee=$fee+($data['fee']);

}?>
<div class="divTableRow">
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell" style="color: red" ><?echo "$ ".number_format($sales,2);?> USD</div>
<div class="divTableCell" style="background-color:green; color: white;"><?echo "$ ".number_format($fee,2);?> USD</div>
<div class="divTableCell" style="color: red"><?echo "$ ".number_format($taxesusd,2);?> USD</div>

</div>
</div>

</div>
</div>
</div>
<div class="blueTable outerTableFooter">
<div class="tableFootStyle">
</div>
</div>


<?
$datas=get_transaction($_POST['year'],$_POST['year'],$_POST['month'],$_POST['month'],"shipping","",$db);
//print("<pre>".print_r ($datas,true )."</pre>");
$ebayus=0;
$ebayca=0;
$postponyca=0;
$postponyus=0;
$shippoca=0;
$shippous=0;
$totalshipping=0;
$taxes=0;$taxesusd=0;
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

<?
$datas=get_transaction($_POST['year'],$_POST['year'],$_POST['month'],$_POST['month'],"ebay_fee","",$db);
//print("<pre>".print_r ($datas,true )."</pre>");
$ebayus=0;
$ebayca=0;
$postponyca=0;
$postponyus=0;
$shippoca=0;
$shippous=0;
$totalshipping=0;
$taxes=0;$taxesusd=0;
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


<?
$datas=get_transaction($_POST['year'],$_POST['year'],$_POST['month'],$_POST['month'],"transfers","",$db);
//print("<pre>".print_r ($datas,true )."</pre>");
$sales=0;
$salescad=0;
$fee=0;
$taxes=0;$taxesusd=0;
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


Transfer entre compte paypal
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">Date</div>
<div class="divTableHead">Compte Paypal</div>
<div class="divTableHead">Devise</div>
<div class="divTableHead">Cout(USD)</div>
<div class="divTableHead">Cout(CAD)</div>
</div>
</div>
<div class="divTableBody">

<?
foreach($datas as $data){
	$date_tmp=explode(" ",$data['date_transaction']);
	$date_info=$date_tmp[0];
	$rate=get_fxrate($date_info,$db);
?>
<div class="divTableRow">
<div class="divTableCell"><?echo $data['date_transaction'];?></div>
<div class="divTableCell"><?echo $data['name'];?></div>
<div class="divTableCell"><?echo $data['currency'];?></div>
<div class="divTableCell"<?if($data['currency']=="USD"){echo 'style="background-color:green; color: white;"';}?>><?if($data['currency']=="USD")echo "$ ".number_format(-$data['gross'],2);?></div>
<div class="divTableCell"<?if($data['currency']=="CAD"){echo 'style="background-color:green; color: white;"';}?>><?if($data['currency']=="CAD"){echo "$ ".number_format(-$data['gross'],2);}?></div>
</div>
<?
if($data['currency']=="USD")$sales=$sales+$data['gross'];
if($data['currency']=="CAD")$salescad=$salescad+($data['gross']);
$fee=$fee+($data['fee']*$rate);
$taxes=$taxes+($data['sales_taxes']*$rate);

if($data['state_province']=="QC")$txqc=$txqc+($data['sales_taxes']*$rate);
if($data['state_province']=="ON")$txon=$txon+($data['sales_taxes']*$rate);
if($data['state_province']=="BC")$txbc=$txbc+($data['sales_taxes']*$rate);
if($data['state_province']=="AB")$txab=$txab+($data['sales_taxes']*$rate);
if($data['state_province']=="SK")$txsk=$txsk+($data['sales_taxes']*$rate);
if($data['state_province']=="MB")$txmb=$txmb+($data['sales_taxes']*$rate);
if($data['state_province']=="NS")$txns=$txns+($data['sales_taxes']*$rate);
if($data['state_province']=="NB")$txnb=$txnb+($data['sales_taxes']*$rate);
if($data['state_province']=="NL")$txnl=$txnl+($data['sales_taxes']*$rate);
if($data['state_province']=="YK")$txyk=$txyk+($data['sales_taxes']*$rate);
if($data['state_province']=="PEI")$txpei=$txpei+($data['sales_taxes']*$rate);
if($data['state_province']=="NWT")$txnwt=$txnwt+($data['sales_taxes']*$rate);
}?>
<div class="divTableRow">
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"><?echo "$ ".number_format(-$sales,2);?> USD</div>
<div class="divTableCell"><?echo "$ ".number_format(-$salescad,2);?> CAD</div>


</div>
</div>

</div>
</div>
</div>
<div class="blueTable outerTableFooter">
<div class="tableFootStyle">
</div>
</div>
<?
$datas=get_transaction($_POST['year'],$_POST['year'],$_POST['month'],$_POST['month'],"deposits","",$db);
//print("<pre>".print_r ($datas,true )."</pre>");
$sales=0;
$salescad=0;
$fee=0;
$taxes=0;$taxesusd=0;
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
Depot sur PayPal
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">Date</div>
<div class="divTableHead">Produit</div>
<div class="divTableHead">Type</div>
<div class="divTableHead">Devise</div>
<div class="divTableHead">Source</div>
<div class="divTableHead">Montant</div>
</div>
</div>
<div class="divTableBody">

<?
foreach($datas as $data){
	$date_tmp=explode(" ",$data['date_transaction']);
	$date_info=$date_tmp[0];
	$rate=get_fxrate($date_info,$db);
?>
<div class="divTableRow">
<div class="divTableCell"><?echo $data['date_transaction'];?></div>
<div class="divTableCell"><?echo $data['item_title'];?></div>
<div class="divTableCell"><?echo $data['type_transaction'];?></div>
<div class="divTableCell"><?echo $data['currency'];?></div>
<div class="divTableCell"><?if($data['currency']=="USD"){echo "KeyBank";}else{echo "RBC";}?></div>
<div class="divTableCell"<?if($data['gross']>0){echo 'style="background-color:green; color: white;"';}?>><?echo "$ ".number_format($data['gross'],2);?></div>

</div>
<?}?>
</div>
</div>
</div>
<div class="blueTable outerTableFooter"> 
<div class="tableFootStyle">
</div>
</div>

<?
$datas=get_transaction($_POST['year'],$_POST['year'],$_POST['month'],$_POST['month'],"withdrawals","",$db);
//print("<pre>".print_r ($datas,true )."</pre>");
$sales=0;
$salescad=0;
$fee=0;
$taxes=0;$taxesusd=0;
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
<br>
<br>
Depot compte de banque
<div class="divTable blueTable">
<div class="divTableHeading">
<div class="divTableRow">
<div class="divTableHead">Date</div>
<div class="divTableHead">Produit</div>
<div class="divTableHead">Type</div>
<div class="divTableHead">Compagnie</div>
<div class="divTableHead">Devise</div>
<div class="divTableHead">Source</div>
<div class="divTableHead">Cout(USD)</div>
<div class="divTableHead">Cout(CAD)</div>
</div>
</div>
<div class="divTableBody">

<?
foreach($datas as $data){
	$date_tmp=explode(" ",$data['date_transaction']);
	$date_info=$date_tmp[0];
	$rate=get_fxrate($date_info,$db);
?>
<div class="divTableRow">
<div class="divTableCell"><?echo $data['date_transaction'];?></div>
<div class="divTableCell"><?echo $data['item_title'];?></div>
<div class="divTableCell"><?echo $data['type_transaction'];?></div>
<div class="divTableCell"><?echo $data['name'];?></div>
<div class="divTableCell"><?echo $data['currency'];?></div>
<div class="divTableCell"><?if($data['currency']=="USD"){echo "KeyBank";}else{echo "RBC";}?></div>
<div class="divTableCell"<?if($data['currency']=="USD"){echo 'style="background-color:green; color: white;"';}?>><?if($data['currency']=="USD")echo "$ ".number_format(-$data['gross'],2);?></div>
<div class="divTableCell"<?if($data['currency']=="CAD"){echo 'style="background-color:green; color: white;"';}?>><?if($data['currency']=="CAD"){echo "$ ".number_format(-$data['gross'],2);}?></div>
</div>
<?
if($data['currency']=="USD")$sales=$sales+$data['gross'];
if($data['currency']=="CAD")$salescad=$salescad+($data['gross']);
$fee=$fee+($data['fee']);
$taxes=$taxes+($data['sales_taxes']*$rate);

if($data['state_province']=="QC")$txqc=$txqc+($data['sales_taxes']*$rate);
if($data['state_province']=="ON")$txon=$txon+($data['sales_taxes']*$rate);
if($data['state_province']=="BC")$txbc=$txbc+($data['sales_taxes']*$rate);
if($data['state_province']=="AB")$txab=$txab+($data['sales_taxes']*$rate);
if($data['state_province']=="SK")$txsk=$txsk+($data['sales_taxes']*$rate);
if($data['state_province']=="MB")$txmb=$txmb+($data['sales_taxes']*$rate);
if($data['state_province']=="NS")$txns=$txns+($data['sales_taxes']*$rate);
if($data['state_province']=="NB")$txnb=$txnb+($data['sales_taxes']*$rate);
if($data['state_province']=="NL")$txnl=$txnl+($data['sales_taxes']*$rate);
if($data['state_province']=="YK")$txyk=$txyk+($data['sales_taxes']*$rate);
if($data['state_province']=="PEI")$txpei=$txpei+($data['sales_taxes']*$rate);
if($data['state_province']=="NWT")$txnwt=$txnwt+($data['sales_taxes']*$rate);
}?>
<div class="divTableRow">
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"></div>
<div class="divTableCell"><?echo "$ ".number_format(-$sales,2);?> USD</div>
<div class="divTableCell"><?echo "$ ".number_format(-$salescad,2);?> CAD</div>


</div>
</div>

</div>
</div>
</div>
<div class="blueTable outerTableFooter">
<div class="tableFootStyle">
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
mysqli_close($db); ?>


