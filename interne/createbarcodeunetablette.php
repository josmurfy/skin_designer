<?php
require_once '/var/www/html/phpqrcode/qrlib.php';

// Fonction pour générer le code QR et le texte sur l'étiquette
function generateQRCodeLabel($format) {
    ini_set('display_errors', 1);
error_reporting(E_ALL);
    // Créer une image pour l'étiquette
    $labelWidth = 900; // Largeur de l'étiquette en pixels
    $labelHeight = 300; // Hauteur de l'étiquette en pixels
    $label = imagecreatetruecolor($labelWidth, $labelHeight);

    // Définir les couleurs
    $white = imagecolorallocate($label, 255, 255, 255);
    $black = imagecolorallocate($label, 0, 0, 0);

    // Remplir l'étiquette avec la couleur blanche
    imagefill($label, 0, 0, $white);

    // Générer le code QR
    $qrCodeData = $format;
    $qrCodeSize = 200; // Taille du code QR en pixels
    $qrCodeMargin = 10; // Marge autour du code QR en pixels
    QRcode::png($qrCodeData, $label, QR_ECLEVEL_L, $qrCodeSize, 1);
    var_dump($label);
    // Ajouter le texte sur l'étiquette
    $text = 'Format: ' . $format;
    $textFont = '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf'; // Chemin vers la police TrueType
    $textSize = 18; // Taille de la police en points
    $textX = 10; // Position horizontale du texte en pixels
    $textY = $qrCodeSize + $qrCodeMargin + 30; // Position verticale du texte en pixels
    imagettftext($label, $textSize, 0, $textX, $textY, $black, $textFont, $text);

    // Afficher l'image de l'étiquette dans le navigateur
    header('Content-Type: image/png');
    imagepng($label);
    imagedestroy($label);
}

// Exemple d'utilisation
$format = 'A-1-A-1';
generateQRCodeLabel($format);
?>
