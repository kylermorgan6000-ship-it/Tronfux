<script setup lang="ts">
import AdminLayout from './AdminLayout.vue'
import { ref, computed, onMounted } from 'vue'
import api, { fetchAdminUsers, createAdminUser, updateAdminUser, deleteAdminUser } from '@/lib/api'
import { Plus, X, Pencil, Trash, RefreshCw, UserCheck } from 'lucide-vue-next'

// ------------------- STATE -------------------
const loading = ref(true)
const showModal = ref(false)
const creating = ref(false)
const editingAdmin = ref<any>(null)
const search = ref('')
const activeFilter = ref<'all' | 'active' | 'inactive'>('all')

const admins = ref<any[]>([])

// ------------------- FETCH -------------------
const loadAdmins = async () => {
  loading.value = true
  try {
    const data = await fetchAdminUsers()
    admins.value = (data.data ?? data).map((u: any) => ({
      id: u.id,
      name: u.name,
      email: u.email,
      role: u.role ?? 'Admin',
      status: u.status ?? 'active',
    }))
  } catch (e) {
    console.error('Failed to load admins', e)
    admins.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => { void loadAdmins() })

// ------------------- FILTERED -------------------
const filteredAdmins = computed(() =>
  admins.value.filter(a => {
    if (activeFilter.value !== 'all' && a.status !== activeFilter.value) return false
    if (search.value && !a.name.toLowerCase().includes(search.value.toLowerCase())) return false
    return true
  })
)

// ------------------- HELPERS -------------------
const openCreateModal = () => {
  editingAdmin.value = null
  form.value = { name:'', email:'', password:'', role:'Admin', status:'active' }
  showModal.value = true
}

const openEditModal = (admin: any) => {
  editingAdmin.value = admin
  form.value = { ...admin, password: '' }
  showModal.value = true
}

// ------------------- FORM -------------------
const form = ref({ name:'', email:'', password:'', role:'Admin', status:'active' })

const saveAdmin = async () => {
  creating.value = true
  try {
    if (editingAdmin.value) {
      const updated = await updateAdminUser(editingAdmin.value.id, form.value)
      Object.assign(editingAdmin.value, updated)
    } else {
      const newAdmin = await createAdminUser(form.value)
      admins.value.unshift(newAdmin)
    }
    showModal.value = false
  } catch (e) {
    console.error('Failed to save admin', e)
  } finally {
    creating.value = false
  }
}

const deleteAdminHandler = async (admin: any) => {
  if (!confirm('Are you sure you want to delete this admin?')) return
  try {
    await deleteAdminUser(admin.id)
    admins.value = admins.value.filter(a => a.id !== admin.id)
  } catch (e) {
    console.error('Failed to delete admin', e)
    admins.value = admins.value.filter(a => a.id !== admin.id) // fallback
  }
}

const toggleStatus = async (admin: any) => {
  try {
    const newStatus = admin.status === 'active' ? 'inactive' : 'active'
    await updateAdminUser(admin.id, { status: newStatus })
    admin.status = newStatus
  } catch (e) {
    console.error('Failed to toggle status', e)
    admin.status = admin.status === 'active' ? 'inactive' : 'active' // fallback
  }
}
</script>

<template>
<AdminLayout>
<section class="p-3 text-white space-y-4 min-h-screen">

  <!-- HEADER -->
  <div class="flex justify-between items-center">
    <h1 class="text-xl font-bold flex items-center gap-2">Admins</h1>
    <button @click="openCreateModal" class="flex items-center gap-2 bg-green-500 text-black px-3 py-1.5 rounded-lg text-sm active:scale-95">
      <Plus class="w-4 h-4"/> New Admin
    </button>
  </div>

  <!-- SEARCH + FILTER -->
  <div class="flex gap-2 mt-2">
    <input v-model="search" placeholder="Search admins" class="w-full bg-white/5 p-2 rounded-lg outline-none text-sm"/>
    <select v-model="activeFilter" class="bg-white/5 p-2 rounded-lg text-black text-sm">
      <option value="all">All</option>
      <option value="active">Active</option>
      <option value="inactive">Inactive</option>
    </select>
  </div>

  <!-- ADMIN LIST -->
  <div class="mt-4 space-y-3">
    <div v-for="admin in filteredAdmins" :key="admin.id" class="bg-white/5 p-4 rounded-xl border border-white/10 flex justify-between items-center">
      <div>
        <p class="font-bold">{{ admin.name }}</p>
        <p class="text-xs text-white/50">{{ admin.email }} · {{ admin.role }}</p>
      </div>
      <div class="flex gap-2 items-center">
        <button @click="toggleStatus(admin)" :class="['px-2 py-1 rounded-lg text-xs', admin.status==='active'?'bg-green-500 text-black':'bg-red-500 text-black']">
          {{ admin.status==='active'?'Active':'Inactive' }}
        </button>
        <button @click="openEditModal(admin)" class="p-2 rounded-lg hover:bg-white/5">
          <Pencil class="w-4 h-4"/>
        </button>
        <button @click="deleteAdmin(admin)" class="p-2 rounded-lg hover:bg-red-500/30">
          <Trash class="w-4 h-4"/>
        </button>
      </div>
    </div>
  </div>

  <!-- CREATE/EDIT MODAL -->
  <div v-if="showModal" class="fixed inset-0 bg-black/50 z-[999] flex items-end sm:items-center justify-center px-3">
    <div class="bg-[#0B0E11] w-full sm:max-w-md rounded-t-2xl sm:rounded-xl p-5 space-y-3 relative">
      <button class="absolute top-3 right-3" @click="showModal=false"><X class="w-5 h-5"/></button>
      <h2 class="text-lg font-semibold">{{ editingAdmin?'Edit Admin':'New Admin' }}</h2>

      <div class="space-y-2">
        <input v-model="form.name" placeholder="Name" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
        <input v-model="form.email" placeholder="Email" type="email" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
        <input v-model="form.password" placeholder="Password" type="password" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
        <select v-model="form.role" class="w-full p-2 rounded-lg bg-white/5 text-black">
          <option>Admin</option>
          <option>Super Admin</option>
        </select>
        <select v-model="form.status" class="w-full p-2 rounded-lg bg-white/5 text-black">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>

      <button @click="saveAdmin" class="mt-3 w-full py-2 rounded-lg bg-green-500 text-black flex justify-center">
        <span v-if="!creating">{{ editingAdmin?'Update':'Create' }}</span>
        <RefreshCw v-else class="w-5 h-5 animate-spin"/>
      </button>
    </div>
  </div>

</section>
</AdminLayout>
</template>

<style scoped>
</style>