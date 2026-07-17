import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Outfit', ...defaultTheme.fontFamily.sans],
                pwoa: ['Raleway', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                pwoa: {
                    blue: '#0095d7',
                    green: '#a2c53e',
                    dark: '#333333',
                }
            }
        },
    },

    plugins: [forms],
};
