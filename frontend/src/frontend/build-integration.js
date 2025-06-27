#!/usr/bin/env node

import { execSync } from 'child_process'
import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

const COLORS = {
  reset: '\x1b[0m',
  green: '\x1b[32m',
  blue: '\x1b[34m',
  yellow: '\x1b[33m',
  red: '\x1b[31m'
}

function log(message, color = 'reset') {
  console.log(`${COLORS[color]}${message}${COLORS.reset}`)
}

function createPhpIntegrationFile() {
  const phpIntegrationContent = `<?php
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
?>`

  const outputPath = path.join(__dirname, '../../../public/react-integration.php')
  fs.writeFileSync(outputPath, phpIntegrationContent)
  log(`✅ Created PHP integration file: ${outputPath}`, 'green')
}

function createNginxConfig() {
  const nginxConfig = `# Nginx configuration for React + PHP integration
location /frontend {
    try_files $uri $uri/ /frontend/index.html;
}

# Handle React app assets
location /frontend/assets {
    expires 1y;
    add_header Cache-Control "public, immutable";
    try_files $uri =404;
}

# API routes go to PHP
location /api {
    try_files $uri $uri/ /index.php?$query_string;
}

# Default PHP handling
location / {
    try_files $uri $uri/ /index.php?$query_string;
}`

  const outputPath = path.join(__dirname, 'nginx-react-config.conf')
  fs.writeFileSync(outputPath, nginxConfig)
  log(`✅ Created Nginx config: ${outputPath}`, 'green')
}

function createApacheConfig() {
  const apacheConfig = `# Apache configuration for React + PHP integration
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Handle React frontend routes
    RewriteCond %{REQUEST_URI} ^/frontend
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^frontend/(.*)$ /frontend/index.html [L]
    
    # Handle API routes (send to PHP)
    RewriteCond %{REQUEST_URI} ^/api
    RewriteRule ^(.*)$ /index.php [L]
    
    # Default PHP handling
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /index.php [L]
</IfModule>

# Cache static assets
<LocationMatch "\\.(css|js|png|jpg|jpeg|gif|ico|svg)$">
    ExpiresActive On
    ExpiresDefault "access plus 1 year"
</LocationMatch>`

  const outputPath = path.join(__dirname, '.htaccess-react')
  fs.writeFileSync(outputPath, apacheConfig)
  log(`✅ Created Apache config: ${outputPath}`, 'green')
}

async function main() {
  try {
    log('🚀 Starting React Frontend Build and Integration...', 'blue')
    
    // Step 1: Clean previous build
    log('🧹 Cleaning previous build...', 'yellow')
    if (fs.existsSync(path.join(__dirname, '../../../public/frontend'))) {
      execSync('rm -rf ../../../public/frontend', { stdio: 'inherit' })
    }
    
    // Step 2: Install dependencies if needed
    log('📦 Installing dependencies...', 'yellow')
    if (!fs.existsSync('node_modules')) {
      execSync('npm install', { stdio: 'inherit' })
    }
    
    // Step 3: Type check
    log('🔍 Running type check...', 'yellow')
    try {
      execSync('npm run type-check', { stdio: 'inherit' })
      log('✅ Type check passed!', 'green')
    } catch (error) {
      log('❌ Type check failed. Please fix TypeScript errors.', 'red')
      process.exit(1)
    }
    
    // Step 4: Build the React app
    log('🏗️ Building React application...', 'yellow')
    execSync('npm run build', { stdio: 'inherit' })
    
    // Step 5: Verify build output
    const buildPath = path.join(__dirname, '../../../public/frontend')
    if (!fs.existsSync(buildPath)) {
      throw new Error('Build output not found')
    }
    
    log('✅ React app built successfully!', 'green')
    
    // Step 6: Create integration files
    log('🔧 Creating integration files...', 'yellow')
    createPhpIntegrationFile()
    createNginxConfig()
    createApacheConfig()
    
    // Step 7: Create update script for PHP routes
    const updateRoutesScript = `<?php
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
?>`

    fs.writeFileSync(path.join(__dirname, 'php-routes-update.php'), updateRoutesScript)
    
    log('🎉 Build and integration completed successfully!', 'green')
    log('', 'reset')
    log('📋 Next steps:', 'blue')
    log('1. Add the PHP routing code to your main routes.php', 'yellow')
    log('2. Configure your web server (Apache/Nginx) using the generated config files', 'yellow')
    log('3. Test the integration by visiting /frontend in your browser', 'yellow')
    log('', 'reset')
    log('📁 Generated files:', 'blue')
    log('- public/frontend/ (React build output)', 'yellow')
    log('- public/react-integration.php (PHP integration helper)', 'yellow')
    log('- nginx-react-config.conf (Nginx configuration)', 'yellow')
    log('- .htaccess-react (Apache configuration)', 'yellow')
    log('- php-routes-update.php (PHP routes update)', 'yellow')
    
  } catch (error) {
    log(`❌ Build failed: ${error.message}`, 'red')
    process.exit(1)
  }
}

main()