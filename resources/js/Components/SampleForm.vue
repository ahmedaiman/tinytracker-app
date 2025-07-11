<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="close"></div>

        <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
            <div class="relative w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm shadow-sm transition-all border border-neutral-100 dark:border-neutral-700">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-neutral-900 dark:text-white">Add Blood Glucose Reading</h3>
                        <button @click="close" class="text-neutral-400 hover:text-neutral-500 dark:hover:text-neutral-300 transition-colors p-1 -mr-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-6">
                    
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <div class="space-y-1">
                                <label for="glucose_level" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    Blood Glucose Level (mg/dL)
                                </label>
                                <div class="relative rounded-lg shadow-sm">
                                    <input
                                        id="glucose_level"
                                        v-model="form.glucose_level"
                                        type="number"
                                        step="0.1"
                                        class="block w-full rounded-lg border-0 bg-white/50 dark:bg-neutral-700/50 py-2.5 pl-4 pr-12 text-neutral-900 dark:text-white placeholder-neutral-400 dark:placeholder-neutral-500 focus:ring-2 focus:ring-primary-500/50 focus:ring-offset-1 dark:focus:ring-offset-neutral-800 sm:text-sm sm:leading-6 transition-all duration-200 border border-neutral-200/50 dark:border-neutral-700/50"
                                        placeholder="e.g. 120"
                                        required
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-neutral-500 dark:text-neutral-400 text-sm">mg/dL</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label for="reading_time" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    Date & Time
                                </label>
                                <input
                                    id="reading_time"
                                    v-model="form.reading_time"
                                    type="datetime-local"
                                    class="block w-full rounded-lg border-0 bg-white/50 dark:bg-neutral-700/50 py-2.5 px-4 text-neutral-900 dark:text-white placeholder-neutral-400 dark:placeholder-neutral-500 focus:ring-2 focus:ring-primary-500/50 focus:ring-offset-1 dark:focus:ring-offset-neutral-800 sm:text-sm sm:leading-6 transition-all duration-200 border border-neutral-200/50 dark:border-neutral-700/50"
                                    required
                                />
                            </div>

                            <div class="space-y-1">
                                <label for="notes" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    Notes (Optional)
                                </label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="block w-full rounded-lg border-0 bg-white/50 dark:bg-neutral-700/50 py-2.5 px-4 text-neutral-900 dark:text-white placeholder-neutral-400 dark:placeholder-neutral-500 focus:ring-2 focus:ring-primary-500/50 focus:ring-offset-1 dark:focus:ring-offset-neutral-800 sm:text-sm sm:leading-6 transition-all duration-200 border border-neutral-200/50 dark:border-neutral-700/50"
                                    placeholder="Add any additional notes..."
                                ></textarea>
                            </div>
                        </div>

                        <div class="mt-8 pt-4 border-t border-neutral-100 dark:border-neutral-700">
                            <div class="flex justify-end space-x-3">
                                <button
                                    type="button"
                                    @click="close"
                                    class="inline-flex items-center justify-center rounded-lg font-semibold uppercase tracking-wider transition-all duration-200 focus:outline-none bg-transparent hover:bg-gray-200 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 active:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md px-5 py-2.5 text-sm"
                                >
                                    Cancel
                                </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center justify-center rounded-lg font-semibold uppercase tracking-wider transition-all duration-200 focus:outline-none bg-transparent hover:bg-primary-600 text-primary-600 hover:text-white border border-primary-600 active:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md px-5 py-2.5 text-sm"
                                :class="{ 'opacity-60 cursor-not-allowed': form.processing }"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Saving...' : 'Save Reading' }}</span>
                            </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

const form = useForm({
    glucose_level: '',
    reading_time: new Date().toISOString().slice(0, 16),
    notes: ''
});

const close = () => {
    emit('close');
};

const submit = () => {
    // You can add form submission logic here
    console.log('Form submitted:', form);
    // For now, just close the form
    close();
};

// Reset form when the modal is opened
watch(() => props.show, (newVal) => {
    if (newVal) {
        form.reset();
        form.reading_time = new Date().toISOString().slice(0, 16);
    }
});
</script>
