<script setup>
import { ref, provide, computed, onMounted, onBeforeUnmount } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Sidebar from '@/Components/Sidebar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

// Sidebar state management
const sidebarCollapsed = ref(localStorage.getItem('sidebarCollapsed') === 'true' || false);
const mobileSidebarOpen = ref(false);
const isMobile = ref(window.innerWidth < 768);

// Handle window resize
const handleResize = () => {
    isMobile.value = window.innerWidth < 768;
    if (!isMobile.value && mobileSidebarOpen.value) {
        mobileSidebarOpen.value = false;
    }
};

// Set up resize listener
onMounted(() => {
    window.addEventListener('resize', handleResize);
});

// Clean up resize listener
onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
});

// Watch for route changes to close mobile sidebar
const page = usePage();
const currentUrl = computed(() => page.url);

// Toggle sidebar based on screen size
const toggleSidebar = () => {
    if (isMobile.value) { // Mobile
        mobileSidebarOpen.value = !mobileSidebarOpen.value;
    } else { // Desktop
        sidebarCollapsed.value = !sidebarCollapsed.value;
        localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value);
    }
};

// Close mobile sidebar when clicking outside
const closeMobileSidebar = () => {
    if (isMobile.value) {
        mobileSidebarOpen.value = false;
    }
};

// Provide sidebar state to child components
const sidebarState = {
    collapsed: computed(() => sidebarCollapsed.value),
    toggle: toggleSidebar
};

provide('sidebarState', sidebarState);

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="flex h-screen bg-lemon_chiffon-500 relative" @click.self="closeMobileSidebar">
            <!-- Mobile overlay -->
            <div 
                v-if="mobileSidebarOpen && isMobile" 
                class="fixed inset-0 bg-black bg-opacity-50 z-30"
                @click="closeMobileSidebar"
            ></div>
            
            <!-- Sidebar -->
            <Sidebar 
                :collapsed="sidebarCollapsed" 
                :mobile-open="mobileSidebarOpen"
                :is-mobile="isMobile"
                @toggle="toggleSidebar" 
                class="z-40"
            />

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Mobile header - Only visible on small screens when sidebar is NOT active -->
                <div v-if="isMobile && !mobileSidebarOpen" 
                     class="fixed top-0 left-0 right-0 h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 z-30">
                    <button @click="toggleSidebar" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex-1 flex justify-center">
                        <Link :href="route('dashboard')" class="flex items-center">
                            <ApplicationMark class="h-8 w-auto" />
                        </Link>
                    </div>
                    <div class="w-6"></div> <!-- Spacer for alignment -->
                </div>
                
                <!-- Scrollable content area -->
                <div class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out"
                     :class="{
                         'ml-0 md:ml-16': sidebarCollapsed, 
                         'ml-0 md:ml-64': !sidebarCollapsed
                     }">
                    <!-- Header -->
                    <header v-if="$slots.header" class="bg-light_blue-900 shadow-md border-b border-light_blue-600 py-3 px-6 sticky top-0 z-20">
                        <div class="flex items-center justify-between h-full">
                            <slot name="header" />

                            <!-- User Profile Menu -->
                            <div class="relative">
                                <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center text-sm font-medium text-light_blue-200 hover:text-white focus:outline-none transition duration-150 ease-in-out">
                                        <div>{{ $page.props.auth.user.name }}</div>
                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </template>

                                <!-- Account Management -->
                                <div class="block px-4 py-2 text-xs text-peach-100">
                                    Manage Account
                                </div>

                                <DropdownLink :href="route('profile.show')">
                                    Profile
                                </DropdownLink>

                                <div class="border-t border-light_blue-700 my-1"></div>

                                <!-- Authentication -->
                                <DropdownLink as="button" @click="logout">
                                    Log Out
                                </DropdownLink>
                                </Dropdown>
                            </div>
                        </div>
                    </header>

                    <!-- Main Content -->
                    <main class="min-h-[calc(100vh-4rem)] p-6">
                        <slot />
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Ensure the main content area takes full height and scrolls properly */
html, body, #app {
    height: 100%;
    margin: 0;
    padding: 0;
}
</style>
