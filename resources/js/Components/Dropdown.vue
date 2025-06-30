<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
        validator: (value) => ['left', 'right', 'top', 'bottom'].includes(value),
    },
    width: {
        type: [String, Number],
        default: '48',
    },
    contentClasses: {
        type: Array,
        default: () => [],
    },
    variant: {
        type: String,
        default: 'default', // 'default', 'light', 'dark', 'primary', 'success', 'danger', 'warning', 'info'
        validator: (value) => ['default', 'light', 'dark', 'primary', 'success', 'danger', 'warning', 'info'].includes(value),
    },
    showArrow: {
        type: Boolean,
        default: true,
    },
    closeOnClick: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['opened', 'closed', 'click-outside']);

const open = ref(false);
const dropdown = ref(null);

const variantClasses = computed(() => {
    const variants = {
        default: {
            button: 'text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:ring-neutral-300 dark:focus:ring-neutral-600',
            menu: 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700',
            item: 'text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-700/50',
            divider: 'border-neutral-100 dark:border-neutral-700/50',
            text: 'text-neutral-700 dark:text-neutral-200',
        },
        primary: {
            button: 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-300 dark:focus:ring-primary-900/30',
            menu: 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700',
            item: 'text-neutral-700 dark:text-neutral-200 hover:bg-primary-50 dark:hover:bg-primary-900/20',
            divider: 'border-neutral-100 dark:border-neutral-700/50',
            text: 'text-primary-700 dark:text-primary-300',
        },
        secondary: {
            button: 'bg-secondary-600 text-white hover:bg-secondary-700 focus:ring-secondary-300 dark:focus:ring-secondary-900/30',
            menu: 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700',
            item: 'text-neutral-700 dark:text-neutral-200 hover:bg-secondary-50 dark:hover:bg-secondary-900/20',
            divider: 'border-neutral-100 dark:border-neutral-700/50',
            text: 'text-secondary-700 dark:text-secondary-300',
        },
        accent: {
            button: 'bg-accent-600 text-white hover:bg-accent-700 focus:ring-accent-300 dark:focus:ring-accent-900/30',
            menu: 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700',
            item: 'text-neutral-700 dark:text-neutral-200 hover:bg-accent-50 dark:hover:bg-accent-900/20',
            divider: 'border-neutral-100 dark:border-neutral-700/50',
            text: 'text-accent-700 dark:text-accent-300',
        },
        success: {
            button: 'bg-success-600 text-white hover:bg-success-700 focus:ring-success-300 dark:focus:ring-success-900/30',
            menu: 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700',
            item: 'text-neutral-700 dark:text-neutral-200 hover:bg-success-50 dark:hover:bg-success-900/20',
            divider: 'border-neutral-100 dark:border-neutral-700/50',
            text: 'text-success-700 dark:text-success-300',
        },
        warning: {
            button: 'bg-warning-600 text-white hover:bg-warning-700 focus:ring-warning-300 dark:focus:ring-warning-900/30',
            menu: 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700',
            item: 'text-neutral-700 dark:text-neutral-200 hover:bg-warning-50 dark:hover:bg-warning-900/20',
            divider: 'border-neutral-100 dark:border-neutral-700/50',
            text: 'text-warning-700 dark:text-warning-300',
        },
        error: {
            button: 'bg-error-600 text-white hover:bg-error-700 focus:ring-error-300 dark:focus:ring-error-900/30',
            menu: 'bg-white dark:bg-neutral-800 border-error-200 dark:border-error-800/50',
            item: 'text-error-700 dark:text-error-200 hover:bg-error-50 dark:hover:bg-error-900/20',
            divider: 'border-error-100 dark:border-error-800/30',
            text: 'text-error-700 dark:text-error-300',
        },
        info: {
            button: 'bg-info-600 text-white hover:bg-info-700 focus:ring-info-300 dark:focus:ring-info-900/30',
            menu: 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700',
            item: 'text-neutral-700 dark:text-neutral-200 hover:bg-info-50 dark:hover:bg-info-900/20',
            divider: 'border-neutral-100 dark:border-neutral-700/50',
            text: 'text-info-700 dark:text-info-300',
        },
    };
    
    return variants[props.variant] || variants.default;
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        close();
    }
};

