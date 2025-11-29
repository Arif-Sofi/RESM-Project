import forms from '@tailwindcss/forms';

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
        colors: {
          'primary': '#727D73',
          'secondary': '#AAB99A',
          'accent': '#D0DDD0',
          'base': '#F0F0D7',
          'lightbase': '#FAF9EE',
        },
        fontFamily: {
          sans: ['Figtree', 'Noto Sans JP', 'sans-serif'],
        },
        minHeight: {
          'screen-fit': '100vh',
        },
      },
    },
    plugins: [forms],
    darkMode: 'class',
  };
