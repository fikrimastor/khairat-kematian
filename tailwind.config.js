import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#4F46E5',
                    '50': '#EBEAFD',
                    '100': '#D7D5FB',
                    '200': '#AEABF8',
                    '300': '#8682F4',
                    '400': '#5D58F1',
                    '500': '#4F46E5',
                    '600': '#2A20DE',
                    '700': '#211AB3',
                    '800': '#191389',
                    '900': '#120D5E',
                },
                success: {
                    DEFAULT: '#10B981',
                    '50': '#E6F6F0',
                    '100': '#CDEEE1',
                    '200': '#9ADEC3',
                    '300': '#68CDA4',
                    '400': '#35BD86',
                    '500': '#10B981',
                    '600': '#0D9267',
                    '700': '#0A6B4C',
                    '800': '#064431',
                    '900': '#031D15',
                },
                danger: {
                    DEFAULT: '#EF4444',
                    '50': '#FDEDED',
                    '100': '#FBDBDB',
                    '200': '#F7B8B8',
                    '300': '#F39494',
                    '400': '#EF7171',
                    '500': '#EF4444',
                    '600': '#EA1818',
                    '700': '#C01212',
                    '800': '#8B0D0D',
                    '900': '#570808',
                }
            }
        },
    },

    plugins: [forms],
};
