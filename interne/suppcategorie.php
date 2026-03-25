<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr&eacute;e la requ�te SQL verifier les ordres 
// savoir ledernier id 
//echo $_GET['sku'];
//echo 'check '.$_POST['skucheck'];
//echo $new;

if (empty ($_GET['parent_id'])){
	//echo "allo";
			$_GET['parent_id']=0;
			$_GET['parent_id_avant']=0;
}
if($_GET['supp']=="oui"){
			$sql = 'delete FROM `oc_category` where category_id="'.$_GET['category_id'].'" and parent_id='.$_GET['parent_id'];
			//echo $sql;
			$req = mysqli_query($db,$sql);
					
}
if (isset($_GET['parent_id'])){
	
	
			$sql = 'SELECT   COUNT(*) AS nbr_doublon, category_id
					FROM     `oc_category` where status=1
					GROUP BY category_id
					HAVING   COUNT(*) > 1 limit 1';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			$sqlm = 'SELECT name,parent_id,oc_category.category_id FROM `oc_category`,`oc_category_description` where language_id=1 and oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$data['category_id'];
			//echo $sqlm;
			$i=0;
			$reqm = mysqli_query($db,$sqlm);
			while($datam = mysqli_fetch_assoc($reqm)){
					$categoryname[$i]=$datam['name'].">>";
					$sql = 'SELECT name,parent_id FROM `oc_category`,`oc_category_description` where language_id=1 and oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$datam['parent_id'];
			//echo $sql."<br>";
					$req = mysqli_query($db,$sql);
					$data = mysqli_fetch_assoc($req);
					$categoryname[$i]=$categoryname[$i].$data['name'].">>";
					$categoryid[$i]=$datam['category_id'];
					$parentid[$i]=$datam['parent_id'];
					
					$sql2 = 'SELECT name FROM `oc_category`,`oc_category_description` where language_id=1 and oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$data['parent_id'];
			//echo $sql2."<br><br>";
					$req2 = mysqli_query($db,$sql2);
					$data2 = mysqli_fetch_assoc($req2);
					$categoryname[$i]=$categoryname[$i].$data2['name']."";
					$i++;
			}
			//echo $categoryname;
			//echo $data['parent_id'];
			
		//if (isset($_POST['manufacturer_id'])==false){$_POST['manufacturer_id']=0;}

}		


	

?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff"> 
<h1>Ajout Categorie</h1>
<form id="form_67341" class="appnitro" action="suppcategorie.php?parent_id=<?echo $_GET['parent_id'];?>&parent_id_avant=<?echo $_GET['parent_id_avant'];?>" method="post">

<a href="interneusa.php" >Retour au MENU</a> <a href="ajoutcategorie.php?parent_id=<?echo $_GET['parent_id_avant'];?>" >Retour en arriere</a> 



<h3><label class="description" for="categorie">Categorie :</label></h3>
<?

	echo $categoryname[0].' '.$parentid[0].' <a href="suppcategorie.php?supp=oui&category_id='.$categoryid[0].'&parent_id='.$parentid[0].'" >Supprimer</a><br>';
	echo $categoryname[1].' '.$parentid[1].' <a href="suppcategorie.php?supp=oui&category_id='.$categoryid[1].'&parent_id='.$parentid[1].'" >Supprimer</a>';
	

?><br>

Nom Anglais <input id="catnameen"  type="text" name="catnameen" value="<?echo $_POST['catnameen'];?>" maxlength="160" /> <br>
Nom Francais <input id="catnamefr"  type="text" name="catnamefr" value="<?echo $_POST['catnamefr'];?>" maxlength="160" /> <br>
Numero Ebay <input type="checkbox" name="ebayyes" value="1" <?if($_POST['ebayyes']=='1')echo 'checked';?>/>Categorie sur EBAY<input id="numcat"  type="text" name="numcat" value="<?echo $_POST['numcat'];?>" maxlength="160" /> <br>



		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="etat" value="<?echo $_POST['etat'];?>" />
		<input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
		<input type="hidden" name="quantityinventaire" value="<?echo $_POST['quantityinventaire'];?>" />
		<input type="hidden" name="skuanc" value="<?echo (string)$_POST['sku'] ;?>" />
		
		<?if($new==1){?>
		Pret pour lister :<input type="checkbox" name="status" value="1" <?if($_POST['status']=='1')echo 'checked';?>/>
		<?}?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		

	
</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>