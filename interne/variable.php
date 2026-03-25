<?php

$_GET['action']=isset($_GET['action'])?$_GET['action']:'';
$_GET['clone']=isset($_GET['clone'])?$_GET['clone']:'';
$_GET['product_id_cloner']=isset($_GET['product_id_cloner'])?$_GET['product_id_cloner']:'';
$_GET['product_id']=isset($_GET['product_id'])?$_GET['product_id']:'';
$_GET['insert']=isset($_GET['insert'])?$_GET['insert']:'';
$_GET['sku']=isset($_GET['sku'])?$_GET['sku']:'';
$_GET['lien_a_cloner']=isset($_GET['lien_a_cloner'])?$_GET['lien_a_cloner']:''; 
 
if (isset($_GET['product_id']) && $_GET['product_id']!="" && !isset($_POST['product_id'])){
	$_POST['product_id'] =$_GET['product_id'];	
}

if (isset($_GET['sku']) && $_GET['sku']!=""){
	(string)$_POST['sku'] =$_GET['sku'];
	 
//echo "allo".$_GET['clone'];
}
//echo $_GET['clone'];
if (isset($_GET['sku']) && $_GET['sku']!="" && isset($_GET['category_id']) && $_GET['category_id']!=""){
	(string)$_POST['sku'] =$_GET['sku'];
	$_POST['category_id']=$_GET['category_id'];
	//echo "allo";
}

if(isset($_GET['hsec'])){
	$_POST['hsec']=$_GET['hsec'];
	$_POST['hmin']=$_GET['hmin'];
	$_POST['hhour']=$_GET['hhour'];
}elseif(!isset($_POST['hsec'])){
	$_POST['hsec']=0;
	$_POST['hmin']=0;
	$_POST['hhour']=0;
}

