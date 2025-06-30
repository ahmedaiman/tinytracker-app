<script setup>
import { computed } from 'vue';

const props = defineProps({
    id: {
        type: [String, Number],
        required: true
    },
    name: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    },
    progress: {
        type: Number,
        default: 0,
        validator: (value) => value >= 0 && value <= 100
    },
    status: {
        type: String,
        default: 'in-progress',
        validator: (value) => ['planning', 'in-progress', 'on-hold', 'completed', 'cancelled'].includes(value)
    },
    members: {
        type: Number,
        default: 0
    },
    dueDate: {
        type: String,
        default: ''
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'success', 'warning', 'danger', 'info', 'accent'].includes(value)
    },
    icon: {
        type: String,
        default: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
    },
    showActions: {
        type: Boolean,
        default: true
    },
    clickable: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['view', 'edit', 'delete']);

const statusConfig = {
    'planning': {
        label: 'Planning',
        color: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'
    },
    'in-progress': {
        label: 'In Progress',
        color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
    },
    'on-hold': {
        label: 'On Hold',
        color: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
        icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'
    },
    'completed': {
        label: 'Completed',
        color: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        icon: 'M5 13l4 4L19 7'
    },
    'cancelled': {
        label: 'Cancelled',
        color: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        icon: 'M6 18L18 6M6 6l12 12'
    }
};

const colorClasses = {
    primary: {
        bg: 'bg-primary-100 dark:bg-primary-900/30',
        text: 'text-primary-700 dark:text-primary-400',
        border: 'border-primary-200 dark:border-primary-800/50'
    },
    success: {
        bg: 'bg-success-100 dark:bg-success-900/30',
        text: 'text-success-700 dark:text-success-400',
        border: 'border-success-200 dark:border-success-800/50'
    },
    warning: {
        bg: 'bg-danger-100 dark:bg-danger-900/30',
        text: 'text-danger-700 dark:text-danger-400',
        border: 'border-danger-200 dark:border-danger-800/50'
    },
    danger: {
        bg: 'bg-danger-100 dark:bg-danger-900/30',
        text: 'text-danger-700 dark:text-danger-400',
        border: 'border-danger-200 dark:border-danger-800/50'
    },
    info: {
        bg: 'bg-info-100 dark:bg-info-900/30',
        text: 'text-info-700 dark:text-info-400',
        border: 'border-info-200 dark:border-info-800/50'
    },
    accent: {
        bg: 'bg-accent-100 dark:bg-accent-900/30',
        text: 'text-accent-700 dark:text-accent-400',
        border: 'border-accent-200 dark:border-accent-800/50'
    }
};

const formattedDueDate = computed(() => {
    if (!props.dueDate) return '';
    const date = new Date(props.dueDate);
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    }).format(date);
});

const progressColor = computed(() => {
    if (props.progress < 30) return 'bg-red-500';
    if (props.progress < 70) return 'bg-yellow-500';
    return 'bg-green-500';
});

const handleClick = () => {
    if (props.clickable) {
        emit('view', props.id);
    }
};
</script>

<template>
    <div
        class="group p-4 rounded-xl border transition-colors relative overflow-hidden"
        :class="[
            colorClasses[color]?.border,
            clickable ? 'hover:shadow-md cursor-pointer' : ''
        ]"
        @click="handleClick"
    >
        <!-- Header -->
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center">
                    <h3 class="text-base font-semibold text-neutral-900 dark:text-white truncate pr-2">
                        {{ name }}
                    </h3>
                    <span
                        v-if="status"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap ml-2"
                        :class="statusConfig[status]?.color"
                    >
                        <svg
                            v-if="statusConfig[status]?.icon"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3 w-3 mr-1"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                :d="statusConfig[status]?.icon"
                            />
                        </svg>
                        {{ statusConfig[status]?.label }}
                    </span>
                </div>
                <p v-if="description" class="mt-1 text-sm text-neutral-500 dark:text-neutral-400 line-clamp-2">
                    {{ description }}
                </p>
            </div>

            <!-- Project Icon -->
            <div
                class="ml-4 flex-shrink-0 p-2 rounded-lg"
                :class="colorClasses[color]?.bg"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    :class="colorClasses[color]?.text"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        :d="icon"
                    />
                </svg>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-3">
            <div class="flex justify-between text-xs text-neutral-500 dark:text-neutral-400 mb-1">
                <span>Progress</span>
                <span>{{ progress }}%</span>
            </div>
            <div class="w-full bg-neutral-200 rounded-full h-2 dark:bg-neutral-700 overflow-hidden">
                <div
                    class="h-full rounded-full transition-all duration-700 ease-out min-w-[0.5rem]"
                    :class="{
                        'bg-red-500': progress < 30,
                        'bg-yellow-500': progress >= 30 && progress < 70,
                        'bg-green-500': progress >= 70
                    }"
                    :style="{
                        width: `${Math.max(5, Math.min(100, progress))}%`
                    }"
                ></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-2 border-t border-neutral-100 dark:border-neutral-700">
            <div class="flex items-center space-x-2">
                <div class="flex -space-x-2">
                    <!-- Member avatars -->
                    <div
                        v-for="i in Math.min(members, 3)"
                        :key="i"
                        class="h-6 w-6 rounded-full bg-neutral-300 dark:bg-neutral-600 border-2 border-white dark:border-neutral-800 flex items-center justify-center text-xs font-medium text-neutral-700 dark:text-neutral-200"
                    >
                        {{ i }}
                    </div>
                    <div
                        v-if="members > 3"
                        class="h-6 w-6 rounded-full bg-neutral-200 dark:bg-neutral-700 border-2 border-white dark:border-neutral-800 flex items-center justify-center text-xs font-medium text-neutral-500 dark:text-neutral-400"
                    >
                        +{{ members - 3 }}
                    </div>
                </div>
                <span class="text-xs text-neutral-500 dark:text-neutral-400">
                    {{ members }} {{ members === 1 ? 'member' : 'members' }}
                </span>
            </div>

            <div v-if="dueDate" class="text-xs text-neutral-500 dark:text-neutral-400">
                Due {{ formattedDueDate }}
            </div>
        </div>

        <!-- Actions (shown on hover) -->
        <div
            v-if="showActions"
            class="absolute top-2 right-2 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm rounded-lg p-1 shadow-sm border border-neutral-200 dark:border-neutral-600"
        >
            <button
                class="p-1.5 rounded-full text-neutral-500 hover:text-primary-500 dark:hover:text-primary-400 hover:bg-white/50 dark:hover:bg-neutral-700/50 transition-colors"
                @click.stop="$emit('edit', id)"
                title="Edit project"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
            <div class="h-4 w-px bg-neutral-200 dark:bg-neutral-600 my-1"></div>
            <button
                class="p-1.5 rounded-full text-neutral-500 hover:text-danger-500 dark:hover:text-danger-400 hover:bg-white/50 dark:hover:bg-neutral-700/50 transition-colors"
                @click.stop="$emit('delete', id)"
                title="Delete project"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
</template>
