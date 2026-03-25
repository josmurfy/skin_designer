<? 
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte à MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on sélectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on crée la requête SQL verifier les ordres  
// savoir ledernier id 

if(isset($_POST['analyseinput']))
{
		$_POST['analyseinput']=str_replace(array("\&quot"),"",$_POST['analyseinput']);
		$analyseinputnametab=explode("\n", $_POST['analyseinput']);

		foreach($analyseinputnametab as $analyseinputname) 
		{
			$analyseinputnameline=explode("###", $analyseinputname);
			
				$sql = 'INSERT INTO `oc_product_reception` (invoice,bluestiker,upc,brand,model,title,pricedetailusd,pricedetailcad)';
				$sql = $sql.'values ("';
						$i=0;
				//echo $analyseinputnameline[0]."<br>";
				for($i=0; $i<=9; $i++) 
				{
					if($i==0 || $i==2 || $i==3 ||$i==4 || $i==5 || $i==6 || $i==8 || $i==9 )
					{
						
							$sql=$sql.htmlentities($analyseinputnameline[$i]);
							if($i==9){
							$sql=$sql.'")';	
							}else{
								$sql=$sql.'","';	
							}
						//echo $i;
					}
					
				}
			

		//echo $sql."<br>";
				$req = mysql_query($sql);
				//$data = mysql_fetch_assoc($req);
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
<form id="form_67341" class="appnitro" action="insertionpricereception.php" method="post">
<div class="form_description">
<h1>Insertion Receptiont</h1>

<h3><label class="description" for="categorie">Reception :</label></h3>

<textarea id="analyseinput" name="analyseinput" rows="5" cols="50"></textarea> <br>

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="analyseinputarbonum" value="<?echo $analyseinputarbonum;?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>

</form>
<p id="footer"> 
</body>
</html>
<? echo 'FINI'; // on ferme la connexion à mysql 
mysql_close(); ?>