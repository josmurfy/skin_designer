<?php
// Add Sportflics to card manufacturers
require_once('../config.php');

// Connect to database
$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Check if Sportflics already exists
$check = $db->query("SELECT * FROM oc_card_manufacturer WHERE name = 'Sportflics'");
if ($check->num_rows > 0) {
    echo "Sportflics existe déjà dans la base de données.\n";
    $db->close();
    exit;
}

// Insert Sportflics
$stmt = $db->prepare("INSERT INTO oc_card_manufacturer (name, status) VALUES (?, 1)");
$name = 'Sportflics';
$stmt->bind_param('s', $name);

if ($stmt->execute()) {
    echo "✅ Sportflics ajouté avec succès!\n";
    
    // Display new total
    $new_count_result = $db->query("SELECT COUNT(*) as total FROM oc_card_manufacturer");
    $new_count = $new_count_result->fetch_assoc()['total'];
    echo "   Total manufacturers: " . $new_count . "\n";
} else {
    echo "❌ Erreur lors de l'ajout: " . $stmt->error . "\n";
}

$stmt->close();
$db->close();
