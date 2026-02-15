<script setup lang="ts">
import { ref } from 'vue';
import { ChevronDown, ChevronUp } from 'lucide-vue-next';

// FAQ item: { question: string, answer: string }
const faqs = [
  {
    question: "Is TronpixTrades safe to use?",
    answer: "Yes, we provide bank-level encryption and follow strict compliance standards to ensure your funds and data are fully protected."
  },
  {
    question: "How fast are withdrawals?",
    answer: "Withdrawals are processed instantly or within a few minutes depending on your payment method."
  },
  {
    question: "Can I trade on mobile?",
    answer: "Absolutely! Our mobile app supports both Android and iOS with full trading functionalities."
  },
  {
    question: "Do you support multiple assets?",
    answer: "Yes, you can trade Forex, cryptocurrencies, indices, and more — all in real-time."
  },
  {
    question: "Is there 24/7 support?",
    answer: "Our support team is available 24/7 via live chat, email, or phone to help with any questions."
  }
];

// Track which FAQ is open (index or null)
const openIndex = ref(null);

const toggleFAQ = (index) => {
  openIndex.value = openIndex.value === index ? null : index;
};
</script>

<template>
  <section class="relative bg-black py-24 px-6 lg:px-20 text-white overflow-hidden">
    
    <!-- Optional floating orbs -->
    <div class="absolute inset-0 pointer-events-none">
      <span class="orb orb-1"></span>
      <span class="orb orb-2"></span>
      <span class="orb orb-3"></span>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto">
      <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-12 text-center text-green-400">
        Frequently Asked Questions
      </h2>

      <div class="flex flex-col gap-4">
        <div 
          v-for="(faq, index) in faqs" 
          :key="index"
          class="glass p-5 rounded-2xl cursor-pointer transition-all hover:shadow-2xl"
          @click="toggleFAQ(index)"
        >
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-lg md:text-xl">{{ faq.question }}</h3>
            <span>
              <component :is="openIndex === index ? ChevronUp : ChevronDown" class="w-5 h-5 text-green-400"/>
            </span>
          </div>
          <div 
            v-show="openIndex === index" 
            class="mt-3 text-gray-300 text-sm md:text-base transition-all duration-300"
          >
            {{ faq.answer }}
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Glassmorphic card */
.glass {
  background: linear-gradient(135deg, rgba(0,255,0,0.05), rgba(0,255,0,0.15));
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border: 1px solid hsl(120 60% 50% / 0.2);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.glass:hover {
  transform: translateY(-2px) scale(1.01);
  box-shadow: 0 0 30px rgba(34,197,94,0.3);
}

/* Floating orbs */
.orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(40px);
  opacity: 0.25;
  animation: float 20s ease-in-out infinite alternate;
}

.orb-1 { width: 200px; height: 200px; top: 10%; left: 15%; background: #22c55e; animation-duration: 22s; }
.orb-2 { width: 150px; height: 150px; top: 50%; left: 70%; background: #16a34a; animation-duration: 25s; }
.orb-3 { width: 180px; height: 180px; top: 30%; right: 20%; background: #0ea5e9; animation-duration: 28s; }

@keyframes float {
  0% { transform: translate(0,0); }
  25% { transform: translate(15px,-20px); }
  50% { transform: translate(-10px,15px); }
  75% { transform: translate(10px,-10px); }
  100% { transform: translate(0,0); }
}
</style>