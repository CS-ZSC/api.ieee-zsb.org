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

// For API requests (except documentation), capture and modify output
if (str_starts_with($uri, '/api/') && !str_starts_with($uri, '/api/documentation')) {
    // Capture Laravel output
    ob_start();
    require_once __DIR__.'/public/index.php';
    $content = ob_get_clean();

    // Strip any leading/trailing whitespace from captured output
    $content = trim($content);

    // Clear any headers Laravel set and send our own (only if headers haven't been sent)
    if (!headers_sent()) {
        header_remove();
        header('Content-Type: application/json');
        header('Content-Length: ' . strlen($content));
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    }

    echo $content;
    return;
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
