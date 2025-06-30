<script setup>
import Modal from './Modal.vue';

const emit = defineEmits(['close']);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="bg-white dark:bg-neutral-800 px-6 py-5 rounded-t-lg">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto shrink-0 flex items-center justify-center size-12 rounded-full bg-primary-50 dark:bg-primary-900/30 sm:mx-0 sm:size-10 transition-colors duration-200">
                    <svg class="size-6 text-primary-600 dark:text-primary-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>

                <div class="mt-3 text-center sm:mt-0 sm:ms-4 sm:text-start">
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">
                        <slot name="title" />
                    </h3>

                    <div class="mt-2 text-sm text-neutral-700 dark:text-neutral-300">
                        <slot name="content" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-row justify-end gap-3 px-6 py-4 bg-neutral-50 dark:bg-neutral-700/10 border-t border-neutral-100 dark:border-neutral-700/50 text-end rounded-b-lg">
            <slot name="footer" />
        </div>
    </Modal>
</template>
