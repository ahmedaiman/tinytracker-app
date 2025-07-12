<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import BloodGlucoseTrends from '@/Components/BloodGlucoseTrends.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SampleForm from '@/Components/SampleForm.vue';

// Modal state
const showingSampleForm = ref(false);

// Recent activity feed
const recentActivity = [
    {
        id: 1,
        user: {
            name: 'Alex Johnson',
            avatar: 'AJ',
            color: 'bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-200'
        },
        action: 'completed',
        target: 'Project Proposal',
        time: '2 minutes ago'
    },
    {
        id: 2,
        user: {
            name: 'Taylor Smith',
            avatar: 'TS',
            color: 'bg-secondary-100 dark:bg-secondary-900/30 text-secondary-800 dark:text-secondary-200'
        },
        action: 'added',
        target: 'New Task: Design Review',
        time: '1 hour ago'
    },
    {
        id: 3,
        user: {
            name: 'Jordan Lee',
            avatar: 'JL',
            color: 'bg-accent-100 dark:bg-accent-900/30 text-accent-800 dark:text-accent-200'
        },
        action: 'commented on',
        target: 'Q2 Planning',
        time: '3 hours ago'
    },
    {
        id: 4,
        user: {
            name: 'Casey Kim',
            avatar: 'CK',
            color: 'bg-success-100 dark:bg-success-900/30 text-success-800 dark:text-success-200'
        },
        action: 'updated',
        target: 'Marketing Campaign',
        time: '5 hours ago'
    },
];

const upcomingTasks = [
    {
        id: 1,
        title: 'Team Standup Meeting',
        due: 'Today, 10:00 AM',
        priority: 'high',
        project: 'Project Alpha',
        progress: 75
    },
    {
        id: 2,
        title: 'Submit Project Report',
        due: 'Tomorrow',
        priority: 'medium',
        project: 'Quarterly Review',
        progress: 45
    },
    {
        id: 3,
        title: 'Review Design Mockups',
        due: 'In 2 days',
        priority: 'low',
        project: 'UI Redesign',
        progress: 20
    },
];

const activeTab = ref('overview');

const projects = [
    {
        id: 1,
        name: 'Website Redesign',
        description: 'Complete redesign of the company website with modern UI/UX',
        progress: 75,
        status: 'in-progress',
        members: 5,
        dueDate: '2023-12-15',
        color: 'primary',
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
    },
    {
        id: 2,
        name: 'Mobile App Launch',
        description: 'Development and launch of the new mobile application',
        progress: 30,
        status: 'in-progress',
        members: 8,
        dueDate: '2024-02-28',
        color: 'accent',
        icon: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'
    },
    {
        id: 3,
        name: 'Marketing Campaign',
        description: 'Q1 marketing campaign planning and execution',
        progress: 90,
        status: 'completed',
        members: 4,
        dueDate: '2023-11-30',
        color: 'success',
        icon: 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 15H15v5.488A9.026 9.026 0 0020.488 15z'
    },
    {
        id: 4,
        name: 'Product Roadmap',
        description: 'Planning the next 6 months of product development',
        progress: 15,
        status: 'planning',
        members: 3,
        dueDate: '2024-01-15',
        color: 'warning',
        icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'
    }
];

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 18) return 'Good afternoon';
    return 'Good evening';
});
</script>

