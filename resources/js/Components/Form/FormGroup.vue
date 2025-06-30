<script setup>
import { computed, inject, toRefs } from 'vue';

const props = defineProps({
    label: {
        type: String,
        default: ''
    },
    labelFor: {
        type: String,
        default: ''
    },
    help: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    error: {
        type: [String, Boolean],
        default: ''
    },
    name: {
        type: String,
        default: ''
    },
    inline: {
        type: Boolean,
        default: false
    },
    labelClass: {
        type: String,
        default: ''
    },
    helpClass: {
        type: String,
        default: ''
    },
    errorClass: {
        type: String,
        default: ''
    },
    wrapperClass: {
        type: String,
        default: ''
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    }
});

const form = inject('form', null);
const { label, labelFor, help, required, error, name, inline, size } = toRefs(props);

// Get error from form context if not explicitly provided
const fieldError = computed(() => {
    if (error.value !== '') return error.value;
    if (form && name.value) return form.errors[name.value];
    return '';
});

// Check if field has been touched
const isTouched = computed(() => {
    if (!form) return false;
    return form.touched[name.value] || false;
});

// Show error only if field is touched or form has been submitted
const showError = computed(() => {
    if (!form) return Boolean(fieldError.value);
    return Boolean(fieldError.value) && (isTouched.value || form.submitCount > 0);
});

// Size classes
const sizeClasses = computed(() => ({
    sm: 'text-xs',
    md: 'text-sm',
    lg: 'text-base',
}));

// Label classes
const labelClasses = computed(() => [
    'block font-medium transition-colors duration-200',
    sizeClasses.value[size.value],
    {
        'text-neutral-700 dark:text-neutral-300': !showError.value,
        'text-error-600 dark:text-error-500': showError.value,
        'opacity-60 cursor-not-allowed': props.disabled,
    },
    props.labelClass
]);

// Help text classes
const helpClasses = computed(() => [
    'mt-1 transition-colors duration-200',
    sizeClasses.value[size.value],
    'text-neutral-500 dark:text-neutral-400',
    props.helpClass
]);

// Error message classes
const errorClasses = computed(() => [
    'mt-1 flex items-start space-x-1.5 transition-colors duration-200',
    sizeClasses.value[size.value],
    'text-error-600 dark:text-error-500',
    props.errorClass
]);

// Wrapper classes
const wrapperClasses = computed(() => [
    'space-y-1.5 transition-all duration-200',
    {
        'flex items-start space-x-4': inline.value,
        'opacity-75': props.disabled
    },
    props.wrapperClass
]);

// Content classes
const contentClasses = computed(() => ({
    'flex-1 w-full': inline.value,
}));

// Required indicator classes
const requiredClasses = computed(() => [
    'ml-0.5 font-bold transition-colors duration-200',
    showError.value ? 'text-error-600 dark:text-error-500' : 'text-primary-600 dark:text-primary-400'
]);
</script>

<template>
    <div :class="wrapperClasses">
        <!-- Label -->
        <label 
            v-if="label || $slots.label" 
            :for="labelFor || name" 
            :class="labelClasses"
            :aria-invalid="showError ? 'true' : 'false'"
            :aria-required="required ? 'true' : 'false'"
        >
            <slot name="label">
                {{ label }}
                <span 
                    v-if="required" 
                    :class="requiredClasses"
                    aria-hidden="true"
                >*</span>
            </slot>
        </label>

        <!-- Content -->
        <div :class="contentClasses">
            <slot></slot>

            <!-- Help text -->
            <p 
                v-if="help && !showError" 
                :class="helpClasses"
                v-html="help"
                :id="`${name}-help`"
            ></p>
            
            <!-- Error message -->
            <div 
                v-if="showError" 
                :class="errorClasses"
                role="alert"
                :id="`${name}-error`"
                :aria-live="showError ? 'assertive' : 'off'"
            >
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>{{ fieldError }}</span>
            </div>
        </div>
    </div>
</template>
