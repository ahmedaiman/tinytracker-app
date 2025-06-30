<script setup>
import { computed } from 'vue';

const props = defineProps({
    on: {
        type: Boolean,
        default: true,
    },
    type: {
        type: String,
        default: 'success', // 'success', 'info', 'warning', 'error'
        validator: (value) => ['success', 'info', 'warning', 'error'].includes(value),
    },
    dismissible: {
        type: Boolean,
        default: false,
    },
    timeout: {
        type: Number,
        default: 0, // Auto-dismiss after milliseconds (0 = no auto-dismiss)
    },
});

const emit = defineEmits(['dismiss']);

// Auto-dismiss if timeout is set
if (props.timeout > 0) {
    setTimeout(() => {
        emit('dismiss');
    }, props.timeout);
}

const typeClasses = computed(() => ({
    success: {
        bg: 'bg-success-50 dark:bg-success-900/20',
        text: 'text-success-800 dark:text-success-100',
        border: 'border-success-200 dark:border-success-800/50',
        icon: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
        iconColor: 'text-success-500 dark:text-success-400',
        button: 'text-success-600 hover:bg-success-100 dark:hover:bg-success-800/30',
    },
    info: {
        bg: 'bg-primary-50 dark:bg-primary-900/20',
        text: 'text-primary-800 dark:text-primary-100',
        border: 'border-primary-200 dark:border-primary-800/50',
        icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        iconColor: 'text-primary-500 dark:text-primary-400',
        button: 'text-primary-600 hover:bg-primary-100 dark:hover:bg-primary-800/30',
    },
    warning: {
        bg: 'bg-warning-50 dark:bg-warning-900/20',
        text: 'text-warning-800 dark:text-warning-100',
        border: 'border-warning-200 dark:border-warning-800/50',
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        iconColor: 'text-warning-500 dark:text-warning-400',
        button: 'text-warning-600 hover:bg-warning-100 dark:hover:bg-warning-800/30',
    },
    error: {
        bg: 'bg-error-50 dark:bg-error-900/20',
        text: 'text-error-800 dark:text-error-100',
        border: 'border-error-200 dark:border-error-800/50',
        icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        iconColor: 'text-error-500 dark:text-error-400',
        button: 'text-error-600 hover:bg-error-100 dark:hover:bg-error-800/30',
    },
}));

const currentType = computed(() => typeClasses.value[props.type] || typeClasses.value.success);

const handleDismiss = () => {
    emit('dismiss');
};
</script>

<template>
    <div class="w-full">
        <transition
            enter-active-class="transform transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-2 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-2 scale-95"
        >
            <div
                v-show="on"
                :class="[
                    'relative flex items-start p-4 rounded-xl shadow-md transition-all duration-200',
                    'backdrop-blur-sm border',
                    currentType.bg,
                    currentType.border,
                    currentType.text,
                ]"
                role="alert"
                aria-live="polite"
                aria-atomic="true"
            >
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <svg
                        class="w-6 h-6 mt-0.5"
                        :class="currentType.iconColor"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            :d="currentType.icon"
                        />
                    </svg>
                </div>

                <!-- Message -->
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium leading-relaxed">
                        <slot />
                    </p>
                </div>

                <!-- Dismiss button -->
                <div v-if="dismissible" class="ml-4 flex-shrink-0 -mt-1 -mr-2">
                    <button
                        type="button"
                        :class="[
                            'inline-flex items-center justify-center rounded-full p-1.5',
                            'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-transparent',
                            'transition-colors duration-200',
                            currentType.button,
                        ]"
                        @click="handleDismiss"
                        aria-label="Dismiss message"
                    >
                        <span class="sr-only">Dismiss</span>
                        <svg 
                            class="w-4 h-4" 
                            viewBox="0 0 20 20" 
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>
