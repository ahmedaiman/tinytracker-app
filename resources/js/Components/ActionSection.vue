<script setup>
import SectionTitle from './SectionTitle.vue';
</script>

<template>
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="space-y-2">
            <SectionTitle>
                <template #title>
                    <slot name="title" />
                </template>
                <template #description>
                    <slot name="description" />
                </template>
            </SectionTitle>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="overflow-hidden bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md dark:shadow-neutral-900/50">
                <div class="p-6 sm:p-8">
                    <div class="space-y-6">
                        <slot name="content" />
                    </div>
                </div>

                <div 
                    v-if="$slots.footer" 
                    class="bg-neutral-50 dark:bg-neutral-700/30 px-6 py-4 border-t border-neutral-100 dark:border-neutral-700"
                >
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0 sm:space-x-4">
                        <div class="text-sm text-neutral-500 dark:text-neutral-400">
                            <slot name="footer-description" />
                        </div>
                        <div class="flex items-center space-x-3">
                            <slot name="footer" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
