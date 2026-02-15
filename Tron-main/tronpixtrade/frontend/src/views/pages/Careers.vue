<script setup lang="ts">
import { ArrowLeft, MapPin, Briefcase, Users } from 'lucide-vue-next'
import { useRouter } from 'vue-router'

const router = useRouter()

const openPositions = [
  {
    title: 'Senior Frontend Developer',
    department: 'Engineering',
    location: 'Remote',
    type: 'Full-time'
  },
  {
    title: 'Backend Engineer (Node.js/Python)',
    department: 'Engineering',
    location: 'Remote',
    type: 'Full-time'
  },
  {
    title: 'DevOps Engineer',
    department: 'Engineering',
    location: 'Remote',
    type: 'Full-time'
  },
  {
    title: 'Product Manager',
    department: 'Product',
    location: 'Remote',
    type: 'Full-time'
  },
  {
    title: 'Customer Success Manager',
    department: 'Support',
    location: 'Remote',
    type: 'Full-time'
  },
  {
    title: 'UI/UX Designer',
    department: 'Design',
    location: 'Remote',
    type: 'Full-time'
  }
]
</script>

<template>
  <div class="min-h-screen bg-black text-white">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-black shadow-sm px-4 py-4 flex items-center gap-3 border-b border-white/10">
      <button @click="router.back()" class="text-white/70 hover:text-white">
        <ArrowLeft class="w-5 h-5" />
      </button>
      <h1 class="font-semibold text-lg">Careers</h1>
    </div>

    <!-- Content -->
    <div class="max-w-5xl mx-auto px-4 py-12">
      <!-- Hero Section -->
      <div class="mb-12 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Join Our Team</h2>
        <p class="text-white/60 text-lg">Help us build the future of trading</p>
      </div>

      <!-- Why Join Us -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white/5 rounded-lg p-6 border border-white/10">
          <Users class="w-8 h-8 text-green-400 mb-3" />
          <h3 class="font-semibold mb-2">Collaborative Culture</h3>
          <p class="text-white/60 text-sm">Work with talented professionals in a supportive environment</p>
        </div>
        <div class="bg-white/5 rounded-lg p-6 border border-white/10">
          <Briefcase class="w-8 h-8 text-green-400 mb-3" />
          <h3 class="font-semibold mb-2">Growth Opportunities</h3>
          <p class="text-white/60 text-sm">Continuous learning and career development programs</p>
        </div>
        <div class="bg-white/5 rounded-lg p-6 border border-white/10">
          <Users class="w-8 h-8 text-green-400 mb-3" />
          <h3 class="font-semibold mb-2">Remote Friendly</h3>
          <p class="text-white/60 text-sm">Flexible work arrangements and global team opportunities</p>
        </div>
      </div>

      <!-- Open Positions -->
      <div>
        <h3 class="text-2xl font-bold mb-6">Open Positions</h3>
        <div class="space-y-4">
          <a
            v-for="position in openPositions"
            :key="position.title"
            href="#"
            class="block bg-white/5 rounded-lg p-6 border border-white/10 hover:border-green-500/30 hover:bg-white/10 transition"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h4 class="font-semibold text-lg mb-2">{{ position.title }}</h4>
                <div class="flex flex-wrap gap-4 text-white/60 text-sm">
                  <span class="flex items-center gap-1">
                    <Briefcase class="w-4 h-4" />
                    {{ position.department }}
                  </span>
                  <span class="flex items-center gap-1">
                    <MapPin class="w-4 h-4" />
                    {{ position.location }}
                  </span>
                  <span>{{ position.type }}</span>
                </div>
              </div>
              <div class="text-green-400">→</div>
            </div>
          </a>
        </div>
      </div>

      <!-- Benefits Section -->
      <div class="mt-12">
        <h3 class="text-2xl font-bold mb-6">Benefits & Perks</h3>
        <div class="bg-white/5 rounded-lg p-8 border border-white/10">
          <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <li class="flex items-center gap-3 text-white/80">
              <span class="w-2 h-2 bg-green-400 rounded-full"></span>
              Competitive salary and equity
            </li>
            <li class="flex items-center gap-3 text-white/80">
              <span class="w-2 h-2 bg-green-400 rounded-full"></span>
              Health insurance coverage
            </li>
            <li class="flex items-center gap-3 text-white/80">
              <span class="w-2 h-2 bg-green-400 rounded-full"></span>
              Professional development budget
            </li>
            <li class="flex items-center gap-3 text-white/80">
              <span class="w-2 h-2 bg-green-400 rounded-full"></span>
              Flexible working hours
            </li>
            <li class="flex items-center gap-3 text-white/80">
              <span class="w-2 h-2 bg-green-400 rounded-full"></span>
              Remote work options
            </li>
            <li class="flex items-center gap-3 text-white/80">
              <span class="w-2 h-2 bg-green-400 rounded-full"></span>
              Team outings and events
            </li>
          </ul>
        </div>
      </div>

      <!-- CTA -->
      <div class="mt-12 bg-gradient-to-r from-green-500/10 to-transparent border border-green-500/20 rounded-lg p-8 text-center">
        <h3 class="text-xl font-semibold mb-3">Don't see a position that fits?</h3>
        <p class="text-white/60 mb-6">Send us your resume and let us know what you're interested in</p>
        <a
          href="mailto:careers@tronpixtrades.com"
          class="inline-block px-6 py-2 bg-green-500 text-black font-semibold rounded-lg hover:bg-green-400 transition"
        >
          Send Your Resume
        </a>
      </div>
    </div>
  </div>
</template>