<template>
    <AppLayout title="Dashboard">
        <Head>
            <title>Dashboard - TinyTracker</title>
        </Head>
        <!-- Background Elements -->
        <div class="fixed inset-0 -z-10 overflow-hidden bg-gradient-to-br from-neutral-50 to-neutral-100 dark:from-neutral-900 dark:to-neutral-800">
            <div class="absolute top-0 right-0 w-72 h-72 bg-primary-200 dark:bg-primary-900/30 rounded-full mix-blend-multiply filter blur-3xl opacity-20 dark:opacity-10"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-secondary-200 dark:bg-secondary-900/30 rounded-full mix-blend-multiply filter blur-3xl opacity-20 dark:opacity-10"></div>
            <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-accent-100 dark:bg-accent-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-10 dark:opacity-5"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 py-6 max-w-7xl">
            <!-- Header -->
            <div class="mb-8 bg-white/90 dark:bg-neutral-800/95 backdrop-blur-sm rounded-2xl p-6 shadow-sm border border-neutral-100/60 dark:border-neutral-700/60 transition-colors duration-200 hover:shadow-md hover:border-primary-200/50 dark:hover:border-primary-700/30">
                <div class="flex flex-col space-y-4 md:space-y-0 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-1">
                        <h1 class="text-2xl md:text-3xl font-bold text-neutral-800 dark:text-white">{{ greeting }}, {{ $page.props.auth.user.name }}! 👋</h1>
                        <p class="text-sm md:text-base text-neutral-600 dark:text-neutral-300">Here's Ivan's diabetes management dashboard for today.</p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="space-y-6">
                <!-- Top Row - Side by Side Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Quick Actions -->
                    <Card title="Quick Log" class="h-full flex flex-col" :show-header="true">
                        <div class="p-2 flex-1">
                            <div class="grid grid-cols-3 gap-2">
                                <button class="group flex flex-col items-center justify-center p-3 rounded-xl bg-white/90 dark:bg-neutral-800/90 backdrop-blur-sm border border-neutral-200/60 dark:border-neutral-700/60 transition-all duration-200 hover:border-primary-300/50 dark:hover:border-primary-600/40 hover:shadow-sm hover:bg-white dark:hover:bg-neutral-800/80">
                                    <div class="p-1.5 rounded-lg bg-emerald-100/70 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-100/90 dark:group-hover:bg-emerald-900/40 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                    <p class="mt-1.5 text-xs font-medium text-gray-700 dark:text-gray-200 group-hover:text-gray-800 dark:group-hover:text-white">Add BG</p>
                                </button>

                                <button 
                                    @click="showingSampleForm = true"
                                    class="group flex flex-col items-center justify-center p-3 rounded-xl bg-white/90 dark:bg-neutral-800/90 backdrop-blur-sm border border-neutral-200/60 dark:border-neutral-700/60 transition-all duration-200 hover:border-primary-300/50 dark:hover:border-primary-600/40 hover:shadow-sm hover:bg-white dark:hover:bg-neutral-800/80"
                                >
                                    <div class="p-1.5 rounded-lg bg-blue-100/70 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:bg-blue-100/90 dark:group-hover:bg-blue-900/40 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                    <p class="mt-1.5 text-xs font-medium text-gray-700 dark:text-gray-200 group-hover:text-gray-800 dark:group-hover:text-white">Add Meal</p>
                                </button>

                                <button class="group flex flex-col items-center justify-center p-3 rounded-xl bg-white/90 dark:bg-neutral-800/90 backdrop-blur-sm border border-neutral-200/60 dark:border-neutral-700/60 transition-all duration-200 hover:border-primary-300/50 dark:hover:border-primary-600/40 hover:shadow-sm hover:bg-white dark:hover:bg-neutral-800/80">
                                    <div class="p-1.5 rounded-lg bg-amber-100/70 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 group-hover:bg-amber-100/90 dark:group-hover:bg-amber-900/40 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    <p class="mt-1.5 text-xs font-medium text-gray-700 dark:text-gray-200 group-hover:text-gray-800 dark:group-hover:text-white">Add Note</p>
                                </button>

                                <button class="group flex flex-col items-center justify-center p-3 rounded-xl bg-white/90 dark:bg-neutral-800/90 backdrop-blur-sm border border-neutral-200/60 dark:border-neutral-700/60 transition-all duration-200 hover:border-primary-300/50 dark:hover:border-primary-600/40 hover:shadow-sm hover:bg-white dark:hover:bg-neutral-800/80">
                                    <div class="p-1.5 rounded-lg bg-violet-100/70 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 group-hover:bg-violet-100/90 dark:group-hover:bg-violet-900/40 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <p class="mt-1.5 text-xs font-medium text-gray-700 dark:text-gray-200 group-hover:text-gray-800 dark:group-hover:text-white">Add Location</p>
                                </button>

                                <button class="group flex flex-col items-center justify-center p-3 rounded-xl bg-white/90 dark:bg-neutral-800/90 backdrop-blur-sm border border-neutral-200/60 dark:border-neutral-700/60 transition-all duration-200 hover:border-primary-300/50 dark:hover:border-primary-600/40 hover:shadow-sm hover:bg-white dark:hover:bg-neutral-800/80">
                                    <div class="p-1.5 rounded-lg bg-indigo-100/70 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-100/90 dark:group-hover:bg-indigo-900/40 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p class="mt-1.5 text-xs font-medium text-gray-700 dark:text-gray-200 group-hover:text-gray-800 dark:group-hover:text-white">Add Appointment</p>
                                </button>
                            </div>
                        </div>
                    </Card>

                    <!-- Meal & Insulin Summary -->
                    <Card title="Today's Summary" class="flex-1 flex flex-col" :show-header="true">
                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-blue-50/80 dark:bg-blue-900/30 p-4 rounded-xl border border-blue-100/60 dark:border-blue-900/40 transition-colors duration-200 hover:border-blue-200/60 dark:hover:border-blue-800/60 hover:shadow-sm">
                                    <p class="text-xs font-medium text-blue-700 dark:text-blue-300">Meals Logged</p>
                                    <p class="mt-1 text-2xl font-semibold text-blue-900 dark:text-blue-100">3</p>
                                </div>
                                <div class="bg-violet-50/80 dark:bg-violet-900/30 p-4 rounded-xl border border-violet-100/60 dark:border-violet-900/40 transition-colors duration-200 hover:border-violet-200/60 dark:hover:border-violet-800/60 hover:shadow-sm">
                                    <p class="text-xs font-medium text-violet-700 dark:text-violet-300">Insulin Doses</p>
                                    <p class="mt-1 text-2xl font-semibold text-violet-900 dark:text-violet-100">2</p>
                                </div>
                                <div class="bg-emerald-50/80 dark:bg-emerald-900/30 p-4 rounded-xl border border-emerald-100/60 dark:border-emerald-900/40 transition-colors duration-200 hover:border-emerald-200/60 dark:hover:border-emerald-800/60 hover:shadow-sm">
                                    <p class="text-xs font-medium text-emerald-700 dark:text-emerald-300">Avg. BG</p>
                                    <p class="mt-1 text-2xl font-semibold text-emerald-900 dark:text-emerald-100">112 <span class="text-sm font-normal text-emerald-700/90 dark:text-emerald-300/90">mg/dL</span></p>
                                </div>
                                <div class="bg-amber-50/80 dark:bg-amber-900/30 p-4 rounded-xl border border-amber-100/60 dark:border-amber-900/40 transition-colors duration-200 hover:border-amber-200/60 dark:hover:border-amber-800/60 hover:shadow-sm">
                                    <p class="text-xs font-medium text-amber-700 dark:text-amber-300">Carbs</p>
                                    <p class="mt-1 text-2xl font-semibold text-amber-900 dark:text-amber-100">145 <span class="text-sm font-normal text-amber-700/90 dark:text-amber-300/90">g</span></p>
                                </div>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Blood Glucose Chart -->
                <Card title="Blood Glucose Tracker" class="flex-1 flex flex-col bg-gradient-to-br from-white/50 to-white/80 dark:from-neutral-800/50 dark:to-neutral-800/80 backdrop-blur-sm border border-neutral-200/60 dark:border-neutral-700/60 shadow-sm hover:shadow-md transition-all duration-300" :show-header="true">
                    <BloodGlucoseTrends />
                </Card>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6 flex flex-col">
                        <!-- Recent Blood Glucose Readings -->
                        <Card title="Recent Readings" class="flex flex-col border border-neutral-200/60 dark:border-neutral-700/60 bg-white/90 dark:bg-neutral-800/90 backdrop-blur-sm hover:shadow-md transition-shadow duration-300" :show-header="true">
                            <div class="flex-1 -mx-1 py-1">
                                <div class="space-y-2">
                                    <div v-for="i in 5" :key="i" class="flex items-center justify-between p-3 rounded-lg hover:bg-neutral-50/50 dark:hover:bg-neutral-700/30 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <div :class="[
                                                'p-1.5 rounded-lg',
                                                [95, 110, 120, 130, 115][i-1] < 100 ? 'bg-green-100/70 dark:bg-green-900/30 text-green-600 dark:text-green-400' :
                                                [95, 110, 120, 130, 115][i-1] < 140 ? 'bg-yellow-100/70 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400' :
                                                'bg-red-100/70 dark:bg-red-900/30 text-red-600 dark:text-red-400'
                                            ]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ [95, 110, 120, 130, 115][i-1] }} mg/dL
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ ['8:00 AM', '12:00 PM', '4:00 PM', '8:00 PM', '12:00 AM'][i-1] }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs px-2 py-1 rounded-full" :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': i % 3 === 0,
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300': i % 3 === 1,
                                                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': i % 3 === 2
                                            }">
                                                {{ ['45g', '60g', '30g', '75g', '50g'][i-1] }} Carbs
                                            </span>
                                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                {{ ['5u', '8u', '4u', '10u', '6u'][i-1] }} Insulin
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>
                    <!-- Right Column -->
                    <div class="space-y-6 flex flex-col">
                        <!-- Upcoming Appointments -->
                        <Card title="Appointments" class="flex-1 flex flex-col border border-neutral-200/60 dark:border-neutral-700/60 bg-white/90 dark:bg-neutral-800/90 backdrop-blur-sm hover:shadow-md transition-shadow duration-300" :show-header="true">
                            <div class="flex-1 -mx-1 py-1">
                                <div class="space-y-2">
                                    <div v-for="i in 3" :key="i" class="flex items-center p-3 rounded-lg hover:bg-neutral-50/50 dark:hover:bg-neutral-700/30 transition-colors">
                                        <div class="p-1.5 rounded-lg bg-indigo-100/50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ['Endocrinologist', 'Dentist', 'Eye Exam'][i-1] }}</p>
                                            <div class="flex items-center mt-0.5">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ ['Dr. Smith', 'Dr. Johnson', 'Dr. Williams'][i-1] }}</span>
                                                <span class="mx-1.5 text-gray-300 dark:text-gray-600">•</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ ['Tomorrow 10:00 AM', 'Friday 2:30 PM', 'Next Monday 11:15 AM'][i-1] }}</span>
                                            </div>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </Card>

                        <!-- Quick Actions Moved to Top -->
                    </div>
                </div>
            </div>
        </div>
        <SampleForm :show="showingSampleForm" @close="showingSampleForm = false" />
    </AppLayout>
</template>

<style scoped>
/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Dark mode scrollbar */
.dark ::-webkit-scrollbar-track {
    background: #2d3748;
}

.dark ::-webkit-scrollbar-thumb {
    background: #4a5568;
}

.dark ::-webkit-scrollbar-thumb:hover {
    background: #718096;
}

/* Animation for stats cards */
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
    100% { transform: translateY(0px); }
}

.group:hover .group-hover\:animate-float {
    animation: float 2s ease-in-out infinite;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Focus styles */

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    body {
        background: white !important;
        color: black !important;
    }

    .dark body {
        background: white !important;
        color: black !important;
    }

    .dark .dark\:bg-gray-800 {
        background-color: white !important;
    }

    .dark .dark\:text-white {
        color: black !important;
    }

    .dark .dark\:border-gray-700 {
        border-color: #e2e8f0 !important;
    }
}
</style>
