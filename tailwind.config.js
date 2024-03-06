import forms from "@tailwindcss/forms";
import typo from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: ["./resources/**/*.blade.php", "./resources/**/*.js"],
    theme: {
        extend: {},
    },
    plugins: [forms, typo],
};
