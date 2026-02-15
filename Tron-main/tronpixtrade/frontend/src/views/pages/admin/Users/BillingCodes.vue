<script setup lang="ts">
import AdminLayout from '../AdminLayout.vue'
import { ref, computed, nextTick, onMounted } from 'vue'
import {
  Plus, X, Ticket, RefreshCw, Copy, Edit, Trash, ShieldCheck, KeyRound
} from 'lucide-vue-next'
import {
  fetchSecuritySteps,
  createSecurityStep,
  updateSecurityStep,
  deleteSecurityStep
} from '@/lib/api'

// ------------------------- STATE -------------------------
/* Step shape: { id, name, explanation, code, account, type } */
const activeType = ref<'bank' | 'broker'>('bank')
const loading = ref(false)
const showModal = ref(false)
const editingStep = ref<any>(null)
const creating = ref(false)
const newStep = ref({
  id: 0,
  name: '',
  explanation: '',
  code: '',
  account: '',
  type: 'bank' as 'bank' | 'broker',
})

const steps = ref<{ bank: any[]; broker: any[] }>({
  bank: [],
  broker: [],
})

const list = computed(() => steps.value[activeType.value])

// ------------------------- HELPERS -------------------------
const switchType = async (type: 'bank' | 'broker') => {
  activeType.value = type
  await fetchSteps(type)
}

const fetchSteps = async (type: 'bank' | 'broker') => {
  loading.value = true
  try {
    steps.value[type] = await fetchSecuritySteps(type)
  } catch (err) {
    console.error('Failed to fetch steps', err)
  } finally {
    loading.value = false
  }
}

const openModal = (step?: any) => {
  if (step) {
    editingStep.value = step
    newStep.value = { ...step }
  } else {
    editingStep.value = null
    newStep.value = { id: 0, name: '', explanation: '', code: '', account: '', type: activeType.value }
  }
  showModal.value = true
}

const saveStep = async () => {
  creating.value = true
  try {
    if (editingStep.value) {
      const res = await updateSecurityStep(editingStep.value.id, newStep.value)
      const idx = steps.value[activeType.value].findIndex(s => s.id === editingStep.value.id)
      steps.value[activeType.value][idx] = res
    } else {
      const res = await createSecurityStep(newStep.value)
      steps.value[activeType.value].push(res)
    }
    showModal.value = false
  } catch (err) {
    console.error('Failed to save step', err)
  } finally {
    creating.value = false
  }
}

const deleteStep = async (id: number) => {
  if (!confirm('Are you sure you want to delete this step?')) return
  try {
    await deleteSecurityStep(id)
    steps.value[activeType.value] = steps.value[activeType.value].filter(s => s.id !== id)
  } catch (err) {
    console.error('Failed to delete step', err)
  }
}

const copyCode = (code: string) => {
  navigator.clipboard.writeText(code)
  alert('Code copied!')
}

onMounted(() => { void fetchSteps('bank') })
</script>

<template>
  <AdminLayout>
    <section class="p-3 text-white space-y-4">

      <!-- HEADER -->
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold flex items-center gap-2">
          <Ticket class="w-5 h-5 text-green-400" />
          Security Steps
        </h1>

        <button
          @click="openModal()"
          class="flex items-center gap-2 bg-green-500 text-black px-3 py-1.5 rounded-lg text-sm active:scale-95"
        >
          <Plus class="w-4 h-4" /> New
        </button>
      </div>

      <!-- ACCOUNT TYPE SWITCH -->
      <div class="grid grid-cols-2 gap-2">
        <button
          @click="switchType('bank')"
          :class="[
            'flex justify-center items-center gap-2 py-2 rounded-lg text-sm',
            activeType==='bank' ? 'bg-green-500 text-black' : 'bg-white/5'
          ]"
        >
          <Ticket class="w-4 h-4" /> Bank
        </button>

        <button
          @click="switchType('broker')"
          :class="[
            'flex justify-center items-center gap-2 py-2 rounded-lg text-sm',
            activeType==='broker' ? 'bg-green-500 text-black' : 'bg-white/5'
          ]"
        >
          <Ticket class="w-4 h-4" /> Broker
        </button>
      </div>

      <!-- LOADER -->
      <div v-if="loading" class="flex justify-center items-center h-40">
        <RefreshCw class="w-8 h-8 animate-spin text-green-400" />
      </div>

      <!-- EMPTY -->
      <div v-else-if="list.length === 0" class="text-center py-10 text-white/50">
        No security steps yet.
      </div>

      <!-- LIST -->
      <div v-else class="space-y-3">
        <div
          v-for="step in list"
          :key="step.id"
          class="bg-white/5 rounded-xl p-3 border border-white/10 relative"
        >
          <!-- CODE COPY -->
          <button @click="copyCode(step.code)" class="absolute top-3 right-3">
            <Copy class="w-4 h-4 text-white/60" />
          </button>

          <div class="flex justify-between items-center mb-2">
            <p class="font-bold">{{ step.name }}</p>
            <div class="flex gap-2">
              <button @click="openModal(step)">
                <Edit class="w-4 h-4 text-white/60" />
              </button>
              <button @click="deleteStep(step.id)">
                <Trash class="w-4 h-4 text-red-400" />
              </button>
            </div>
          </div>

          <p class="text-xs text-white/50">{{ step.explanation }}</p>
          <p class="text-[11px] text-white/40 mt-1">Account: {{ step.account }}</p>
          <p class="text-[11px] text-white/30 mt-1">Code: {{ step.code }}</p>
        </div>
      </div>

      <!-- CREATE / EDIT MODAL -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 z-[9999] flex items-end sm:items-center"
      >
        <div class="bg-[#0B0E11] w-full sm:max-w-md rounded-t-2xl sm:rounded-xl p-4">
          <div class="flex justify-between items-center mb-3">
            <h2 class="font-semibold">{{ editingStep ? 'Edit Step' : 'New Step' }}</h2>
            <X class="w-5 h-5 cursor-pointer" @click="showModal=false" />
          </div>

          <div class="space-y-3">
            <input
              v-model="newStep.name"
              placeholder="Step Name"
              class="w-full bg-white/5 p-2 rounded-lg outline-none"
            />
            <input
              v-model="newStep.explanation"
              placeholder="Step Explanation"
              class="w-full bg-white/5 p-2 rounded-lg outline-none"
            />
            <input
              v-model="newStep.code"
              placeholder="Verification Code"
              class="w-full bg-white/5 p-2 rounded-lg outline-none"
            />
            <input
              v-model="newStep.account"
              placeholder="Account / Area"
              class="w-full bg-white/5 p-2 rounded-lg outline-none"
            />
          </div>

          <button
            @click="saveStep"
            class="mt-4 w-full bg-green-500 text-black py-2 rounded-lg flex justify-center"
          >
            <span v-if="!creating">{{ editingStep ? 'Update' : 'Create' }}</span>
            <RefreshCw v-else class="w-5 h-5 animate-spin" />
          </button>
        </div>
      </div>

    </section>
  </AdminLayout>
</template>