<?
include 'connection.php';
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
include '/home/n7f9655/public_html/phoenixliquidation/interne/translatenew.php';


$response=getCategoriesRequest($connectionapi);
    //$new = simplexml_load_string($response);  
				// Convert into json 
			//	$result = json_encode($response); 
				//$textoutput=str_replace("}","<br><==<br>",$result);
			//	$textoutput=str_replace("{","<br>==><br>",$textoutput);
				
				//echo $textoutput."\nallo"."<br>";
		//		$json = json_decode($response, true);
        //print("<pre>".print_r ($json,true )."</pre>");
       //         echo $response;
       $nb_cat_tot=0;
       $nb_cat=0;
       
       // Parcourir les catégories et insérer les données
foreach ($response['CategoryArray']['Category'] as $category) {
    $nb_cat_tot++;
    $categoryID = $category['CategoryID'];
    $categoryName = $category['CategoryName'];
    $categoryParentID = $category['CategoryParentID'];
    $ebayyes = $category['LeafCategory'] ? 1 : 0; // Si LeafCategory est true, ebayyes=1 sinon ebayyes=0

    // Vérifier si CategoryID est égal à CategoryParentID
    if ($categoryID == $categoryParentID) {
        $categoryParentID = 0;
    }

    // Vérifier si la catégorie existe déjà
    $checkSql = "SELECT COUNT(*) as count FROM `oc_category` WHERE `category_id` = '$categoryID'";
    echo $checkSql . "<br>";
    $checkResult = mysqli_query($db, $checkSql);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['count'] == 0) { 
        // La catégorie n'existe pas, procéder à l'insertion
        $sql = "INSERT INTO `oc_category` ( `category_id`, `image`, `parent_id`, `top`, `column`, `sort_order`, `status`, `date_added`, `date_modified`, `verif`) 
                VALUES ( '$categoryID', NULL, '$categoryParentID', NULL, NULL, '0', '0', now(), now(), '0')";
        echo $sql . "<br>";
        $req = mysqli_query($db, $sql);

        $sql = "INSERT INTO `oc_category_description` (`category_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`, `old`, `ebayyes`) 
                VALUES ('$categoryID', '1', '" . strtoupper(addslashes($categoryName)) . "', '', '', '', '', NULL, '$ebayyes')";
        echo $sql . "<br>";
        $req = mysqli_query($db, $sql);
        $POST['nameen']=$categoryName;
        $POST= translate_field($POST);
        $categoryNameFR= $POST['namefr'];
        unset($POST);  
        echo $categoryName . "<br>";
        $sql = "INSERT INTO `oc_category_description` (`category_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`, `old`, `ebayyes`) 
                VALUES ('$categoryID', '2', '" . strtoupper(addslashes($categoryNameFR)) . "', '', '', '', '', NULL, '$ebayyes')";
        echo $sql . "<br>";
        $req = mysqli_query($db, $sql);

        $sql = "INSERT INTO `oc_category_to_store` (`category_id`, `store_id`) 
                VALUES ('$categoryID', '0')";
        echo $sql . "<br>";
        $req = mysqli_query($db, $sql);
     $nb_cat++;
    } else {
        echo "La catégorie $categoryID existe déjà.<br>";
    }
    
  //  if($nb_cat_tot==10)
  //      exit;
}
echo "nb_cat: ".$nb_cat . "<br>";
echo "nb_cat_tot: ".$nb_cat_tot . "<br>";
// Fermeture de la connexion
$db->close();
?>