// Vérification des clés de $_POST et définition des valeurs par défaut en ordre alphabétique
$_POST['accessoryen'] = isset($_POST['accessoryen']) ? $_POST['accessoryen'] : '';
$_POST['accessoryfr'] = isset($_POST['accessoryfr']) ? $_POST['accessoryfr'] : '';
$_POST['action'] = isset($_POST['action']) ? $_POST['action'] : '';
$_POST['brand'] = isset($_POST['brand']) ? $_POST['brand'] : '';
$_POST['categoriecheck'] = isset($_POST['categoriecheck']) ? $_POST['categoriecheck'] : '';
$_POST['categoryname'] = isset($_POST['categoryname']) ? $_POST['categoryname'] : '';
$_POST['changeupc'] = isset($_POST['changeupc']) ? $_POST['changeupc'] : '';
$_POST['clone'] = isset($_POST['clone']) ? $_POST['clone'] : '';
$_POST['sourcecode'] = isset($_POST['sourcecode']) ? $_POST['sourcecode'] : '';
$_POST['color_anc'] = isset($_POST['color_anc']) ? $_POST['color_anc'] : '';
$_POST['color_item'] = isset($_POST['color_item']) ? $_POST['color_item'] : '';
$_POST['colorcheck'] = isset($_POST['colorcheck']) ? $_POST['colorcheck'] : '';
$_POST['coloren'] = isset($_POST['coloren']) ? $_POST['coloren'] : '';
$_POST['colorfr'] = isset($_POST['colorfr']) ? $_POST['colorfr'] : '';
$_POST['condition_id'] = isset($_POST['condition_id']) && is_numeric($_POST['condition_id']) ? $_POST['condition_id'] : 9;
$_POST['condition_name'] = isset($_POST['condition_name']) ? $_POST['condition_name'] : '';
$_POST['condition_suppen'] = isset($_POST['condition_suppen']) ? $_POST['condition_suppen'] : '';
$_POST['condition_suppfr'] = isset($_POST['condition_suppfr']) ? $_POST['condition_suppfr'] : '';
$_POST['conditioncheck'] = isset($_POST['conditioncheck']) ? $_POST['conditioncheck'] : '';
$_POST['createlabel'] = isset($_POST['createlabel']) ? $_POST['createlabel'] : '';
$_POST['date_price_upd_ps'] = isset($_POST['date_price_upd_ps']) ? $_POST['date_price_upd_ps'] : '';
$_POST['description_suppen'] = isset($_POST['description_suppen']) ? $_POST['description_suppen'] : '';
$_POST['description_suppfr'] = isset($_POST['description_suppfr']) ? $_POST['description_suppfr'] : '';
$_POST['dimensioncheck'] = isset($_POST['dimensioncheck']) ? $_POST['dimensioncheck'] : '';
$_POST['marketplace_item_id'] = isset($_POST['marketplace_item_id']) ? $_POST['marketplace_item_id'] : 0;
$_POST['ebay_id_a_cloner'] = isset($_POST['ebay_id_a_cloner']) ? $_POST['ebay_id_a_cloner'] : '';
$_POST['ebayinputarbonum'] = isset($_POST['ebayinputarbonum']) ? $_POST['ebayinputarbonum'] : '';
$_POST['englishcheck'] = isset($_POST['englishcheck']) ? $_POST['englishcheck'] : '';
$_POST['etat'] = isset($_POST['etat']) ? $_POST['etat'] : '';
$_POST['findprice'] = isset($_POST['findprice']) ? $_POST['findprice'] : 0;
$_POST['findshipping']= isset($_POST['findshipping']) ? $_POST['findshipping'] : 0;
$_POST['frenchcheck'] = isset($_POST['frenchcheck']) ? $_POST['frenchcheck'] : '';
$_POST['hid_sku_ancien'] = isset($_POST['hid_sku_ancien']) ? $_POST['hid_sku_ancien'] : '';
$_POST['hmin'] = isset($_POST['hmin']) ? $_POST['hmin'] : 0;
$_POST['hhour'] = isset($_POST['hhour']) ? $_POST['hhour'] : 0;
$_POST['hsec'] = isset($_POST['hsec']) ? $_POST['hsec'] : 0;
$_POST['image'] = isset($_POST['image']) ? $_POST['image'] : '';
$_POST['imageprincipale'] = isset($_POST['imageprincipale']) ? $_POST['imageprincipale'] : '';
$_POST['infosuppcheck'] = isset($_POST['infosuppcheck']) ? $_POST['infosuppcheck'] : '';
$_POST['invoice'] = isset($_POST['invoice']) ? $_POST['invoice'] : '';
$_POST['lien_a_cloner']= isset($_POST['lien_a_cloner']) ? $_POST['lien_a_cloner'] : '';
$_POST['location'] = isset($_POST['location']) ? $_POST['location'] : '';
$_POST['manufacturer_id'] = isset($_POST['manufacturer_id']) && is_numeric($_POST['manufacturer_id']) ? $_POST['manufacturer_id'] : 0;
$_POST['manufacturercheck'] = isset($_POST['manufacturercheck']) ? $_POST['manufacturercheck'] : '';
$_POST['manufacturersupp'] = isset($_POST['manufacturersupp']) ? $_POST['manufacturersupp'] : '';
$_POST['manufacturer_recom'] = isset($_POST['manufacturer_recom']) ? $_POST['manufacturer_recom'] : '';
$_POST['model'] = isset($_POST['model']) ? $_POST['model'] : 'None';
$_POST['modelcheck'] = isset($_POST['modelcheck']) ? $_POST['modelcheck'] : '';
$_POST['name'] = isset($_POST['name']) ? $_POST['name'] : '';
$_POST['nameen'] = isset($_POST['nameen']) ? $_POST['nameen'] : '';
$_POST['namefr'] = isset($_POST['namefr']) ? $_POST['namefr'] : '';
$_POST['new'] = isset($_POST['new']) ? $_POST['new'] : 1;
$_POST['new_ebay_id'] = isset($_POST['new_ebay_id']) ? $_POST['new_ebay_id'] : '';
$_POST['new_ebay_listing'] = isset($_POST['new_ebay_listing']) ? $_POST['new_ebay_listing'] : '';
$_POST['ebay_id_refer'] = isset($_POST['ebay_id_refer']) ? $_POST['ebay_id_refer'] : '';
$_POST['openpageprix'] = isset($_POST['openpageprix']) ? $_POST['openpageprix'] : 0;
$_POST['pas_ebay'] = isset($_POST['pas_ebay']) ? $_POST['pas_ebay'] : '';
$_POST['pas_upc'] = isset($_POST['pas_upc']) ? $_POST['pas_upc'] : '';
$_POST['poidscheck'] = isset($_POST['poidscheck']) ? $_POST['poidscheck'] : '';
$_POST['pourverification'] = isset($_POST['pourverification']) ? $_POST['pourverification'] : '';
$_POST['price'] = isset($_POST['price']) ? $_POST['price'] : 0.0;
$_POST['price_with_shipping'] = isset($_POST['price_with_shipping']) ? $_POST['price_with_shipping'] : 9999;
$_POST['price_with_shippingshipping']= isset($_POST['price_with_shippingshipping']) && is_numeric($_POST['price_with_shippingshipping']) ? $_POST['price_with_shippingshipping'] : 0;
$_POST['pricecad'] = isset($_POST['pricecad']) ? $_POST['pricecad'] : 0;
$_POST['pricecadtmp'] = isset($_POST['pricecadtmp']) ? $_POST['pricecadtmp'] : 0;
$_POST['priceebaycheck'] = isset($_POST['priceebaycheck']) ? $_POST['priceebaycheck'] : '';
$_POST['pricedetailcheck'] = isset($_POST['pricedetailcheck']) ? $_POST['pricedetailcheck'] : ''; 
$_POST['priceebaynow'] = isset($_POST['priceebaynow']) ? $_POST['priceebaynow'] : '';
$_POST['priceebaysold'] = isset($_POST['priceebaysold']) ? $_POST['priceebaysold'] : 0.0;
$_POST['priceterasold'] = isset($_POST['priceterasold']) ? $_POST['priceterasold'] : '';
$_POST['priceusd'] = isset($_POST['priceusd']) ? $_POST['priceusd'] : 0;
$_POST['priceusdtmp'] = isset($_POST['priceusdtmp']) ? $_POST['priceusdtmp'] : 0;
$_POST['processing'] = isset($_POST['processing']) ? $_POST['processing'] : ''; 
$_POST['product_id'] = isset($_POST['product_id']) ? $_POST['product_id'] : 0; 
$_POST['product_id_no'] = isset($_POST['product_id_no']) ? $_POST['product_id_no'] : '';
$_POST['product_id_r'] = isset($_POST['product_id_r']) ? $_POST['product_id_r'] : '';
$_POST['product_id_cloner'] = isset($_POST['product_id_cloner']) ? $_POST['product_id_cloner'] : '';
$_POST['quantityentrepot_ajouter'] = isset($_POST['quantityentrepot_ajouter']) ? $_POST['quantityentrepot_ajouter'] : 0;
$_POST['quantityinventaire'] = isset($_POST['quantityinventaire']) ? $_POST['quantityinventaire'] : 0;
$_POST['quantitymagasin_ajouter'] = isset($_POST['quantitymagasin_ajouter']) ? $_POST['quantitymagasin_ajouter'] : 0;
$_POST['quantitytotal']= isset($_POST['quantitytotal']) ? $_POST['quantitytotal'] : 0;
$_POST['remarque_interne'] = isset($_POST['remarque_interne']) ? $_POST['remarque_interne'] : '';
$_POST['showerror'] = isset($_POST['showerror']) ? $_POST['showerror'] : '';
$_POST['ShortMessage'] = isset($_POST['ShortMessage']) ? $_POST['ShortMessage'] : ''; 
$_POST['start'] = isset($_POST['start']) ? $_POST['start'] : '';
$_POST['suggestebay'] = isset($_POST['suggestebay']) ? $_POST['suggestebay'] : 0;
$_POST['suggest']= isset($_POST['suggest']) ? $_POST['suggest'] : 0;
$_POST['sku'] = isset($_POST['sku']) ? $_POST['sku'] : '';
$_POST['sku_old'] = isset($_POST['sku_old']) ? $_POST['sku_old'] : '';
$_POST['skucheck'] = isset($_POST['skucheck']) ? $_POST['skucheck'] : '';
$_POST['testen'] = isset($_POST['testen']) ? $_POST['testen'] : '';
$_POST['testfr'] = isset($_POST['testfr']) ? $_POST['testfr'] : '';
$_POST['upc'] = isset($_POST['upc']) ? $_POST['upc'] : '';
$_POST['upcorigine'] = isset($_POST['upcorigine']) ? $_POST['upcorigine'] : '';
$_POST['upctemp'] = isset($_POST['upctemp']) ? $_POST['upctemp'] : '';
$_POST['dateverification']= isset($_POST['dateverification']) ? $_POST['dateverification'] : '';
$_POST['dateretail']= isset($_POST['dateretail']) ? $_POST['dateretail'] : '';
$_POST['dateretail2']= isset($_POST['dateretail2']) ? $_POST['dateretail2'] : '';
$_POST['datemagasin']= isset($_POST['datemagasin']) ? $_POST['datemagasin'] : '';
$_POST['datemagasin2']= isset($_POST['datemagasin2']) ? $_POST['datemagasin2'] : '';


