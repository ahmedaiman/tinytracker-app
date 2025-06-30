<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    icon: {
        type: String,
        required: true
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'accent', 'indigo', 'purple', 'pink', 'cyan', 'teal', 'emerald', 'lime', 'amber', 'orange', 'rose'].includes(value)
    },
    description: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    badge: {
        type: [String, Number],
        default: ''
    },
    badgeColor: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'success', 'warning', 'danger', 'info', 'accent', 'gray'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    fullWidth: {
        type: Boolean,
        default: false
    },
    shadow: {
        type: Boolean,
        default: true
    },
    hoverEffect: {
        type: Boolean,
        default: true
    },
    rounded: {
        type: String,
        default: 'xl',
        validator: (value) => ['none', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', 'full'].includes(value)
    }
});

const emit = defineEmits(['click']);

const colorClasses = computed(() => ({
    // Background colors
    bg: {
        primary: 'bg-primary-50 dark:bg-primary-900/10',
        success: 'bg-success-50 dark:bg-success-900/10',
        warning: 'bg-warning-50 dark:bg-warning-900/10',
        danger: 'bg-danger-50 dark:bg-danger-900/10',
        info: 'bg-info-50 dark:bg-info-900/10',
        accent: 'bg-accent-50 dark:bg-accent-900/10',
        indigo: 'bg-indigo-50 dark:bg-indigo-900/10',
        purple: 'bg-purple-50 dark:bg-purple-900/10',
        pink: 'bg-pink-50 dark:bg-pink-900/10',
        cyan: 'bg-cyan-50 dark:bg-cyan-900/10',
        teal: 'bg-teal-50 dark:bg-teal-900/10',
        emerald: 'bg-emerald-50 dark:bg-emerald-900/10',
        lime: 'bg-lime-50 dark:bg-lime-900/10',
        amber: 'bg-amber-50 dark:bg-amber-900/10',
        orange: 'bg-orange-50 dark:bg-orange-900/10',
        rose: 'bg-rose-50 dark:bg-rose-900/10'
    },
    // Text colors
    text: {
        primary: 'text-primary-700 dark:text-primary-400',
        success: 'text-success-700 dark:text-success-400',
        warning: 'text-warning-700 dark:text-warning-400',
        danger: 'text-danger-700 dark:text-danger-400',
        info: 'text-info-700 dark:text-info-400',
        accent: 'text-accent-700 dark:text-accent-400',
        indigo: 'text-indigo-700 dark:text-indigo-400',
        purple: 'text-purple-700 dark:text-purple-400',
        pink: 'text-pink-700 dark:text-pink-400',
        cyan: 'text-cyan-700 dark:text-cyan-400',
        teal: 'text-teal-700 dark:text-teal-400',
        emerald: 'text-emerald-700 dark:text-emerald-400',
        lime: 'text-lime-700 dark:text-lime-400',
        amber: 'text-amber-700 dark:text-amber-400',
        orange: 'text-orange-700 dark:text-orange-400',
        rose: 'text-rose-700 dark:text-rose-400'
    },
    // Hover background colors
    hoverBg: {
        primary: 'hover:bg-primary-100 dark:hover:bg-primary-900/20',
        success: 'hover:bg-success-100 dark:hover:bg-success-900/20',
        warning: 'hover:bg-warning-100 dark:hover:bg-warning-900/20',
        danger: 'hover:bg-danger-100 dark:hover:bg-danger-900/20',
        info: 'hover:bg-info-100 dark:hover:bg-info-900/20',
        accent: 'hover:bg-accent-100 dark:hover:bg-accent-900/20',
        indigo: 'hover:bg-indigo-100 dark:hover:bg-indigo-900/20',
        purple: 'hover:bg-purple-100 dark:hover:bg-purple-900/20',
        pink: 'hover:bg-pink-100 dark:hover:bg-pink-900/20',
        cyan: 'hover:bg-cyan-100 dark:hover:bg-cyan-900/20',
        teal: 'hover:bg-teal-100 dark:hover:bg-teal-900/20',
        emerald: 'hover:bg-emerald-100 dark:hover:bg-emerald-900/20',
        lime: 'hover:bg-lime-100 dark:hover:bg-lime-900/20',
        amber: 'hover:bg-amber-100 dark:hover:bg-amber-900/20',
        orange: 'hover:bg-orange-100 dark:hover:bg-orange-900/20',
        rose: 'hover:bg-rose-100 dark:hover:bg-rose-900/20'
    },
    // Border colors
    border: {
        primary: 'border-primary-200 dark:border-primary-800/50',
        success: 'border-success-200 dark:border-success-800/50',
        warning: 'border-warning-200 dark:border-warning-800/50',
        danger: 'border-danger-200 dark:border-danger-800/50',
        info: 'border-info-200 dark:border-info-800/50',
        accent: 'border-accent-200 dark:border-accent-800/50',
        indigo: 'border-indigo-200 dark:border-indigo-800/50',
        purple: 'border-purple-200 dark:border-purple-800/50',
        pink: 'border-pink-200 dark:border-pink-800/50',
        cyan: 'border-cyan-200 dark:border-cyan-800/50',
        teal: 'border-teal-200 dark:border-teal-800/50',
        emerald: 'border-emerald-200 dark:border-emerald-800/50',
        lime: 'border-lime-200 dark:border-lime-800/50',
        amber: 'border-amber-200 dark:border-amber-800/50',
        orange: 'border-orange-200 dark:border-orange-800/50',
        rose: 'border-rose-200 dark:border-rose-800/50'
    },
    // Hover border colors
    hoverBorder: {
        primary: 'hover:border-primary-300 dark:hover:border-primary-700/50',
        success: 'hover:border-success-300 dark:hover:border-success-700/50',
        warning: 'hover:border-warning-300 dark:hover:border-warning-700/50',
        danger: 'hover:border-danger-300 dark:hover:border-danger-700/50',
        info: 'hover:border-info-300 dark:hover:border-info-700/50',
        accent: 'hover:border-accent-300 dark:hover:border-accent-700/50',
        indigo: 'hover:border-indigo-300 dark:hover:border-indigo-700/50',
        purple: 'hover:border-purple-300 dark:hover:border-purple-700/50',
        pink: 'hover:border-pink-300 dark:hover:border-pink-700/50',
        cyan: 'hover:border-cyan-300 dark:hover:border-cyan-700/50',
        teal: 'hover:border-teal-300 dark:hover:border-teal-700/50',
        emerald: 'hover:border-emerald-300 dark:hover:border-emerald-700/50',
        lime: 'hover:border-lime-300 dark:hover:border-lime-700/50',
        amber: 'hover:border-amber-300 dark:hover:border-amber-700/50',
        orange: 'hover:border-orange-300 dark:hover:border-orange-700/50',
        rose: 'hover:border-rose-300 dark:hover:border-rose-700/50'
    },
    // Badge background colors
    badgeBg: {
        primary: 'bg-primary-100 dark:bg-primary-900/30',
        success: 'bg-success-100 dark:bg-success-900/30',
        warning: 'bg-warning-100 dark:bg-warning-900/30',
        danger: 'bg-danger-100 dark:bg-danger-900/30',
        info: 'bg-info-100 dark:bg-info-900/30',
        accent: 'bg-accent-100 dark:bg-accent-900/30',
        gray: 'bg-neutral-100 dark:bg-neutral-800/50'
    },
    // Badge text colors
    badgeText: {
        primary: 'text-primary-800 dark:text-primary-200',
        success: 'text-success-800 dark:text-success-200',
        warning: 'text-warning-800 dark:text-warning-200',
        danger: 'text-danger-800 dark:text-danger-200',
        info: 'text-info-800 dark:text-info-200',
        accent: 'text-accent-800 dark:text-accent-200',
        gray: 'text-neutral-700 dark:text-neutral-300'
    }
}));

