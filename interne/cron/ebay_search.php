<?
 include '../connection.php';
 include 'json.php';
$timestamp1 = time();

 $sql = 'SELECT * FROM `oc_product` P where P.ebay_search_sold = "" and quantity>0 group by upc order by P.quantity desc limit 5';//limit 5';
         /* 	LEFT JOIN `oc_product_to_category` PC ON (P.product_id=PC.product_id)
 LEFT JOIN `oc_category_description` CD ON (CD.category_id=PC.category_id AND CD.ebayyes=1 AND CD.language_id=1)
*/
//echo "<br>".$sql."<br>";
 $req = mysqli_query($db,$sql);
 while($data = mysqli_fetch_assoc($req))
 {
 //   $ebay_search=search_ebay($connectionapi,"","",$data['upc'],'',100);
    $ebay_search_sold=search_ebay_sold($connectionapi,"vendu","",$data['upc'],'',100);
    $sql2="UPDATE `oc_product` SET ebay_search ='".addslashes($ebay_search)."',ebay_search_sold ='".addslashes($ebay_search_sold)."',
    ebay_search_date=now() where upc='".$data['upc']."'";
	echo "<br>".$sql2."<br>";
	//$req2=mysqli_query($db,$sql2);
 }
 $timestamp2 = time();

 $difference = $timestamp2 - $timestamp1;


 function calculer_moyenne_prix_ebay($json) {
    $data = json_decode($json, true);
 //print("<pre>".print_r ($data,true )."</pre>"); 

    // Initialiser un tableau pour stocker les prix par état
    $prix_etat = array();

    // Boucler sur les articles pour calculer la moyenne pondérée des prix par état
    foreach ($data["searchResult"]["item"] as $item) {
        // Extraire le prix, l'état et la date de début de l'annonce de l'article
        $prix = floatval($item["sellingStatus"]["currentPrice"]);
        $etat = $item["condition"]["conditionDisplayName"];
        $date_debut = new DateTime($item["listingInfo"]["startTime"]);
        $itemid = $item["itemId"];
        // Ajouter le coût d'expédition au prix
        $prix += floatval($item["shippingInfo"]["shippingServiceCost"]);

        // Si l'état n'est pas encore dans le tableau, l'initialiser avec un tableau vide
        if (!isset($prix_etat[$etat])) {
            $prix_etat[$etat] = array("prix" => array(), "nb_prix" => 0);
        }

        // Ajouter le prix à la liste des prix pour cet état
        $prix_etat[$etat]["prix"][] = array("prix" => $prix, "date" => $date_debut->format("Y-m-d"), "itemid" =>$itemid);
        $prix_etat[$etat]["nb_prix"]++;

    }

    // Calculer la moyenne pondérée des prix pour chaque état
    $moyenne_etat = array();
    foreach ($prix_etat as $etat => $prix) {
        $moyenne_etat[$etat] = array(
            "moyenne_ponderee" => calculer_moyenne_ponderee($prix["prix"]),
            "moyenne" => calculer_moyenne($prix["prix"]),
            "nb_prix" => $prix["nb_prix"]
        );
    }

    // Retourner le tableau des moyennes par état
    return $moyenne_etat;
}




