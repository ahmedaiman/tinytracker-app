<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        default: '#',
    },
    active: {
        type: Boolean,
        default: false,
    },
    exact: {
        type: Boolean,
        default: false,
    },
    as: {
        type: String,
        default: 'link',
        validator: (value) => ['link', 'button', 'a'].includes(value),
    },
    icon: {
        type: [String, Object],
        default: null,
    },
    iconRight: {
        type: Boolean,
        default: false,
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => [
            'default', 'primary', 'secondary', 'accent', 
            'success', 'danger', 'warning', 'info', 'light', 'dark'
        ].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    fullWidth: {
        type: Boolean,
        default: true,
    },
    noStyle: {
        type: Boolean,
        default: false,
    },
    rounded: {
        type: [Boolean, String],
        default: 'md',
        validator: (value) => {
            if (typeof value === 'boolean') return true;
            return ['none', 'sm', 'md', 'lg', 'full'].includes(value);
        },
    },
    activeClass: {
        type: String,
        default: '',
    },
    inactiveClass: {
        type: String,
        default: '',
    },
    external: {
        type: Boolean,
        default: false,
    },
    showExternalIcon: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['click', 'focus', 'blur', 'keydown']);

const currentPath = ref(window.location.pathname);

const updateCurrentPath = () => {
    currentPath.value = window.location.pathname;
};

onMounted(() => {
    window.addEventListener('popstate', updateCurrentPath);    
    window.addEventListener('pushstate', updateCurrentPath);
    window.addEventListener('replacestate', updateCurrentPath);
});

onUnmounted(() => {
    window.removeEventListener('popstate', updateCurrentPath);
    window.removeEventListener('pushstate', updateCurrentPath);
    window.removeEventListener('replacestate', updateCurrentPath);
});

const isActive = computed(() => {
    if (props.active) return true;
    
    if (props.exact) {
        return currentPath.value === props.href;
    }
    return currentPath.value.startsWith(props.href);
});

