<?
function upload_file_ebay($db){

	$SITE_ROOT=$_SERVER['DOCUMENT_ROOT'];
	
		$file=$_FILES['file_import']['tmp_name'];
		//$image= addslashes(file_get_contents($_FILES['file_import']['tmp_name']));
		$file_name= addslashes($_FILES['file_import']['name']);
			//print_r($_FILES);
			$uploads_dir = 'interne/admin/data';
			
			//$uploads_dir = 'upload';
				if ($_FILES['file_import']['error'] == 0) {
					if (is_uploaded_file($_FILES['file_import']['tmp_name']))
					{
						$tmp_name = $_FILES['file_import']['tmp_name'];
						// basename() may prevent filesystem traversal attacks;
						// further validation/sanitation of the filename may be appropriate
							$dir_name=$SITE_ROOT."/".$uploads_dir."/".time().$file_name;
						move_uploaded_file($tmp_name, $dir_name);
						//echo $sql2;
					}
					$num = 0;
					if (($file = fopen($dir_name, "r")) !== FALSE) {
						//echo $handle;
						while (($line = fgetcsv($file, 1000, ',')) !== FALSE) {
							if(!isset($count)) {
								$count = count($line);
							}
							//$line[18]=$line[18]."-".$num."-";
							//if($transaction_id==$line[18])	echo $line[18]."<br>";
							

							

							//echo $line[18];
							//$dateformat=explode ("/",$line[0]);
							$dateheure=$line[0];
							//$dateformat[2]."-".$dateformat[1]."-".$dateformat[0]." 00:00:01";
								//$line[19]=str_replace("\\","",$line[19]);
								if($line[2]=="--" && $line[1]=="Payout"){
									$line[2]=$line[13];
								}elseif($line[2]=="--" && $line[1]=="Other fee"){
									$line[2]=$line[35];
								}elseif($line[2]=="--" && $line[1]=="Adjustment"){
									$line[2]=$line[35];
								}elseif($line[2]=="--" && $line[1]=="Transfer"){
									$line[2]=$line[35];
								}elseif($line[2]=="--" && $line[1]=="Shipping label"){
									$tmp=str_replace("-","",$line[0]);
									$tmp=str_replace(" ","",$line[0]);
									$tmp=str_replace(":","",$line[0]);
									$line[2]=$tmp;
								}
								$line['order_id']=$line[2]."_".$line[1];

								$line[10]=str_replace("--",0,$line[10]);
								$line[21]=str_replace("--",0,$line[21]);
								$line[22]=str_replace("--",0,$line[22]);
								$line[23]=str_replace("--",0,$line[23]);
								$line[24]=str_replace("--",0,$line[24]);
								$line[25]=str_replace("--",0,$line[25]);
								$line[26]=str_replace("--",0,$line[26]);
								$line[27]=str_replace("--",0,$line[27]);
								$line[28]=str_replace("--",0,$line[28]);
								$line[29]=str_replace("--",0,$line[29]);
								$line[30]=str_replace("--",0,$line[30]);
								$line[31]=str_replace("--",0,$line[31]);
								$line[32]=str_replace("--",0,$line[32]);
								$line[33]=str_replace("--",0,$line[33]);
								
								
								if(strtolower($line[23])=="")$line[23]="0.00";
								if(strtolower($line[38])=="")$line[38]="0.00";
								if(strtolower($line[21])=="")$line[21]="0";
								if(strtolower($line[24])=="")$line[24]="0.00";
								if(strtolower($line[25])=="")$line[25]="0.00";
								if(strtolower($line[26])=="")$line[26]="0.00";
								if(strtolower($line[10])=="")$line[10]="0.00";
								if(strtolower($line[39])=="")$line[39]="0.00";
								
								
								if(strtolower($line[7])=="puerto rico")$line[7]="PR";
								if(strtolower($line[7])=="pennsylvania")$line[7]="PA";
								if(strtolower($line[7])=="oregon")$line[7]="OR";
								if(strtolower($line[7])=="ontario")$line[7]="ON";
								if(strtolower($line[7])=="oklahoma")$line[7]="OK";
								if(strtolower($line[7])=="nova scotia")$line[7]="NS";
								if(strtolower($line[7])=="north carolina")$line[7]="NC";
								if(strtolower($line[7])=="newfoundland")$line[7]="NL";
								if(strtolower($line[7])=="new york")$line[7]="NY";
								if(strtolower($line[7])=="new mexico")$line[7]="NM";
								if(strtolower($line[7])=="new jersey")$line[7]="NJ";
								if(strtolower($line[7])=="missouri (mo)")$line[7]="MO";
								if(strtolower($line[7])=="missouri")$line[7]="MO";
								if(strtolower($line[7])=="mississippi")$line[7]="MS";
								if(strtolower($line[7])=="michigan")$line[7]="MI";
								if(strtolower($line[7])=="minnesota")$line[7]="MN";
								if(strtolower($line[7])=="manitoba")$line[7]="MB";
								if(strtolower($line[7])=="louisiana")$line[7]="LA";
								if(strtolower($line[7])=="kentucky")$line[7]="KY";
								if(strtolower($line[7])=="kansas")$line[7]="KS";
								if(strtolower($line[7])=="indiana")$line[7]="IN";
								if(strtolower($line[7])=="illinois")$line[7]="IL";
								if(strtolower($line[7])=="guam")$line[7]="GU";
								if(strtolower($line[7])=="georgia")$line[7]="GA";
								if(strtolower($line[7])=="florida")$line[7]="FL";
								if(strtolower($line[7])=="colorado")$line[7]="CO";
								if(strtolower($line[7])=="california")$line[7]="CA";
								if(strtolower($line[7])=="british columbia")$line[7]="BC";
								if(strtolower($line[7])=="alberta")$line[7]="AB";
								if(strtolower($line[7])=="quebec")$line[7]="QC";
								if(strtolower($line[7])=="québec")$line[7]="QC";
								if(strtolower($line[7])=="saskatchewan")$line[7]="SK";
								if(strtolower($line[7])=="new-brunswick")$line[7]="NB";
								$line[7]=strtoupper($line[7]);

							// add to new array (subarray)
							//if(strlen($line[18])>10)$line[18]="";
							$importarray = array(
								'transaction_id' => $line['order_id'],
								'date_transaction' => $dateheure,
								'timezone' => "EST",
								'type_transaction' => $line[1],
								'status' => $line[1],
								'item_id' => $line[17],
								'item_title' => preg_replace("/[^a-zA-Z0-9\s]/", "", addslashes(substr($line[19],0,80))),
								'quantity' => $line[21],
								'gross' => str_replace(",","",$line[32]),
								'fee' => str_replace(",","",($line[26]+$line[27]+$line[28]+$line[29]+$line[30]+$line[31])),
								'net' => str_replace(",","",$line[10]),
								'shipping_handling' => str_replace(",","",$line[23]),
								'insurance' => 0,
								'sales_taxes' => str_replace(",","",($line[24]+$line[25])),
								'currency' => $line[33],
								'balance' => str_replace(",","",$line[39]),
								'balance_impact' => "",
								'name' => preg_replace("/[^a-zA-Z0-9\s]/", "",addslashes(substr($line[5],0,120))),
								'from_email' => "",
								'to_email' => "info@phoenixsupplies.ca",
								'shipping_address' => preg_replace("/[^a-zA-Z0-9\s]/", "",addslashes(substr($line[5]." ".$line[6]." ".$line[7]." ".$line[8]." ".$line[9],0,120))),
								'state_province' => $line[7],
								'country' => $line[9],
								'address_status' => "",
								'invoice_number' =>$line[2],
								'custom_number' =>$line[18] 
							);
							$i=0;
							//print("<pre>".print_r ($importarray,true )."</pre>");
							//echo $importarray['transaction_id']."<br>";
							if($num>0){
									$sql = 'INSERT INTO `admin_paypal` (`transaction_id`, `date_transaction`, `timezone`, `type_transaction`, `status`';
									$sql = $sql.', `item_id`, `item_title`, `quantity`, `gross`, `fee`, `net`, `shipping_handling`, `insurance`, `sales_taxes`';
									$sql = $sql.', `currency`, `balance`, `balance_impact`, `name`, `from_email`, `to_email`, `shipping_address`, `state_province`, `country`,`address_status` ';
									$sql = $sql.', `invoice_number`, `custom_number`)';
									$sql = $sql.'VALUES ( "'.$importarray['transaction_id'].'", "'.$importarray['date_transaction'].'", "'.$importarray['timezone'].'", "'.$importarray['type_transaction'].'", "'.$importarray['status'].'"';
									$sql = $sql.', "'.$importarray['item_id'].'", "'.$importarray['item_title'].'", "'.$importarray['quantity'].'", "'.$importarray['gross'].'", "'.$importarray['fee'].'"';
									$sql = $sql.', "'.$importarray['net'].'", "'.$importarray['shipping_handling'].'", "'.$importarray['insurance'].'","'. $importarray['sales_taxes'].'", "'.$importarray['currency'].'"';
									$sql= $sql.', "'.$importarray['balance'].'", "'.$importarray['balance_impact'].'", "'.$importarray['name'].'", "'.$importarray['from_email'].'", "'.$importarray['to_email'].'"';
									$sql= $sql.', "'.$importarray['shipping_address'].'", "'.$importarray['state_province'].'", "'.$importarray['country'].'", "'.$importarray['address_status'].'"';
									$sql= $sql.', "'.$importarray['invoice_number'].'", "'.$importarray['custom_number'].'")';
									//echo $importarray[0]."<br>";	
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql); 
									if(mysqli_error($db)!=""){
										echo "***".$importarray['transaction_id']."***<p style=\"color:red\">".mysqli_error($db)."</p><br>";
										//print("<pre>".print_r ($importarray,true )."</pre>");
									}
									
							}
							//print("<pre>".print_r ($importarray,true )."</pre>");
							// add to the array of all orders
							$array[$num] = $importarray;
							$num++;
						}
						unset($array[0]);
						
						fclose($file);
					}
				}
	return $num-1;
}

