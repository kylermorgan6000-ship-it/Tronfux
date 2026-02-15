<script setup lang="ts">
import { ArrowLeft, ShieldCheck, CreditCard, Lock, Layers, Plus } from 'lucide-vue-next'
import { ref, onMounted, computed } from 'vue'
import FooterLink from '@/components/banking/FooterLink.vue'
import { fetchCardsList } from '@/lib/api'

// ------------------- STATE -------------------
interface Card {
  id: string | number
  type: string
  number: string
  holder: string
  expiry: string
  status: string
  color: string
  logo: string
  cardId: string | number
}

const cards = ref<Card[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

// ------------------- CARD LOGOS & COLORS -------------------
const cardLogos: Record<string, string> = {
  debit: 'https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png',
  credit: 'https://upload.wikimedia.org/wikipedia/commons/3/30/American_Express_logo.svg',
}

const cardColors = [
  'from-teal-500 via-blue-500 to-purple-600',
  'from-gray-700 via-gray-900 to-black',
  'from-orange-500 via-red-500 to-pink-600',
]

// ------------------- HELPERS -------------------
const formatExpiry = (expiryDate?: string | null) => {
  if (!expiryDate) return 'MM / YY'
  const date = new Date(expiryDate)
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = String(date.getFullYear()).slice(-2)
  return `${month} / ${year}`
}

// ------------------- FETCH CARDS -------------------
onMounted(async () => {
  loading.value = true
  try {
    const data = await fetchCardsList()

    cards.value = data.map((card: any, index: number) => ({
      id: card.id,
      type: card.card_type === 'debit' ? 'Debit Card' : 'Credit Card',
      number: card.card_number ? card.card_number.slice(-4) : '••••',
      holder: card.card_name || 'Your Card',
      expiry: formatExpiry(card.expiry_date),
      status: card.status || 'active',
      color: cardColors[index % cardColors.length],
      logo: cardLogos[card.card_type] || cardLogos.debit,
      cardId: card.id,
    }))
  } catch (err: any) {
    error.value = err.message
    console.error('Error fetching cards:', err)
  } finally {
    loading.value = false
  }
})

// ------------------- COMPUTED -------------------
const activeCards = computed(() => cards.value.filter(c => c.status === 'active'))
const blockedCards = computed(() => cards.value.filter(c => c.status === 'blocked'))
</script>

<template>
  <!-- HEADER -->
  <div class="sticky top-0 z-40 bg-black shadow-[0_1px_0_rgba(255,255,255,0.06)] px-4 py-3 flex items-center gap-3">
    <ArrowLeft class="w-5 h-5 text-white/70" />
    <h1 class="font-semibold text-lg">Cards</h1>
  </div> 
  
<!-- FLOATING RIGHT BUTTON -->
<div class="px-4 flex justify-end mt-3">
  <router-link to="/banking/card/application">
    <button
      type="button"
      class="px-4 py-2 rounded-[10px] text-xs font-semibold
             bg-gradient-to-r from-green-500 to-black
             border border-white/10
             text-white shadow-md
             flex items-center gap-2
             hover:scale-[1.03]
             hover:from-green-400 hover:to-black
             active:scale-[0.98]
             transition-all duration-200"
    >
      <Plus class="w-4 h-4" />
      Apply for card
    </button>
  </router-link>
</div>

  <!-- INTRO -->
  <div class="px-4 mb-4 mt-4">
    <p class="text-sm font-medium">Secured Cards</p>
    <p class="text-xs text-white/50 mt-1">
      View all your cards including Active, Blocked, and future cards. All card types are bound to your account.
    </p>
  </div>

  <!-- ERROR STATE -->
  <div v-if="error" class="px-4 mb-4">
    <div class="bg-red-900/20 border border-red-600 text-red-400 p-3 rounded-lg text-sm">
      {{ error }}
    </div>
  </div>

  <!-- LOADING STATE -->
  <div v-if="loading" class="px-4 mb-4">
    <div class="bg-gray-900/50 rounded-xl p-4 flex items-center justify-center gap-2">
      <div class="animate-spin w-4 h-4 border-2 border-green-500 border-t-transparent rounded-full"></div>
      <span class="text-sm text-white/60">Loading cards...</span>
    </div>
  </div>

  <!-- CARD STATS -->
  <div v-if="!loading" class="px-4 flex gap-4 mb-6 text-[11px]">
    <div class="flex-1 bg-gray-900/50 rounded-xl p-3 flex flex-col items-center gap-1">
      <CreditCard class="w-5 h-5 text-green-400" />
      <span class="font-semibold text-green-400">{{ activeCards.length }}</span>
      Active Cards
    </div>

    <div class="flex-1 bg-gray-900/50 rounded-xl p-3 flex flex-col items-center gap-1">
      <Lock class="w-5 h-5 text-red-400" />
      <span class="font-semibold text-red-400">{{ blockedCards.length }}</span>
      Blocked Cards
    </div>

    <div class="flex-1 bg-gray-900/50 rounded-xl p-3 flex flex-col items-center gap-1">
      <Layers class="w-5 h-5 text-white" />
      <span class="font-semibold text-white">{{ cards.length }}</span>
      Total Cards
    </div>
  </div>

  <!-- ACTIVE CARDS SECTION -->
  <div class="mb-6 px-4">
    <p class="text-sm font-medium">Active Cards</p>
    <p class="text-xs text-white/50 mt-1 mb-3">
      Swipe horizontally to see the back side of the card, or contact support if you wish to disable any.
    </p>

    <!-- SCROLL ROW -->
    <div class="flex gap-6 overflow-x-auto pb-4">
      <template v-for="card in activeCards" :key="card.id">
        <!-- CARD PAIR (Front + Back must stay together) -->
        <div class="flex gap-4 flex-shrink-0">
          <!-- FRONT CARD -->
          <div
            class="relative w-[280px] h-52 rounded-2xl p-5 overflow-hidden shadow-xl"
            :class="`bg-gradient-to-br ${card.color}`"
          >
            <div class="absolute w-32 h-32 bg-green-500/20 blur-3xl -top-10 -left-10"></div>

            <div class="relative z-10 h-full flex flex-col justify-between">
              <!-- Chip + Logo + Type -->
              <div class="flex items-center justify-between">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/EMV_Chip.png" class="w-10" />

                <div class="flex items-center gap-2">
                  <!-- NO background -->
                  <img :src="card.logo" class="w-12 object-contain" />
                  <span class="text-[10px] font-semibold text-white/90 whitespace-nowrap">
                    {{ card.type }}
                  </span>
                </div>
              </div>

              <!-- Card number -->
              <div class="text-lg tracking-widest font-semibold">
                •••• •••• •••• {{ card.number }}
              </div>

              <!-- Holder & Expiry -->
              <div class="flex justify-between text-[11px] text-white/80">
                <div>
                  <p class="uppercase text-[9px]">Card Holder</p>
                  <p>{{ card.holder }}</p>
                </div>
                <div>
                  <p class="uppercase text-[9px]">Expires</p>
                  <p>{{ card.expiry }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- BACK CARD -->
          <div class="relative w-[280px] h-52 rounded-2xl bg-gray-900 shadow-inner p-4">
            <div class="h-8 bg-black/80 rounded-sm mb-4"></div>

            <div class="flex justify-end">
              <div class="bg-white/90 text-black text-xs px-3 py-1 rounded">
                CVV •••
              </div>
            </div>

            <p class="text-[10px] text-white/40 mt-4">
              This card is issued by our licensed financial partners. Unauthorized use is prohibited.
            </p>
          </div>
        </div>
      </template>
    </div>
  </div>

  <!-- BLOCKED CARDS SECTION -->
  <div class="mb-6 px-4">
    <p class="text-sm font-medium">Blocked / Disabled Cards</p>
    <p class="text-xs text-white/50 mt-1 mb-3">
      These cards are currently disabled and cannot be used. You can contact support to enable them again.
    </p>

    <!-- SCROLL ROW -->
    <div class="flex gap-6 overflow-x-auto pb-4">
      <template v-for="card in blockedCards" :key="card.id">
        <!-- CARD PAIR -->
        <div class="flex gap-4 flex-shrink-0">
          <!-- FRONT CARD -->
          <div
            class="relative w-[280px] h-52 rounded-2xl p-5 overflow-hidden shadow-inner opacity-60"
            :class="`bg-gradient-to-br ${card.color}`"
          >
            <div class="absolute w-32 h-32 bg-red-500/20 blur-3xl -top-10 -left-10"></div>

            <div class="absolute top-3 right-3 bg-red-600 text-[10px] px-2 py-1 rounded font-semibold">
              Disabled
            </div>

            <div class="relative z-10 h-full flex flex-col justify-between">
              <!-- Chip + Logo + Type -->
              <div class="flex items-center justify-between">
                <img
                  src="https://upload.wikimedia.org/wikipedia/commons/5/5e/EMV_Chip.png"
                  class="w-10 opacity-50"
                />

                <div class="flex items-center gap-2">
                  <img :src="card.logo" class="w-12 object-contain opacity-50" />
                  <span class="text-[10px] font-semibold text-white/80 whitespace-nowrap">
                    {{ card.type }}
                  </span>
                </div>
              </div>

              <!-- Card number -->
              <div class="text-lg tracking-widest font-semibold opacity-50">
                •••• •••• •••• {{ card.number }}
              </div>

              <!-- Holder & Expiry -->
              <div class="flex justify-between text-[11px] text-white/50">
                <div>
                  <p class="uppercase text-[9px]">Card Holder</p>
                  <p>{{ card.holder }}</p>
                </div>
                <div>
                  <p class="uppercase text-[9px]">Expires</p>
                  <p>{{ card.expiry }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- BACK CARD -->
          <div class="relative w-[280px] h-52 rounded-2xl bg-gray-900 shadow-inner p-4 opacity-50">
            <div class="h-8 bg-black/80 rounded-sm mb-4"></div>

            <div class="flex justify-end">
              <div class="bg-white/50 text-black text-xs px-3 py-1 rounded">
                CVV •••
              </div>
            </div>

            <p class="text-[10px] text-white/40 mt-4">
              This card is disabled. Unauthorized use is prohibited.
            </p>
          </div>
        </div>
      </template>
    </div>
  </div>

  <!-- PLACEHOLDER CARD -->
  <div class="mb-6 px-4">
    <div
      class="relative min-w-[280px] h-52 rounded-2xl p-5 bg-gray-900/40 backdrop-blur-lg shadow-lg flex flex-col justify-center items-center gap-2"
    >
      <CreditCard class="w-12 h-12 text-white/50" />
      <p class="text-white/50 text-sm font-semibold text-center">
        Your next card will appear here
      </p>
      <Link
        href="/banking/card/application"
        class="block text-center w-full py-3 rounded-xl bg-green-600 hover:bg-green-500 transition font-semibold"
      >
        Apply for New Card
      </Link>
    </div>
  </div>

  <!-- SECURITY INFO -->
  <div class="space-y-3 px-4 pb-20 mt-6 text-[11px] text-white/60">
    <div class="flex items-center gap-2">
      <ShieldCheck class="w-4 h-4 text-green-400" />
      <span>Cards are protected with industry-grade encryption.</span>
    </div>
    <p>
      Your card details are never shared and can be frozen instantly.
    </p>
  </div>

  <FooterLink />
</template>