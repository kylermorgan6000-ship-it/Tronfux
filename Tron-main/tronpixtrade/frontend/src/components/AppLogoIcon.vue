<script setup>
defineOptions({
    inheritAttrs: false,
});

defineProps({
    className: String,
});
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 100 100"
        :class="['tronpix-logo', className]"
        v-bind="$attrs"
    >
        <!-- Glow filter -->
        <defs>
            <filter id="greenGlow" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur stdDeviation="3" result="blur" />
                <feMerge>
                    <feMergeNode in="blur" />
                    <feMergeNode in="SourceGraphic" />
                </feMerge>
            </filter>
        </defs>

        <!-- Rhombus outline -->
        <path
            d="M50 5 L95 50 L50 95 L5 50 Z"
            fill="none"
            stroke="#00ff88"
            stroke-width="6"
            stroke-linejoin="round"
            filter="url(#greenGlow)"
        />

        <!-- Stylized T -->
        <path
            d="M30 32 H70 V40 H56 V72 H44 V40 H30 Z"
            fill="#00ff88"
            filter="url(#greenGlow)"
        />
    </svg>
</template>

<style scoped>
.tronpix-logo {
    width: 100%;
    height: 100%;
}
</style>