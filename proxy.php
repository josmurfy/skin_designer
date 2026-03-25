<?php
if (isset($_GET['url'])) {
    $imageUrl = $_GET['url'];
    $imageContent = file_get_contents($imageUrl);
    header('Content-Type: image/png');
    echo $imageContent;
} else {
    http_response_code(400);
    echo 'Bad request. URL parameter is missing.';
}
?>
