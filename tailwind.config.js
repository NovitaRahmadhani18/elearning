import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Poppins", "Figtree", ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                "hero-pattern": "url('/resources/images/pattern.png')",
                school: "url('/resources/images/Gambar SDB.png')",
            },
            colors: {
                primary: {
                    DEFAULT: "#bbe9fe", // Blue from logo
                    light: "#E2F5FF",
                    dark: "#19BCB2",
                },
                secondary: {
                    DEFAULT: "#FFC361", // Yellow from logo
                    light: "#FFE6B9",
                    dark: "#CC9235",
                },
                neutral: {
                    50: "#f9fafb",
                    100: "#f3f4f6",
                    200: "#e5e7eb",
                    300: "#d1d5db",
                    400: "#9ca3af",
                    500: "#6b7280",
                    600: "#4b5563",
                    700: "#374151",
                    800: "#1f2937",
                    900: "#111827",
                },
            },
        },
    },

    plugins: [forms, typography],
};
