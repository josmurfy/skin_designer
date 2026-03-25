<? 
//echo (string)$_POST['sku'] ;
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';

$post = '<?xml version="1.0" encoding="utf-8"?>
<GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
        <eBayAuthToken>AgAAAA**AQAAAA**aAAAAA**kVv0YQ**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AHkIKmAJWAowSdj6x9nY+seQ**Cz4GAA**AAMAAA**djnbCtzpxegQw91AW0Z88mjXID6FJGInAbgAo43hm3OkU+PYTd1PcUB12eSLOX10ABFkgKC7dBGXjuBwGrfovRyGcSNg1xePTSQKHMKIFSddar1M91r7hoTaXeVXqUZcFKwLj5gJRw7SCeE3UnD/dJPJXtf3fcXd+w5ygiTlXpw3yA7PrOlZBFkvUAZ8by7gxRoJEpt4RRgm0UJ1zE7APE6xPruXWNTUP3k6Eh7j0rOXQYH+PEtAnXVj6k6TznGCSDI4LJXeTCGiNAxUlqRk9AkJNwYwS2r7YgX8Zi8rXiIjUb9zdo9u0e5WPF9P+NPWrjky3LIadsx/GDh8VbQj2nbFShZpSAfyl7/PXbhsNmyL4601wsUFC3oV1+L87ougZYnsfQVrRUZp2PO+HehonOGsDMoe6S+sA3iyjaYDiPA9GU7JK2mnd7KH7tXRwlu66lMnot2l3gTl7aWyIrLJCnWdZlx0EX3IMZb4F+Bsf3DFHCRpwDzgoovVY+fwbKjchvAscg0lkGFZVWhtMw588ckJcol9EHDd2ErHNaFS1ux7bElbgxTw9u9Hp6TEqdejfa2x+O1rR26Je5dp0Rr/cSOG2iVfBYGv9NYVCFc16xh/47hiV/QE9zJCAq5IhQzA/sjnV5BJ1hYgifB+2ANNPPUwyEOIzazC01cH6ML5WXlhc9CUMM9JwIpvPBzVEsVeOvOqWmRqHw3eMx5CIm6cfvB44O2dbjiDJmcI4Jdtxpd6F39J7e90DDxTAc8eRAnR</eBayAuthToken>
    </RequesterCredentials>
    <ErrorLanguage>en_US</ErrorLanguage>
  <WarningLevel>High</WarningLevel>
  <GranularityLevel>Coarse</GranularityLevel> 
  <EndTimeFrom>2022-02-25</EndTimeFrom> 
  <EndTimeTo>2022-02-27</EndTimeTo> 
 
  <IncludeWatchCount>true</IncludeWatchCount> 
  <Pagination> 
    <EntriesPerPage>200</EntriesPerPage> 
  </Pagination> 
</GetSellerListRequest>';/* <SKUArray><SKU>23094</SKU>
</SKUArray>*/
$headers = array(
            "X-EBAY-API-COMPATIBILITY-LEVEL: 1193",
            "X-EBAY-API-DEV-NAME: 73b8492a-f471-4170-86b8-ce9e6e2d6796",
            "X-EBAY-API-APP-NAME: 73b8492a-f471-4170-86b8-ce9e6e2d6796",
            "X-EBAY-API-CERT-NAME: PRD-f78dd8ce63e4-212d-4ac1-8aa3-d2ad",
            "X-EBAY-API-CALL-NAME: GetSellerList",
            "X-EBAY-API-SITEID: 0" // 3 for UK 
);
//print("<pre>".print_r ($post,true )."</pre>");
$connection = curl_init();
curl_setopt($connection, CURLOPT_URL, "https://api.ebay.com/ws/api.dll");
curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
curl_setopt($connection, CURLOPT_POST, 1);
curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1); 
$response = curl_exec($connection);
 
 //echo "allo";
$xml = new SimpleXMLElement($response);
//$xml->account_id=$customer;
//	//print("<pre>".print_r ($xml,true )."</pre>");
$xml->marketplace_account_id=$account_query['id'];
//echo $xml['account_id'];
/*$xml1 = json_encode($xml); // convert the XML string to JSON
$xml= json_decode($xml1,TRUE);*/

        //echo json_encode($new); 
