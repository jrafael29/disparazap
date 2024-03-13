import forms from "@tailwindcss/forms";
import typo from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./vendor/robsontenorio/mary/src/View/Components/**/*.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [forms, typo, require("daisyui")],
};
