<script setup lang="ts">
import { ref } from 'vue';
import api from '@/lib/api'
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
const emailValue = ref('');

const handleSubmit = async () => {
    processing.value = true;
    try {
        await api.post('/forgot-password', { email: emailValue.value })
        emailValue.value = '';
        router.push('/auth/login');
    } catch (err) {
        errors.value.email = err.response?.data?.message || err.message;
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <AuthLayout
        title="Forgot password"
        description="Enter your email to receive a password reset link"
    >
        <div class="space-y-6">
            <form @submit.prevent="handleSubmit">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        v-model="emailValue"
                        type="email"
                        name="email"
                        autocomplete="off"
                        autofocus
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="processing"
                        data-test="email-password-reset-link-button"
                    >
                        <Spinner v-if="processing" />
                        Email password reset link
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Or, return to</span>
                <router-link to="/auth/login" class="text-primary hover:underline">log in</router-link>
            </div>
        </div>
    </AuthLayout>
</template>
