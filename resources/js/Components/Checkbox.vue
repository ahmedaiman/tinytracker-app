<script setup>
import { computed, toRefs } from 'vue';
import InputError from '@/Components/InputError.vue';

const emit = defineEmits(['update:modelValue', 'change']);

const props = defineProps({
    modelValue: {
        type: [Boolean, Array],
        default: false,
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
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    indeterminate: {
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
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'accent', 'success', 'warning', 'error', 'info'].includes(value),
    },
});


const { size, disabled, error, color } = toRefs(props);

const sizeClasses = computed(() => ({
    sm: 'h-3.5 w-3.5',
    md: 'h-4 w-4',
    lg: 'h-5 w-5',
}));

const labelClasses = computed(() => ({
    sm: 'text-xs',
    md: 'text-sm',
    lg: 'text-base',
}));

const colorClasses = computed(() => ({
    primary: 'text-primary-600 dark:text-primary-500 focus:ring-primary-200 dark:focus:ring-primary-900/30 border-neutral-300 dark:border-neutral-600 checked:bg-primary-600 dark:checked:bg-primary-500 hover:border-primary-400 dark:hover:border-primary-400',
    secondary: 'text-secondary-600 dark:text-secondary-500 focus:ring-secondary-200 dark:focus:ring-secondary-900/30 border-neutral-300 dark:border-neutral-600 checked:bg-secondary-600 dark:checked:bg-secondary-500 hover:border-secondary-400 dark:hover:border-secondary-400',
    accent: 'text-accent-600 dark:text-accent-500 focus:ring-accent-200 dark:focus:ring-accent-900/30 border-neutral-300 dark:border-neutral-600 checked:bg-accent-600 dark:checked:bg-accent-500 hover:border-accent-400 dark:hover:border-accent-400',
    success: 'text-success-600 dark:text-success-500 focus:ring-success-200 dark:focus:ring-success-900/30 border-neutral-300 dark:border-neutral-600 checked:bg-success-600 dark:checked:bg-success-500 hover:border-success-400 dark:hover:border-success-400',
    warning: 'text-warning-600 dark:text-warning-500 focus:ring-warning-200 dark:focus:ring-warning-900/30 border-neutral-300 dark:border-neutral-600 checked:bg-warning-600 dark:checked:bg-warning-500 hover:border-warning-400 dark:hover:border-warning-400',
    error: 'text-error-600 dark:text-error-500 focus:ring-error-200 dark:focus:ring-error-900/30 border-neutral-300 dark:border-neutral-600 checked:bg-error-600 dark:checked:bg-error-500 hover:border-error-400 dark:hover:border-error-400',
    info: 'text-info-600 dark:text-info-500 focus:ring-info-200 dark:focus:ring-info-900/30 border-neutral-300 dark:border-neutral-600 checked:bg-info-600 dark:checked:bg-info-500 hover:border-info-400 dark:hover:border-info-400',
}));

const isChecked = computed(() => {
    if (Array.isArray(props.modelValue)) {
        return props.modelValue.includes(props.value);
    }
    return props.modelValue;
});

const updateValue = (event) => {
    let newValue;
    
    if (Array.isArray(props.modelValue)) {
        newValue = [...props.modelValue];
        const valueIndex = newValue.indexOf(props.value);
        
        if (event.target.checked) {
            if (valueIndex === -1) {
                newValue.push(props.value);
            }
        } else {
            if (valueIndex > -1) {
                newValue.splice(valueIndex, 1);
            }
        }
    } else {
        newValue = event.target.checked;
    }
    
    emit('update:modelValue', newValue);
    emit('change', newValue);
};

const toggleIndeterminate = (el) => {
    if (el) {
        el.indeterminate = props.indeterminate;
    }
};

const checkboxId = computed(() => props.id || `checkbox-${Math.random().toString(36).substr(2, 9)}`);
</script>

<template>
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input
                :id="checkboxId"
                ref="checkbox"
                type="checkbox"
                :name="name || checkboxId"
                :checked="isChecked"
                :disabled="disabled"
                :required="required"
                :class="[
                    'rounded border-2 transition-all duration-200 ease-in-out',
                    'focus:outline-none focus:ring-0',
                    'dark:ring-offset-neutral-800',
                    sizeClasses[size],
                    colorClasses[color],
                    {
                        'opacity-50 cursor-not-allowed': disabled,
                        'border-error-300 focus:ring-error-200 dark:border-error-700': error,
                        'cursor-pointer': !disabled,
                        'dark:bg-neutral-800': !disabled,
                        'dark:checked:bg-current': !disabled,
                    },
                ]"
                :value="value"
                @change="updateValue"
                v-bind="$attrs"
            >
        </div>
        
        <div v-if="label || $slots.default || help" class="ml-3">
            <label 
                :for="checkboxId" 
                :class="[
                    'block font-medium transition-colors duration-200',
                    labelClasses[size],
                    {
                        'text-neutral-700 dark:text-neutral-300': !error && !disabled,
                        'text-error-600 dark:text-error-400': error && !disabled,
                        'text-neutral-400 dark:text-neutral-600': disabled,
                        'cursor-pointer': !disabled,
                    },
                ]"
            >
                <slot>{{ label }}</slot>
                <span v-if="required" class="text-error-500 dark:text-error-400 ml-0.5">*</span>
            </label>
            
            <p 
                v-if="help && !error" 
                class="text-neutral-500 dark:text-neutral-400 mt-1 text-xs"
                :class="{
                    'opacity-50': disabled
                }"
            >
                {{ help }}
            </p>
            
            <InputError v-if="error" :message="error" class="mt-0.5" />
        </div>
    </div>
</template>
