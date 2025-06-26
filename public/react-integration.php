<?php
/**
 * React Frontend Integration for TopMKT Platform
 * 
 * This file serves the React frontend and handles routing for SPA
 */

// Check if request is for React frontend
function isReactRoute($path) {
    // Define routes that should be handled by React
    $reactRoutes = [
        '/frontend',
        '/frontend/',
        '/frontend/login',
        '/frontend/signup',
        '/frontend/lectures',
        '/frontend/community',
        '/frontend/profile',
        '/frontend/admin',
        '/frontend/my'
    ];
    
    foreach ($reactRoutes as $route) {
        if (strpos($path, $route) === 0) {
            return true;
        }
    }
    
    return false;
}

// Handle React routing
function serveReactApp() {
    $frontendPath = __DIR__ . '/frontend';
    $indexPath = $frontendPath . '/index.html';
    
    if (!file_exists($indexPath)) {
        http_response_code(404);
        echo 'React app not built. Please run: npm run build';
        return;
    }
    
    // Set proper headers
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Serve the React app
    readfile($indexPath);
}

// Add to your main index.php routing logic:
// if (isReactRoute($_SERVER['REQUEST_URI'])) {
//     serveReactApp();
//     exit;
// }
?>