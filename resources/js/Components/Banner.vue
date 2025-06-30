<script setup>
import { ref, watchEffect,onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(true);
const bannerType = ref('success');
const message = ref('');
const isVisible = ref(false);

// Banner type definitions with their respective styles
const bannerTypes = {
    success: {
        bg: 'bg-success-50 dark:bg-success-900/30',
        border: 'border-success-200 dark:border-success-800/50',
        text: 'text-success-800 dark:text-success-100',
        icon: 'check-circle',
        iconColor: 'text-success-500 dark:text-success-400',
        iconBg: 'bg-success-100 dark:bg-success-900/50',
        hoverBg: 'hover:bg-success-100 dark:hover:bg-success-900/40',
        ring: 'ring-success-500',
    },
    error: {
        bg: 'bg-error-50 dark:bg-error-900/30',
        border: 'border-error-200 dark:border-error-800/50',
        text: 'text-error-800 dark:text-error-100',
        icon: 'x-circle',
        iconColor: 'text-error-500 dark:text-error-400',
        iconBg: 'bg-error-100 dark:bg-error-900/50',
        hoverBg: 'hover:bg-error-100 dark:hover:bg-error-900/40',
        ring: 'ring-error-500',
    },
    warning: {
        bg: 'bg-warning-50 dark:bg-warning-900/30',
        border: 'border-warning-200 dark:border-warning-800/50',
        text: 'text-warning-800 dark:text-warning-100',
        icon: 'exclamation-triangle',
        iconColor: 'text-warning-500 dark:text-warning-400',
        iconBg: 'bg-warning-100 dark:bg-warning-900/50',
        hoverBg: 'hover:bg-warning-100 dark:hover:bg-warning-900/40',
        ring: 'ring-warning-500',
    },
    info: {
        bg: 'bg-info-50 dark:bg-info-900/30',
        border: 'border-info-200 dark:border-info-800/50',
        text: 'text-info-800 dark:text-info-100',
        icon: 'information-circle',
        iconColor: 'text-info-500 dark:text-info-400',
        iconBg: 'bg-info-100 dark:bg-info-900/50',
        hoverBg: 'hover:bg-info-100 dark:hover:bg-info-900/40',
        ring: 'ring-info-500',
    },
};

// Icons for different banner types
const icons = {
    'check-circle': {
        path: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        viewBox: '0 0 24 24',
    },
    'x-circle': {
        path: 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        viewBox: '0 0 24 24',
    },
    'exclamation-triangle': {
        path: 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
        viewBox: '0 0 24 24',
    },
    'information-circle': {
        path: 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
        viewBox: '0 0 24 24',
    },
    'x-mark': {
        path: 'M6 18L18 6M6 6l12 12',
        viewBox: '0 0 24 24',
    },
};

// Watch for changes to the banner message and type
watchEffect(() => {
    const flash = page.props.jetstream?.flash;
    if (flash?.banner) {
        bannerType.value = flash.bannerStyle || 'success';
        message.value = flash.banner;
        showBanner();
    }
});

// Show banner with animation
const showBanner = () => {
    show.value = true;
    // Trigger the animation after the DOM updates
    setTimeout(() => {
        isVisible.value = true;
    }, 50);
};

// Hide banner with animation
const hideBanner = () => {
    isVisible.value = false;
    // Wait for the animation to complete before hiding the banner
    setTimeout(() => {
        show.value = false;
    }, 300); // Match this with the transition duration
};

// Auto-dismiss after a delay if enabled
const autoDismiss = () => {
    if (bannerType.value !== 'error') {
        setTimeout(() => {
            hideBanner();
        }, 5000);
    }
};

// Start auto-dismiss when component is mounted
onMounted(() => {
    if (show.value && message.value) {
        showBanner();
        autoDismiss();
    }
});
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="-translate-y-full opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition-all duration-300 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="-translate-y-full opacity-0"
    >
        <div
            v-if="show && message"
            class="fixed top-0 left-0 right-0 z-50 shadow-lg"
            :class="[
                bannerTypes[bannerType]?.bg,
                bannerTypes[bannerType]?.border,
                'border-b',
                isVisible ? 'translate-y-0 opacity-100' : '-translate-y-full opacity-0',
            ]"
            role="alert"
            :aria-live="bannerType === 'error' ? 'assertive' : 'polite'"
        >
            <div class="max-w-7xl mx-auto px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <!-- Banner content -->
                    <div class="flex items-center min-w-0 flex-1">
                        <!-- Icon -->
                        <span
                            class="flex-shrink-0 p-2 rounded-full transition-colors"
                            :class="[
                                bannerTypes[bannerType]?.iconBg,
                                'group hover:bg-opacity-75',
                            ]"
                        >
                            <svg
                                v-if="bannerTypes[bannerType]?.icon"
                                class="h-5 w-5 transition-transform group-hover:scale-110"
                                :class="bannerTypes[bannerType]?.iconColor"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.75"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    :d="icons[bannerTypes[bannerType]?.icon]?.path"
                                />
                            </svg>
                        </span>

                        <!-- Message -->
                        <p
                            class="ml-3 font-medium text-sm truncate leading-tight"
                            :class="bannerTypes[bannerType]?.text"
                        >
                            {{ message }}
                        </p>
                    </div>

                    <!-- Dismiss button -->
                    <div class="ml-4 flex-shrink-0 flex">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-full p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all"
                            :class="[
                                bannerTypes[bannerType]?.hoverBg,
                                bannerTypes[bannerType]?.iconColor,
                                'focus:ring-offset-2 focus:ring-current',
                                'hover:scale-110 active:scale-95',
                            ]"
                            :aria-label="$t('Dismiss')"
                            @click="hideBanner"
                        >
                            <span class="sr-only">Dismiss</span>
                            <svg
                                class="h-5 w-5"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
