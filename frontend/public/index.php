<?php
/**
 * Frontend entry point for MMS
 * Routes all requests to router.php
 */
define('API_BASE', 'http://127.0.0.1:8000/api'); // Dev - change for prod

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$_GET['path'] = $path;
require_once __DIR__ . '/../pages/router.php';
?>

