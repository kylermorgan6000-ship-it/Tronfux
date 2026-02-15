<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const inputEmail = ref('');
const password = ref('');
const password_confirmation = ref('');
const processing = ref(false);

const handleSubmit = async () => {
  processing.value = true;
  setTimeout(() => {
    processing.value = false;
    router.push('/auth/login');
  }, 1000);
};
</script>

<template>
    <AuthLayout
        title="Reset password"
        description="Please enter your new password below"
    >
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="inputEmail"
                        class="mt-1 block w-full"
                        readonly
                    />
                    <InputError :message="undefined" class="mt-2" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        class="mt-1 block w-full"
                        v-model="password"
                        autofocus
                        placeholder="Password"
                    />
                    <InputError :message="undefined" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">
                        Confirm Password
                    </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        class="mt-1 block w-full"
                        v-model="password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="undefined" />
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :disabled="processing"
                    data-test="reset-password-button"
                >
                    <Spinner v-if="processing" />
                    Reset password
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
