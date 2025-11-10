import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            publicDirectory: 'public',
            // Point Laravel root to backend directory
            root: resolve(__dirname, '../backend'),
            detectTls: false,
        }),
        tailwindcss(),
    ],
    root: __dirname,
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
    },
    // Load environment variables from backend/.env
    envDir: resolve(__dirname, '../backend'),
    envPrefix: ['VITE_', 'APP_'],
});
