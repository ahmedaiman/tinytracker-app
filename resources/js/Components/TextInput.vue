<script setup>
import { computed, onMounted, ref, toRefs } from 'vue';
import InputError from './InputError.vue';

const props = defineProps({
    /**
     * The input's value
     * @model
     */
    modelValue: {
        type: [String, Number],
        default: '',
    },
    id: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    /**
     * Input type
     * @values 'text', 'email', 'password', 'tel', 'number', 'search', 'url'
     */
    type: {
        type: String,
        default: 'text',
        validator: (value) => ['text', 'email', 'password', 'tel', 'number', 'search', 'url', 'date', 'time', 'datetime-local'].includes(value),
    },
    placeholder: {
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
    readonly: {
        type: Boolean,
        default: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    autocomplete: {
        type: String,
        default: 'off',
    },
    error: {
        type: [String, Boolean],
        default: '',
    },
    success: {
        type: [String, Boolean],
        default: '',
    },
    help: {
        type: String,
        default: '',
    },
    /**
     * Input size
     * @values 'sm', 'md', 'lg'
     */
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    
    /**
     * Color variant
     * @values 'default', 'primary', 'secondary', 'accent', 'success', 'danger', 'warning', 'info', 'neutral'
     */
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'primary', 'secondary', 'accent', 'success', 'danger', 'warning', 'info', 'neutral'].includes(value),
    },
    
    /**
     * Whether to show a loading state
     */
    loading: {
        type: Boolean,
        default: false,
    },
    
    /**
     * Loading indicator position
     * @values 'left', 'right'
     */
    loadingPosition: {
        type: String,
        default: 'right',
        validator: (value) => ['left', 'right'].includes(value),
    },
    prefixIcon: {
        type: String,
        default: '',
    },
    suffixIcon: {
        type: String,
        default: '',
    },
    showPasswordToggle: {
        type: Boolean,
        default: false,
    },
    inputClass: {
        type: String,
        default: '',
    },
});

const emit = defineEmits([
    'update:modelValue',
    'focus',
    'blur',
    'keyup',
    'keydown',
    'keypress',
]);

const input = ref(null);
const isFocused = ref(false);
const showPassword = ref(false);
const { variant, size, disabled, error, success, loading } = toRefs(props);

// Size classes are now handled directly in the template for better reliability

// Default variant classes
const defaultVariant = {
    base: 'border border-neutral-300 dark:border-neutral-600 text-neutral-900 dark:text-white',
    focus: 'focus:border-primary-400 dark:focus:border-primary-400 focus:ring-2 focus:ring-offset-1 focus:ring-primary-100 dark:focus:ring-primary-900/30 dark:focus:ring-offset-neutral-900',
    placeholder: 'placeholder-neutral-400 dark:placeholder-neutral-500',
};

