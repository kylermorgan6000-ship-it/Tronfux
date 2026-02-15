<script setup lang="ts">
import { ref } from 'vue'
import { XCircle } from 'lucide-vue-next'
import FooterLink from '@/components/banking/FooterLink.vue'

// MOCK LOANS
const loans = ref([
  { id: 1, name: 'Personal Loan', amount: '$1,000 - $10,000', term: '6-24 months' },
  { id: 2, name: 'Business Loan', amount: '$5,000 - $50,000', term: '12-60 months' },
  { id: 3, name: 'Car Loan', amount: '$2,000 - $30,000', term: '12-48 months' },
  { id: 4, name: 'Education Loan', amount: '$500 - $20,000', term: '6-48 months' },
])

// STATE
const selectedLoan = ref(null)
const showModal = ref(false)

function openLoan(loan: any) {
  selectedLoan.value = loan
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedLoan.value = null
}
</script>

<template>
  <div class="min-h-screen bg-black text-white relative">

    <!-- HEADER -->
    <div class="sticky top-0 z-40 bg-black bg-opacity-80 backdrop-blur-md px-4 py-3 flex items-center gap-3 shadow-md">
      <h1 class="font-semibold text-lg">Loans</h1>
    </div>

    <!-- LOANS LIST -->
    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div
        v-for="loan in loans"
        :key="loan.id"
        class="glass p-4 rounded-xl cursor-pointer hover:scale-105 transition-transform shadow-lg"
        @click="openLoan(loan)"
      >
        <h3 class="font-semibold text-lg mb-2">{{ loan.name }}</h3>
        <p class="text-sm text-white/50">Amount: {{ loan.amount }}</p>
        <p class="text-sm text-white/50">Term: {{ loan.term }}</p>
      </div>
    </div>

    <!-- MODAL -->
    <div v-if="showModal" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center px-4">
      <div class="bg-[#151a20] rounded-xl p-8 max-w-md w-full relative text-center shadow-2xl">
        <XCircle class="w-12 h-12 text-red-500 mx-auto mb-4" />
        <h2 class="text-xl font-semibold mb-2">Offer Unavailable</h2>
        <p class="text-sm text-white/60 mb-6">
          Sorry, the {{ selectedLoan?.name }} offer is currently not available.
        </p>
        <button
          @click="closeModal"
          class="px-6 py-2 bg-green-500 text-black rounded-xl font-semibold hover:shadow-[0_0_12px_rgba(34,197,94,0.6)] transition-all"
        >
          Close
        </button>
      </div>
    </div>

    <!-- FOOTER -->
    <footer class="mt-12">
      <FooterLink />
    </footer>
  </div>
</template>

<style scoped>
.glass {
  background: linear-gradient(135deg, rgba(0,255,0,0.05), rgba(0,255,0,0.15));
  background-color: hsl(0 0% 0% / 0.35);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-radius: 1.5rem;
  border: 1px solid hsl(120 60% 50% / 0.2);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
</style>