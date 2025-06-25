/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
       colors: {
                        'eco-green': '#2e7d32',
                        'eco-dark': '#1a202c',
                        'eco-light': '#f0fdf4',
                        'eco-text': '#e2e8f0',
                    }
    },
  },
  plugins: [],
}