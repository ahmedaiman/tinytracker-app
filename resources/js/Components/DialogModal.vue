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
        <div class="flex flex-col flex-1 min-h-0">
            <!-- Header -->
            <div class="shrink-0 px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <slot name="title" />
            </div>

            <!-- Content -->
            <div class="flex-auto overflow-y-auto px-6 py-4">
                <slot name="content" />
            </div>

            <!-- Footer -->
            <div class="shrink-0 flex justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <slot name="footer" />
            </div>
        </div>
    </Modal>
</template>
