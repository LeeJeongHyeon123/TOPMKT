<?php
// Add this to your main routes.php file

// React Frontend routes
if (strpos($_SERVER['REQUEST_URI'], '/frontend') === 0) {
    // Serve React app
    $frontendPath = __DIR__ . '/../../public/frontend';
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Remove /frontend prefix
    $filePath = str_replace('/frontend', '', $requestPath);
    $fullPath = $frontendPath . $filePath;
    
    // If file exists, serve it
    if ($filePath !== '/' && file_exists($fullPath) && is_file($fullPath)) {
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon'
        ];
        
        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
            header('Cache-Control: public, max-age=31536000'); // 1 year
        }
        
        readfile($fullPath);
        exit;
    }
    
    // Otherwise, serve the React app
    header('Content-Type: text/html; charset=utf-8');
    readfile($frontendPath . '/index.html');
    exit;
}
?>