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
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // 라이브러리 청크 분할
                    if (id.includes('node_modules')) {
                        if (id.includes('react') || id.includes('react-dom')) {
                            return 'react-vendor';
                        }
                        if (id.includes('react-router')) {
                            return 'router-vendor';
                        }
                        if (id.includes('@tanstack/react-query')) {
                            return 'query-vendor';
                        }
                        if (id.includes('react-window')) {
                            return 'virtualization-vendor';
                        }
                        if (id.includes('axios')) {
                            return 'http-vendor';
                        }
                        if (id.includes('tailwind') || id.includes('clsx')) {
                            return 'style-vendor';
                        }
                        return 'other-vendor';
                    }
                    
                    // 페이지별 청크 분할
                    if (id.includes('/pages/community/')) {
                        return 'community-pages';
                    }
                    if (id.includes('/pages/chat/')) {
                        return 'chat-pages';
                    }
                    if (id.includes('/components/chat/')) {
                        return 'chat-components';
                    }
                    if (id.includes('/hooks/api/')) {
                        return 'api-hooks';
                    }
                }
            }
        },
        // 압축 최적화
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.debug']
            }
        },
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