import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'Noto Sans JP', ...defaultTheme.fontFamily.sans],
            },
            // スクリーンフィットのための高さと幅の拡張
            minHeight: {
                'screen-fit': '100vh',
            },
        },
    },

    plugins: [forms],
    // ダークモードを設定（オプション）
    darkMode: 'class', // または 'media'（システム設定に基づく場合）
};
