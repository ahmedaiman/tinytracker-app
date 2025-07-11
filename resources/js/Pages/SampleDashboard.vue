<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, defineComponent } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import StatCard from '@/Components/StatCard.vue';
import ActivityItem from '@/Components/ActivityItem.vue';
import ProjectCard from '@/Components/ProjectCard.vue';
import TaskItem from '@/Components/TaskItem.vue';
import QuickAction from '@/Components/QuickAction.vue';
import SampleForm from './Dashboard/Partials/SampleForm.vue';
import LineChart from '@/Components/LineChart.vue';

const showingSampleForm = ref(false);

const chartData = ref({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
  datasets: [
    {
      label: 'Tasks Completed',
      backgroundColor: 'rgba(56, 198, 246, 0.2)',
      borderColor: '#38c6f6',
      borderWidth: 2,
      pointRadius: 3,
      pointBackgroundColor: '#38c6f6',
      pointBorderColor: '#fff',
      pointHoverRadius: 5,
      pointHoverBackgroundColor: '#fff',
      pointHoverBorderColor: '#38c6f6',
      tension: 0.4,
      data: [65, 59, 80, 81, 56, 55, 40]
    }
  ]
});

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
        display: false,
    },
    tooltip: {
        bodyFont: {
            family: 'Figtree, sans-serif',
        },
        titleFont: {
            family: 'Figtree, sans-serif',
        }
    }
  },
  scales: {
    x: {
      grid: {
        display: false,
        drawBorder: false,
      },
      ticks: {
        color: '#9CA3AF', // neutral-400
        font: {
            family: 'Figtree, sans-serif',
            weight: 400,
        }
      }
    },
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(255, 255, 255, 0.1)', // subtle grid lines for dark mode
        drawBorder: false,
      },
      ticks: {
        color: '#9CA3AF', // neutral-400
        font: {
            family: 'Figtree, sans-serif',
            weight: 400,
        }
      }
    }
  }
});

