<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    exact: {
        type: Boolean,
        default: false,
    },
    icon: {
        type: String,
        default: null,
    },
    iconRight: {
        type: Boolean,
        default: false,
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => [
            'default', 'primary', 'secondary', 'accent',
            'success', 'danger', 'warning', 'info', 'light', 'dark'
        ].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    block: {
        type: Boolean,
        default: false,
    },
    noStyle: {
        type: Boolean,
        default: false,
    },
    rounded: {
        type: [Boolean, String],
        default: 'lg',
        validator: (value) => [true, false, 'none', 'sm', 'md', 'lg', 'full'].includes(value),
    },
});

const isActive = computed(() => {
    if (props.active) return true;
    if (typeof window === 'undefined') return false;
    
    const currentPath = window.location.pathname;
    if (props.exact) {
        return currentPath === props.href;
    }
    return currentPath.startsWith(props.href);
});

const variantClasses = computed(() => {
    if (props.noStyle) return '';
    
    const base = [
        'inline-flex items-center transition-all duration-200 focus:outline-none',
        'font-medium whitespace-nowrap',
        {
            'w-full justify-center': props.block,
            'rounded-none': props.rounded === 'none',
            'rounded-sm': props.rounded === 'sm',
            'rounded-md': props.rounded === 'md',
            'rounded-lg': props.rounded === 'lg' || props.rounded === true,
            'rounded-full': props.rounded === 'full',
        },
    ];
    
    const sizeClasses = {
        xs: ['text-xs px-2.5 py-1.5', 'space-x-1.5'],
        sm: ['text-sm px-3 py-2', 'space-x-2'],
        md: ['text-sm px-4 py-2.5', 'space-x-2.5'],
        lg: ['text-base px-5 py-3', 'space-x-3'],
        xl: ['text-lg px-6 py-3.5', 'space-x-3.5'],
    }[props.size] || sizeClasses.md;
    
    const variants = {
        default: {
            active: [
                'bg-neutral-100 dark:bg-neutral-700/50',
                'text-neutral-900 dark:text-white',
                'border border-transparent',
            ],
            inactive: [
                'text-neutral-700 dark:text-neutral-300',
                'hover:bg-neutral-50/70 dark:hover:bg-neutral-800/50',
                'border border-transparent',
            ],
            focus: 'focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-neutral-900',
        },
        primary: {
            active: [
                'bg-primary-600 dark:bg-primary-600/90',
                'text-white',
                'hover:bg-primary-700 dark:hover:bg-primary-700/90',
                'border border-primary-600 dark:border-primary-600/90',
            ],
            inactive: [
                'text-primary-700 dark:text-primary-400',
                'hover:bg-primary-50/70 dark:hover:bg-primary-900/30',
                'border border-primary-200 dark:border-primary-800',
            ],
            focus: 'focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-neutral-900',
        },
        secondary: {
            active: [
                'bg-secondary-600 dark:bg-secondary-600/90',
                'text-white',
                'hover:bg-secondary-700 dark:hover:bg-secondary-700/90',
                'border border-secondary-600 dark:border-secondary-600/90',
            ],
            inactive: [
                'text-secondary-700 dark:text-secondary-400',
                'hover:bg-secondary-50/70 dark:hover:bg-secondary-900/20',
                'border border-secondary-200 dark:border-secondary-800',
            ],
            focus: 'focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 dark:focus:ring-offset-neutral-900',
        },
        accent: {
            active: [
                'bg-accent-500 dark:bg-accent-500/90',
                'text-white',
                'hover:bg-accent-600 dark:hover:bg-accent-600/90',
                'border border-accent-500 dark:border-accent-500/90',
            ],
            inactive: [
                'text-accent-700 dark:text-accent-400',
                'hover:bg-accent-50/70 dark:hover:bg-accent-900/20',
                'border border-accent-200 dark:border-accent-800',
            ],
            focus: 'focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 dark:focus:ring-offset-neutral-900',
        },
        success: {
            active: [
                'bg-success-600 dark:bg-success-600/90',
                'text-white',
                'hover:bg-success-700 dark:hover:bg-success-700/90',
                'border border-success-600 dark:border-success-600/90',
            ],
            inactive: [
                'text-success-700 dark:text-success-400',
                'hover:bg-success-50/70 dark:hover:bg-success-900/20',
                'border border-success-200 dark:border-success-800',
            ],
            focus: 'focus:ring-2 focus:ring-offset-2 focus:ring-success-500 dark:focus:ring-offset-neutral-900',
        },
        danger: {
            active: [
                'bg-danger-600 dark:bg-danger-600/90',
                'text-white',
                'hover:bg-danger-700 dark:hover:bg-danger-700/90',
                'border border-danger-600 dark:border-danger-600/90',
            ],
            inactive: [
                'text-danger-700 dark:text-danger-400',
                'hover:bg-danger-50/70 dark:hover:bg-danger-900/20',
                'border border-danger-200 dark:border-danger-800',
            ],
            focus: 'focus:ring-2 focus:ring-offset-2 focus:ring-danger-500 dark:focus:ring-offset-neutral-900',
        },
        warning: {
            active: [
                'bg-warning-600 dark:bg-warning-600/90',
                'text-white',
                'hover:bg-warning-700 dark:hover:bg-warning-700/90',
                'border border-warning-600 dark:border-warning-600/90',
            ],
            inactive: [
                'text-warning-700 dark:text-warning-500',
                'hover:bg-warning-50/70 dark:hover:bg-warning-900/20',
                'border border-warning-200 dark:border-warning-800',
            ],
            focus: 'focus:ring-2 focus:ring-offset-2 focus:ring-warning-500 dark:focus:ring-offset-neutral-900',
        },
        info: {
            active: [
                'bg-info-600 dark:bg-info-600/90',
                'text-white',
                'hover:bg-info-700 dark:hover:bg-info-700/90',
                'border border-info-600 dark:border-info-600/90',
            ],
            inactive: [
                'text-info-700 dark:text-info-400',
                'hover:bg-info-50/70 dark:hover:bg-info-900/20',
                'border border-info-200 dark:border-info-800',
            ],
            focus: 'focus:ring-2 focus:ring-offset-2 focus:ring-info-500 dark:focus:ring-offset-neutral-900',
        },
        light: {
            active: [
                'bg-neutral-50 dark:bg-neutral-800/80',
                'text-neutral-900 dark:text-white',
                'border border-neutral-200 dark:border-neutral-700',
            ],
            inactive: [
                'text-neutral-700 dark:text-neutral-300',
                'hover:bg-neutral-100/70 dark:hover:bg-neutral-800/50',
                'border border-transparent hover:border-neutral-200 dark:border-neutral-800 dark:hover:border-neutral-700',
            ],
            focus: 'focus:ring-2 focus:ring-offset-2 focus:ring-neutral-400 dark:focus:ring-offset-neutral-900',
        },
        dark: {
            active: [
                'bg-neutral-900 dark:bg-neutral-800',
                'text-white',
                'border border-neutral-900 dark:border-neutral-700',
            ],
            inactive: [
                'text-neutral-900 dark:text-white',
                'hover:bg-neutral-800/90 dark:hover:bg-neutral-700/90',
                'border border-neutral-300 dark:border-neutral-700',
            ],
            focus: 'focus:ring-2 focus:ring-offset-2 focus:ring-neutral-800 dark:focus:ring-offset-neutral-900',
        },
    };
    
    const variant = variants[props.variant] || variants.default;
    const stateClasses = isActive.value ? variant.active : variant.inactive;
    const disabledClass = props.disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer';
    
    return [
        ...base,
        ...sizeClasses,
        ...(Array.isArray(stateClasses) ? stateClasses : [stateClasses]),
        variant.focus,
        disabledClass,
        props.icon ? (props.iconRight ? 'justify-between' : 'space-x-2') : '',
    ];
});

