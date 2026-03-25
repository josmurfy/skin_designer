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
if(!isset($yearnow)){
	$yearnow=2021;
}
if(isset($_GET['tout'])){
	$_POST['tout']=$_GET['tout'];
}elseif(isset($_POST['tout'])){
	$_POST['tout']=$_POST['tout'];
}else{
	$_POST['tout']="";
}
$sql2 = 'SELECT * FROM `admin_currency_conversion` WHERE currency = "CAD" AND rate!=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
			?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css?version=1" rel="stylesheet">

</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="summary_total.php" method="post" enctype="multipart/form-data">
<div class="form_description">
<?/* <h1>Sommaire par année</h1>
                  <select name="year" id="input-year" class="form-control">
                    <option value="2018" <?php if ($i == 2018) { ?>selected="selected"<?}?>>2018</option>
                    <option value="2019" <?php if ($i == 2019) { ?>selected="selected"<?}?>>2019</option>
                    <option value="2020" <?php if ($i == 2020) { ?>selected="selected"<?}?>>2020</option>
                    <option value="2021" <?php if ($i == 2021) { ?>selected="selected"<?}?>>2021</option> 
                 </select> */?>
<?
$yearnow=11;
$html="";
$array = array(
   
);
if(!isset($_POST['year'])){
    $yearnow=date("Y");
 }else{
   $yearnow=$_POST['year'];
 }?>
<br><br>Services de transport<br><br>

<select name="year" id="year" class="form-control">
				  <?
				  $yearnowselect=date("Y");

				  for ($i=2018;$i<$yearnowselect+1;$i++){
					 
					?>
					
					<option value="<?echo $i;?>" selected><?echo $i;?></option>
					
				 <?}?>
                 </select>	
                 <input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />

                 <br><br><?echo $yearnow;?>
                 <?
  $array ['Novembre '.($yearnow-1)][]= get_ebay_sum_by_month($yearnow-1,$yearnow-1,11,11,'ebay_sales','n7f9655_phoenixliquidation','Vente Ebay PhoenixLiquidation');
 $array ['Novembre '.($yearnow-1)][]= get_ebay_sum_by_month($yearnow-1,$yearnow-1,11,11,'ebay_refunds','n7f9655_phoenixliquidation','Refund Ebay PhoenixLiquidation');
 $array ['Novembre '.($yearnow-1)][]= get_ebay_sum_by_month($yearnow-1,$yearnow-1,11,11,'ebay_sales','n7f9655_phoenixsupplies','Vente Ebay PhoenixSupplies');
 $array ['Novembre '.($yearnow-1)][]= get_ebay_sum_by_month($yearnow-1,$yearnow-1,11,11,'ebay_refunds','n7f9655_phoenixsupplies','Refund Ebay PhoenixSupplies');
 $array ['Novembre '.($yearnow-1)][]= get_sum_by_month($yearnow-1,$yearnow-1,11,11,'canuship_sales','n7f9655_phoenixsupplies','CanUShip Paypal');
 $array ['Novembre '.($yearnow-1)][]= get_website_transaction_by_month($yearnow-1,$yearnow-1,11,11,'n7f9655_canuship','CanUShip.com');
 $array ['Novembre '.($yearnow-1)][]= get_website_transaction_by_month($yearnow-1,$yearnow-1,11,11,'n7f9655_phoenixliquidation','PhoenixLiquidation.ca');
 $array ['Novembre '.($yearnow-1)][]= get_sum_by_month($yearnow-1,$yearnow-1,11,11,'website_refunds','n7f9655_phoenixliquidation','Website Refunds PL');
 $array ['Novembre '.($yearnow-1)][]= get_website_transaction_by_month($yearnow-1,$yearnow-1,11,11,'n7f9655_phoenixsupplies','PhoenixSupplies.ca');
 $array ['Novembre '.($yearnow-1)][]= get_sum_by_month($yearnow-1,$yearnow-1,11,11,'website_refunds','n7f9655_phoenixsupplies','Website Refunds PS');


 $array ['Decembre '.($yearnow-1)][]= get_ebay_sum_by_month($yearnow-1,$yearnow-1,12,12,'ebay_sales','n7f9655_phoenixliquidation','Vente Ebay PhoenixLiquidation');
 $array ['Decembre '.($yearnow-1)][]= get_ebay_sum_by_month($yearnow-1,$yearnow-1,12,12,'ebay_refunds','n7f9655_phoenixliquidation','Refund Ebay PhoenixLiquidation');
 $array ['Decembre '.($yearnow-1)][]= get_ebay_sum_by_month($yearnow-1,$yearnow-1,12,12,'ebay_sales','n7f9655_phoenixsupplies','Vente Ebay PhoenixSupplies');
 $array ['Decembre '.($yearnow-1)][]= get_ebay_sum_by_month($yearnow-1,$yearnow-1,12,12,'ebay_refunds','n7f9655_phoenixsupplies','Refund Ebay PhoenixSupplies');
 $array ['Decembre '.($yearnow-1)][]= get_sum_by_month($yearnow-1,$yearnow-1,12,12,'canuship_sales','n7f9655_phoenixsupplies','CanUShip Paypal');
 $array ['Decembre '.($yearnow-1)][]= get_website_transaction_by_month($yearnow-1,$yearnow-1,12,12,'n7f9655_canuship','CanUShip.com');
 $array ['Decembre '.($yearnow-1)][]= get_website_transaction_by_month($yearnow-1,$yearnow-1,12,12,'n7f9655_phoenixliquidation','PhoenixLiquidation.ca');
 $array ['Decembre '.($yearnow-1)][]= get_sum_by_month($yearnow-1,$yearnow-1,12,12,'website_refunds','n7f9655_phoenixliquidation','Website Refunds PL');
 $array ['Decembre '.($yearnow-1)][]= get_website_transaction_by_month($yearnow-1,$yearnow-1,12,12,'n7f9655_phoenixsupplies','PhoenixSupplies.ca');
 $array ['Decembre '.($yearnow-1)][]= get_sum_by_month($yearnow-1,$yearnow-1,12,12,'website_refunds','n7f9655_phoenixsupplies','Website Refunds PS');
 
 $sql2 = 'SELECT * FROM `admin_month` limit 10';
 $req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
