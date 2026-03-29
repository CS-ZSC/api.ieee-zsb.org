<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/'
);

// Serve static files from public directory
if ($uri !== '/' && file_exists($file = __DIR__.'/public'.$uri)) {
    $mime = get_mime_type($file);
    header('Content-Type: '.$mime);
    readfile($file);
    return;
}

// Serve Swagger assets from vendor directory
if (str_starts_with($uri, '/docs/asset/')) {
    $assetPath = __DIR__.'/vendor/swagger-api/swagger-ui/dist/'.substr($uri, 13);
    if (file_exists($assetPath)) {
        $mime = get_mime_type($assetPath);
        header('Content-Type: '.$mime);
        header('Cache-Control: public, max-age=31536000');
        readfile($assetPath);
        return;
    }
}

// Set proper headers for API requests (except documentation)
if (str_starts_with($uri, '/api/') && !str_ends_with($uri, '/api/documentation')) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
}

// Forward everything else to Laravel
require_once __DIR__.'/public/index.php';

function get_mime_type(string $file): string
{
    $extension = pathinfo($file, PATHINFO_EXTENSION);

    return match ($extension) {
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'json'  => 'application/json',
        'xml'   => 'application/xml',
        'html'  => 'text/html',
        'txt'   => 'text/plain',
        'png'   => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'webp'  => 'image/webp',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'otf'   => 'font/otf',
        'pdf'   => 'application/pdf',
        'zip'   => 'application/zip',
        default => 'application/octet-stream',
    };
}
