<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';

defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    }
});
</script>

<template>
    <Head title="Welcome to TinyTrack" />

    <!-- Main Container -->
    <div class="min-h-screen bg-gradient-to-br from-lemon_chiffon-50 to-light_blue-50 text-gray-800 dark:from-gray-900 dark:to-gray-800 dark:text-gray-200">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-0 right-0 w-72 h-72 bg-peach-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 dark:opacity-10 dark:bg-peach-700"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-light_blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 dark:opacity-10 dark:bg-light_blue-800"></div>
            <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-flax-100 rounded-full mix-blend-multiply filter blur-3xl opacity-10 dark:opacity-5 dark:bg-flax-800"></div>
        </div>

        <!-- Header -->
        <header class="relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <ApplicationMark class="h-10 w-auto text-light_blue-600 dark:text-light_blue-400" />
                        <span class="text-2xl font-bold bg-gradient-to-r from-light_blue-600 to-peach-500 bg-clip-text text-transparent">
                            TinyTrack
                        </span>
                    </div>

                    <nav v-if="canLogin" class="flex items-center space-x-3">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="px-5 py-2 text-sm font-medium rounded-full transition-all duration-200 hover:bg-light_blue-50 hover:text-light_blue-700 dark:hover:bg-gray-800 dark:hover:text-light_blue-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-light_blue-300"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="px-5 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-full transition-all duration-200 hover:bg-light_blue-50 hover:text-light_blue-700 dark:hover:bg-gray-800 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-light_blue-300"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="px-5 py-2 text-sm font-medium text-white bg-gradient-to-r from-peach-500 to-flax-500 rounded-full shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-peach-300"
                            >
                                Get Started
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-24">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold tracking-tight">
                        <span class="block">Track Your Time,</span>
                        <span class="block bg-gradient-to-r from-peach-500 to-flax-500 bg-clip-text text-transparent mt-2">
                            Effortlessly
                        </span>
                    </h1>
                    <p class="mt-6 text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        TinyTrack helps you manage your time with a clean, intuitive interface and powerful analytics to boost your productivity.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-8 py-3 text-base font-medium text-white bg-gradient-to-r from-peach-500 to-flax-500 rounded-full shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-peach-300"
                        >
                            Start Free Trial
                        </Link>
                        <Link
                            href="#features"
                            class="px-8 py-3 text-base font-medium text-peach-700 dark:text-peach-300 bg-peach-100 dark:bg-peach-900/30 rounded-full hover:bg-peach-200 dark:hover:bg-peach-800/50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-peach-300"
                        >
                            Learn More
                        </Link>
                    </div>
                </div>

                <!-- App Preview -->
                <div class="mt-16 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden max-w-4xl mx-auto border border-gray-100 dark:border-gray-700">
                    <div class="p-1 bg-gradient-to-r from-light_blue-200 via-peach-200 to-flax-200 dark:from-light_blue-900/50 dark:via-peach-900/50 dark:to-flax-900/50 rounded-xl">
                        <div class="bg-white dark:bg-gray-800/80 p-6 rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Today's Tasks</h3>
                                <span class="px-2 py-1 text-xs font-semibold text-celadon-800 bg-celadon-100 dark:bg-celadon-900/50 dark:text-celadon-200 rounded-full">
                                    3 Active
                                </span>
                            </div>

                            <!-- Task List -->
                            <div class="space-y-3">
                                <div v-for="i in 3" :key="i" class="flex items-center p-3 bg-white dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <div class="flex-shrink-0 h-5 w-5 rounded-full border-2 border-light_blue-300 dark:border-light_blue-600 flex items-center justify-center">
                                        <div class="h-2.5 w-2.5 rounded-full bg-light_blue-500 dark:bg-light_blue-400"></div>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">Task {{ i }}</div>
                                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                            <span>0:00 / 2:00</span>
                                            <span class="mx-2">•</span>
                                            <span>In Progress</span>
                                        </div>
                                    </div>
                                    <button class="p-1 text-gray-400 hover:text-peach-500 dark:hover:text-peach-400 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="relative">
                                    <input type="text" placeholder="Add a new task..." class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-gray-700/50 shadow-sm focus:border-peach-300 focus:ring focus:ring-peach-200 focus:ring-opacity-50 dark:focus:ring-peach-800/50 pl-10 py-2 text-sm" />
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <section id="features" class="py-16 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Powerful Features</h2>
                        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                            Everything you need to manage your time effectively and boost your productivity.
                        </p>
                    </div>

                    <div class="mt-16 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                        <!-- Feature 1 -->
                        <div class="bg-white/80 dark:bg-gray-800/80 p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100 dark:border-gray-700">
                            <div class="w-12 h-12 bg-light_blue-100 dark:bg-light_blue-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-light_blue-600 dark:text-light_blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Time Tracking</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Track time spent on tasks with a simple click. Start, pause, and stop timers as you work.
                            </p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="bg-white/80 dark:bg-gray-800/80 p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100 dark:border-gray-700">
                            <div class="w-12 h-12 bg-peach-100 dark:bg-peach-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-peach-600 dark:text-peach-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Analytics</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Gain insights into your productivity with detailed reports and visualizations of your time usage.
                            </p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="bg-white/80 dark:bg-gray-800/80 p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100 dark:border-gray-700">
                            <div class="w-12 h-12 bg-flax-100 dark:bg-flax-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-flax-600 dark:text-flax-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Billing</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Track billable hours and generate invoices for clients with ease.
                            </p>
                        </div>

                        <!-- Feature 4 -->
                        <div class="bg-white/80 dark:bg-gray-800/80 p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100 dark:border-gray-700">
                            <div class="w-12 h-12 bg-celadon-100 dark:bg-celadon-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-celadon-600 dark:text-celadon-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Team Collaboration</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Invite team members, assign tasks, and track everyone's time in one place.
                            </p>
                        </div>

                        <!-- Feature 5 -->
                        <div class="bg-white/80 dark:bg-gray-800/80 p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100 dark:border-gray-700">
                            <div class="w-12 h-12 bg-light_blue-100 dark:bg-light_blue-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-light_blue-600 dark:text-light_blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Secure & Private</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Your data is encrypted and secure. We take your privacy seriously.
                            </p>
                        </div>

                        <!-- Feature 6 -->
                        <div class="bg-white/80 dark:bg-gray-800/80 p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100 dark:border-gray-700">
                            <div class="w-12 h-12 bg-peach-100 dark:bg-peach-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-peach-600 dark:text-peach-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Customizable Dashboard</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Tailor your dashboard to show the metrics and data that matter most to you.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="py-16 bg-gradient-to-r from-light_blue-50 to-peach-50 dark:from-gray-800 dark:to-gray-900">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Ready to boost your productivity?</h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Join thousands of professionals who trust TinyTrack to manage their time effectively.
                    </p>
                    <div class="mt-8">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-8 py-3 text-base font-medium text-white bg-gradient-to-r from-peach-500 to-flax-500 rounded-full shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-peach-300"
                        >
                            Get Started for Free
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex justify-center md:justify-start space-x-6">
                        <a href="#" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">GitHub</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                    <div class="mt-8 md:mt-0 text-center md:text-right">
                        <p class="text-base text-gray-400 dark:text-gray-500">
                            &copy; 2023 TinyTrack. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
