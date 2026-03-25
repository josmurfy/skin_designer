<? 
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte � MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s�lectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on cr�e la requ�te SQL verifier les ordres  
// savoir ledernier id 

if(isset($_POST['analyseinput']))
{
		$_POST['analyseinput']=str_replace(array("\&quot"),"",$_POST['analyseinput']);
		$analyseinputnametab=explode("\n", $_POST['analyseinput']);

		foreach($analyseinputnametab as $analyseinputname) 
		{
			$analyseinputnameline=explode("\t", $analyseinputname);
			
				$sql = 'INSERT INTO `oc_product_reception` (title,brand,model,upc,quantity,pricedetailusd,pricedetailcad,quantityrecu)';
				$sql = $sql.'values ("';
						$i=0;
				//echo $analyseinputnameline[0]."<br>"; 


						    $price=floatval(ltrim($analyseinputnameline[1], '$'));
							$sql=$sql.addslashes($analyseinputnameline[4]).'","","","'.htmlentities($analyseinputnameline[5]).'","'.htmlentities($analyseinputnameline[0]).'","'.$price.'","'.$price*1.3.'","'.htmlentities($analyseinputnameline[0]).'")';

						//echo $i;


			

		echo $sql."<br>";
				$req = mysql_query($sql);
			
//exit;
		}

}?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<style> 
input[type=text] {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}
textarea  {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}

select {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}
select:focus {
    border: 3px solid #555;
}

input[type=text]:focus {
    border: 3px solid #555;
}
textarea:focus {
    border: 3px solid #555;
}
</style>
</head>
<body bgcolor="a8c6fe">
<form id="form_67341" class="appnitro" action="insertionpricereceptionwalmart.php" method="post">
<div class="form_description">
<h1>Insertion Reception WALMART</h1>

<h3><label class="description" for="categorie">Reception :</label></h3>

<textarea id="analyseinput" name="analyseinput" rows="5" cols="50"></textarea> <br>

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="analyseinputarbonum" value="<?echo $analyseinputarbonum;?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>

</form>
<p id="footer">�
</body>
</html>
<? echo 'FINI'; // on ferme la connexion � mysql 
mysql_close(); ?>