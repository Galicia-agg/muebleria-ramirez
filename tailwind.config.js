import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#FAF6F1',
                    100: '#F0E4D8',
                    200: '#E1C7B0',
                    300: '#CBA07E',
                    400: '#B47F55',
                    500: '#8B5E34',
                    600: '#6F4A29',
                    700: '#593B21',
                    800: '#47301C',
                    900: '#3B2818',
                    950: '#201409',
                },
                accent: {
                    50: '#FBF6EA',
                    100: '#F5E8C8',
                    200: '#EBD08D',
                    300: '#DFB65A',
                    400: '#CE9D3B',
                    500: '#B8862E',
                    600: '#966B24',
                    700: '#77531D',
                    800: '#5C4016',
                    900: '#4A3412',
                    950: '#2A1D0A',
                },
            },
        },
    },

    plugins: [forms, typography],
};
