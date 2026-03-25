<?php

include '../phoenixsupplies/connection.php';

// Configuration de la base de données
//include '../connection.php';

// Vérifier la connexion
if ($db->connect_error) {
    die("Connexion échouée: " . $db->connect_error);
}
/*
// Sélectionner tous les produits de la table `oc_product`
$sql = "SELECT product_id, product_specifics FROM oc_product";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    // Mettre à jour les champs `product_specifics` dans la table `oc_product_description`
    while($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $product_specifics = $row['product_specifics'];

        $update_sql = "UPDATE oc_product_description SET product_specifics = ? WHERE product_id = ? AND language_id = 1";
        $stmt = $db->prepare($update_sql);
        if ($stmt) {
            $stmt->bind_param("si", $product_specifics, $product_id);
            if (!$stmt->execute()) {
                echo "Erreur lors de la mise à jour du produit ID $product_id: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Erreur lors de la préparation de la requête pour le produit ID $product_id: " . $db->error;
        }
    }
} else {
    echo "Aucun produit trouvé dans la table `oc_product`.";
}*/

// Sélectionner la quantité de chaque produit depuis la table `oc_category`
$sql_magasin = "SELECT category_id, ebay_listableOLD FROM oc_category_description where language_id=1 AND ebay_listableOLD=1 ";
$result_magasin = $db->query($sql_magasin);

if ($result_magasin->num_rows > 0) {
    // Mettre à jour le champ `specifics` dans la table `oc_category_description`
    while($row_magasin = $result_magasin->fetch_assoc()) {
        $category_id_magasin = $row_magasin['category_id'];
        $ebay_listable = $row_magasin['ebay_listableOLD'];

        $update_sql_magasin = "UPDATE oc_category SET ebay_listable = ? WHERE category_id = ? ";
        $stmt_magasin = $db->prepare($update_sql_magasin);
        if ($stmt_magasin) {
            $stmt_magasin->bind_param("ii", $ebay_listable, $category_id_magasin);
            if (!$stmt_magasin->execute()) {
                echo "Erreur lors de la mise à jour du produit ID $category_id_magasin: " . $stmt_magasin->error;
            }
            $stmt_magasin->close();
        } else {
            echo "Erreur lors de la préparation de la requête pour le produit ID $category_id_magasin: " . $db->error;
        }
        // Mise à jour de la table oc_category_description
        $update_sql_desc = "UPDATE oc_category_description SET ebay_listableOLD = 9 WHERE category_id = ?";
        $stmt_desc = $db->prepare($update_sql_desc);
        if ($stmt_desc) {
            $stmt_desc->bind_param("i", $category_id_magasin);
            if (!$stmt_desc->execute()) {
                echo "Erreur lors de la mise à jour de la description pour la catégorie ID $category_id_magasin: " . $stmt_desc->error;
            }
            $stmt_desc->close();
        } else {
            echo "Erreur lors de la préparation de la requête pour la description de la catégorie ID $category_id_magasin: " . $db->error;
        }
    }
} else {
    echo "Aucun produit trouvé dans la table `oc_category`.";
}

// Fermer la connexion
$db->close();

echo "Mise à jour terminée avec succès!";
?>
