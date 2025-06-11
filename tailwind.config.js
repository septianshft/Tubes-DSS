/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    // "./resources/**/*.vue", // Uncomment if you use Vue
    "./app/Livewire/**/*.php",
    "./app/View/Components/**/*.php",
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php", // If using Laravel pagination views
    // Add any other paths that contain Tailwind classes
  ],
  darkMode: 'class', // Enables class-based dark mode
  theme: {
    extend: {
      // You can extend your theme here
    },
  },
  plugins: [
    // require('@tailwindcss/forms'), // Example: if you use Tailwind forms plugin
  ],
}
