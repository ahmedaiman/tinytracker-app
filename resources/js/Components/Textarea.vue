<script setup>
import { computed, onMounted, ref, toRefs, watch, nextTick } from 'vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
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
    placeholder: {
        type: String,
        default: '',
    },
    rows: {
        type: [String, Number],
        default: 3,
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
    autosize: {
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
    resize: {
        type: [String, Boolean],
        default: 'vertical',
        validator: (value) => [true, false, 'none', 'both', 'horizontal', 'vertical'].includes(value),
    },
    maxlength: {
        type: [String, Number],
        default: null,
    },
    showCounter: {
        type: Boolean,
        default: false,
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

const textarea = ref(null);
const isFocused = ref(false);
const { size, disabled, error, autosize } = toRefs(props);

const sizeClasses = computed(() => ({
    sm: 'px-2.5 py-1.5 text-xs',
    md: 'px-3 py-2 text-sm',
    lg: 'px-4 py-3 text-base',
}));

const textareaClasses = computed(() => [
    'block w-full rounded-lg border bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 shadow-sm transition-all duration-200',
    'placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-offset-1',
    {
        // Size classes
        'px-2.5 py-1.5 text-xs': size.value === 'sm',
        'px-3 py-2 text-sm': size.value === 'md' || !size.value,
        'px-4 py-3 text-base': size.value === 'lg',
        // State classes
        'border-neutral-300 dark:border-neutral-600 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-200 dark:focus:ring-primary-900/50': !error,
        'border-danger-500 dark:border-danger-400 text-danger-900 dark:text-danger-100 placeholder-danger-300 dark:placeholder-danger-700 focus:border-danger-500 dark:focus:border-danger-400 focus:ring-danger-200 dark:focus:ring-danger-900/50': error,
        'bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 dark:text-neutral-400 cursor-not-allowed': disabled.value,
    },
    resizeClass.value,
]);

const resizeClass = computed(() => {
    if (props.resize === false) return 'resize-none';
    if (props.resize === true) return 'resize';
    return `resize-${props.resize}`;
});

const counterClasses = computed(() => ({
    'text-xs mt-1 transition-colors': true,
    'text-neutral-500 dark:text-neutral-400': !(props.showCounter && props.maxlength && String(props.modelValue).length > props.maxlength),
    'text-danger-500 dark:text-danger-400': props.showCounter && props.maxlength && String(props.modelValue).length > props.maxlength,
}));

const characterCount = computed(() => {
    return String(props.modelValue || '').length;
});

const onInput = (event) => {
    emit('update:modelValue', event.target.value);
    
    if (autosize.value) {
        resizeTextarea();
    }
};

const onFocus = (event) => {
    isFocused.value = true;
    emit('focus', event);
};

const onBlur = (event) => {
    isFocused.value = false;
    emit('blur', event);
};

const resizeTextarea = () => {
    if (!autosize.value || !textarea.value) return;

    // Store current scroll position to prevent jumping
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Reset height to auto to get the correct scrollHeight
    textarea.value.style.height = 'auto';
    
    // Calculate new height with min and max constraints
    const minHeight = parseInt(getComputedStyle(textarea.value).minHeight) || 0;
    const maxHeight = 300; // 300px max height
    let newHeight = Math.max(textarea.value.scrollHeight, minHeight);
    
    // Apply max height constraint
    if (maxHeight > 0) {
        newHeight = Math.min(newHeight, maxHeight);
    }
    
    // Apply the new height
    textarea.value.style.height = `${newHeight}px`;
    
    // Restore scroll position
    window.scrollTo(0, scrollTop);
};

const focus = () => {
    textarea.value?.focus();
};

const blur = () => {
    textarea.value?.blur();
};

watch(() => props.modelValue, () => {
    if (autosize.value) {
        nextTick(resizeTextarea);
    }
});

onMounted(() => {
    if (props.autofocus) {
        textarea.value?.focus();
    }
    
    if (autosize.value) {
        resizeTextarea();
    }
});

defineExpose({
    focus,
    blur,
    textarea,
});
</script>

<template>
    <div class="w-full">
        <!-- Label -->
        <label 
            v-if="label" 
            :for="id || $attrs.id" 
            class="block text-sm font-medium text-peach-700 mb-1"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <div class="relative">
            <!-- Textarea -->
            <textarea
                :id="id || $attrs.id"
                ref="textarea"
                :class="textareaClasses"
                :value="modelValue"
                :placeholder="placeholder"
                :rows="rows"
                :required="required"
                :disabled="disabled"
                :readonly="readonly"
                :maxlength="maxlength || undefined"
                v-bind="$attrs"
                @input="onInput"
                @focus="onFocus"
                @blur="onBlur"
                @keyup="$emit('keyup', $event)"
                @keydown="$emit('keydown', $event)"
                @keypress="$emit('keypress', $event)"
            ></textarea>

            <!-- Character Counter -->
            <div v-if="showCounter && maxlength" :class="counterClasses" :aria-live="'polite'" :aria-atomic="true">
                <span class="sr-only">Character count: </span>
                <span :class="{ 'font-medium': String(modelValue).length > maxlength * 0.9 }">
                    {{ String(modelValue).length }}
                </span>
                <span aria-hidden="true">/</span>
                <span class="sr-only">out of </span>
                {{ maxlength }} characters
                <span v-if="String(modelValue).length > maxlength" class="ml-1 font-medium">
                    ({{ String(modelValue).length - maxlength }} over limit)
                </span>
            </div>
        </div>

        <!-- Help text and error message -->
        <div v-if="error || help" class="mt-2">
            <InputError 
                v-if="error" 
                :message="error" 
                type="error"
                class="mt-0"
            />
            <p v-else class="text-xs text-peach-500">
                {{ help }}
            </p>
        </div>
    </div>
</template>