//    $array [$data2['name'].' '.$yearnow]= get_sum_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],"canuship_sales",'n7f9655_phoenixsupplies','CanUShip');
 $array [$data2['name'].' '.$yearnow][]= get_ebay_sum_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'ebay_sales','n7f9655_phoenixliquidation','Vente Ebay PhoenixLiquidation');
$array [$data2['name'].' '.$yearnow][]= get_ebay_sum_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'ebay_refunds','n7f9655_phoenixliquidation','Refund Ebay PhoenixLiquidation');
$array [$data2['name'].' '.$yearnow][]= get_ebay_sum_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'ebay_sales','n7f9655_phoenixsupplies','Vente Ebay PhoenixSupplies');
$array [$data2['name'].' '.$yearnow][]= get_ebay_sum_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'ebay_refunds','n7f9655_phoenixsupplies','Refund Ebay PhoenixSupplies');
$array [$data2['name'].' '.$yearnow][]= get_sum_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'canuship_sales','n7f9655_phoenixsupplies','CanUShip Paypal');
 $array [$data2['name'].' '.$yearnow][]= get_website_transaction_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'n7f9655_canuship','CanUShip.com');
 $array [$data2['name'].' '.$yearnow][]= get_website_transaction_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'n7f9655_phoenixliquidation','PhoenixLiquidation.ca');
 $array [$data2['name'].' '.$yearnow][]= get_sum_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'website_refunds','n7f9655_phoenixliquidation','Website Refunds PL');
$array [$data2['name'].' '.$yearnow][]= get_website_transaction_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'n7f9655_phoenixsupplies','PhoenixSupplies.ca');
$array [$data2['name'].' '.$yearnow][]= get_sum_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'website_refunds','n7f9655_phoenixsupplies','Website Refunds PS');

}
//mysqli_close($db); 
//print("<pre>".print_r ($array,true )."</pre>");

$html='
<div class="divTable blueTable">
	<div class="divTableHeading">
		<div class="divTableRow">';
        $html.='			
        <div class="divTableHead">DONNEE</div>';
        $skip="";
