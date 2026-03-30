<?php
$files = [
    '/home/n7f9655/public_html/phoenixliquidation/administrator/controller/shopmanager/maintenance/image.php' => [
        'controller',
    ],
    '/home/n7f9655/public_html/phoenixliquidation/administrator/model/shopmanager/maintenance/image.php' => [
        'model',
    ],
    '/home/n7f9655/public_html/phoenixliquidation/administrator/view/template/shopmanager/maintenance/image.twig' => [
        'twig:image',
    ],
    '/home/n7f9655/public_html/phoenixliquidation/administrator/view/template/shopmanager/maintenance/image_list.twig' => [
        'twig:image_list',
    ],
];

foreach ($files as $path => $replacements) {
    $type = array_shift($replacements);
    $content = file_get_contents($path);
    $before = substr_count($content, 'shopmanager/maintenance_image');

    // Replace all route/load path strings
    $content = str_replace('shopmanager/maintenance_image', 'shopmanager/maintenance/image', $content);

    $after = substr_count($content, 'shopmanager/maintenance_image');
    file_put_contents($path, $content);
    echo "[$type] remplace=$before restant=$after -> " . basename($path) . "\n";
}

echo "DONE\n";
