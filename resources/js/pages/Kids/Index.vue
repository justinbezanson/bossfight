<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as kidsIndex, store as kidStore, update as kidUpdate, destroy as kidDestroy } from '@/routes/kids';

interface Kid {
    id: number;
    name: string;
}

const { kids } = defineProps<{
    kids: Kid[];
}>();

const addPopoverOpen = ref(false);
const editingKidId = ref<number | null>(null);
const deletingKid = ref<Kid | null>(null);

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
            <Popover v-model:open="addPopoverOpen">
                <PopoverTrigger as-child>
                    <Button>Add Kid</Button>
                </PopoverTrigger>
                <PopoverContent class="w-80" align="end">
                    <Form
                        v-bind="kidStore.form()"
                        reset-on-success
                        @success="addPopoverOpen = false"
                        v-slot="{ errors, processing }"
                        class="grid gap-4"
                    >
                        <div class="space-y-2">
                            <h4 class="font-medium leading-none">New Kid</h4>
                            <p class="text-sm text-muted-foreground">Add a new kid to your list.</p>
                        </div>
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" name="name" placeholder="Kid name" required />
                            <InputError :message="errors.name" />
                        </div>
                        <Button type="submit" :disabled="processing">
                            {{ processing ? 'Adding...' : 'Add Kid' }}
                        </Button>
                    </Form>
                </PopoverContent>
            </Popover>
        </div>

        <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <div v-for="kid in kids" :key="kid.id" class="flex items-center justify-between border-b border-sidebar-border/70 px-4 py-3 last:border-b-0">
                <span>{{ kid.name }}</span>
                <div class="flex gap-2">
                    <Popover
                        :open="editingKidId === kid.id"
                        @update:open="(open) => { editingKidId = open ? kid.id : null }"
                    >
                        <PopoverTrigger as-child>
                            <Button variant="outline" size="sm">Edit</Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-80" align="end">
                            <Form
                                v-bind="kidUpdate.form({ kid: kid.id })"
                                reset-on-success
                                @success="editingKidId = null"
                                v-slot="{ errors, processing }"
                                class="grid gap-4"
                            >
                                <div class="space-y-2">
                                    <h4 class="font-medium leading-none">Edit Kid</h4>
                                    <p class="text-sm text-muted-foreground">Update the kid's name.</p>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="edit-name">Name</Label>
                                    <Input id="edit-name" name="name" :default-value="kid.name" placeholder="Kid name" required />
                                    <InputError :message="errors.name" />
                                </div>
                                <Button type="submit" :disabled="processing">
                                    {{ processing ? 'Saving...' : 'Save' }}
                                </Button>
                            </Form>
                        </PopoverContent>
                    </Popover>

                    <Dialog :open="deletingKid?.id === kid.id" @update:open="(open) => { deletingKid = open ? kid : null }">
                        <DialogTrigger as-child>
                            <Button variant="destructive" size="sm">Delete</Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Delete Kid</DialogTitle>
                                <DialogDescription>
                                    Are you sure you want to delete <strong>{{ kid.name }}</strong>? This action cannot be undone.
                                </DialogDescription>
                            </DialogHeader>
                            <Form
                                v-bind="kidDestroy.form({ kid: kid.id })"
                                reset-on-success
                                @success="deletingKid = null"
                                v-slot="{ errors, processing }"
                            >
                                <InputError :message="errors.kid" />
                                <DialogFooter class="mt-6">
                                    <Button variant="outline" @click="deletingKid = null" :disabled="processing">
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

            <div v-if="kids.length === 0" class="p-4 text-center text-sm text-muted-foreground">
                No kids yet.
            </div>
        </div>
    </div>
</template>
