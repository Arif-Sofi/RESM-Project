/** @type {import('tailwindcss').Config} */
export default {
    content: [
      './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
      './storage/framework/views/*.php',
      './resources/views/**/*.blade.php',
      './resources/**/*.js',
      './resources/**/*.vue',
    ],
    theme: {
      extend: {
        fontFamily: {
          sans: ['Figtree', 'Noto Sans JP', 'sans-serif'],
        },
        minHeight: {
          'screen-fit': '100vh',
        },
      },
    },
    plugins: [
      require('@tailwindcss/forms'),
    ],
    darkMode: 'class',
  };
