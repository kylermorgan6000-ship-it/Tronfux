<script setup lang="ts">
import { ref } from 'vue';
import api, { csrf } from '@/lib/api';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const processing = ref(false);
const errors = ref({});
const password = ref('');

const handleSubmit = async () => {
    processing.value = true;
    errors.value = {};
    try {
        await csrf();
        await api.post('/confirm-password', { password: password.value });
        password.value = '';
        router.push('/banking/dashboard');
    } catch (err) {
        errors.value.password = err.response?.data?.message || err.message;
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <AuthLayout
        title="Confirm your password"
        description="This is a secure area of the application. Please confirm your password before continuing."
    >
        <form @submit="handleSubmit" class="space-y-6">
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password">Password</Label>
                    <Input
                        id="password"
                        v-model="password"
                        type="password"
                        name="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                        autofocus
                    />

                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center">
                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="processing"
                        data-test="confirm-password-button"
                    >
                        <Spinner v-if="processing" />
                        Confirm Password
                    </Button>
                </div>
            </div>
        </form>
    </AuthLayout>
</template>