function upload_file($db){

	$SITE_ROOT=$_SERVER['DOCUMENT_ROOT'];
	
		$file=$_FILES['file_import']['tmp_name'];
		//$image= addslashes(file_get_contents($_FILES['file_import']['tmp_name']));
		$file_name= addslashes($_FILES['file_import']['name']);
			//print_r($_FILES);
			$uploads_dir = 'interne/admin/data';
			
			//$uploads_dir = 'upload';
				if ($_FILES['file_import']['error'] == 0) {
					if (is_uploaded_file($_FILES['file_import']['tmp_name']))
					{
						$tmp_name = $_FILES['file_import']['tmp_name'];
						// basename() may prevent filesystem traversal attacks;
						// further validation/sanitation of the filename may be appropriate
							$dir_name=$SITE_ROOT."/".$uploads_dir."/".time().$file_name;
						move_uploaded_file($tmp_name, $dir_name);
						//echo $sql2;
					}
					$num = 0;
					if (($file = fopen($dir_name, "r")) !== FALSE) {
						//echo $handle;
						while (($line = fgetcsv($file, 1000, ',')) !== FALSE) {
							if(!isset($count)) {
								$count = count($line);
							}
							//$line[12]=$line[12]."-".$num."-";
							//if($transaction_id==$line[12])	echo $line[12]."<br>";
							

							$line[12]=$line[12]."_".$line[5]."_".str_replace(":","",$line[1]);

							//echo $line[12];
							$dateformat=explode ("/",$line[0]);
							$dateheure=$dateformat[2]."-".$dateformat[1]."-".$dateformat[0]." ".$line[1];
								//$line[15]=str_replace("\\","",$line[15]);
								if(strtolower($line[17])=="")$line[17]="0.00";
								if(strtolower($line[18])=="")$line[18]="0.00";
								if(strtolower($line[27])=="")$line[27]="0";
								if(strtolower($line[19])=="")$line[19]="0.00";
								if(strtolower($line[8])=="")$line[8]="0.00";
								if(strtolower($line[9])=="")$line[9]="0.00";
								if(strtolower($line[29])=="")$line[29]="0.00";
								
								if(strtolower($line[33])=="puerto rico")$line[33]="PR";
								if(strtolower($line[33])=="pennsylvania")$line[33]="PA";
								if(strtolower($line[33])=="oregon")$line[33]="OR";
								if(strtolower($line[33])=="ontario")$line[33]="ON";
								if(strtolower($line[33])=="oklahoma")$line[33]="OK";
								if(strtolower($line[33])=="nova scotia")$line[33]="NS";
								if(strtolower($line[33])=="north carolina")$line[33]="NC";
								if(strtolower($line[33])=="newfoundland")$line[33]="NL";
								if(strtolower($line[33])=="new york")$line[33]="NY";
								if(strtolower($line[33])=="new mexico")$line[33]="NM";
								if(strtolower($line[33])=="new jersey")$line[33]="NJ";
								if(strtolower($line[33])=="missouri (mo)")$line[33]="MO";
								if(strtolower($line[33])=="missouri")$line[33]="MO";
								if(strtolower($line[33])=="mississippi")$line[33]="MS";
								if(strtolower($line[33])=="michigan")$line[33]="MI";
								if(strtolower($line[33])=="minnesota")$line[33]="MN";
								if(strtolower($line[33])=="manitoba")$line[33]="MB";
								if(strtolower($line[33])=="louisiana")$line[33]="LA";
								if(strtolower($line[33])=="kentucky")$line[33]="KY";
								if(strtolower($line[33])=="kansas")$line[33]="KS";
								if(strtolower($line[33])=="indiana")$line[33]="IN";
								if(strtolower($line[33])=="illinois")$line[33]="IL";
								if(strtolower($line[33])=="guam")$line[33]="GU";
								if(strtolower($line[33])=="georgia")$line[33]="GA";
								if(strtolower($line[33])=="florida")$line[33]="FL";
								if(strtolower($line[33])=="colorado")$line[33]="CO";
								if(strtolower($line[33])=="california")$line[33]="CA";
								if(strtolower($line[33])=="british columbia")$line[33]="BC";
								if(strtolower($line[33])=="alberta")$line[33]="AB";
								if(strtolower($line[33])=="quebec")$line[33]="QC";
								if(strtolower($line[33])=="québec")$line[33]="QC";
								if(strtolower($line[33])=="saskatchewan")$line[33]="SK";
								if(strtolower($line[33])=="new-brunswick")$line[33]="NB";
								$line[33]=strtoupper($line[33]);
							// add to new array (subarray)
							if(strlen($line[26])>10)$line[26]="";
							$importarray = array(
								'transaction_id' => $line[12],
								'date_transaction' => $dateheure,
								'timezone' => $line[2],
								'type_transaction' => $line[1],
								'status' => $line[5],
								'item_id' => $line[16],
								'item_title' => preg_replace("/[^a-zA-Z0-9\s]/", "", addslashes(substr($line[15],0,80))),
								'quantity' => $line[27],
								'gross' => str_replace(",","",$line[7]),
								'fee' => str_replace(",","",$line[8]),
								'net' => str_replace(",","",$line[9]),
								'shipping_handling' => str_replace(",","",$line[17]),
								'insurance' => str_replace(",","",$line[18]),
								'sales_taxes' => str_replace(",","",$line[19]),
								'currency' => $line[6],
								'balance' => str_replace(",","",$line[29]),
								'balance_impact' => $line[40],
								'name' => preg_replace("/[^a-zA-Z0-9\s]/", "",addslashes(substr($line[3],0,120))),
								'from_email' => $line[10],
								'to_email' => $line[11],
								'shipping_address' => preg_replace("/[^a-zA-Z0-9\s]/", "",addslashes(substr($line[13],0,120))),
								'state_province' => $line[33],
								'country' => $line[39],
								'address_status' => $line[14],
								'invoice_number' =>$line[25],
								'custom_number' =>$line[26] 
							);
							$i=0;
							//print("<pre>".print_r ($importarray,true )."</pre>");
							//echo $importarray['transaction_id']."<br>";
							if($num>0){
									$sql = 'INSERT INTO `admin_paypal` (`transaction_id`, `date_transaction`, `timezone`, `type_transaction`, `status`';
									$sql = $sql.', `item_id`, `item_title`, `quantity`, `gross`, `fee`, `net`, `shipping_handling`, `insurance`, `sales_taxes`';
									$sql = $sql.', `currency`, `balance`, `balance_impact`, `name`, `from_email`, `to_email`, `shipping_address`, `state_province`, `country`,`address_status` ';
									$sql = $sql.', `invoice_number`, `custom_number`)';
									$sql = $sql.'VALUES ( "'.$importarray['transaction_id'].'", "'.$importarray['date_transaction'].'", "'.$importarray['timezone'].'", "'.$importarray['type_transaction'].'", "'.$importarray['status'].'"';
									$sql = $sql.', "'.$importarray['item_id'].'", "'.$importarray['item_title'].'", "'.$importarray['quantity'].'", "'.$importarray['gross'].'", "'.$importarray['fee'].'"';
									$sql = $sql.', "'.$importarray['net'].'", "'.$importarray['shipping_handling'].'", "'.$importarray['insurance'].'","'. $importarray['sales_taxes'].'", "'.$importarray['currency'].'"';
									$sql= $sql.', "'.$importarray['balance'].'", "'.$importarray['balance_impact'].'", "'.$importarray['name'].'", "'.$importarray['from_email'].'", "'.$importarray['to_email'].'"';
									$sql= $sql.', "'.$importarray['shipping_address'].'", "'.$importarray['state_province'].'", "'.$importarray['country'].'", "'.$importarray['address_status'].'"';
									$sql= $sql.', "'.$importarray['invoice_number'].'", "'.$importarray['custom_number'].'")';
									//echo $importarray[0]."<br>";	
									//echo $sql."<br>";
									$req = mysqli_query($db,$sql); 
									if(mysqli_error($db)!=""){
										echo "***".$importarray['transaction_id']."***<p style=\"color:red\">".mysqli_error($db)."</p><br>";
										//print("<pre>".print_r ($importarray,true )."</pre>");
									}
									
							}
							//print("<pre>".print_r ($importarray,true )."</pre>");
							// add to the array of all orders
							$array[$num] = $importarray;
							$num++;
						}
						unset($array[0]);
						
						fclose($file);
					}
				}
	return $num-1;
}
function get_sum($yeardeb,$yearfin,$monthdeb,$monthfin,$from,$where,$db){

	$sql="SELECT SUM(net) FROM `admin_".$from."` ";
	$sql.="WHERE ".$where." date_transaction BETWEEN '".$yeardeb."-".$monthdeb."-01 00:00:00' AND '".$yearfin."-".$monthfin."-31 23:59:59'";
//	echo $sql;
	$req = mysqli_query($db,$sql); 
	$data= mysqli_fetch_assoc($req);
	//print("<pre>".print_r ($data,true )."</pre>");
	return $data['SUM(net)'];
}
function get_sum_cad($yeardeb,$yearfin,$monthdeb,$monthfin,$from,$where,$db){
	$net=0;
	$sql="SELECT net,date_transaction FROM `admin_".$from."` ";
	$sql.="WHERE ".$where." date_transaction BETWEEN '".$yeardeb."-".$monthdeb."-01 00:00:00' AND '".$yearfin."-".$monthfin."-31 23:59:59'";
	//echo $sql;
	$req = mysqli_query($db,$sql); 
	while($data= mysqli_fetch_assoc($req)){
		if($where=="currency='CAD' AND "){
			$net+=$data['net'];
		}else{
			$date_tmp=explode(" ",$data['date_transaction']);
			$date_info=$date_tmp[0];
			$rate=get_fxrate($date_info,$db);
			$net+=($rate*$data['net']);
		}
	}
	//print("<pre>".print_r ($data,true )."</pre>");
	return $net;
}
function get_sum_by_month($yeardeb,$yearfin,$monthdeb,$monthfin,$from,$fromdb,$title){
	$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$',$fromdb);
	$data_return= array(
	//	'DATE' => 0,	
		
		'TITLE' => $title,
		'PAYPAL' => 0,
		'TPS' => 0,
		'TVQ' => 0,
		'TVH' => 0,
		'FEE' => 0
	);
	$sql="SELECT * FROM `admin_".$from."` ";
	$sql.="WHERE date_transaction BETWEEN '".$yeardeb."-".$monthdeb."-01 00:00:00' AND '".$yearfin."-".$monthfin."-31 23:59:59'";
	//echo $sql;
	$req = mysqli_query($db,$sql); 
	while($data= mysqli_fetch_assoc($req)){
		
		if($data['country']=="CA"){
			
			
			if($data['currency']=='CAD'){
				$rate=1;
				$data_return['PAYPAL']+=($data['net']);
			}/*else{
				$date_tmp=explode(" ",$data['date_transaction']);
				$date_info=$date_tmp[0];
				$rate=get_fxrate($date_info,$db);
				$data_return['VENTEUSD']+=($data['net']);
			}*/
			if($data['state_province']=="QC"){
				$data_return['TPS']+=($data['sales_taxes']/.14975*.05*$rate);
				$data_return['TVQ']+=($data['sales_taxes']/.14975*.09975*$rate);
			}
			$data_return['FEE']+=(-$data['fee']*$rate);
			if($data['state_province']=="ON")$data_return['TVH']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="BC")$data_return['TPS']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="AB")$data_return['TPS']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="SK")$data_return['TPS']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="MB")$data_return['TPS']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="NS")$data_return['TVH']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="NB")$data_return['TVH']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="NL")$data_return['TVH']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="YK")$data_return['TPS']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="PEI")$data_return['TVH']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="NWT")$data_return['TPS']+=($data['sales_taxes']*$rate);
			
		}
	}
	//print("<pre>".print_r ($data,true )."</pre>");
	mysqli_close($db);
	return $data_return;
}
function get_ebay_sum_by_month($yeardeb,$yearfin,$monthdeb,$monthfin,$from,$fromdb,$title){
	$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$',$fromdb);
	$data_return= array(
	//	'DATE' => 0,	
		
		'TITLE' => $title,
		'PAYPAL NET' => 0,
		'TPS' => 0,
		'TVQ' => 0,
		'TVH' => 0,
		'FEE' => 0
	);
	$sql="SELECT * FROM `admin_".$from."` ";
	$sql.="WHERE country='CA' and date_transaction BETWEEN '".$yeardeb."-".$monthdeb."-01 00:00:00' AND '".$yearfin."-".$monthfin."-31 23:59:59'";
	
	if($from=='ebay_refunds'){
	//	//print("<pre>".print_r ($data,true )."</pre>");
	//	echo $sql."<br><br>";
		}
	$req = mysqli_query($db,$sql); 
	while($data= mysqli_fetch_assoc($req)){
		if($from=='ebay_refunds'){
		//print("<pre>".print_r ($data,true )."</pre>");
		//echo $sql."<br><br>";
		}
		if($data['country']=="CA"){
			
			
		//	if($data['currency']=='CAD'){
				
			}/*else{
				$date_tmp=explode(" ",$data['date_transaction']);
				$date_info=$date_tmp[0];
				$rate=get_fxrate($date_info,$db);
				$data_return['VENTEUSD']+=($data['net']);
			}*/
			$date_tmp=explode(" ",$data['date_transaction']);
				$date_info=$date_tmp[0];
			$rate=get_fxrate($date_info,$db);
			$data_return['PAYPAL NET']+=($data['net'])*$rate;
			if($data['state_province']=="QC"){
				$data_return['TPS']+=($data['sales_taxes']/.14975*.05*$rate);
				$data_return['TVQ']+=($data['sales_taxes']/.14975*.09975*$rate);
			}
			$data_return['FEE']+=(-$data['fee']*$rate);
			if($data['state_province']=="ON")$data_return['TVH']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="BC")$data_return['TPS']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="AB")$data_return['TPS']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="SK")$data_return['TPS']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="MB")$data_return['TPS']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="NS")$data_return['TVH']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="NB")$data_return['TVH']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="NL")$data_return['TVH']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="YK")$data_return['TPS']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="PEI")$data_return['TVH']+=($data['sales_taxes']*$rate);
			if($data['state_province']=="NWT")$data_return['TPS']+=($data['sales_taxes']*$rate);
			
		//}
	}
	
	mysqli_close($db);
	return $data_return;
}
function get_website_transaction_by_month($yeardeb,$yearfin,$monthdeb,$monthfin,$fromdb,$title){
	//mysqli_close($db);
	$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$',$fromdb);
	$data_return= array(
	//	'DATE' => 0,	
		
		'TITLE' => $title,
		'VENTE_NET' => 0,
		'PAYPAL' => 0,
		'ELAVON' => 0,
		'SQUARE' => 0,
		'STRIPE' => 0,
		'COMPTANT' => 0,
		'CARTECREDIT' => 0,
		'CARTEDEBIT' => 0,
		'TPS' => 0,
		'TVQ' => 0,
		'TVH' => 0,
		'FEE' => 0
	);
	$return=array();
	$sql="SELECT *,o.date_added as date_transaction FROM `oc_order` o LEFT JOIN `oc_order_history` oh ON (oh.order_id=o.order_id) LEFT JOIN `oc_order_status` os ON (oh.order_status_id=os.order_status_id && oh.order_status_id=5)";
	$sql.="WHERE currency_code='CAD' AND  o.date_added  BETWEEN '".$yeardeb."-".$monthdeb."-01 00:00:00' AND '".$yearfin."-".$monthfin."-31 23:59:59' group by o.order_id order by o.date_added ";// limit 1
	//echo "<br>".$sql."<br>";
	$req = mysqli_query($db,$sql); 
	$i=0;
	$sum=0;
	while($data= mysqli_fetch_assoc($req)) {
		$currency_value=$data['currency_value'];
	//	//print("<pre>".print_r ($data,true )."</pre>");
	
		
		if($data['payment_country_id']=="38"){
			
			if($data['currency_code']=='CAD'){
				$sql2="SELECT * FROM `oc_order_total` ot WHERE (ot.order_id=".$data['order_id'].")";
				$req2 = mysqli_query($db,$sql2); 
				
			//	echo "<br>".$sql2."<br>";
				//$return[$i] = $data;
		/*		<div class="divTableCell"><?if($data['currency_code']=="CAD"){echo "$ ".number_format($data['sub_total']['value']*1.34,2);}else{echo "$ ".number_format($data['sub_total']['value'],2);}?></div>
		<div class="divTableCell"><?if($data['currency_code']=="CAD"){echo "$ ".number_format($data['mdiscount']['value']*1.34,2);}else{echo "$ ".number_format($data['mdiscount']['value'],2);}?></div>
		<div class="divTableCell"><?if($data['currency_code']=="CAD"){echo "$ ".number_format($data['shipping']['value']*1.34,2);}else{echo "$ ".number_format($data['shipping']['value'],2);}?></div>
		<div class="divTableCell"><?if($data['currency_code']=="CAD")echo "$ ".number_format($data['tax']['value']*1.34,2);?></div>
		*/
				$tax=0;
				$sub_total=0;
				$mdiscount=0;
				$shipping=0;

				while($result= mysqli_fetch_assoc($req2)){
				//	$total=($result['sub_total']['value']*$currency_value)-($result['mdiscount']['value']*$currency_value)+($result['shipping']['value']*$currency_value);
			//		//print("<pre>".print_r ($result,true )."</pre>");
					if($result['code']=='tax')$tax=$result['value'];
					if($result['code']=='sub_total')$sub_total=$result['value'];
					if($result['code']=='total')$total=$result['value'];
					if($result['code']=='mdiscount')$mdiscount=$result['value'];
					if($result['code']=='shipping')$shipping=$result['value'];
			//		echo $result['code']."<br>"	;
				}
				if($data['payment_zone_id']=="612"){
					$data_return['TPS']+=($tax/.14975*.05*$currency_value);
					$data_return['TVQ']+=($tax/.14975*.09975*$currency_value);
				}
				
				if($data['payment_zone_id']=="610")$data_return['TVH']+=($tax*$currency_value);
				if($data['payment_zone_id']=="603")$data_return['TPS']+=($tax*$currency_value);
				if($data['payment_zone_id']=="602")$data_return['TPS']+=($tax*$currency_value);
				if($data['payment_zone_id']=="613")$data_return['TPS']+=($tax*$currency_value);
				if($data['payment_zone_id']=="604")$data_return['TPS']+=($tax*$currency_value);
				if($data['payment_zone_id']=="608")$data_return['TVH']+=($tax*$currency_value);
				if($data['payment_zone_id']=="605")$data_return['TVH']+=($tax*$currency_value);
				if($data['payment_zone_id']=="606")$data_return['TVH']+=($tax*$currency_value);
				if($data['payment_zone_id']=="614")$data_return['TPS']+=($tax*$currency_value);
				if($data['payment_zone_id']=="611")$data_return['TVH']+=($tax*$currency_value);
				if($data['payment_zone_id']=="607")$data_return['TPS']+=($tax*$currency_value);
				$data['payment_code']=str_replace("\xE9",'é', $data['payment_code']);
				if($data['payment_code']=="virtualmerchant")$data_return['ELAVON']+=(($total))*$currency_value;
				if($data['payment_code']=="pp_standard")$data_return['PAYPAL']+=(($total))*$currency_value;
				if($data['payment_code']=="squareup")$data_return['SQUARE']+=(($total))*$currency_value;
				if($data['payment_code']=="stripe")$data_return['STRIPE']+=(($total))*$currency_value;
				if($data['payment_code']=="Cash" || $data['payment_code']=="Comptant")$data_return['COMPTANT']+=(($total))*$currency_value;
				if($data['payment_code']=="Credit" || $data['payment_code']=="Crédit")$data_return['CARTECREDIT']+=(($total))*$currency_value;
				if($data['payment_code']=="Debit" || $data['payment_code']=="Débit")$data_return['CARTEDEBIT']+=(($total))*$currency_value;
				$data_return['VENTE_NET']+=($sub_total+$mdiscount+$shipping)*$currency_value;
				//		echo "Paymentcode ".$data['payment_code']."<br>"	;
			//	$string = str_replace("\xEF\xBF\xBD",'X','My ��� some text');
		//		echo $string;
			//$sum+=$result2['Total'];
			}
			

		}
	
	}
	
	
	
		
		
	//print("<pre>".print_r ($data_return,true )."</pre>");
	mysqli_close($db);
	return $data_return;
}
function get_bank_transaction_by_month($yeardeb,$yearfin,$monthdeb,$monthfin,$from,$fromdb,$title){
	//mysqli_close($db);
	
		$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$',$fromdb);
		$data_return= array(
		//	'DATE' => 0,	
			
			'TITLE' => $title,
			
		);
		$sql="SELECT * FROM `admin_".$from."` ";
		$sql.="WHERE date_transaction BETWEEN '".$yeardeb."-".$monthdeb."-01 00:00:00' AND '".$yearfin."-".$monthfin."-31 23:59:59'";
		$req = mysqli_query($db,$sql); 
	$i=1;
	$sum=0;
	while($data= mysqli_fetch_assoc($req)) {
		$currency_value=$data['currency_value'];
		//print("<pre>".print_r ($data,true )."</pre>");
	
	/*	<div class="divTableCell"<?if($data['currency']=="USD"){echo 'style="background-color:green; color: white;"';}?>><?if($data['currency']=="USD")echo "$ ".number_format(-$data['gross'],2);?></div>
 <div class="divTableCell"<?if($data['currency']=="CAD"){echo 'style="background-color:green; color: white;"';}?>><?if($data['currency']=="CAD"){echo "$ ".number_format(-$data['gross'],2);}?></div>
*/
		$data_return[$i]['date_transaction']=$data['date_transaction'];
		$data_return[$i]['gross']=-$data['gross'];
		//$data_return[$data['date_transaction']]['net']=$data['net'];
		$data_return[$i]['currency']=$data['currency'];
		$data_return[$i]['name']=$data['name'];
		$i++;
			

		
	
	}
	
	
	
		
		
//	//print("<pre>".print_r ($data_return,true )."</pre>");
	mysqli_close($db);
	return $data_return;
}
function get_website_sum($yeardeb,$yearfin,$monthdeb,$monthfin,$where,$db){

	$return=array();
	$sql="SELECT *,o.date_added as date_transaction FROM `oc_order` o LEFT JOIN `oc_order_history` oh ON (oh.order_id=o.order_id) LEFT JOIN `oc_order_status` os ON (oh.order_status_id=os.order_status_id && oh.order_status_id=5)";
	$sql.="WHERE os.language_id=2  AND ".$where." o.date_added  BETWEEN '".$yeardeb."-".$monthdeb."-01 00:00:00' AND '".$yearfin."-".$monthfin."-31 23:59:59' order by o.date_added";
	//echo $sql;
	$req = mysqli_query($db,$sql); 
	$i=0;
	$sum=0;
	while($result= mysqli_fetch_assoc($req)) {
		$sql2="SELECT * FROM `oc_order_total` ot WHERE code='sub_total' AND (ot.order_id=".$result['order_id'].")";
		//echo $sql2;
		$req2 = mysqli_query($db,$sql2); 
		while($result2= mysqli_fetch_assoc($req2)){
			if($where=="currency_code='CAD' AND "){
				$sum+=($result2['value']*1.34);
			}else{
				$date_tmp=explode(" ",$result['date_transaction']);
				$date_info=$date_tmp[0];
				$rate=get_fxrate($date_info,$db);
				$sum+=($rate*$result2['value']);
			}
		}
	}
	//print("<pre>".print_r ($return,true )."</pre>");
	return $sum;
}
function get_transaction($yeardeb,$yearfin,$monthdeb,$monthfin,$from,$where,$db){

	$return=array();
	$sql="SELECT * FROM `admin_".$from."` ";
	$sql.="WHERE ".$where." date_transaction BETWEEN '".$yeardeb."-".$monthdeb."-01 00:00:00' AND '".$yearfin."-".$monthfin."-31 23:59:59' order by date_transaction ";
	//echo $sql;
	$req = mysqli_query($db,$sql); 
	$i=0;
	while($result= mysqli_fetch_assoc($req)) {
        $return[$i] = $result;
		$i++;
	}
	//print("<pre>".print_r ($return,true )."</pre>");
	return $return;
}
function get_website_transaction($yeardeb,$yearfin,$monthdeb,$monthfin,$where,$db){

	$return=array();
	$sql="SELECT *,o.date_added as date_transaction FROM `oc_order` o LEFT JOIN `oc_order_history` oh ON (oh.order_id=o.order_id) LEFT JOIN `oc_order_status` os ON (oh.order_status_id=os.order_status_id && oh.order_status_id=5)";
	$sql.="WHERE os.language_id=2  AND ".$where." o.date_added  BETWEEN '".$yeardeb."-".$monthdeb."-01 00:00:00' AND '".$yearfin."-".$monthfin."-31 23:59:59' order by o.date_added";
	//echo $sql;
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
	//print("<pre>".print_r ($return,true )."</pre>");
	return $return;
}

