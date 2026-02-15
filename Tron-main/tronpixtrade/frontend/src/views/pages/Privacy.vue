<script setup lang="ts">
import { ArrowLeft } from 'lucide-vue-next'
import { useRouter } from 'vue-router'

const router = useRouter()
</script>

<template>
  <div class="min-h-screen bg-black text-white">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-black shadow-sm px-4 py-4 flex items-center gap-3 border-b border-white/10">
      <button @click="router.back()" class="text-white/70 hover:text-white">
        <ArrowLeft class="w-5 h-5" />
      </button>
      <h1 class="font-semibold text-lg">Privacy Policy</h1>
    </div>

    <!-- Content -->
    <div class="max-w-3xl mx-auto px-4 py-12 space-y-8">
      <div>
        <h2 class="text-2xl font-bold mb-4">Privacy Policy</h2>
        <p class="text-white/60 text-sm mb-6">Effective Date: January 1, 2026</p>
      </div>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">1. Introduction</h3>
        <p class="text-white/70 leading-relaxed">
          TronpixTrades ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our website and services.
        </p>
      </section>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">2. Information We Collect</h3>
        <p class="text-white/70 leading-relaxed">
          We collect information you voluntarily provide, including name, email, phone number, and financial information needed to process your requests. We also automatically collect device and usage information through cookies and similar technologies.
        </p>
      </section>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">3. How We Use Your Information</h3>
        <p class="text-white/70 leading-relaxed">
          Your information is used to provide, maintain, and improve our services, process transactions, send administrative information, and comply with legal obligations.
        </p>
      </section>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">4. Data Protection</h3>
        <p class="text-white/70 leading-relaxed">
          We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.
        </p>
      </section>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">5. Your Rights</h3>
        <p class="text-white/70 leading-relaxed">
          You have the right to access, correct, or delete your personal information. You can also opt-out of marketing communications at any time. Contact us for any requests.
        </p>
      </section>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">6. Contact Us</h3>
        <p class="text-white/70 leading-relaxed">
          If you have questions about this Privacy Policy, please contact us at support@tronpixtrades.com or through our contact form.
        </p>
      </section>
    </div>
  </div>
</template>
