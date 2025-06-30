<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';

defineProps({
    size: {
        type: String,
        default: 'lg', // 'sm', 'md', 'lg', 'xl'
        validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value),
    },
    color: {
        type: String,
        default: 'primary', // 'primary', 'secondary', 'accent', 'neutral'
        validator: (value) => ['primary', 'secondary', 'accent', 'neutral'].includes(value),
    },
    showText: {
        type: Boolean,
        default: true,
    },
    href: {
        type: String,
        default: '/',
    },
});

const sizeClasses = {
    sm: {
        container: 'space-x-2',
        text: 'text-xl',
    },
    md: {
        container: 'space-x-3',
        text: 'text-2xl',
    },
    lg: {
        container: 'space-x-4',
        text: 'text-3xl',
    },
    xl: {
        container: 'space-x-5',
        text: 'text-4xl',
    },
};

const markSizes = {
    sm: 'md',
    md: 'lg',
    lg: 'xl',
    xl: '2xl',
};
</script>

<template>
    <Link 
        :href="href" 
        class="group inline-flex items-center transition-all duration-300 hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary-500 rounded-lg"
        :class="sizeClasses[size].container"
    >
        <div class="transition-all duration-300 group-hover:scale-105 group-hover:rotate-3 group-active:scale-95">
            <ApplicationMark 
                :size="markSizes[size]" 
                :color="color"
                :show-background="true"
                class="transition-transform duration-300 group-hover:rotate-6"
            />
        </div>
        
        <span 
            v-if="showText"
            class="font-bold tracking-tight bg-gradient-to-r from-primary-600 via-secondary-600 to-accent-600 dark:from-primary-400 dark:via-secondary-400 dark:to-accent-400 bg-clip-text text-transparent"
            :class="[sizeClasses[size].text, 'bg-300% animate-gradient-slow']"
        >
            TinyTrack
        </span>
    </Link>
</template>

<style scoped>
@keyframes gradientFlow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.animate-gradient-slow {
    animation: gradientFlow 8s ease infinite;
    background-size: 200% 200%;
}
</style>
