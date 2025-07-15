/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Titillium Web', 'sans-serif'], // Define como fonte padrão
                titillium: ['Titillium Web', 'sans-serif'] // Ou como uma classe separada
            },
        },
    },
    plugins: [],
}

