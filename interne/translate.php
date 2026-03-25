<?php

require_once '/home/n7f9655/public_html/phoenixliquidation/vendor/autoload.php';

use Google\Cloud\Translate\V2\TranslateClient;

$translate = new TranslateClient();
$json = array();
$json['success'] = "";
//$_POST['text_field']='openbox';
//$_POST['targetLanguage']='fr';
if (isset($_POST['text_field']) && !empty($_POST['text_field']) && isset($_POST['targetLanguage']) && !empty($_POST['targetLanguage'])) {
    $result = $translate->translate($_POST['text_field'], [
        'target' => $_POST['targetLanguage'],
    ]);
    
    $json['success'] = addslashes($result['text']);
}

//print("<pre>".print_r ($json,true )."</pre>");

//echo $result['text'];
// Définir l'en-tête de la réponse pour JSON
header('Content-Type: application/json');
// Envoyer la réponse JSON
echo json_encode($json);
exit;

?>
