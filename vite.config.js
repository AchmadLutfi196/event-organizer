import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/css/auth.css',
                'resources/js/app.js',
                'resources/js/auth.js',
                'resources/js/auth-transitions.js'
            ],
            refresh: true,
        }),
    ],
});
