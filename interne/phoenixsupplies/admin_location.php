<?php
// Se connecter à la base de données (à adapter selon votre configuration)
include 'connection.php';

// Exécuter la requête SQL
$sql = "
    SELECT
        P.location,
        SUM(P.weight * P.quantity) AS poids_total,
        SUM(P.length * P.height * P.width * P.quantity) / 1728 AS volume_total_cubic_feet
    FROM
       `oc_product` AS P 
    WHERE
        P.quantity > 0 AND P.location LIKE '%-%'
    GROUP BY
        P.location
    ORDER BY
        P.location ASC;
";

$req = mysqli_query($db, $sql);

// Initialiser un tableau multidimensionnel pour stocker les données par groupe de location
$groupes = array();

// Traiter les résultats
if (mysqli_num_rows($req) > 0) {
    while ($row = mysqli_fetch_assoc($req)) {
        // Extraire la rangée, la tablette et la colonne de la location (ex: A1-C)
        preg_match('/^([A-Za-z]+)(\d+)-([A-Za-z]+)$/', $row['location'], $matches);

        if (count($matches) === 4) {
            // Si la location est correctement formatée, ajouter la ligne au tableau du groupe correspondant
            $rangee = $matches[1];
            $tablette = $matches[2];
            $colonne = $matches[3];

            $groupes[$rangee][$tablette][$colonne] = array(
                'poids_total' => $row['poids_total'],
                'volume_total_cubic_feet' => $row['volume_total_cubic_feet']
            );
        } else {
            // Si la location contient un point-virgule, la diviser
            $locations = explode(';', $row['location']);

            foreach ($locations as $location) {
                preg_match('/^([A-Za-z]+)(\d+)-([A-Za-z]+)$/', $location, $matches);

                if (count($matches) === 4) {
                    $rangee = $matches[1];
                    $tablette = $matches[2];
                    $colonne = $matches[3];

                    $groupes[$rangee][$tablette][$colonne] = array(
                        'poids_total' => $row['poids_total'] / count($locations),
                        'volume_total_cubic_feet' => $row['volume_total_cubic_feet'] / count($locations)
                    );
                }
            }
        }
    }
}

// Trier les rangées par ordre alphabétique descendant
// krsort($groupes);

// Générer le tableau HTML

$poidstotal=0;
$poidstotal_inventaire=0;
$nb_rangee=0;
// Boucler sur les groupes
foreach ($groupes as $rangee => $tablettes) {
    echo "<table border='1'>";
echo "<tr><th>Rangée</th><th colspan='2'>Tablette</th><th colspan='2'>Colonne</th></tr>";
echo "<tr><td></td><td>Tablette</td><td>Rangee</td><td>lbs</td><td>Volume</td></tr>";
    krsort($tablettes);
    foreach ($tablettes as $tablette => $colonnes) {
        ksort($colonnes);
        echo "<tr>";
        echo "<td rowspan='" . count($colonnes) . "'>$rangee</td>";
        echo "<td rowspan='" . count($colonnes) . "'>$tablette</td>";

        $premiereColonne = true;

        foreach ($colonnes as $colonne => $data) {
            if (!$premiereColonne) {
                echo "<tr>";
            }

            // Définir la couleur de fond en fonction de la valeur de poids
            $bg_color = 'green'; // Par défaut, vert
            if ($data['poids_total'] > 100) {
                $bg_color = 'black';
                $color = 'white';
            } elseif ($data['poids_total'] > 60) {
                $bg_color = '#999999';
                $color = 'white';
            } elseif ($data['poids_total'] > 30) {
                $bg_color = '#d3d3d3';
                $color = 'black';
            } else{
                $bg_color = 'white';
                $color = 'black';
            }
            $poidstotal+=$data['poids_total'];
           echo "<td style='background-color: $bg_color; color: $color;'>$colonne</td>";
           echo "<td style='background-color: $bg_color; color: $color;'>" . number_format($data['poids_total'], 2) . "</td>";
           echo "<td style='background-color: $bg_color; color: $color;'>" . number_format($data['volume_total_cubic_feet'], 2) . "</td>";
            echo "</tr>";

            $premiereColonne = false;
        
        }
    
    } echo "<tr>";
    echo "Poids Total rangee ".$rangee.": ".number_format($poidstotal, 2)." LBS";
    $poidstotal_inventaire+=number_format($poidstotal, 2);
    $nb_rangee++;
    echo "</tr>";
    $poidstotal=0;
    echo "</table>";
    echo "</br>";
    echo "<table border='1'>";

}

echo "</table>";

echo "<br>MOYENNE PAR RANGEE: ".     $poidstotal_inventaire/$nb_rangee;
// Fermer la connexion à la base de données
mysqli_close($db);
?>
