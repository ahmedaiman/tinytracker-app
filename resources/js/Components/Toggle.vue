<script setup>
import { computed, onMounted, ref, toRefs } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    id: {
        type: String,
        default: '',
    },
    name: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'accent', 'success', 'danger', 'warning', 'info'].includes(value),
    },
    labelPosition: {
        type: String,
        default: 'right',
        validator: (value) => ['left', 'right', 'top', 'bottom'].includes(value),
    },
    showIcons: {
        type: Boolean,
        default: true,
    },
    onLabel: {
        type: String,
        default: 'On',
    },
    offLabel: {
        type: String,
        default: 'Off',
    },
    showLabels: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['update:modelValue', 'change', 'toggle']);

const { 
    modelValue, 
    disabled, 
    size, 
    color, 
    labelPosition, 
    showIcons, 
    onLabel, 
    offLabel, 
    showLabels 
} = toRefs(props);

const toggleId = ref(props.id || `toggle-${Math.random().toString(36).substr(2, 9)}`);

// Size classes
const sizeClasses = computed(() => ({
    sm: {
        container: 'h-5 w-9',
        dot: 'h-4 w-4',
        dotTranslate: 'translate-x-4',
        text: 'text-xs',
    },
    md: {
        container: 'h-6 w-11',
        dot: 'h-5 w-5',
        dotTranslate: 'translate-x-5',
        text: 'text-sm',
    },
    lg: {
        container: 'h-7 w-14',
        dot: 'h-6 w-6',
        dotTranslate: 'translate-x-7',
        text: 'text-base',
    },
}));

// Color classes with dark mode support
const colorClasses = computed(() => ({
    primary: {
        container: modelValue.value 
            ? 'bg-primary-500 dark:bg-primary-600' 
            : 'bg-neutral-200 dark:bg-neutral-700',
        dot: 'bg-white dark:bg-neutral-100',
        text: 'text-neutral-700 dark:text-neutral-300',
        ring: 'focus:ring-primary-200 dark:focus:ring-primary-900/50',
    },
    secondary: {
        container: modelValue.value 
            ? 'bg-secondary-500 dark:bg-secondary-600' 
            : 'bg-neutral-200 dark:bg-neutral-700',
        dot: 'bg-white dark:bg-neutral-100',
        text: 'text-neutral-700 dark:text-neutral-300',
        ring: 'focus:ring-secondary-200 dark:focus:ring-secondary-900/50',
    },
    accent: {
        container: modelValue.value 
            ? 'bg-accent-500 dark:bg-accent-600' 
            : 'bg-neutral-200 dark:bg-neutral-700',
        dot: 'bg-white dark:bg-neutral-100',
        text: 'text-neutral-700 dark:text-neutral-300',
        ring: 'focus:ring-accent-200 dark:focus:ring-accent-900/50',
    },
    success: {
        container: modelValue.value 
            ? 'bg-success-500 dark:bg-success-600' 
            : 'bg-success-100 dark:bg-success-900/30',
        dot: 'bg-white',
        text: 'text-success-700 dark:text-success-200',
        ring: 'focus:ring-success-200 dark:focus:ring-success-900/50',
    },
    danger: {
        container: modelValue.value 
            ? 'bg-danger-500 dark:bg-danger-600' 
            : 'bg-danger-100 dark:bg-danger-900/30',
        dot: 'bg-white',
        text: 'text-danger-700 dark:text-danger-200',
        ring: 'focus:ring-danger-200 dark:focus:ring-danger-900/50',
    },
    warning: {
        container: modelValue.value 
            ? 'bg-warning-500 dark:bg-warning-600' 
            : 'bg-warning-100 dark:bg-warning-900/30',
        dot: 'bg-white',
        text: 'text-warning-700 dark:text-warning-200',
        ring: 'focus:ring-warning-200 dark:focus:ring-warning-900/50',
    },
    info: {
        container: modelValue.value 
            ? 'bg-info-500 dark:bg-info-600' 
            : 'bg-info-100 dark:bg-info-900/30',
        dot: 'bg-white',
        text: 'text-info-700 dark:text-info-200',
        ring: 'focus:ring-info-200 dark:focus:ring-info-900/50',
    },
}));

// Layout classes
const layoutClasses = computed(() => ({
    left: 'flex-row-reverse',
    right: 'flex-row',
    top: 'flex-col items-start',
    bottom: 'flex-col-reverse items-start',
}));

