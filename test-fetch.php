<?php
header('Content-Type: text/plain');
header('Access-Control-Allow-Origin: *');
error_reporting(0);
echo "TEST: Fetch is working. Method: " . $_SERVER['REQUEST_METHOD'];
?>
