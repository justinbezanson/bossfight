<script setup lang="ts">
import { Form, Head, router, useForm } from '@inertiajs/vue3';
import { Copy, KeyRound, Plus, Trash2 } from '@lucide/vue';
import { useClipboard } from '@vueuse/core';
import { ref, watch } from 'vue';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import type { Props as ManageTwoFactorProps } from '@/components/ManageTwoFactor.vue';
import ManageTwoFactor from '@/components/ManageTwoFactor.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store as createToken, destroy as deleteToken } from '@/routes/api-tokens';
import { edit } from '@/routes/security';

type Token = {
    id: number;
    name: string;
    last_used_at: string | null;
    created_at: string;
};

type Props = {
    passwordRules: string;
    tokens: Token[];
    newApiToken?: string;
} & ManageTwoFactorProps;

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Security settings',
                href: edit(),
            },
        ],
    },
});

const showTokenForm = ref(false);
const newTokenValue = ref<string | null>(props.newApiToken ?? null);

const createTokenForm = useForm({ name: '' });

const { copy } = useClipboard();

function copyToken(token: string) {
    copy(token);
}

function revokeToken(tokenId: number) {
    router.delete(deleteToken.url(tokenId));
}

watch(() => props.newApiToken, (token) => {
    if (token) {
        newTokenValue.value = token;
    }
}, { immediate: true });
</script>

<template>
    <Head title="Security settings" />

    <h1 class="sr-only">Security settings</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Update password"
            description="Ensure your account is using a long, random password to stay secure"
        />

        <Form
            v-bind="SecurityController.update.form()"
            :options="{
                preserveScroll: true,
            }"
            reset-on-success
            :reset-on-error="[
                'password',
                'password_confirmation',
                'current_password',
            ]"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="current_password">Current password</Label>
                <PasswordInput
                    id="current_password"
                    name="current_password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                    placeholder="Current password"
                />
                <InputError :message="errors.current_password" />
            </div>

            <div class="grid gap-2">
                <Label for="password">New password</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                    placeholder="New password"
                    :passwordrules="props.passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirm password</Label>
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                    placeholder="Confirm password"
                    :passwordrules="props.passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <div class="flex items-center gap-4">
                <Button
                    :disabled="processing"
                    data-test="update-password-button"
                >
                    Save
                </Button>
            </div>
        </Form>
    </div>

    <ManageTwoFactor
        :canManageTwoFactor="canManageTwoFactor"
        :requiresConfirmation="requiresConfirmation"
        :twoFactorEnabled="twoFactorEnabled"
    />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="API Tokens"
            description="Manage your API tokens for programmatic access"
        />

        <div v-if="newTokenValue" class="rounded-md border border-emerald-500/50 bg-emerald-50 p-4 dark:bg-emerald-950/20">
            <p class="mb-2 text-sm font-medium text-emerald-800 dark:text-emerald-200">
                Your new API token
            </p>
            <p class="mb-3 text-xs text-emerald-600 dark:text-emerald-400">
                Copy this token now. You won't be able to see it again.
            </p>
            <div class="flex items-center gap-2">
                <code class="flex-1 truncate rounded bg-emerald-100 px-3 py-2 text-sm dark:bg-emerald-900/40">{{ newTokenValue }}</code>
                <Button variant="outline" size="icon" @click="copyToken(newTokenValue!)">
                    <Copy class="h-4 w-4" />
                </Button>
            </div>
        </div>

        <div v-if="!showTokenForm && !newTokenValue">
            <Button @click="showTokenForm = true">
                <Plus class="h-4 w-4" />
                Create token
            </Button>
        </div>

        <div v-if="showTokenForm && !newTokenValue" class="space-y-4">
            <form
                @submit.prevent="createTokenForm.post(createToken.url(), {
                    onSuccess: () => { showTokenForm = false; createTokenForm.reset(); },
                })"
                class="flex items-end gap-3"
            >
                <div class="grid gap-2">
                    <Label for="token_name">Token name</Label>
                    <Input
                        id="token_name"
                        v-model="createTokenForm.name"
                        class="mt-1 block w-full"
                        placeholder="My API token"
                    />
                    <InputError :message="createTokenForm.errors.name" />
                </div>
                <Button type="submit" :disabled="createTokenForm.processing">
                    Create
                </Button>
                <Button variant="ghost" type="button" @click="showTokenForm = false">
                    Cancel
                </Button>
            </form>
        </div>

        <div v-if="tokens.length > 0" class="space-y-3">
            <div
                v-for="token in tokens"
                :key="token.id"
                class="flex items-center justify-between rounded-lg border p-3"
            >
                <div class="flex items-center gap-3">
                    <KeyRound class="h-4 w-4 text-muted-foreground" />
                    <div>
                        <p class="text-sm font-medium">{{ token.name }}</p>
                        <p class="text-xs text-muted-foreground">
                            Created {{ token.created_at }}
                            <span v-if="token.last_used_at">· Last used {{ token.last_used_at }}</span>
                            <span v-else>· Never used</span>
                        </p>
                    </div>
                </div>
                <form @submit.prevent="revokeToken(token.id)">
                    <Button
                        variant="ghost"
                        size="icon"
                        type="submit"
                        :disabled="createTokenForm.processing"
                    >
                        <Trash2 class="h-4 w-4 text-destructive" />
                    </Button>
                </form>
            </div>
        </div>

        <p v-else class="text-sm text-muted-foreground">
            You haven't created any API tokens yet.
        </p>
    </div>
</template>
