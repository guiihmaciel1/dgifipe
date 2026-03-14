import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['-apple-system', 'BlinkMacSystemFont', 'SF Pro Display', 'SF Pro Text', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                apple: {
                    bg: '#F5F5F7',
                    card: '#FFFFFF',
                    text: '#1D1D1F',
                    muted: '#6E6E73',
                    blue: '#0071E3',
                    green: '#34C759',
                    orange: '#FF9500',
                    red: '#FF3B30',
                    separator: '#D2D2D7',
                },
            },
            borderRadius: {
                'apple': '12px',
                'apple-lg': '16px',
                'apple-xl': '20px',
            },
            boxShadow: {
                'apple': '0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04)',
                'apple-md': '0 4px 12px rgba(0,0,0,0.08)',
                'apple-lg': '0 8px 24px rgba(0,0,0,0.12)',
            },
        },
    },
    plugins: [],
};
