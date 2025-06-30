<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
        validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'].includes(value),
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    closeOnBackdrop: {
        type: Boolean,
        default: true,
    },
    closeOnEscape: {
        type: Boolean,
        default: true,
    },
    scrollable: {
        type: Boolean,
        default: false,
    },
    fullscreen: {
        type: Boolean,
        default: false,
    },
    centered: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close', 'opened', 'closed']);
const dialog = ref();
const showSlot = ref(props.show);
const isVisible = ref(false);

const openModal = () => {
    document.body.style.overflow = 'hidden';
    showSlot.value = true;
    isVisible.value = true;
    dialog.value?.showModal();
    emit('opened');
};

const closeModal = () => {
    isVisible.value = false;
    document.body.style.overflow = '';
    setTimeout(() => {
        dialog.value?.close();
        showSlot.value = false;
        emit('closed');
    }, 200);
};

// Handle modal visibility and body scroll
watch(() => props.show, (newVal) => {
    if (newVal) {
        openModal();
    } else {
        closeModal();
    }
}, { immediate: true });

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

// Handle keyboard events
const handleKeydown = (e) => {
    if (e.key === 'Escape' && props.closeOnEscape) {
        e.preventDefault();
        if (props.show) {
            close();
        }
    }
};

// Handle click outside
const handleBackdropClick = (e) => {
    if (props.closeOnBackdrop && e.target === dialog.value) {
        close();
    }
};

// Lifecycle hooks
onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
    if (props.show) {
        openModal();
    }
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
});

// Computed classes
const maxWidthClass = computed(() => ({
    'sm': 'sm:max-w-sm',
    'md': 'sm:max-w-md',
    'lg': 'sm:max-w-lg',
    'xl': 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
    '3xl': 'sm:max-w-3xl',
    '4xl': 'sm:max-w-4xl',
    '5xl': 'sm:max-w-5xl',
    '6xl': 'sm:max-w-6xl',
    '7xl': 'sm:max-w-7xl',
    'full': 'sm:max-w-full',
}[props.maxWidth] || 'sm:max-w-2xl'));

const modalClasses = computed(() => [
    'relative bg-white dark:bg-neutral-800 rounded-xl shadow-2xl transform transition-all',
    'w-full mx-auto flex flex-col overflow-hidden',
    {
        'h-auto max-h-[90vh]': !props.fullscreen,
        'h-[95vh]': props.fullscreen,
        'my-8': !props.fullscreen,
        'm-0': props.fullscreen,
        'border border-neutral-200 dark:border-neutral-700': !props.fullscreen,
        'border-0': props.fullscreen,
    },
]);

const backdropClasses = computed(() => [
    'fixed inset-0 bg-neutral-900/50 dark:bg-neutral-900/70 backdrop-blur-sm',
    'transition-opacity duration-300',
    {
        'opacity-100': isVisible.value,
        'opacity-0': !isVisible.value,
    },
]);

const contentClasses = computed(() => [
    'relative z-10 transform transition-all duration-300',
    {
        'opacity-100 translate-y-0': isVisible.value,
        'opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95': !isVisible.value,
        'flex items-center min-h-full': props.centered,
        'h-full': props.fullscreen,
    },
]);
</script>

<template>
    <dialog 
        ref="dialog"
        class="fixed inset-0 z-50 m-0 min-h-full min-w-full overflow-y-auto bg-transparent p-0 backdrop:bg-transparent"
        :class="{ 'pointer-events-none': !show }"
        @click="handleBackdropClick"
    >
        <!-- Backdrop -->
        <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" :class="backdropClasses" aria-hidden="true" />
        </transition>

        <!-- Modal Content -->
        <div 
            class="fixed inset-0 z-50 flex min-h-full items-center justify-center p-4 text-left sm:p-6"
            role="dialog" 
            aria-modal="true"
            :class="{ 'items-start pt-16': !centered }"
        >
            <transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div 
                    v-show="show" 
                    :class="[modalClasses, maxWidthClass]"
                    role="document"
                >
                    <slot v-if="showSlot" />
                </div>
            </transition>
        </div>
    </dialog>
</template>

<style scoped>
dialog {
    /* Reset default dialog styles */
    border: none;
    padding: 0;
    background: transparent;
}

dialog::backdrop {
    background: transparent;
}

/* Smooth scrolling for modal content */
.overflow-y-auto {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 4px;
    border: 2px solid transparent;
    background-clip: padding-box;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: rgba(107, 114, 128, 0.7);
}

/* Dark mode scrollbar */
.dark .overflow-y-auto {
    scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: rgba(75, 85, 99, 0.5);
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: rgba(107, 114, 128, 0.7);
}
</style>
