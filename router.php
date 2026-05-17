<?php
// PHP built-in server router — serves static files before falling through to Laravel
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$public = __DIR__ . '/public';

if ($uri !== '/' && file_exists($public . $uri)) {
    return false;
}

require_once $public . '/index.php';
