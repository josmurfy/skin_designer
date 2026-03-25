<?
$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$','n7f9655_phoenixliquidation');
include_once 'function.php';
include_once 'variable.php'; 
$servername = "127.0.0.1";
$username = "'n7f9655_n7f9655";
$password = "jnthngrvs01$$";
$dbname = "n7f9655_phoenixliquidation";
// phoenixliquidation
$sql2 = "SELECT * FROM oc_marketplace_accounts";
$req2 = mysqli_query($db,$sql2); 
$data2 = mysqli_fetch_assoc($req2);

$connectionapi=array();
//$connectionapi['EBAYTOKEN']='AgAAAA**AQAAAA**aAAAAA**mzpxXg**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AHkIKmAJWAowSdj6x9nY+seQ**Cz4GAA**AAMAAA**hBmRP25hzAW+2yC1y07QV4jwW1/hbujM8pu01RSk9vCEEaDaeV5VTffsq2jzl/EwkEefepZiS+KJJykSN14Bq0m6BT6a3qsHdzn063/nlmjdx/M0+E/ZrtTqAx2gK4KP3Y0zm4pf9K+J2aRnYN3gMFEyJEuMyn0KPy8OTFJqxsZIO0mIWr98uysMj6D14yzR6saGmRQkuOvZ/sJaHbp3ljq7GbF5wF4Cyrwys4xRaT1KHFq6Trw5WqLdkvEn1K6KRxSvvZYlvZQW0IKbAlZ8cAXUewEgmOVABH8QP3xpODGMoKg5cqmz44RgULfm/e3JGx5gZ7ZT0sd0pWdOf7fHzVQqmmMuhGSNmpyx+rxu4eCM0q2Ssm4hMGyJd/bXmNJ7eIkOLacCQqg0Sw3SFyjaWUANdVcdIU/MYmk4c7fp3x9K0px5Yukez/Im6wm23LjkK4bjwK8w+2bcQAMA6CSAY5SoTSPt5QF7tOPqySMhnFJmTwHV+2OU6ImlClWc+nm+X6iwrp5yZZqHkMWvTU6Es/UQQmoKfZcHPu0um09ZLJs/U89bNn3VpO2T2hVCb3qjE4IAkr7ZphS9ucVkRo5lsO9xsP60G3qQhHsQ+sO2aPf9tZr+B2quYLAyA62dkskpbkychqiqL5g50ZL2No3RavrDHA/20erygkUNBgL5RFpZJzYmPFIH3m6DXItIVGllZrnadnPqce4pqb7JbZn/9y5XTsQ1Dui1Cnc/7qPy7wIPPjh7zyvgKwLzWMPIDml1';
//$connectionapi['EBAYTOKEN']='AgAAAA**AQAAAA**aAAAAA**0Y/RXw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AHkIKmAJWAowSdj6x9nY+seQ**Cz4GAA**AAMAAA**hBmRP25hzAW+2yC1y07QV4jwW1/hbujM8pu01RSk9vCEEaDaeV5VTffsq2jzl/EwkEefepZiS+KJJykSN14Bq0m6BT6a3qsHdzn063/nlmjdx/M0+E/ZrtTqAx2gK4KP3Y0zm4pf9K+J2aRnYN3gMFEyJEuMyn0KPy8OTFJqxsZIO0mIWr98uysMj6D14yzR6saGmRQkuOvZ/sJaHbp3ljq7GbF5wF4Cyrwys4xRaT1KHFq6Trw5WqLdkvEn1K6KRxSvvZYlvZQW0IKbAlZ8cAXUewEgmOVABH8QP3xpODGMoKg5cqmz44RgULfm/e3JGx5gZ7ZT0sd0pWdOf7fHzVQqmmMuhGSNmpyx+rxu4eCM0q2Ssm4hMGyJd/bXmNJ7eIkOLacCQqg0Sw3SFyjaWUANdVcdIU/MYmk4c7fp3x9K0px5Yukez/Im6wm23LjkK4bjwK8w+2bcQAMA6CSAY5SoTSPt5QF7tOPqySMhnFJmTwHV+2OU6ImlClWc+nm+X6iwrp5yZZqHkMWvTU6Es/UQQmoKfZcHPu0um09ZLJs/U89bNn3VpO2T2hVCb3qjE4IAkr7ZphS9ucVkRo5lsO9xsP60G3qQhHsQ+sO2aPf9tZr+B2quYLAyA62dkskpbkychqiqL5g50ZL2No3RavrDHA/20erygkUNBgL5RFpZJzYmPFIH3m6DXItIVGllZrnadnPqce4pqb7JbZn/9y5XTsQ1Dui1Cnc/7qPy7wIPPjh7zyvgKwLzWMPIDml1';
//						       AgAAAA**AQAAAA**aAAAAA**X2quYA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AHkIKmAJWAowSdj6x9nY+seQ**Cz4GAA**AAMAAA**djnbCtzpxegQw91AW0Z88mjXID6FJGInAbgAo43hm3OkU+PYTd1PcUB12eSLOX10ABFkgKC7dBGXjuBwGrfovRyGcSNg1xePTSQKHMKIFSddar1M91r7hoTaXeVXqUZcFKwLj5gJRw7SCeE3UnD/dJPJXtf3fcXd+w5ygiTlXpw3yA7PrOlZBFkvUAZ8by7gxRoJEpt4RRgm0UJ1zE7APE6xPruXWNTUP3k6Eh7j0rOXQYH+PEtAnXVj6k6TznGCSDI4LJXeTCGiNAxUlqRk9AkJNwYwS2r7YgX8Zi8rXiIjUb9zdo9u0e5WPF9P+NPWrjky3LIadsx/GDh8VbQj2nbFShZpSAfyl7/PXbhsNmyL4601wsUFC3oV1+L87ougZYnsfQVrRUZp2PO+HehonOGsDMoe6S+sA3iyjaYDiPA9GU7JK2mnd7KH7tXRwlu66lMnot2l3gTl7aWyIrLJCnWdZlx0EX3IMZb4F+Bsf3DFHCRpwDzgoovVY+fwbKjchvAscg0lkGFZVWhtMw588ckJcol9EHDd2ErHNaFS1ux7bElbgxTw9u9Hp6TEqdejfa2x+O1rR26Je5dp0Rr/cSOG2iVfBYGv9NYVCFc16xh/47hiV/QE9zJCAq5IhQzA/sjnV5BJ1hYgifB+2ANNPPUwyEOIzazC01cH6ML5WXlhc9CUMM9JwIpvPBzVEsVeOvOqWmRqHw3eMx5CIm6cfvB44O2dbjiDJmcI4Jdtxpd6F39J7e90DDxTAc8eRAnR
$connectionapi['EBAYTOKEN']=$data2['auth_token'];//'AgAAAA**AQAAAA**aAAAAA**Gd6SYA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AHkIKmAJWAowSdj6x9nY+seQ**Cz4GAA**AAMAAA**djnbCtzpxegQw91AW0Z88mjXID6FJGInAbgAo43hm3OkU+PYTd1PcUB12eSLOX10ABFkgKC7dBGXjuBwGrfovRyGcSNg1xePTSQKHMKIFSddar1M91r7hoTaXeVXqUZcFKwLj5gJRw7SCeE3UnD/dJPJXtf3fcXd+w5ygiTlXpw3yA7PrOlZBFkvUAZ8by7gxRoJEpt4RRgm0UJ1zE7APE6xPruXWNTUP3k6Eh7j0rOXQYH+PEtAnXVj6k6TznGCSDI4LJXeTCGiNAxUlqRk9AkJNwYwS2r7YgX8Zi8rXiIjUb9zdo9u0e5WPF9P+NPWrjky3LIadsx/GDh8VbQj2nbFShZpSAfyl7/PXbhsNmyL4601wsUFC3oV1+L87ougZYnsfQVrRUZp2PO+HehonOGsDMoe6S+sA3iyjaYDiPA9GU7JK2mnd7KH7tXRwlu66lMnot2l3gTl7aWyIrLJCnWdZlx0EX3IMZb4F+Bsf3DFHCRpwDzgoovVY+fwbKjchvAscg0lkGFZVWhtMw588ckJcol9EHDd2ErHNaFS1ux7bElbgxTw9u9Hp6TEqdejfa2x+O1rR26Je5dp0Rr/cSOG2iVfBYGv9NYVCFc16xh/47hiV/QE9zJCAq5IhQzA/sjnV5BJ1hYgifB+2ANNPPUwyEOIzazC01cH6ML5WXlhc9CUMM9JwIpvPBzVEsVeOvOqWmRqHw3eMx5CIm6cfvB44O2dbjiDJmcI4Jdtxpd6F39J7e90DDxTAc8eRAnR';
//$connectionapi['EBAYTOKEN']='v^1.1#i^1#I^3#r^1#p^3#f^0#t^Ul4xMF82OjFDQjZFNjU1RTE2NzdEM0Y0QzExRDhFMDg5OEVBNUY1XzNfMSNFXjI2MA==';

