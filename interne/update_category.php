<?php
ob_start();
include 'connection.php';
//print("<pre>" . print_r($_GET, true) . "</pre>");
$_POST['category_id'] = (isset($_POST['category_id']) && !isset($_GET['category_id']))?$_POST['category_id']:null;
$_POST['category_id'] = isset($_GET['category_id'])?$_GET['category_id']:$_POST['category_id'];

// Connexion à la base de données
//print("<pre>" . print_r($_POST['category_id'], true) . "</pre>");
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$sql2 = 'SELECT CD.category_id AS category_id, CD.name AS category_name,specifics
                     FROM `oc_product_to_category` PC 
                     LEFT JOIN `oc_category` C ON (PC.category_id = C.category_id)
                     LEFT JOIN `oc_category_description` CD ON (C.category_id = CD.category_id AND CD.language_id = 1 AND CD.ebayyes = 1)
                     WHERE PC.category_id = "' .$_POST['category_id'] . '" 
                     ORDER BY CD.category_id DESC LIMIT 1';
            // Execute the category query and handle potential errors
        //    echo $sql2;
            $req2 = mysqli_query($db, $sql2);
            if (!$req2) {
                printf("Error: %s\n", mysqli_error($db));
                exit();
            }
$category = mysqli_fetch_assoc($req2);
$specifics = $category['specifics'];
if (isset($_POST['aspects'])) {
    $specifics=json_decode($specifics,true);
    $aspectsToRemove = $_POST['aspects']; // Par exemple ['Brand', 'MPN']
  //print("<pre>" . print_r($specifics, true) . "</pre>");
    foreach ($aspectsToRemove as $aspectToRemove) {
        foreach ($specifics as $key => $aspect) {
            if (isset($aspect['localizedAspectName']) && $aspect['localizedAspectName'] === $aspectToRemove) {
                unset($specifics[$key]);
             //   echo "key:".$key;
            }
        }
    }
    
    if(count($specifics)==0){
        $dataspecific=' = null ';
        $specifics=json_encode($specifics, true);
    }else{
        $specifics=json_encode($specifics, true);
        $dataspecific= " ='".addslashes($specifics)."' ";
    }

    $sql2="UPDATE `oc_category` SET ebay=1,`specifics` ".$dataspecific." WHERE `oc_category`.`category_id` = '".$_POST['category_id']."'";
	//	//print("<pre>".print_r ($sql2,true )."</pre>");
		mysqli_query($db,$sql2);

        $sql3 = 'SELECT * FROM `oc_product_to_category` where category_id = "'.$_POST['category_id'].'"';
        //echo $sql3."<br>";
        $req3 = mysqli_query($db,$sql3);
        while($data3 = mysqli_fetch_assoc($req3)){
            $sql2="UPDATE `oc_product` SET `product_specifics` =null WHERE `product_id` = '".$data3['product_id']."'";
          //print("<pre>".print_r ($sql2,true )."</pre>");
            mysqli_query($db,$sql2);
        }
    // Réindexer le tableau pour éviter les clés manquantes
   // $specifics = array_values($specifics);

    // Afficher le tableau specifics après suppression
   //print("<pre>" . print_r($specifics, true) . "</pre>");
}elseif(!isset($category['specifics']) && isset($category['category_id']) ){
    $specifics  = getCategorySpecifics($connectionapi,$category,$db);
   
}
$specifics=json_decode($specifics,true);

//print("<pre>".print_r ($specifics,true )."</pre>");
//echo json_encode($specifics);

$db->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Category Specifics</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        function toggleCheckboxes(source) {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
        }
    </script>
</head>
<body>
    <h1>Update Category Specifics (<? echo $category['category_name']; ?>)</h1>
    <form action="update_category.php" method="post">
        <label for="category_id">Category ID:</label>
        <input type="text" id="category_id" name="category_id" required value="<? echo $_POST['category_id']; ?>" size="10">
        
        <button type="submit">Update Category</button><br><br>
      <?if (isset($specifics)){?>

    <table>
        <thead>
            <tr>
                <th><label>
        <input type="checkbox" onclick="toggleCheckboxes(this)"> Select/Unselect All
    </label></th>
                <th>Aspect Name</th>
                <th>aspectDataType</th>
                <th>itemToAspectCardinality</th>
                <th>aspectMode</th>
                <th>aspectRequired</th>
                <th>aspectUsage</th>
                          </tr>
        </thead>
        <tbody>
            <?php
            foreach ($specifics as $aspect) {
                $localizedAspectName = $aspect['localizedAspectName'];
                $aspectDataType = $aspect['aspectConstraint']['aspectDataType'];
                $itemToAspectCardinality = $aspect['aspectConstraint']['itemToAspectCardinality'];
                $aspectMode = $aspect['aspectConstraint']['aspectMode'];
                $aspectRequired = $aspect['aspectConstraint']['aspectRequired'];
                $aspectUsage = $aspect['aspectConstraint']['aspectUsage'];

                        echo "<tr>
                                <td><input type=\"checkbox\" name=\"aspects[]\" value=\"$localizedAspectName\"></td>
                                <td>$localizedAspectName</td>
                                <td>$aspectDataType</td>
                                <td>$itemToAspectCardinality</td>
                                <td>$aspectMode</td>
                                <td>$aspectRequired</td>
                                <td>$aspectUsage</td>
                              </tr>";
                    
            
            }
            ?>
        </tbody>
    </table>
    <?}?>
</form>
</body>
</html>
<?

ob_end_flush();?>