function insert_currency_exchange($db){
	$uploads_dir = 'admin/interne/admin/data';
	$dir_name="data/FXUSDCAD.json";
	$string = file_get_contents($dir_name);
	if ($string === false) {
		echo "erreur";
	}

	$currencies = json_decode($string, true);
	if ($currencies === null) {
		echo "erreur2";
	}
	//print("<pre>".print_r ($currencies['observations'],true )."</pre>");
 	foreach ($currencies['observations'] as $currency) {
		//echo $currency['d']."<br>";
		//echo $currency['FXUSDCAD']['v']."<br>";
		$sql = 'INSERT INTO `admin_fxusdcad` (`date_info`, `rate`) ';
		$sql = $sql.'VALUES ( "'.$currency['d'].'", "'.$currency['FXUSDCAD']['v'].'")';
		$req = mysqli_query($db,$sql); 
	} 
}
function ajustement_transaction($db){
	$orders= get_website_transaction(2017,2022,01,02,"payment_code!='pp_standard' AND payment_code!='squareup' AND payment_code!='virtualmerchant' AND ",$db);
	$i=0;
	$j=0;
	foreach($orders as $order){
		if(isset($order['mdiscount']['value']) && number_format($order['tax']['value']-(($order['sub_total']['value']+$order['mdiscount']['value'])*.14975),2)<0){
			echo "ORDER NU:".$order['order_id']."<br>Total:".number_format(($order['sub_total']['value']),2)." Discount:".number_format(($order['mdiscount']['value']),2)." Tax:".number_format(($order['tax']['value']),2)." Total:".number_format(($order['total']['value']),2).
			"  <br>-------".number_format(($order['sub_total']['value']),2)." Discount:".number_format(($order['mdiscount']['value']),2)." Tax:".number_format((($order['sub_total']['value']+$order['mdiscount']['value'])*.14975),2)." Total:".number_format((($order['sub_total']['value']+$order['mdiscount']['value'])*1.14975),2)."<br><br>";
			//print("<pre>".print_r ($order,true )."</pre>");
			$j++;
		}elseif(isset($order['mdiscount']['value']) && number_format($order['tax']['value']-(($order['sub_total']['value']+$order['mdiscount']['value'])*.14975),2)>0){
			echo "***<br>Total:".number_format(($order['sub_total']['value']),2)." Discount:".number_format(($order['mdiscount']['value']),2)." Tax:".number_format(($order['tax']['value']),2)." Total:".number_format(($order['total']['value']),2).
			"  <br>-------".number_format(($order['sub_total']['value']),2)." Discount:".number_format(($order['mdiscount']['value']),2)." Tax:".number_format((($order['sub_total']['value']+$order['mdiscount']['value'])*.14975),2)." Total:".number_format((($order['sub_total']['value']+$order['mdiscount']['value'])*1.14975),2)."<br>";
			//print("<pre>".print_r ($order,true )."</pre>");
				$sql="UPDATE `oc_order_total` SET `value` = '".(($order['sub_total']['value']+$order['mdiscount']['value'])*.14975)."' WHERE `oc_order_total`.`order_id` = '".$order['order_id']."' AND `oc_order_total`.`code` ='tax'";
				echo "  <br>-------".$sql;
				//$req = mysqli_query($db,$sql); 
				$sql="UPDATE `oc_order_total` SET `value` = '".(($order['sub_total']['value']+$order['mdiscount']['value'])*1.14975)."' WHERE `oc_order_total`.`order_id` = '".$order['order_id']."' AND `oc_order_total`.`code` ='total'";
				echo "  <br>-------".$sql."";
				//$req = mysqli_query($db,$sql); 
				$sql="INSERT `oc_order_total` SET `value` = '".$order['tax']['value']."',`order_id` = '".$order['order_id']."',`code` ='tax_old',`title` ='tax_old',sort_order='9'";
				echo "  <br>-------".$sql."<br><br>";
				//$req = mysqli_query($db,$sql); 
			$i++;
		}
		
	}
	echo "nb a modifier:".$i;
	echo "<br>nb a verifier:".$j;
}

function get_fxrate($date,$db){

	$return=array();
	$sql="SELECT * FROM `admin_fxusdcad` ";
	$sql.="WHERE date_info <='".$date."' order by date_info DESC limit 1";
	//echo $sql;
	$req = mysqli_query($db,$sql); 
	$result= mysqli_fetch_assoc($req);
	//print("<pre>".print_r ($return,true )."</pre>");
	return $result['rate']*1.04;
}


?>