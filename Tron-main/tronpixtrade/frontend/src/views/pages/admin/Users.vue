<script setup lang="ts">
import AdminLayout from './AdminLayout.vue'
import { Search, ShieldCheck, UserX, Eye, Users, UserCheck } from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'
import { fetchAdminUsersList } from '@/lib/api'

// ------------------- STATE -------------------
const search = ref('')
const filter = ref('all')
const loading = ref(true)
const users = ref<any[]>([])

// ------------------- FETCH -------------------
const loadUsers = async () => {
  loading.value = true
  try {
    // Fetch bank/broker users
    const list = await fetchAdminUsersList({ type: 'broker' }) // or 'bank'
    users.value = list.map((u: any) => ({
      id: u.id,
      name: u.name,
      email: u.email,
      status: u.status ?? 'active',
      kyc: u.kyc_verified ?? false,
      balance: u.balance ?? '$0.00',
    }))
  } catch (e) {
    console.error('Failed to load users', e)
    users.value = []
  } finally {
    loading.value = false
  }
}

// Fetch on mount
onMounted(() => { void loadUsers() })

// ------------------- COMPUTED -------------------
const filteredUsers = computed(() =>
  users.value.filter(u => {
    const matchesSearch =
      u.name?.toLowerCase().includes(search.value.toLowerCase()) ||
      u.email?.toLowerCase().includes(search.value.toLowerCase())
    const matchesFilter = filter.value === 'all' || u.status === filter.value
    return matchesSearch && matchesFilter
  })
)
</script>


<template>
  <AdminLayout>
    <section class="p-4 text-white space-y-6">

      <!-- HEADER -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
          <h1 class="text-2xl font-bold">Users Management</h1>
          <p class="text-white/60 text-sm">Manage all registered customers</p>
        </div>

        <div class="flex gap-2">
          <div class="relative">
            <Search class="absolute left-2 top-2.5 w-4 h-4 text-white/50" />
            <input
              v-model="search"
              placeholder="Search users..."
              class="bg-white/5 border border-white/10 rounded-lg pl-8 pr-3 py-2 text-sm outline-none focus:border-green-500"
            />
          </div>

          <select
            v-model="filter"
            class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm"
          >
            <option value="all">All</option>
            <option value="active">Active</option>
            <option value="pending">Pending</option>
            <option value="suspended">Suspended</option>
          </select>
        </div>
      </div>

      <!-- STATS -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="stat-card">
          <Users class="w-5 h-5 text-green-400" />
          <div>
            <p class="text-xs text-white/60">Total Users</p>
            <p class="text-lg font-bold">12,480</p>
          </div>
        </div>
        <div class="stat-card">
          <UserCheck class="w-5 h-5 text-blue-400" />
          <div>
            <p class="text-xs text-white/60">KYC Verified</p>
            <p class="text-lg font-bold">8,930</p>
          </div>
        </div>
        <div class="stat-card">
          <ShieldCheck class="w-5 h-5 text-yellow-400" />
          <div>
            <p class="text-xs text-white/60">Pending KYC</p>
            <p class="text-lg font-bold">2,120</p>
          </div>
        </div>
        <div class="stat-card">
          <UserX class="w-5 h-5 text-red-400" />
          <div>
            <p class="text-xs text-white/60">Suspended</p>
            <p class="text-lg font-bold">340</p>
          </div>
        </div>
      </div>

      <!-- USERS TABLE -->
      <div class="bg-gray-900/40 backdrop-blur-xl rounded-xl border border-white/5 overflow-hidden">

        <table class="w-full text-sm">
          <thead class="bg-white/5">
            <tr>
              <th class="px-3 py-3 text-left">User</th>
              <th class="px-3 py-3 text-left">Balance</th>
              <th class="px-3 py-3 text-left">Status</th>
              <th class="px-3 py-3 text-left">KYC</th>
              <th class="px-3 py-3 text-right">Action</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="u in filteredUsers"
              :key="u.id"
              class="border-b border-white/5 hover:bg-white/5 transition"
            >
              <td class="px-3 py-3">
                <div class="flex items-center gap-3">
                  <img src="https://i.pravatar.cc/40" class="w-8 h-8 rounded-full" />
                  <div>
                    <p class="font-semibold">{{ u.name }}</p>
                    <p class="text-xs text-white/50">{{ u.email }}</p>
                  </div>
                </div>
              </td>

              <td class="px-3 py-3 font-semibold">{{ u.balance }}</td>

              <td class="px-3 py-3">
                <span
                  class="px-2 py-1 rounded-full text-xs"
                  :class="{
                    'bg-green-500/20 text-green-400': u.status==='active',
                    'bg-yellow-500/20 text-yellow-400': u.status==='pending',
                    'bg-red-500/20 text-red-400': u.status==='suspended'
                  }"
                >
                  {{ u.status }}
                </span>
              </td>

              <td class="px-3 py-3">
                <span
                  v-if="u.kyc"
                  class="flex items-center gap-1 text-green-400 text-xs"
                >
                  <ShieldCheck class="w-4 h-4" /> Verified
                </span>
                <span v-else class="text-yellow-400 text-xs">Pending</span>
              </td>

              <td class="px-3 py-3 text-right">
                <div class="flex justify-end gap-2">
                  <button class="action-btn bg-blue-500/20 text-blue-400">
                    <Eye class="w-4 h-4" />
                  </button>
                  <button class="action-btn bg-red-500/20 text-red-400">
                    <UserX class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

      </div>

    </section>
  </AdminLayout>
</template>

<style scoped>
.stat-card {
  display: flex;
  gap: 10px;
  align-items: center;
  padding: 12px;
  border-radius: 14px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.05);
  transition: all 0.3s;
}
.stat-card:hover {
  box-shadow: 0 0 25px rgba(34,197,94,0.15);
  transform: translateY(-2px);
}

.action-btn {
  padding: 6px;
  border-radius: 8px;
  transition: all 0.2s;
}
.action-btn:hover {
  transform: scale(1.1);
}
</style>