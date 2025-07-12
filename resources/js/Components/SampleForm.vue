<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Overlay -->
        <div 
          class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" 
          @click="close"
        ></div>

        <!-- Modal content -->
        <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
          <Transition name="modal-slide">
            <div 
              v-if="show" 
              class="relative w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm shadow-sm transition-all border border-neutral-100 dark:border-neutral-700"
            >
              <!-- Modal header -->
              <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-700">
                <div class="flex items-center justify-between">
                  <h3 class="text-xl font-bold text-neutral-900 dark:text-white">
                    Add Blood Glucose Reading
                  </h3>
                  <button 
                    @click="close" 
                    class="text-neutral-400 hover:text-neutral-500 dark:hover:text-neutral-300 transition-colors p-1 -mr-2"
                    aria-label="Close modal"
                  >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
              
              <!-- Form -->
              <form @submit.prevent="submit" class="p-6">
                <div class="space-y-6">
                  <!-- Glucose Level -->
                  <div>
                    <label 
                      for="glucose_level" 
                      class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1"
                    >
                      Blood Glucose Level (mg/dL)
                    </label>
                    <input
                      id="glucose_level"
                      v-model="form.glucose_level"
                      type="number"
                      step="0.1"
                      class="block w-full rounded-lg border-0 bg-white/50 dark:bg-neutral-700/50 py-2.5 pl-4 pr-10 text-neutral-900 dark:text-white placeholder-neutral-400 dark:placeholder-neutral-500 focus:ring-2 focus:ring-primary-500/50 focus:ring-offset-1 dark:focus:ring-offset-neutral-800 sm:text-sm sm:leading-6 transition-all duration-200 border border-neutral-200/50 dark:border-neutral-700/50"
                      placeholder="e.g. 120"
                      required
                    />
                  </div>

                  <!-- Date & Time -->
                  <div>
                    <label 
                      for="reading_time" 
                      class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1"
                    >
                      Date & Time
                    </label>
                    <div class="relative rounded-lg shadow-sm">
                      <input
                        id="reading_time"
                        ref="dateTimeInput"
                        v-model="form.reading_time"
                        type="datetime-local"
                        class="block w-full rounded-lg border-0 bg-white/50 dark:bg-neutral-700/50 py-2.5 pl-4 pr-10 text-neutral-900 dark:text-white placeholder-neutral-400 dark:placeholder-neutral-500 focus:ring-2 focus:ring-primary-500/50 focus:ring-offset-1 dark:focus:ring-offset-neutral-800 sm:text-sm sm:leading-6 transition-all duration-200 border border-neutral-200/50 dark:border-neutral-700/50 [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:right-0 [&::-webkit-calendar-picker-indicator]:w-full [&::-webkit-calendar-picker-indicator]:h-full [&::-webkit-calendar-picker-indicator]:cursor-pointer"
                        style="color-scheme: light dark;"
                        required
                      />
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button 
                          type="button" 
                          @click="openDateTimePicker" 
                          class="text-neutral-400 hover:text-neutral-500 focus:outline-none"
                          aria-label="Open date picker"
                        >
                          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Notes -->
                  <div>
                    <label 
                      for="notes" 
                      class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1"
                    >
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

                <!-- Form actions -->
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
                      <svg 
                        v-if="form.processing" 
                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" 
                        xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 24 24"
                      >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <span>{{ form.processing ? 'Saving...' : 'Save Reading' }}</span>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
/* Fade transition for overlay */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

/* Slide transition for modal content */
.modal-slide-enter-active {
    transition: all 0.2s ease-out;
}

.modal-slide-leave-active {
    transition: all 0.15s ease-in;
}

.modal-slide-enter-from,
.modal-slide-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

/* Button styles */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
    outline: 2px solid transparent;
    outline-offset: 2px;
    padding: 0.625rem 1.25rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.btn-secondary {
    background-color: transparent;
    color: rgb(55 65 81);
    border: 1px solid rgb(209 213 219);
}

.dark .btn-secondary {
    color: rgb(209 213 219);
    border-color: rgb(75 85 99);
}

.btn-secondary:hover {
    background-color: rgb(229 231 235);
}

.dark .btn-secondary:hover {
    background-color: rgb(55 65 81);
}

.btn-secondary:active {
    background-color: rgb(209 213 219);
}

.btn-secondary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-primary {
    background-color: transparent;
    color: rgb(37 99 235);
    border: 1px solid rgb(37 99 235);
}

.btn-primary:hover {
    background-color: rgb(37 99 235);
    color: white;
}

.btn-primary:active {
    background-color: rgb(29 78 216);
}

.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.loading-spinner {
    animation: spin 1s linear infinite;
    margin-left: -0.25rem;
    margin-right: 0.5rem;
    height: 1rem;
    width: 1rem;
    color: white;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>

<script setup>
import { ref, watch, nextTick, onMounted, defineProps, defineEmits } from 'vue';
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

const dateTimeInput = ref(null);

const openDateTimePicker = () => {
    if (dateTimeInput.value) {
        dateTimeInput.value.showPicker();
    }
};

const close = () => {
    emit('close');
};

const submit = () => {
    console.log('Form submitted:', form);
    // For now, just close the form
    close();
};

// Format the initial date time when component mounts
const formatInitialDateTime = () => {
    if (!form.reading_time) {
        form.reading_time = new Date().toISOString().slice(0, 16);
    }
};

// Call it immediately
formatInitialDateTime();

// Reset form when the modal is opened
watch(() => props.show, (newVal) => {
    if (newVal) {
        form.reset();
        form.reading_time = new Date().toISOString().slice(0, 16);
    }
});
</script>
