/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      screens: {
        '3xl': '1800px',
        '4xl': '2400px',
      }
    },
    fontFamily: {
        'sans': ["ibm-plex-mono", "ui-monospace", "SFMono-Regular", "Menlo", "Monaco", "Consolas", "monospace"],
    }
  },
  plugins: [
    require('@tailwindcss/line-clamp'),
  ],
}