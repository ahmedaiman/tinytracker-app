<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';

const props = defineProps({
    collapsed: {
        type: Boolean,
        default: false
    },
    mobileOpen: {
        type: Boolean,
        default: false
    },
    isMobile: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['toggle']);

// Computed property to determine if sidebar is expanded
const isExpanded = computed(() => !props.collapsed);

// Always show text when sidebar is expanded or on mobile
const shouldShowText = computed(() => isExpanded.value || props.mobileOpen);

// Toggle sidebar collapse
const toggleSidebar = () => {
    emit('toggle');
};

// Sidebar state
const activeGroup = ref('General'); // Track active group
const user = usePage().props.auth.user;

// Use a computed property to sync with parent's collapsed state
const isCollapsed = computed(() => props.collapsed);

// Toggle navigation group
const toggleGroup = (groupTitle) => {
    if (activeGroup.value === groupTitle) {
        activeGroup.value = null;
    } else {
        activeGroup.value = groupTitle;
    }
};

// Check if a group is active
const isGroupActive = (groupTitle) => {
    return activeGroup.value === groupTitle;
};

// Navigation groups and items
const navGroups = [
    {
        title: 'Dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        items: [
            { name: 'Overview', href: route('dashboard'), icon: 'M4 6h16M4 10h16M4 14h16M4 18h16' },
        ],
    },
    {
        title: 'Projects',
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        items: [
            { name: 'All Projects', href: '#', icon: 'M4 6h16M4 10h16M4 14h16M4 18h16' },
            { name: 'Create New', href: '#', icon: 'M12 4v16m8-8H4' },
        ],
    },
    {
        title: 'Tasks',
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        items: [
            { name: 'My Tasks', href: '#', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
            { name: 'Assigned', href: '#', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
            { name: 'Completed', href: '#', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0zM9 12a3 3 0 116 0 3 3 0 01-6 0z' },
        ],
    },
    {
        title: 'Calendar',
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        items: [
            { name: 'Upcoming', href: '#', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
            { name: 'Today', href: '#', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
        ],
    },
];

// Import router for logout
import { router } from '@inertiajs/vue3';

// Settings navigation
const settingsNav = [
    { name: 'Settings', href: route('profile.show'), icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' },
    { 
        name: 'Logout', 
        href: '#', 
        icon: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
        onClick: () => router.post(route('logout'))
    },
];
</script>

<template>
    <aside 
        :class="[
            'fixed inset-y-0 left-0 z-40 flex flex-col h-full',
            'bg-white dark:bg-gray-800',
            'bg-gradient-to-b from-light_blue-50 to-light_blue-100 dark:from-gray-800 dark:to-gray-900',
            'border-r border-light_blue-100 dark:border-gray-700',
            'transition-all duration-250 ease-[cubic-bezier(0.25,0.1,0.25,1)]',
            'shadow-lg',
            'transform-gpu will-change-[width,transform]',
            'overflow-hidden',
            {
                // Responsive width classes
                'w-16': props.collapsed && !props.mobileOpen,
                'w-64': !props.collapsed && !props.isMobile, // Full width on mobile, 16rem on desktop expanded
                'w-72': !props.collapsed && props.isMobile,   // Slightly wider on mobile when expanded
                // Mobile behavior
                'translate-x-0': props.mobileOpen || !props.isMobile,
                '-translate-x-full': !props.mobileOpen && props.isMobile,
                // Responsive shadow
                'shadow-xl': props.isMobile && props.mobileOpen
            }
        ]"
    >
        <!-- Logo and Toggle -->
        <div class="flex items-center h-16 border-b border-light_blue-200/50 dark:border-gray-700/50 transition-all duration-200" :class="isExpanded ? 'px-4 justify-between' : 'justify-center'">
            <div v-if="isExpanded && !(props.isMobile && props.mobileOpen)" class="flex-1 min-w-0">
                <ApplicationMark class="h-8 w-auto text-light_blue-600" />
                <span class="ml-3 text-xl font-bold text-light_blue-900 dark:text-white">TinyTrack</span>
            </div>
            <div v-else class="relative w-full h-full flex items-center justify-center group">
                <div class="relative">
                    <ApplicationMark class="h-8 w-auto text-light_blue-600" />
                    <button 
                        @click="toggleSidebar" 
                        title="Expand"
                        class="absolute inset-0 flex items-center justify-center w-full h-full text-light_blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                    >
                        <div class="p-1.5 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
            
            <button 
                v-if="!isCollapsed"
                @click="toggleSidebar" 
                title="Collapse"
                class="p-1.5 rounded-full text-light_blue-500 hover:bg-light_blue-200/50 dark:text-light_blue-400 dark:hover:bg-gray-700 transition-colors"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Main Navigation -->
        <div class="flex-1 flex flex-col overflow-y-auto py-4">
            <nav class="flex-1 px-2 py-4 space-y-2 sidebar-nav">
                <div v-for="group in navGroups" :key="group.title" class="space-y-1">
                    <!-- Group Header -->
                    <button
                        v-if="!collapsed"
                        @click="toggleGroup(group.title)"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md group transition-all duration-200 ease-[cubic-bezier(0.25,0.1,0.25,1)] transform-gpu"
                        :class="[
                            isGroupActive(group.title) 
                                ? 'text-light_blue-800 dark:text-white bg-light_blue-100 dark:bg-gray-700/50' 
                                : 'text-light_blue-600 hover:bg-light_blue-50 dark:text-gray-300 dark:hover:bg-gray-700/30'
                        ]"
                    >
                        <div class="flex items-center">
                            <svg 
                                class="flex-shrink-0 h-5 w-5 mr-3 transition-transform duration-200 ease-[cubic-bezier(0.25,0.1,0.25,1)] transform-gpu" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor"
                                :class="isGroupActive(group.title) ? 'text-light_blue-600 dark:text-light_blue-400' : 'text-light_blue-500 dark:text-gray-400'"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="group.icon" />
                            </svg>
                            <Transition
                                enter-active-class="transition-all duration-200 ease-out"
                                enter-from-class="opacity-0 -translate-x-2 scale-95"
                                enter-to-class="opacity-100 translate-x-0 scale-100"
                                leave-active-class="transition-all duration-150 ease-in"
                                leave-from-class="opacity-100 translate-x-0 scale-100"
                                leave-to-class="opacity-0 -translate-x-2 scale-95"
                            >
                                <div v-if="isExpanded" class="flex items-center">
                                    <div class="flex items-center px-4 py-2 text-sm font-medium text-light_blue-900 dark:text-white" :class="{ 'justify-center': collapsed }">
                                        <span v-if="!collapsed">{{ group.title }}</span>
                                        <span v-else class="sr-only">{{ group.title }}</span>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </button>
                    
                    <!-- Group Items with Transition -->
                    <Transition
                        enter-active-class="transition-all duration-250 ease-[cubic-bezier(0.25,0.1,0.25,1)] overflow-hidden"
                        enter-from-class="opacity-0 max-h-0 transform-gpu -translate-y-2"
                        enter-to-class="opacity-100 max-h-[500px] transform-gpu translate-y-0"
                        leave-active-class="transition-all duration-200 ease-[cubic-bezier(0.25,0.1,0.25,1)] overflow-hidden"
                        leave-from-class="opacity-100 max-h-[500px] transform-gpu translate-y-0"
                        leave-to-class="opacity-0 max-h-0 transform-gpu -translate-y-2"
                    >
                        <div v-if="isGroupActive(group.title) || collapsed" class="space-y-1" :class="{ 'pl-4': !collapsed }">
                            <Link 
                                v-for="item in group.items" 
                                :key="item.name"
                                :href="item.href" 
                                class="group flex items-center text-sm font-medium rounded-md transition-all duration-200 ease-[cubic-bezier(0.25,0.1,0.25,1)]"
                                :class="[
                                    $page.url.startsWith(item.href) 
                                        ? 'text-light_blue-800 dark:text-white bg-light_blue-100 dark:bg-gray-700' 
                                        : 'text-light_blue-600 hover:bg-light_blue-50 dark:text-gray-300 dark:hover:bg-gray-700/30',
                                    'text-white',
                                    props.collapsed && !props.isMobile ? 'justify-center px-3 py-2 mx-1' : 'px-3 py-2',
                                    'relative overflow-hidden', // Ensure text doesn't overflow
                                    'md:justify-start' // Always left-align on medium and small screens
                                ]"
                            >
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 transition-all duration-200 ease-[cubic-bezier(0.25,0.1,0.25,1)]" :class="{
                                        'text-light_blue-600 dark:text-light_blue-400': $page.url.startsWith(item.href),
                                        'text-gray-200 dark:text-gray-400 group-hover:text-light_blue-300': !$page.url.startsWith(item.href),
                                        'hover:scale-110': !$page.url.startsWith(item.href)
                                    }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="item.icon" />
                                    </svg>
                                    <span class="whitespace-nowrap transition-all duration-200 ease-[cubic-bezier(0.25,0.1,0.25,1)]"
                                        :class="[
                                            'ml-3',
                                            $page.url.startsWith(item.href) ? 'font-semibold' : 'font-medium',
                                            {
                                                'opacity-100': shouldShowText,
                                                'opacity-0 w-0 absolute': !shouldShowText
                                            }
                                        ]">
                                        {{ item.name }}
                                    </span>
                                </div>
                            </Link>
                        </div>
                    </Transition>
                </div>
            </nav>
        </div>

        <!-- User & Settings -->
        <div class="p-3 border-t border-light_blue-200/50 dark:border-gray-700/50">
            <div v-if="!collapsed" class="flex items-center px-2 py-2">
                <img 
                    class="h-8 w-8 rounded-full" 
                    :src="user.profile_photo_url" 
                    :alt="user.name"
                >
                <div class="ml-3">
                    <p class="text-sm font-medium text-light_blue-900 dark:text-white">{{ user.name }}</p>
                    <p class="text-xs font-medium text-light_blue-500 dark:text-gray-400">View profile</p>
                </div>
            </div>
            
            <div class="mt-2 space-y-1">
                <component
                    v-for="item in settingsNav"
                    :key="item.name"
                    :is="item.onClick ? 'button' : Link"
                    :href="item.href"
                    :as="item.onClick ? 'button' : 'a'"
                    :method="item.onClick ? 'post' : 'get'"
                    @click="item.onClick"
                    class="w-full group flex items-center text-sm font-medium rounded-md transition-all duration-200 ease-[cubic-bezier(0.25,0.1,0.25,1)]"
                    :class="[
                        $page.url === item.href 
                            ? 'text-light_blue-800 dark:text-white bg-light_blue-100 dark:bg-gray-700/50' 
                            : 'text-light_blue-600 hover:bg-light_blue-50 dark:text-gray-300 dark:hover:bg-gray-700/30',
                        collapsed ? 'justify-center px-2 py-2 mx-1' : 'px-2 py-2',
                        'text-left'
                    ]"
                >
                    <svg 
                        class="flex-shrink-0 h-5 w-5" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                        :class="$page.url === item.href ? 'text-light_blue-600 dark:text-light_blue-400' : 'text-light_blue-500 dark:text-gray-400'"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="item.icon" />
                    </svg>
                    <span v-if="!collapsed" class="ml-3">{{ item.name }}</span>
                    <span v-else class="sr-only">{{ item.name }}</span>
                </component>
            </div>
        </div>
    </aside>
</template>

<style scoped>
/* Custom scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.03);
    border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.15);
    border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.25);
}

/* Dark mode scrollbar */
.dark .custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.25);
}

/* Animation for collapsible groups */
@keyframes slideDown {
    from { 
        opacity: 0; 
        transform: translateY(-10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

.animate-slide-down {
    animation: slideDown 0.15s cubic-bezier(0.25, 0.1, 0.25, 1) forwards;
}

/* Prevent width animation flicker */
:deep(.v-enter-active),
:deep(.v-leave-active) {
    pointer-events: none;
}

/* Optimize transitions */
:deep(.transition-all) {
    backface-visibility: hidden;
    transform-style: preserve-3d;
}

/* Prevent scrollbar flash during transitions */
:deep(.sidebar-nav) {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
    overflow-y: auto;
}

:deep(.sidebar-nav::-webkit-scrollbar) {
    display: none; /* Chrome, Safari, Opera */
}

/* Only show scrollbar when hovering and not during transitions */
:deep(.sidebar-nav:hover:not(.v-enter-active):not(.v-leave-active)::-webkit-scrollbar) {
    display: block;
}
</style>
