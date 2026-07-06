<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as kidsIndex } from '@/routes/kids';

interface Kid {
    id: number;
    name: string;
}

const { kids } = defineProps<{
    kids: Kid[];
}>();

defineOptions({
    layout: AppLayout,
    breadcrumbs: [
        {
            title: 'Kids',
            href: kidsIndex(),
        },
    ],
});
</script>

<template>
    <Head title="Kids" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Kids</h1>
        </div>

        <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <div v-for="kid in kids" :key="kid.id" class="flex items-center justify-between border-b border-sidebar-border/70 px-4 py-3 last:border-b-0">
                <span>{{ kid.name }}</span>
                <div class="flex gap-2">
                    <button class="text-sm text-blue-500 hover:underline">Edit</button>
                    <button class="text-sm text-red-500 hover:underline">Delete</button>
                </div>
            </div>

            <div v-if="kids.length === 0" class="p-4 text-center text-sm text-muted-foreground">
                No kids yet.
            </div>
        </div>

        <div class="flex gap-2">
            <input
                v-model="newName"
                type="text"
                placeholder="Kid name"
                class="rounded-md border border-input bg-background px-3 py-2 text-sm"
            />
            <button class="rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground">Add Kid</button>
        </div>
    </div>
</template>
