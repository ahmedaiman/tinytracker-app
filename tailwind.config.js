import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import plugin from 'tailwindcss/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Primary brand colors
                primary: {
                    DEFAULT: '#38c6f6',  // Custom blue
                    '50': '#f0f9ff',
                    '100': '#e0f2fe',
                    '200': '#bae6fd',
                    '300': '#7dd3fc',
                    '400': '#38c6f6',
                    '500': '#0ea5e9',
                    '600': '#0284c7',
                    '700': '#0369a1',
                    '800': '#075985',
                    '900': '#0c4a6e',
                },
                // Secondary brand colors
                secondary: {
                    DEFAULT: '#10B981',  // Emerald-500
                    '50': '#ECFDF5',
                    '100': '#D1FAE5',
                    '200': '#A7F3D0',
                    '300': '#6EE7B7',
                    '400': '#34D399',
                    '500': '#10B981',
                    '600': '#059669',
                    '700': '#047857',
                    '800': '#065F46',
                    '900': '#064E3B',
                },
                // Accent colors
                accent: {
                    DEFAULT: '#F59E0B',  // Amber-500
                    '50': '#FFFBEB',
                    '100': '#FEF3C7',
                    '200': '#FDE68A',
                    '300': '#FCD34D',
                    '400': '#FBBF24',
                    '500': '#F59E0B',
                    '600': '#D97706',
                    '700': '#B45309',
                    '800': '#92400E',
                    '900': '#78350F',
                },
                // Neutral colors
                neutral: {
                    DEFAULT: '#6B7280',  // Gray-500
                    '50': '#F9FAFB',
                    '100': '#F3F4F6',
                    '200': '#E5E7EB',
                    '300': '#D1D5DB',
                    '400': '#9CA3AF',
                    '500': '#6B7280',
                    '600': '#4B5563',
                    '700': '#374151',
                    '800': '#1F2937',
                    '900': '#111827',
                },
                // Success colors
                success: {
                    DEFAULT: '#10B981',
                    '50': '#ECFDF5',
                    '100': '#D1FAE5',
                    '200': '#A7F3D0',
                    '300': '#6EE7B7',
                    '400': '#34D399',
                    '500': '#10B981',
                    '600': '#059669',
                    '700': '#047857',
                    '800': '#065F46',
                    '900': '#064E3B',
                },
                // Warning colors
                warning: {
                    DEFAULT: '#F59E0B',
                    '50': '#FFFBEB',
                    '100': '#FEF3C7',
                    '200': '#FDE68A',
                    '300': '#FCD34D',
                    '400': '#FBBF24',
                    '500': '#F59E0B',
                    '600': '#D97706',
                    '700': '#B45309',
                    '800': '#92400E',
                    '900': '#78350F',
                },
                // Danger/Error colors
                danger: {
                    DEFAULT: '#EF4444',
                    '50': '#FEF2F2',
                    '100': '#FEE2E2',
                    '200': '#FECACA',
                    '300': '#FCA5A5',
                    '400': '#F87171',
                    '500': '#EF4444',
                    '600': '#DC2626',
                    '700': '#B91C1C',
                    '800': '#991B1B',
                    '900': '#7F1D1D',
                },
                // Background colors
                background: {
                    light: '#F9FAFB',
                    DEFAULT: '#FFFFFF',
                    dark: '#1F2937',
                },
                // Text colors
                text: {
                    primary: '#111827',
                    secondary: '#4B5563',
                    tertiary: '#6B7280',
                    inverted: '#FFFFFF',
                },
                // Sidebar specific colors
                sidebar: {
                    DEFAULT: '#1E40AF',
                    hover: '#1E3A8A',
                    active: '#3B82F6',
                    text: '#EFF6FF',
                    icon: '#93C5FD',
                },
            },
        },
    },

    plugins: [
        forms, 
        typography,
        plugin(function({ addUtilities }) {
            addUtilities({
                '.scrollbar-thin': {
                    'scrollbar-width': 'thin',
                    'scrollbar-color': '#9ca3af transparent',
                },
                '.scrollbar-thin::-webkit-scrollbar': {
                    'height': '6px',
                    'width': '6px',
                },
                '.scrollbar-thin::-webkit-scrollbar-track': {
                    'background': 'transparent',
                },
                '.scrollbar-thin::-webkit-scrollbar-thumb': {
                    'background-color': '#9ca3af',
                    'border-radius': '20px',
                },
                // Dark mode styles
                '.dark .scrollbar-thin': {
                    'scrollbar-color': '#4b5563 transparent',
                },
                '.dark .scrollbar-thin::-webkit-scrollbar-thumb': {
                    'background-color': '#4b5563',
                },
            });
        }),
    ],
};
