<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as kidsIndex, store as kidStore } from '@/routes/kids';

interface Kid {
    id: number;
    name: string;
}

const { kids } = defineProps<{
    kids: Kid[];
}>();

const popoverOpen = ref(false);

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
            <Popover v-model:open="popoverOpen">
                <PopoverTrigger as-child>
                    <Button>Add Kid</Button>
                </PopoverTrigger>
                <PopoverContent class="w-80" align="end">
                    <Form
                        v-bind="kidStore.form()"
                        reset-on-success
                        @success="popoverOpen = false"
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
                    <button class="text-sm text-blue-500 hover:underline">Edit</button>
                    <button class="text-sm text-red-500 hover:underline">Delete</button>
                </div>
            </div>

            <div v-if="kids.length === 0" class="p-4 text-center text-sm text-muted-foreground">
                No kids yet.
            </div>
        </div>
    </div>
</template>