$nbpages=$xml->PaginationResult->TotalNumberOfPages;//$xml->PaginationResult->TotalNumberOfPages;
$j=0;
     for($i=1;$i<=$nbpages;$i++){
        $post = '<?xml version="1.0" encoding="utf-8"?>
        <GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">
        <RequesterCredentials>
                <eBayAuthToken>AgAAAA**AQAAAA**aAAAAA**kVv0YQ**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AHkIKmAJWAowSdj6x9nY+seQ**Cz4GAA**AAMAAA**djnbCtzpxegQw91AW0Z88mjXID6FJGInAbgAo43hm3OkU+PYTd1PcUB12eSLOX10ABFkgKC7dBGXjuBwGrfovRyGcSNg1xePTSQKHMKIFSddar1M91r7hoTaXeVXqUZcFKwLj5gJRw7SCeE3UnD/dJPJXtf3fcXd+w5ygiTlXpw3yA7PrOlZBFkvUAZ8by7gxRoJEpt4RRgm0UJ1zE7APE6xPruXWNTUP3k6Eh7j0rOXQYH+PEtAnXVj6k6TznGCSDI4LJXeTCGiNAxUlqRk9AkJNwYwS2r7YgX8Zi8rXiIjUb9zdo9u0e5WPF9P+NPWrjky3LIadsx/GDh8VbQj2nbFShZpSAfyl7/PXbhsNmyL4601wsUFC3oV1+L87ougZYnsfQVrRUZp2PO+HehonOGsDMoe6S+sA3iyjaYDiPA9GU7JK2mnd7KH7tXRwlu66lMnot2l3gTl7aWyIrLJCnWdZlx0EX3IMZb4F+Bsf3DFHCRpwDzgoovVY+fwbKjchvAscg0lkGFZVWhtMw588ckJcol9EHDd2ErHNaFS1ux7bElbgxTw9u9Hp6TEqdejfa2x+O1rR26Je5dp0Rr/cSOG2iVfBYGv9NYVCFc16xh/47hiV/QE9zJCAq5IhQzA/sjnV5BJ1hYgifB+2ANNPPUwyEOIzazC01cH6ML5WXlhc9CUMM9JwIpvPBzVEsVeOvOqWmRqHw3eMx5CIm6cfvB44O2dbjiDJmcI4Jdtxpd6F39J7e90DDxTAc8eRAnR</eBayAuthToken>
            </RequesterCredentials>
            <ErrorLanguage>en_US</ErrorLanguage>
        <WarningLevel>High</WarningLevel>
        <GranularityLevel>Coarse</GranularityLevel> 
        <EndTimeFrom>2022-01-01</EndTimeFrom> 
        <EndTimeTo>2022-03-01</EndTimeTo> 
        
        <IncludeWatchCount>true</IncludeWatchCount> 
        <Pagination> 
            <EntriesPerPage>200</EntriesPerPage> 
            <PageNumber>'.$i.'</PageNumber>
        </Pagination> 
        </GetSellerListRequest>';/* <SKUArray><SKU>23094</SKU>
        </SKUArray>*/
        $headers = array(
                    "X-EBAY-API-COMPATIBILITY-LEVEL: 1193",
                    "X-EBAY-API-DEV-NAME: 73b8492a-f471-4170-86b8-ce9e6e2d6796",
                    "X-EBAY-API-APP-NAME: 73b8492a-f471-4170-86b8-ce9e6e2d6796",
                    "X-EBAY-API-CERT-NAME: PRD-f78dd8ce63e4-212d-4ac1-8aa3-d2ad",
                    "X-EBAY-API-CALL-NAME: GetSellerList",
                    "X-EBAY-API-SITEID: 0" // 3 for UK 
        );
        //print("<pre>".print_r ($post,true )."</pre>");
        $connection = curl_init();
        curl_setopt($connection, CURLOPT_URL, "https://api.ebay.com/ws/api.dll");
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($connection, CURLOPT_POST, 1);
        curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1); 
        $response = curl_exec($connection);
        
        //echo "allo";
        $xml = new SimpleXMLElement($response);
        /*$xml1 = json_encode($xml); // convert the XML string to JSON
        $xml= json_decode($xml1,TRUE);*/
        //print("<pre>".print_r ($xml,true )."</pre>");
        foreach($xml->ItemArray->Item as $item){
           //print("<pre>".print_r ($item,true )."</pre>");
		   if($item->ListingDetails->RelistedItemID==""){
				$sql = 'UPDATE `oc_product` set ebay_id_old = "'.$item->ItemID.'",
				ebay_id_relisted = "0",ebay_last_check="2020-09-01" where product_id="'.$item->SKU.'"';
				echo $sql.'<br><br>';
				$j++;
				
		   }else{
			   $sql = 'UPDATE `oc_product` set ebay_id_old = "'.$item->ItemID.'",
				ebay_id_relisted = "'.$item->ListingDetails->RelistedItemID.'",ebay_last_check="2020-09-01", ebay_count=ebay_count+1 where product_id="'.$item->SKU.'"';
				echo $sql.'<br><br>';
		   }
		   $req = mysqli_query($db,$sql);
        }
    }
	echo "<br>Nb a relister:".$j;
mysqli_close($db);
?>