const variantClasses = computed(() => {
    if (props.noStyle) return '';
    
    const base = 'inline-flex items-center transition-all duration-200 focus:outline-none';
    const widthClass = props.fullWidth ? 'w-full' : 'w-auto';
    
    const sizeClasses = {
        xs: 'px-2.5 py-1 text-xs',
        sm: 'px-3 py-1.5 text-sm',
        md: 'px-4 py-2 text-sm',
        lg: 'px-5 py-2.5 text-base',
        xl: 'px-6 py-3 text-lg',
    };
    
    const roundedClasses = {
        none: 'rounded-none',
        sm: 'rounded',
        md: 'rounded-lg',
        lg: 'rounded-xl',
        full: 'rounded-full',
    };
    
    const roundedClass = typeof props.rounded === 'boolean' 
        ? (props.rounded ? 'rounded-full' : 'rounded-none')
        : roundedClasses[props.rounded] || 'rounded-lg';
    
    const variants = {
        default: {
            active: 'bg-neutral-100 text-neutral-900 dark:bg-neutral-700 dark:text-white',
            inactive: 'text-neutral-700 hover:bg-neutral-50 dark:text-neutral-300 dark:hover:bg-neutral-800/80',
            focus: 'focus:ring-2 focus:ring-offset-1 focus:ring-neutral-200 dark:focus:ring-neutral-600',
        },
        primary: {
            active: 'bg-primary-100 text-primary-900 dark:bg-primary-900/30 dark:text-primary-100',
            inactive: 'text-primary-700 hover:bg-primary-50 dark:text-primary-400 dark:hover:bg-primary-900/20',
            focus: 'focus:ring-2 focus:ring-offset-1 focus:ring-primary-200 dark:focus:ring-primary-800',
        },
        secondary: {
            active: 'bg-secondary-100 text-secondary-900 dark:bg-secondary-900/30 dark:text-secondary-100',
            inactive: 'text-secondary-700 hover:bg-secondary-50 dark:text-secondary-400 dark:hover:bg-secondary-900/20',
            focus: 'focus:ring-2 focus:ring-offset-1 focus:ring-secondary-200 dark:focus:ring-secondary-800',
        },
        accent: {
            active: 'bg-accent-100 text-accent-900 dark:bg-accent-900/30 dark:text-accent-100',
            inactive: 'text-accent-700 hover:bg-accent-50 dark:text-accent-400 dark:hover:bg-accent-900/20',
            focus: 'focus:ring-2 focus:ring-offset-1 focus:ring-accent-200 dark:focus:ring-accent-800',
        },
        success: {
            active: 'bg-success-100 text-success-900 dark:bg-success-900/30 dark:text-success-100',
            inactive: 'text-success-700 hover:bg-success-50 dark:text-success-400 dark:hover:bg-success-900/20',
            focus: 'focus:ring-2 focus:ring-offset-1 focus:ring-success-200 dark:focus:ring-success-800',
        },
        danger: {
            active: 'bg-danger-100 text-danger-900 dark:bg-danger-900/30 dark:text-danger-100',
            inactive: 'text-danger-700 hover:bg-danger-50 dark:text-danger-400 dark:hover:bg-danger-900/20',
            focus: 'focus:ring-2 focus:ring-offset-1 focus:ring-danger-200 dark:focus:ring-danger-800',
        },
        warning: {
            active: 'bg-warning-100 text-warning-900 dark:bg-warning-900/30 dark:text-warning-100',
            inactive: 'text-warning-700 hover:bg-warning-50 dark:text-warning-400 dark:hover:bg-warning-900/20',
            focus: 'focus:ring-2 focus:ring-offset-1 focus:ring-warning-200 dark:focus:ring-warning-800',
        },
        info: {
            active: 'bg-info-100 text-info-900 dark:bg-info-900/30 dark:text-info-100',
            inactive: 'text-info-700 hover:bg-info-50 dark:text-info-400 dark:hover:bg-info-900/20',
            focus: 'focus:ring-2 focus:ring-offset-1 focus:ring-info-200 dark:focus:ring-info-800',
        },
        light: {
            active: 'bg-neutral-100 text-neutral-900 dark:bg-neutral-700 dark:text-white',
            inactive: 'text-neutral-600 hover:bg-neutral-50 dark:text-neutral-300 dark:hover:bg-neutral-700',
            focus: 'focus:ring-2 focus:ring-offset-1 focus:ring-neutral-200 dark:focus:ring-neutral-600',
        },
        dark: {
            active: 'bg-neutral-800 text-white dark:bg-neutral-200 dark:text-neutral-900',
            inactive: 'text-neutral-900 hover:bg-neutral-200 dark:text-neutral-100 dark:hover:bg-neutral-700',
            focus: 'focus:ring-2 focus:ring-offset-1 focus:ring-neutral-300 dark:focus:ring-neutral-500',
        },
    };
    
    const variant = variants[props.variant] || variants.default;
    const stateClass = isActive.value 
        ? `${variant.active} ${props.activeClass}` 
        : `${variant.inactive} ${props.inactiveClass}`;
        
    const disabledClass = props.disabled 
        ? 'opacity-50 cursor-not-allowed' 
        : 'cursor-pointer';
    
    const iconSpacing = props.icon 
        ? (props.iconRight ? 'justify-between' : 'space-x-2')
        : '';
    
    return [
        base,
        widthClass,
        sizeClasses[props.size],
        roundedClass,
        stateClass,
        variant.focus,
        disabledClass,
        iconSpacing,
    ];
});

const iconSizeClasses = computed(() => ({
    xs: 'h-3 w-3',
    sm: 'h-3.5 w-3.5',
    md: 'h-4 w-4',
    lg: 'h-5 w-5',
    xl: 'h-6 w-6',
}));

const handleClick = (event) => {
    if (props.disabled) {
        event.preventDefault();
        return false;
    }
    emit('click', event);
    return true;
};

const handleFocus = (event) => {
    if (props.disabled) return;
    emit('focus', event);
};

const handleBlur = (event) => {
    if (props.disabled) return;
    emit('blur', event);
};

const handleKeydown = (event) => {
    if (props.disabled) return;
    emit('keydown', event);
};

const isExternalUrl = computed(() => {
    if (typeof props.href !== 'string') return false;
    return props.href.startsWith('http') || props.href.startsWith('//');
});

