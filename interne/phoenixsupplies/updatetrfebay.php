<?
//echo $_POST['sku'];

// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 

if (isset($_POST['codetrf'])){
	if($_POST['quantitysold']==0)
	{
		$erreurquantitysold='<strong><font color="red">***ENTREZ LA QTY VENDUE!</font></strong>';
		$new=1;
	}else{
		$qtysold=$_POST['quantity']-$_POST['quantitysold'];
		//$sql2 = 'UPDATE `oc_product` SET quantity='.$qtysold.', location ="'.$_POST['location'].'" where product_id='.$_POST['product_id'];
		//echo $sql2.'<br><br>';
		//$req2 = mysqli_query($db,$sql2);
		//$data2 = mysqli_fetch_assoc($req2); 
	$_POST['codetrf']="";
	}
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="a8c6fe">
<form id="form_67341" class="appnitro" action="updatetrfebay.php" method="post">
<div class="form_description">
<h1>Trf numero ebay dans la base</h1>
</div>
<textarea id="codetrf" name="codetrf" rows="50" cols="500"><?echo $_POST['codetrf'];?></textarea> <br>
<p class="buttons">
<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
<h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>
</form>
<p id="footer">�
</body>
</html>
<? echo 'FINI'; // on ferme la connexion � mysql 
mysqli_close($db); ?>