/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.php", // Scan PHP files for Tailwind classes
    "./assets/js/**/*.js",   // Scan JS files for Tailwind classes
  ],
  theme: {
    extend: {
      // Extend Tailwind's default theme here if needed
      // For example:
      // colors: {
      //   'primary': '#007bff',
      // },
    },
  },
  plugins: [
    // Add any Tailwind CSS plugins here, e.g., for Shadcn UI components
    // require('@tailwindcss/forms'), // Example plugin
  ],
}