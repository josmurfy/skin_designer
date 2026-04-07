<?php
require_once(__DIR__ . '/../config.php');

$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->query("DELETE FROM `" . DB_PREFIX . "modification` WHERE `code` = 'debug_logger'");

// Supprimer aussi l'entrée d'installation si la table existe
$r = $mysqli->query("SHOW TABLES LIKE '" . DB_PREFIX . "extension_install'");
if ($r && $r->num_rows > 0) {
    $mysqli->query("DELETE FROM `" . DB_PREFIX . "extension_install` WHERE `filename` = 'debug_logger.ocmod.zip'");
}
$r2 = $mysqli->query("SHOW TABLES LIKE '" . DB_PREFIX . "modification_install'");
if ($r2 && $r2->num_rows > 0) {
    $mysqli->query("DELETE FROM `" . DB_PREFIX . "modification_install` WHERE `filename` = 'debug_logger.ocmod.zip'");
}

echo "Suppression terminée. Réessaie l’import dans OpenCart.\n";
$mysqli->close();