const iconSizeClasses = computed(() => ({
    xs: 'h-3.5 w-3.5',
    sm: 'h-4 w-4',
    md: 'h-4 w-4',
    lg: 'h-5 w-5',
    xl: 'h-6 w-6',
}));

const iconColorClasses = computed(() => {
    if (props.noStyle) return '';
    
    const variant = props.variant || 'default';
    const isActiveState = isActive.value;
    
    if (isActiveState) {
        return {
            default: 'text-neutral-700 dark:text-neutral-300',
            primary: 'text-white dark:text-white/90',
            secondary: 'text-white dark:text-white/90',
            accent: 'text-white dark:text-white/90',
            success: 'text-white dark:text-white/90',
            danger: 'text-white dark:text-white/90',
            warning: 'text-white dark:text-white/90',
            info: 'text-white dark:text-white/90',
            light: 'text-neutral-700 dark:text-neutral-300',
            dark: 'text-white dark:text-white/90',
        }[variant];
    }
    
    return {
        default: 'text-neutral-500 dark:text-neutral-400',
        primary: 'text-primary-600 dark:text-primary-400',
        secondary: 'text-secondary-600 dark:text-secondary-400',
        accent: 'text-accent-600 dark:text-accent-400',
        success: 'text-success-600 dark:text-success-400',
        danger: 'text-danger-600 dark:text-danger-400',
        warning: 'text-warning-600 dark:text-warning-500',
        info: 'text-info-600 dark:text-info-400',
        light: 'text-neutral-500 dark:text-neutral-400',
        dark: 'text-neutral-900 dark:text-white',
    }[variant];
});

const handleClick = (event) => {
    if (props.disabled) {
        event.preventDefault();
        event.stopPropagation();
        return false;
    }
    return true;
};
</script>

<template>
    <Link
        :href="!disabled ? href : '#'"
        :class="[
            'transition-colors duration-200',
            variantClasses,
            { 'opacity-70': disabled },
        ]"
        :aria-current="isActive ? 'page' : undefined"
        :aria-disabled="disabled ? 'true' : undefined"
        :tabindex="disabled ? -1 : 0"
        @click="handleClick"
    >
        <span 
            class="inline-flex items-center w-full"
            :class="{
                'flex-row-reverse': iconRight,
                'justify-center': block,
            }"
        >
            <template v-if="icon">
                <svg
                    v-if="typeof icon === 'string'"
                    :class="[
                        'flex-shrink-0',
                        iconSizeClasses[size],
                        iconColorClasses,
                        {
                            'order-2': iconRight,
                            'mr-2': !iconRight,
                            'ml-2': iconRight,
                        },
                    ]"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path :d="icon" />
                </svg>
                <component
                    v-else
                    :is="icon"
                    :class="[
                        'flex-shrink-0',
                        iconSizeClasses[size],
                        iconColorClasses,
                        {
                            'order-2': iconRight,
                            'mr-2': !iconRight,
                            'ml-2': iconRight,
                        },
                    ]"
                    aria-hidden="true"
                />
            </template>
            
            <span class="truncate">
                <slot />
            </span>
            
            <!-- Loading indicator slot -->
            <slot name="loading">
                <svg
                    v-if="$slots.loading"
                    class="animate-spin ml-2 h-4 w-4 text-current"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </slot>
        </span>
    </Link>
</template>

<style scoped>
/* Smooth transitions for interactive states */
a {
    transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

/* Focus styles */
a:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

/* Disabled state */
a[aria-disabled="true"] {
    pointer-events: none;
}

/* Dark mode transitions */
@media (prefers-color-scheme: dark) {
    a {
        transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }
}
</style>
