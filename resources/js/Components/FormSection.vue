<script setup>
import { computed, useSlots } from 'vue';
import SectionTitle from './SectionTitle.vue';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    /**
     * Form section variant
     * @values 'default', 'card', 'bordered'
     */
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'card', 'bordered'].includes(value),
    },
    /**
     * Whether to show a top border (only applies to 'default' variant)
     */
    border: {
        type: Boolean,
        default: true,
    },
    /**
     * Background color variant
     * @values 'white', 'lemon_chiffon', 'light_blue', 'peach', 'celadon', 'gray'
     */
    bg: {
        type: String,
        default: 'lemon_chiffon',
        validator: (value) => ['white', 'lemon_chiffon', 'light_blue', 'peach', 'celadon', 'gray'].includes(value),
    },
    /**
     * Whether the form section should be full width
     */
    fullWidth: {
        type: Boolean,
        default: false,
    },
    /**
     * Whether to show a shadow
     */
    shadow: {
        type: Boolean,
        default: true,
    },
    /**
     * Whether to show the form actions section even if no actions slot is provided
     */
    alwaysShowActions: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['submitted']);

const slots = useSlots();
const hasActions = computed(() => props.alwaysShowActions || !!slots.actions);

const formClasses = computed(() => ({
    'space-y-6': true,
    'max-w-3xl mx-auto': !props.fullWidth,
}));

const sectionClasses = computed(() => {
    const base = [
        'rounded-lg transition-all duration-200',
        props.shadow ? 'shadow-sm' : 'shadow-none',
    ];

    // Background colors
    const bgColors = {
        white: 'bg-white',
        lemon_chiffon: 'bg-lemon_chiffon-50',
        light_blue: 'bg-light_blue-50',
        peach: 'bg-peach-50',
        celadon: 'bg-celadon-50',
        gray: 'bg-gray-50',
    };

    // Border colors
    const borderColors = {
        white: 'border-gray-200',
        lemon_chiffon: 'border-lemon_chiffon-200',
        light_blue: 'border-light_blue-200',
        peach: 'border-peach-200',
        celadon: 'border-celadon-200',
        gray: 'border-gray-200',
    };

    base.push(bgColors[props.bg] || 'bg-white');
    base.push(props.variant === 'bordered' ? `border ${borderColors[props.bg] || 'border-gray-200'}` : 'border-0');

    if (props.variant === 'card') {
        base.push('p-6');
    } else if (props.variant === 'bordered') {
        base.push('p-6');
    } else {
        base.push('py-6');
        if (props.border) {
            base.push('border-t border-gray-200');
        }
    }

    return base;
});

const actionsClasses = computed(() => {
    const base = [
        'flex items-center justify-end space-x-3',
        'px-6 py-4',
        'rounded-b-lg',
        'transition-colors duration-200',
    ];

    // Background colors - slightly darker than the form background
    const bgColors = {
        white: 'bg-gray-50',
        lemon_chiffon: 'bg-lemon_chiffon-100',
        light_blue: 'bg-light_blue-100',
        peach: 'bg-peach-100',
        celadon: 'bg-celadon-100',
        gray: 'bg-gray-100',
    };

    base.push(bgColors[props.bg] || 'bg-gray-50');
    
    return base;
});
</script>

<template>
    <div class="form-section">
        <form 
            class="space-y-6" 
            :class="formClasses"
            @submit.prevent="$emit('submitted')"
            v-bind="$attrs"
        >
            <div 
                v-if="$slots.title || $slots.description"
                class="mb-6"
            >
                <SectionTitle>
                    <template v-if="$slots.title" #title>
                        <slot name="title" />
                    </template>
                    <template v-if="$slots.description" #description>
                        <slot name="description" />
                    </template>
                </SectionTitle>
            </div>

            <div :class="sectionClasses">
                <div class="space-y-6">
                    <slot name="form" />
                </div>

                <div 
                    v-if="hasActions" 
                    :class="actionsClasses"
                >
                    <slot name="actions" />
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>
.form-section :deep(.form-actions) {
    @apply flex items-center justify-end space-x-3;
}

/* Smooth transitions for form elements */
.form-section :deep(input[type="text"]),
.form-section :deep(input[type="email"]),
.form-section :deep(input[type="password"]),
.form-section :deep(input[type="number"]),
.form-section :deep(input[type="tel"]),
.form-section :deep(input[type="url"]),
.form-section :deep(input[type="date"]),
.form-section :deep(input[type="datetime-local"]),
.form-section :deep(input[type="month"]),
.form-section :deep(input[type="week"]),
.form-section :deep(input[type="time"]),
.form-section :deep(select),
.form-section :deep(textarea) {
    @apply transition-all duration-200 focus:ring-2 focus:ring-offset-1 focus:ring-primary-200 dark:focus:ring-primary-400;
}

/* Style form labels */
.form-section :deep(label) {
    @apply block text-sm font-medium text-gray-700 mb-1;
}

/* Style form help text */
.form-section :deep(.help-text) {
    @apply mt-1 text-sm text-gray-500;
}

/* Style form errors */
.form-section :deep(.error-message) {
    @apply mt-1 text-sm text-red-600;
}
</style>