// Logique pour définir la clé 'color' dans $_POST
if ($_POST['color_item'] == "") {
    $_POST['color'] = $_POST['color_anc'];
} else {
    $_POST['color'] = $_POST['color_item'];
}
// set cookies
$_POST['category_id'] = isset($_POST['category_id']) ? $_POST['category_id'] : (isset($_COOKIE['category_id']) ? $_COOKIE['category_id'] : 617);
$_POST['weight'] = isset($_POST['weight']) ? $_POST['weight'] : (isset($_COOKIE['weight']) ? $_COOKIE['weight'] : 0);
$_POST['weight2'] = isset($_POST['weight2']) ? $_POST['weight2'] : (isset($_COOKIE['weight2']) ? $_COOKIE['weight2'] : 4);
$_POST['width'] = isset($_POST['width']) ? $_POST['width'] : (isset($_COOKIE['width']) ? $_COOKIE['width'] : 5.0);
$_POST['height'] = isset($_POST['height']) ? $_POST['height'] : (isset($_COOKIE['height']) ? $_COOKIE['height'] : 1.0);
$_POST['length'] = isset($_POST['length']) ? $_POST['length'] : (isset($_COOKIE['length']) ? $_COOKIE['length'] : 7.0);

if (isset($_POST['weight'])) {
    $weightValue = is_array($_POST['weight']) ? json_encode($_POST['weight']) : $_POST['weight'];
    setcookie("weight", $weightValue, time() + (86400), "/");
}

if (isset($_POST['weight2'])) {
    $weight2Value = is_array($_POST['weight2']) ? json_encode($_POST['weight2']) : $_POST['weight2'];
    setcookie("weight2", $weight2Value, time() + (86400), "/");
}

if (isset($_POST['width'])) {
    $widthValue = is_array($_POST['width']) ? json_encode($_POST['width']) : $_POST['width'];
    setcookie("width", $widthValue, time() + (86400), "/");
}

if (isset($_POST['height'])) {
    $heightValue = is_array($_POST['height']) ? json_encode($_POST['height']) : $_POST['height'];
    setcookie("height", $heightValue, time() + (86400), "/");
}

if (isset($_POST['length'])) {
    $lengthValue = is_array($_POST['length']) ? json_encode($_POST['length']) : $_POST['length'];
    setcookie("length", $lengthValue, time() + (86400), "/");
}

// Vérification des variables
$new = isset($new) ? $new : '';
$updquantity = isset($updquantity) ? $updquantity : 0;
$quantity = isset($quantity) ? $quantity : 0;
$endprix  = isset($endprix ) ? $endprix  : 0;
$erreurvide  = isset($erreurvide ) ? $erreurvide  : '';
