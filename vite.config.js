import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        hmr: {
            // host: 'localhost',
            host: "localhost",
            protocol: "ws",
        }
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler'
        }
    },
    plugins: [
        vue({
            template: {
                compilerOptions: {
                    isCustomElement: (tag) => {
                        return tag.startsWith('is-') // (return true)
                    }
                }
            }
        }),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
            // input: [
            //     'resources/css/app.scss',
            //     'resources/js/app.js',
            // ],
            // refresh: true,
        }),
    ],
});