function calculer_moyenne_ponderee($prix) {
   //print("<pre>".print_r ($prix,true )."</pre>"); 
    usort($prix, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

  //print("<pre>".print_r ($prix,true )."</pre>"); 
    // Initialiser le facteur de pondération et le total des prix pondérés à zéro
    $facteur_pond = 0.95;
    $total_prix = 0;
    $total_pond = 0;

    // Trouver la date de vente la plus récente dans la liste de prix
    $date_max = new DateTime("now");
    foreach ($prix as $p) {
        $date_vente = new DateTime($p["date"]);
        if ($date_vente > $date_max) {
            $date_max = $date_vente;
        }
    }

    // Calculer la moyenne pondérée des prix
    foreach ($prix as $p) {
        // Calcul du facteur de pondération en fonction de la date de vente
        $date_vente = new DateTime($p["date"]);
        $diff = $date_max->diff($date_vente);
        $jours = $diff->days;
        $pond = pow($facteur_pond, $jours);

        // Ajout du prix pondéré au total
        $total_prix += $p["prix"] * $pond;
        $total_pond += $pond;
    }

    // Calculer la moyenne pondérée des prix avec deux décimales
    $moyenne_ponderee = round($total_prix / $total_pond, 2);

    return $moyenne_ponderee;
}
function calculer_moyenne($prix) {
    // Initialiser le total des prix à zéro
    $total_prix = 0;

    // Calculer la somme de tous les prix
    foreach ($prix as $p) {
        $total_prix += $p["prix"];
    }

    // Calculer la moyenne des prix
    return $total_prix / count($prix);
}
function calculer_meilleur_prix_ebay($json, $diff_acceptable = .05) {
    
    $data = json_decode($json, true);

    // Initialiser un tableau pour stocker les prix par état
    $prix_etat = array();

    // Boucler sur les articles pour stocker les prix par état avec la date de début de l'annonce
    foreach ($data["searchResult"]["item"] as $item) {
        // Extraire le prix, l'état et la date de début de l'annonce de l'article
        $prix = floatval($item["sellingStatus"]["currentPrice"]);
        $etat = $item["condition"]["conditionDisplayName"];
        $itemid = $item["itemId"];
        $date_debut = new DateTime($item["listingInfo"]["startTime"]);

        // Ajouter le coût d'expédition au prix
        $prix += floatval($item["shippingInfo"]["shippingServiceCost"]);

        // Si l'état n'est pas encore dans le tableau, l'initialiser avec un tableau vide
        if (!isset($prix_etat[$etat])) {
            $prix_etat[$etat] = array("prix" => array(), "nb_prix" => 0, "moyenne_ponderee" => 0);
        }

        // Ajouter le prix à la liste des prix pour cet état
        $prix_etat[$etat]["prix"][] = array("prix" => $prix, "date" => $date_debut->format("Y-m-d"),"itemid"=>$itemid);
        $prix_etat[$etat]["nb_prix"]++;

    }
    
    // Calculer la moyenne pondérée des prix pour chaque état
    foreach ($prix_etat as $etat => $prix) {
        $prix_etat[$etat]["moyenne_ponderee"] = calculer_moyenne_ponderee($prix["prix"]);
        $prix_etat[$etat]["moyenne"] = calculer_moyenne($prix["prix"]);
    }
    // Retourner le tableau des meilleurs prix par état
    $meilleurs_prix = array();
    
    foreach ($prix_etat as $etat => $prix) {
       // $prix_etat[$etat]["prix"] = array_column($prix_etat[$etat]["prix"], "prix");
       $meilleur_prix = trouver_meilleur_prix($prix_etat[$etat]["prix"], $prix_etat[$etat]["moyenne_ponderee"], $prix_etat[$etat]["moyenne"],$diff_acceptable);
        if ($meilleur_prix) {
            $meilleurs_prix[$etat] = array(
                "prix" => $meilleur_prix["prix"],
                "itemid" => $meilleur_prix["itemid"]
            );
        }
    }
  //print("<pre>".print_r ($meilleurs_prix,true )."</pre>"); 

    return $meilleurs_prix;
}
function comparer_dates($a, $b) {
    return strtotime($a['date']) - strtotime($b['date']);
}


// Trouver lemeilleur prix
function trouver_meilleur_prix($prix_etat, $moyenne_ponderee, $moyenne,$diff_acceptable) {
    // Trier les prix du moins cher au plus cher
    usort($prix_etat, 'comparer_dates');

    // Récupérer la date de début des annonces et initialiser le meilleur prix avec le premier prix
    $date_debut = $prix_etat[0]["date"];
    $meilleur_prix = $prix_etat[0];
    $meilleur_prix_date = $meilleur_prix["date"];
  //print("<pre>".print_r ($prix_etat,true )."</pre>");
    // Parcourir tous les prix et trouver le prix le plus proche de la date de début des annonces
    foreach ($prix_etat as $prix) {
        // Calculer la différence en jours entre la date de début des annonces et la date du prix
        $diff_jours = abs(strtotime($prix["date"]) - strtotime($date_debut)) / (60 * 60 * 24);
        

        // Si la différence est plus petite que la différence en jours avec le meilleur prix actuel, mettre à jour le meilleur prix
      //  if ($diff_jours < abs(strtotime($meilleur_prix_date) - strtotime($date_debut)) / (60 * 60 * 24)) {
            // Calculer la différence entre le prix et la moyenne pondérée
         //print("<pre>".print_r ($moyenne,true )."</pre>");
         //print("<pre>".print_r ($moyenne_ponderee,true )."</pre>");
            $diff_prix = abs(($prix["prix"] - $moyenne_ponderee) / $moyenne_ponderee);
        //print("<pre>".print_r ($diff_prix,true )."</pre>");
        //print("<pre>".print_r ($diff_acceptable,true )."</pre>");
            if ($diff_prix < $diff_acceptable) {
         //print("<pre>".print_r ($prix,true )."</pre>");
                $meilleur_prix = $prix;
                $meilleur_prix_date = $meilleur_prix["date"];
            }
      //  }
    }

    // Stocker le meilleur prix, son itemid et sa date dans un tableau
    $meilleur_prix_data = array(
        "prix" => $meilleur_prix["prix"]-.05,
        "itemid" => $meilleur_prix["itemid"],
        "date" => $meilleur_prix_date
    );
  //print("<pre>".print_r ($meilleur_prix_data,true )."</pre>");

    return $meilleur_prix_data;
}
$moyennes_ponderees = calculer_moyenne_prix_ebay($json);
$prix__bas = calculer_meilleur_prix_ebay($json);
// Trouver le meilleur prix pour chaque état
//  $prix_moins_cher_acceptables = calculer_moins_cher_acceptable_ebay($json, 1);
/*   foreach ($prix_moins_cher_acceptables as $etat => $prix_moins_cher) {
    echo "Le prix le plus bas acceptable pour l'état $etat est de " . number_format($prix_moins_cher["prix"], 2, '.', ',') . " USD, trouvé chez " . $prix_moins_cher["itemid"] . ".<br>";
}*/ 
    // Afficher les résultats
    foreach ($moyennes_ponderees as $etat => $moyenne_ponderee) {
        echo "La moyenne pondérée des prix pour l'état $etat est de " . number_format($moyenne_ponderee['moyenne_ponderee'], 2, '.', ',') . " USD, calculée sur un total de " . $moyenne_ponderee['nb_prix'] . " prix.<br>";
      /*  if (isset($meilleurs_prix[$etat])) {
            $meilleur_prix = $meilleurs_prix[$etat];
            echo "Le meilleur prix pour l'état $etat est de " . number_format($meilleur_prix['prix'], 2, '.', ',') . " USD, trouvé chez " . $meilleur_prix['itemid'] . ".<br>"; 
        } else {
            echo "Il n'y a pas de meilleur prix trouvé pour l'état $etat.<br>";
        }*/
    }
   //print("<pre>".print_r ($prix__bas,true )."</pre>");

    foreach ($prix__bas as $etat => $prix_bas) {
        echo "La moyenne pondérée des prix pour l'état $etat est de " . number_format($prix_bas['moyenne_ponderee'], 2, '.', ',') . " USD, calculée sur un total de " . $prix_bas['nb_prix'] . " prix.<br>";
      /*  if (isset($meilleurs_prix[$etat])) {
            $meilleur_prix = $meilleurs_prix[$etat];
            echo "Le meilleur prix pour l'état $etat est de " . number_format($meilleur_prix['prix'], 2, '.', ',') . " USD, trouvé chez " . $meilleur_prix['itemid'] . ".<br>"; 
        } else {
            echo "Il n'y a pas de meilleur prix trouvé pour l'état $etat.<br>";
        }*/
    }

 $minutes = floor($difference / 60);
 $secondes = $difference % 60;
 
 echo "La durée entre les deux dates est de " . $minutes . " minutes et " . $secondes . " secondes.";
    
?>