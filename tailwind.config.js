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
            colors: {
                'outline-variant': '#c3c6d6',
                'on-primary-container': '#c4d2ff',
                'on-error': '#ffffff',
                'surface-variant': '#d6e3ff',
                'on-secondary-container': '#003179',
                'on-error-container': '#93000a',
                'surface-container-highest': '#d6e3ff',
                'error-container': '#ffdad6',
                'secondary-fixed': '#d9e2ff',
                error: '#ba1a1a',
                'secondary-fixed-dim': '#b1c6ff',
                'on-surface-variant': '#434654',
                'surface-container-high': '#dfe8ff',
                'surface-bright': '#f9f9ff',
                'secondary-container': '#709bfe',
                'on-primary': '#ffffff',
                'surface-container-low': '#f0f3ff',
                'tertiary-container': '#a33500',
                'on-tertiary': '#ffffff',
                'primary-container': '#0052cc',
                'tertiary-fixed-dim': '#ffb59b',
                'on-primary-fixed': '#001848',
                'on-surface': '#091c35',
                'inverse-surface': '#20314b',
                outline: '#737685',
                'on-secondary': '#ffffff',
                'tertiary-fixed': '#ffdbcf',
                'on-background': '#091c35',
                'on-secondary-fixed': '#001946',
                'on-tertiary-fixed-variant': '#812800',
                'surface-container-lowest': '#ffffff',
                background: '#f9f9ff',
                tertiary: '#7b2600',
                'surface-dim': '#cadbfc',
                'inverse-primary': '#b2c5ff',
                primary: '#003d9b',
                'primary-fixed': '#dae2ff',
                'inverse-on-surface': '#ecf0ff',
                'on-tertiary-container': '#ffc6b2',
                'surface-tint': '#0c56d0',
                secondary: '#285ab9',
                surface: '#f9f9ff',
                'surface-container': '#e7eeff',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            spacing: {
                'topbar-height': '64px',
                'sidebar-width': '260px',
            },
        },
    },

    plugins: [forms],
};
