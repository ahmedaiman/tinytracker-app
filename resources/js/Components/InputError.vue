<script setup>
import { computed } from 'vue';

const props = defineProps({
    message: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'error', // 'error', 'warning', 'info', 'success'
        validator: (value) => ['error', 'warning', 'info', 'success'].includes(value),
    },
    showIcon: {
        type: Boolean,
        default: true,
    },
    dismissible: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['dismiss']);

const typeClasses = computed(() => ({
    error: {
        bg: 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 dark:border-red-400',
        text: 'text-red-800 dark:text-red-100',
        icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        iconColor: 'text-red-500 dark:text-red-400',
        ring: 'focus:ring-red-200 dark:focus:ring-red-800',
    },
    warning: {
        bg: 'bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-300',
        text: 'text-yellow-800 dark:text-yellow-100',
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        iconColor: 'text-yellow-500 dark:text-yellow-300',
        ring: 'focus:ring-yellow-200 dark:focus:ring-yellow-800',
    },
    info: {
        bg: 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-300',
        text: 'text-blue-800 dark:text-blue-100',
        icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        iconColor: 'text-blue-500 dark:text-blue-300',
        ring: 'focus:ring-blue-200 dark:focus:ring-blue-800',
    },
    success: {
        bg: 'bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 dark:border-green-300',
        text: 'text-green-800 dark:text-green-100',
        icon: 'M5 13l4 4L19 7',
        iconColor: 'text-green-500 dark:text-green-300',
        ring: 'focus:ring-green-200 dark:focus:ring-green-800',
    },
}));

const currentType = computed(() => typeClasses.value[props.type] || typeClasses.value.error);

const handleDismiss = () => {
    emit('dismiss');
};
</script>

<style scoped>
/* Animation for error message */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-4px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animation for dismiss button */
button {
    transition: all 0.2s ease-in-out;
}

button:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

/* Dark mode transitions */
.dark .transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>

<template>
    <Transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
    >
        <div 
            v-if="message" 
            :class="[
                'mt-1.5 flex items-start rounded-lg p-3.5 text-sm shadow-sm transition-all duration-200',
                currentType.bg,
                currentType.text,
                dismissible ? 'pr-10' : '',
            ]"
            role="alert"
            :aria-live="message ? 'assertive' : 'off'"
        >
            <!-- Icon -->
            <svg 
                v-if="showIcon"
                :class="['h-5 w-5 flex-shrink-0 mr-3 mt-0.5', currentType.iconColor]" 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
                aria-hidden="true"
            >
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="1.5"
                    :d="currentType.icon"
                />
            </svg>
            
            <!-- Message -->
            <div class="flex-1 leading-normal">
                <p class="leading-relaxed">
                    {{ message }}
                </p>
            </div>
            
            <!-- Dismiss Button -->
            <button
                v-if="dismissible"
                type="button"
                :class="['ml-3 -mr-1.5 flex-shrink-0 p-1 rounded-full inline-flex items-center justify-center hover:bg-opacity-20 hover:bg-current transition-colors duration-150 focus:outline-none', currentType.iconColor, currentType.ring]"
                @click="handleDismiss"
                aria-label="Dismiss"
            >
                <span class="sr-only">Dismiss</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </Transition>
</template>