// Toggle switch container classes
const containerClasses = computed(() => [
    'relative inline-flex items-center rounded-full transition-all duration-200 ease-in-out',
    'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-neutral-900',
    sizeClasses.value[size.value].container,
    colorClasses.value[color.value].container,
    colorClasses.value[color.value].ring,
    {
        'opacity-50 cursor-not-allowed': disabled.value,
        'cursor-pointer hover:opacity-90': !disabled.value,
        'ring-2 ring-offset-1 ring-offset-white dark:ring-offset-neutral-900': modelValue.value && !disabled.value,
    },
]);

// Toggle dot classes
const dotClasses = computed(() => [
    'inline-block rounded-full transform transition-all duration-200 ease-in-out',
    'flex items-center justify-center shadow-sm',
    'border border-white/20',
    sizeClasses.value[size.value].dot,
    colorClasses.value[color.value].dot,
    {
        'translate-x-0': !modelValue.value,
        [sizeClasses.value[size.value].dotTranslate]: modelValue.value,
        'shadow-sm': !modelValue.value,
        'shadow-md': modelValue.value,
    },
]);

// Toggle text classes
const textClasses = computed(() => [
    sizeClasses.value[size.value].text,
    colorClasses.value[color.value].text,
    'font-medium transition-colors',
    {
        'ml-3': ['right', 'bottom'].includes(labelPosition.value),
        'mr-3': labelPosition.value === 'left',
        'mb-1.5': labelPosition.value === 'bottom',
        'mt-1.5': labelPosition.value === 'top',
        'opacity-70': disabled.value,
    },
]);

// Toggle wrapper classes
const wrapperClasses = computed(() => [
    'flex items-center',
    layoutClasses.value[labelPosition.value],
    {
        'space-x-3': ['left', 'right'].includes(labelPosition.value),
        'space-y-1.5': ['top', 'bottom'].includes(labelPosition.value),
    },
]);

// Toggle the switch
const toggle = () => {
    if (disabled.value) return;
    
    const newValue = !modelValue.value;
    emit('update:modelValue', newValue);
    emit('change', newValue);
    emit('toggle', newValue);
    
    // Play a subtle click sound for better feedback
    if (typeof window !== 'undefined') {
        const audio = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU' + 'A'.repeat(100));
        audio.volume = 0.2;
        audio.play().catch(() => {});
    }
};

// Handle keyboard events
const onKeydown = (e) => {
    if ([' ', 'Enter'].includes(e.key)) {
        e.preventDefault();
        toggle();
    }
};

// Expose methods
defineExpose({
    toggle,
});
</script>

<template>
    <div :class="wrapperClasses">
        <!-- Label -->
        <label v-if="label || $slots.default" :for="toggleId" :class="['select-none', textClasses, { 'cursor-pointer': !disabled, 'cursor-not-allowed': disabled }]">
            <template v-if="label">{{ label }}</template>
            <template v-else><slot /></template>
            <span v-if="required" class="text-danger-500 ml-0.5">*</span>
        </label>

        <!-- Toggle Button -->
        <button
            :id="toggleId"
            type="button"
            role="switch"
            :aria-checked="modelValue"
            :aria-readonly="disabled"
            :class="containerClasses"
            @click="toggle"
            @keydown="onKeydown"
        >
            <!-- Dot -->
            <span :class="dotClasses">
                <!-- Icons -->
                <template v-if="showIcons">
                    <span class="absolute inset-0 flex items-center justify-center transition-opacity" :class="{ 'opacity-0 ease-out duration-100': modelValue, 'opacity-100 ease-in duration-200': !modelValue }">
                        <svg class="h-3 w-3 text-neutral-400" fill="none" viewBox="0 0 12 12">
                            <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="absolute inset-0 flex items-center justify-center transition-opacity" :class="{ 'opacity-100 ease-in duration-200': modelValue, 'opacity-0 ease-out duration-100': !modelValue }">
                        <svg class="h-3 w-3 text-primary-500" fill="currentColor" viewBox="0 0 12 12">
                            <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zM3.293 6.707l1.414-1.414L3.293 3.879 2.586 4.586l.707.707.707-.707zm1.414 1.414l3-3-1.414-1.414-3 3 1.414 1.414zm3-1.586l-3-3-1.414 1.414 3 3 1.414-1.414z" />
                        </svg>
                    </span>
                </template>
            </span>
        </button>

        <!-- On/Off labels -->
        <span v-if="showLabels" :class="[textClasses, 'min-w-[2rem]']">
            {{ modelValue ? onLabel : offLabel }}
        </span>

        <!-- Hidden input for form submission -->
        <input
            type="checkbox"
            :name="name || toggleId"
            :checked="modelValue"
            :disabled="disabled"
            :required="required"
            class="sr-only"
            @change="toggle"
        >
    </div>
</template>
