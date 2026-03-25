<? 
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$','n7f9655_phoenixsupplies');
include_once 'functionload.php';
// on sélectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
$transaction_id="";
if (isset($_FILES['file_import']))
{	
		$num=upload_file_ebay($db);
	
echo "<p style=\"color:red\">".$num." Importée de paypal</p><br><br>";		
}


//isoler les pending et canceled

$sql = "UPDATE `admin_paypal` SET `type_transaction_ok` = '99' WHERE (`admin_paypal`.`status` ='Canceled' OR `admin_paypal`.`status` ='Unpaid' OR `admin_paypal`.`status` ='Denied'
OR `admin_paypal`.`type_transaction`='Reversal of General Account Hold' OR `admin_paypal`.`type_transaction`='Postage Payment' OR `admin_paypal`.`type_transaction`='General Authorization' OR `admin_paypal`.`type_transaction`='Payment Hold' OR `admin_paypal`.`type_transaction`='Payment Release' OR `admin_paypal`.`type_transaction`='Hold on Balance for Dispute Investigation' OR `admin_paypal`.`type_transaction`='Account Hold for Open Authorization' OR `admin_paypal`.`balance_impact`like '%Memo%' OR `admin_paypal`.`type_transaction`='General Incentive/Certificate Redemption')";
					//UPDATE `admin_paypal` SET sales_taxes_ok=9 WHERE status="Canceled" and sales_taxes_ok=0';// or status="Pending" or status="Unpaid"
					//echo $sql."<br>"Postage Payment;
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					
echo "Transaction Pending, Unpaid et Canceled ISOLEE<br><br>";

//link avec vente website
$j=0;


