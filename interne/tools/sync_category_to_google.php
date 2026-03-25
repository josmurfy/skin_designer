<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte à MySQL 
include '../connection.php';include '../functionload.php';
if ($_POST['category_id']!="" && $_POST['status']==2){

		$sql2 = 'UPDATE `oc_category` SET `link`=2 WHERE category_id='.$_POST['category_id'];
		//echo $sql2.'<br><br>';	  
		$req2 = mysqli_query($db,$sql2); 

}

if ($_POST['google_base_category_id']!="" && $_POST['category_id']!="" && $_POST['status']!=2){

		$sql2 = 'UPDATE `oc_google_base_category` SET `link`=1 WHERE `oc_google_base_category`.`google_base_category_id` ='.$_POST['google_base_category_id'];
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); 
		$sql2 = 'UPDATE `oc_category` SET `link`=1 WHERE category_id='.$_POST['category_id'];

		//echo $sql2.'<br><br>';	  
		$req2 = mysqli_query($db,$sql2); 
			$sql2 = 'INSERT INTO `oc_google_base_category_to_category` (`google_base_category_id`,`category_id`) VALUES ("'.$_POST['google_base_category_id'].'","'.$_POST['category_id'].'")';
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
		$_POST['searchmanuel']="";
}


			$sql3 = 'SELECT * FROM `oc_category_description`,`oc_category` where `oc_category_description`.category_id=`oc_category`.category_id and oc_category_description.ebayyes=1 and link=0 and language_id=1';
 			$req3 = mysqli_query($db,$sql3);	
			$afaire= mysqli_num_rows($req3);
			$sql3 = 'SELECT * FROM `oc_category_description`,`oc_category` where `oc_category_description`.category_id=`oc_category`.category_id and status=1 and oc_category_description.ebayyes=1 and link=0 and language_id=1 limit 1';
 			$req3 = mysqli_query($db,$sql3);	
			//echo $sql3."<br>";
			$data3 = mysqli_fetch_assoc($req3);
			//trouver le chemin complet
			$nomcat=$data3['name'];
			$catparent=$data3['parent_id'];
			//echo $catparent;
			while($catparent>0){
				$sql4 = 'SELECT * FROM `oc_category_description`,`oc_category` where `oc_category_description`.category_id=`oc_category`.category_id and language_id=1 and `oc_category`.category_id='.$catparent.' limit 1';
				$req4 = mysqli_query($db,$sql4);	
				//echo $sql4."<br>";
				$data4 = mysqli_fetch_assoc($req4);
				$catparent=$data4['parent_id'];
				$nomcat.="--->".$data4['name'];
			}
		
			$_POST['category_id']=$data3['category_id'];
			$category_id=explode(' ', $data3['name']);
			$search="";
			echo $_POST['searchmanuel'];
			if($_POST['searchmanuel']==""){
					for ($i = 1; $i <= count($category_id); $i++) {
			
						if(strlen($category_id[$i-1])>2 && !($category_id[$i-1]=="Parts" || $category_id[$i-1]=="Accessories" || $category_id[$i-1]=="Others" || $category_id[$i-1]=="Other")){
								$search.='"%'.$category_id[$i-1].'%"';
								if(count($category_id)>1 && $i<count($category_id)) $search.= " or name like ";
							}
						}
			}else{
				
				$search='"%'.$_POST['searchmanuel'].'%"';
			}
			$sql = 'SELECT * FROM `oc_google_base_category` where name like '.$search.' order by name';
			echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$calcitem=0;
			//$data = mysqli_fetch_assoc($req);
			//print_r($category_id);

		

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
 <link href="../stylesheet.css" rel="stylesheet">  
</head>
<body bgcolor="ffbaba">
<h1>Modification GOOGLE CATEGORY (A faire :<?echo $afaire;?>) </h1>  
<?echo '<h1><font color="red">'.$erreurvide.'</font></h1>';?>
<form action="sync_category_to_google.php?update=yes&action=<?echo $_GET['action'];?>" method="post">
<div class="form_description">


</div>
<h3>SKU <?if($new==1){?><a href="modificationitemusa.php" class="button--style-red">Changer de categorie</a><?}?> 
<a href="interneusa.php" class="button--style-red">Retour au MENU</a> 

<h3><label class="description" for="categorie">Categorie SiteWeb : <?echo $nomcat;?></label></h3>

		Pas trouver :<input type="checkbox" name="status" value="2" /><input id="searchmanuel" class="" type="text" name="searchmanuel" value="<?echo $_POST['searchmanuel'];?>" maxlength="25" />

		


<?/*?><a href="ajoutcategorie.php?sku=<?echo $_POST['sku']?>&category_id=<?echo $_POST['category_id']?>" class="button--style-red">Ajout Categorie</a><?*/?>
		<table width="100%">
		  <tr>
			<td valign="top"><h3>Categorie :<select name="google_base_category_id">
			<option value="" selected></option>

<?

$categoryrecom="";
	while($data = mysqli_fetch_assoc($req)){			
    
/* 		$selected="";
		if (isset($_POST['google_base_category_id']) && $_POST['google_base_category_id']!=0){
			$test2=strtolower ($_POST['google_base_category_id']);
			$test1=strtolower ($data['google_base_category_id']);
			if ($test1==$test2) {
				$selected="selected";
			}
			//echo "allo";
		}else{
			$test2=strtolower ($data['name']);
			$test1=strtolower ($_POST['name_product']);
			//echo "allo2";
			if (strpos($test1, $test2) !== false) {
				//$selected="selected";
				echo 'allo3';
				//$categoryrecom[$i]
				$categoryrecom=$categoryrecom.",".$data['name']."@".$data['google_base_category_id'];
			}
		} */
	

		
?>
			<option value="<?echo $data['google_base_category_id'];?>" <?echo $selected;?>><?echo $data['name'];?></option>
<?}?>
		</select><br>
		<input type="hidden" name="category_id_old" value="<?echo $_POST['google_base_category_id'];?>" />
<?	
//echo $categoryrecom;
$categoryrecomtab=explode(',', $categoryrecom);
foreach($categoryrecomtab as $categoryrecomtab2){
	
	if($categoryrecomtab2!=null ){
		//echo $categoryrecomtab2;
		$categoryrecomtab3=explode('@', $categoryrecomtab2);
		echo '<input id="category_recom" class="element radio" type="radio" name="category_recom" value="'.$categoryrecomtab3[1].'"/> 
				<label class="choice" for="etat_1">'.$categoryrecomtab3[0].'</label><br>';
	}
}	
?>		
		</td>
		  </tr>
		  </table>
		



		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
		
		
		

</form>
<table bgcolor="ffffff" align="center">
<td>

</td>
</table>
 <h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>

</body>
</html>
<? // on ferme la connexion à mysql 
mysqli_close($db);
?>