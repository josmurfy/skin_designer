<? 
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
$j=0;

//if($_POST[incluremagasin])
 
//print_r ($_POST[incluremagasin]);
		foreach($_POST[incluremagasin] as $incluremagasin)  
			{	
				
				$sql2 = 'UPDATE `oc_product` SET excluremagasin=1 where product_id='.$incluremagasin; 
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				
			}
		foreach($_POST[excluremagasin] as $excluremagasin)  
			{	
				
				$sql2 = 'UPDATE `oc_product` SET excluremagasin=9 where product_id='.$excluremagasin;
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				
			}
				$sql2 = 'UPDATE `oc_product` SET location="" where quantity=0';
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>

<script type="text/javascript">
    function selectAll() {
        var items = document.getElementsByName('incluremagasin[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAll() {
        var items = document.getElementsByName('incluremagasin[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }
    function selectAllex() {
        var items = document.getElementsByName('excluremagasin[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAllex() {
        var items = document.getElementsByName('excluremagasin[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }		
</script>
<link href="stylesheet.css" rel="stylesheet">



</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="rapportinventaire.php" method="post">
<div class="form_description">


<?
		$nbitem=0;
		$_POST['ebayinput']=str_replace(array("\&quot"),"",$_POST['ebayinput']);
		$ebayinputnametab=explode("\n", $_POST['ebayinput']);
		$j=0;
		

		foreach($ebayinputnametab as $ebayinputname) 
		{	
			
			$ebayinputnameline=explode("\t", $ebayinputname);
			
			$test=strlen($ebayinputnameline[31]);

			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` WHERE `oc_product`.product_id=`oc_product_description`.product_id and `location` NOT LIKE "%magasin%" and `location` NOT LIKE "" AND `quantity` > 0 and excluremagasin=1 group by `oc_product_description`.product_id ORDER BY `oc_product`.`location` DESC ';  
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$j=0;
			while ($data = mysqli_fetch_assoc($req)){
			

				$ebayoutputnametab[$j]['location']=$data['location'];
				$ebayoutputnametab[$j]['name']=$data['name'];
				$ebayoutputnametab[$j]['product_id']=$data['product_id'];  
				
				$ebayoutputnametab[$j]['sku']=$data['sku'];
				$ebayoutputnametab[$j]['poids']=$data['weight'];
				$ebayoutputnametab[$j]['dimension']=number_format($data['length'], 1, '.', '')."x".number_format($data['width'], 1, '.', '')."x".number_format($data['height'], 1, '.', '');
				$ebayoutputnametab[$j]['quantiterestant']=$data['quantity'];
				if($data['image']!="") 	$ebayoutputnametab[$j]['image']='<img height="50" src="'.$GLOBALS['WEBSITE'].'image/'.$data['image'].'"/>';
				//print_r($ebayoutputnametab)."<br>"; 
				$j++;
			}

		}

		//echo $sql."<br>";
				
//exit;
?>


<?
		sort($ebayoutputnametab);
			foreach($ebayoutputnametab as $ebayoutputname) 
			{	 
			if($nbitem==11)$nbitem=0;
			if($nbitem==0){
				echo '</table><h1>RAPPORT item pour magasin</h1><script language="JavaScript">

</script>
<table border="1" width="100%">
	<tr>
	<th bgcolor="ff6251">
	

	</th>
	<th bgcolor="ff6251">
	SKU
	</th>

		<th bgcolor="ff6251">
	Titre
	</th>
	<th bgcolor="ff6251">
	Poids
	</th>
	<th bgcolor="ff6251">
	Dimension
	</th>
	<th bgcolor="ff6251">
	Location
	</th>
	<th bgcolor="ff6251">
	Qte Restant
	</th>
	</tr>';
			}
			$nbitem++;
			$k++;
			//echo "allo";
				if ($bgcolor=="ffffff"){
					$bgcolor="c0c0c0";
				}else{
					$bgcolor="ffffff";
				}
?>
					<tr>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['image'];?>

					</td>
					<td bgcolor="<?echo $bgcolor;?>">

						<?echo $ebayoutputname['sku'];?>
					</td>

					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['name'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['poids'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['dimension'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['location'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['quantiterestant'];?>
					</td>
					</tr>
		<?
		
	//$k++;
	//echo $k;
	}
		?>
</table>
		


		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />

		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
</form>
</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>