<?
include 'connection.php';
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
include $GLOBALS['SITE_ROOT'].'interne/translatenew.php';
require_once '/home/n7f9655/public_html/canuship/vendor/autoload.php';
//print("<pre>".print_r ($_POST,true )."</pre>");
//echo $_POST['name']['24415'];
//print("<pre>".print_r ($_POST['name']['24415'],true )."</pre>");
$testnb=0;
$totalsale=0;
if(isset($_POST['traiter'])){
	foreach($_POST['product_id'] as $product){
        $data=explode(',',$product); 
		$product_id=$data[0];
        $sql2 = "UPDATE `oc_product` SET `verif_fait`='3'";
            $sql2 .=" WHERE product_id='".$product_id."' and ebay_id>0";
         //  echo $sql2."<br>";
           $req2 = mysqli_query($db,$sql2);
		   $sql2 = "UPDATE `oc_algopix` SET `verif_fait`='3'";
		   $sql2 .=" WHERE product_id='".$product_id."'";
		//  echo $sql2."<br>";
		  $req2 = mysqli_query($db,$sql2);
	}
}
if(isset($_POST['update'])){
	foreach($_POST['product_id'] as $product){
        $data=explode(',',$product); 
		$product_id=$data[0];
        $sql2 = "UPDATE `oc_product_description` SET `color`='".strtoupper( $_POST['coloren'][$product_id] )."'";
            $sql2 .=", `name` = '".strtoupper( $_POST['nameen'][$product_id] )."', `condition_supp` ='". $_POST['condition_suppen'][$product_id]."'";
            $sql2 .=" WHERE language_id=1 and product_id='".$product_id."'";
         //   echo $sql2."<br>";
            $req2 = mysqli_query($db,$sql2);
           $sql2 = "UPDATE `oc_product_description` SET `color`='".strtoupper( addslashes($_POST['colorfr'][$product_id] ))."'";
           $sql2 .=", `name` = '".strtoupper( addslashes($_POST['namefr'][$product_id]) )."', `condition_supp` ='". addslashes($_POST['condition_suppfr'][$product_id])."'";
           $sql2 .=" WHERE language_id=2 and product_id='".$product_id."'";
          // echo $sql2."<br>";
            $req2 = mysqli_query($db,$sql2);
            if($_POST['marketplace_item_id'][$product_id]>0){
                $ebay_id=", `ebay_id_old` = '". $_POST['marketplace_item_id'][$product_id] ."' ";
                $_POST['ebay_id_old']="oui";
            }elseif($_POST['ebay_id_hidden'][$product_id]>0){
                $ebay_id=", `ebay_id_old` = '". $_POST['ebay_id_hidden'][$product_id] ."' ";
                unset($_POST['ebay_id_old']);
            }else{
                unset($_POST['ebay_id_old']);
                $ebay_id="";
            }
            $sql2 = "UPDATE `oc_product` SET `model`='".strtoupper( $_POST['model'][$product_id] )."'";
            $sql2 .=",`verif_fait`=2,`manufacturer_id` = '0". $_POST['manufacturer_id'.$product_id]."'";
            $sql2 .=$ebay_id.", `weight`='". $_POST['weight'][$product_id] ."',`height`='". $_POST['height'][$product_id] ."'";
            $sql2 .=", `width`='". $_POST['width'][$product_id] ."',`length`='". $_POST['length'][$product_id]."'" ;
            $sql2 .=",`price`=".( $_POST['price_with_shipping'][$product_id]-3.49).",`price_with_shipping`=".( $_POST['price_with_shipping'][$product_id]);
            $sql2 .=" ,ebay_last_check='2020-09-01' WHERE product_id='".$product_id."'";
          echo $sql2."<br>";
            $req2 = mysqli_query($db,$sql2);
		    mise_en_page_description($connectionapi,$product_id,$db);
    }
}
if(isset($_POST['list'])){
	foreach($_POST['product_id'] as $product){
        $data=explode(',',$product); 
		$product_id=$data[0];
       if($_POST['marketplace_item_id'][$product_id]==""){
//echo "allo";
        $post=array(
            'model' =>$_POST['model'][$product_id] ,
            'condition_id' =>$_POST['condition_id'][$product_id] ,
            'brand' =>$_POST['brand'][$product_id] ,
            'manufacturer_id' =>$_POST['manufacturer_id'.$product_id],
            'weight' =>$_POST['weight'][$product_id],
            'height' =>$_POST['height'][$product_id],
            'width' =>$_POST['width'][$product_id] ,
            'length' =>$_POST['length'][$product_id],
            'price' =>$_POST['price'][$product_id],
            'price_with_shipping' =>$_POST['price_with_shipping'][$product_id],
            'coloren' =>$_POST['coloren'][$product_id],
            'nameen' =>$_POST['nameen'][$product_id]
        );
        $sql4 = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$product_id.'" and ebayyes=1';
			//echo $sql4;
			$req4 = mysqli_query($db,$sql4);
			$data4 = mysqli_fetch_assoc($req4);
			$post['categoryname']=$data4['name'];
			$post['category_id']=$data4['category_id'];
			$post['category_id']=$data4['category_id'];
           //print("<pre>".print_r ($post,true )."</pre>");
        $ebayresult=get_ebay_product($connectionapi,$_POST['ebay_id_hidden'][$product_id]);
        $json=add_ebay_item($connectionapi,$ebayresult,$post,$db); 
       //print("<pre>".print_r ($json,true )."</pre>");
            if($json['ItemID']>0){
                $_POST['marketplace_item_id'][$product_id]=$json['ItemID'];
            $sql2 = "UPDATE `oc_product` SET error_ebay='',`ebay_id`='".$json['ItemID']."'";
            $sql2 .=" WHERE product_id='".$product_id."' ";
         //  echo $sql2."<br>";
           $req2 = mysqli_query($db,$sql2);
           $result=revise_ebay_product($connectionapi,$json['ItemID'],$product_id,$_POST['quantity'][$product_id],$db,"oui");
         //print("<pre>".print_r ($result,true )."</pre>");
            }else{
				$error_message="";
				foreach($json['Errors'] as $error){
					if($error['SeverityCode'] =='Error'){
						foreach($error['ErrorParameters'] as $ErrorParameters){
							$error_message.=$error['SeverityCode'].": ".$ErrorParameters['Value']."<br>";
						}
					}
				}
				$sql2 = "UPDATE `oc_product` SET `error_ebay`='".addslashes($error_message)."'";
            	$sql2 .=" WHERE product_id='".$product_id."' ";
				//echo $sql2."<br>";
				$req2 = mysqli_query($db,$sql2);
			}
        }
    }
}
if(isset($_GET['action']) && $_GET['action']=="list"){ 
//	echo "list";
	$sql4 = 'select *, p.upc,p.product_id,p.quantity,p.sku,
		p.condition_id,pd.condition_supp,p.remarque_interne
		from oc_product p
		left join  oc_product_description pd on (p.product_id=pd.product_id) 
		left join oc_product_to_category pc on (pc.product_id=p.product_id )
		where  pc.category_id=177666 and pd.language_id=1 and p.quantity>0 and p.ebay_id=0 order by p.product_id limit 25'; 
echo $sql4."<br>";
//print("<pre>".print_r ($_POST['ebay_id_hidden'],true )."</pre>"); 

//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>";
$req4 = mysqli_query($db,$sql4);
while($product = mysqli_fetch_assoc($req4)){
		$testnb++;
		$product_id=$product['product_id'];
        if($product['condition_id']==9){
            $variant=1;
            $product['etat']="9,";
        }elseif($product['condition_id']==99){
            $variant=.9;
            $product['etat']="99,NO";
        }elseif($product['condition_id']==8){
            $variant=.9;
            $product['etat']="8,LN";
        }elseif($product['condition_id']==7){
            $variant=.8;
            $product['etat']="7,VG";
        }elseif($product['condition_id']==6){
            $variant=.75;
            $product['etat']="6,G";
        }elseif($product['condition_id']==5){
            $variant=.65;
            $product['etat']="5,P";
        }elseif($product['condition_id']==1){
            $variant=.5;
            $product['etat']="1,FP";
        }elseif($product['condition_id']==2){
            $variant=.85;
            $product['etat']="2,SR";
        }elseif($product['condition_id']==22){
            $variant=.85;
            $product['etat']="22,R";
        }
        $product['price_with_shipping']=($product['price']*$variant)-.05;	
        $product['price']=$product['price_with_shipping'];
		if($product['condition_id']!=9){
			$product['condition_supp']='New NEVER USED,No retail box';
        }else{
            $product['condition_supp']="";
        }
        if($product['marketplace_item_id']>0){
			$ebay_id=", `ebay_id_old` = '". $product['marketplace_item_id'] ."' ";
		}else{
			$ebay_id="";
		}
        //if(($product['price_with_shipping']-3.49)>0){
            $sql2 = "UPDATE `oc_product` SET `verif_fait`=2";
            $sql2 .=" ,ebay_last_check='2020-09-01'  WHERE product_id='".$product_id."'";
        //  echo $sql2."<br>";
            $req2 = mysqli_query($db,$sql2);

        // $_POST=translate_field($_POST);
       //     link_to_download($connectionapi,$product_id,$product['image'],"",$db);
		//    mise_en_page_description($connectionapi,$product_id,$db);
      //  }
	}
}
if(isset($_POST['product_id']) && isset($_POST['ebay']) && $_POST['ebay']!=""){
//	echo "<br>ebay:".$_POST['ebay'];
	foreach($_POST['product_id'] as $product){
    //   echo "<br>loop";
		$testnb++;
		$data=explode(',',$product); 
		$upc=$data[1];
		$product_id=$data[0];
       //print("<pre>".print_r ($_POST['ebay_id_hidden'],true )."</pre>"); 
       // echo $product_id;
        //echo $_POST['ebay_id_hidden'][$product_id];
		if($_POST['ebay_id_hidden'][$product_id]>0){
           // echo "ebay";
         //print("<pre>".print_r ($_POST,true )."</pre>"); 
			$result=get_ebay_product($connectionapi,$_POST['ebay_id_hidden'][$product_id]);
			$json = json_decode($result, true);
            //print("<pre>".print_r ($json,true )."</pre>"); 
		//	echo $_POST['condition_id'][$product_id];
        if(!isset($json['Item']['ProductListingDetails']['UPC'])){
           // echo "pas de upc:".$product_id;
            $upcresearch=$upc;
        }elseif($json['Item']['ProductListingDetails']['UPC']<>$upc){
            $upcresearch=$json['Item']['ProductListingDetails']['UPC'];
          //  echo $json['Item']['ProductListingDetails']['UPC'];
        }else{
            $upcresearch=$upc;
        }
        //echo $upc."allo";
			$result_ebay=find_bestprice_ebay($connectionapi,$_POST['nameen'][$product_id],$upcresearch,1,1,'',$_POST['ebay_id_hidden'][$product_id]);
//echo $connectionapi,$_POST['name'][$product_id];
//echo $upc;
			//print("<pre>".print_r ($result_ebay,true )."</pre>"); 
			$pricevariant=json_encode($result_ebay['pricevariant']);
			if($_POST['brand_hidden'][$product_id]==""){
				foreach($json['Item']['ItemSpecifics']['NameValueList'] as $itemspecific){
					if($itemspecific['Name']=='Studio'){
						$_POST['brand'][$product_id]= $itemspecific['Value'];
						$_POST['brand_hidden'][$product_id]=$itemspecific['Value'];
					}
				}
			}
			if($_POST['brand'][$product_id]==""){
				$_POST['brand'][$product_id]=$result_ebay['brand'];
			}
			if(!isset($json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']))
			$json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']=$json['Item']['ShippingDetails']['ShippingServiceOptions'][0]['ShippingServiceCost'];
			//echo "SurEBAY".($json['Item']['StartPrice']+$json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']);
			//	echo "Algopix".$_POST['algopix_price_hidden'][$product_id];
			if(($json['Item']['ListingDetails']['ConvertedStartPrice']
			+$json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost'])
			>$_POST['algopix_price_hidden'][$product_id]){
				//echo "SurEBAY".($json['Item']['ListingDetails']['ConvertedStartPrice']+$json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']);
				//echo "Algopix".$_POST['algopix_price_hidden'][$product_id];
				$_POST['price'][$product_id]=$json['Item']['ListingDetails']['ConvertedStartPrice']+$json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']-$_POST['algopix_price_hidden'][$product_id];
			}
			if((strlen($result_ebay['name']) > strlen($_POST['name'][$product_id])) && isset($_POST['majname'][$product_id])){
				$_POST['name'][$product_id]=$result_ebay['name'];
			}
			$_POST['ebayname'][$product_id]=$result_ebay['name'];
		}
		//echo $_POST['manufacturersupp'][$product_id];
		if(isset($_POST['manufacturersupp'][$product_id])&& $_POST['manufacturersupp'][$product_id]!=""){
			$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.strtoupper($_POST['manufacturersupp'][$product_id]).'")';
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
			$_POST['manufacturer_id'.$product_id]= mysqli_insert_id($db);
			$_POST['brand'][$product_id]=$_POST['manufacturersupp'][$product_id];
		}elseif(isset($_POST['manufacturer_id'.$product_id]) && $_POST['manufacturer_id'.$product_id]!=""){
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'.$product_id];
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$_POST['brand'][$product_id]=$data2['name'];
		}elseif(isset($_POST['manufacturer_recom'][$product_id]) ){
			$_POST['manufacturer_id'.$product_id]=$_POST['manufacturer_recom'][$product_id];
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'.$product_id];
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$_POST['brand'][$product_id]=$data2['name'];
		}
		if($_POST['marketplace_item_id'][$product_id]!=""){
			$ebay_id=", `ebay_id` = '".$_POST['marketplace_item_id'][$product_id]."' ";
		}else{
			$ebay_id="";
		}
		/*$sql2 = "UPDATE `oc_algopix` SET `model`='".strtoupper($_POST['model'][$product_id])."',`color`='".strtoupper($_POST['color'][$product_id])."'";
		$sql2 .=", `name` = '".strtoupper($_POST['name'][$product_id])."',`manufacturer_id` = '0".$_POST['manufacturer_id'.$product_id]."',
		`image`='".$_POST['image'][$product_id]."',`brand` = '".$_POST['brand'][$product_id]."'";
		$sql2 .=$ebay_id.",`upc`='".$upc."', `weight`='".$_POST['weight'][$product_id]."',`height`='".$_POST['height'][$product_id]."'";
		$sql2 .=",pricevariant='".$pricevariant."', `width`='".$_POST['width'][$product_id]."',`length`='".$_POST['length'][$product_id];
		$sql2 .="',date_update=now() WHERE product_id='".$product_id."' or upc='".$upc."'";
		//echo $sql2."<br>";
		$req2 = mysqli_query($db,$sql2);*/
	}
}
echo "nb:".$testnb;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
?>
<html> 
<head>
<?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title>
			<script type="text/javascript" src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/bootstrap/js/bootstrap.min.js"></script>
	<link href="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/moment.js" type="text/javascript"></script>
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
	<link href="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/common.js" type="text/javascript"></script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
 <link href="stylesheet.css" rel="stylesheet">
  <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
</head>
<body bgcolor="<?if($resultebay){?>red<?}else{?>ffffff<?}?>">
<form action="bulklistingnew.php" method="post"  enctype="multipart/form-data">
  <table style="text-align: left; width: 1000px; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <th colspan="3" rowspan="1" style="vertical-align:  middle; ">
			<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>/image/catalog/cie/entetelow.jpg">
		</th>
      </tr>
      <tr>
        <th style="vertical-align: middle; background-color: #e4bc03;  text-align: center;width: 200px">
		<a href="listing.php" >Annuler</a> 		
        </th>
        <th colspan="3" style="vertical-align: middle;height: 50; background-color: #030282; color: white;text-align: center;">
		<h1>Bulk Listing</h1>
		</th>
     </tr>
	 	  <tr>
	    <th colspan="3" style="vertical-align:  middle; height: 50px; background-color: #e4bc03; width: 200px;text-align:right"> 	
       <input id="saveForm" class="button_text" type="submit" name="update" value="Update"  onclick="selectAll()"/>
       <input id="saveForm" class="button_text" type="submit" name="list" value="List on eBay" />
       <input id="saveForm" class="button_text" type="submit" name="traiter" value="Traiter" />
       <br> 		<input id="saveForm" class="button_text" type="submit" name="ebay" value="eBay" />
		</th>
	  </tr>
</table>
<table style="text-align: left; width: 100%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
		<tr>
        <th bgcolor="030282">
	<input type="button" onclick='selectAll()' value="Select All"/><br>
	<input type="button" onclick='UnSelectAll()' value="Unselect All"/>
	</th>
		</tr>
        <?
$sqlproduct = 'select p.error_ebay,pc.category_id,p.product_id,p.upc,p.sku,p.price,p.price_with_shipping,p.quantity,p.condition_id,p.model,p.ebay_id_old,p.ebay_id,p.image image,pd.name nameen,pd2.name namefr, 
        pd.condition_supp condition_suppen,pd2.condition_supp condition_suppfr,
        pd.color coloren,pd2.color colorfr, m.name brand,m.manufacturer_id 
		from oc_product_description pd  
		left join oc_product p on (p.product_id=pd.product_id) 
        left join oc_manufacturer m on (p.manufacturer_id=m.manufacturer_id) 
        left join oc_product_description pd2 on (pd.product_id=pd2.product_id and pd2.language_id=2) 
		left join oc_product_to_category pc on (pc.product_id=p.product_id )
		where pc.category_id=177666 and p.`verif_fait`=2 and pd.language_id=1 and p.quantity>0 order by p.product_id  limit 25'; 
//echo $sqlproduct."<br>";
//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>"; 
$reqproduct = mysqli_query($db,$sqlproduct);
while($product = mysqli_fetch_assoc($reqproduct)){
	//print("<pre>".print_r ($product,true )."</pre>");
    //print("<pre>".print_r ($product,true )."</pre>");
    if($product['condition_id']==9){
        $variant=1;
        $product['etat']="9,";
    }elseif($product['condition_id']==99){
        $variant=.9;
        $product['etat']="99,NO";
    }elseif($product['condition_id']==8){
        $variant=.9;
        $product['etat']="8,LN";
    }elseif($product['condition_id']==7){
        $variant=.8;
        $product['etat']="7,VG";
    }elseif($product['condition_id']==6){
        $variant=.75;
        $product['etat']="6,G";
    }elseif($product['condition_id']==5){
        $variant=.65;
        $product['etat']="5,P";
    }elseif($product['condition_id']==1){
        $variant=.5;
        $product['etat']="1,FP";
    }elseif($product['condition_id']==2){
        $variant=.85;
        $product['etat']="2,SR";
    }elseif($product['condition_id']==22){
        $variant=.85;
        $product['etat']="22,R";
    }
        if ($bgcolor=="ffffff"){
					$bgcolor="c0c0c0";
				}else{
					$bgcolor="ffffff";
				}
    $script.="if(document.getElementById('name".$product['product_id']."fr').value==''){
        getTranslate(document.getElementById('name".$product['product_id']."en').value,'name".$product['product_id']."','fr')
    }
     if(document.getElementById('condition_supp".$product['product_id']."fr').value==''){
        getTranslate(document.getElementById('condition_supp".$product['product_id']."en').value,'condition_supp".$product['product_id']."','fr')
    }";
?>
<th style="vertical-align:  middle;  width: 200px;">
</th>
<th style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">
<label>SKU:</label> 
</th>
        <th style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; width: 200px;">
			<label>Title:</label>
		</th>
		<th style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; width: 200px;">
			<label>Quantity:</label>
		</th>
			</tr>
					<tr>
					<td id="champ1<?echo $product['product_id'];?>" bgcolor="<?echo $bgcolor;?>">
					<input type="checkbox" name="product_id[]" value="<?echo $product['product_id'].','.$product['upc'];?>" onclick="document.getElementById('champ1<?echo $product['product_id'];?>').style.backgroundColor='green';"/><br>
					<?echo $product['product_id'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<a href="https://phoenixliquidation.ca/interne/pretlister.php?product_id=<?echo $product['product_id'];?>&action=pretlister&new=0" target="google"><?echo $product['sku'];?></a> 	
					</td>
                    <td bgcolor="<?if($product['nameen']!="" && strlen($product['nameen'])<81){echo $bgcolor;}else{echo "red";}?>" id="champ4<?echo $product['product_id'];?>">
                    <?$dem= str_replace($product['upc'],' ',$product['sku']);?>
<?echo "Lenght:".strlen($product['nameen'])."<br>"?>
						<input type="text" id="name<?echo $product['product_id'];?>en" name="nameen[<?echo $product['product_id'];?>]" value="<?echo $product['nameen']?>" size="80" onchange="getTranslate(document.getElementById('name<?echo $product['product_id'];?>en').value,'name<?echo $product['product_id'];?>','fr')" onclick="document.getElementById('champ4<?echo $product['product_id'];?>').style.backgroundColor='green';"/>
                        <input type="text" id="name<?echo $product['product_id'];?>fr" name="namefr[<?echo $product['product_id'];?>]" value="<?echo $product['namefr']?>" size="80" onclick="document.getElementById('champ4<?echo $product['product_id'];?>').style.backgroundColor='green';"/>
                    </td>
					<td bgcolor="<?echo $bgcolor;?>">
					<input type="hidden" name="quantity[<?echo $product['product_id'];?>]" value="<?echo $product['quantity'];?>" />
<?echo $product['quantity'];
$totalsale=$totalsale+(($product['quantity']*(($product['price_with_shipping']*.85)-3.49))*1.25);?>
</td>
			</tr>
			<tr>
			<td>
			</td>
<th style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
Price EBAY
</th>
<th style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
Price Website
</th>
<th style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
    Ebay clone: 
</th>
		</tr>
			<tr>
            <td bgcolor="<?if($product['image']!=""){echo $bgcolor;}else{echo "red";}?>" id="champ3<?echo $product['product_id'];?>">
					<?echo '<img height="100" src="https://phoenixliquidation.ca/image/'.$product['image'].'"/>';?>
					</td>
                    <? 
                        $productvariants=json_decode($product['pricevariant']);
                        $sql22 = 'SELECT conditions FROM `oc_conditions_to_category` CC LEFT JOIN `oc_conditions` AS C ON CC.conditions_id=C.conditions_id where CC.category_id= '.$product['category_id'];
	//echo "<br>".$sql2;
                        $req22 = mysqli_query($db,$sql22);
                        $data22 = mysqli_fetch_assoc($req22);
                        $conditions = json_decode($data22['conditions'], true);
                        //print("<pre>".print_r ($conditions,true )."</pre>");
                        $product['condition']=$conditions[$product['condition_id']]['value'];
                        $pricelowest=99999;
                        $variantlowest;
                        $pricecheck=0;
                        $varianthtml="";
                        foreach($productvariants as $key=>$value){
                            //echo $value->price;
                            //print("<pre>".print_r ( $value->price,true )."</pre>");
                               if($value->price<99999 ){
                                if($pricelowest>$value->price){
                                    $pricelowest=$value->price;
                                    $variantlowest=$key;
                                    if($variantlowest<>1000){
                                        $variant=1;
                                    }
                                   // $product['price_with_shipping']=($value->price-.05)*$variant;
                                    $product['price']=$product['price_with_shipping']-3.54;
                                }
                                    if($product['condition']==$key){
                                        $pricecheck=$value->price;
                                        $varianthtml.= "<strong>";
                                    }
                                    $varianthtml.= "[".$key."-$".number_format($value->price, 2,'.', '')."]";
                                    if($product['condition']==$key)
                                    $varianthtml.= "</strong>";
                                }                       
                        }
                        ?>
                    <td bgcolor="<?
					if((($product['price_with_shipping']-.05)>=$pricecheck && ($pricecheck>5.63))){
						echo $bgcolor;
					}elseif($pricecheck>5.64){
						if($product['price_with_shipping']<5.64){
							$product['price_with_shipping']=5.64;
							$product['price']=2.15;
							echo "red";
						}else{
							$text= "<br> Sugest price:".($pricecheck-.05);
						//$product['price']=$product['price_with_shipping']-3.49;
						echo "lightblue";
						}
					}elseif($product['price_with_shipping']<5.64){
						$product['price_with_shipping']=5.64;
						$product['price']=2.15;
						echo "red";
					}?>" id="champ7<?echo $product['product_id'];?>">
                        <?
//echo "Pricelow:".$pricelowest;
                        echo $varianthtml;
						echo $text;
						$text="";
                        ?>
                    <input id="price_with_shipping"  type="text" name="price_with_shipping[<?echo $product['product_id'];?>]" value="<?echo number_format($product['price_with_shipping'], 2,'.', '');?>" size="10" onclick="document.getElementById('champ7<?echo $product['product_id'];?>').style.backgroundColor='green';"/>
					</td>
                    <td bgcolor="<?echo $bgcolor;?>" id="champ6<?echo $product['product_id'];?>">
					<input type="hidden" name="price_hidden[<?echo $product['product_id'];?>]" value="<?echo $product['algopix_price'];?>" />
					<input id="price"  type="text" name="price[<?echo $product['product_id'];?>]" value="<?echo number_format($product['price'], 2,'.', '');?>" size="10" onclick="document.getElementById('champ6<?echo $product['product_id'];?>').style.backgroundColor='green';"/>
					</td>
					<td bgcolor="<?if($product['error_ebay']!=""){echo "red";}elseif($product['marketplace_item_id']>0){echo "green";}elseif($product['ebay_id_old']>0){echo $bgcolor;}else{echo "red";}?>" id="champ8<?echo $product['product_id'];?>">
					<?if($product['marketplace_item_id']>0){?>
                        <a href="https://bulksell.ebay.com/ws/eBayISAPI.dll?SingleList&sellingMode=ReviseItem&&lineID=<?echo $product['marketplace_item_id'];?>" target="ebayactive"><?echo $product['marketplace_item_id'];?></a> 	
                        <input type="hidden" name="ebay_id[<?echo $product['product_id'];?>]" value="<?echo $product['marketplace_item_id'];?>" />
                        <?}else{?>
							<?echo $product['error_ebay'];?>
                            <a href="https://www.ebay.com/itm/<?echo $product['ebay_id_old'];?>" target="ebayactive"><?echo $product['ebay_id_old'];?></a> 	
					<input type="hidden" name="ebay_id_hidden[<?echo $product['product_id'];?>]" value="<?echo $product['ebay_id_old'];?>" />
					<br><input type="text" name="ebay_id[<?echo $product['product_id'];?>]" value="" size="10" onclick="document.getElementById('champ8<?echo $product['product_id'];?>').style.backgroundColor='green';"/>
				<?}?>
                </td>
					</tr>
					<tr>
<td></td>
					<th style="vertical-align:  middle; height: 25px; background-color: #030282; color: white; width: 200px;">
<label>Model:</label>
</th>
<th style="vertical-align:  middle; background-color: #030282; color: white; width: 200px;">
<label>Brand:</label>         
</th>
<th style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">
<label>Color</label>
 </th>
			</tr>
				<tr>
				<td></td>
				<?if($product['model']=="")
						$product['model']="NONE";
					?>
					<td bgcolor="<?echo $bgcolor;?>" id="champ10<?echo $product['product_id'];?>">
					<input type="text" name="model[<?echo $product['product_id'];?>]" value="<?echo $product['model'];?>" size="10" onclick="document.getElementById('champ10<?echo $product['product_id'];?>').style.backgroundColor='green';"/>
					</td>
                    <td bgcolor="<?if($product['manufacturer_id']>0){echo $bgcolor;}else{echo "red";}?>" id="champ11<?echo $product['product_id'];?>">
                    <?echo $product['brand'];?>
					<input type="hidden" name="brand[<?echo $product['product_id'];?>]" value="<?echo $product['brand'];?>" />
                    <input type="hidden" name="condition_id[<?echo $product['product_id'];?>]" value="<?echo $product['condition_id'];?>" />
					<select name="manufacturer_id<?echo $product['product_id'];?>" onclick="document.getElementById('champ11<?echo $product['product_id'];?>').style.backgroundColor='green';">
			<option value="" selected></option>
<?
			$sql = 'SELECT * FROM `oc_manufacturer` order by name';
			// on envoie la requête
			$req = mysqli_query($db,$sql);
			// on fait une boucle qui va faire un tour pour chaque enregistrement
			$brandrecom="";
			while($data = mysqli_fetch_assoc($req))
				{
					$selected="";
					if (isset($product['manufacturer_id']) && $product['manufacturer_id']!=0){
						$test2=strtolower ($data['manufacturer_id']);
						$test1=strtolower ($product['manufacturer_id']);
						if ($test1==$test2) {
							$selected="selected";
						}
						//echo "allo";
					}else{
						$test2=strtolower ($data['name']);
						$test1=strtolower ($product['brand']);
						//echo "allo2";
						if (strpos($test1, $test2) !== false) {
							//$selected="selected";
							//echo 'allo3';
							//$brandrecom[$i]
							$brandrecom=$brandrecom.",".$data['name']."@".$data['manufacturer_id'];
						}
					}
					?>
								<option value="<?echo $data['manufacturer_id'];?>" <?echo $selected;?>><?echo $data['name'];?></option>
					<?}?>
							</select><br>
							<input type="hidden" name="manufacturer_id_old<?echo $product['product_id'];?>" value="<?echo $product['manufacturer_id'];?>" />
					<?	
					//echo $brandrecom;
					$brandrecomtab=explode(',', $brandrecom);
					foreach($brandrecomtab as $brandrecomtab2){
						if($brandrecomtab2!=null ){
							//echo $brandrecomtab2;
							$brandrecomtab3=explode('@', $brandrecomtab2);
							echo '<input id="manufacturer_recom" class="element radio" type="radio" name="manufacturer_recom['.$product['product_id'].']" value="'.$brandrecomtab3[1].'"/> 
									<label class="choice" for="etat_1">'.$brandrecomtab3[0].'</label><br>';
						}
					}	 
?>		
		<label>Add if not in the list:</label> <br><input id="manufacturersupp"  type="text" name="manufacturersupp[<?echo $product['product_id'];?>]" value="" maxlength="80" />
		</div>
					</td>
                    <td bgcolor="<?echo $bgcolor;?>" id="champ12<?echo $product['product_id'];?>">
					<input type="text" id="color<?echo $product['product_id'];?>en" name="coloren[<?echo $product['product_id'];?>]" value="<?echo $product['coloren'];?>" size="10" onchange="getTranslate(document.getElementById('color<?echo $product['product_id'];?>en').value,'color<?echo $product['product_id'];?>','fr')" onclick="document.getElementById('champ12<?echo $product['product_id'];?>').style.backgroundColor='green';" />
					<input type="text" id="color<?echo $product['product_id'];?>fr" name="colorfr[<?echo $product['product_id'];?>]" value="<?echo $product['colorfr'];?>" size="10" onclick="document.getElementById('champ12<?echo $product['product_id'];?>').style.backgroundColor='green';" />
					</td>
					</tr>
					<tr>
					<td></td>
					<th style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;"><label>Dimension:</label>
 </th>
					<th style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">
<label>Weight:</label>		 </th>
<th style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">	
<label>Extra Info:</label>		 </th>
				</tr>
					<tr>
					<td></td>
                    <td bgcolor="<?echo $bgcolor;?>">
					<input id="length"  type="text" name="length[<?echo $product['product_id'];?>]" value="5" size="3" />
X					<input id="width"  type="text" name="width[<?echo $product['product_id'];?>]" value="1" size="3" />
X					<input id="height"  type="text" name="height[<?echo $product['product_id'];?>]" value="1" size="3" />
					</td>
                    <td bgcolor="<?echo $bgcolor;?>">
					<input id="weight"  type="text" name="weight[<?echo $product['product_id'];?>]" value=".4375" size="5" />lbs
					</td>
                    <td bgcolor="<?echo $bgcolor;?>">
       				 	<input id="condition_supp<?echo $product['product_id'];?>en"  type="text" name="condition_suppen[<?echo $product['product_id'];?>]" value="<?echo $product['condition_suppen'];?>" size="10" onchange="getTranslate(document.getElementById('condition_supp<?echo $product['product_id'];?>en').value,'condition_supp<?echo $product['product_id'];?>','fr')" />
       				 	<input id="condition_supp<?echo $product['product_id'];?>fr"  type="text" name="condition_suppfr[<?echo $product['product_id'];?>]" value="<?echo $product['condition_suppfr'];?>" size="10" />
					</td>
			</tr>
		<?
	//$j++;
	//echo $j;
	$i++;
	$itemcount++;
            }
		?>
        		<tr>
        <th bgcolor="030282">
	<input type="button" onclick='selectAll()' value="Select All"/><br>
	<input type="button" onclick='UnSelectAll()' value="Unselect All"/>
	</th>
    <th colspan="3" style="vertical-align:  middle; height: 50px; background-color: #e4bc03; width: 200px;text-align:right"> 	
<input id="saveForm" class="button_text" type="submit" name="update" value="Update"  onclick="selectAll()"/>
<input id="saveForm" class="button_text" type="submit" name="list" value="List on eBay" />
<input id="saveForm" class="button_text" type="submit" name="traiter" value="Traiter" />
<br> 		<input id="saveForm" class="button_text" type="submit" name="ebay" value="eBay" />
 </th>
		</tr>
</table>
<?echo "Total Sale:".number_format($totalsale, 2,'.', '');?>
<input type="hidden" name="start" value="1" />
</form>
<script type="text/javascript">
	$(function() {
		  $(document).ready(function () {
		   var todaysDate = new Date(); // Gets today's date
			// Max date attribute is in "YYYY-MM-DD".  Need to format today's date accordingly
			var year = todaysDate.getFullYear(); 						// YYYY
			var month = ("0" + (todaysDate.getMonth() + 1)).slice(-2);	// MM
			var day = ("0" + todaysDate.getDate()).slice(-2);			// DD
			var minDate = (year +"-"+ month +"-"+ day); // Results in "YYYY-MM-DD" for today's date 
			// Now to set the max date value for the calendar to be today's date
			$('.departDate input').attr('min',minDate);
			  });
              <?echo $script;?>
	});
    function selectAll() {
        var items = document.getElementsByName('product_id[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }
    function UnSelectAll() {
        var items = document.getElementsByName('product_id[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }		
    function getTranslate(text_field,field,targetLanguage) {
	//alert(order_status_id_mod);
       $.ajax({
			method: "POST",
			url: 'translate.php',//'+ text_field +'
			data:{targetLanguage:targetLanguage,text_field:text_field},
            cache: false,
            dataType: "JSON",  
			beforeSend: function() {
						//	$('#button-customer').button('loading');
				/* 	document.getElementById("refresh").classList.remove('fa','fa-refresh');
					//$('#button-customer').button('loading');
					document.getElementById("refresh").classList.add('fa','fa-refresh','fa-spin');
				document.getElementById("alert-message").style.display = "block";	 */
			},
			complete: function() {
			/* 	 $('#button-customer').button('reset');
				 document.getElementById("alert-message").style.display = "none"; */
			}, 
			success:function(json) {
			   // location.reload();//if (text_field!="")
				 //  alert(json['success']);
				  document.getElementById(field+targetLanguage).value=decodeHtml(json['success']);
			   	/* document.getElementById("refresh").classList.remove('fa','fa-refresh','fa-spin');
				document.getElementById("refresh").classList.add('fa','fa-refresh'); */
				//alert("name"+targetLanguage);
			},
			error:function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
      }); 
 }
 function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}	
</script>
</body>
</html>
<?  
// on ferme la connexion à mysql 
mysqli_close($db); ?>