$sql2 = 'SELECT * FROM `admin_paypal` WHERE (type_transaction="Transfer" or type_transaction="Adjustment") AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_deposits` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `gross`, `net`, `currency`, `name`, `type_transaction`, `item_title`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['gross'].'", "'.$data2['net'].'", "'.$data2['currency'].'", "'.$data2['name'].'", "'.$data2['type_transaction'].'", "'.$data2['item_title'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_deposits",type_transaction_ok=1 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}
echo "Deposit to PP Account:<p style=\"color:red\">".$j." Importée</p><br>";
$j=0;
//ajout Vente provenant de ebayinput
$sql2 = 'SELECT * FROM `admin_paypal` WHERE (type_transaction="Order" OR type_transaction="Other fee") AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
//echo $sql2."<br>";
while ($data2 = mysqli_fetch_assoc($req2)){
	//	if($data2['item_title']=="eBay Seller Fee"){
        
			$sql = 'INSERT INTO `admin_ebay_fee` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `currency`, `net`,`name`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['currency'].'", "'.$data2['fee'].'", "'.$data2['name'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_ebay_fee",type_transaction_ok=2 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}


//ajout Taxes CANADA
if ($data2['type_transaction']!="Other fee"){
				if($data2['sales_taxes']>0){
					if(strtolower($data2['state_province'])=="puerto rico")$data2['state_province']="PR";
					if(strtolower($data2['state_province'])=="pennsylvania")$data2['state_province']="PA";
					if(strtolower($data2['state_province'])=="oregon")$data2['state_province']="OR";
					if(strtolower($data2['state_province'])=="ontario")$data2['state_province']="ON";
					if(strtolower($data2['state_province'])=="oklahoma")$data2['state_province']="OK";
					if(strtolower($data2['state_province'])=="nova scotia")$data2['state_province']="NS";
					if(strtolower($data2['state_province'])=="north carolina")$data2['state_province']="NC";
					if(strtolower($data2['state_province'])=="newfoundland")$data2['state_province']="NL";
					if(strtolower($data2['state_province'])=="new york")$data2['state_province']="NY";
					if(strtolower($data2['state_province'])=="new mexico")$data2['state_province']="NM";
					if(strtolower($data2['state_province'])=="new jersey")$data2['state_province']="NJ";
					if(strtolower($data2['state_province'])=="missouri (mo)")$data2['state_province']="MO";
					if(strtolower($data2['state_province'])=="missouri")$data2['state_province']="MO";
					if(strtolower($data2['state_province'])=="mississippi")$data2['state_province']="MS";
					if(strtolower($data2['state_province'])=="michigan")$data2['state_province']="MI";
					if(strtolower($data2['state_province'])=="minnesota")$data2['state_province']="MN";
					if(strtolower($data2['state_province'])=="manitoba")$data2['state_province']="MB";
					if(strtolower($data2['state_province'])=="louisiana")$data2['state_province']="LA";
					if(strtolower($data2['state_province'])=="kentucky")$data2['state_province']="KY";
					if(strtolower($data2['state_province'])=="kansas")$data2['state_province']="KS";
					if(strtolower($data2['state_province'])=="indiana")$data2['state_province']="IN";
					if(strtolower($data2['state_province'])=="illinois")$data2['state_province']="IL";
					if(strtolower($data2['state_province'])=="guam")$data2['state_province']="GU";
					if(strtolower($data2['state_province'])=="georgia")$data2['state_province']="GA";
					if(strtolower($data2['state_province'])=="florida")$data2['state_province']="FL";
					if(strtolower($data2['state_province'])=="colorado")$data2['state_province']="CO";
					if(strtolower($data2['state_province'])=="california")$data2['state_province']="CA";
					if(strtolower($data2['state_province'])=="british columbia")$data2['state_province']="BC";
					if(strtolower($data2['state_province'])=="alberta")$data2['state_province']="AB";
					if(strtolower($data2['state_province'])=="quebec")$data2['state_province']="QC";
					if(strtolower($data2['state_province'])=="québec")$data2['state_province']="QC";
					if(strtolower($data2['state_province'])=="saskatchewan")$data2['state_province']="SK";
					if(strtolower($data2['state_province'])=="new-brunswick")$data2['state_province']="NB";
					
					$data2['state_province']=strtoupper($data2['state_province']); 
					//echo $data2['country'];
					if($data2['country']=="CA"){

									$sql = 'INSERT INTO `admin_ebay_taxes` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
									$sql = $sql.', `net`, `currency`, `state_province`,`country`, `name`) ';
									$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
									$sql = $sql.', "'.$data2['status'].'", "'.$data2['sales_taxes'].'", "'.$data2['currency'].'", "'.$data2['state_province'].'", "'.$data2['country'].'", "'.$data2['type_transaction'].'")';
									
									//echo $data2[0]."<br>";	
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);

					}elseif($data2['country']=="US"){
									$sql = 'INSERT INTO `admin_ebay_taxes_tmp` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
									$sql = $sql.', `net`, `currency`, `state_province`,`country`, `name`) ';
									$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
									$sql = $sql.', "'.$data2['status'].'", "'.$data2['sales_taxes'].'", "'.$data2['currency'].'", "'.$data2['state_province'].'", "'.$data2['country'].'", "'.$data2['type_transaction'].'")';
									
									//echo $data2[0]."<br>";	
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
									$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_ebay_taxes_tmp",type_transaction_ok=33 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql);
									$data2['sales_taxes']=0;
						
					}
				}
					$sql = 'INSERT INTO `admin_ebay_sales` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `item_id`, `item_title`, `quantity`, `gross`, `net`, `fee`, `sales_taxes`, `shipping_handling`';
					$sql = $sql.', `currency`, `balance_impact`, `name`, `shipping_address`, `state_province`, `country`, `type_transaction`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['item_id'].'", "'.$data2['item_title'].'"';
					$sql = $sql.', "'.$data2['quantity'].'", "'.$data2['gross'].'", "'.$data2['net'].'", "'.$data2['fee'].'", "'.$data2['sales_taxes'].'","'.$data2['shipping_handling'].'"';
					$sql= $sql.', "'.$data2['currency'].'", "'.$data2['balance_impact'].'", "'.$data2['name'].'"';
					$sql= $sql.', "'.$data2['shipping_address'].'", "'.$data2['state_province'].'", "'.$data2['country'].'", "'.$data2['type_transaction'].'")';
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_ebay_sales",type_transaction_ok=2 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
		
	//	}
                    }
					$j++;
}
echo "Ebay Sales:<p style=\"color:red\">".$j." Importée</p><br>";
$j=0;

