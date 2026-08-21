import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import viteCompression from 'vite-plugin-compression'; // Adicione esta linha

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        viteCompression({ // Agora esta função está definida
            algorithm: 'gzip',
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5174,

        hmr: {
            host: '192.168.30.64',
            port: 5174,
        },
    },
    build: {
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'axios', 'lodash'],
                    inertia: ['@inertiajs/vue3'],
                },
            },
        },
    },

});
