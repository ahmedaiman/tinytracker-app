<script setup>
import { computed } from 'vue';

const props = defineProps({
    /**
     * Color variant for the border
     * @values 'default', 'primary', 'secondary', 'accent', 'success', 'danger', 'warning', 'info', 'light', 'dark'
     */
    variant: {
        type: String,
        default: 'default',
        validator: (value) =>
            ['default', 'primary', 'secondary', 'accent', 'success', 'danger', 'warning', 'info', 'light', 'dark'].includes(value)
    },
    /**
     * Border style
     * @values 'solid', 'dashed', 'dotted', 'double', 'hidden', 'none'
     */
    borderStyle: {
        type: String,
        default: 'solid',
        validator: (value) => ['solid', 'dashed', 'dotted', 'double', 'hidden', 'none'].includes(value)
    },
    /**
     * Border width in pixels (1-8)
     */
    width: {
        type: [String, Number],
        default: 1,
        validator: (value) => {
            const num = parseInt(value);
            return !isNaN(num) && num > 0 && num <= 8;
        }
    },
    /**
     * Vertical padding in rem units (0-12)
     */
    paddingY: {
        type: [String, Number],
        default: 2,
        validator: (value) => {
            const num = parseFloat(value);
            return !isNaN(num) && num >= 0 && num <= 12;
        }
    },
    /**
     * Horizontal padding in rem units (0-12)
     */
    paddingX: {
        type: [String, Number],
        default: 0,
        validator: (value) => {
            const num = parseFloat(value);
            return !isNaN(num) && num >= 0 && num <= 12;
        }
    },
    /**
     * Whether to show the border on mobile
     */
    showOnMobile: {
        type: Boolean,
        default: false
    },
    /**
     * Border position
     * @values 'top', 'bottom', 'both'
     */
    position: {
        type: String,
        default: 'top',
        validator: (value) => ['top', 'bottom', 'both'].includes(value)
    },
    /**
     * Border opacity (0-100)
     */
    opacity: {
        type: [String, Number],
        default: 100,
        validator: (value) => {
            const num = parseInt(value);
            return !isNaN(num) && num >= 0 && num <= 100;
        }
    },
    /**
     * Custom class to apply to the border
     */
    customClass: {
        type: String,
        default: ''
    },
    /**
     * ARIA label for accessibility
     */
    ariaLabel: {
        type: String,
        default: 'Section divider'
    },
    /**
     * Whether to animate the border on hover
     */
    animateOnHover: {
        type: Boolean,
        default: false
    },
    /**
     * Gradient direction (for gradient variant)
     * @values 'to-r', 'to-l', 'to-b', 'to-t', 'to-br', 'to-bl', 'to-tr', 'to-tl'
     */
    gradientDirection: {
        type: String,
        default: 'to-r',
        validator: (value) =>
            ['to-r', 'to-l', 'to-b', 'to-t', 'to-br', 'to-bl', 'to-tr', 'to-tl'].includes(value)
    },
    /**
     * Gradient colors (array of 2-3 colors)
     */
    gradientColors: {
        type: Array,
        default: null,
        validator: (value) => {
            if (!Array.isArray(value)) return false;
            return value.length >= 2 && value.length <= 3;
        }
    }
});

// Border color classes with dark mode support
const borderColorClasses = computed(() => {
    const colors = {
        default: 'border-neutral-200 dark:border-neutral-700',
        primary: 'border-primary-500 dark:border-primary-400',
        secondary: 'border-secondary-500 dark:border-secondary-400',
        accent: 'border-accent-500 dark:border-accent-400',
        success: 'border-success-500 dark:border-success-400',
        danger: 'border-danger-500 dark:border-danger-400',
        warning: 'border-warning-500 dark:border-warning-400',
        info: 'border-info-500 dark:border-info-400',
        light: 'border-neutral-100 dark:border-neutral-600',
        dark: 'border-neutral-800 dark:border-neutral-300',
    };

    return colors[props.variant] || colors.default;
});

// Border style classes
const borderStyleClasses = {
    solid: 'border-solid',
    dashed: 'border-dashed',
    dotted: 'border-dotted',
    double: 'border-double',
    hidden: 'border-hidden',
    none: 'border-none'
};

// Border position classes
const borderPositionClasses = computed(() => {
    const base = [];

    if (props.position === 'top' || props.position === 'both') {
        base.push('border-t');
    }

    if (props.position === 'bottom' || props.position === 'both') {
        base.push('border-b');
    }

    return base.join(' ');
});

