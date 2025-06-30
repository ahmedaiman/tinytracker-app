<script setup>
import { computed, ref, toRefs, onMounted, onUnmounted } from 'vue';
import InputError from './InputError.vue';

const props = defineProps({
    modelValue: {
        type: [String, Number, Array, Object],
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
    options: {
        type: Array,
        default: () => [],
    },
    optionLabel: {
        type: String,
        default: 'label',
    },
    optionValue: {
        type: String,
        default: 'value',
    },
    placeholder: {
        type: String,
        default: 'Select an option',
    },
    multiple: {
        type: Boolean,
        default: false,
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
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    searchable: {
        type: Boolean,
        default: false,
    },
    clearable: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'change', 'focus', 'blur']);

const { size, disabled, error } = toRefs(props);
const isOpen = ref(false);
const searchQuery = ref('');
const selectRef = ref(null);

const sizeClasses = computed(() => ({
    sm: 'text-xs py-1.5 pl-3 pr-8',
    md: 'text-sm py-2 pl-3 pr-10',
    lg: 'text-base py-2.5 pl-4 pr-12',
}));

const containerClasses = computed(() => [
    'relative w-full',
    { 'opacity-70': disabled.value },
]);

const selectClasses = computed(() => [
    'appearance-none w-full bg-white dark:bg-neutral-800 border rounded-lg shadow-sm transition-all duration-200',
    'focus:outline-none focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-neutral-900',
    'dark:border-neutral-700 dark:text-white dark:placeholder-neutral-400',
    sizeClasses.value[size.value],
    {
        // Default state
        'border-neutral-300 dark:border-neutral-600 focus:border-primary-400 dark:focus:border-primary-400 focus:ring-primary-100 dark:focus:ring-primary-900/30': 
            !error.value && !disabled.value,
        // Error state
        'border-danger-300 dark:border-danger-500 text-danger-900 dark:text-danger-100 focus:border-danger-400 dark:focus:border-danger-400 focus:ring-danger-100 dark:focus:ring-danger-900/30': 
            error.value && !disabled.value,
        // Disabled state
        'bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 dark:text-neutral-400 border-neutral-200 dark:border-neutral-700 cursor-not-allowed': 
            disabled.value,
        // Clearable adjustment
        'pr-10': props.clearable,
    },
]);

const dropdownClasses = computed(() => ({
    'absolute z-10 mt-1 w-full bg-white dark:bg-neutral-800 shadow-lg rounded-md py-1 ring-1 ring-black dark:ring-white/10 ring-opacity-5 dark:ring-opacity-10 max-h-60 overflow-auto': true
}));

const filteredOptions = computed(() => {
    if (!props.searchable || !searchQuery.value) return props.options;
    
    const query = searchQuery.value.toLowerCase();
    return props.options.filter(option => {
        const label = String(option[props.optionLabel] || option).toLowerCase();
        return label.includes(query);
    });
});

const displayValue = computed(() => {
    if (props.multiple && Array.isArray(props.modelValue)) {
        return props.modelValue.length ? `${props.modelValue.length} selected` : '';
    }
    
    const selectedOption = props.options.find(
        option => String(option[props.optionValue] || option) === String(props.modelValue)
    );
    
    return selectedOption ? (selectedOption[props.optionLabel] || selectedOption) : '';
});

const isSelected = (option) => {
    const optionValue = option[props.optionValue] ?? option;
    if (props.multiple) {
        return Array.isArray(props.modelValue) && 
               props.modelValue.some(val => String(val) === String(optionValue));
    }
    return String(props.modelValue) === String(optionValue);
};

const selectOption = (option) => {
    const optionValue = option[props.optionValue] ?? option;
    
    if (props.multiple) {
        const currentValue = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
        const valueIndex = currentValue.findIndex(val => String(val) === String(optionValue));
        
        if (valueIndex > -1) {
            currentValue.splice(valueIndex, 1);
        } else {
            currentValue.push(optionValue);
        }
        
        emit('update:modelValue', currentValue);
        emit('change', currentValue);
    } else {
        emit('update:modelValue', optionValue);
        emit('change', optionValue);
        isOpen.value = false;
    }
};

const clearSelection = (event) => {
    event.stopPropagation();
    emit('update:modelValue', props.multiple ? [] : '');
    emit('change', props.multiple ? [] : '');
};

const onFocus = (event) => {
    if (!disabled.value) {
        isOpen.value = true;
    }
    emit('focus', event);
};

const onBlur = (event) => {
    isOpen.value = false;
    emit('blur', event);
};

const toggleDropdown = () => {
    if (disabled.value) return;
    isOpen.value = !isOpen.value;
    if (isOpen.value && props.searchable) {
        nextTick(() => {
            const searchInput = selectRef.value?.querySelector('input[type="text"]');
            searchInput?.focus();
        });
    }
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (selectRef.value && !selectRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="w-full">
        <!-- Label -->
        <label 
            v-if="label" 
            :for="id || $attrs.id" 
            class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5 transition-colors duration-200"
        >
            {{ label }}
            <span v-if="required" class="text-danger-500 dark:text-danger-400">*</span>
        </label>

        <div :class="containerClasses" ref="selectRef">
            <!-- Select Input -->
            <div 
                :class="[
                    'relative cursor-default',
                    { 'pointer-events-none': disabled }
                ]"
                @click="toggleDropdown"
            >
                <div :class="selectClasses">
                    <span class="block truncate">
                        {{ displayValue || placeholder }}
                    </span>
                </div>

                <!-- Clear Button -->
                <button
                    v-if="clearable && modelValue && !disabled"
                    type="button"
                    class="absolute inset-y-0 right-6 flex items-center pr-1.5 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200"
                    @click.stop="clearSelection"
                    :disabled="disabled"
                    :aria-label="'Clear selection'"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Dropdown Icon -->
                <span class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none">
                    <svg class="h-5 w-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </span>
            </div>

            <!-- Dropdown -->
            <Transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <div 
                    v-show="isOpen"
                    :class="dropdownClasses"
                    @click.stop
                >
                    <!-- Search Input -->
                    <div v-if="searchable" class="sticky top-0 z-10 px-3 py-2 bg-white dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                v-model="searchQuery"
                                class="block w-full pl-10 pr-3 py-2 text-sm border border-neutral-200 dark:border-neutral-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-300 dark:focus:ring-primary-600 focus:border-primary-300 dark:focus:border-primary-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white"
                                :placeholder="'Search ' + (label || 'options')"
                                @click.stop
                                :disabled="disabled"
                                :aria-label="'Search ' + (label || 'options')"
                            />
                        </div>
                    </div>

                    <!-- Options -->
                    <ul class="py-1">
                        <li 
                            v-for="(option, index) in filteredOptions" 
                            :key="index"
                            class="px-3 py-2.5 text-sm cursor-pointer flex items-center transition-colors duration-150"
                            :class="{
                                // Selected state
                                'bg-primary-50 dark:bg-primary-900/30 text-primary-900 dark:text-primary-100': isSelected(option) && !disabled,
                                // Hover state
                                'hover:bg-neutral-100 dark:hover:bg-neutral-700/70 text-neutral-800 dark:text-neutral-200': !isSelected(option) && !disabled,
                                // Disabled state
                                'text-neutral-400 dark:text-neutral-500 cursor-not-allowed': disabled,
                            }"
                            @click="!disabled && selectOption(option)"
                            :aria-selected="isSelected(option)"
                            :aria-disabled="disabled"
                            role="option"
                        >
                            <!-- Checkbox for multiple select -->
                            <span 
                                v-if="multiple"
                                class="mr-3 flex-shrink-0 h-4 w-4 rounded border flex items-center justify-center transition-colors duration-200"
                                :class="{
                                    // Selected state
                                    'bg-primary-500 border-primary-500 text-white': isSelected(option) && !disabled,
                                    // Unselected state
                                    'bg-white dark:bg-neutral-700 border-neutral-300 dark:border-neutral-500': !isSelected(option) && !disabled,
                                    // Disabled state
                                    'bg-neutral-100 dark:bg-neutral-700 border-neutral-200 dark:border-neutral-600': disabled,
                                }"
                                aria-hidden="true"
                            >
                                <svg v-if="isSelected(option)" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            
                            <!-- Option Content -->
                            <span class="block truncate">
                                {{ option[optionLabel] || option }}
                            </span>
                            
                            <!-- Selected Indicator -->
                            <span 
                                v-if="!multiple && isSelected(option)" 
                                class="ml-auto text-primary-500 dark:text-primary-400"
                                aria-hidden="true"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        </li>
                        
                        <!-- No Results -->
                        <li 
                            v-if="filteredOptions.length === 0" 
                            class="px-3 py-3 text-sm text-neutral-500 dark:text-neutral-400 text-center"
                            role="status"
                            aria-live="polite"
                        >
                            <div class="flex flex-col items-center justify-center py-2">
                                <svg class="h-6 w-6 text-neutral-400 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>No options found</div>
                                <div v-if="searchQuery" class="text-xs mt-1">Try a different search term</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </Transition>
        </div>

        <!-- Help text and error message -->
        <div v-if="error || help" class="mt-1.5">
            <InputError v-if="error" :message="error" />
            <p v-else-if="help" class="text-xs text-neutral-500 dark:text-neutral-400">
                {{ help }}
            </p>
        </div>
    </div>
</template>