// Variant classes
const variantClasses = computed(() => {
    // Error state takes highest priority
    if (error.value) {
        return {
            base: 'border-danger-300 dark:border-danger-500 text-danger-900 dark:text-danger-100',
            focus: 'focus:border-danger-400 dark:focus:border-danger-400 focus:ring-2 focus:ring-offset-1 focus:ring-danger-100 dark:focus:ring-danger-900/30 dark:focus:ring-offset-neutral-900',
            placeholder: 'placeholder-danger-400 dark:placeholder-danger-500/70',
        };
    }
    
    // Success state
    if (success.value) {
        return {
            base: 'border-success-300 dark:border-success-500 text-success-900 dark:text-success-100',
            focus: 'focus:border-success-400 dark:focus:border-success-400 focus:ring-2 focus:ring-offset-1 focus:ring-success-100 dark:focus:ring-success-900/30 dark:focus:ring-offset-neutral-900',
            placeholder: 'placeholder-success-400 dark:placeholder-success-500/70',
        };
    }
    
    // Define all variants
    const variants = {
        default: defaultVariant,
        primary: {
            base: 'border-primary-300 dark:border-primary-600 text-primary-900 dark:text-white',
            focus: 'focus:border-primary-400 dark:focus:border-primary-400 focus:ring-2 focus:ring-offset-1 focus:ring-primary-100 dark:focus:ring-primary-900/30 dark:focus:ring-offset-neutral-900',
            placeholder: 'placeholder-primary-400 dark:placeholder-primary-500/70',
        },
        secondary: {
            base: 'border-secondary-300 dark:border-secondary-600 text-secondary-900 dark:text-white',
            focus: 'focus:border-secondary-400 dark:focus:border-secondary-400 focus:ring-2 focus:ring-offset-1 focus:ring-secondary-100 dark:focus:ring-secondary-900/30 dark:focus:ring-offset-neutral-900',
            placeholder: 'placeholder-secondary-400 dark:placeholder-secondary-500/70',
        },
        accent: {
            base: 'border-accent-300 dark:border-accent-600 text-accent-900 dark:text-white',
            focus: 'focus:border-accent-400 dark:focus:border-accent-400 focus:ring-2 focus:ring-offset-1 focus:ring-accent-100 dark:focus:ring-accent-900/30 dark:focus:ring-offset-neutral-900',
            placeholder: 'placeholder-accent-400 dark:placeholder-accent-500/70',
        },
        success: {
            base: 'border-success-300 dark:border-success-600 text-success-900 dark:text-white',
            focus: 'focus:border-success-400 dark:focus:border-success-400 focus:ring-2 focus:ring-offset-1 focus:ring-success-100 dark:focus:ring-success-900/30 dark:focus:ring-offset-neutral-900',
            placeholder: 'placeholder-success-400 dark:placeholder-success-500/70',
        },
        danger: {
            base: 'border-danger-300 dark:border-danger-600 text-danger-900 dark:text-white',
            focus: 'focus:border-danger-400 dark:focus:border-danger-400 focus:ring-2 focus:ring-offset-1 focus:ring-danger-100 dark:focus:ring-danger-900/30 dark:focus:ring-offset-neutral-900',
            placeholder: 'placeholder-danger-400 dark:placeholder-danger-500/70',
        },
        warning: {
            base: 'border-warning-300 dark:border-warning-600 text-warning-900 dark:text-white',
            focus: 'focus:border-warning-400 dark:focus:border-warning-400 focus:ring-2 focus:ring-offset-1 focus:ring-warning-100 dark:focus:ring-warning-900/30 dark:focus:ring-offset-neutral-900',
            placeholder: 'placeholder-warning-400 dark:placeholder-warning-500/70',
        },
        info: {
            base: 'border-info-300 dark:border-info-600 text-info-900 dark:text-white',
            focus: 'focus:border-info-400 dark:focus:border-info-400 focus:ring-2 focus:ring-offset-1 focus:ring-info-100 dark:focus:ring-info-900/30 dark:focus:ring-offset-neutral-900',
            placeholder: 'placeholder-info-400 dark:placeholder-info-500/70',
        },
        neutral: {
            base: 'border-neutral-300 dark:border-neutral-600 text-neutral-900 dark:text-white',
            focus: 'focus:border-neutral-400 dark:focus:border-neutral-400 focus:ring-2 focus:ring-offset-1 focus:ring-neutral-100 dark:focus:ring-neutral-900/30 dark:focus:ring-offset-neutral-900',
            placeholder: 'placeholder-neutral-400 dark:placeholder-neutral-500/70',
        },
    };
    
    // Return the selected variant or default if not found
    return variants[variant?.value] || variants.default;
});

// Label classes
const labelClasses = computed(() => ({
    base: 'block text-sm font-medium mb-1.5 transition-colors duration-200',
    default: 'text-neutral-700 dark:text-neutral-300',
    error: 'text-danger-600 dark:text-danger-400',
    success: 'text-success-600 dark:text-success-400',
}));

// Get the current label class based on state
const currentLabelClass = computed(() => ({
    [labelClasses.value.base]: true,
    [labelClasses.value.error]: !!error.value,
    [labelClasses.value.success]: !!success.value && !error.value,
    [labelClasses.value.default]: !error.value && !success.value,
}));

// Help text classes
const helpTextClasses = computed(() => ({
    base: 'text-xs mt-1.5',
    default: 'text-neutral-500 dark:text-neutral-400',
    error: 'text-danger-500 dark:text-danger-400',
    success: 'text-success-500 dark:text-success-400',
}));

// Get the current help text class based on state
const currentHelpClass = computed(() => ({
    [helpTextClasses.value.base]: true,
    [helpTextClasses.value.error]: !!error.value,
    [helpTextClasses.value.success]: !!success.value && !error.value,
    [helpTextClasses.value.default]: !error.value && !success.value,
}));

// Methods
const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const computedType = computed(() => {
    if (props.type !== 'password') return props.type;
    return showPassword.value ? 'text' : 'password';
});

const onInput = (event) => {
    emit('update:modelValue', event.target.value);
};

const onFocus = (event) => {
    isFocused.value = true;
    emit('focus', event);
};

const onBlur = (event) => {
    isFocused.value = false;
    emit('blur', event);
};

