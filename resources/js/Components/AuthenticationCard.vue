<script setup>
import ApplicationMark from '@/Components/ApplicationMark.vue';

defineProps({
    showLogo: {
        type: Boolean,
        default: true,
    },
    maxWidth: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'
        validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'].includes(value),
    },
    padding: {
        type: String,
        default: 'md', // 'none', 'sm', 'md', 'lg', 'xl'
        validator: (value) => ['none', 'sm', 'md', 'lg', 'xl'].includes(value),
    },
});

const maxWidthClasses = {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
    '3xl': 'sm:max-w-3xl',
    '4xl': 'sm:max-w-4xl',
    '5xl': 'sm:max-w-5xl',
    '6xl': 'sm:max-w-6xl',
    '7xl': 'sm:max-w-7xl',
};

const paddingClasses = {
    none: 'p-0',
    sm: 'p-4 sm:p-6',
    md: 'p-6 sm:p-8',
    lg: 'p-8 sm:p-10',
    xl: 'p-10 sm:p-12',
};

const contentPaddingClasses = {
    none: 'p-0',
    sm: 'px-4 py-6 sm:px-6',
    md: 'px-6 py-8 sm:px-8',
    lg: 'px-8 py-10 sm:px-10',
    xl: 'px-10 py-12 sm:px-12',
};
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center bg-neutral-50 dark:bg-neutral-900 transition-colors duration-200">
        <!-- Background pattern -->
        <div class="absolute inset-0 bg-grid-neutral-200/50 dark:bg-grid-neutral-800/30 [mask-image:linear-gradient(0deg,transparent,white)] dark:[mask-image:linear-gradient(0deg,transparent,#1a1a1a)]" aria-hidden="true"></div>
        
        <!-- Logo -->
        <div v-if="showLogo" class="relative mb-8 sm:mb-12">
            <slot name="logo">
                <div class="flex items-center space-x-3 group">
                    <ApplicationMark 
                        size="lg" 
                        color="primary" 
                        :show-background="true"
                        class="transition-all duration-300 group-hover:scale-105 group-hover:rotate-6"
                    />
                    <span class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-800 dark:from-primary-400 dark:to-primary-600 bg-clip-text text-transparent">
                        TinyTrack
                    </span>
                </div>
            </slot>
        </div>

        <!-- Card -->
        <div 
            class="relative w-full bg-white dark:bg-neutral-800 shadow-xl ring-1 ring-neutral-200 dark:ring-neutral-700 sm:rounded-2xl overflow-hidden"
            :class="[maxWidthClasses[maxWidth]]"
        >
            <!-- Decorative elements -->
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-primary-100 dark:bg-primary-900/30 rounded-full opacity-70 mix-blend-multiply filter blur-xl"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-secondary-100 dark:bg-secondary-900/30 rounded-full opacity-70 mix-blend-multiply filter blur-xl"></div>
            <div class="absolute top-1/2 -right-20 w-40 h-40 bg-accent-100 dark:bg-accent-900/20 rounded-full opacity-50 mix-blend-multiply filter blur-xl"></div>
            
            <!-- Card content -->
            <div class="relative">
                <!-- Header -->
                <div 
                    v-if="$slots.header || $slots.title || $slots.description"
                    class="border-b border-neutral-100 dark:border-neutral-700"
                    :class="contentPaddingClasses[padding]"
                >
                    <slot name="header">
                        <div class="text-center space-y-2">
                            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">
                                <slot name="title" />
                            </h2>
                            <p v-if="$slots.description" class="text-sm text-neutral-600 dark:text-neutral-400">
                                <slot name="description" />
                            </p>
                        </div>
                    </slot>
                </div>
                
                <!-- Main content -->
                <div :class="contentPaddingClasses[padding]">
                    <slot />
                </div>
                
                <!-- Footer -->
                <div 
                    v-if="$slots.footer"
                    class="bg-neutral-50 dark:bg-neutral-700/30 border-t border-neutral-100 dark:border-neutral-700"
                    :class="{
                        'p-4': padding === 'none',
                        'p-4 sm:p-6': padding === 'sm',
                        'p-6 sm:p-8': padding === 'md',
                        'p-8 sm:p-10': padding === 'lg',
                        'p-10 sm:p-12': padding === 'xl',
                    }"
                >
                    <slot name="footer" />
                </div>
            </div>
        </div>
        
        <!-- Additional content slot -->
        <div v-if="$slots.after" class="mt-8 text-center">
            <slot name="after" />
        </div>
    </div>
</template>
