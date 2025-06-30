<script setup>
import { computed, toRefs } from 'vue';
import InputError from '@/Components/InputError.vue';

const emit = defineEmits(['update:modelValue', 'change', 'focus', 'blur', 'keydown']);

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean, Object],
        default: null,
    },
    value: {
        type: [String, Number, Boolean, Object],
        default: null,
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
    /**
     * Additional description text shown below the label
     */
    description: {
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
    error: {
        type: [String, Boolean],
        default: '',
    },
    help: {
        type: String,
        default: '',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value),
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => [
            'primary', 'secondary', 'accent', 'success', 
            'danger', 'warning', 'info', 'light', 'dark'
        ].includes(value),
    },
    /**
     * Visual style variant
     * @values 'outline', 'filled', 'ghost'
     */
    variant: {
        type: String,
        default: 'outline',
        validator: (value) => ['outline', 'filled', 'ghost'].includes(value),
    },
    /**
     * Border radius style
     */
    rounded: {
        type: [Boolean, String],
        default: 'full',
        validator: (value) => 
            typeof value === 'boolean' || 
            ['none', 'sm', 'md', 'lg', 'full'].includes(value)
    },
    inline: {
        type: Boolean,
        default: false,
    },
    noStyle: {
        type: Boolean,
        default: false,
    },
    showError: {
        type: Boolean,
        default: true,
    },
    showHelp: {
        type: Boolean,
        default: true,
    },
    tabindex: {
        type: [String, Number],
        default: 0,
    },
    /**
     * Show loading state
     */
    loading: {
        type: Boolean,
        default: false,
    },
    /**
     * Icon to show when loading
     */
    loadingIcon: {
        type: String,
        default: 'loader-2',
    },
    /**
     * Position of the loading icon
     * @values 'left', 'right'
     */
    loadingPosition: {
        type: String,
        default: 'right',
        validator: (value) => ['left', 'right'].includes(value),
    },
});

const { 
    size, 
    disabled, 
    error, 
    color, 
    inline, 
    noStyle,
    showError,
    showHelp,
    tabindex,
    loading,
    loadingPosition,
    variant,
    rounded
} = toRefs(props);

// Size classes
const sizeClasses = computed(() => ({
    xs: 'h-3 w-3 text-xs',
    sm: 'h-3.5 w-3.5 text-sm',
    md: 'h-4 w-4 text-sm',
    lg: 'h-5 w-5 text-base',
    xl: 'h-6 w-6 text-lg',
}[size.value]));

// Label size classes
const labelClasses = computed(() => ({
    xs: 'text-xs',
    sm: 'text-sm',
    md: 'text-sm',
    lg: 'text-base',
    xl: 'text-lg',
}[size.value]));

// Container classes
const containerClasses = computed(() => [
    'flex items-start',
    {
        'inline-flex mr-6': inline.value,
        'mb-3 last:mb-0': !inline.value,
        'opacity-60': disabled.value || loading.value,
    }
]);

// Rounded classes
const roundedClasses = computed(() => {
    if (rounded.value === true || rounded.value === 'full') return 'rounded-full';
    if (rounded.value === false || rounded.value === 'none') return 'rounded-none';
    return `rounded-${rounded.value}`;
});

