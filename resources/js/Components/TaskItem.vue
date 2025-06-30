<script setup>
import { computed } from 'vue';

const props = defineProps({
    id: {
        type: [String, Number],
        required: true
    },
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    },
    due: {
        type: String,
        default: ''
    },
    priority: {
        type: String,
        default: 'medium',
        validator: (value) => ['low', 'medium', 'high'].includes(value)
    },
    project: {
        type: String,
        default: ''
    },
    progress: {
        type: Number,
        default: 0,
        validator: (value) => value >= 0 && value <= 100
    },
    completed: {
        type: Boolean,
        default: false
    },
    showProject: {
        type: Boolean,
        default: true
    },
    showActions: {
        type: Boolean,
        default: true
    },
    clickable: {
        type: Boolean,
        default: true
    },
    showCheckbox: {
        type: Boolean,
        default: true
    },
    showProgress: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['complete', 'view', 'edit', 'delete']);

const priorityConfig = {
    high: {
        label: 'High',
        color: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        icon: 'M5 12h14m-7-7v14m0 0l-7-7m7 7l7-7',
        dot: 'bg-red-500'
    },
    medium: {
        label: 'Medium',
        color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        icon: 'M5 12h14m-7-7v14m0 0l-7-7m7 7l7-7',
        dot: 'bg-yellow-500'
    },
    low: {
        label: 'Low',
        color: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        icon: 'M5 12h14m-7-7v14m0 0l-7-7m7 7l7-7',
        dot: 'bg-blue-500'
    }
};

const isDueSoon = computed(() => {
    if (!props.due) return false;
    const today = new Date();
    const dueDate = new Date(props.due);
    const diffTime = dueDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays >= 0 && diffDays <= 3; // Due in the next 3 days
});

const isOverdue = computed(() => {
    if (!props.due) return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const dueDate = new Date(props.due);
    return dueDate < today;
});

const dueDateFormatted = computed(() => {
    if (!props.due) return '';
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const dueDate = new Date(props.due);
    const diffTime = dueDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Tomorrow';
    if (diffDays === -1) return 'Yesterday';
    if (diffDays < 0) return `${Math.abs(diffDays)} days ago`;
    if (diffDays < 7) return `In ${diffDays} days`;
    
    return new Intl.DateTimeFormat('en-US', { 
        month: 'short', 
        day: 'numeric' 
    }).format(dueDate);
});

const progressColor = computed(() => {
    if (props.completed) return 'bg-green-500';
    if (props.progress < 30) return 'bg-red-500';
    if (props.progress < 70) return 'bg-yellow-500';
    return 'bg-green-500';
});

const handleComplete = (event) => {
    event.stopPropagation();
    emit('complete', { id: props.id, completed: event.target.checked });
};

const handleClick = () => {
    if (props.clickable) {
        emit('view', props.id);
    }
};
</script>

<template>
    <div 
        class="group relative w-full p-3 rounded-lg border transition-colors"
        :class="[
            completed 
                ? 'bg-green-50/50 dark:bg-green-900/10 border-green-200 dark:border-green-800/50' 
                : 'bg-white/50 dark:bg-neutral-800/50 border-neutral-100 dark:border-neutral-700/50 hover:border-neutral-200 dark:hover:border-neutral-600/50',
            clickable ? 'cursor-pointer hover:shadow-sm' : ''
        ]"
        @click="handleClick"
    >
        <div class="flex">
            <!-- Checkbox -->
            <div v-if="showCheckbox" class="flex-shrink-0 flex items-start mr-2 pt-0.5">
                <input
                    type="checkbox"
                    :checked="completed"
                    @change.stop="handleComplete"
                    :title="completed ? 'Mark as incomplete' : 'Mark as complete'"
                    class="h-4 w-4 rounded border-neutral-300 text-primary-600 appearance-none focus:ring-0 focus:outline-none focus:shadow-none focus-visible:outline-none focus:border-neutral-300 active:outline-none active:ring-0 active:shadow-none active:border-neutral-300 dark:focus:border-neutral-600 dark:border-neutral-600 dark:bg-neutral-800 dark:checked:bg-primary-500 dark:checked:border-primary-500 mt-0.5"
                />
            </div>
            
            <!-- Main Content -->
            <div class="flex-1 min-w-0 overflow-hidden">
                <!-- Title and Priority -->
                <div class="flex items-start">
                    <h3 
                        class="text-sm font-medium leading-tight break-words pr-2 flex-1 min-w-0"
                        :class="[
                            completed 
                                ? 'text-green-800 dark:text-green-200 line-through' 
                                : 'text-neutral-900 dark:text-white',
                            priority && !completed ? 'pr-1' : ''
                        ]"
                    >
                        {{ title }}
                    </h3>
                    
                    <!-- Priority Badge -->
                    <div v-if="priority && !completed" class="flex-shrink-0 mt-0.5">
                        <span 
                            class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium whitespace-nowrap"
                            :class="priorityConfig[priority]?.color"
                        >
                            <span 
                                class="w-1.5 h-1.5 rounded-full mr-1"
                                :class="priorityConfig[priority]?.dot"
                            ></span>
                            <span class="hidden xs:inline">{{ priorityConfig[priority]?.label }}</span>
                            <span class="xs:hidden">{{ priorityConfig[priority]?.label.charAt(0) }}</span>
                        </span>
                    </div>
                </div>
                
                <!-- Description -->
                <p 
                    v-if="description" 
                    class="mt-1 text-xs text-neutral-600 dark:text-neutral-400 line-clamp-2 leading-tight"
                    :class="{ 'line-through': completed }"
                >
                    {{ description }}
                </p>
                
                <!-- Meta -->
                <div class="mt-2 flex flex-wrap items-center gap-1.5">
                    <!-- Project -->
                    <div 
                        v-if="showProject && project" 
                        class="inline-flex items-center text-[11px] text-neutral-500 dark:text-neutral-400 bg-neutral-100 dark:bg-neutral-700/50 rounded px-1.5 py-0.5"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 text-neutral-400 dark:text-neutral-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="truncate max-w-[120px] sm:max-w-none">{{ project }}</span>
                    </div>
                    
                    <!-- Due Date -->
                    <div 
                        v-if="due" 
                        class="inline-flex items-center text-[11px] text-neutral-500 dark:text-neutral-400 bg-neutral-100 dark:bg-neutral-700/50 rounded px-1.5 py-0.5"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 text-neutral-400 dark:text-neutral-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="whitespace-nowrap">{{ due }}</span>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div 
                    v-if="showProgress && progress > 0" 
                    class="mt-3"
                >
                    <div class="flex justify-between text-xs text-neutral-500 dark:text-neutral-400 mb-1">
                        <span>Progress</span>
                        <span>{{ progress }}%</span>
                    </div>
                    <div class="w-full bg-neutral-200 rounded-full h-1.5 dark:bg-neutral-700">
                        <div 
                            class="h-1.5 rounded-full transition-all duration-500 ease-in-out" 
                            :class="progressColor"
                            :style="{ width: `${progress}%` }"
                        ></div>
                    </div>
                </div>
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
                title="Edit task"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
            <div class="h-4 w-px bg-neutral-200 dark:bg-neutral-600 my-1"></div>
            <button 
                class="p-1.5 rounded-full text-neutral-500 hover:text-danger-500 dark:hover:text-danger-400 hover:bg-white/50 dark:hover:bg-neutral-700/50 transition-colors"
                @click.stop="$emit('delete', id)"
                title="Delete task"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
</template>
