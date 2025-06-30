<script setup>
import { computed, onMounted, ref, toRefs, watch, watchEffect, onBeforeUnmount } from 'vue';
import { format, parseISO, isValid as isDateValid } from 'date-fns';

const props = defineProps({
  modelValue: {
    type: [Date, String, Number],
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
  placeholder: {
    type: String,
    default: 'Select a date',
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
  format: {
    type: String,
    default: 'yyyy-MM-dd',
  },
  displayFormat: {
    type: String,
    default: 'PPP', // e.g., "April 29th, 2021"
  },
  min: {
    type: [Date, String],
    default: null,
  },
  max: {
    type: [Date, String],
    default: null,
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
  clearable: {
    type: Boolean,
    default: false,
  },
  showIcon: {
    type: Boolean,
    default: true,
  },
  inline: {
    type: Boolean,
    default: false,
  },
  weekStartsOn: {
    type: Number,
    default: 0, // 0 = Sunday, 1 = Monday, etc.
    validator: (value) => value >= 0 && value <= 6,
  },
  firstDayOfWeek: {
    type: Number,
    default: 0, // Alias for weekStartsOn
    validator: (value) => value >= 0 && value <= 6,
  },
  disabledDates: {
    type: Array,
    default: () => [],
  },
  disabledDays: {
    type: Array, // Array of day numbers (0-6, where 0 is Sunday)
    default: () => [],
  },
  showWeekNumbers: {
    type: Boolean,
    default: false,
  },
  yearRange: {
    type: Array,
    default: () => [1900, 2100],
  },
  locale: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits([
  'update:modelValue',
  'focus',
  'blur',
  'input',
  'change',
  'clear',
  'open',
  'close',
  'update:open',
]);

const { 
  modelValue, 
  disabled, 
  readonly, 
  format: dateFormat, 
  displayFormat,
  color,
  size,
  clearable,
  showIcon,
  inline,
  weekStartsOn,
  firstDayOfWeek: firstDayProp,
  min: minDate,
  max: maxDate,
  disabledDates,
  disabledDays: disabledDaysProp,
  showWeekNumbers,
  yearRange,
  locale,
} = toRefs(props);

const firstDayOfWeek = computed(() => firstDayProp.value || weekStartsOn.value);

const datePicker = ref(null);
const isOpen = ref(false);
const currentDate = ref(null);
const inputValue = ref('');
const displayValue = ref('');

// Parse date from various input types
const parseDate = (date) => {
  if (!date) return null;
  if (date instanceof Date) return date;
  if (typeof date === 'string') return new Date(date);
  if (typeof date === 'number') return new Date(date);
  return null;
};

// Format date for display
const formatDate = (date, formatStr = displayFormat.value) => {
  if (!date || !isDateValid(date)) return '';
  return format(date, formatStr, { locale: locale?.value });
};

// Format date for input[type="date"]
const formatForInput = (date) => {
  if (!date || !isDateValid(date)) return '';
  return format(date, 'yyyy-MM-dd');
};

// Update display value when modelValue changes
watchEffect(() => {
  const date = parseDate(modelValue.value);
  currentDate.value = date;
  inputValue.value = date ? formatForInput(date) : '';
  displayValue.value = date ? formatDate(date) : '';
});

// Handle date selection
const selectDate = (event) => {
  if (disabled.value || readonly.value) return;
  
  const newDate = event?.target?.value ? parseISO(event.target.value) : null;
  
  if (!newDate || !isDateValid(newDate)) {
    clearDate();
    return;
  }
  
  // Check if date is disabled
  if (isDateDisabled(newDate)) {
    return;
  }
  
  currentDate.value = newDate;
  emit('update:modelValue', newDate);
  emit('change', newDate);
  
  // Close the picker if not in inline mode
  if (!inline.value) {
    isOpen.value = false;
  }
};

// Clear the selected date
const clearDate = () => {
  if (disabled.value || readonly.value) return;
  
  currentDate.value = null;
  inputValue.value = '';
  displayValue.value = '';
  
  emit('update:modelValue', null);
  emit('change', null);
  emit('clear');
};

// Check if a date is disabled
const isDateDisabled = (date) => {
  if (!date) return false;
  
  // Check min date
  if (minDate.value) {
    const min = parseDate(minDate.value);
    if (min && date < min) return true;
  }
  
  // Check max date
  if (maxDate.value) {
    const max = parseDate(maxDate.value);
    if (max && date > max) return true;
  }
  
  // Check disabled dates
  if (disabledDates.value && disabledDates.value.length > 0) {
    const disabled = disabledDates.value.some(disabledDate => {
      const d = parseDate(disabledDate);
      return d && d.toDateString() === date.toDateString();
    });
    
    if (disabled) return true;
  }
  
  // Check disabled days of week
  if (disabledDaysProp.value && disabledDaysProp.value.length > 0) {
    const day = date.getDay();
    if (disabledDaysProp.value.includes(day)) return true;
  }
  
  return false;
};

// Toggle date picker visibility
const togglePicker = () => {
  if (disabled.value || readonly.value) return;
  
  isOpen.value = !isOpen.value;
  
  if (isOpen.value) {
    emit('open');
  } else {
    emit('close');
  }
  
  emit('update:open', isOpen.value);
};

// Close the picker when clicking outside
const onClickOutside = (event) => {
  if (isOpen.value && !datePicker.value.contains(event.target)) {
    isOpen.value = false;
    emit('close');
    emit('update:open', false);
  }
};

// Handle keyboard events
const onKeydown = (event) => {
  if (event.key === 'Escape' && isOpen.value) {
    event.preventDefault();
    isOpen.value = false;
    emit('close');
    emit('update:open', false);
  } else if (event.key === 'Enter' || event.key === ' ') {
    event.preventDefault();
    togglePicker();
  }
};

// Size classes
const sizeClasses = computed(() => ({
  sm: 'px-2.5 py-1.5 text-xs',
  md: 'px-3 py-2 text-sm',
  lg: 'px-4 py-3 text-base',
}));

// Color classes
const colorClasses = computed(() => ({
  primary: {
    input: 'border-neutral-300 dark:border-neutral-600 focus:border-primary-500 focus:ring-primary-200 dark:focus:ring-primary-900/30',
    icon: 'text-primary-500 dark:text-primary-400',
    clear: 'text-neutral-400 hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300',
    calendar: 'text-primary-600 hover:bg-primary-50 dark:text-primary-400 dark:hover:bg-primary-900/30',
  },
  secondary: {
    input: 'border-neutral-300 dark:border-neutral-600 focus:border-secondary-500 focus:ring-secondary-200 dark:focus:ring-secondary-900/30',
    icon: 'text-secondary-500 dark:text-secondary-400',
    clear: 'text-neutral-400 hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300',
    calendar: 'text-secondary-600 hover:bg-secondary-50 dark:text-secondary-400 dark:hover:bg-secondary-900/30',
  },
  accent: {
    input: 'border-neutral-300 dark:border-neutral-600 focus:border-accent-500 focus:ring-accent-200 dark:focus:ring-accent-900/30',
    icon: 'text-accent-500 dark:text-accent-400',
    clear: 'text-neutral-400 hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300',
    calendar: 'text-accent-600 hover:bg-accent-50 dark:text-accent-400 dark:hover:bg-accent-900/30',
  },
  success: {
    input: 'border-neutral-300 dark:border-neutral-600 focus:border-success-500 focus:ring-success-200 dark:focus:ring-success-900/30',
    icon: 'text-success-500 dark:text-success-400',
    clear: 'text-neutral-400 hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300',
    calendar: 'text-success-600 hover:bg-success-50 dark:text-success-400 dark:hover:bg-success-900/30',
  },
  warning: {
    input: 'border-neutral-300 dark:border-neutral-600 focus:border-warning-500 focus:ring-warning-200 dark:focus:ring-warning-900/30',
    icon: 'text-warning-500 dark:text-warning-400',
    clear: 'text-neutral-400 hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300',
    calendar: 'text-warning-600 hover:bg-warning-50 dark:text-warning-400 dark:hover:bg-warning-900/30',
  },
  error: {
    input: 'border-error-300 dark:border-error-700 focus:border-error-500 focus:ring-error-200 dark:focus:ring-error-900/30',
    icon: 'text-error-500 dark:text-error-400',
    clear: 'text-error-500 hover:text-error-700 dark:text-error-400 dark:hover:text-error-300',
    calendar: 'text-error-600 hover:bg-error-50 dark:text-error-400 dark:hover:bg-error-900/30',
  },
  info: {
    input: 'border-neutral-300 dark:border-neutral-600 focus:border-info-500 focus:ring-info-200 dark:focus:ring-info-900/30',
    icon: 'text-info-500 dark:text-info-400',
    clear: 'text-neutral-400 hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300',
    calendar: 'text-info-600 hover:bg-info-50 dark:text-info-400 dark:hover:bg-info-900/30',
  },
}));

// Input classes
const inputClasses = computed(() => [
  'block w-full rounded-lg border bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white shadow-sm transition-all',
  'focus:outline-none focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-neutral-800',
  'placeholder-neutral-400 dark:placeholder-neutral-500',
  'dark:border-neutral-700 dark:bg-neutral-800/50',
  sizeClasses.value[size.value],
  colorClasses.value[color.value].input,
  {
    'pl-10': showIcon.value,
    'pr-10': clearable.value && currentDate.value,
    'opacity-60 cursor-not-allowed': disabled.value || readonly.value,
    'border-error-300 dark:border-error-700 text-error-900 dark:text-error-100 placeholder-error-300 dark:placeholder-error-700': props.error,
  },
]);

// Calendar button classes
const calendarButtonClasses = computed(() => [
  'absolute inset-y-0 left-0 flex items-center pl-3',
  {
    'pointer-events-none': disabled.value || readonly.value,
    'opacity-50': disabled.value,
  },
]);

// Clear button classes
const clearButtonClasses = computed(() => [
  'absolute inset-y-0 right-0 flex items-center pr-3',
  'transition-colors duration-200',
  colorClasses.value[color.value].clear,
  {
    'cursor-pointer': !disabled.value && !readonly.value,
    'cursor-not-allowed': disabled.value || readonly.value,
    'opacity-50': disabled.value,
  },
]);

// Expose methods
defineExpose({
  focus: () => datePicker.value?.focus(),
  blur: () => datePicker.value?.blur(),
  open: () => { isOpen.value = true; },
  close: () => { isOpen.value = false; },
  toggle: togglePicker,
  clear: clearDate,
});

// Lifecycle hooks
onMounted(() => {
  if (props.autofocus) {
    datePicker.value?.focus();
  }
  
  document.addEventListener('click', onClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', onClickOutside);
});
</script>

<template>
  <div class="relative w-full" ref="datePicker">
    <!-- Hidden input for form submission -->
    <input
      type="hidden"
      :name="name"
      :value="currentDate ? format(currentDate, 'yyyy-MM-dd') : ''"
    >
    
    <!-- Display input -->
    <div class="relative">
      <!-- Calendar icon -->
      <button
        v-if="showIcon"
        type="button"
        :class="[calendarButtonClasses, colorClasses[color].calendar]"
        @click="togglePicker"
        :disabled="disabled || readonly"
        :aria-label="'Open date picker' + (currentDate ? ' for ' + displayValue : '')"
      >
        <svg
          class="h-5 w-5"
          :class="colorClasses[color].icon"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="1.5"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
      </button>
      
      <!-- Date input -->
      <input
        ref="datePicker"
        type="date"
        :id="id"
        v-model="inputValue"
        :class="inputClasses"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled || readonly"
        :readonly="readonly"
        :min="min ? formatForInput(parseDate(min)) : ''"
        :max="max ? formatForInput(parseDate(max)) : ''"
        :aria-invalid="!!error"
        :aria-describedby="error ? `${id}-error` : help ? `${id}-help` : null"
        @change="selectDate"
        @focus="$emit('focus', $event)"
        @blur="$emit('blur', $event)"
        @keydown="onKeydown"
        v-bind="$attrs"
      >
      
      <!-- Clear button -->
      <button
        v-if="clearable && currentDate"
        type="button"
        :class="clearButtonClasses"
        @click="clearDate"
        :disabled="disabled || readonly"
        aria-label="Clear date"
      >
        <svg
          class="h-4 w-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>
    </div>
    
    <!-- Display value (for non-native date picker) -->
    <div 
      v-if="displayValue && !isOpen"
      class="mt-1 text-sm text-primary-600 dark:text-primary-400"
    >
      {{ displayValue }}
    </div>
    
    <!-- Error message -->
    <div 
      v-if="error" 
      class="mt-1 text-sm text-error-600 dark:text-error-400"
      :id="`${id}-error`"
    >
      {{ error }}
    </div>
    
    <!-- Help text -->
    <div 
      v-else-if="help" 
      class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
      :id="`${id}-help`"
    >
      {{ help }}
    </div>
  </div>
</template>