const focus = () => {
    input.value?.focus();
};

const blur = () => {
    input.value?.blur();
};

const select = () => {
    input.value?.select();
};

onMounted(() => {
    if (props.autofocus) {
        focus();
    }
});

defineExpose({
    focus,
    blur,
    select,
    input,
});
</script>

<template>
    <div class="w-full">
        <!-- Label -->
        <label v-if="label" :for="id || $attrs.id" :class="labelClasses">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>

        <!-- Input Wrapper -->
        <div class="relative mt-1">
            <!-- Loading Indicator -->
            <div 
                v-if="loading && loadingPosition === 'left'" 
                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                :class="{ 'text-neutral-400': !error && !success, 'text-danger-400': error, 'text-success-400': success }"
            >
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Prefix Icon -->
            <div 
                v-else-if="prefixIcon" 
                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                :class="{
                    'text-neutral-400 dark:text-neutral-500': !error && !success,
                    'text-danger-400 dark:text-danger-500': error,
                    'text-success-400 dark:text-success-500': success && !error,
                }"
            >
                <slot name="prefix-icon">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="prefixIcon" />
                    </svg>
                </slot>
            </div>

            <!-- Input -->
            <input
                :id="id || $attrs.id"
                ref="input"
                :type="computedType"
                :value="modelValue"
                :placeholder="placeholder"
                :required="required"
                :disabled="disabled || loading"
                :readonly="readonly"
                :autocomplete="autocomplete"
                :aria-invalid="!!error"
                :aria-describedby="error || success || help ? `${id}-help` : undefined"
                class="block w-full rounded-lg shadow-sm transition-all duration-200 bg-white dark:bg-neutral-800"
                :class="[
                    // Size classes
                    {
                        'px-2.5 py-1.5 text-xs h-8': size === 'sm',
                        'px-3 py-2 text-sm h-9': size === 'md' || !size,
                        'px-4 py-3 text-base h-10': size === 'lg',
                    },
                    // Variant classes
                    variantClasses.value?.base || defaultVariant.base,
                    variantClasses.value?.focus || defaultVariant.focus,
                    {
                        // Padding adjustments
                        'pl-10': prefixIcon || (loading && loadingPosition === 'left'),
                        'pr-10': suffixIcon || (showPasswordToggle && type === 'password') || (loading && loadingPosition === 'right'),
                        // States
                        'bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 dark:text-neutral-400 cursor-not-allowed': disabled || loading,
                        'opacity-70': disabled,
                    },
                    variantClasses.value?.placeholder || defaultVariant.placeholder,
                    inputClass
                ]"
                v-bind="$attrs"
                @input="onInput"
                @focus="onFocus"
                @blur="onBlur"
                @keyup="$emit('keyup', $event)"
                @keydown="$emit('keydown', $event)"
                @keypress="$emit('keypress', $event)"
            />

            <!-- Loading Indicator (Right) -->
            <div 
                v-if="loading && loadingPosition === 'right'" 
                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                :class="{ 'text-neutral-400': !error && !success, 'text-danger-400': error, 'text-success-400': success }"
            >
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Suffix Icon or Password Toggle -->
            <div 
                v-else-if="suffixIcon || (showPasswordToggle && type === 'password')" 
                class="absolute inset-y-0 right-0 flex items-center pr-3"
            >
                <button
                    v-if="showPasswordToggle && type === 'password'"
                    type="button"
                    class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-primary-200 dark:focus:ring-primary-900/50 rounded-md p-1 transition-colors duration-200"
                    :class="{ 'cursor-not-allowed': disabled || loading }"
                    :disabled="disabled || loading"
                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                    @click="togglePassword"
                    tabindex="-1"
                >
                    <svg v-if="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                    <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
                <slot v-else name="suffix-icon">
                    <svg 
                        class="h-5 w-5" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                        :class="{
                            'text-neutral-400 dark:text-neutral-500': !error && !success,
                            'text-danger-400 dark:text-danger-500': error,
                            'text-success-400 dark:text-success-500': success && !error,
                        }"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="suffixIcon" />
                    </svg>
                </slot>
            </div>
        </div>

        <!-- Help text and error/success messages -->
        <div v-if="error || success || help" :id="`${id}-help`" class="mt-1.5">
            <InputError v-if="error" :message="error" />
            <p v-else-if="success" class="text-sm font-medium text-success-500 dark:text-success-400">
                {{ success }}
            </p>
            <p v-else-if="help" class="text-xs text-neutral-500 dark:text-neutral-400">
                {{ help }}
            </p>
        </div>
    </div>
</template>
