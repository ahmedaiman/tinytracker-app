<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div class="w-full space-y-8">
        <!-- Welcome Header -->
        <div class="p-8 bg-gradient-to-r from-lemon_chiffon-50 to-light_blue-50 rounded-2xl shadow-inner border border-lemon_chiffon-200">
            <div class="flex flex-col md:flex-row items-center text-center md:text-left">
                <div class="mb-6 md:mb-0 md:mr-8">
                    <ApplicationLogo class="h-16 w-auto text-peach-600" />
                </div>
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-peach-600 to-light_blue-600 bg-clip-text text-transparent">
                        Welcome to TinyTrack
                    </h1>
                    <p class="mt-3 text-lg text-peach-700 max-w-2xl">
                        Manage your tasks and projects with our intuitive dashboard. Stay organized, be productive, and achieve more every day.
                    </p>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Getting Started Card -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-lemon_chiffon-50 to-lemon_chiffon-100 p-6 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border-2 border-lemon_chiffon-200 hover:border-peach-300">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-peach-200 rounded-full opacity-10 group-hover:opacity-20 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-start">
                        <div class="bg-peach-100 p-3 rounded-xl shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-peach-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h2 class="text-xl font-bold text-peach-800 mb-2">
                                <a href="#" class="hover:text-peach-600 transition-colors before:absolute before:inset-0">Getting Started</a>
                            </h2>
                            <p class="text-peach-700 text-sm leading-relaxed">
                                New to TinyTrack? Our comprehensive documentation will guide you through the setup process and help you make the most of our features.
                            </p>
                            <a href="#" class="mt-4 inline-flex items-center text-sm font-medium text-peach-600 hover:text-peach-700 transition-colors group">
                                View documentation
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 size-4 fill-peach-500 group-hover:translate-x-1 transition-transform">
                                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Video Tutorials Card -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-light_blue-50 to-light_blue-100 p-6 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border-2 border-light_blue-200 hover:border-light_blue-300">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-light_blue-200 rounded-full opacity-10 group-hover:opacity-20 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-start">
                        <div class="bg-light_blue-100 p-3 rounded-xl shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-light_blue-600">
                                <path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h2 class="text-xl font-bold text-light_blue-800 mb-2">
                                <a href="#" class="hover:text-light_blue-600 transition-colors before:absolute before:inset-0">Video Tutorials</a>
                            </h2>
                            <p class="text-light_blue-700 text-sm leading-relaxed">
                                Watch our video tutorials to learn how to use TinyTrack effectively and discover tips and tricks for better task management.
                            </p>
                            <a href="#" class="mt-4 inline-flex items-center text-sm font-medium text-light_blue-600 hover:text-light_blue-700 transition-colors group">
                                Watch tutorials
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 size-4 fill-light_blue-500 group-hover:translate-x-1 transition-transform">
                                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Card -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-celadon-50 to-celadon-100 p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border-2 border-celadon-200 hover:border-celadon-300 md:col-span-2">
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-celadon-200 rounded-full opacity-10 group-hover:opacity-20 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row items-center">
                        <div class="bg-celadon-100 p-4 rounded-xl shadow-inner mb-6 md:mb-0 md:mr-8">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-8 stroke-celadon-700">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <div class="text-center md:text-left">
                            <h2 class="text-2xl font-bold text-celadon-800 mb-3">
                                Your Security is Our Priority
                            </h2>
                            <p class="text-celadon-700 mb-4 max-w-3xl">
                                We use industry-standard encryption and security practices to keep your data safe and protected. Your privacy matters to us, and we're committed to maintaining the highest standards of data protection.
                            </p>
                            <div class="flex flex-wrap justify-center md:justify-start gap-4">
                                <a href="#" class="px-4 py-2 bg-celadon-600 hover:bg-celadon-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Security Overview
                                </a>
                                <a href="#" class="px-4 py-2 border-2 border-celadon-200 hover:border-celadon-300 text-celadon-700 hover:bg-celadon-50 text-sm font-medium rounded-lg transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                    Privacy Policy
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
