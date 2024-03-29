const defaultTheme = require('tailwindcss/defaultTheme');
const themes = require('./resources/tailwindTheme/index');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{vue,ts,js}',
        './node_modules/atmosphere-ui/**/*.{vue,js,ts}',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ...themes.blueLight
            },
            gridTemplateRows: {
              'section-footer': '1fr 100px',
              'section-footer-card': '1fr 50px',
            }
        },
    },

    plugins: [require('@tailwindcss/typography')],
};
