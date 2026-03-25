<?
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
$data= array();
$data['upc']="660543009481";
$data['etat']="1000";
$i=0;
//mise a jour prix en haut de 5000$
				$sql2 = 'SELECT * FROM `oc_product_special` where price>3000 order by product_id';//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				echo $sql2.'<br><br>';
				while($data2 = mysqli_fetch_assoc($req2)){
					$sql3 = 'UPDATE `oc_product` SET status=0 where product_id='.$data2['product_id']; 
					echo $sql3.'<br><br>';
					$req3 = mysqli_query($db,$sql3);  
					$i++;
				}
				echo $i;
//get_myeBay_selling($connectionapi,"");
//get_ebay_product($connectionapi,$ebay_id)
//add_ebay_item($connectionapi,$result,$data,$db); 
//$html=browse_ebay($connectionapi,"Samsung - S-view Flip Cover For Samsung Galaxy S4 Cell Phones - White","887276054070","5","5");
//refresh_token($connectionapi);
//get_product_by_sku("660543009481"); 
 ?>


<html> 
<head>
<?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
 <link href="stylesheet.css" rel="stylesheet">
  <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
</head>
<body bgcolor="ffffff">
<?echo $html;?>
</body>
</html>

<?  



// on ferme la connexion à mysql 
mysqli_close($db); ?>