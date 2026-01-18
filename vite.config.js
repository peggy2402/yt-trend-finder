import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import path from 'path';
import { globSync } from 'glob';

export default defineConfig({
    build: {
        // CHỈNH SỬA QUAN TRỌNG:
        // Đổi từ true sang chuỗi tên file để ép Vite đưa file ra thư mục gốc
        // giúp Laravel cũ có thể tìm thấy.
        manifest: 'manifest.json', 
        outDir: 'public/build',
        rollupOptions: {
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // 'resources/js/clearInputHistory.js',
                // 'resources/js/submitForm.svelte.js',
                // 'resources/js/timeZoneDatetime.js',
                ...globSync("resources/js/Pages/**/*.svelte")
            ],
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // 'resources/js/clearInputHistory.js',
                // 'resources/js/submitForm.svelte.js',
                // 'resources/js/timeZoneDatetime.js',
                ...globSync("resources/js/Pages/**/*.svelte")
            ],
            // ssr: 'resources/js/ssr.js', 
            refresh: true,
        }),
        svelte(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '~': path.resolve(__dirname, 'node_modules'),
            '^': path.resolve(__dirname, 'public'),
        },
    },
});