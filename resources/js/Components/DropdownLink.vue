<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    href: {
        type: String,
        default: '#',
    },
    as: {
        type: String,
        default: 'link', // 'link', 'button', 'a'
        validator: (value) => ['link', 'button', 'a'].includes(value),
    },
    active: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    variant: {
        type: String,
        default: 'default', // 'default', 'primary', 'success', 'danger', 'warning', 'info'
        validator: (value) => ['default', 'primary', 'success', 'danger', 'warning', 'info'].includes(value),
    },
    icon: {
        type: String,
        default: null,
    },
    iconRight: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg'
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
});

const emit = defineEmits(['click']);

const variantClasses = computed(() => {
    const baseClasses = 'flex items-center w-full text-left transition-all duration-200 focus:outline-none';
    const sizeClasses = {
        sm: 'px-3 py-1.5 text-xs',
        md: 'px-4 py-2 text-sm',
        lg: 'px-5 py-2.5 text-base',
    };

    const variants = {
        default: {
            active: 'bg-neutral-100 dark:bg-neutral-700/50 text-neutral-900 dark:text-white',
            inactive: 'text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-700/30',
            icon: 'text-neutral-500 dark:text-neutral-400',
        },
        primary: {
            active: 'bg-primary-50 dark:bg-primary-900/30 text-primary-900 dark:text-primary-100',
            inactive: 'text-neutral-700 dark:text-neutral-200 hover:bg-primary-50/70 dark:hover:bg-primary-900/20',
            icon: 'text-primary-500 dark:text-primary-400',
        },
        secondary: {
            active: 'bg-secondary-50 dark:bg-secondary-900/30 text-secondary-900 dark:text-secondary-100',
            inactive: 'text-neutral-700 dark:text-neutral-200 hover:bg-secondary-50/70 dark:hover:bg-secondary-900/20',
            icon: 'text-secondary-500 dark:text-secondary-400',
        },
        accent: {
            active: 'bg-accent-50 dark:bg-accent-900/30 text-accent-900 dark:text-accent-100',
            inactive: 'text-neutral-700 dark:text-neutral-200 hover:bg-accent-50/70 dark:hover:bg-accent-900/20',
            icon: 'text-accent-500 dark:text-accent-400',
        },
        success: {
            active: 'bg-success-50 dark:bg-success-900/30 text-success-900 dark:text-success-100',
            inactive: 'text-neutral-700 dark:text-neutral-200 hover:bg-success-50/70 dark:hover:bg-success-900/20',
            icon: 'text-success-500 dark:text-success-400',
        },
        warning: {
            active: 'bg-warning-50 dark:bg-warning-900/30 text-warning-900 dark:text-warning-100',
            inactive: 'text-neutral-700 dark:text-neutral-200 hover:bg-warning-50/70 dark:hover:bg-warning-900/20',
            icon: 'text-warning-500 dark:text-warning-400',
        },
        error: {
            active: 'bg-error-50 dark:bg-error-900/30 text-error-900 dark:text-error-100',
            inactive: 'text-error-700 dark:text-error-200 hover:bg-error-50/70 dark:hover:bg-error-900/20',
            icon: 'text-error-500 dark:text-error-400',
        },
        info: {
            active: 'bg-info-50 dark:bg-info-900/30 text-info-900 dark:text-info-100',
            inactive: 'text-neutral-700 dark:text-neutral-200 hover:bg-info-50/70 dark:hover:bg-info-900/20',
            icon: 'text-info-500 dark:text-info-400',
        },
    };

    const variant = variants[props.variant] || variants.default;
    const stateClass = props.active ? variant.active : variant.inactive;
    const disabledClass = props.disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
    const hoverClass = props.disabled ? '' : 'hover:shadow-sm';

    return [
        baseClasses,
        sizeClasses[props.size],
        stateClass,
        disabledClass,
        hoverClass,
        props.icon ? (props.iconRight ? 'justify-between' : 'space-x-2') : '',
    ];
});

const iconSizeClasses = computed(() => ({
    sm: 'h-3.5 w-3.5',
    md: 'h-4 w-4',
    lg: 'h-5 w-5',
}));

const handleClick = (event) => {
    if (!props.disabled) {
        emit('click', event);
    }
};
</script>

<template>
    <component
        :is="as === 'link' ? Link : as"
        :href="as !== 'button' ? href : undefined"
        :type="as === 'button' ? 'button' : undefined"
        :disabled="disabled"
        :class="[
            'block w-full px-4 py-2 text-left text-sm',
            'transition-all duration-150 ease-in-out',
            'focus:outline-none focus:ring-1 focus:ring-inset',
            'rounded-md',
            variantClasses,
            {
                'focus:ring-primary-500 dark:focus:ring-primary-400': variant === 'default',
                'focus:ring-primary-500 dark:focus:ring-primary-400': variant === 'primary',
                'focus:ring-secondary-500 dark:focus:ring-secondary-400': variant === 'secondary',
                'focus:ring-accent-500 dark:focus:ring-accent-400': variant === 'accent',
                'focus:ring-success-500 dark:focus:ring-success-400': variant === 'success',
                'focus:ring-warning-500 dark:focus:ring-warning-400': variant === 'warning',
                'focus:ring-error-500 dark:focus:ring-error-400': variant === 'error',
                'focus:ring-info-500 dark:focus:ring-info-400': variant === 'info',
            }
        ]"
        @click="handleClick"
    >
        <div class="flex items-center">
            <svg
                v-if="icon && !iconRight"
                :class="[
                    'flex-shrink-0 mr-2',
                    iconSizeClasses[size],
                    variantClasses.includes('opacity-50') ? 'opacity-50' : 'opacity-80',
                    variants[variant]?.icon || 'text-neutral-500 dark:text-neutral-400',
                ]"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
            >
                <path :d="icon" />
            </svg>
            <span class="flex-1">
                <slot />
            </span>
            <svg
                v-if="icon && iconRight"
                :class="[
                    'flex-shrink-0 ml-2',
                    iconSizeClasses[size],
                    variantClasses.includes('opacity-50') ? 'opacity-50' : 'opacity-80',
                    variants[variant]?.icon || 'text-neutral-500 dark:text-neutral-400',
                ]"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
            >
                <path :d="icon" />
            </svg>
        </div>
    </component>
</template>