const showExternalIndicator = computed(() => {
    return props.showExternalIcon && props.external && !props.iconRight;
});
</script>

<template>
    <component
        :is="as === 'link' ? Link : as"
        :href="!disabled && as !== 'button' ? href : undefined"
        :type="as === 'button' ? 'button' : undefined"
        :disabled="disabled"
        :class="[
            'font-medium text-start',
            'relative group',
            variantClasses,
            {
                'opacity-60': disabled,
                'transition-colors': !noStyle,
            },
        ]"
        :aria-current="isActive ? 'page' : undefined"
        :aria-disabled="disabled"
        :tabindex="disabled ? -1 : 0"
        :target="external ? '_blank' : undefined"
        :rel="external ? 'noopener noreferrer' : undefined"
        @click="handleClick"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown.enter.space.prevent.self="handleKeydown"
        @keyup.enter.space="(e) => { if (e.target === $el) e.target.click() }"
    >
        <span class="flex items-center">
            <!-- Left Icon -->
            <template v-if="icon && !iconRight">
                <component
                    :is="typeof icon === 'object' ? icon : 'span'"
                    v-if="typeof icon === 'object'"
                    :class="[
                        'flex-shrink-0',
                        iconSizeClasses[size],
                        {
                            'opacity-100': isActive,
                            'opacity-70 group-hover:opacity-100': !isActive,
                            'text-current': !noStyle,
                        },
                        icon.class,
                    ]"
                    aria-hidden="true"
                />
                <svg
                    v-else
                    :class="[
                        'flex-shrink-0',
                        iconSizeClasses[size],
                        'text-current',
                        {
                            'opacity-100': isActive,
                            'opacity-70 group-hover:opacity-100': !isActive,
                        },
                    ]"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path :d="icon" />
                </svg>
            </template>

            <!-- Label -->
            <span class="truncate">
                <slot />
            </span>

            <!-- Right Icon -->
            <template v-if="(icon && iconRight) || showExternalIndicator">
                <component
                    v-if="icon && iconRight && typeof icon === 'object'"
                    :is="icon"
                    :class="[
                        'flex-shrink-0',
                        iconSizeClasses[size],
                        'ml-1',
                        {
                            'opacity-100': isActive,
                            'opacity-70 group-hover:opacity-100': !isActive,
                            'text-current': !noStyle,
                        },
                        icon.class,
                    ]"
                    aria-hidden="true"
                />
                <svg
                    v-else-if="icon && iconRight"
                    :class="[
                        'flex-shrink-0',
                        iconSizeClasses[size],
                        'ml-1',
                        'text-current',
                        {
                            'opacity-100': isActive,
                            'opacity-70 group-hover:opacity-100': !isActive,
                        },
                    ]"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path :d="icon" />
                </svg>

                <!-- External Link Indicator -->
                <svg
                    v-if="showExternalIndicator"
                    :class="[
                        'flex-shrink-0',
                        iconSizeClasses[size],
                        'ml-1',
                        'opacity-50 group-hover:opacity-70',
                        'text-current',
                    ]"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                    <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 112 0v3a4 4 0 01-4 4H5a4 4 0 01-4-4V7a4 4 0 014-4h3a1 1 0 010 2H5z" />
                </svg>
            </template>
        </span>
        
        <!-- Active Indicator -->
        <span 
            v-if="isActive && !noStyle"
            class="absolute inset-x-0 bottom-0 h-0.5 bg-current opacity-20"
            aria-hidden="true"
        ></span>
    </component>
</template>

<style scoped>
/* Smooth transitions for interactive states */
.component {
    transition-property: color, background-color, border-color, opacity, box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Focus styles for keyboard navigation */
:focus-visible {
    outline: 2px solid currentColor;
    outline-offset: 2px;
    border-radius: 0.25rem;
}

/* Dark mode transitions */
@media (prefers-color-scheme: dark) {
    .dark-transition {
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }
}

/* Disabled state */
[disabled] {
    pointer-events: none;
}

/* External link indicator animation */
group-hover .external-icon {
    transition: transform 0.2s ease-in-out;
}

group:hover .external-icon {
    transform: translate(1px, -1px);
}
</style>
