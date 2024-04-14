/** @type {import('tailwindcss').Config} */
export default {
  content: ["./src/**/*.vue", "./src/**/*.js"],
  theme: {
    extend: {
      scale: {
        "-1": "-1",
      },
    },
  },
  plugins: [],
}
