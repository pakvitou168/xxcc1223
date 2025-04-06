import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import commonjs from 'vite-plugin-commonjs'
import svgLoader from 'vite-svg-loader'
import { createRequire } from 'node:module';
import ckeditor5 from '@ckeditor/vite-plugin-ckeditor5';

const require = createRequire(import.meta.url);
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/js/script.js', 'resources/css/core.css', 'resources/css/app.css']
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
                compilerOptions: {
                    isCustomElement: (tag) => ['portal-target', 'ToastContainer', 'portal'].includes(tag), // Ignore `portal-target` as a Vue component
                },
            },
        }),
        ckeditor5({ theme: require.resolve('@ckeditor/ckeditor5-theme-lark'), sourcemap: false }),
        svgLoader(),
        commonjs({
            filter(id) {
                if (id.includes('node_modules/fast-deep-equal/index.js'))
                    return true
            }
        }),
    ],
    resolve: {
        alias: {
            '~': 'node_modules',
            '$': 'jQuery',
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    base: '/build',
    build: {
        sourcemap: false,
        minify: 'esbuild',
        manifest: true,
        outDir: 'public/build',
        assetsDir: 'assets',
        commonjsOptions: {
            transformMixedEsModules: true,
        },
        chunkSizeWarningLimit: 1000
    },
    server: {
        host: 'localhost',
        port: 3000,
        hmr: {
            host: 'localhost',
        },
        cors: true,
        headers: { 'Access-Control-Allow-Origin': '*' }
    },
    css: {
        devSourcemap: true,
        modules: {
            scopeBehaviour: 'local', // Ensures module styles are locally scoped
        },
        preprocessorOptions: {
            css: {
                additionalData: '@import "./resources/css/custom.css";'
            },
            scss: {
                api: 'modern-compiler' // or "modern"
            }
        },
        postcss: './postcss.config.js',
    },

});