$connectionapi['EBAYAPIDEVNAME']=$data2['application_id'];//'73b8492a-f471-4170-86b8-ce9e6e2d6796'; 
$connectionapi['EBAYAPIAPPNAME']=$data2['developer_id'];//'73b8492a-f471-4170-86b8-ce9e6e2d6796';
$connectionapi['APICERTNAME']=$data2['certification_id'];//'PRD-f78dd8ce63e4-212d-4ac1-8aa3-d2ad';

if (!isset($_COOKIE['bearer_token'])) {
	// Fonction personnalisée pour rafraîchir le token d'accès (définissez cette fonction ailleurs dans votre code)
//	$newAccessTokenData = refreshAccessToken($connectionapi['EBAYTOKEN']);

	// Si un nouveau bearer_token est obtenu, le stocker dans un cookie
	if (isset($newAccessTokenData['bearer_token'])) {
		// Définir le cookie avec une expiration de 2 heures
		setcookie('bearer_token', $newAccessTokenData['bearer_token'], time() + 7200, "/"); 
		// Mettre à jour le bearer token dans le tableau $connectionapi
		$connectionapi['bearer_token'] = $newAccessTokenData['bearer_token'];
	}
} else {
	// Utiliser le bearer_token déjà présent dans les cookies
	$connectionapi['bearer_token'] = $_COOKIE['bearer_token'];
}
$connectionapi['APICLIENTID']='CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28';//'PRD-f78dd8ce63e4-212d-4ac1-8aa3-d2ad';
$connectionapi['APICLIENTSECRET']='PRD-93ff3ada979d-7fcf-4938-be46-ba89';//'PRD-f78dd8ce63e4-212d-4ac1-8aa3-d2ad';
$connectionapi['APIEBAYURL']='https://api.ebay.com/ws/api.dll';


