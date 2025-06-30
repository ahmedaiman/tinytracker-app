<script setup>
import { computed } from 'vue';

const props = defineProps({
    /**
     * The variant of the badge
     * @values 'default', 'primary', 'secondary', 'accent', 'success', 'danger', 'warning', 'info', 'neutral'
     */
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'primary', 'secondary', 'accent', 'success', 'danger', 'warning', 'info', 'neutral'].includes(value)
    },
    /**
     * The size of the badge
     * @values 'xs', 'sm', 'md', 'lg'
     */
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
    },
    /**
     * Whether the badge should be rounded (pill) or square
     */
    rounded: {
        type: Boolean,
        default: true
    },
    /**
     * Whether the badge should have a solid or subtle appearance
     */
    solid: {
        type: Boolean,
        default: false
    },
    /**
     * Whether the badge should be outlined
     */
    outlined: {
        type: Boolean,
        default: false
    },
    /**
     * Custom class to apply to the badge
     */
    class: {
        type: String,
        default: ''
    },
    /**
     * Whether to show a dot before the text
     */
    withDot: {
        type: Boolean,
        default: false
    },
    /**
     * Whether the badge is interactive (hover/active states)
     */
    interactive: {
        type: Boolean,
        default: false
    }
});

const variantClasses = {
    default: {
        solid: 'bg-neutral-100 text-neutral-800 dark:bg-neutral-700/50 dark:text-neutral-300',
        subtle: 'bg-neutral-50 text-neutral-600 dark:bg-neutral-800/50 dark:text-neutral-300',
        outlined: 'bg-transparent text-neutral-800 dark:text-neutral-300 border border-neutral-200 dark:border-neutral-700'
    },
    primary: {
        solid: 'bg-primary-600 text-white dark:bg-primary-700',
        subtle: 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400',
        outlined: 'bg-transparent text-primary-700 dark:text-primary-400 border border-primary-200 dark:border-primary-800/50'
    },
    secondary: {
        solid: 'bg-secondary-600 text-white dark:bg-secondary-700',
        subtle: 'bg-secondary-50 text-secondary-700 dark:bg-secondary-900/30 dark:text-secondary-400',
        outlined: 'bg-transparent text-secondary-700 dark:text-secondary-400 border border-secondary-200 dark:border-secondary-800/50'
    },
    accent: {
        solid: 'bg-accent-600 text-white dark:bg-accent-700',
        subtle: 'bg-accent-50 text-accent-700 dark:bg-accent-900/30 dark:text-accent-400',
        outlined: 'bg-transparent text-accent-700 dark:text-accent-400 border border-accent-200 dark:border-accent-800/50'
    },
    success: {
        solid: 'bg-success-600 text-white dark:bg-success-700',
        subtle: 'bg-success-50 text-success-700 dark:bg-success-900/30 dark:text-success-400',
        outlined: 'bg-transparent text-success-700 dark:text-success-400 border border-success-200 dark:border-success-800/50'
    },
    danger: {
        solid: 'bg-danger-600 text-white dark:bg-danger-700',
        subtle: 'bg-danger-50 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400',
        outlined: 'bg-transparent text-danger-700 dark:text-danger-400 border border-danger-200 dark:border-danger-800/50'
    },
    warning: {
        solid: 'bg-warning-600 text-white dark:bg-warning-700',
        subtle: 'bg-warning-50 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400',
        outlined: 'bg-transparent text-warning-700 dark:text-warning-400 border border-warning-200 dark:border-warning-800/50'
    },
    info: {
        solid: 'bg-info-600 text-white dark:bg-info-700',
        subtle: 'bg-info-50 text-info-700 dark:bg-info-900/30 dark:text-info-400',
        outlined: 'bg-transparent text-info-700 dark:text-info-400 border border-info-200 dark:border-info-800/50'
    },
    neutral: {
        solid: 'bg-neutral-600 text-white dark:bg-neutral-700',
        subtle: 'bg-neutral-50 text-neutral-700 dark:bg-neutral-800/50 dark:text-neutral-300',
        outlined: 'bg-transparent text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-neutral-700'
    }
};

const sizeClasses = {
    xs: 'text-xs px-1.5 py-0.5',
    sm: 'text-xs px-2 py-0.5',
    md: 'text-xs px-2.5 py-1',
    lg: 'text-sm px-3 py-1.5'
};

const dotSizeClasses = {
    xs: 'h-1.5 w-1.5',
    sm: 'h-1.5 w-1.5',
    md: 'h-2 w-2',
    lg: 'h-2.5 w-2.5'
};

const dotMarginClasses = {
    xs: 'mr-1',
    sm: 'mr-1.5',
    md: 'mr-1.5',
    lg: 'mr-2'
};

const badgeClasses = computed(() => [
    'inline-flex items-center font-medium leading-none whitespace-nowrap',
    {
        'rounded-full': props.rounded,
        'rounded': !props.rounded,
        'transition-colors': props.interactive,
        'hover:opacity-90': props.interactive,
        'active:opacity-75': props.interactive,
        'cursor-pointer': props.interactive
    },
    sizeClasses[props.size],
    props.solid 
        ? variantClasses[props.variant].solid 
        : props.outlined 
            ? variantClasses[props.variant].outlined 
            : variantClasses[props.variant].subtle,
    props.class
]);

const dotClasses = computed(() => [
    'inline-block rounded-full flex-shrink-0',
    dotSizeClasses[props.size],
    dotMarginClasses[props.size],
    props.solid 
        ? 'bg-current opacity-90' 
        : props.outlined 
            ? 'border border-current' 
            : 'bg-current opacity-70'
]);
</script>

<template>
    <span :class="badgeClasses">
        <span 
            v-if="withDot" 
            :class="dotClasses" 
            aria-hidden="true"
        />
        <slot />
    </span>
</template>
