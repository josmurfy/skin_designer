<?
//$db = mysqli_connect("127.0.0.1","phoenkv5_store","Vivi1FX2Pixel$$","phoenkv5_store");
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on sélectionne la base


$image = file_get_contents('https://images-na.ssl-images-amazon.com/images/I/31vgJTuXe1L.jpg');
//echo $image;
file_put_contents('/home/phoenkv5/public_html/image/catalog/producttmp/zzzztestjo2.jpg', $image); //Where to save the image on your server

 mysqli_close($db); 
?>