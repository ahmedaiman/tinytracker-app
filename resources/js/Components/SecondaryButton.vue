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
    fullWidth: {
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
        :disabled="disabled"
        :class="[
            'inline-flex items-center justify-center rounded-lg font-medium transition-all duration-200',
            'focus:outline-none',
            'bg-transparent text-secondary-600 border border-secondary-600 hover:bg-secondary-600 hover:text-white',
            'active:bg-secondary-700',
            'disabled:opacity-50 disabled:cursor-not-allowed',
            'shadow-sm hover:shadow',
            iconOnly ? iconSizeClasses[size] : sizeClasses[size],
            { 'w-full': fullWidth && !iconOnly },
            { 'px-4': icon && !iconOnly },
        ]"
    >
        <slot v-if="!iconOnly" />
        <slot v-else name="icon" />
    </button>
</template>
