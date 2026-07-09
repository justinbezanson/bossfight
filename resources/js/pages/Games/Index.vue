<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import {
    TagsInput,
    TagsInputInput,
    TagsInputItem,
    TagsInputItemDelete,
    TagsInputItemText,
} from '@/components/ui/tags-input';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as gamesIndex, store as gameStore, update as gameUpdate, destroy as gameDestroy } from '@/routes/games';

interface Game {
    id: number;
    name: string;
    processes: string[];
}

const { games } = defineProps<{
    games: Game[];
}>();

const addPopoverOpen = ref(false);
const editingGameId = ref<number | null>(null);
const deletingGame = ref<Game | null>(null);

const addProcesses = ref<string[]>([]);
const editProcesses = ref<string[]>([]);

defineOptions({
    layout: AppLayout,
    breadcrumbs: [
        {
            title: 'Games',
            href: gamesIndex(),
        },
    ],
});
</script>

<template>
    <Head title="Games" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Games</h1>
            <Popover v-model:open="addPopoverOpen">
                <PopoverTrigger as-child>
                    <Button>Add Game</Button>
                </PopoverTrigger>
                <PopoverContent class="w-96" align="end">
                    <Form
                        v-bind="gameStore.form()"
                        reset-on-success
                        @success="addPopoverOpen = false"
                        v-slot="{ errors, processing }"
                        class="grid gap-4"
                    >
                        <div class="space-y-2">
                            <h4 class="font-medium leading-none">New Game</h4>
                            <p class="text-sm text-muted-foreground">Add a new game to your list.</p>
                        </div>
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" name="name" placeholder="Game name" required />
                            <InputError :message="errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Processes</Label>
                            <TagsInput v-model="addProcesses">
                                <TagsInputItem v-for="item in addProcesses" :key="item" :value="item">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Add process name..." />
                            </TagsInput>
                            <input type="hidden" name="processes" :value="JSON.stringify(addProcesses)" />
                            <InputError :message="errors.processes" />
                        </div>
                        <Button type="submit" :disabled="processing">
                            {{ processing ? 'Adding...' : 'Add Game' }}
                        </Button>
                    </Form>
                </PopoverContent>
            </Popover>
        </div>

        <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <div v-for="game in games" :key="game.id" class="flex items-center justify-between border-b border-sidebar-border/70 px-4 py-3 last:border-b-0">
                <div class="flex flex-col gap-1">
                    <span>{{ game.name }}</span>
                    <div class="flex flex-wrap gap-1">
                        <span
                            v-for="process in game.processes"
                            :key="process"
                            class="inline-flex items-center rounded-md bg-secondary px-2 py-0.5 text-xs font-medium text-secondary-foreground"
                        >
                            {{ process }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2 shrink-0">
                    <Popover
                        :open="editingGameId === game.id"
                        @update:open="(open) => { editingGameId = open ? game.id : null; if (open) editProcesses = [...game.processes] }"
                    >
                        <PopoverTrigger as-child>
                            <Button variant="outline" size="sm">Edit</Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-96" align="end">
                            <Form
                                v-bind="gameUpdate.form({ game: game.id })"
                                reset-on-success
                                @success="editingGameId = null"
                                v-slot="{ errors, processing }"
                                class="grid gap-4"
                            >
                                <div class="space-y-2">
                                    <h4 class="font-medium leading-none">Edit Game</h4>
                                    <p class="text-sm text-muted-foreground">Update the game details.</p>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="edit-name">Name</Label>
                                    <Input id="edit-name" name="name" :default-value="game.name" placeholder="Game name" required />
                                    <InputError :message="errors.name" />
                                </div>
                                <div class="grid gap-2">
                                    <Label>Processes</Label>
                                    <TagsInput v-model="editProcesses">
                                        <TagsInputItem v-for="item in editProcesses" :key="item" :value="item">
                                            <TagsInputItemText />
                                            <TagsInputItemDelete />
                                        </TagsInputItem>
                                        <TagsInputInput placeholder="Add process name..." />
                                    </TagsInput>
                                    <input type="hidden" name="processes" :value="JSON.stringify(editProcesses)" />
                                    <InputError :message="errors.processes" />
                                </div>
                                <Button type="submit" :disabled="processing">
                                    {{ processing ? 'Saving...' : 'Save' }}
                                </Button>
                            </Form>
                        </PopoverContent>
                    </Popover>

                    <Dialog :open="deletingGame?.id === game.id" @update:open="(open) => { deletingGame = open ? game : null }">
                        <DialogTrigger as-child>
                            <Button variant="destructive" size="sm">Delete</Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Delete Game</DialogTitle>
                                <DialogDescription>
                                    Are you sure you want to delete <strong>{{ game.name }}</strong>? This action cannot be undone.
                                </DialogDescription>
                            </DialogHeader>
                            <Form
                                v-bind="gameDestroy.form({ game: game.id })"
                                reset-on-success
                                @success="deletingGame = null"
                                v-slot="{ errors, processing }"
                            >
                                <InputError :message="errors.game" />
                                <DialogFooter class="mt-6">
                                    <Button variant="outline" @click="deletingGame = null" :disabled="processing">
                                        Cancel
                                    </Button>
                                    <Button type="submit" variant="destructive" :disabled="processing">
                                        {{ processing ? 'Deleting...' : 'Delete' }}
                                    </Button>
                                </DialogFooter>
                            </Form>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>

            <div v-if="games.length === 0" class="p-4 text-center text-sm text-muted-foreground">
                No games yet.
            </div>
        </div>
    </div>
</template>
