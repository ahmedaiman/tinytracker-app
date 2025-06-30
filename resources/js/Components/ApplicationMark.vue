<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value),
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'accent', 'neutral'].includes(value),
    },
    showBackground: {
        type: Boolean,
        default: true,
    },
    backgroundOpacity: {
        type: Number,
        default: 10, // Reduced default opacity for better contrast
        validator: (value) => value >= 0 && value <= 100,
    },
});

const sizeMap = {
    sm: { class: 'w-8 h-8', size: 32 },
    md: { class: 'w-10 h-10', size: 40 },
    lg: { class: 'w-12 h-12', size: 48 },
    xl: { class: 'w-16 h-16', size: 64 },
};

const colorMap = {
    primary: {
        icon: 'text-primary-600 dark:text-primary-400',
        bg: 'bg-primary-100 dark:bg-primary-900/30',
    },
    secondary: {
        icon: 'text-secondary-600 dark:text-secondary-400',
        bg: 'bg-secondary-100 dark:bg-secondary-900/30',
    },
    accent: {
        icon: 'text-accent-600 dark:text-accent-400',
        bg: 'bg-accent-100 dark:bg-accent-900/30',
    },
    neutral: {
        icon: 'text-neutral-600 dark:text-neutral-400',
        bg: 'bg-neutral-100 dark:bg-neutral-800/30',
    },
    // Legacy color support (deprecated)
    // Legacy color support (deprecated)
    lemon_chiffon: {
        icon: 'text-warning-500',
        bg: 'bg-warning-100',
    },
};

const currentSize = computed(() => sizeMap[props.size]);
const currentColor = computed(() => colorMap[props.color]);

const backgroundStyle = computed(() => ({
    backgroundColor: `rgba(255, 255, 255, ${props.backgroundOpacity / 100})`
}));
</script>

<template>
    <div 
        class="inline-flex items-center justify-center rounded-full overflow-hidden transition-all duration-300"
        :class="[
            currentSize.class,
            showBackground ? currentColor.bg : '',
        ]"
        :style="backgroundStyle"
    >
        <svg 
            :class="currentColor.icon"
            :width="currentSize.size"
            :height="currentSize.size"
            viewBox="0 0 24 24" 
            fill="none" 
            xmlns="http://www.w3.org/2000/svg"
        >
            <path 
                d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" 
                stroke="currentColor" 
                stroke-width="1.5" 
                stroke-linecap="round" 
                stroke-linejoin="round"
            />
            <path 
                d="M12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16Z" 
                stroke="currentColor" 
                stroke-width="1.5" 
                stroke-linecap="round" 
                stroke-linejoin="round"
            />
            <path 
                d="M12 8V2" 
                stroke="currentColor" 
                stroke-width="1.5" 
                stroke-linecap="round" 
                stroke-linejoin="round"
            />
            <path 
                d="M12 22V18" 
                stroke="currentColor" 
                stroke-width="1.5" 
                stroke-linecap="round" 
                stroke-linejoin="round"
            />
            <path 
                d="M2 12H8" 
                stroke="currentColor" 
                stroke-width="1.5" 
                stroke-linecap="round" 
                stroke-linejoin="round"
            />
            <path 
                d="M16 12H22" 
                stroke="currentColor" 
                stroke-width="1.5" 
                stroke-linecap="round" 
                stroke-linejoin="round"
            />
        </svg>
    </div>
</template>
