<script setup>
import { ref, reactive, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DialogModal from './DialogModal.vue';
import InputError from './InputError.vue';
import InputLabel from './InputLabel.vue';
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import TextInput from './TextInput.vue';

const emit = defineEmits(['confirmed']);

const props = defineProps({
    title: {
        type: String,
        default: 'Confirm Password',
    },
    content: {
        type: String,
        default: 'For your security, please confirm your password to continue.',
    },
    button: {
        type: String,
        default: 'Confirm',
    },
    buttonVariant: {
        type: String,
        default: 'primary', // 'primary', 'danger', 'success', 'warning'
        validator: (value) => ['primary', 'danger', 'success', 'warning'].includes(value),
    },
    buttonSize: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg'
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    showCancel: {
        type: Boolean,
        default: true,
    },
});

const confirmingPassword = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const error = ref('');
const isProcessing = ref(false);

// Button variants using new color tokens
const buttonVariants = {
    primary: {
        bg: 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-500',
        text: 'text-white',
        icon: 'lock-closed',
    },
    danger: {
        bg: 'bg-error-600 hover:bg-error-700 focus:ring-error-500 dark:bg-error-600 dark:hover:bg-error-700 dark:focus:ring-error-500',
        text: 'text-white',
        icon: 'exclamation-triangle',
    },
    success: {
        bg: 'bg-success-600 hover:bg-success-700 focus:ring-success-500 dark:bg-success-600 dark:hover:bg-success-700 dark:focus:ring-success-500',
        text: 'text-white',
        icon: 'check-circle',
    },
    warning: {
        bg: 'bg-warning-600 hover:bg-warning-700 focus:ring-warning-500 dark:bg-warning-600 dark:hover:bg-warning-700 dark:focus:ring-warning-500',
        text: 'text-neutral-900 dark:text-white',
        icon: 'exclamation',
    },
};

// Icons for different states
const icons = {
    'lock-closed': 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
    'exclamation-triangle': 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
    'check-circle': 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'exclamation': 'M12 9v3.75m-9.303 3.376c-.483.377-.76.94-.765 1.526A7.5 7.5 0 0012 21.75a7.5 7.5 0 007.5-7.5c0-.589-.301-1.135-.811-1.5m-6.39-3.38a3.75 3.75 0 10-5.37-5.37 3.75 3.75 0 005.37 5.37z',
};

const startConfirmingPassword = async () => {
    try {
        const response = await axios.get(route('password.confirmation'));
        if (response.data.confirmed) {
            emit('confirmed');
        } else {
            openModal();
        }
    } catch (error) {
        // If there's an error with the confirmation check, still show the password prompt
        openModal();
    }
};

const openModal = () => {
    confirmingPassword.value = true;
    nextTick(() => {
        passwordInput.value?.focus();
    });
};

const confirmPassword = async () => {
    if (isProcessing.value) return;
    
    error.value = '';
    isProcessing.value = true;

    try {
        await form.post(route('password.confirm'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                emit('confirmed');
            },
            onError: (errors) => {
                error.value = errors.password?.[0] || 'The provided password is incorrect.';
                nextTick(() => {
                    passwordInput.value?.focus();
                    passwordInput.value?.select();
                });
            },
            onFinish: () => {
                form.reset();
                isProcessing.value = false;
            },
        });
    } catch (error) {
        error.value = 'An error occurred. Please try again.';
        isProcessing.value = false;
    }
};

const closeModal = () => {
    if (isProcessing.value) return;
    
    confirmingPassword.value = false;
    form.reset();
    error.value = '';
    
    // Small delay to allow the modal to close before resetting the form
    setTimeout(() => {
        form.reset();
    }, 300);
};

// Handle keyboard events
const handleKeyDown = (event) => {
    if (event.key === 'Enter') {
        event.preventDefault();
        confirmPassword();
    } else if (event.key === 'Escape') {
        closeModal();
    }
};
</script>

<template>
    <span class="inline-flex">
        <span 
            @click="startConfirmingPassword" 
            class="cursor-pointer transition-all duration-200 hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-neutral-800 rounded"
            :aria-label="title"
        >
            <slot />
        </span>

        <DialogModal
            :show="confirmingPassword"
            @close="closeModal"
            max-width="md"
            :closeable="!isProcessing"
        >
            <template #title>
                <div class="flex items-center space-x-3">
                    <div class="p-1.5 rounded-full bg-opacity-10 dark:bg-opacity-20" :class="[
                        buttonVariants[buttonVariant]?.bg.replace('hover:bg-', 'bg-').split(' ')[0],
                        'flex items-center justify-center transition-colors duration-200'
                    ]">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" :class="buttonVariants[buttonVariant]?.text">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icons[buttonVariants[buttonVariant]?.icon]" />
                        </svg>
                    </div>
                    <span :class="['font-medium', buttonVariants[buttonVariant]?.text.includes('text-white') ? 'text-neutral-800 dark:text-white' : buttonVariants[buttonVariant]?.text]">{{ title }}</span>
                </div>
            </template>

            <template #content>
                <div class="mt-4">
                    <p class="text-sm text-neutral-700 dark:text-neutral-300">
                        {{ content }}
                    </p>

                    <div class="mt-4">
                        <InputLabel for="password" value="Password" class="sr-only" />
                        <TextInput
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            placeholder="Enter your password"
                            :class="{ 'border-error-500 focus:border-error-500 focus:ring-error-500 dark:border-error-600 dark:focus:border-error-600 dark:focus:ring-error-600': error }"
                            @keyup.enter="confirmPassword"
                        />
                        <InputError :message="error" class="mt-2" type="error" />
                    </div>
                </div>
            </template>

            <template #footer>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                    <SecondaryButton 
                        @click="closeModal"
                        :disabled="isProcessing"
                        class="transition-colors duration-200"
                    >
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        :class="[
                            'transition-colors duration-200',
                            isProcessing ? 'opacity-75' : 'opacity-100',
                            buttonVariants[buttonVariant]?.bg
                        ]"
                        :disabled="isProcessing"
                        @click="confirmPassword"
                    >
                        <span v-if="isProcessing" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="transition-all">
                                {{ isProcessing ? 'Verifying...' : button }}
                            </span>
                        </span>
                        <span v-else>
                            {{ button }}
                        </span>
                    </PrimaryButton>
                </div>
            </template>
        </DialogModal>
    </span>
</template>
