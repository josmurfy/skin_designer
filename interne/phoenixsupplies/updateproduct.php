<?php
//$db = mysqli_connect("127.0.0.1","phoenkv5_store","Vivi1FX2Pixel$$","phoenkv5_store");
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base


$sql = 'SELECT * FROM oc_product_description where meta_keyword="" and language_id=1 order by product_id'; 

$req = mysqli_query($db,$sql);

// on fait une boucle qui va faire un tour pour chaque enregistrement

  
//echo 'allo';





while($data = mysqli_fetch_assoc($req))
	{
		
//echo strlen($data['description']); echo "<br>";

  $string= str_replace($stringrep,'',$data['description']);

$sql2 = 'SELECT * FROM oc_product where product_id= '.$data['product_id'].' '; 


$req2 = mysqli_query($db,$sql2);

$data2 = mysqli_fetch_assoc($req2);

$sql4 = 'SELECT * FROM oc_manufacturer where manufacturer_id= '.$data2['manufacturer_id'].''; 

$req4 = mysqli_query($db,$sql4);

$data4 = mysqli_fetch_assoc($req4);
//echo $data3['name']; 
$line = $data['description'];


$line = strip_tags($line);
$line = html_entity_decode($line);
$line = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $line);
$line = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $line);
$line = preg_replace('(\n|\r|\t|<h1>|</h1>|</p>|<p>|<br>|<b>|<div>|</div>|<font|</font>|color="rgb(0,51,204)"|<font color="rgb(0,51,204)">|&nbsp;|<font color="#202020">|<font color="#000000">)',' ',$line);
$line = preg_replace('/\s\s+/', ' ', $line); 
  
  $line= addslashes ($line);
  //echo $line;
  //$line= htmlentities($line, ENT_QUOTES);
  //echo $line;
 // $new = htmlentities("<a href='test'>Test</a>", ENT_QUOTES);
//echo $new; // &lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;


  $name= addslashes($data['name']);


$sql3 = "UPDATE `oc_product_description` SET `meta_description` = '".$name." ".$line."',`meta_keyword`='".str_replace(' ',',',str_replace('/',',',$name))."',`tag`='".str_replace(' ',',',str_replace('/',',',$name)).",".$data4['name'].",".str_replace(' ',',',$data2['model']).",".str_replace(' ',',',$data2['mpn'])."', `meta_title` ='".$name."' WHERE `product_id` = ".$data['product_id'];
$req3 = mysqli_query($db,$sql3) ;
//echo $sql3.'<br>';
//echo "UPDATE `oc_product_description` SET `meta_description` = '".$line."',`tag`='".str_replace(' ',',',str_replace('/',',',$name)).",".$data4['name'].",".str_replace(' ',',',$data2['model']).",".str_replace(' ',',',$data2['mpn'])."', `meta_title` ='".$name."' WHERE `product_id` = ".$data['product_id'];;
//echo '<br>';
}
//echo "UPDATE `oc_product_description` SET `meta_description` = '".addslashes($line)."' `meta_title` ='". addslashes($data['name'])."' WHERE `product_id` = ".$data['product_id'];

?>
