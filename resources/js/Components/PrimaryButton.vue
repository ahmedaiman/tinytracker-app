<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
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
    fullWidth: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    icon: {
        type: Boolean,
        default: false,
    },
    iconOnly: {
        type: Boolean,
        default: false,
    },
});

const sizeClasses = {
    xs: 'px-3 py-1.5 text-xs',
    sm: 'px-4 py-2 text-sm',
    md: 'px-5 py-2.5 text-sm',
    lg: 'px-6 py-3 text-base',
};

const iconSizeClasses = {
    xs: 'p-1.5',
    sm: 'p-2',
    md: 'p-2.5',
    lg: 'p-3',
};
</script>

<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        :class="[
            'inline-flex items-center justify-center rounded-lg font-semibold uppercase tracking-wider transition-all duration-200 focus:outline-none',
            'bg-transparent hover:bg-primary-600 text-primary-600 hover:text-white border border-primary-600 active:bg-primary-700',
            'disabled:opacity-50 disabled:cursor-not-allowed',
            'shadow-sm hover:shadow-md',
            iconOnly ? iconSizeClasses[size] : sizeClasses[size],
            { 'w-full': fullWidth && !iconOnly },
            { 'opacity-70 cursor-wait': loading },
            { 'px-4': icon && !iconOnly }, // Add extra padding when there's an icon
        ]"
    >
        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <slot v-if="!iconOnly" />
        <slot v-else name="icon" />
    </button>
</template>
