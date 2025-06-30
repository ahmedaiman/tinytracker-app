<script setup>
defineProps({
    name: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    change: {
        type: String,
        default: ''
    },
    changeType: {
        type: String,
        default: 'none',
        validator: (value) => ['increase', 'decrease', 'none'].includes(value)
    },
    icon: {
        type: String,
        required: true
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'success', 'warning', 'danger', 'info', 'accent'].includes(value)
    },
    iconBg: {
        type: String,
        default: 'bg-primary-100 dark:bg-primary-900/30'
    },
    iconColor: {
        type: String,
        default: 'text-primary-600 dark:text-primary-400'
    }
});

const colorClasses = {
    primary: {
        bg: 'bg-primary-200 dark:bg-primary-900/30',
        text: 'text-primary-600 dark:text-primary-400',
        border: 'border-primary-200 dark:border-primary-800/50'
    },
    success: {
        bg: 'bg-success-200 dark:bg-success-900/30',
        text: 'text-success-600 dark:text-success-400',
        border: 'border-success-200 dark:border-success-800/50'
    },
    warning: {
        bg: 'bg-warning-200 dark:bg-warning-900/30',
        text: 'text-warning-600 dark:text-warning-400',
        border: 'border-warning-200 dark:border-warning-800/50'
    },
    danger: {
        bg: 'bg-danger-200 dark:bg-danger-900/30',
        text: 'text-danger-600 dark:text-danger-400',
        border: 'border-danger-200 dark:border-danger-800/50'
    },
    info: {
        bg: 'bg-info-200 dark:bg-info-900/30',
        text: 'text-info-600 dark:text-info-400',
        border: 'border-info-200 dark:border-info-800/50'
    },
    accent: {
        bg: 'bg-accent-200 dark:bg-accent-900/30',
        text: 'text-accent-600 dark:text-accent-400',
        border: 'border-accent-200 dark:border-accent-800/50'
    }
};

const changeIcon = {
    increase: '↑',
    decrease: '↓',
    none: ''
};
</script>

<template>
    <div class="relative group bg-white/50 dark:bg-neutral-800/50 p-4 rounded-xl border border-neutral-100 dark:border-neutral-700/50 hover:shadow-md transition-shadow duration-200 overflow-hidden">
        <!-- Decorative background element -->
        <div 
            class="absolute -right-6 -top-6 w-24 h-24 rounded-full opacity-10 group-hover:opacity-20 transition-opacity duration-300"
            :class="colorClasses[color]?.bg"
        />
        
        <div class="relative z-10 flex items-center">
            <!-- Icon -->
            <div class="p-2 rounded-lg mr-3" :class="[iconBg, colorClasses[color]?.bg]">
                <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    class="h-5 w-5" 
                    :class="[iconColor, colorClasses[color]?.text]" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        :d="icon" 
                    />
                </svg>
            </div>
            
            <!-- Content -->
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400 truncate">{{ name }}</p>
                <div class="flex items-baseline">
                    <p class="text-xl font-semibold text-neutral-900 dark:text-white">{{ value }}</p>
                    <span 
                        v-if="change && changeType !== 'none'"
                        class="ml-2 text-xs font-medium"
                        :class="{
                            'text-success-600 dark:text-success-400': changeType === 'increase',
                            'text-danger-600 dark:text-danger-400': changeType === 'decrease'
                        }"
                    >
                        {{ changeIcon[changeType] }}{{ change }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