$sql2 = 'SELECT * FROM `admin_paypal` WHERE (type_transaction="Refund" OR type_transaction="Shipping label" OR type_transaction="Claim")AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
	if($data2['type_transaction']=="Shipping label"){
		$sql = 'INSERT INTO `admin_shipping` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `currency`, `name`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['net'].'", "'.$data2['currency'].'", "'.$data2['name'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_shipping",type_transaction_ok=8  WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
	}elseif($data2['type_transaction']=="Refund" || $data2['type_transaction']="Shipping label"){
	
		
		if($data2['sales_taxes']>0 || $data2['country']=="CA" ){
					if(strtolower($data2['state_province'])=="puerto rico")$data2['state_province']="PR";
					if(strtolower($data2['state_province'])=="pennsylvania")$data2['state_province']="PA";
					if(strtolower($data2['state_province'])=="oregon")$data2['state_province']="OR";
					if(strtolower($data2['state_province'])=="ontario")$data2['state_province']="ON";
					if(strtolower($data2['state_province'])=="oklahoma")$data2['state_province']="OK";
					if(strtolower($data2['state_province'])=="nova scotia")$data2['state_province']="NS";
					if(strtolower($data2['state_province'])=="north carolina")$data2['state_province']="NC";
					if(strtolower($data2['state_province'])=="newfoundland")$data2['state_province']="NL";
					if(strtolower($data2['state_province'])=="new york")$data2['state_province']="NY";
					if(strtolower($data2['state_province'])=="new mexico")$data2['state_province']="NM";
					if(strtolower($data2['state_province'])=="new jersey")$data2['state_province']="NJ";
					if(strtolower($data2['state_province'])=="missouri (mo)")$data2['state_province']="MO";
					if(strtolower($data2['state_province'])=="missouri")$data2['state_province']="MO";
					if(strtolower($data2['state_province'])=="mississippi")$data2['state_province']="MS";
					if(strtolower($data2['state_province'])=="michigan")$data2['state_province']="MI";
					if(strtolower($data2['state_province'])=="minnesota")$data2['state_province']="MN";
					if(strtolower($data2['state_province'])=="manitoba")$data2['state_province']="MB";
					if(strtolower($data2['state_province'])=="louisiana")$data2['state_province']="LA";
					if(strtolower($data2['state_province'])=="kentucky")$data2['state_province']="KY";
					if(strtolower($data2['state_province'])=="kansas")$data2['state_province']="KS";
					if(strtolower($data2['state_province'])=="indiana")$data2['state_province']="IN";
					if(strtolower($data2['state_province'])=="illinois")$data2['state_province']="IL";
					if(strtolower($data2['state_province'])=="guam")$data2['state_province']="GU";
					if(strtolower($data2['state_province'])=="georgia")$data2['state_province']="GA";
					if(strtolower($data2['state_province'])=="florida")$data2['state_province']="FL";
					if(strtolower($data2['state_province'])=="colorado")$data2['state_province']="CO";
					if(strtolower($data2['state_province'])=="california")$data2['state_province']="CA";
					if(strtolower($data2['state_province'])=="british columbia")$data2['state_province']="BC";
					if(strtolower($data2['state_province'])=="alberta")$data2['state_province']="AB";
					if(strtolower($data2['state_province'])=="quebec")$data2['state_province']="QC";
					if(strtolower($data2['state_province'])=="québec")$data2['state_province']="QC";
					if(strtolower($data2['state_province'])=="saskatchewan")$data2['state_province']="SK";
					
					$data2['state_province']=strtoupper($data2['state_province']);
					//echo $data2['country'];
					if($data2['country']=="CA"){

									$sql = 'INSERT INTO `admin_ebay_taxes` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
									$sql = $sql.', `net`, `currency`, `state_province`,`country`, `name`) ';
									$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
									$sql = $sql.', "'.$data2['status'].'", "'.$data2['sales_taxes'].'", "'.$data2['currency'].'", "'.$data2['state_province'].'", "'.$data2['country'].'", "'.$data2['type_transaction'].'")';
									
									//echo $data2[0]."<br>";	
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);

					}elseif($data2['country']=="US"){
									$sql = 'INSERT INTO `admin_ebay_taxes_tmp` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
									$sql = $sql.', `net`, `currency`, `state_province`,`country`, `name`) ';
									$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
									$sql = $sql.', "'.$data2['status'].'", "'.$data2['sales_taxes'].'", "'.$data2['currency'].'", "'.$data2['state_province'].'", "'.$data2['country'].'", "'.$data2['type_transaction'].'")';
									
									//echo $data2[0]."<br>";	
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
									$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_ebay_taxes_tmp",type_transaction_ok=44 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql);
									$data2['sales_taxes']=0;
						
					}
				}
					$sql = 'INSERT INTO `admin_ebay_refunds` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `item_id`, `item_title`, `quantity`, `gross`, `net`, `fee`, `sales_taxes`, `shipping_handling`';
					$sql = $sql.', `currency`, `balance_impact`, `name`, `shipping_address`, `state_province`, `country`, `type_transaction`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['item_id'].'", "'.$data2['item_title'].'"';
					$sql = $sql.', "'.$data2['quantity'].'", "'.$data2['gross'].'", "'.$data2['net'].'", "'.$data2['fee'].'", "'.$data2['sales_taxes'].'","'.$data2['shipping_handling'].'"';
					$sql= $sql.', "'.$data2['currency'].'", "'.$data2['balance_impact'].'", "'.$data2['name'].'"';
					$sql= $sql.', "'.$data2['shipping_address'].'", "'.$data2['state_province'].'", "'.$data2['country'].'", "'.$data2['type_transaction'].'")';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);

					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_ebay_refunds",type_transaction_ok=8  WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
	}
}
echo "Refund:<p style=\"color:red\">".$j." Importée</p><br>";
/*
$j=0;
//ajout Subscription and purchase
$sql2 = 'SELECT * FROM `admin_paypal` WHERE (type_transaction="Subscription Payment" OR (type_transaction="Mass Pay Payment" AND gross>0) OR type_transaction="Payment Reversal") AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_purchases_paypal` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `net`,`sales_taxes`,`currency`, `name`,`item_title`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['gross'].'", "'.($data2['net']).'", "'.$data2['sales_taxes'].'", "'.$data2['currency'].'", "'.$data2['name'].'", "'.$data2['item_title'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>"; 
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_purchases_paypal",type_transaction_ok=9 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql);} 
					$j++;
}
echo "Ebay Subscription:<p style=\"color:red\">".$j." Importée</p><br>";
$j=0;
$sql2 = 'SELECT * FROM `admin_paypal` WHERE type_transaction="Express Checkout Payment" AND type_transaction_ok=0 AND gross<0 AND name!="eBay Canada Limited" order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_purchases_paypal` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `net`,`sales_taxes`,`currency`, `name`,`item_title`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['gross'].'", "'.($data2['net']).'", "'.$data2['sales_taxes'].'", "'.$data2['currency'].'", "'.$data2['name'].'", "'.$data2['item_title'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_purchases_paypal",type_transaction_ok=10 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}
$sql2 = 'SELECT * FROM `admin_paypal` WHERE type_transaction="PreApproved Payment Bill User Payment" AND name!="eBay Inc Shipping" AND name!="Shippo" AND type_transaction_ok=0 AND gross<0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_purchases_paypal` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `net`,`sales_taxes`,`currency`, `name`,`item_title`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['gross'].'", "'.($data2['net']).'", "'.$data2['sales_taxes'].'", "'.$data2['currency'].'", "'.$data2['name'].'", "'.$data2['item_title'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_purchases_paypal",type_transaction_ok=11 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}



echo "Ebay Purchase sur Paypal:<p style=\"color:red\">".$j." Importée</p><br>";

//OpenCart Ltd
$j=0;
$sql2 = 'SELECT * FROM `admin_paypal` WHERE type_transaction="Website Payment" AND name!="POSTPONY LLC" AND type_transaction_ok=0 AND gross<0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_purchases_paypal` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `currency`, `name`,`item_title`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['gross'].'", "'.$data2['currency'].'", "'.$data2['name'].'", "'.$data2['item_title'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_purchases_paypal",type_transaction_ok=12 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}
echo "Purchase:<p style=\"color:red\">".$j." Importée</p><br>";
$j=0;
$sql2 = 'SELECT * FROM `admin_paypal` WHERE type_transaction="General Payment" AND type_transaction_ok=0 AND gross<0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_purchases_paypal` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `currency`, `name`,`item_title`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['gross'].'", "'.$data2['currency'].'", "'.$data2['name'].'", "'.$data2['item_title'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_purchases_paypal",type_transaction_ok=13 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}
echo "General Payment:<p style=\"color:red\">".$j." Importée</p><br>";

//purchase sur carte de credit
$j=0;
$sql2 = 'SELECT * FROM `admin_paypal` WHERE type_transaction="General Credit Card Deposit" AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_purchases_cc` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `currency`, `name`,`item_title`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['gross'].'", "'.$data2['currency'].'", "'.$data2['name'].'", "'.$data2['item_title'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_purchases_cc",type_transaction_ok=14 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}
echo "Ebay Deposit sur CC:<p style=\"color:red\">".$j." Importée</p><br>";


$sql2 = 'SELECT * FROM `admin_paypal` WHERE type_transaction="General Credit Card Withdrawal" AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_purchases_cc` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `currency`, `name`,`item_title`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['gross'].'", "'.$data2['currency'].'", "'.$data2['name'].'", "'.$data2['item_title'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_purchases_cc",type_transaction_ok=15 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}
echo "Ebay Withdrawal sur CC:<p style=\"color:red\">".$j." Importée</p><br>";
//link les achat avec la carte de credit dans paypal

$sql2 = "SELECT * FROM `admin_purchases_paypal`as pp LEFT JOIN `admin_purchases_cc` as pc ON (pc.date_transaction=pp.date_transaction) where pp.link is NULL";
$req2 = mysqli_query($db,$sql2); 
//echo $sql2."<br>";
while ($data2 = mysqli_fetch_assoc($req2)){
	$sql = 'UPDATE `admin_purchases_paypal` SET link="Carte Credit" WHERE admin_purchases_paypal_id="'.$data2['admin_purchases_paypal_id'].'"';
					//echo $sql."<br>";
	$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
	$sql = 'UPDATE `admin_purchases_cc` SET link="Paypal" WHERE admin_purchases_cc_id="'.$data2['admin_purchases_cc_id'].'"';
					//echo $sql."<br>";
	$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
	
}
$sql2 = "SELECT admin_purchases_cc_id,date_transaction FROM `admin_purchases_cc` where link='' OR `name` is NULL OR `name` ='' OR `name` like '%please visit your Order%'";
$req2 = mysqli_query($db,$sql2); 
//echo $sql2."<br>";
while ($data2 = mysqli_fetch_assoc($req2)){
	$sql = 'SELECT * FROM `admin_paypal` WHERE balance_impact="Debit" AND date_transaction="'.$data2['date_transaction'].'"order by date_transaction';
	$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
	$data = mysqli_fetch_assoc($req);
	$source=explode(' ', $data['to_table']);
	if($data['link']=="Paypal")$link=',link="'.$source[1].'"';
	$sql = 'UPDATE `admin_purchases_cc` SET name="'.$data['name'].'"'.$link.' WHERE admin_purchases_cc_id="'.$data2['admin_purchases_cc_id'].'"';
	//echo $sql."<br>";
	$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
	$link="";
	
}
$sql2 = "SELECT admin_purchases_cc_id,date_transaction FROM `admin_purchases_cc` where link='' OR `name` is NULL OR `name` ='' OR `name` like '%please visit your Order%'";
$req2 = mysqli_query($db,$sql2); 
//echo $sql2."<br>";
while ($data2 = mysqli_fetch_assoc($req2)){
	$sql = 'SELECT * FROM `admin_paypal` WHERE balance_impact="Credit" AND date_transaction="'.$data2['date_transaction'].'"order by date_transaction';
	$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
	//echo $sql."<br>";
	$data = mysqli_fetch_assoc($req);
	//echo $data['link'];
	$source=explode(' ', $data['to_table']);
	if($data['link']=="Paypal")$link=',link="'.$source[1].'"';
	$sql = 'UPDATE `admin_purchases_cc` SET name="'.$data['name'].'"'.$link.' WHERE admin_purchases_cc_id="'.$data2['admin_purchases_cc_id'].'"';
	//echo $sql."<br>";
	$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
	$link="";
	
}
//ajout Shipping
$j=0;
$sql2 = 'SELECT * FROM `admin_paypal` WHERE name="POSTPONY LLC" AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_shipping` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `currency`, `name`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['gross'].'", "'.$data2['currency'].'", "'.$data2['name'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_shipping POSTPONY",type_transaction_ok=16 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}
echo "POSTPONY LLC Shipping:<p style=\"color:red\">".$j." Importée</p><br>";

$j=0;
$sql2 = 'SELECT * FROM `admin_paypal` WHERE name like "%Shippo%" AND type_transaction="PreApproved Payment Bill User Payment" AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_shipping` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `currency`, `name`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['gross'].'", "'.$data2['currency'].'", "'.$data2['name'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_shipping SHIPPO",type_transaction_ok=17 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}
echo "Shippo Shipping:<p style=\"color:red\">".$j." Importée</p><br>";


$j=0;
$sql2 = 'SELECT * FROM `admin_paypal` WHERE name="eBay Inc Shipping" AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_shipping` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `currency`, `name`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['gross'].'", "'.$data2['currency'].'", "'.$data2['name'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_shipping EBAY",type_transaction_ok=18 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}
echo "eBay Shipping:<p style=\"color:red\">".$j." Importée</p><br>";
$j=0;
$sql2 = 'SELECT * FROM `admin_paypal` WHERE item_title="" AND type_transaction="General Account Adjustment" AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_shipping` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `type_transaction`, `gross`, `currency`, `name`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['type_transaction'].'", "'.$data2['gross'].'", "'.$data2['currency'].'", "'.$data2['name'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_shipping EBAY",type_transaction_ok=19 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}
echo "Ebay Ajustement Shipping:<p style=\"color:red\">".$j." Importée</p><br>";


$j=0;
//ajout Tax collected by partner
$sql2 = 'SELECT * FROM `admin_paypal` WHERE type_transaction="Tax collected by partner" AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_ebay_taxes_tmp` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `net`, `currency`, `state_province`, `country`, `name`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['gross'].'", "'.$data2['currency'].'", "'.$data2['state_province'].'", "'.$data2['country'].'", "'.$data2['type_transaction'].'")';
						
					//echo $data2[0]."<br>";	
					//if($data2['gross']=="-66.0000"){
						//echo $sql."<br>";
						$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
						$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_ebay_taxes_tmp",type_transaction_ok=66 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql);

					$j++;
}
$sql2 = 'SELECT * FROM `admin_paypal` WHERE type_transaction="Partner Fee" AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_bonanza_taxes_tmp` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `net`, `currency`, `state_province`, `country`, `name`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['gross'].'", "'.$data2['currency'].'", "'.$data2['state_province'].'", "'.$data2['country'].'", "'.$data2['type_transaction'].'")';
					
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_bonanza_taxes_tmp",type_transaction_ok=55 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql);
					$j++;
}
echo "Tax collected by partner:<p style=\"color:red\">".$j." Importée</p><br>";

*/

