import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default { content: ['./index.html', './src/**/*.{js,jsx}'], theme: { extend: { colors: { ink: '#102a43', navy: '#1e40af', sky: '#2563eb', mist: '#f4f8ff' }, fontFamily: { display: ['Outfit', 'sans-serif'], sans: ['Inter', 'sans-serif'] } } }, plugins: [forms] };