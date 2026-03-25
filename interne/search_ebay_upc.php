<?php

// Include la fonction search_ebay_all_results et get_ebay_product_upc
include 'connection.php';

// Fonction pour récupérer les UPC et calculer le pourcentage d'occurrence
function get_upc_list($connectionapi, $response) {
    $response = json_decode($response, true);
    // Vérification de l'existence de la clé 'searchResult' dans la réponse
    if (!isset($response['searchResult']) || !isset($response['searchResult']['item'])) {
        return []; // Retourne une liste vide si 'searchResult' ou 'item' n'est pas défini
    }

    $items = $response['searchResult']['item'];
    $upc_list = array();
  //print("<pre>".print_r ($items,true )."</pre>");
    if(isset($items['itemId'])){
        $itemstmp=$items;
        unset($items);
        $items[0]= $itemstmp;
    }
 //print("<pre>".print_r ($items,true )."</pre>");
    foreach ($items as $item) {
      //print("<pre>".print_r ($item,true )."</pre>");

        // Appel de la fonction pour récupérer le UPC pour chaque élément
        $upc = get_ebay_product_upc($connectionapi, $item['itemId']);
        
        // Vérifie si le UPC est récupéré avec succès et a une longueur de 12 ou 13 caractères
        if ($upc && (strlen($upc) == 12 || strlen($upc) == 13)) {
            // Transforme les UPC commençant par "00" en "0" et enleve le deuxième zéro si nécessaire
            if (substr($upc, 0, 2) == "00") {
                $upc = "0" . substr($upc, 2);
            } elseif (substr($upc, 0, 1) == "0") {
                $upc = substr($upc, 1);
            }

            if (!isset($upc_list[$upc])) {
                $upc_list[$upc] = 1;
            } else {
                $upc_list[$upc]++;
            }
        }
    }

    // Calcul du pourcentage d'occurrence pour chaque UPC
    $total_items = count($items);
    foreach ($upc_list as $upc => $count) {
        $upc_list[$upc] = ($count / $total_items) * 100;
    }

    return $upc_list;
}

// Appel de la fonction search_ebay_all_results

$vendu = isset($_GET['vendu']) ? true : false;
$q = isset($_GET['q']) ? $_GET['q'] : '';
$gtin = isset($_GET['gtin']) ? $_GET['gtin'] : '';
$sort = "";
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;

$response = search_ebay_all_results($connectionapi, $vendu, $q, $gtin, $sort, $limit);

// Récupération de la liste des UPC et de leur pourcentage d'occurrence
//print("<pre>".print_r ($response,true )."</pre>");

$upc_list = get_upc_list($connectionapi, $response);

// Affichage des UPC et de leur pourcentage d'occurrence
echo "<h1>Liste des UPC et leur pourcentage d'occurrence :</h1>";
echo "<ul>";
foreach ($upc_list as $upc => $percentage) {
    echo "<li><a href='https://www.ebay.com/sh/research?marketplace=EBAY-US&keywords=$upc&dayRange=1095&categoryId=0&offset=0&limit=50&tabName=SOLD&tz=America%2FToronto' target='ebayupc'>$upc : $percentage% (" . strlen($upc) . ")</a>";
    echo " ----> <a href='listing.php?sku=$upc' target='listing'>Lister</a></li>";
}
echo "</ul>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche eBay</title>
</head>
<body>
    <h1>Recherche eBay</h1>
    <form action="search_ebay_upc.php" method="get">
        <label for="query">Recherche :</label>
        <input type="text" id="query" name="q" required><br><br>
        
        <label for="limit">Limite :</label>
        <input type="number" id="limit" name="limit" value="10" min="1" max="100"><br><br>

        <label for="gtin">GTIN :</label>
        <input type="text" id="gtin" name="gtin"><br><br>

        <label for="sold">Vendu :</label>
        <input type="checkbox" id="sold" name="vendu" value="1"><br><br>

        <input type="submit" value="Rechercher">
    </form>
    <script>
        // Mettre automatiquement le focus sur le champ de texte lors du chargement de la page
        window.onload = function() {
            document.getElementById('query').focus();
        };
    </script>
</body>
</html>
