<?php
/**
 * Simple router for frontend pages
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$page = trim($path, '/');
$page = $page ?: 'home';

$pages = ['home', 'login', 'dashboard', 'admin', 'rent'];

if (!in_array($page, $pages)) {
    http_response_code(404);
    echo '<h1>Page not found</h1>';
    exit;
}

include __DIR__ . '/' . $page . '.php';
?>

