<?php
// Database configurations
$phoenixsupplies_db =new mysqli("localhost", "n7f9655_n7f9655", "jnthngrvs01$$", "n7f9655_phoenixsupplies");
$phoenixliquidation_db = new mysqli("localhost", "n7f9655_n7f9655", "jnthngrvs01$$", "n7f9655_phoenixliquidation");


// Check connection
if ($phoenixsupplies_db->connect_error || $phoenixliquidation_db->connect_error) {
    die("Connection failed: " . $phoenixsupplies_db->connect_error . " " . $phoenixliquidation_db->connect_error);
}

// Function to update category descriptions based on `specifics` value   


function sync_category_description($source_db, $target_db, $language_id = 1) {

    $nb=0;
    // Sélectionner les catégories avec `specifics` et les champs supplémentaires non vides ou non nuls
    $query = "
        SELECT category_id, specifics, name, meta_keyword, description, meta_title, meta_description 
        FROM oc_category_description 
        WHERE specifics IS NOT NULL 
          AND TRIM(name) <> '' 
          AND TRIM(meta_keyword) <> '' 
          AND TRIM(description) <> '' 
          AND TRIM(meta_title) <> '' 
          AND TRIM(meta_description) <> '' 
          AND language_id = ? 
      
         
    ";// AND category_id = 53297 
   // LIMIT 1
    $stmt = $source_db->prepare($query);
    $stmt->bind_param("i", $language_id);
    $stmt->execute();
    $result = $stmt->get_result();
    //print("<pre>" . print_r('16:synchro.php', true) . "</pre>");
    //print("<pre>" . print_r($result, true) . "</pre>");
    while ($row = $result->fetch_assoc()) {
        $category_id = $row['category_id'];
        $specifics = $row['specifics'];
        $name = $row['name'];
        $meta_keyword = $row['meta_keyword'];
        $description = $row['description'];
        $meta_title = $row['meta_title'];
        $meta_description = $row['meta_description'];

        // Debug: Afficher les données traitées
        //print("<pre>" . print_r('16:synchro.php', true) . "</pre>");
        //print("<pre>" . print_r($row, true) . "</pre>");
        $nb++;
        // Mettre à jour ou insérer les données dans la base cible
        $update_query = $target_db->prepare("
            INSERT INTO oc_category_description (category_id, language_id, specifics, name, meta_keyword, description, meta_title, meta_description) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
                specifics = VALUES(specifics), 
                name = VALUES(name), 
                meta_keyword = VALUES(meta_keyword), 
                description = VALUES(description), 
                meta_title = VALUES(meta_title), 
                meta_description = VALUES(meta_description)
        ");
        $update_query->bind_param(
            "iissssss", 
            $category_id, 
            $language_id, 
            $specifics, 
            $name, 
            $meta_keyword, 
            $description, 
            $meta_title, 
            $meta_description
        );
        $update_query->execute();
    }
    
    $stmt->close();
    //print("<pre>" . print_r('NB traiter:'.$nb, true) . "</pre>");
}

    
// Function to copy physical images between directories
function copy_images($source_dir, $target_dir) {
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    foreach (glob("$source_dir/*") as $file) {
        $file_name = basename($file);
        if (!file_exists("$target_dir/$file_name")) {
            copy($file, "$target_dir/$file_name");
        }
    }
}

// Sync from phoenixliquidation to phoenixsupplies
//sync_category_description($phoenixliquidation_db, $phoenixsupplies_db);
sync_category_description($phoenixliquidation_db, $phoenixsupplies_db, 1);
sync_category_description($phoenixliquidation_db, $phoenixsupplies_db, 2);
copy_images('/home/n7f9655/public_html/phoenixliquidation/image/data/category', '/home/n7f9655/public_html/phoenixsupplies/image/data/category');

// Sync from phoenixsupplies to phoenixliquidation
sync_category_description($phoenixsupplies_db, $phoenixliquidation_db,1);
sync_category_description($phoenixsupplies_db, $phoenixliquidation_db,2);
copy_images('/home/n7f9655/public_html/phoenixsupplies/image/data/category', '/home/n7f9655/public_html/phoenixliquidation/image/data/category');

// Sync other tables (oc_category, oc_category_filter, etc.)
$tables_to_sync = [
  //  "oc_category",
  //  "oc_category_filter",
  //  "oc_category_path",
    "oc_category_specifics",
 //   "oc_category_to_layout",
 //   "oc_category_to_store"
];

foreach ($tables_to_sync as $table) {
   // $phoenixsupplies_db->query("REPLACE INTO `n7f9655_phoenixliquidation`.`$table` SELECT * FROM `n7f9655_phoenixsupplies`.`$table`");
    $phoenixliquidation_db->query("REPLACE INTO `n7f9655_phoenixsupplies`.`$table` SELECT * FROM `n7f9655_phoenixliquidation`.`$table`");
}

// Close connections
$phoenixsupplies_db->close();
$phoenixliquidation_db->close();
?>
