<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Radio from '@/Components/Radio.vue';
import Toggle from '@/Components/Toggle.vue';
import DatePicker from '@/Components/DatePicker.vue';
import FileUpload from '@/Components/FileUpload.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    title: '',
    description: '',
    priority: 'low',
    dueDate: null,
    attachments: [],
    sendNotifications: true,
    status: 'todo',
    termsAccepted: false,
});

const submit = () => {
    // Handle form submission logic here
    console.log('Form submitted:', form.data());
    closeModal();
};

const closeModal = () => {
    emit('close');
};
</script>

<template>
    <DialogModal :show="show" max-width="2xl" @close="closeModal">
        <template #title>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                Create New Task
            </h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Fill out the details below to create a new task.
            </p>
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <div>
                    <div class="space-y-6">
                        <!-- Task Details Section -->
                        <div class="p-4 border rounded-lg dark:border-gray-700">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-4">Task Details</h4>
                            <div class="space-y-4">
                                <!-- Task Title -->
                                <div>
                                    <InputLabel for="title" value="Task Title" />
                                    <TextInput
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="Enter task title"
                                        required
                                        :error="form.errors.title"
                                    />
                                </div>

                                <!-- Task Description -->
                                <div>
                                    <InputLabel for="description" value="Description" />
                                    <Textarea
                                        id="description"
                                        v-model="form.description"
                                        class="mt-1 block w-full"
                                        placeholder="Enter task description"
                                        rows="3"
                                        :error="form.errors.description"
                                    />
                                </div>

                                <!-- Priority & Due Date -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="priority" value="Priority" />
                                        <SelectInput
                                            id="priority"
                                            v-model="form.priority"
                                            :options="['low', 'medium', 'high']"
                                            class="mt-1 block w-full"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel for="due-date" value="Due Date" />
                                        <DatePicker
                                            id="due-date"
                                            v-model="form.dueDate"
                                            class="mt-1 block w-full"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments Section -->
                        <div class="p-4 border rounded-lg dark:border-gray-700">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-4">Attachments</h4>
                            <div>
                                <InputLabel for="attachments" value="Attachments" class="sr-only"/>
                                <FileUpload
                                    id="attachments"
                                    v-model="form.attachments"
                                    multiple
                                    class="mt-1"
                                />
                            </div>
                        </div>

                        <!-- Settings Section -->
                        <div class="p-4 border rounded-lg dark:border-gray-700">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-4">Settings & Status</h4>
                            <div class="space-y-4">
                                <!-- Notifications -->
                                <div class="flex items-center justify-between">
                                    <InputLabel for="notifications" value="Send Notifications" />
                                    <Toggle id="notifications" v-model="form.sendNotifications" />
                                </div>

                                <!-- Status -->
                                <fieldset>
                                    <legend class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</legend>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <Radio v-model="form.status" value="todo" name="status">To Do</Radio>
                                        <Radio v-model="form.status" value="in-progress" name="status">In Progress</Radio>
                                        <Radio v-model="form.status" value="done" name="status">Done</Radio>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        
                        <!-- Terms Section -->
                        <div class="p-4 border rounded-lg dark:border-gray-700">
                            <div class="flex items-center">
                                <Checkbox id="terms" v-model="form.termsAccepted" required />
                                <InputLabel for="terms" class="ms-3">I accept the terms and conditions</InputLabel>
                            </div>
                            <InputError :message="form.errors.termsAccepted" class="mt-2" />
                        </div>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <DangerButton @click="closeModal">Cancel</DangerButton>
            <PrimaryButton @click="submit" class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Create Task
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