const sizeClasses = computed(() => ({
    sm: {
        container: 'p-3',
        icon: 'h-4 w-4',
        iconContainer: 'p-1.5',
        title: 'text-xs',
        description: 'text-xs mt-0.5',
        badge: 'text-[0.6rem] px-1.5 py-0.5'
    },
    md: {
        container: 'p-4',
        icon: 'h-5 w-5',
        iconContainer: 'p-2',
        title: 'text-sm',
        description: 'text-xs mt-1',
        badge: 'text-xs px-2 py-0.5'
    },
    lg: {
        container: 'p-5',
        icon: 'h-6 w-6',
        iconContainer: 'p-2.5',
        title: 'text-base',
        description: 'text-sm mt-1',
        badge: 'text-sm px-2.5 py-1'
    }
}));

const roundedClasses = {
    none: 'rounded-none',
    sm: 'rounded-sm',
    md: 'rounded-md',
    lg: 'rounded-lg',
    xl: 'rounded-xl',
    '2xl': 'rounded-2xl',
    '3xl': 'rounded-3xl',
    full: 'rounded-full'
};

const handleClick = (event) => {
    if (!props.disabled && !props.loading) {
        emit('click', event);
    }
};
</script>

<template>
    <button
        type="button"
        class="relative overflow-hidden flex flex-col items-center justify-center text-center transition-all duration-200 focus:outline-none focus:ring-0"
        :class="[
            colorClasses.bg[color],
            colorClasses.border[color],
            props.hoverEffect ? colorClasses.hoverBg[color] : '',
            props.hoverEffect ? colorClasses.hoverBorder[color] : '',
            props.shadow ? 'shadow-sm hover:shadow' : '',
            props.fullWidth ? 'w-full' : '',
            props.disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer',
            roundedClasses[rounded],
            sizeClasses[size].container
        ]"
        :disabled="disabled || loading"
        @click="handleClick"
    >
        <!-- Decorative background element -->
        <div 
            v-if="hoverEffect"
            class="absolute -right-4 -top-4 w-16 h-16 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"
            :class="colorClasses?.bg?.[color] ? colorClasses.bg[color].replace('50', '300').replace('900/10', '700') : ''"
        />
        
        <!-- Icon -->
        <div 
            class="rounded-lg mb-2.5 transition-transform duration-200"
            :class="[
                colorClasses?.bg?.[color] ? colorClasses.bg[color].replace('50', '100').replace('900/10', '900/30') : '',
                colorClasses?.text?.[color] || '',
                sizeClasses?.[size]?.iconContainer || '',
                hoverEffect ? 'group-hover:scale-110' : ''
            ].filter(Boolean)"
        >
            <svg 
                v-if="!loading"
                xmlns="http://www.w3.org/2000/svg" 
                class="transition-transform duration-200" 
                :class="sizeClasses[size].icon" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
                stroke-width="1.5"
            >
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    :d="icon" 
                />
            </svg>
            <svg 
                v-else
                class="animate-spin" 
                :class="[sizeClasses[size].icon, colorClasses.text[color]]" 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24"
            >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        
        <!-- Title -->
        <h3 
            class="font-medium leading-tight" 
            :class="[colorClasses.text[color], sizeClasses[size].title]"
        >
            {{ title }}
        </h3>
        
        <!-- Description -->
        <p 
            v-if="description" 
            class="mt-1 opacity-80" 
            :class="[colorClasses.text[color], sizeClasses[size].description]"
        >
            {{ description }}
        </p>
        
        <!-- Badge -->
        <span 
            v-if="badge" 
            class="absolute top-2 right-2 rounded-full font-medium"
            :class="[
                colorClasses.badgeBg[badgeColor],
                colorClasses.badgeText[badgeColor],
                sizeClasses[size].badge
            ]"
        >
            {{ badge }}
        </span>
    </button>
</template>