const handleClickOutside = (event) => {
    if (dropdown.value && !dropdown.value.contains(event.target)) {
        close();
        emit('click-outside');
    }
};

const toggle = () => {
    if (open.value) {
        close();
    } else {
        open.value = true;
        emit('opened');
        if (props.closeOnClick) {
            document.addEventListener('click', handleClickOutside);
        }
    }
};

const close = () => {
    open.value = false;
    emit('closed');
    document.removeEventListener('click', handleClickOutside);
};

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.removeEventListener('click', handleClickOutside);
});

const widthClass = computed(() => {
    if (typeof props.width === 'number') {
        return `w-${props.width}`;
    }
    return {
        'auto': 'w-auto',
        '48': 'w-48',
        '56': 'w-56',
        '64': 'w-64',
        '72': 'w-72',
        '80': 'w-80',
        '96': 'w-96',
    }[props.width.toString()] || 'w-48';
});

const alignmentClasses = computed(() => {
    const classes = [];
    
    // Horizontal alignment
    if (props.align === 'left') {
        classes.push('left-0 origin-top-left');
    } else if (props.align === 'right') {
        classes.push('right-0 origin-top-right');
    } else {
        classes.push('right-0 origin-top-right');
    }
    
    return classes.join(' ');
});

const menuClasses = computed(() => [
    'absolute z-50 mt-1 w-56 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none',
    'transform-gpu will-change-transform',
    'dark:ring-white/10 dark:ring-1',
    variantClasses.value.menu,
    widthClass.value,
    alignmentClasses.value,
    ...props.contentClasses,
]);
</script>

<template>
    <div class="relative inline-block text-left" ref="dropdown">
        <!-- Trigger Slot -->
        <div @click.stop="toggle">
            <slot name="trigger" :open="open" :close="close" :toggle="toggle">
                <button
                    type="button"
                    class="inline-flex items-center justify-center w-full rounded-md px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 ease-[cubic-bezier(0.25,0.1,0.25,1)]"
                    :class="[
                        variantClasses.button,
                        'focus:ring-offset-white dark:focus:ring-offset-neutral-800',
                        'shadow-sm hover:shadow',
                        'border border-transparent',
                        {
                            'border-neutral-200 dark:border-neutral-700': variant === 'default',
                        }
                    ]"
                >
                    <slot name="button-content">
                        <span :class="variant === 'default' ? 'text-neutral-700 dark:text-neutral-200' : 'text-white'">
                            Options
                        </span>
                    </slot>
                    <svg 
                        v-if="showArrow"
                        class="-mr-1 ml-2 h-4 w-4 transition-transform duration-200 ease-[cubic-bezier(0.25,0.1,0.25,1)]" 
                        xmlns="http://www.w3.org/2000/svg" 
                        viewBox="0 0 20 20" 
                        fill="currentColor"
                        :class="{
                            'transform-gpu rotate-180': open,
                            'text-white': ['primary', 'secondary', 'accent', 'success', 'warning', 'error', 'info'].includes(variant),
                            'text-neutral-700 dark:text-neutral-300': variant === 'default'
                        }"
                        aria-hidden="true"
                    >
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </slot>
        </div>

        <!-- Dropdown Panel -->
        <transition
            enter-active-class="transition-all ease-[cubic-bezier(0.25,0.1,0.25,1)] duration-150"
            enter-from-class="transform-gpu opacity-0 scale-95 -translate-y-2"
            enter-to-class="transform-gpu opacity-100 scale-100 translate-y-0"
            leave-active-class="transition-all ease-[cubic-bezier(0.25,0.1,0.25,1)] duration-100"
            leave-from-class="transform-gpu opacity-100 scale-100 translate-y-0"
            leave-to-class="transform-gpu opacity-0 scale-95 -translate-y-2"
        >
            <div
                v-show="open"
                :class="menuClasses"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="menu-button"
                tabindex="-1"
            >
                <div class="py-1" role="none">
                    <slot :close="close" :variantClasses="variantClasses" />
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
/* Optimize transitions */
:deep(.transition-all) {
    backface-visibility: hidden;
    transform-style: preserve-3d;
}

/* Prevent pointer events during transitions */
:deep(.v-enter-active),
:deep(.v-leave-active) {
    pointer-events: none;
}
</style>
