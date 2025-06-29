import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
    plugins: [react()],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
        },
    },
    define: {
        global: 'globalThis',
        'process.env': {},
    },
    optimizeDeps: {
        include: ['react', 'react-dom', 'react-router-dom']
    },
    build: {
        outDir: '../../../public/frontend',
        emptyOutDir: true,
        assetsDir: 'assets',
        target: 'es2020',
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['react', 'react-dom'],
                    router: ['react-router-dom'],
                    utils: ['axios', 'clsx', 'tailwind-merge']
                }
            }
        },
        // Force legacy mode for better Node.js compatibility
        reportCompressedSize: false
    },
    server: {
        port: 3000,
        proxy: {
            '/api': {
                target: 'http://localhost',
                changeOrigin: true,
                secure: false,
                configure: (proxy) => {
                    proxy.on('error', (err) => {
                        console.log('proxy error', err);
                    });
                    proxy.on('proxyReq', (proxyReq, req) => {
                        console.log('Sending Request to the Target:', req.method, req.url);
                    });
                    proxy.on('proxyRes', (proxyRes, req) => {
                        console.log('Received Response from the Target:', proxyRes.statusCode, req.url);
                    });
                },
            }
        }
    },
    base: '/frontend/'
});