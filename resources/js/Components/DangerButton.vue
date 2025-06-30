<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value),
    },
});

const sizeClasses = {
    xs: 'px-3 py-1.5 text-xs',
    sm: 'px-4 py-2 text-sm',
    md: 'px-5 py-2.5 text-sm',
    lg: 'px-6 py-3 text-base',
};

const baseClasses = [
    'inline-flex items-center justify-center rounded-lg font-medium transition-all duration-200',
    'focus:outline-none',
    'disabled:opacity-60 disabled:cursor-not-allowed',
];

const variantClasses = [
    'bg-transparent text-danger-600 border border-danger-600 hover:bg-danger-600 hover:text-white',
    'dark:text-danger-500 dark:border-danger-500 dark:hover:bg-danger-600 dark:hover:text-white',
    'active:bg-danger-700 dark:active:bg-danger-700',
    'hover:shadow-md active:shadow-inner',
    'disabled:bg-transparent disabled:text-danger-400 disabled:border-danger-400 dark:disabled:text-danger-700 dark:disabled:border-danger-700',
];
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
            ...baseClasses,
            ...variantClasses,
            sizeClasses[size],
            $attrs.class,
        ]"
    >
        <slot />
    </button>
</template>
