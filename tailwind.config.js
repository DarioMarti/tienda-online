/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./src/**/*.php",
        "./animaciones/**/*.js",
        "./styles/**/*.css",
        "./**/*.html",
        "./**/*.php",
        "!./node_modules/**",
    ],
    theme: {
        extend: {
            colors: {
                'fashion-black': '#111111',
                'fashion-accent': '#D4AF37',
                'fashion-gray': '#F5F5F5',
            },
            fontFamily: {
                'editorial': ['Bodoni Moda', 'serif'],
                'sans': ['Jost', 'sans-serif'],
            },
            letterSpacing: {
                'widest': '0.2em',
            },
        },
    },
    safelist: [
        'bg-fashion-black',
        'text-fashion-black',
        'border-fashion-black',
        'bg-fashion-accent',
        'text-fashion-accent',
        'border-fashion-accent',
        'bg-fashion-gray',
        'text-fashion-gray',
        'border-fashion-gray',
    ],
    plugins: [],
}
