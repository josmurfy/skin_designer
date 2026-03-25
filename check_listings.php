<?php
require 'config.php';

$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$result = $db->query("SELECT l.listing_id, l.set_name,
    CASE WHEN EXISTS (
        SELECT 1 FROM " . DB_PREFIX . "card_listing_description ld
        WHERE ld.listing_id = l.listing_id
        AND ld.ebay_item_id IS NOT NULL
        AND ld.ebay_item_id != ''
    ) THEN 1 ELSE 0 END as is_published
    FROM " . DB_PREFIX . "card_listing l LIMIT 5");

while ($row = $result->fetch_assoc()) {
    echo 'Listing ' . $row['listing_id'] . ': ' . $row['set_name'] . ' - Published: ' . $row['is_published'] . PHP_EOL;
}

$db->close();
?>