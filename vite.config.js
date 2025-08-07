import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    define: {
        "process.env": {
            VITE_APP_NAME: JSON.stringify(process.env.VITE_APP_NAME),
            VITE_APP_URL: JSON.stringify(process.env.VITE_APP_URL),
        },
    },
    build: {
        manifest: true,
        outDir: "public/build",
        emptyOutDir: true,
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