// Border width class
const borderWidthClass = computed(() => {
    if (props.position === 'both') {
        return {
            'border-t-1 border-b-1': props.width == 1,
            'border-t-2 border-b-2': props.width == 2,
            'border-t-3 border-b-3': props.width == 3,
            'border-t-4 border-b-4': props.width == 4,
            'border-t-[5px] border-b-[5px]': props.width == 5,
            'border-t-[6px] border-b-[6px]': props.width == 6,
            'border-t-[7px] border-b-[7px]': props.width == 7,
            'border-t-8 border-b-8': props.width == 8,
        };
    }

    return {
        'border-t-1': props.width == 1 && props.position === 'top',
        'border-t-2': props.width == 2 && props.position === 'top',
        'border-t-3': props.width == 3 && props.position === 'top',
        'border-t-4': props.width == 4 && props.position === 'top',
        'border-t-[5px]': props.width == 5 && props.position === 'top',
        'border-t-[6px]': props.width == 6 && props.position === 'top',
        'border-t-[7px]': props.width == 7 && props.position === 'top',
        'border-t-8': props.width == 8 && props.position === 'top',
        'border-b-1': props.width == 1 && props.position === 'bottom',
        'border-b-2': props.width == 2 && props.position === 'bottom',
        'border-b-3': props.width == 3 && props.position === 'bottom',
        'border-b-4': props.width == 4 && props.position === 'bottom',
        'border-b-[5px]': props.width == 5 && props.position === 'bottom',
        'border-b-[6px]': props.width == 6 && props.position === 'bottom',
        'border-b-[7px]': props.width == 7 && props.position === 'bottom',
        'border-b-8': props.width == 8 && props.position === 'bottom',
    };
});

// Padding classes
const paddingClasses = computed(() => ({
    // Y-axis padding
    'py-0': props.paddingY == 0,
    'py-0.5': props.paddingY == 0.5,
    'py-1': props.paddingY == 1,
    'py-1.5': props.paddingY == 1.5,
    'py-2': props.paddingY == 2,
    'py-2.5': props.paddingY == 2.5,
    'py-3': props.paddingY == 3,
    'py-3.5': props.paddingY == 3.5,
    'py-4': props.paddingY == 4,
    'py-5': props.paddingY == 5,
    'py-6': props.paddingY == 6,
    'py-7': props.paddingY == 7,
    'py-8': props.paddingY == 8,
    'py-9': props.paddingY == 9,
    'py-10': props.paddingY == 10,
    'py-11': props.paddingY == 11,
    'py-12': props.paddingY == 12,
    // X-axis padding
    'px-0': props.paddingX == 0,
    'px-0.5': props.paddingX == 0.5,
    'px-1': props.paddingX == 1,
    'px-1.5': props.paddingX == 1.5,
    'px-2': props.paddingX == 2,
    'px-2.5': props.paddingX == 2.5,
    'px-3': props.paddingX == 3,
    'px-3.5': props.paddingX == 3.5,
    'px-4': props.paddingX == 4,
    'px-5': props.paddingX == 5,
    'px-6': props.paddingX == 6,
    'px-7': props.paddingX == 7,
    'px-8': props.paddingX == 8,
    'px-9': props.paddingX == 9,
    'px-10': props.paddingX == 10,
    'px-11': props.paddingX == 11,
    'px-12': props.paddingX == 12,
}));

// Opacity class
const opacityClass = computed(() => {
    if (props.opacity === 100) return '';
    return `opacity-${props.opacity}`;
});

// Gradient style object for inline styles
const gradientStyle = computed(() => {
    if (!props.gradientColors || !Array.isArray(props.gradientColors) || props.gradientColors.length < 2) {
        return {};
    }

    const gradientStops = props.gradientColors
        .map((color, index) => {
            const percentage = Math.round((index / (props.gradientColors.length - 1)) * 100);
            return `${color} ${percentage}%`;
        })
        .join(', ');

    return {
        backgroundImage: `linear-gradient(${props.gradientDirection}, ${gradientStops})`,
        backgroundSize: '100% 100%',
        backgroundRepeat: 'no-repeat',
        backgroundPosition: 'center'
    };
});

