<script setup lang="ts">
import AdminLayout from '../AdminLayout.vue'
import { ref, computed, onMounted } from 'vue'
import api from '@/lib/api'
import { fetchBrokerPlans, createBrokerPlan, updateBrokerPlan, deleteBrokerPlan } from '@/lib/api'
import {
  Plus,
  X,
  Pencil,
  Trash,
  RefreshCw,
  Flame,
  ChevronDown,
} from 'lucide-vue-next'

/* STATE */
const loading = ref(true)
const showModal = ref(false)
const creating = ref(false)
const editingPlan = ref(null)
const search = ref('')

const activeFilter = ref('all')
const showSortMenu = ref(false)
const sortOrder = ref('apy-desc')

/* INVESTMENT PLANS */
const plans = ref([])

const loadPlans = async () => {
  loading.value = true
  try {
    const res = await fetchBrokerPlans()
    plans.value = res.data ?? res
  } catch (e) {
    console.error('Failed to load plans', e)
    plans.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => { void loadPlans() })

/* FORM */
const form = ref({
  name: '',
  apy: 0,
  min: 0,
  duration: '',
  type: 'variable',
  featured: false,
  description: '',
})

/* FILTERED + SORTED */
const filteredPlans = computed(() => {
  let list = plans.value.filter(p => {
    if (activeFilter.value !== 'all' && p.type !== activeFilter.value) return false
    if (search.value && !p.name.toLowerCase().includes(search.value.toLowerCase())) return false
    return true
  })
  return list.sort((a,b) => sortOrder.value==='apy-desc' ? b.apy-a.apy : a.apy-b.apy)
})

/* HELPERS */
const openCreateModal = () => {
  editingPlan.value = null
  form.value = { name:'', apy:0, min:0, duration:'', type:'variable', featured:false, description:'' }
  showModal.value = true
}

const openEditModal = (plan) => {
  editingPlan.value = plan
  form.value = { ...plan }
  showModal.value = true
}

const savePlan = async () => {
  creating.value = true
  try {
    if (editingPlan.value) {
      await updateBrokerPlan(editingPlan.value.id, form.value)
      Object.assign(editingPlan.value, form.value)
    } else {
      const newPlan = await createBrokerPlan(form.value)
      plans.value.unshift(newPlan)
    }
    showModal.value = false
  } catch (err) {
    console.error('Failed to save plan', err)
    alert('Error saving plan')
  } finally {
    creating.value = false
  }
}

const deletePlan = async (plan) => {
  if (!confirm('Delete this plan?')) return
  try {
    await deleteBrokerPlan(plan.id)
    plans.value = plans.value.filter(p => p.id !== plan.id)
  } catch (err) {
    console.error('Failed to delete plan', err)
    alert('Error deleting plan')
  }
}

const deletePlan = (plan) => {
  if(confirm('Are you sure you want to delete this plan?')) {
    plans.value = plans.value.filter(p => p.id !== plan.id)
  }
}
</script>

<template>
<AdminLayout>
<section class="p-3 text-white space-y-4 min-h-screen">

  <!-- HEADER -->
  <div class="flex justify-between items-center">
    <h1 class="text-xl font-bold flex items-center gap-2">
      <Flame class="w-5 h-5 text-green-400" /> Investment Plans
    </h1>
    <button @click="openCreateModal" class="flex items-center gap-2 bg-green-500 text-black px-3 py-1.5 rounded-lg text-sm active:scale-95">
      <Plus class="w-4 h-4" /> New Plan
    </button>
  </div>

  <!-- SEARCH + SORT -->
  <div class="flex gap-2">
    <input v-model="search" placeholder="Search plan" class="w-full bg-white/5 p-2 rounded-lg outline-none text-sm" />
    <div class="relative">
      <button @click="showSortMenu = !showSortMenu" class="flex items-center gap-1 text-xs text-white/70 px-2 py-1 bg-white/5 rounded-lg">
        APY <ChevronDown class="w-3 h-3"/>
      </button>
      <div v-if="showSortMenu" class="absolute right-0 mt-2 w-36 bg-black border border-white/10 rounded-lg text-[13px] overflow-hidden z-40">
        <button @click="sortOrder='apy-desc'; showSortMenu=false" class="w-full px-3 py-2 text-left hover:bg-white/10">APY High → Low</button>
        <button @click="sortOrder='apy-asc'; showSortMenu=false" class="w-full px-3 py-2 text-left hover:bg-white/10">APY Low → High</button>
      </div>
    </div>
  </div>

  <!-- FILTER CHIPS -->
  <div class="flex gap-2 mt-3">
    <button class="filter-chip" :class="{active:activeFilter==='all'}" @click="activeFilter='all'">All</button>
    <button class="filter-chip" :class="{active:activeFilter==='fixed'}" @click="activeFilter='fixed'">Fixed</button>
    <button class="filter-chip" :class="{active:activeFilter==='variable'}" @click="activeFilter='variable'">Variable</button>
  </div>

  <!-- PLAN LIST -->
  <div class="mt-4 space-y-3">
    <div v-for="plan in filteredPlans" :key="plan.id" class="bg-white/5 p-4 rounded-xl border border-white/10 flex justify-between items-center">
      <div>
        <p class="font-bold text-sm">{{ plan.name }} <span v-if="plan.featured" class="text-green-400 text-xs">(Featured)</span></p>
        <p class="text-xs text-white/50">{{ plan.duration }} | Minimum: ${{ plan.min }} | APY: {{ plan.apy }}%</p>
      </div>
      <div class="flex gap-2">
        <button @click="openEditModal(plan)" class="p-2 rounded-lg hover:bg-white/5">
          <Pencil class="w-4 h-4"/>
        </button>
        <button @click="deletePlan(plan)" class="p-2 rounded-lg hover:bg-red-500/30">
          <Trash class="w-4 h-4"/>
        </button>
      </div>
    </div>
  </div>

  <!-- CREATE/EDIT MODAL -->
  <div v-if="showModal" class="fixed inset-0 bg-black/50 z-[999] flex items-end sm:items-center justify-center px-3">
    <div class="bg-[#0B0E11] w-full sm:max-w-md rounded-t-2xl sm:rounded-xl p-5 space-y-3 relative">
      <button class="absolute top-3 right-3" @click="showModal=false"><X class="w-5 h-5"/></button>
      <h2 class="text-lg font-semibold">{{ editingPlan?'Edit Plan':'New Plan' }}</h2>
      
      <div class="space-y-2">
        <input v-model="form.name" placeholder="Plan Name" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
        <input v-model.number="form.apy" type="number" placeholder="APY (%)" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
        <input v-model.number="form.min" type="number" placeholder="Minimum Amount" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
        <input v-model="form.duration" placeholder="Duration (e.g., 7-Day Lock)" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
        <select v-model="form.type" class="w-full p-2 rounded-lg bg-white/5 outline-none text-black">
          <option value="fixed">Fixed</option>
          <option value="variable">Variable</option>
        </select>
        <textarea v-model="form.description" placeholder="Description" class="w-full p-2 rounded-lg bg-white/5 outline-none"></textarea>
        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" v-model="form.featured"/> Featured
        </label>
      </div>

      <button @click="savePlan" class="mt-3 w-full py-2 rounded-lg bg-green-500 text-black flex justify-center">
        <span v-if="!creating">{{ editingPlan?'Update':'Create' }}</span>
        <RefreshCw v-else class="w-5 h-5 animate-spin"/>
      </button>
    </div>
  </div>

</section>
</AdminLayout>
</template>

<style scoped>
.filter-chip {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 11px;
  background: rgba(255,255,255,0.08);
}
.filter-chip.active {
  background: rgba(34,197,94,0.25);
  color: #22c55e;
  font-weight: 600;
}
</style>