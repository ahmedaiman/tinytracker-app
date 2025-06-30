<script setup>
import { computed, provide, reactive, toRefs } from 'vue';

const props = defineProps({
    method: {
        type: String,
        default: 'POST',
        validator: (value) => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'].includes(value.toUpperCase())
    },
    action: {
        type: String,
        default: ''
    },
    model: {
        type: Object,
        default: () => ({})
    },
    rules: {
        type: Object,
        default: () => ({})
    },
    validateOnChange: {
        type: Boolean,
        default: true
    },
    validateOnBlur: {
        type: Boolean,
        default: true
    },
    scrollToError: {
        type: Boolean,
        default: true
    },
    scrollOffset: {
        type: Number,
        default: 20
    },
    autocomplete: {
        type: String,
        default: 'on'
    },
    novalidate: {
        type: Boolean,
        default: false
    },
    class: {
        type: [String, Object, Array],
        default: ''
    },
    style: {
        type: [String, Object, Array],
        default: ''
    }
});

const emit = defineEmits([
    'submit',
    'success',
    'error',
    'invalid',
    'valid',
    'reset',
    'update:model',
    'update:errors'
]);

const state = reactive({
    errors: {},
    isSubmitting: false,
    isValidating: false,
    isDirty: false,
    submitCount: 0,
    touched: {}
});

// Provide form context to child components
provide('form', {
    model: props.model,
    errors: state.errors,
    touched: state.touched,
    isSubmitting: computed(() => state.isSubmitting),
    isValidating: computed(() => state.isValidating),
    isDirty: computed(() => state.isDirty),
    submitCount: computed(() => state.submitCount),
    validateField,
    validateForm,
    resetForm,
    resetField,
    setFieldValue,
    setFieldError,
    setFieldTouched,
    handleChange,
    handleBlur,
    handleSubmit
});

// Watch for model changes to update dirty state
watch(() => JSON.stringify(props.model), (newVal, oldVal) => {
    if (oldVal !== '{}' && newVal !== oldVal) {
        state.isDirty = true;
    }
}, { deep: true });

// Methods
const validateField = async (field) => {
    if (!props.rules[field]) return true;
    
    const rules = Array.isArray(props.rules[field]) 
        ? props.rules[field] 
        : [props.rules[field]];
    
    let isValid = true;
    const errors = [];
    
    for (const rule of rules) {
        let result;
        
        if (typeof rule === 'function') {
            result = await rule(props.model[field], props.model);
        } else if (rule && typeof rule.validate === 'function') {
            result = await rule.validate(props.model[field], props.model);
        }
        
        if (result === false || (typeof result === 'object' && !result.valid)) {
            isValid = false;
            const message = rule.message || 'Validation failed';
            errors.push(typeof message === 'function' ? message() : message);
        }
    }
    
    if (isValid) {
        if (state.errors[field]) {
            const newErrors = { ...state.errors };
            delete newErrors[field];
            state.errors = newErrors;
        }
    } else {
        state.errors = { ...state.errors, [field]: errors[0] };
    }
    
    return isValid;
};

const validateForm = async () => {
    state.isValidating = true;
    let isValid = true;
    
    for (const field in props.rules) {
        const fieldValid = await validateField(field);
        if (!fieldValid) {
            isValid = false;
        }
    }
    
    state.isValidating = false;
    return isValid;
};

const resetForm = () => {
    state.errors = {};
    state.isDirty = false;
    state.submitCount = 0;
    state.touched = {};
    emit('reset');
};

const resetField = (field) => {
    const newErrors = { ...state.errors };
    delete newErrors[field];
    state.errors = newErrors;
    
    const newTouched = { ...state.touched };
    delete newTouched[field];
    state.touched = newTouched;
};

const setFieldValue = (field, value) => {
    props.model[field] = value;
    emit('update:model', { ...props.model });
    
    if (props.validateOnChange) {
        validateField(field);
    }
};

const setFieldError = (field, message) => {
    state.errors = { ...state.errors, [field]: message };
};

const setFieldTouched = (field, isTouched = true) => {
    state.touched = { ...state.touched, [field]: isTouched };
    
    if (isTouched && props.validateOnBlur) {
        validateField(field);
    }
};

const handleChange = (field, value) => {
    setFieldValue(field, value);
};

const handleBlur = (field) => {
    setFieldTouched(field, true);
};

const scrollToFirstError = () => {
    if (!props.scrollToError) return;
    
    const firstErrorField = Object.keys(state.errors)[0];
    if (!firstErrorField) return;
    
    const element = document.querySelector(`[name="${firstErrorField}"]`);
    if (element) {
        const yOffset = props.scrollOffset;
        const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({ top: y, behavior: 'smooth' });
        element.focus({ preventScroll: true });
    }
};

const handleSubmit = async (e) => {
    e?.preventDefault();
    
    state.isSubmitting = true;
    state.submitCount++;
    
    try {
        const isValid = await validateForm();
        
        if (!isValid) {
            emit('invalid', { errors: state.errors });
            scrollToFirstError();
            return;
        }
        
        emit('submit', { values: props.model, form: e?.target });
        
        if (props.action) {
            // Handle form submission to the server
            const formData = new FormData(e?.target);
            const response = await fetch(props.action, {
                method: props.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            
            if (!response.ok) {
                throw new Error('Form submission failed');
            }
            
            emit('success', await response.json());
        } else {
            emit('success', { values: props.model });
        }
    } catch (error) {
        console.error('Form submission error:', error);
        emit('error', error);
    } finally {
        state.isSubmitting = false;
    }
};

// Expose methods
defineExpose({
    validate: validateForm,
    reset: resetForm,
    resetField,
    setFieldValue,
    setFieldError,
    setFieldTouched,
    submit: handleSubmit,
    errors: computed(() => state.errors),
    isSubmitting: computed(() => state.isSubmitting),
    isValidating: computed(() => state.isValidating),
    isDirty: computed(() => state.isDirty),
    submitCount: computed(() => state.submitCount),
    touched: computed(() => state.touched)
});
</script>

<template>
    <form
        :action="action"
        :method="method === 'GET' ? 'GET' : 'POST'"
        :autocomplete="autocomplete"
        :novalidate="novalidate"
        :class="['space-y-6', class]"
        @keydown.enter="handleKeyDown"
        :style="style"
        @submit="handleSubmit"
    >
        <input 
            v-if="method !== 'GET' && method !== 'POST'" 
            type="hidden" 
            name="_method" 
            :value="method"
        >
        
        <slot 
            :errors="state.errors"
            :is-submitting="state.isSubmitting"
            :is-validating="state.isValidating"
            :is-dirty="state.isDirty"
            :submit-count="state.submitCount"
            :touched="state.touched"
            :handle-submit="handleSubmit"
            :reset-form="resetForm"
            :validate-form="validateForm"
        />
    </form>
</template>
