const defaultTheme = require('tailwindcss/defaultTheme');
const themes = require('./resources/tailwindTheme/index');

const generateColorClass = (variable) => {
  return ({ opacityValue }) =>
    opacityValue
      ? `rgb(var(--${variable}) / ${opacityValue})`
      : `rgb(var(--${variable}))`
}


/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{vue,ts,js}',
        './node_modules/atmosphere-ui/dist/**/*.{vue,js,ts}',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ...themes.blueLight,
                primary: generateColorClass('ic-primary-color'),
                secondary: generateColorClass('ic-secondary-color')
            },
            gridTemplateRows: {
              'section-footer': '1fr 100px',
              'section-footer-card': '1fr 50px',
            }
        },
    },

    plugins: [require('@tailwindcss/typography')],
};
