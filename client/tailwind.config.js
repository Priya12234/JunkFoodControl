/** @type {import('tailwindcss').Config} */
export default {
  content: ["./src/**/*.{js,jsx,ts,tsx}"],
  theme: {
    extend: {
      fontFamily: {
        jaro: ["Jaro", "sans-serif"], // 👈 custom font
      },
    },
  },
  plugins: [],
};