// UPS
$connectionapi['APIUPSURL']='https://www.ups.com/ups.app/xml/Rate'; 
$connectionapi['APIUPSTOKEN']='DD9F9AE20FFC7DD5';
$connectionapi['APIUPSUSERID']='jonathangervais';
$connectionapi['APIUPSPASSWORD']='jnthngrvs01$$'; 
//$connectionapi['API-UPS-PASSWORD']='Bonjour01$$$$';

//USPS
$connectionapi['APIUSPSURL']='https://secure.shippingapis.com/ShippingApi.dll?';
$connectionapi['APIUSPSUSERID']='209PHOEN3821';

$GLOBALS['SITE_ROOT']='/home/n7f9655/public_html/phoenixliquidation/';
$GLOBALS['NAME_CIE']='PhoenixLiquidation';
$GLOBALS['WEBSITE']= 'https://phoenixliquidation.ca/';
$GLOBALS['DESC1']='<p><b>PhoenixLiquidation </b>is a business based in USA and in CANADA that resells products acquired from liquidation center, primarily to American and Canadian buyers. </p>
					<p><b>OUR GOAL: </b><br>Offer very good products, sold at the BEST PRICE and thus make you happy!</p>
					<p>We resell <b>NEW</b> and <b>OPENBOX</b> products.</p>
					<p><b>NEW products:</b> Our new products are sealed and have been removed from supermarkets for several reasons:<br>
					- Discontinued items or end of line.<br>
					- Replaced by a new model.<br>
					- Packaging or retail box was embossed or damaged.<br>
					- And most of the case, returned because the customer had changed \'idea.<br><br>
					<b>OPENBOX:</b> All of our OPENBOX products have been removed from supermarkets for several returns reasons:<br>
					- From a customer who has changed their mind but the retail box has been opened or missing. In that case we sell NEW(Openbox).<br>
					- From the customer who declared the product non-functional but is NOT. Sometimes the client doesn\'t know how to use it or is too embarrassed to tell the real reason.<br>
					- From the customer who declared the product damaged and it is. We do NOT resell damaged products unless we write it down in the auction.<br>
					<b>All of our OPENBOX products are fully functional. </b>We <b>test them</b>, <b>clean</b> them and <b>sanitize</b> them. <b>ALWAYS!</b><br>
					</p>
					<p>
					If you receive a non-functional product. This product will be replaced or refunded at our expense if you contact us within 30 days of the eBay Money Back Guarantee. It is <b>GUARANTEE!</b>
					</p>
					';