// Gradient background class (if gradientColors are provided)
const gradientClass = computed(() => {
    if (!props.gradientColors || !Array.isArray(props.gradientColors) || props.gradientColors.length < 2) {
        return '';
    }

    const gradientStops = props.gradientColors
        .map((color, index) => {
            const percentage = Math.round((index / (props.gradientColors.length - 1)) * 100);
            return `${color} ${percentage}%`;
        })
        .join(', ');

    return `bg-gradient-${props.gradientDirection.replace('-', '')} from-${props.gradientColors[0]} to-${props.gradientColors[props.gradientColors.length - 1]}`;
});

// Main border class
const borderClass = computed(() => [
    'w-full',
    borderPositionClasses.value,
    borderStyleClasses[props.borderStyle],
    borderColorClasses.value,
    opacityClass.value,
    gradientClass.value,
    paddingClasses.value,
    borderWidthClass.value,
    {
        'transition-all duration-300': props.animateOnHover,
        'hover:opacity-80': props.animateOnHover && props.opacity === 100,
        'hover:opacity-100': props.animateOnHover && props.opacity < 100,
    },
    props.customClass
].filter(Boolean).join(' '));
</script>

<template>
    <div
        :class="[
            'w-full',
            { 'hidden sm:block': !showOnMobile }
        ]"
        role="separator"
        :aria-label="ariaLabel"
    >
        <div :class="['w-full', paddingClasses]">
            <div
                :class="borderClass"
                :style="gradientStyle"
            />
        </div>
    </div>
</template>

<script>
// This script section is needed for Volar to properly infer component props
export default {
    inheritAttrs: false
};
</script>

<style scoped>
/* Smooth transitions for interactive elements */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Dark mode transitions */
@media (prefers-color-scheme: dark) {
    .dark-transition {
        transition: border-color 0.2s ease-in-out, opacity 0.2s ease-in-out;
    }
}

/* Ensure border is visible in high contrast mode */
@media (forced-colors: active) {
    .border-1,
    .border-2,
    .border-3,
    .border-4,
    .border-5,
    .border-6,
    .border-7,
    .border-8 {
        border-color: CanvasText !important;
    }
}

/* Custom border widths for more precise control */
.border-t-1 { border-top-width: 1px; }
.border-t-2 { border-top-width: 2px; }
.border-t-3 { border-top-width: 3px; }
.border-t-4 { border-top-width: 4px; }
.border-t-\[5px\] { border-top-width: 5px; }
.border-t-\[6px\] { border-top-width: 6px; }
.border-t-\[7px\] { border-top-width: 7px; }
.border-t-8 { border-top-width: 8px; }

.border-b-1 { border-bottom-width: 1px; }
.border-b-2 { border-bottom-width: 2px; }
.border-b-3 { border-bottom-width: 3px; }
.border-b-4 { border-bottom-width: 4px; }
.border-b-\[5px\] { border-bottom-width: 5px; }
.border-b-\[6px\] { border-bottom-width: 6px; }
.border-b-\[7px\] { border-bottom-width: 7px; }
.border-b-8 { border-bottom-width: 8px; }

/* Opacity utilities */
.opacity-0 { opacity: 0; }
.opacity-5 { opacity: 0.05; }
.opacity-10 { opacity: 0.1; }
.opacity-20 { opacity: 0.2; }
.opacity-25 { opacity: 0.25; }
.opacity-30 { opacity: 0.3; }
.opacity-40 { opacity: 0.4; }
.opacity-50 { opacity: 0.5; }
.opacity-60 { opacity: 0.6; }
.opacity-70 { opacity: 0.7; }
.opacity-75 { opacity: 0.75; }
.opacity-80 { opacity: 0.8; }
.opacity-90 { opacity: 0.9; }
.opacity-95 { opacity: 0.95; }
.opacity-100 { opacity: 1; }

/* Custom gradient utility */
.bg-gradient-tor {
    background-image: linear-gradient(to right, var(--tw-gradient-stops));
}
.bg-gradient-tol {
    background-image: linear-gradient(to left, var(--tw-gradient-stops));
}
.bg-gradient-tob {
    background-image: linear-gradient(to bottom, var(--tw-gradient-stops));
}
.bg-gradient-tot {
    background-image: linear-gradient(to top, var(--tw-gradient-stops));
}
.bg-gradient-totr {
    background-image: linear-gradient(to top right, var(--tw-gradient-stops));
}
.bg-gradient-totl {
    background-image: linear-gradient(to top left, var(--tw-gradient-stops));
}
.bg-gradient-tobr {
    background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}
.bg-gradient-tobl {
    background-image: linear-gradient(to bottom left, var(--tw-gradient-stops));
}
</style>

