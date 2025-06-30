<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import {
    UserCircleIcon,
    LockClosedIcon,
    ShieldCheckIcon,
    ComputerDesktopIcon,
    TrashIcon
} from '@heroicons/vue/24/outline';
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import Card from '@/Components/Card.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import StatCard from '@/Components/StatCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});

const activeSection = ref('profile');

// User stats for profile page
const userStats = [
    {
        name: 'Tasks Created',
        value: '12',
        icon: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
        color: 'primary',
        iconBg: 'bg-primary-100 dark:bg-primary-900/30',
        iconColor: 'text-primary-600 dark:text-primary-400',
    },
    {
        name: 'Completed',
        value: '8',
        icon: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'success',
        iconBg: 'bg-success-100 dark:bg-success-900/30',
        iconColor: 'text-success-600 dark:text-success-400',
    },
    {
        name: 'Projects',
        value: '3',
        icon: 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z',
        color: 'accent',
        iconBg: 'bg-accent-100 dark:bg-accent-900/30',
        iconColor: 'text-accent-600 dark:text-accent-400',
    },
    {
        name: 'Account Age',
        value: getAccountAge(),
        icon: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
        color: 'info',
        iconBg: 'bg-info-100 dark:bg-info-900/30',
        iconColor: 'text-info-600 dark:text-info-400',
    },
];

// Calculate account age in days
function getAccountAge() {
    const page = usePage();
    if (!page?.props?.auth?.user?.created_at) return '0 days';
    const createdDate = new Date(page.props.auth.user.created_at);
    const today = new Date();
    const diffTime = Math.abs(today - createdDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays + ' days';
}
const navigation = [
    { id: 'profile', name: 'Profile', icon: UserCircleIcon },
    { id: 'password', name: 'Password', icon: LockClosedIcon },
    { id: 'two-factor', name: 'Two-Factor Auth', icon: ShieldCheckIcon },
    { id: 'sessions', name: 'Browser Sessions', icon: ComputerDesktopIcon },
    { id: 'delete-account', name: 'Delete Account', icon: TrashIcon, class: 'text-red-600 dark:text-red-400' },
].filter(item => {
    if (item.id === 'password' && !props.jetstream?.canUpdatePassword) return false;
    if (item.id === 'two-factor' && !props.jetstream?.canManageTwoFactorAuthentication) return false;
    if (item.id === 'delete-account' && !props.jetstream?.hasAccountDeletionFeatures) return false;
    return true;
});

const scrollTo = (id) => {
    const element = document.getElementById(id);
    if (element) {
        activeSection.value = id;
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

const handleScroll = () => {
    const sections = navigation.map(item => document.getElementById(item.id)).filter(Boolean);
    const scrollPosition = window.scrollY + 100;

    for (const section of sections) {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.offsetHeight;

        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
            activeSection.value = section.id;
            break;
        }
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    // Set initial active section based on hash
    if (window.location.hash) {
        const id = window.location.hash.substring(1);
        if (navigation.some(item => item.id === id)) {
            activeSection.value = id;
            setTimeout(() => {
                scrollTo(id);
            }, 100);
        }
    }
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <AppLayout title="Profile">
        <Head>
            <title>My Profile - TinyTracker</title>
        </Head>

        <!-- Background Elements -->
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-50 to-primary-100 dark:from-neutral-900 dark:to-neutral-800"></div>
            <div class="absolute inset-0 opacity-10 dark:opacity-5 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]">
            </div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 py-6 max-w-7xl">
            <!-- Header -->
            <div class="mb-8 bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-neutral-100/50 dark:border-neutral-700/50">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-2xl font-bold text-primary-600 dark:text-primary-400 flex-shrink-0">
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate">
                                {{ $page.props.auth.user.name }}
                            </h1>
                            <p class="text-gray-600 dark:text-gray-300 mt-1">
                                {{ $page.props.auth.user.email }}
                            </p>
                            <div class="flex flex-wrap gap-4 mt-2 text-sm">
                                <div class="flex items-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Member since {{ new Date($page.props.auth.user.created_at).toLocaleDateString() }}
                                </div>
                                <div v-if="$page.props.auth.user.two_factor_confirmed_at" class="flex items-center text-success-600 dark:text-success-400">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    2FA Enabled
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <PrimaryButton>
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit Profile
                        </PrimaryButton>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="mt-6 mb-8 grid grid-cols-2 lg:grid-cols-4 gap-4">
                <StatCard
                    v-for="stat in userStats"
                    :key="stat.name"
                    :name="stat.name"
                    :value="stat.value"
                    :icon="stat.icon"
                    :color="stat.color"
                    :icon-bg="stat.iconBg"
                    :icon-color="stat.iconColor"
                    class="h-full"
                />
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 gap-6">
                <div class="space-y-6">
                    <!-- Profile Information -->
                    <section id="profile" class="scroll-mt-24">
                        <SectionTitle>Profile Settings</SectionTitle>
                        <div class="bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm rounded-xl p-6 shadow-sm border border-neutral-100/50 dark:border-neutral-700/50">
                            <div class="mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Profile Information</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update your account's profile information and email address.</p>
                            </div>
                            <UpdateProfileInformationForm :user="$page.props.auth.user" />
                        </div>
                    </section>

                    <!-- Update Password -->
                    <section v-if="$page.props.jetstream.canUpdatePassword" id="password" class="scroll-mt-24">
                        <SectionTitle>Security</SectionTitle>
                        <div class="bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm rounded-xl p-6 shadow-sm border border-neutral-100/50 dark:border-neutral-700/50">
                            <div class="mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Update Password</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ensure your account is using a long, random password to stay secure.</p>
                            </div>
                            <UpdatePasswordForm />
                        </div>
                    </section>

                    <!-- Two Factor Authentication -->
                    <section v-if="$page.props.jetstream.canManageTwoFactorAuthentication" id="two-factor" class="scroll-mt-24">
                        <SectionTitle>Two-Factor Authentication</SectionTitle>
                        <div class="bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm rounded-xl p-6 shadow-sm border border-neutral-100/50 dark:border-neutral-700/50">
                            <div class="mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Two-Factor Authentication</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add additional security to your account using two-factor authentication.</p>
                            </div>
                            <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" />
                        </div>
                    </section>

                    <!-- Browser Sessions -->
                    <section id="sessions" class="scroll-mt-24">
                        <SectionTitle>Sessions</SectionTitle>
                        <div class="bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm rounded-xl p-6 shadow-sm border border-neutral-100/50 dark:border-neutral-700/50">
                            <div class="mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Browser Sessions</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage and log out your active sessions on other browsers and devices.</p>
                            </div>
                            <LogoutOtherBrowserSessionsForm :sessions="sessions" />
                        </div>
                    </section>

                    <!-- Delete Account -->
                    <section v-if="$page.props.jetstream.hasAccountDeletionFeatures" id="delete-account" class="scroll-mt-24">
                        <SectionTitle class="text-danger-600 dark:text-danger-400">Danger Zone</SectionTitle>
                        <div class="bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm rounded-xl p-6 shadow-sm border border-red-100/50 dark:border-red-900/30">
                            <div class="mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delete Account</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Permanently delete your account and all of its data.</p>
                            </div>
                            <DeleteUserForm />
                        </div>
                    </section>
                </div>
            </div>
        </div>
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

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
