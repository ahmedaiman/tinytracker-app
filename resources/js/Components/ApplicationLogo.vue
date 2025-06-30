<script setup>
defineProps({
    size: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg'
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    showText: {
        type: Boolean,
        default: true,
    },
    color: {
        type: String,
        default: 'primary', // 'primary', 'secondary', 'accent', 'neutral'
        validator: (value) => ['primary', 'secondary', 'accent', 'neutral'].includes(value),
    },
});

const sizeClasses = {
    sm: {
        icon: 'w-8 h-8',
        text: 'text-xl',
    },
    md: {
        icon: 'w-10 h-10',
        text: 'text-2xl',
    },
    lg: {
        icon: 'w-12 h-12',
        text: 'text-3xl',
    },
};

const colorClasses = {
    primary: {
        icon: 'text-primary-600 dark:text-primary-400',
        text: 'text-primary-900 dark:text-primary-100',
    },
    secondary: {
        icon: 'text-secondary-600 dark:text-secondary-400',
        text: 'text-secondary-900 dark:text-secondary-100',
    },
    accent: {
        icon: 'text-accent-600 dark:text-accent-400',
        text: 'text-accent-900 dark:text-accent-100',
    },
    neutral: {
        icon: 'text-neutral-600 dark:text-neutral-400',
        text: 'text-neutral-900 dark:text-neutral-100',
    },
    // Legacy color support (deprecated)
    lemon_chiffon: {
        icon: 'text-warning-100',
        text: 'text-warning-800',
    },
};
</script>

<template>
    <div class="flex items-center space-x-3 transition-all duration-200">
        <!-- Icon -->
        <div class="relative">
            <div 
                class="absolute inset-0 rounded-full opacity-20" 
                :class="[
                    color === 'peach' ? 'bg-peach-200' : '',
                    color === 'light_blue' ? 'bg-light_blue-200' : '',
                    color === 'celadon' ? 'bg-celadon-200' : '',
                    color === 'flax' ? 'bg-flax-200' : '',
                    color === 'lemon_chiffon' ? 'bg-lemon_chiffon-200' : '',
                ]"
                :style="{
                    transform: 'scale(1.2)',
                }"
            ></div>
            <svg 
                viewBox="0 0 24 24" 
                :class="[sizeClasses[size].icon, colorClasses[color].icon]"
                fill="none" 
                xmlns="http://www.w3.org/2000/svg"
            >
                <path 
                    d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round"
                />
                <path 
                    d="M12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16Z" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round"
                />
                <path 
                    d="M12 8V2" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round"
                />
                <path 
                    d="M12 22V18" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round"
                />
                <path 
                    d="M2 12H8" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round"
                />
                <path 
                    d="M16 12H22" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round"
                />
            </svg>
        </div>
        
        <!-- Text -->
        <span 
            v-if="showText" 
            class="font-bold tracking-tight"
            :class="[sizeClasses[size].text, colorClasses[color].text]"
        >
            TinyTrack
        </span>
    </div>
</template>
