import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),

        vue(),
        tailwindcss(),
    ],
    server: {
        proxy: {
            "/api": {
                target: "http://127.0.0.1:8000", // Laravel dev server
                changeOrigin: true,
                secure: false,
            },
        },
    },
});
