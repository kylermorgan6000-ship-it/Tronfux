<script setup lang="ts">
import AdminLayout from '../AdminLayout.vue'
import { ref, computed, onMounted } from 'vue'
import { User, CheckCircle, XCircle, Loader2, Search, Eye, Trash } from 'lucide-vue-next'
import {
  fetchBrokerKycUsers,
  approveBrokerKyc,
  rejectBrokerKyc,
  deleteBrokerKyc,
} from '@/lib/api'

// ------------------------- STATE -------------------------
const loading = ref(true)
const showModal = ref(false)
const selectedUser = ref<any | null>(null)
const actionLoading = ref(false)
const searchQuery = ref('')

const kycList = ref<any[]>([])

// ------------------------- LOAD DATA -------------------------
const loadKyc = async () => {
  loading.value = true
  try {
    const users = await fetchBrokerKycUsers()
    kycList.value = users.map((u) => ({
      id: u.id,
      name: u.name,
      email: u.email,
      status: 'Pending',
      country: u.country ?? '',
      documentType: u.documentType ?? 'ID',
      documentNumber: u.documentNumber ?? '',
      selfie: `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=111827&color=fff`,
      documentUrl:
        u.documentUrl ?? `https://via.placeholder.com/300x200.png?text=${encodeURIComponent('Document')}`,
    }))
  } catch (e) {
    console.error('Failed to load KYC list', e)
    kycList.value = []
  } finally {
    loading.value = false
  }
}

// ------------------------- ACTIONS -------------------------
const approveKYCHandler = async () => {
  if (!selectedUser.value) return
  actionLoading.value = true
  try {
    await approveBrokerKyc(selectedUser.value.id)
    selectedUser.value.status = 'Approved'
  } catch (e) {
    console.warn('Approve KYC failed, updating locally', e)
    selectedUser.value.status = 'Approved'
  } finally {
    actionLoading.value = false
    showModal.value = false
  }
}

const rejectKYCHandler = async () => {
  if (!selectedUser.value) return
  actionLoading.value = true
  try {
    await rejectBrokerKyc(selectedUser.value.id)
    selectedUser.value.status = 'Rejected'
  } catch (e) {
    console.warn('Reject KYC failed, updating locally', e)
    selectedUser.value.status = 'Rejected'
  } finally {
    actionLoading.value = false
    showModal.value = false
  }
}

const deleteKYCHandler = async (id: number) => {
  if (!confirm('Delete this KYC entry?')) return
  try {
    await deleteBrokerKyc(id)
  } catch (e) {
    console.warn('Delete KYC failed, removing locally', e)
  } finally {
    kycList.value = kycList.value.filter(u => u.id !== id)
  }
}

// ------------------------- COMPUTED -------------------------
const filteredList = computed(() =>
  kycList.value.filter(
    u =>
      u.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      u.email.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
)

// ------------------------- HELPERS -------------------------
const openModal = (user: any) => {
  selectedUser.value = user
  showModal.value = true
}

// ------------------------- MOUNT -------------------------
onMounted(() => {
  void loadKyc()
})
</script>

<template>
  <AdminLayout>
    <section class="p-3 text-white space-y-4 min-h-screen">

      <!-- HEADER -->
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold flex items-center gap-2">
          <User class="w-5 h-5 text-green-400" /> KYC Submissions
        </h1>
      </div>

      <!-- SEARCH -->
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by name or email..."
          class="w-full bg-white/5 text-white/80 p-2 rounded-lg pl-10 outline-none"
        />
        <Search class="absolute top-2.5 left-3 w-5 h-5 text-white/50" />
      </div>

      <!-- LOADER -->
      <div v-if="loading" class="flex justify-center items-center h-40">
        <Loader2 class="w-8 h-8 animate-spin text-green-400" />
      </div>

      <!-- EMPTY STATE -->
      <div v-else-if="filteredList.length === 0" class="text-center text-white/50 py-10">
        No KYC submissions found.
      </div>

      <!-- LIST -->
      <div v-else class="space-y-3">
        <div
          v-for="user in filteredList"
          :key="user.id"
          class="bg-white/5 p-3 rounded-xl border border-white/10 flex justify-between items-center"
        >
          <div class="flex items-center gap-3">
            <img :src="user.selfie" class="w-10 h-10 rounded-full border border-white/10" />
            <div>
              <p class="font-semibold">{{ user.name }}</p>
              <p class="text-xs text-white/50">{{ user.email }}</p>
              <span
                class="text-[10px] px-2 py-0.5 rounded-full"
                :class="{
                  'bg-yellow-500 text-black': user.status==='Pending',
                  'bg-green-500 text-black': user.status==='Approved',
                  'bg-red-500 text-white': user.status==='Rejected'
                }"
              >
                {{ user.status }}
              </span>
            </div>
          </div>
          <div class="flex gap-2">
            <button @click="openModal(user)" class="p-2 rounded-lg hover:bg-white/5">
              <Eye class="w-4 h-4 text-white/60" />
            </button>
            <button @click="deleteKYC(user.id)" class="p-2 rounded-lg hover:bg-red-600/30">
              <Trash class="w-4 h-4 text-red-400" />
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

          <h2 class="text-lg font-semibold">{{ selectedUser.name }}</h2>
          <p class="text-xs text-white/50">{{ selectedUser.email }} · {{ selectedUser.country }}</p>

          <div class="space-y-2">
            <p class="text-sm font-semibold">Document Type</p>
            <p class="text-xs text-white/50">{{ selectedUser.documentType }}</p>

            <p class="text-sm font-semibold">Document Number</p>
            <p class="text-xs text-white/50">{{ selectedUser.documentNumber }}</p>

            <p class="text-sm font-semibold">Selfie</p>
            <img :src="selectedUser.selfie" class="w-full h-48 object-cover rounded-lg border border-white/10" />

            <p class="text-sm font-semibold">Document Image</p>
            <img :src="selectedUser.documentUrl" class="w-full h-48 object-cover rounded-lg border border-white/10" />
          </div>

          <div class="flex justify-between gap-3 mt-3">
            <button
              @click="approveKYC"
              class="flex-1 bg-green-500 text-black py-2 rounded-lg flex justify-center items-center gap-2"
            >
              <span v-if="!actionLoading">Approve</span>
              <Loader2 v-else class="w-5 h-5 animate-spin" />
            </button>

            <button
              @click="rejectKYC"
              class="flex-1 bg-red-500 text-white py-2 rounded-lg flex justify-center items-center gap-2"
            >
              <span v-if="!actionLoading">Reject</span>
              <Loader2 v-else class="w-5 h-5 animate-spin" />
            </button>
          </div>
        </div>
      </div>

    </section>
  </AdminLayout>
</template>