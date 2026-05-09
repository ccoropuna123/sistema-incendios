import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // Intento de ponerlo en servidor para acceder con celular
    
    /*server: {
        host: true,
        port: 5173,
    }, */
});