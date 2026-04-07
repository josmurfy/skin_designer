<?php
require_once(__DIR__ . '/../config.php');

// Connexion directe à la base de données
$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Vérifier si la modification existe déjà
$result = $mysqli->query("SELECT * FROM `" . DB_PREFIX . "modification` WHERE `code` = 'debug_logger'");
if ($result->num_rows > 0) {
    echo "Modification 'debug_logger' already exists.\n";
    exit;
}

// Lire le fichier XML
$xml_file = DIR_SYSTEM . 'debug_logger.ocmod.xml';
if (!file_exists($xml_file)) {
    echo "File 'debug_logger.ocmod.xml' not found in system/.\n";
    exit;
}

$xml_content = file_get_contents($xml_file);

// Insérer dans la base de données
$stmt = $mysqli->prepare("INSERT INTO `" . DB_PREFIX . "modification` (`name`, `code`, `author`, `version`, `link`, `xml`, `status`, `date_added`) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
$name = 'Debug Logger';
$code = 'debug_logger';
$author = 'PhoenixLiquidation';
$version = '1.0.0';
$link = '';
$status = 1;
$stmt->bind_param('ssssssi', $name, $code, $author, $version, $link, $xml_content, $status);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Modification 'debug_logger' registered successfully.\n";
} else {
    echo "Failed to register modification.\n";
}

$stmt->close();
$mysqli->close();