// Mock data - replace with actual data from your backend
const stats = [
    {
        name: 'Total Tasks',
        value: '24',
        change: '+2',
        changeType: 'increase',
        icon: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
        color: 'primary',
        iconBg: 'bg-primary-100 dark:bg-primary-900/30',
        iconColor: 'text-primary-600 dark:text-primary-400',
    },
    {
        name: 'Completed',
        value: '18',
        change: '+5',
        changeType: 'increase',
        icon: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'success',
        iconBg: 'bg-success-100 dark:bg-success-900/30',
        iconColor: 'text-success-600 dark:text-success-400',
    },
    {
        name: 'In Progress',
        value: '4',
        change: '0',
        changeType: 'none',
        icon: 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'accent',
        iconBg: 'bg-accent-100 dark:bg-accent-900/30',
        iconColor: 'text-accent-600 dark:text-accent-400',
    },
    {
        name: 'Overdue',
        value: '2',
        change: '-1',
        changeType: 'decrease',
        icon: 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
        color: 'danger',
        iconBg: 'bg-danger-100 dark:bg-danger-900/30',
        iconColor: 'text-danger-600 dark:text-danger-400',
    },
];

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
            <div class="mb-8 bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-neutral-100/50 dark:border-neutral-700/50">
                <div class="flex flex-col space-y-4 md:space-y-0 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-1">
                        <h1 class="text-2xl md:text-3xl font-bold text-neutral-900 dark:text-white">{{ greeting }}, {{ $page.props.auth.user.name }}! 👋</h1>
                        <p class="text-sm md:text-base text-neutral-600 dark:text-neutral-400">Here's what's happening with your projects today.</p>
                    </div>
                    <div class="flex-shrink-0">
                        <PrimaryButton class="w-full md:w-auto justify-center" @click="showingSampleForm = true">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 mr-1.5 md:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Task
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard
                        v-for="stat in stats"
                        :key="stat.name"
                        :name="stat.name"
                        :value="stat.value"
                        :change="stat.change"
                        :change-type="stat.changeType"
                        :icon="stat.icon"
                        :color="stat.color"
                        :icon-bg="stat.iconBg"
                        :icon-color="stat.iconColor"
                        class="h-full"
                    />
                </div>
            </div>

            <!-- Main Content -->
            <div class="space-y-6">
                <!-- Task Completion Chart -->
                <Card title="Task Completion" class="flex-1 flex flex-col" :show-header="true">
                    <template #header-actions>
                        <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Last 7 months</span>
                    </template>
                    <div class="h-80 p-4">
                        <LineChart :chart-data="chartData" :chart-options="chartOptions" />
                    </div>
                </Card>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6 flex flex-col">
                        <!-- Recent Activity -->
                        <Card title="Recent Activity" class="flex flex-col" :show-header="true">
                            <template #header-actions>
                                <Link href="#" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors inline-flex items-center">
                                    View all
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </template>
                            <div class="flex-1 overflow-y-auto max-h-[250px] -mx-1 px-1 py-2">
                                <div class="space-y-1 px-3">
                                    <ActivityItem
                                        v-for="activity in recentActivity"
                                        :key="activity.id"
                                        :id="activity.id"
                                        :user="activity.user"
                                        :action="activity.action"
                                        :target="activity.target"
                                        :time="activity.time"
                                        class="px-2 py-2"
                                        @view="() => {}"
                                    />
                                </div>
                            </div>
                        </Card>

                        <!-- Projects Overview -->
                        <Card title="Projects Overview" class="flex-1 flex flex-col" :show-header="true">
                            <template #header-actions>
                                <Link href="#" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors inline-flex items-center">
                                    View all
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </template>
                            <div class="space-y-4">
                                <ProjectCard
                                    v-for="project in projects"
                                    :key="project.id"
                                    :id="project.id"
                                    :name="project.name"
                                    :description="project.description"
                                    :progress="project.progress"
                                    :status="project.status"
                                    :members="project.members"
                                    :due-date="project.dueDate"
                                    :color="project.color"
                                    :icon="project.icon"
                                    class="border border-neutral-200 dark:border-neutral-700 hover:border-neutral-300 dark:hover:border-neutral-600"
                                    @view="() => {}"
                                    @edit="() => {}"
                                    @delete="() => {}"
                                />
                            </div>
                        </Card>
                    </div>
                    <!-- Right Column -->
                    <div class="space-y-6 flex flex-col">
                        <!-- Upcoming Tasks -->
                        <Card title="Upcoming Tasks" class="flex-1 flex flex-col" :show-header="true">
                            <template #header-actions>
                                <Link href="#" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors inline-flex items-center">
                                    View all
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </template>
                            <div class="px-0 py-3 w-full">
                                <div class="space-y-3 w-full">
                                    <div
                                        v-for="task in upcomingTasks"
                                        :key="task.id"
                                        class="transition-transform hover:-translate-y-0.5 w-full"
                                    >
                                        <TaskItem
                                            :id="task.id"
                                            :title="task.title"
                                            :due-date="task.due"
                                            :priority="task.priority"
                                            :project="task.project"
                                            :progress="task.progress"
                                            :completed="false"
                                            show-project
                                            show-actions
                                            class="w-full bg-white/50 dark:bg-neutral-800/30 backdrop-blur-sm hover:shadow-sm transition-shadow"
                                            @view="() => {}"
                                            @edit="() => {}"
                                            @delete="() => {}"
                                            @complete="() => {}"
                                        />
                                    </div>
                                </div>
                            </div>
                        </Card>

                        <!-- Quick Actions -->
                        <Card title="Quick Actions" class="h-full flex flex-col" :show-header="true">
                            <div class="p-3 flex-1">
                                <div class="grid grid-cols-2 gap-3">
                                    <QuickAction
                                        title="New Task"
                                        icon="M12 4v16m8-8H4"
                                        color="primary"
                                        description="Create a new task"
                                        @click="() => {}"
                                    />

                                    <QuickAction
                                        title="Export"
                                        icon="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        color="emerald"
                                        description="Export your data"
                                        @click="() => {}"
                                    />

                                    <QuickAction
                                        title="Settings"
                                        icon="M10.325 4.317c-.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                        color="accent"
                                        description="Configure app settings"
                                        @click="() => {}"
                                    />

                                    <QuickAction
                                        title="Help"
                                        icon="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        color="purple"
                                        description="Get help and support"
                                        @click="() => {}"
                                    />
                                </div>

                                <div class="mt-4">
                                    <PrimaryButton size="sm" @click="showingSampleForm = true">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1.5a4.5 4.5 0 014.5-4.5h3a4.5 4.5 0 014.5 4.5V21zM19 8v3m0 3v-3m-3 0h3m3 0h-3" />
                                </svg>
                                New Task
                            </PrimaryButton>
                                </div>
                            </div>

                            <div class="mt-4">
                                <PrimaryButton size="sm" @click="showingSampleForm = true">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1.5a4.5 4.5 0 014.5-4.5h3a4.5 4.5 0 014.5 4.5V21zM19 8v3m0 3v-3m-3 0h3m3 0h-3" />
                            </svg>
                            New Task
                        </PrimaryButton>
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
