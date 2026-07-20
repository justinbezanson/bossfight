<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

interface Game {
    id: number;
    name: string;
}

interface Kid {
    id: number;
    name: string;
}

interface TopGame {
    game: Game;
    total_minutes: number;
    session_count: number;
}

interface RecentSession {
    game: Game;
    kid: Kid;
    start: string;
    end: string;
    duration_minutes: number;
}

interface TopPlayer {
    kid: Kid;
    total_minutes: number;
    session_count: number;
}

defineProps<{
    topGames: TopGame[];
    recentSessions: RecentSession[];
    topPlayers: TopPlayer[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const colors = ['bg-green-700', 'bg-amber-700', 'bg-red-700'];

function formatDuration(minutes: number): string {
    const hours = Math.floor(minutes / 60);
    const mins = Math.round(minutes % 60);

    if (hours === 0) {
        return `${mins}m`;
    }

    if (mins === 0) {
        return `${hours}h`;
    }

    return `${hours}h ${mins}m`;
}

/**
 * Return date string in a more readable format, e.g. "June 15, 2022"
 * @param dateString
 */
function formatDateTime(dateString: string): string {
    const date = new Date(dateString);

    return date.toLocaleDateString();
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="font-bold text-2xl">Top Games</div>
        <div class="grid gap-4 md:grid-cols-3">
            <Card v-for="(item, index) in topGames" :key="item.game.id">
                <CardHeader>
                    <CardTitle :class="colors[index % colors.length]" class="p-4 rounded-lg">{{ item.game.name }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ formatDuration(item.total_minutes) }}</div>
                    <div class="text-sm text-muted-foreground">{{ item.session_count }} session{{ item.session_count !== 1 ? 's' : '' }}</div>
                </CardContent>
            </Card>
            <Card v-if="topGames.length === 0">
                <CardContent class="flex items-center justify-center py-12">
                    <p class="text-muted-foreground">No game data yet.</p>
                </CardContent>
            </Card>
        </div>

        <div class="">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="px-4 py-3 font-medium text-lg">Top Players</div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-sidebar-border/70 text-left bg-gray-800">
                                <th class="px-4 py-3 font-medium">Player</th>
                                <th class="px-4 py-3 font-medium">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(player, index) in topPlayers" :key="index" class="border-b border-sidebar-border/70 last:border-b-0">
                                <td class="px-4 py-3">{{ player.kid.name }}</td>
                                <td class="px-4 py-3">{{ formatDuration(player.total_minutes) }}</td>
                            </tr>
                            <tr v-if="topPlayers.length === 0">
                                <td colspan="5" class="p-4 text-center text-muted-foreground">No player data yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-span-2 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="px-4 py-3 font-medium text-lg">Recent Sessions</div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-sidebar-border/70 text-left bg-gray-800">
                                <th class="px-4 py-3 font-medium">Player</th>
                                <th class="px-4 py-3 font-medium">Game</th>
                                <th class="px-4 py-3 font-medium">Date</th>
                                <th class="px-4 py-3 font-medium">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(session, index) in recentSessions" :key="index" class="border-b border-sidebar-border/70 last:border-b-0">
                                <td class="px-4 py-3">{{ session.kid.name }}</td>
                                <td class="px-4 py-3">{{ session.game.name }}</td>
                                <td class="px-4 py-3">{{ formatDateTime(session.start) }}</td>
                                <td class="px-4 py-3">{{ formatDuration(session.duration_minutes) }}</td>
                            </tr>
                            <tr v-if="recentSessions.length === 0">
                                <td colspan="5" class="p-4 text-center text-muted-foreground">No sessions yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
