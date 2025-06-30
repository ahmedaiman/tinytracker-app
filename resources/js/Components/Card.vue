<script setup>
import { computed, useSlots } from 'vue';
import SectionTitle from './SectionTitle.vue';
import PrimaryButton from './PrimaryButton.vue';

const emit = defineEmits(['header-action-click']);

const props = defineProps({
    /**
     * Card title
     */
    title: {
        type: String,
        default: ''
    },
    /**
     * Card description
     */
    description: {
        type: String,
        default: ''
    },
    /**
     * Whether to show a border around the card
     */
    bordered: {
        type: Boolean,
        default: true
    },
    /**
     * Whether to show a shadow around the card
     */
    shadow: {
        type: Boolean,
        default: true
    },
    /**
     * Whether to show a subtle background
     */
    background: {
        type: Boolean,
        default: true
    },
    /**
     * Whether to add padding to the card
     */
    padded: {
        type: Boolean,
        default: true
    },
    /**
     * Custom class to apply to the card
     */
    class: {
        type: String,
        default: ''
    },
    /**
     * Whether to show a header section
     */
    showHeader: {
        type: Boolean,
        default: true
    },
    /**
     * Whether to show a footer section
     */
    showFooter: {
        type: Boolean,
        default: false
    },
    /**
     * Header action component (e.g., a button or link)
     */
    headerAction: {
        type: Object,
        default: null
    }
});

const cardClasses = computed(() => [
    'rounded-2xl transition-colors duration-200',
    {
        'border border-neutral-100 dark:border-neutral-700': props.bordered,
        'shadow-sm': props.shadow,
        'bg-white/80 dark:bg-neutral-800/80': props.background,
        'backdrop-blur-sm': props.background,
        'p-6': props.padded,
    },
    props.class
]);

const hasHeaderSlot = useSlots().header || (props.title || props.description);
const hasFooterSlot = useSlots().footer;
</script>

<template>
    <div :class="cardClasses">
        <!-- Header -->
        <div
            v-if="showHeader && hasHeaderSlot"
            class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-700"
        >
            <SectionTitle
                v-if="$slots.title || title || $slots.description || description"
                :title="$slots.title ? undefined : title"
                :description="$slots.description ? undefined : description"
                variant="h3"
                class="mb-0"
            >
                <template v-if="$slots.title" #title>
                    <slot name="title" />
                </template>
                <template v-if="$slots.description" #description>
                    <slot name="description" />
                </template>
            </SectionTitle>

            <slot v-else name="header" />

            <div v-if="headerAction || $slots.headerAction" class="flex-shrink-0">
                <slot name="headerAction">
                    <PrimaryButton
                        v-if="headerAction"
                        v-bind="headerAction"
                        @click="emit('header-action-click', $event)"
                    />
                </slot>
            </div>
        </div>

        <!-- Content -->
        <div class="card-content px-6 py-4">
            <slot />
        </div>

        <!-- Footer -->
        <div
            v-if="showFooter && hasFooterSlot"
            class="mt-6 pt-6 border-t border-neutral-100 dark:border-neutral-700"
        >
            <slot name="footer" />
        </div>
    </div>
</template>
