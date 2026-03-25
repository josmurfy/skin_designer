<?
include 'connection.php';
//include $GLOBALS['SITE_ROOT'].'interne/translatenew.php'; 

if($_POST['conditions_id']!="" && $_POST['category_id']!=""){

	insert_condition($db,$_POST['category_id'],$_POST['conditions_id'],"",1); 
    
	
}
function insert_condition($db,$category_id,$condition_id,$tab,$premier){
	
		$sql2 = "UPDATE `oc_category` SET verif=1 WHERE category_id='".$category_id."'";
		//echo $sql2."<br>";
		$req2 = mysqli_query($db,$sql2);
	
		$sql = 'SELECT COUNT(*) FROM `oc_category` C,`oc_category_description` CD where CD.language_id=1 and C.category_id=CD.category_id and C.parent_id='.$category_id;
	//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			echo $data['COUNT(*)']."<br>";
			//echo $data;
			//print("<pre>".print_r ($data,true )."</pre>");
	if ($data['COUNT(*)']>1){
		$sql = 'SELECT CD.name,C.category_id,C.parent_id,CD.ebayyes  FROM `oc_category` C,`oc_category_description` CD where CD.language_id=1 and C.category_id=CD.category_id and C.parent_id="'.$category_id.'" ';
		//echo $tab.$sql."<br>";
		
				$req = mysqli_query($db,$sql);
				while($data = mysqli_fetch_assoc($req)){
					echo $tab.$data['name']."(".$data['ebayyes'].")<br>";
					insert_condition($db,$data['category_id'],$condition_id,$tab." *** ","");
				}
	}else{
				$sql = 'SELECT CD.name,C.category_id,C.parent_id,CD.ebayyes  FROM `oc_category` C,`oc_category_description` CD where CD.language_id=1 and C.category_id=CD.category_id and C.category_id="'.$category_id.'"';
		//echo $tab.$sql."<br>";
		
				$req = mysqli_query($db,$sql);
				$data = mysqli_fetch_assoc($req);
					//echo $tab.$data['name']."FIN <br>";
					//insert_condition($db,$data['category_id'],$condition_id,$tab."***");
					$sql2 = "INSERT INTO `oc_conditions_to_category` (`conditions_id`, `category_id` ) VALUES ('".$condition_id."', '".$data['category_id']."')";
					echo $sql2."<br><br>";
					$req2 = mysqli_query($db,$sql2);
				
		
	}

	/* else{		
	$sql = 'SELECT C.category_id,C.parent_id,CD.ebayyes  FROM `oc_category` C,`oc_category_description` CD where CD.language_id=1 and C.category_id=CD.category_id and C.category_id='.$category_id;
		echo $sql."<br>";
				$req = mysqli_query($db,$sql);
				$data = mysqli_fetch_assoc($req);
				
				//$name=$data['name'];
				//echo $data['parent_id'];
				$sql2 = "UPDATE `oc_category` SET verif=1 WHERE category_id='".$condition_id."'";
					echo $sql2."<br>";
					//$req2 = mysqli_query($db,$sql2);
				
				if($sql2==1){
					$sql2 = "INSERT INTO `oc_conditions_to_category` (`conditions_id`, `category_id` ) VALUES ('".$condition_id."', '".$data['category_id']."')";
					echo $sql2."<br>";
					//$req2 = mysqli_query($db,$sql2);
					
					$sql3 = 'SELECT C.category_id,C.parent_id,CD.ebayyes   FROM `oc_category` C,`oc_category_description` CD where C.category_id=CD.category_id and C.parent_id='.$category_id;
		echo $sql3."<br>"; 
					$req3 = mysqli_query($db,$sql3);
					while($data3 = mysqli_fetch_assoc($req3)){
						
					}
					//$_POST['category_id']=$data['category_id'];

				}
	} */
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head> <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="mod_category_by_condition.php" method="post">
<div class="form_description">
<h1>Modification conditon par category</h1>

</div><select name="category_id">
<?
$sql = 'SELECT CD.name as name,CD.category_id as category_id FROM `oc_category_description` CD LEFT JOIN oc_category C ON (C.category_id=CD.category_id) where CD.language_id=1 AND C.verif=0 order by CD.name';

// on envoie la requête
$req = mysqli_query($db,$sql);
//echo $sql;
?>
			<option value="" selected></option>
<?
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysqli_fetch_assoc($req))
    {
?>
			<option value="<?echo $data['category_id'];?>"><?echo $data['name'];?></option>
<?}?>
		</select>
		<??>
<select name="conditions_id">
			<option value="" selected></option>
<?
$sql = 'SELECT * FROM `oc_conditions`';

// on envoie la requête
$req = mysqli_query($db,$sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysqli_fetch_assoc($req))
    {
?>
			<option value="<?echo $data['conditions_id'];?>"><?echo $data['name'];?></option>
<?}?>
		</select>

		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />


</form>

</body>
</html>


<?
mysqli_close($db); 
?>