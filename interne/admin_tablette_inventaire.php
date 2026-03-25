<?
$rangees = array('A');//, 'B', 'C');
$rangeesTablette = array(1);//, 2);
$hauteurs = array('A', 'B', 'C', 'D', 'E');
$positions = array(1, 2, 3, 4, 5, 6);
$positions2 = array('A', 'B');

foreach ($rangees as $rangee) {
    foreach ($rangeesTablette as $rangeeTablette) {
        foreach ($hauteurs as $hauteur) {
            foreach ($positions as $position) {
           //     foreach ($positions2 as $position2) {
                    $codeFinal = $rangee . $rangeeTablette . $hauteur . $position ;//. $position2;
                 //   echo $codeFinal;
                    echo '<script>window.open("createlabeltablette_new.php?tablette='.$codeFinal.'","_blank")</script>';
               //     sleep(5); // Attendre pendant 5 secondes

           //     }
            }
        }
    }
}


?>