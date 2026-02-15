<script setup lang="ts">
import AdminLayout from '../AdminLayout.vue'
import { Search, Eye, UserX, Users, Activity } from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'
import api from '@/lib/api'
import { deleteUser } from '@/lib/api'

const search = ref('')
const loading = ref(true)

const users = ref([])

const loadUsers = async () => {
  loading.value = true
  try {
    const res = await api.get('/admin/users', { params: { status: 'active' } })
    users.value = res.data.data ?? res.data
    users.value = users.value.map(u => ({ ...u, balance: u.balance ?? '0.00', lastLogin: u.last_login ?? 'N/A' }))
  } catch (e) {
    console.error('Failed to load active users', e)
    users.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => { void loadUsers() })

const filteredUsers = computed(() => {
  return users.value.filter(u =>
    u.name?.toLowerCase().includes(search.value.toLowerCase()) ||
    u.email?.toLowerCase().includes(search.value.toLowerCase())
  )
})

const removeUser = async (userId: number) => {
  if (!confirm('Delete this user?')) return
  try {
    await deleteUser(userId)
    users.value = users.value.filter(u => u.id !== userId)
  } catch (err) {
    console.error('Failed to delete user', err)
  }
}
</script>

<template>
  <AdminLayout>
    <section class="p-4 text-white space-y-6">

      <!-- HEADER -->
      <div class="relative overflow-hidden rounded-xl border border-white/5 bg-gray-900/40 p-4">
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-green-500/20 blur-3xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:justify-between gap-3">
          <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
              <Activity class="w-6 h-6 text-green-400" />
              Active Users
            </h1>
            <p class="text-white/60 text-sm">
              Users currently enabled on the platform
            </p>
          </div>

          <div class="flex items-center gap-2">
            <div class="relative">
              <Search class="absolute left-2 top-2.5 w-4 h-4 text-white/50" />
              <input
                v-model="search"
                placeholder="Search active users..."
                class="bg-white/5 border border-white/10 rounded-lg pl-8 pr-3 py-2 text-sm outline-none focus:border-green-500"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- STATS -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="stat-card">
          <Users class="w-5 h-5 text-green-400" />
          <div>
            <p class="text-xs text-white/60">Active Users</p>
            <p class="text-lg font-bold">{{ users.length }}</p>
          </div>
        </div>
        <div class="stat-card">
          <Activity class="w-5 h-5 text-blue-400" />
          <div>
            <p class="text-xs text-white/60">Online Now</p>
            <p class="text-lg font-bold">124</p>
          </div>
        </div>
        <div class="stat-card">
          <Users class="w-5 h-5 text-yellow-400" />
          <div>
            <p class="text-xs text-white/60">New Today</p>
            <p class="text-lg font-bold">32</p>
          </div>
        </div>
        <div class="stat-card">
          <Users class="w-5 h-5 text-purple-400" />
          <div>
            <p class="text-xs text-white/60">VIP Users</p>
            <p class="text-lg font-bold">8</p>
          </div>
        </div>
      </div>

      <!-- TABLE -->
      <div class="bg-gray-900/40 backdrop-blur-xl rounded-xl border border-white/5 overflow-hidden">

        <table class="w-full text-sm">
          <thead class="bg-white/5">
            <tr>
              <th class="px-3 py-3 text-left">User</th>
              <th class="px-3 py-3 text-left">Balance</th>
              <th class="px-3 py-3 text-left">Last Login</th>
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

              <td class="px-3 py-3 text-green-400 text-sm">
                {{ u.lastLogin }}
              </td>

              <td class="px-3 py-3 text-right">
                <div class="flex justify-end gap-2">
                  <button class="action-btn bg-blue-500/20 text-blue-400">
                    <Eye class="w-4 h-4" />
                  </button>
                  <button @click="removeUser(u.id)" class="action-btn bg-red-500/20 text-red-400">
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