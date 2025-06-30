<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    htmlFor: {
        type: String,
        default: '',
    },
    srOnly: {
        type: Boolean,
        default: false,
    },
});

const sizeClasses = computed(() => ({
    sm: 'text-xs',
    md: 'text-sm',
    lg: 'text-base',
}[props.size]));

const marginClasses = computed(() => ({
    sm: 'mb-1',
    md: 'mb-1.5',
    lg: 'mb-2',
}[props.size]));

const labelClasses = computed(() => [
    'block font-medium transition-colors duration-200',
    sizeClasses.value,
    marginClasses.value,
    {
        'text-neutral-700 dark:text-neutral-300': !props.disabled,
        'text-neutral-400 dark:text-neutral-500': props.disabled,
        'sr-only': props.srOnly,
    },
]);
</script>

<template>
    <label 
        :for="htmlFor || undefined"
        :class="labelClasses"
    >
        <span class="flex items-center">
            <template v-if="value || $slots.default">
                <slot>{{ value }}</slot>
                <span 
                    v-if="required" 
                    class="ml-1 text-error-500 dark:text-error-400"
                    aria-hidden="true"
                >
                    *
                </span>
                <span 
                    v-if="required" 
                    class="sr-only"
                >
                    (required)
                </span>
            </template>
            <span v-else>&nbsp;</span>
        </span>
    </label>
</template>

<style scoped>
/* Smooth transitions for better UX */
label {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Focus styles for better accessibility */
:deep(a:focus-visible),
:deep(button:focus-visible) {
    outline: 2px solid var(--color-primary-500);
    outline-offset: 2px;
    border-radius: 0.25rem;
}
</style>