foreach($array as $key=>$value){
    $i=0;
 //dans la loop        
 //print("<pre>".print_r ($value,true )."</pre>");
 //$year = current($value);
 //echo key($value);
 //print_r(array_keys($value[''], 1));
        $html.='			
            <div class="divTableHead">'.$key.'</div>';
        foreach($value as $val){
            foreach ($val as $keydata=>$data){
    //            echo $keydata."<br>";
      //print("<pre>".print_r ($data,true )."</pre>");
                    $data_row["ROW_".$i][0]=$keydata;
            
                if($keydata=='TITLE'){

                    $data_row["ROW_".$i][0]=$data;
                    $i++;
                }
                if($keydata!='TITLE'){

                    $data_row["ROW_".$i][]=$data;
                    $i++;
                }
            }
        }
        $i=0;
        $skip="";


}
//print("<pre>".print_r ($data_row,true )."</pre>");
$html.='
 </div>
	</div>  <div class="divTableBody">';
    foreach($data_row as $data){
        $total=0;
        $html.='

        <div class="divTableRow"';
        if(count($data)==1)
            $html.=' style="background-color:blue; color: white;"';
        $html.='>';
        foreach($data as $data2){
           
           

            $html.='<div class="divTableCell"';
            if($data2>0){
                $html.=' style="background-color:green; color: white;"';
            }elseif($data2<0){
                $html.=' style="background-color:red; color: white;"';
            }elseif($data2==0 && is_numeric($data2)){
                $html.=' style="background-color:lightgray; color: white;"';
            }
            $html.='>';
            if(is_numeric($data2) && $data2<>0){
                $total+=$data2;
                $data2='$'.number_format($data2,2);
                $html.=$data2;
            }elseif(!is_numeric($data2)){
                $html.=$data2;
            }
           
            $html.=' </div>';
       
        }
        if(is_numeric($total) && $total<>0){
            $html.='<div class="divTableCell">'. '$'.number_format($total,2).' </div>';
        }else{
            $html.='<div class="divTableCell"></div>';
        }
        $html.=' </div>';
    }
	//echo $result;
    //print("<pre>".print_r ($result,true )."</pre>");
    $html.='</div>
    </div>
</div>'
;

echo $html."<br><br><br>";
  ?>
<?
$html="";
unset($array);
//$array ['Novembre '.($yearnow-1)][]= get_bank_transaction_by_month($yearnow-1,$yearnow-1,11,11,'n7f9655_phoenixliquidation','Transaction Bancaire PhoenixLiquidation');
$html.='
Transaction Bancaire PhoenixLiquidation<br><br>';


$arrays ['Novembre '.($yearnow-1)]= get_bank_transaction_by_month($yearnow-1,$yearnow-1,11,11,'n7f9655_phoenixliquidation');
$arrays ['Decembre '.($yearnow-1)]= get_bank_transaction_by_month($yearnow-1,$yearnow-1,12,12,'n7f9655_phoenixliquidation');
$sql2 = 'SELECT * FROM `admin_month` limit 10';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
//unset($arrays);




$arrays [$data2['name'].' '.$yearnow]= get_bank_transaction_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'n7f9655_phoenixliquidation','Transaction Bancaire PhoenixLiquidation');
}
//print("<pre>".print_r ($arrays,true )."</pre>");

//print("<pre>".print_r ($arrays,true )."</pre>");
$html.='
<div class="divTable blueTable">';
foreach ($arrays as $key=>$value){

    $html.='

	<div class="divTableHeading">
		<div class="divTableRow">';
        $html.='			
            <div class="divTableHead">'.$key.'
            </div>
            <div class="divTableHead">Date</div>
            <div class="divTableHead">Produit</div>
            <div class="divTableHead">Compagnie</div>
            <div class="divTableHead">Type</div>
            <div class="divTableHead">Source</div>
            <div class="divTableHead">USD</div>
            <div class="divTableHead">CAD</div>
            <div class="divTableHead">Taxes</div>

        </div>
    </div>
    <div class="divTableBody">
       ';
foreach ($value as $key2=>$val){
  // 
    $html.='<div class="divTableRow">
                <div class="divTableCell" ';
    
        $html.=' style="background-color:blue; color: white;">
        ';
       
    $html.= $key2;
   //print("<pre>".print_r ($val,true )."</pre>");
    $html.='    
    </div>
    </div>';
    
   
    if(count($val)>0){
        
        $html.='
       ';
       
    //echo "allo***";
    
        foreach ($val as $key3=>$vl){
            $html.='
        
            <div class="divTableRow">
            ';

           
            $html.='
           
                <div class="divTableCell">
                </div>
                ';
            foreach ($vl as $v){
                $v=str_replace(",","",$v);
        //print("<pre>".print_r ($vl,true )."</pre>");
                $html.='   
                 <div class="divTableCell" ';
                 if($v>0 && is_numeric($v)){
                    $html.=' style="background-color:green; color: white;"';
                }elseif($v<0 && is_numeric($v)){
                    $html.=' style="background-color:red; color: white;"';
                }elseif($v==0 && is_numeric($v)){
                    $html.=' style="background-color:lightgray; color: white;"';
                }
                $html.='>
                ';
                if(is_numeric($v) && $v<>0){
                    $html.="$".number_format($v,2);
                }elseif(!is_numeric($v)){
                    $html.= substr($v,0,40);
                }

                $html.='    
                </div>';
            }
            $html.='
            </div>';
            
        }
        $html.='
        ';
        $html.='
       ';
    }else{
       // $html.='</div>';
       
    }

$html.='

   
 
 
';
    }
    $html.='
</div>

';
}
$html.='

