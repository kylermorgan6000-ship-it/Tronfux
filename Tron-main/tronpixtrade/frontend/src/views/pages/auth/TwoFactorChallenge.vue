<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { InputOTP, InputOTPGroup, InputOTPSlot } from '@/components/ui/input-otp'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import api, { csrf } from '@/lib/api'

const router = useRouter()

const showRecoveryInput = ref(false)
const processing = ref(false)
const errors = ref({})
const code = ref('')

const authConfigContent = computed(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Recovery Code',
            description:
                'Please confirm access to your account by entering one of your emergency recovery codes.',
            toggleText: 'login using an authentication code',
        }
    }

    return {
        title: 'Authentication Code',
        description:
            'Enter the authentication code provided by your authenticator application.',
        toggleText: 'login using a recovery code',
    }
})

const toggleRecoveryMode = () => {
    showRecoveryInput.value = !showRecoveryInput.value
    errors.value = {}
    code.value = ''
}

const handleSubmit = async () => {
    processing.value = true
    errors.value = {}
    try {
        await csrf()
        await api.post('/2fa/verify', { code: code.value })
        router.push('/banking/dashboard')
    } catch (err) {
        errors.value.code = err.response?.data?.message || err.message
    } finally {
        processing.value = false
    }
}
</script>

<template>
    <AuthLayout
        :title="authConfigContent.title"
        :description="authConfigContent.description"
    >
        <div class="space-y-6">
            <template v-if="!showRecoveryInput">
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div
                        class="flex flex-col items-center justify-center space-y-3 text-center"
                    >
                        <div class="flex w-full items-center justify-center">
                            <InputOTP
                                id="otp"
                                v-model="code"
                                :maxlength="6"
                                :disabled="processing"
                                autofocus
                            >
                                <InputOTPGroup>
                                    <InputOTPSlot
                                        v-for="index in 6"
                                        :key="index"
                                        :index="index - 1"
                                    />
                                </InputOTPGroup>
                            </InputOTP>
                        </div>
                        <InputError :message="errors.code" />
                    </div>
                    <Button type="submit" class="w-full" :disabled="processing"
                        >Continue</Button
                    >
                    <div class="text-center text-sm text-muted-foreground">
                        <span>or you can </span>
                        <button
                            type="button"
                            class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            @click="toggleRecoveryMode"
                        >
                            {{ authConfigContent.toggleText }}
                        </button>
                    </div>
                </form>
            </template>

            <template v-else>
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <Input
                        v-model="code"
                        name="recovery_code"
                        type="text"
                        placeholder="Enter recovery code"
                        :autofocus="showRecoveryInput"
                        required
                    />
                    <InputError :message="errors.code" />
                    <Button type="submit" class="w-full" :disabled="processing"
                        >Verify</Button
                    >
                    <div class="text-center text-sm text-muted-foreground">
                        <span>or </span>
                        <button
                            type="button"
                            class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            @click="toggleRecoveryMode"
                        >
                            {{ authConfigContent.toggleText }}
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </AuthLayout>
</template>