// Color variant classes
const colorVariantClasses = computed(() => {
    if (noStyle.value) return '';
    
    const base = 'focus:ring-2 focus:ring-offset-2 focus:outline-none';
    
    if (error.value) {
        return `${base} border-danger-500 text-danger-600 focus:ring-danger-100 dark:border-danger-600 dark:text-danger-500 dark:focus:ring-danger-900/30`;
    }
    
    const variants = {
        outline: {
            primary: 'border-2 border-primary-500 text-primary-600 focus:ring-primary-100 dark:border-primary-600 dark:text-primary-500 dark:focus:ring-primary-900/30',
            secondary: 'border-2 border-secondary-500 text-secondary-600 focus:ring-secondary-100 dark:border-secondary-600 dark:text-secondary-500 dark:focus:ring-secondary-900/30',
            accent: 'border-2 border-accent-500 text-accent-600 focus:ring-accent-100 dark:border-accent-600 dark:text-accent-500 dark:focus:ring-accent-900/30',
            success: 'border-2 border-success-500 text-success-600 focus:ring-success-100 dark:border-success-600 dark:text-success-500 dark:focus:ring-success-900/30',
            danger: 'border-2 border-danger-500 text-danger-600 focus:ring-danger-100 dark:border-danger-600 dark:text-danger-500 dark:focus:ring-danger-900/30',
            warning: 'border-2 border-warning-500 text-warning-600 focus:ring-warning-100 dark:border-warning-600 dark:text-warning-500 dark:focus:ring-warning-900/30',
            info: 'border-2 border-info-500 text-info-600 focus:ring-info-100 dark:border-info-600 dark:text-info-500 dark:focus:ring-info-900/30',
            light: 'border-2 border-neutral-200 text-neutral-600 focus:ring-neutral-100 dark:border-neutral-600 dark:text-neutral-400 dark:focus:ring-neutral-900/30',
            dark: 'border-2 border-neutral-800 text-neutral-900 focus:ring-neutral-200 dark:border-neutral-400 dark:text-white dark:focus:ring-neutral-700/30',
        },
        filled: {
            primary: 'border-2 border-primary-500 bg-primary-50 text-primary-600 focus:ring-primary-100 dark:border-primary-600 dark:bg-primary-900/30 dark:text-primary-400 dark:focus:ring-primary-900/50',
            secondary: 'border-2 border-secondary-500 bg-secondary-50 text-secondary-600 focus:ring-secondary-100 dark:border-secondary-600 dark:bg-secondary-900/30 dark:text-secondary-400 dark:focus:ring-secondary-900/50',
            accent: 'border-2 border-accent-500 bg-accent-50 text-accent-600 focus:ring-accent-100 dark:border-accent-600 dark:bg-accent-900/30 dark:text-accent-400 dark:focus:ring-accent-900/50',
            success: 'border-2 border-success-500 bg-success-50 text-success-600 focus:ring-success-100 dark:border-success-600 dark:bg-success-900/30 dark:text-success-400 dark:focus:ring-success-900/50',
            danger: 'border-2 border-danger-500 bg-danger-50 text-danger-600 focus:ring-danger-100 dark:border-danger-600 dark:bg-danger-900/30 dark:text-danger-400 dark:focus:ring-danger-900/50',
            warning: 'border-2 border-warning-500 bg-warning-50 text-warning-600 focus:ring-warning-100 dark:border-warning-600 dark:bg-warning-900/30 dark:text-warning-400 dark:focus:ring-warning-900/50',
            info: 'border-2 border-info-500 bg-info-50 text-info-600 focus:ring-info-100 dark:border-info-600 dark:bg-info-900/30 dark:text-info-400 dark:focus:ring-info-900/50',
            light: 'border-2 border-neutral-200 bg-neutral-50 text-neutral-600 focus:ring-neutral-100 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:focus:ring-neutral-900/50',
            dark: 'border-2 border-neutral-800 bg-neutral-900 text-white focus:ring-neutral-200 dark:border-neutral-400 dark:bg-neutral-700 dark:text-white dark:focus:ring-neutral-700/50',
        },
        ghost: {
            primary: 'border-0 text-primary-600 focus:ring-2 focus:ring-primary-100 dark:text-primary-400 dark:focus:ring-primary-900/30',
            secondary: 'border-0 text-secondary-600 focus:ring-2 focus:ring-secondary-100 dark:text-secondary-400 dark:focus:ring-secondary-900/30',
            accent: 'border-0 text-accent-600 focus:ring-2 focus:ring-accent-100 dark:text-accent-400 dark:focus:ring-accent-900/30',
            success: 'border-0 text-success-600 focus:ring-2 focus:ring-success-100 dark:text-success-400 dark:focus:ring-success-900/30',
            danger: 'border-0 text-danger-600 focus:ring-2 focus:ring-danger-100 dark:text-danger-400 dark:focus:ring-danger-900/30',
            warning: 'border-0 text-warning-600 focus:ring-2 focus:ring-warning-100 dark:text-warning-400 dark:focus:ring-warning-900/30',
            info: 'border-0 text-info-600 focus:ring-2 focus:ring-info-100 dark:text-info-400 dark:focus:ring-info-900/30',
            light: 'border-0 text-neutral-600 focus:ring-2 focus:ring-neutral-100 dark:text-neutral-300 dark:focus:ring-neutral-900/30',
            dark: 'border-0 text-neutral-900 focus:ring-2 focus:ring-neutral-200 dark:text-white dark:focus:ring-neutral-700/30',
        }
    };
    
    return `${base} ${variants[variant.value]?.[color.value] || variants.outline.primary}`;
});

// Check if radio is checked
const isChecked = computed(() => {
    if (props.modelValue === null || props.value === null) return false;
    return String(props.modelValue) === String(props.value);
});

// Generate unique ID for accessibility
const radioId = computed(() => 
    props.id || `radio-${Math.random().toString(36).substr(2, 9)}`
);

// Indicator size classes
const indicatorSize = computed(() => ({
    xs: 'h-1.5 w-1.5',
    sm: 'h-2 w-2',
    md: 'h-2.5 w-2.5',
    lg: 'h-3 w-3',
    xl: 'h-3.5 w-3.5',
}[size.value]));

// Indicator color classes
const indicatorColor = computed(() => {
    if (error.value) return 'bg-danger-500';
    
    const colors = {
        primary: 'bg-primary-600 dark:bg-primary-500',
        secondary: 'bg-secondary-600 dark:bg-secondary-500',
        accent: 'bg-accent-600 dark:bg-accent-500',
        success: 'bg-success-600 dark:bg-success-500',
        danger: 'bg-danger-600 dark:bg-danger-500',
        warning: 'bg-warning-600 dark:bg-warning-500',
        info: 'bg-info-600 dark:bg-info-500',
        light: 'bg-neutral-600 dark:bg-neutral-400',
        dark: 'bg-neutral-900 dark:bg-white',
    };
    
    return colors[color.value] || colors.primary;
});