</div>
';





echo $html;
$html="";
unset($array);
//$array ['Novembre '.($yearnow-1)][]= get_bank_transaction_by_month($yearnow-1,$yearnow-1,11,11,'n7f9655_phoenixliquidation','Transaction Bancaire PhoenixLiquidation');
$html.='<br><br>
Transaction Bancaire PhoenixSupplies<br><br>';


$arrays ['Novembre '.($yearnow-1)]= get_bank_transaction_by_month($yearnow-1,$yearnow-1,11,11,'n7f9655_phoenixsupplies');
$arrays ['Decembre '.($yearnow-1)]= get_bank_transaction_by_month($yearnow-1,$yearnow-1,12,12,'n7f9655_phoenixsupplies');
$sql2 = 'SELECT * FROM `admin_month` limit 10';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
//unset($arrays);




$arrays [$data2['name'].' '.$yearnow]= get_bank_transaction_by_month($yearnow,$yearnow,$data2['id'],$data2['id'],'n7f9655_phoenixsupplies','Transaction Bancaire PhoenixLiquidation');
}
//print("<pre>".print_r ($arrays,true )."</pre>");

//print("<pre>".print_r ($arrays,true )."</pre>");
$html.='
<div class="divTable blueTable">';
foreach ($arrays as $key=>$value){

    $html.='

	<div class="divTableHeading">
		<div class="divTableRow">';
        $html.='			
            <div class="divTableHead">'.$key.'
            </div>
            <div class="divTableHead">Date</div>
            <div class="divTableHead">Produit</div>
            <div class="divTableHead">Compagnie</div>
            <div class="divTableHead">Type</div>
            <div class="divTableHead">Source</div>
            <div class="divTableHead">USD</div>
            <div class="divTableHead">CAD</div>
            <div class="divTableHead">Taxes</div>

        </div>
    </div>
    <div class="divTableBody">
       ';
foreach ($value as $key2=>$val){
  // 
    $html.='<div class="divTableRow">
                <div class="divTableCell" ';
    
        $html.=' style="background-color:blue; color: white;">
        ';
       
    $html.= $key2;
   //print("<pre>".print_r ($val,true )."</pre>");
    $html.='    
    </div>
    </div>';
    
   
    if(count($val)>0){
        
        $html.='
       ';
       
    //echo "allo***";
    
        foreach ($val as $key3=>$vl){
            $html.='
        
            <div class="divTableRow">
            ';

           
            $html.='
           
                <div class="divTableCell">
                </div>
                ';
            foreach ($vl as $v){
                $v=str_replace(",","",$v);
        //print("<pre>".print_r ($vl,true )."</pre>");
                $html.='   
                 <div class="divTableCell" ';
                if($v>0 && is_numeric($v)){
                    $html.=' style="background-color:green; color: white;"';
                }elseif($v<0 && is_numeric($v)){
                    $html.=' style="background-color:red; color: white;"';
                }elseif($v==0 && is_numeric($v)){
                    $html.=' style="background-color:lightgray; color: white;"';
                }
                $html.='>
                ';
                if(is_numeric($v) && $v<>0){
                    $html.="$".number_format($v,2);
                }elseif(!is_numeric($v)){
                    $html.= substr($v,0,40);
                }

                $html.='    
                </div>';
            }
            $html.='
            </div>';
            
        }
        $html.='
        ';
        $html.='
       ';
    }else{
       // $html.='</div>';
       
    }

$html.='

   
 
 
';
    }
    $html.='
</div>

';
}
$html.='

</div>
';





echo $html;
?>



</body>
</html>
<?  // on ferme la connexion à mysql 
?>


