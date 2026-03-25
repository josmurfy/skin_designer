<?php
// on se connecte � MySQL
//header('Content-Type: application/vnd.ms-excel; charset=utf-8');
//header('Content-Disposition: attachment; filename=import.xls');

// create a file pointer connected to the output stream
//$output = fopen('php://output', 'w'); 
//unlink('/home/n7f9655/public_html/phoenixliquidation/interne/algopix/algopix'.date("Y-m-d").'___.csv');
link('/home/n7f9655/public_html/phoenixliquidation/interne/algopix/algopix'.date("Y-m-d").'___.csv','/home/n7f9655/public_html/phoenixliquidation/interne/algopix/algopix'.date("Y-m-d").'___.csv');
$fp = fopen('/home/n7f9655/public_html/phoenixliquidation/interne/algopix/algopix'.date("Y-m-d").'___.csv', 'w');
fwrite($fp, "Product identifier,Identifier type,Cost USD,Comments");

fputs($fp, "\n");
include 'connection.php';
//include 'functionload.php';

libxml_use_internal_errors(true);

$domtree = new SimpleXMLElement('<xml/>');
	
// on cr�e la requ�te SQL
$sql = 'select *

from oc_product_description pd  

left join oc_product p on (p.product_id=pd.product_id) 

left join oc_product_to_category pc on (pc.product_id=p.product_id and (pc.category_id=139973 or pc.category_id=617))

where pd.`name`="" and p.verif_fait is null and pd.language_id=1 and p.quantity>0 order by p.product_id'; 
// on envoie la requ�te
$req = mysqli_query($db,$sql);
//echo $sql;
//$data = mysqli_fetch_assoc($req);
//print("<pre>".print_r ($data,true )."</pre>");
//echo "Category;System Model;System Mfg;Part Mfg;Comments;Part Number;Part/Item Name;Condition;Listing Type;Request Type;Price;D2D Price;VTS Friend Price;Currency;Quantity;Paypal Activate?;Paypal Shipping;Make an Offer?;Auto-Accept Offers above;Auto-Reject Offers below;Weight;'Weight Units (1 = ''Lbs'' 2= ''Kg'')';Height;Width;Depth;Will Rent;Available as Parts?;In Stock;Item ID;Item ID Type (SKU/EAC/JAN/MPN/MMN);Your Item ID;Youtube URL;Video URL;Listing ID(Leave Blank to Add);Delete;Image 1;Image 2;Image 3;Image 4;Image 5;Image 6;Image 7;Image 8;Image 9;Image 10";


    /* create the root element of the xml tree */
   // $xmlRoot = $domtree->createElement("xml");
    /* append it to the document created */
    //$xmlRoot = $domtree->addChild($xmlRoot);
//
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysqli_fetch_assoc($req))
    {
		//print("<pre>".print_r ($data,true )."</pre>");
		
		fwrite($fp, $data['upc'].",UPC,1,".$data['product_id']);
		fputs($fp, "\n");
		$sql2="UPDATE oc_product SET verif_fait='2' WHERE product_id='".$data['product_id']."'"; 
	//	echo $sql2."<br>";
		$req2 = mysqli_query($db,$sql2);  
	
		
	}
	

	
//$xmlRoot  .='</document>';

//echo $xmlRoot ;

//$test = new SimpleXMLElement($xmlRoot );


//echo $nb;
// on ferme la connexion � mysql

//Header('Content-type: text/xml');
//print($xmlRoot ->asXML());

//echo $domtree->saveXML();
//Header('Content-type: text/xml');
//print($domtree->asXML());
echo '<a href="algopix/algopix'.date("Y-m-d").'___.csv" target="_blank" style="color:#ff0000"><strong>Download fichier</strong></a>';
echo '	<a href="algopix.php"  style="color:#ff0000"><strong>Algopix</strong></a> ';
mysqli_close($db); 
?> 
