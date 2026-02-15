<script setup lang="ts">
import AdminLayout from '../AdminLayout.vue'
import { ref, computed, onMounted } from 'vue'
import { CreditCard, Plus, X, RefreshCw, Loader2, Eye } from 'lucide-vue-next'
import { fetchCardsAdmin, createCardAdmin, toggleCardStatusAdmin } from '@/lib/api'

// ------------------- STATE -------------------
const pageLoading = ref(false)
const actionLoading = ref(false)
const creating = ref(false)
const showModal = ref(false)
const selectedCard = ref<any>(null)
const searchQuery = ref('')

const cards = ref<any[]>([])

// ------------------- FETCH -------------------
const loadCards = async () => {
  pageLoading.value = true
  try {
    cards.value = await fetchCardsAdmin()
  } catch (err) {
    console.error('Failed to load cards', err)
  } finally {
    pageLoading.value = false
  }
}

onMounted(loadCards)

// ------------------- FORM -------------------
const newCard = ref({
  holder: '',
  number: '',
  balance: '',
  currency: 'USD',
  type: 'Debit',
  status: 'Active',
})

// ------------------- COMPUTED -------------------
const filteredCards = computed(() =>
  cards.value.filter(c =>
    c.holder?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    c.number?.includes(searchQuery.value)
  )
)

// ------------------- ACTIONS -------------------
const openModal = (card: any) => {
  selectedCard.value = card
  showModal.value = true
}

const toggleStatus = async (card: any) => {
  actionLoading.value = true
  try {
    const newStatus = card.status === 'Active' ? 'Inactive' : 'Active'
    await toggleCardStatusAdmin(card.id, newStatus)
    card.status = newStatus
  } catch (err) {
    console.error('Failed to toggle card status', err)
  } finally {
    actionLoading.value = false
  }
}

const createCard = async () => {
  creating.value = true
  try {
    const payload = {
      card_type: newCard.value.type.toLowerCase(),
      card_number: newCard.value.number,
      holder_name: newCard.value.holder,
      currency: newCard.value.currency,
      status: newCard.value.status,
    }
    const created = await createCardAdmin(payload)
    cards.value.unshift(created)
    showModal.value = false
  } catch (err) {
    console.error('Failed to create card', err)
  } finally {
    creating.value = false
  }
}
</script>

<template>
  <AdminLayout>
    <section class="p-3 text-white space-y-4 min-h-screen">

      <!-- HEADER -->
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold flex items-center gap-2">
          <CreditCard class="w-5 h-5 text-green-400" /> Bank Cards
        </h1>

        <button
          @click="showModal=true"
          class="flex items-center gap-2 bg-green-500 text-black px-3 py-1.5 rounded-lg text-sm active:scale-95"
        >
          <Plus class="w-4 h-4" /> New Card
        </button>
      </div>

      <!-- SEARCH -->
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by holder or card number..."
          class="w-full bg-white/5 text-white/80 p-2 rounded-lg pl-10 outline-none"
        />
        <CreditCard class="absolute top-2.5 left-3 w-5 h-5 text-white/50" />
      </div>

      <!-- LOADER -->
      <div v-if="loading" class="flex justify-center items-center h-40">
        <Loader2 class="w-8 h-8 animate-spin text-green-400" />
      </div>

      <!-- EMPTY STATE -->
      <div v-else-if="filteredCards.length === 0" class="text-center text-white/50 py-10">
        No cards found.
      </div>

      <!-- CARD LIST -->
      <div v-else class="space-y-3">
        <div
          v-for="card in filteredCards"
          :key="card.id"
          class="bg-white/5 p-3 rounded-xl border border-white/10 flex justify-between items-center"
        >
          <div class="flex items-center gap-3">
            <CreditCard class="w-8 h-8 text-white/60" />
            <div>
              <p class="font-semibold">{{ card.holder }}</p>
              <p class="text-xs text-white/50">{{ card.number }}</p>
              <span
                class="text-[10px] px-2 py-0.5 rounded-full"
                :class="card.status==='Active' ? 'bg-green-500 text-black' : 'bg-red-500 text-white'"
              >
                {{ card.status }}
              </span>
            </div>
          </div>

          <div class="flex gap-2 items-center">
            <p class="text-sm text-white/60">{{ card.currency }} {{ card.balance }}</p>
            <button @click="openModal(card)" class="p-2 rounded-lg hover:bg-white/5">
              <Eye class="w-4 h-4 text-white/60" />
            </button>
            <button
              @click="toggleStatus(card)"
              class="p-2 rounded-lg"
              :class="card.status==='Active' ? 'bg-red-500 text-white' : 'bg-green-500 text-black'"
            >
              <span v-if="!actionLoading">{{ card.status==='Active' ? 'Deactivate' : 'Activate' }}</span>
              <Loader2 v-else class="w-5 h-5 animate-spin" />
            </button>
          </div>
        </div>
      </div>

      <!-- MODAL -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 z-[9999] flex items-end sm:items-center justify-center px-3"
      >
        <div class="bg-[#0B0E11] w-full sm:max-w-md rounded-t-2xl sm:rounded-xl p-4 space-y-3 relative">
          <button class="absolute top-3 right-3" @click="showModal=false">
            <X class="w-5 h-5" />
          </button>

          <h2 class="text-lg font-semibold">
            {{ selectedCard ? 'Card Details' : 'New Card' }}
          </h2>

          <div class="space-y-2">
            <input
              v-if="!selectedCard"
              v-model="newCard.holder"
              placeholder="Holder Name"
              class="w-full bg-white/5 p-2 rounded-lg outline-none"
            />
            <input
              v-if="!selectedCard"
              v-model="newCard.number"
              placeholder="Card Number"
              class="w-full bg-white/5 p-2 rounded-lg outline-none"
            />
            <input
              v-if="!selectedCard"
              v-model="newCard.balance"
              type="number"
              placeholder="Initial Balance"
              class="w-full bg-white/5 p-2 rounded-lg outline-none"
            />
            <select
              v-if="!selectedCard"
              v-model="newCard.type"
              class="w-full bg-white/5 p-2 rounded-lg outline-none"
            >
              <option>Debit</option>
              <option>Credit</option>
            </select>
            <select
              v-if="!selectedCard"
              v-model="newCard.status"
              class="w-full bg-white/5 p-2 rounded-lg outline-none"
            >
              <option>Active</option>
              <option>Inactive</option>
            </select>

            <div v-if="selectedCard" class="space-y-1 text-xs text-white/60">
              <p><span class="font-semibold">Holder:</span> {{ selectedCard.holder }}</p>
              <p><span class="font-semibold">Number:</span> {{ selectedCard.number }}</p>
              <p><span class="font-semibold">Balance:</span> {{ selectedCard.currency }} {{ selectedCard.balance }}</p>
              <p><span class="font-semibold">Type:</span> {{ selectedCard.type }}</p>
              <p><span class="font-semibold">Status:</span> {{ selectedCard.status }}</p>
              <p><span class="font-semibold">Issued At:</span> {{ selectedCard.issuedAt }}</p>
            </div>
          </div>

          <button
            v-if="!selectedCard"
            @click="createCard"
            class="mt-4 w-full bg-green-500 text-black py-2 rounded-lg flex justify-center items-center"
          >
            <span v-if="!creating">Create</span>
            <RefreshCw v-else class="w-5 h-5 animate-spin" />
          </button>
        </div>
      </div>

    </section>
  </AdminLayout>
</template>