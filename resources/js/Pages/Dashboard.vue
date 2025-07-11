<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import BloodGlucoseTrends from '@/Components/BloodGlucoseTrends.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SampleForm from '@/Components/SampleForm.vue';

// Stats for the dashboard
const stats = [
  {
    name: 'Today\'s BG Checks',
    value: '12',
    change: '-2',
    changeType: 'decrease',
    icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    color: 'success',
    iconBg: 'bg-success-100 dark:bg-success-900/30',
    iconColor: 'text-success-600 dark:text-success-400',
  },
  {
    name: 'Insulin Doses',
    value: '8',
    change: '+3',
    changeType: 'increase',
    icon: 'M7 4.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM7 19.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM20 12a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
    color: 'accent',
    iconBg: 'bg-accent-100 dark:bg-accent-900/30',
    iconColor: 'text-accent-600 dark:text-accent-400',
  },
  {
    name: 'Carb Intake',
    value: '200',
    change: '0',
    changeType: 'none',
    icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    color: 'warning',
    iconBg: 'bg-warning-100 dark:bg-warning-900/30',
    iconColor: 'text-warning-600 dark:text-warning-400',
  },
];

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

const showingSampleForm = ref(false);
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
            <div class="mb-8 bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-neutral-100/50 dark:border-neutral-700/50">
                <div class="flex flex-col space-y-4 md:space-y-0 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-1">
                        <h1 class="text-2xl md:text-3xl font-bold text-neutral-900 dark:text-white">{{ greeting }}, {{ $page.props.auth.user.name }}! 👋</h1>
                        <p class="text-sm md:text-base text-neutral-600 dark:text-neutral-400">Here's your diabetes management dashboard for today.</p>
                    </div>
                    <div class="flex-shrink-0">
                        <PrimaryButton class="w-full md:w-auto justify-center" @click="showingSampleForm = true">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 mr-1.5 md:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Add Reading
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div v-for="stat in stats" :key="stat.name" class="bg-white/60 dark:bg-neutral-800/60 backdrop-blur-sm rounded-xl p-4 border border-neutral-200/50 dark:border-neutral-700/50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ stat.name }}</div>
                            <div :class="[stat.iconBg, 'p-1.5 rounded-lg']">
                                <svg :class="[stat.iconColor, 'h-4 w-4']" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2 flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stat.value }}</div>
                            <span v-if="stat.change !== '0'" :class="[
                                stat.changeType === 'increase' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400',
                                'ml-2 text-xs font-medium'
                            ]">
                                {{ stat.changeType === 'increase' ? '↑' : '↓' }} {{ stat.change }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="space-y-6">
                <!-- Blood Glucose Chart -->
                <Card title="Blood Glucose" class="flex-1 flex flex-col" :show-header="true">
                    <BloodGlucoseTrends />
                </Card>

                <!-- Daily Goals & Progress -->
                <Card title="Daily Goals" class="flex-1 flex flex-col" :show-header="true">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 p-2">
                        <!-- Blood Glucose Checks -->
                        <div class="bg-white/60 dark:bg-neutral-800/60 backdrop-blur-sm rounded-xl p-4 border border-neutral-200/50 dark:border-neutral-700/50">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">BG Checks</h3>
                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400">3/5</span>
                            </div>
                            <div class="w-full bg-gray-200/50 dark:bg-gray-700/50 rounded-full h-1.5">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: 60%"></div>
                            </div>
                        </div>

                        <!-- Meals Tracked -->
                        <div class="bg-white/60 dark:bg-neutral-800/60 backdrop-blur-sm rounded-xl p-4 border border-neutral-200/50 dark:border-neutral-700/50">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Meals</h3>
                                <span class="text-xs font-medium text-green-600 dark:text-green-400">2/3</span>
                            </div>
                            <div class="w-full bg-gray-200/50 dark:bg-gray-700/50 rounded-full h-1.5">
                                <div class="bg-green-500 h-1.5 rounded-full" style="width: 66%"></div>
                            </div>
                        </div>

                        <!-- Insulin Doses -->
                        <div class="bg-white/60 dark:bg-neutral-800/60 backdrop-blur-sm rounded-xl p-4 border border-neutral-200/50 dark:border-neutral-700/50">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Insulin</h3>
                                <span class="text-xs font-medium text-purple-600 dark:text-purple-400">4/4</span>
                            </div>
                            <div class="w-full bg-gray-200/50 dark:bg-gray-700/50 rounded-full h-1.5">
                                <div class="bg-purple-600 h-1.5 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>

                        <!-- Activity -->
                        <div class="bg-white/60 dark:bg-neutral-800/60 backdrop-blur-sm rounded-xl p-4 border border-neutral-200/50 dark:border-neutral-700/50">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Activity</h3>
                                <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400">30/60 min</span>
                            </div>
                            <div class="w-full bg-gray-200/50 dark:bg-gray-700/50 rounded-full h-1.5">
                                <div class="bg-yellow-500 h-1.5 rounded-full" style="width: 50%"></div>
                            </div>
                        </div>
                    </div>
                </Card>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6 flex flex-col">
                        <!-- Recent Blood Glucose Readings -->
                        <Card title="Recent Readings" class="flex flex-col" :show-header="true">
                            <div class="flex-1 -mx-1 py-1">
                                <div class="space-y-2">
                                    <div v-for="i in 5" :key="i" class="flex items-center justify-between p-3 rounded-lg hover:bg-neutral-50/50 dark:hover:bg-neutral-700/30 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <div :class="[
                                                'p-1.5 rounded-lg',
                                                [95, 110, 120, 130, 115][i-1] < 100 ? 'bg-green-100/50 dark:bg-green-900/20 text-green-600 dark:text-green-400' :
                                                [95, 110, 120, 130, 115][i-1] < 140 ? 'bg-yellow-100/50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400' :
                                                'bg-red-100/50 dark:bg-red-900/20 text-red-600 dark:text-red-400'
                                            ]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold" :class="{
                                                'text-green-600 dark:text-green-400': [95, 110, 120, 130, 115][i-1] < 100,
                                                'text-yellow-600 dark:text-yellow-400': [95, 110, 120, 130, 115][i-1] >= 100 && [95, 110, 120, 130, 115][i-1] < 140,
                                                'text-red-600 dark:text-red-400': [95, 110, 120, 130, 115][i-1] >= 140
                                            }">
                                                {{ [95, 110, 120, 130, 115][i-1] }} <span class="text-xs font-normal text-gray-500 dark:text-gray-400">mg/dL</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Card>
                        
                        <!-- Next Dose Card -->
                        <Card class="flex flex-col" :show-header="false">
                            <div class="flex items-center">
                                <div class="p-1.5 rounded-lg bg-yellow-100/50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Next Dose</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">7:30 PM</p>
                                </div>
                            </div>
                        </Card>
                    </div>
                    <!-- Right Column -->
                    <div class="space-y-6 flex flex-col">
                        <!-- Upcoming Appointments -->
                        <Card title="Appointments" class="flex-1 flex flex-col" :show-header="true">
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

                        <!-- Quick Actions -->
                        <Card title="Quick Actions" class="h-full flex flex-col" :show-header="true">
                            <div class="p-2 flex-1">
                                <div class="grid grid-cols-2 gap-2">
                                    <button @click="showingSampleForm = true" class="flex flex-col items-center justify-center p-3 rounded-xl bg-white/60 dark:bg-neutral-800/60 backdrop-blur-sm border border-neutral-200/50 dark:border-neutral-700/50 hover:bg-neutral-50/50 dark:hover:bg-neutral-700/30 transition-colors">
                                        <div class="p-1.5 rounded-lg bg-blue-100/50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </div>
                                        <p class="mt-1.5 text-xs font-medium text-gray-700 dark:text-gray-200">Add BG</p>
                                    </button>
                                    
                                    <button class="flex flex-col items-center justify-center p-3 rounded-xl bg-white/60 dark:bg-neutral-800/60 backdrop-blur-sm border border-neutral-200/50 dark:border-neutral-700/50 hover:bg-neutral-50/50 dark:hover:bg-neutral-700/30 transition-colors">
                                        <div class="p-1.5 rounded-lg bg-green-100/50 dark:bg-green-900/20 text-green-600 dark:text-green-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </div>
                                        <p class="mt-1.5 text-xs font-medium text-gray-700 dark:text-gray-200">Add Meal</p>
                                    </button>
                                    
                                    <button class="flex flex-col items-center justify-center p-3 rounded-xl bg-white/60 dark:bg-neutral-800/60 backdrop-blur-sm border border-neutral-200/50 dark:border-neutral-700/50 hover:bg-neutral-50/50 dark:hover:bg-neutral-700/30 transition-colors">
                                        <div class="p-1.5 rounded-lg bg-purple-100/50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </div>
                                        <p class="mt-1.5 text-xs font-medium text-gray-700 dark:text-gray-200">Add Insulin</p>
                                    </button>
                                    
                                    <button class="flex flex-col items-center justify-center p-3 rounded-xl bg-white/60 dark:bg-neutral-800/60 backdrop-blur-sm border border-neutral-200/50 dark:border-neutral-700/50 hover:bg-neutral-50/50 dark:hover:bg-neutral-700/30 transition-colors">
                                        <div class="p-1.5 rounded-lg bg-yellow-100/50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <p class="mt-1.5 text-xs font-medium text-gray-700 dark:text-gray-200">Add Activity</p>
                                    </button>
                                </div>

                                <div class="mt-3">
                                    <button class="w-full flex items-center justify-center px-3 py-2 rounded-xl text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Quick Add
                                    </button>
                                </div>
                            </div>
                        </Card>
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
