<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as logsIndex } from '@/routes/logs';

interface Log {
    id: number;
    log_date: string;
    kid: { id: number; name: string } | null;
    game: { id: number; name: string } | null;
    message: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedLogs {
    data: Log[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
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

function goToPage(url: string | null) {
    if (url) {
        router.visit(url);
    }
}
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

        <div v-if="logs.last_page > 1" class="flex gap-1">
            <Button
                variant="ghost"
                size="sm"
                :disabled="logs.current_page === 1"
                @click="goToPage(logs.links[0].url)"
            >
                Previous
            </Button>

            <Button
                v-for="link in logs.links.slice(1, -1)"
                :key="link.label"
                :variant="link.active ? 'default' : 'ghost'"
                size="sm"
                :disabled="!link.url"
                @click="goToPage(link.url)"
            >
                {{ link.label }}
            </Button>

            <Button
                variant="ghost"
                size="sm"
                :disabled="logs.current_page === logs.last_page"
                @click="goToPage(logs.links[logs.links.length - 1].url)"
            >
                Next
            </Button>
        </div>
    </div>
</template>