//ajout Withdraw
$j=0;
$sql2 = 'SELECT * FROM `admin_paypal` WHERE (type_transaction="Payout") AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_withdrawals` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `gross`, `net`, `currency`, `name`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['gross'].'", "'.$data2['net'].'", "'.$data2['currency'].'", "'.$data2['name'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_withdrawals",type_transaction_ok=24 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}
echo "Depots vers Banque:<p style=\"color:red\">".$j." Importée</p><br>";
/*
//ajout Currency conversion
$j=0;
$sql2 = 'SELECT * FROM `admin_paypal` WHERE ((type_transaction="General Currency Conversion" OR type_transaction="User Initiated Currency Conversion") ) AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){
					$sql = 'INSERT INTO `admin_currency_conversion` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `gross`, `net`, `currency`, `name`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "0", "'.$data2['net'].'", "'.$data2['currency'].'", "'.$data2['name'].'")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_currency_conversion",type_transaction_ok=25 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql);} 
					$j++;
}
echo "Currency conversion USD CAD:<p style=\"color:red\">".$j." Importée</p><br>";
$j=0;
//ajout Chargeback Paypal FEE provenant de ebayinput
$sql2 = 'SELECT * FROM `admin_paypal` WHERE (type_transaction like "%Chargeback%" OR type_transaction like "%General Bonus%") AND type_transaction_ok=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){

					$sql = 'INSERT INTO `admin_paypal_fee` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `currency`, `net`, `name`,`from_order`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['currency'].'", "'.$data2['gross'].'", "'.$data2['name'].'","ChargeBackFee")';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_paypal_fee",type_transaction_ok=26 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
}

echo "ChargeBack Paypal FEE:<p style=\"color:red\">".$j." Importée</p><br>";
//calcul du taux echange usd-cad
insert_currency_exchange($db);
$j=0;
$sql2 = 'SELECT * FROM `admin_currency_conversion` WHERE currency = "USD" AND rate=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){

					$sql = 'UPDATE `admin_currency_conversion` SET rate=`admin_currency_conversion`.net/'.-$data2['net'].' ';
					$sql = $sql.'WHERE currency="CAD" AND date_transaction="'.$data2['date_transaction'].'"';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$j++;
}

$sql2 = 'SELECT * FROM `admin_currency_conversion` WHERE currency = "CAD" AND rate!=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
while ($data2 = mysqli_fetch_assoc($req2)){

					$sql = 'UPDATE `admin_currency_conversion` SET rate=`admin_currency_conversion`.net/'.-$data2['net'].' ';
					$sql = $sql.'WHERE currency="USD" AND date_transaction="'.$data2['date_transaction'].'"';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$j++;
}
$sql2 = 'SELECT * FROM `admin_currency_conversion` WHERE currency = "CAD" AND rateofficiel=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
$rateprecedent=0;
while ($data2 = mysqli_fetch_assoc($req2)){
					$date_tmp=explode(" ",$data2['date_transaction']);
					$date_info=$date_tmp[0];
					$sql3 = 'SELECT * FROM `admin_fxusdcad` WHERE date_info = "'.$date_info.'"';
					//echo $sql3."<br>";
					$req3 = mysqli_query($db,$sql3); 
					$data3 = mysqli_fetch_assoc($req3);
					if($data3['rate']==""){
						$data3['rate']=$rateprecedent;
					}else{
						$rateprecedent=$data3['rate'];
					}
						
					$sql = 'UPDATE `admin_currency_conversion` SET `admin_currency_conversion`.rateofficiel="'.$data3['rate'].'", `admin_currency_conversion`.commision_rate='.$data3['rate'].'-`admin_currency_conversion`.rate';
					$sql = $sql.' WHERE currency="CAD" AND date_transaction="'.$data2['date_transaction'].'"';
					
					//echo $data2[0]."<br>";	
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);

					$j++;
}
//echo "Currency_conversion:<p style=\"color:red\">".$j." Importée</p><br>";
//restant REFUND a linker
$j=0;
$sql2 = "SELECT p1.admin_paypal_id,p1.date_transaction,p1.timezone,p1.status,p2.item_id,p2.item_title,p2.quantity,p1.gross,
p1.net,p1.fee,p1.sales_taxes,p1.shipping_handling,p1.currency,p1.balance_impact,p1.name,p2.shipping_address,p2.state_province,p2.country,p2.to_table
FROM `admin_paypal` p1 left join `admin_paypal` p2 on (p1.`name`=p2.`name` AND p2.`type_transaction_ok`
<99 AND p1.`type_transaction` NOT LIKE p2.`type_transaction`) WHERE p1.`type_transaction_ok` = 0 AND p1.`type_transaction` 
like '%refund%' ORDER BY p1.`date_transaction`";
$req2 = mysqli_query($db,$sql2); 
//$data2 = mysqli_fetch_assoc($req2);
//print("<pre>".print_r ($data2,true )."</pre>");
while ($data2 = mysqli_fetch_assoc($req2)){
	//print("<pre>".print_r ($data2,true )."</pre>");
	if($data2['to_table']=="admin_ebay_sales")$table="ebay";
	if($data2['to_table']=="admin_bonanza_sales")$table="bonanza";
	if($data2['to_table']=="admin_website_sales")$table="website";
	if($data2['to_table']=="admin_invoice_sales")$table="invoice";

				if($data2['sales_taxes']>0 || $data2['country']=="CA"){
					if(strtolower($data2['state_province'])=="puerto rico")$data2['state_province']="PR";
					if(strtolower($data2['state_province'])=="pennsylvania")$data2['state_province']="PA";
					if(strtolower($data2['state_province'])=="oregon")$data2['state_province']="OR";
					if(strtolower($data2['state_province'])=="ontario")$data2['state_province']="ON";
					if(strtolower($data2['state_province'])=="oklahoma")$data2['state_province']="OK";
					if(strtolower($data2['state_province'])=="nova scotia")$data2['state_province']="NS";
					if(strtolower($data2['state_province'])=="north carolina")$data2['state_province']="NC";
					if(strtolower($data2['state_province'])=="newfoundland")$data2['state_province']="NL";
					if(strtolower($data2['state_province'])=="new york")$data2['state_province']="NY";
					if(strtolower($data2['state_province'])=="new mexico")$data2['state_province']="NM";
					if(strtolower($data2['state_province'])=="new jersey")$data2['state_province']="NJ";
					if(strtolower($data2['state_province'])=="missouri (mo)")$data2['state_province']="MO";
					if(strtolower($data2['state_province'])=="missouri")$data2['state_province']="MO";
					if(strtolower($data2['state_province'])=="mississippi")$data2['state_province']="MS";
					if(strtolower($data2['state_province'])=="michigan")$data2['state_province']="MI";
					if(strtolower($data2['state_province'])=="minnesota")$data2['state_province']="MN";
					if(strtolower($data2['state_province'])=="manitoba")$data2['state_province']="MB";
					if(strtolower($data2['state_province'])=="louisiana")$data2['state_province']="LA";
					if(strtolower($data2['state_province'])=="kentucky")$data2['state_province']="KY";
					if(strtolower($data2['state_province'])=="kansas")$data2['state_province']="KS";
					if(strtolower($data2['state_province'])=="indiana")$data2['state_province']="IN";
					if(strtolower($data2['state_province'])=="illinois")$data2['state_province']="IL";
					if(strtolower($data2['state_province'])=="guam")$data2['state_province']="GU";
					if(strtolower($data2['state_province'])=="georgia")$data2['state_province']="GA";
					if(strtolower($data2['state_province'])=="florida")$data2['state_province']="FL";
					if(strtolower($data2['state_province'])=="colorado")$data2['state_province']="CO";
					if(strtolower($data2['state_province'])=="california")$data2['state_province']="CA";
					if(strtolower($data2['state_province'])=="british columbia")$data2['state_province']="BC";
					if(strtolower($data2['state_province'])=="alberta")$data2['state_province']="AB";
					if(strtolower($data2['state_province'])=="quebec")$data2['state_province']="QC";
					if(strtolower($data2['state_province'])=="québec")$data2['state_province']="QC";
					if(strtolower($data2['state_province'])=="saskatchewan")$data2['state_province']="SK";
					
					$data2['state_province']=strtoupper($data2['state_province']);
					//echo $data2['country'];
					if($data['country']=="US"){
									$sql = 'INSERT INTO `admin_'.$table.'_taxes_tmp` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
									$sql = $sql.', `net`, `currency`, `state_province`,`country`, `name`) ';
									$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
									$sql = $sql.', "'.$data2['status'].'", "'.$data2['sales_taxes'].'", "'.$data2['currency'].'", "'.$data2['state_province'].'", "'.$data2['country'].'", "'.$data2['type_transaction'].'")';
									
									//echo $data2[0]."<br>";	
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
									$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_'.$table.'_taxes_tmp",type_transaction_ok=55 WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql);
									$data2['sales_taxes']=0;
						
					}
				}
					$sql = 'INSERT INTO `admin_'.$table.'_refunds` (`admin_paypal_id`, `date_transaction`, `timezone`, `status`';
					$sql = $sql.', `item_id`, `item_title`, `quantity`, `gross`, `net`, `fee`,`sales_taxes`,`shipping_handling`';
					$sql = $sql.', `currency`, `balance_impact`, `name`, `shipping_address`, `state_province`, `country`) ';
					$sql = $sql.'VALUES ( "'.$data2['admin_paypal_id'].'", "'.$data2['date_transaction'].'", "'.$data2['timezone'].'"';
					$sql = $sql.', "'.$data2['status'].'", "'.$data2['item_id'].'", "'.$data2['item_title'].'"';
					$sql = $sql.', "'.$data2['quantity'].'", "'.$data2['gross'].'", "'.($data2['net']-$data2['sales_taxes']).'", "'.$data2['fee'].'", "'.$data2['sales_taxes'].'","'.$data2['shipping_handling'].'"';
					$sql= $sql.', "'.$data2['currency'].'", "'.$data2['balance_impact'].'", "'.$data2['name'].'"';
					$sql= $sql.', "'.$data2['shipping_address'].'", "'.$data2['state_province'].'", "'.$data2['country'].'")';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);
					$mysqlierr=mysqli_error($req);if($mysqlierr!="")echo $mysqlierr."<br>";if($mysqlierr==""){$sql = 'UPDATE `admin_paypal` SET row_check="'.$numrows.'",to_table="admin_'.$table.'_refunds",type_transaction_ok=27  WHERE admin_paypal_id="'.$data2['admin_paypal_id'].'"';
					//echo $sql."<br>";
					$req = mysqli_query($db,$sql); $numrows=mysqli_insert_id($db);}
					$j++;
	}
	echo "Ajustement Refund:<p style=\"color:red\">".$j." Importée</p><br>";
*/
			?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="../stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="importation_ebay_mp.php" method="post" enctype="multipart/form-data">
<div class="form_description">
<h1>Importation de Paypal</h1>
Enlever virgule dans le nom de produit <br>
enlever les guillemets <br> mettre point dans la date changer format en yyyy-mm-dd xx:xx:xx
<input type="file" name="file_import" class="ed"><br />

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