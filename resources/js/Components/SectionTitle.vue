<template>
    <div :class="wrapperClasses">
        <!-- Accent line -->
        <div 
            v-if="accent && (accentPosition === 'left' || accentPosition === 'bottom')" 
            :class="accentClasses" 
            aria-hidden="true"
        />
        
        <div class="relative">
            <!-- Title -->
            <component 
                :is="variant === 'default' ? 'div' : variant" 
                v-if="hasTitle"
                :class="titleClasses"
            >
                <slot name="title">{{ title }}</slot>
            </component>

            <!-- Description -->
            <p 
                v-if="hasDescription"
                :class="descriptionClasses"
            >
                <slot name="description">{{ description }}</slot>
            </p>


            <!-- Divider -->
            <div 
                v-if="divider" 
                :class="dividerClasses"
            />

            <!-- Aside content (aligned to the right by default) -->
            <div 
                v-if="hasAside" 
                :class="center ? 'mt-4 flex justify-center' : 'mt-2 flex justify-end'"
            >
                <slot name="aside" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, useSlots } from 'vue';

const slots = useSlots();

const props = defineProps({
    /**
     * Title variant
     * @values 'default', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
     */
    variant: {
        type: String,
        default: 'h3',
        validator: (value) => ['default', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(value)
    },
    /**
     * Color variant for the title
     * @values 'default', 'primary', 'secondary', 'accent', 'success', 'danger', 'warning', 'info', 'neutral'
     */
    color: {
        type: String,
        default: 'default',
        validator: (value) => 
            ['default', 'primary', 'secondary', 'accent', 'success', 'danger', 'warning', 'info', 'neutral'].includes(value)
    },
    /**
     * Whether to show a decorative accent line
     */
    accent: {
        type: Boolean,
        default: false
    },
    /**
     * Accent position
     * @values 'left', 'bottom', 'left-border', 'bottom-border'
     */
    accentPosition: {
        type: String,
        default: 'left',
        validator: (value) => ['left', 'bottom', 'left-border', 'bottom-border'].includes(value)
    },
    /**
     * Whether to show a subtle background
     */
    background: {
        type: Boolean,
        default: false
    },
    /**
     * Background opacity (0-100)
     */
    backgroundOpacity: {
        type: [String, Number],
        default: 10,
        validator: (value) => {
            const num = parseInt(value);
            return !isNaN(num) && num >= 0 && num <= 100;
        }
    },
    /**
     * Custom class to apply to the title wrapper
     */
    wrapperClass: {
        type: String,
        default: ''
    },
    /**
     * Custom class to apply to the title
     */
    titleClass: {
        type: String,
        default: ''
    },
    /**
     * Custom class to apply to the description
     */
    descriptionClass: {
        type: String,
        default: ''
    },
    /**
     * Whether to make the title uppercase
     */
    uppercase: {
        type: Boolean,
        default: false
    },
    /**
     * Whether to make the title bold
     */
    bold: {
        type: Boolean,
        default: true
    },
    /**
     * Whether to center the title and description
     */
    center: {
        type: Boolean,
        default: false
    },
    /**
     * Whether to show a divider below the title
     */
    divider: {
        type: Boolean,
        default: false
    },
    /**
     * Title text (alternative to slot)
     */
    title: {
        type: String,
        default: ''
    },
    /**
     * Description text (alternative to slot)
     */
    description: {
        type: String,
        default: ''
    }
});

const colorClasses = {
    default: 'text-neutral-900 dark:text-white',
    primary: 'text-primary-600 dark:text-primary-400',
    secondary: 'text-secondary-600 dark:text-secondary-400',
    accent: 'text-accent-600 dark:text-accent-400',
    success: 'text-success-600 dark:text-success-400',
    danger: 'text-danger-600 dark:text-danger-400',
    warning: 'text-warning-600 dark:text-warning-400',
    info: 'text-info-600 dark:text-info-400',
    neutral: 'text-neutral-600 dark:text-neutral-300'
};

const descriptionColorClasses = {
    default: 'text-neutral-500 dark:text-neutral-400',
    primary: 'text-primary-500 dark:text-primary-300',
    secondary: 'text-secondary-500 dark:text-secondary-300',
    accent: 'text-accent-500 dark:text-accent-300',
    success: 'text-success-500 dark:text-success-300',
    danger: 'text-danger-500 dark:text-danger-300',
    warning: 'text-warning-500 dark:text-warning-300',
    info: 'text-info-500 dark:text-info-300',
    neutral: 'text-neutral-500 dark:text-neutral-400'
};

const bgColorClasses = {
    default: 'bg-neutral-50 dark:bg-neutral-800/50',
    primary: 'bg-primary-50 dark:bg-primary-900/30',
    secondary: 'bg-secondary-50 dark:bg-secondary-900/30',
    accent: 'bg-accent-50 dark:bg-accent-900/30',
    success: 'bg-success-50 dark:bg-success-900/30',
    danger: 'bg-danger-50 dark:bg-danger-900/30',
    warning: 'bg-warning-50 dark:bg-warning-900/30',
    info: 'bg-info-50 dark:bg-info-900/30',
    neutral: 'bg-neutral-100 dark:bg-neutral-800/50'
};

const accentColorClasses = {
    default: 'bg-primary-500 dark:bg-primary-400',
    primary: 'bg-primary-500 dark:bg-primary-400',
    secondary: 'bg-secondary-500 dark:bg-secondary-400',
    accent: 'bg-accent-500 dark:bg-accent-400',
    success: 'bg-success-500 dark:bg-success-400',
    danger: 'bg-danger-500 dark:bg-danger-400',
    warning: 'bg-warning-500 dark:bg-warning-400',
    info: 'bg-info-500 dark:bg-info-400',
    neutral: 'bg-neutral-400 dark:bg-neutral-500'
};

const titleSizeClasses = {
    default: 'text-base',
    h1: 'text-3xl sm:text-4xl font-extrabold tracking-tight',
    h2: 'text-2xl sm:text-3xl font-bold tracking-tight',
    h3: 'text-xl sm:text-2xl font-bold',
    h4: 'text-lg sm:text-xl font-semibold',
    h5: 'text-base sm:text-lg font-semibold',
    h6: 'text-sm sm:text-base font-semibold',
};

const hasTitle = computed(() => slots.title || props.title);
const hasDescription = computed(() => slots.description || props.description);
const hasAside = computed(() => slots.aside);

const wrapperClasses = computed(() => [
    'w-full',
    props.center ? 'text-center' : 'text-left',
    props.wrapperClass,
    props.background ? `p-4 sm:p-6 rounded-lg ${bgColorClasses[props.color] || bgColorClasses.default}` : '',
    {
        'relative': props.accent,
        'pl-6': props.accent && props.accentPosition === 'left',
        'pb-2': props.accent && props.accentPosition === 'bottom',
        'border-l-4': props.accent && props.accentPosition === 'left-border',
        'border-b': props.accent && props.accentPosition === 'bottom-border',
    }
]);

const titleClasses = computed(() => [
    'leading-tight',
    props.uppercase ? 'uppercase tracking-wider' : '',
    props.bold ? 'font-semibold' : 'font-normal',
    colorClasses[props.color] || colorClasses.default,
    titleSizeClasses[props.variant] || titleSizeClasses.default,
    props.titleClass,
    {
        'mb-2': hasDescription.value || props.divider,
        'mb-6': props.variant === 'h1' || props.variant === 'h2',
        'mb-4': props.variant === 'h3' || props.variant === 'h4',
    }
]);

const descriptionClasses = computed(() => [
    'mt-1',
    props.variant === 'h1' || props.variant === 'h2' ? 'text-lg' : 'text-sm',
    descriptionColorClasses[props.color] || descriptionColorClasses.default,
    props.descriptionClass,
    {
        'mb-4': props.divider && (props.variant === 'h1' || props.variant === 'h2'),
        'mb-3': props.divider && (props.variant === 'h3' || props.variant === 'h4'),
        'mb-2': props.divider && (props.variant === 'h5' || props.variant === 'h6' || props.variant === 'default'),
    }
]);

const accentClasses = computed(() => [
    'absolute',
    {
        'left-0 top-0 bottom-0 w-1': props.accentPosition === 'left',
        'left-0 right-0 bottom-0 h-0.5': props.accentPosition === 'bottom',
        'border-l-4': props.accentPosition === 'left-border',
        'border-b': props.accentPosition === 'bottom-border',
    },
    accentColorClasses[props.color] || accentColorClasses.default,
]);

const dividerClasses = computed(() => [
    'h-px w-16 my-4 transition-colors duration-200',
    props.center ? 'mx-auto' : 'mx-0',
    {
        'bg-primary-200 dark:bg-primary-700/50': ['default', 'primary', 'accent'].includes(props.color),
        'bg-secondary-200 dark:bg-secondary-700/50': props.color === 'secondary',
        'bg-success-200 dark:bg-success-700/50': props.color === 'success',
        'bg-danger-200 dark:bg-danger-700/50': props.color === 'danger',
        'bg-warning-200 dark:bg-warning-700/50': props.color === 'warning',
        'bg-info-200 dark:bg-info-700/50': props.color === 'info',
        'bg-neutral-200 dark:bg-neutral-700/50': props.color === 'neutral',
    }
]);
</script>

<style scoped>
/* Smooth transitions for theme changes */
.w-full {
    @apply transition-colors duration-200;
}

/* Accessibility focus styles */
:deep(a:focus-visible) {
    @apply outline-none ring-2 ring-offset-2 ring-primary-500 dark:ring-offset-neutral-900 rounded;
}

/* Print styles */
@media print {
    .w-full {
        break-inside: avoid;
    }
    
    :deep(a) {
        text-decoration: underline;
    }
}
</style>
