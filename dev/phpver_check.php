<?php
header('Content-Type: text/plain; charset=utf-8');
echo 'PHP_VERSION=' . PHP_VERSION . "\n";
echo 'SAPI=' . PHP_SAPI . "\n";
echo 'mb_strlen=' . (function_exists('mb_strlen') ? 'yes' : 'no') . "\n";