// Loading color classes
const loadingColor = computed(() => {
    if (error.value) return 'text-danger-500';
    
    const colors = {
        primary: 'text-primary-600 dark:text-primary-400',
        secondary: 'text-secondary-600 dark:text-secondary-400',
        accent: 'text-accent-600 dark:text-accent-400',
        success: 'text-success-600 dark:text-success-400',
        danger: 'text-danger-600 dark:text-danger-400',
        warning: 'text-warning-600 dark:text-warning-400',
        info: 'text-info-600 dark:text-info-400',
        light: 'text-neutral-600 dark:text-neutral-400',
        dark: 'text-neutral-900 dark:text-white',
    };
    
    return colors[color.value] || colors.primary;
});

// ARIA described by attribute
const ariaDescribedBy = computed(() => {
    const ids = [];
    if (error.value) ids.push(`${radioId.value}-error`);
    if (props.help) ids.push(`${radioId.value}-help`);
    if (props.description) ids.push(`${radioId.value}-description`);
    return ids.length ? ids.join(' ') : undefined;
});

// Update value handler
const updateValue = () => {
    if (disabled.value || loading.value) return;
    
    emit('update:modelValue', props.value);
    emit('change', props.value);
};

// Focus event handler
const onFocus = (event) => {
    if (disabled.value || loading.value) return;
    emit('focus', event);
};

// Blur event handler
const onBlur = (event) => {
    if (disabled.value || loading.value) return;
    emit('blur', event);
};

// Keydown event handler
const onKeyDown = (event) => {
    if (disabled.value || loading.value) return;
    
    // Toggle on space or enter key
    if ([' ', 'Enter'].includes(event.key)) {
        event.preventDefault();
        updateValue();
    }
    
    emit('keydown', event);
};
</script>

<template>
    <div :class="containerClasses">
        <div class="relative flex items-start">
            <div class="flex items-center h-5">
                <input
                    :id="radioId"
                    type="radio"
                    :name="name || radioId"
                    :checked="isChecked"
                    :disabled="disabled"
                    :required="required"
                    :aria-invalid="!!error"
                    :aria-describedby="error ? `${radioId}-error` : help ? `${radioId}-help` : undefined"
                    :tabindex="disabled ? -1 : tabindex"
                    :class="[
                        'form-radio',
                        'transition duration-150 ease-in-out',
                        'appearance-none relative',
                        'flex items-center justify-center',
                        'bg-white dark:bg-neutral-800',
                        'focus:outline-none',
                        'rounded-full',
                        sizeClasses,
                        colorVariantClasses,
                        {
                            'cursor-not-allowed': disabled,
                            'opacity-50': disabled,
                            'ring-1 ring-offset-1': !noStyle,
                        },
                    ]"
                    @change="updateValue"
                    @focus="onFocus"
                    @blur="onBlur"
                    v-bind="$attrs"
                >
                <span 
                    v-if="isChecked && !noStyle"
                    :class="[
                        'absolute rounded-full',
                        'transition-all duration-200',
                        'flex items-center justify-center',
                        indicatorSize[size],
                        indicatorColor,
                    ]"
                    aria-hidden="true"
                ></span>
            </div>
            
            <div v-if="label || $slots.default || help || error" class="ml-3">
                <label 
                    :for="radioId" 
                    :class="[
                        'block',
                        'font-medium',
                        labelClasses[size],
                        {
                            'text-neutral-900 dark:text-white': !error && !disabled,
                            'text-neutral-500 dark:text-neutral-400': disabled,
                            'text-danger-600 dark:text-danger-400': error,
                            'cursor-pointer': !disabled,
                            'cursor-not-allowed': disabled,
                        },
                    ]"
                >
                    <slot>{{ label }}</slot>
                    <span 
                        v-if="required" 
                        :class="[
                            'ml-0.5',
                            error ? 'text-danger-500' : 'text-neutral-500 dark:text-neutral-400',
                        ]"
                        aria-hidden="true"
                    >*</span>
                </label>
                
                <p 
                    v-if="help && !error" 
                    class="text-neutral-500 dark:text-neutral-400 mt-1 text-xs"
                    :class="{ 'opacity-50': disabled }"
                >
                    {{ help }}
                </p>
                
                <InputError 
                    v-if="showError && error" 
                    :id="`${radioId}-error`"
                    :message="error" 
                    class="mt-1"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom radio button styling */
input[type="radio"] {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding: 0;
}

/* Focus styles */
input[type="radio"]:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

/* Dark mode transitions */
@media (prefers-color-scheme: dark) {
    input[type="radio"] {
        transition: border-color 0.2s ease-in-out, background-color 0.2s ease-in-out;
    }
}

/* Animation for the radio indicator */
@keyframes radioCheck {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

input[type="radio"]:checked + span {
    animation: radioCheck 0.2s ease-out;
}
</style>
