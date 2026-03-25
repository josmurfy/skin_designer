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
<h1>RAPPORT</h1>


		<h1><a href="interneusa.php" >Retour au MENU</a></h1>

<?
		$_POST['ebayinput']=str_replace(array("\&quot"),"",$_POST['ebayinput']);
		$ebayinputnametab=explode("\n", $_POST['ebayinput']);
		$j=0;
		

		foreach($ebayinputnametab as $ebayinputname) 
		{	
			
			$ebayinputnameline=explode("\t", $ebayinputname);
			
			$test=strlen($ebayinputnameline[31]);

			$sql = 'SELECT *,PD.name as nameen,P.quantity_anc,
			p.unallocated_quantity,P.location AS location_entepot,
			P.quantity,P.location AS location_entrepot 
			FROM `oc_product` AS P 
			LEFT JOIN `oc_product_description` AS PD on P.product_id=PD.product_id 
			 
			
			where  PD.language_id=1   and  P.location !="" and P.quantity >0 and P.ebay_id=0 Order by P.location ';		//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$j=0;
			while ($data = mysqli_fetch_assoc($req)){
			

				$ebayoutputnametab[$j]['location']=$data['location'];
				$ebayoutputnametab[$j]['name']=$data['name'];
				$ebayoutputnametab[$j]['product_id']=$data['product_id'];  
				
				$ebayoutputnametab[$j]['sku']=$data['sku'];
				$ebayoutputnametab[$j]['marketplace_item_id']=$data['marketplace_item_id'];
				$ebayoutputnametab[$j]['upc']=$data['upc'];
				$ebayoutputnametab[$j]['quantiterestant']=$data['quantity'];
				if($data['image']!="") 	$ebayoutputnametab[$j]['image']='<img height="50" src="'.$GLOBALS['WEBSITE'].'image/'.$data['image'].'"/>';
				//print_r($ebayoutputnametab)."<br>"; 
				$j++;
			}

		}

		//echo $sql."<br>";
				
//exit;
?>
<script language="JavaScript">

</script>
<table border="1" width="100%">
	<tr>
	<th bgcolor="ff6251">
	
	<input type="button" onclick='selectAll()' value="Magasin"/><br>
	<input type="button" onclick='UnSelectAll()' value="Magasin Non"/>
		<input type="button" onclick='selectAllex()' value="Exclure"/><br>
	<input type="button" onclick='UnSelectAllex()' value="Exclure NON"/>
	</th>
	<th bgcolor="ff6251">
	Lister su ebay
	</th>

		<th bgcolor="ff6251">
	Modifier Item
	</th>
	<th bgcolor="ff6251">
	Location
	</th>
	<th bgcolor="ff6251">
	Qty
	</th>
	<th bgcolor="ff6251">
	Check Prix sur ebay
	</th>
	</tr>

<?
		sort($ebayoutputnametab);
			foreach($ebayoutputnametab as $ebayoutputname) 
			{	 
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
					Inclure:<input type="checkbox" name="incluremagasin[]" value="<?echo $ebayoutputname['product_id'];?>"/>
					Exclure:<input type="checkbox" name="excluremagasin[]" value="<?echo $ebayoutputname['product_id'];?>"/>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<a href="<?echo $GLOBALS['WEBSITE'];?>/interne/pretlister.php?product_id=<?echo $ebayoutputname['product_id'];?>&action=listing" target='listing' ><?echo $ebayoutputname['sku'];?></a>
					</td>

					<td bgcolor="<?echo $bgcolor;?>">
					<a href="<?echo $GLOBALS['WEBSITE'];?>/interne/pretlister.php?product_id=<?echo $ebayoutputname['product_id'];?>&action=listing" target='listing' ><?echo $ebayoutputname['name'];?></a>

				
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['location'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['quantiterestant'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<a href="https://www.ebay.com/sch/i.html?_nkw=<?echo $ebayoutputname['upc'];?>&LH_PrefLoc=1&LH_ItemCondition=3&LH_BIN=1&_sop=15" target='ebay2' >0<?echo $ebayoutputname['marketplace_item_id'];?></a>

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