<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as logsIndex } from '@/routes/logs';

interface Log {
    id: number;
    log_date: string;
    kid: { id: number; name: string } | null;
    game: { id: number; name: string } | null;
    message: string;
}

interface PaginatedLogs {
    data: Log[];
    links: Record<string, string | null>;
}

const { logs } = defineProps<{
    logs: PaginatedLogs;
}>();

defineOptions({
    layout: AppLayout,
    breadcrumbs: [
        {
            title: 'Logs',
            href: logsIndex(),
        },
    ],
});
</script>

<template>
    <Head title="Logs" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <h1 class="text-2xl font-bold">Logs</h1>

        <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-sidebar-border/70 text-left">
                        <th class="px-4 py-3 font-medium">Date</th>
                        <th class="px-4 py-3 font-medium">Kid</th>
                        <th class="px-4 py-3 font-medium">Game</th>
                        <th class="px-4 py-3 font-medium">Message</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logs.data" :key="log.id" class="border-b border-sidebar-border/70 last:border-b-0">
                        <td class="px-4 py-3">{{ log.log_date }}</td>
                        <td class="px-4 py-3">{{ log.kid?.name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ log.game?.name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ log.message }}</td>
                    </tr>
                    <tr v-if="logs.data.length === 0">
                        <td colspan="4" class="p-4 text-center text-muted-foreground">No logs yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
