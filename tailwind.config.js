/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./vista/**/*.php",
        "./vista/**/*.html",
        "./*.php",
        "./*.html"
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
    plugins: [],
}
