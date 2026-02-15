<script setup lang="ts">
import { ArrowRight, DownloadCloud } from 'lucide-vue-next'
</script>

<template>
<section class="relative overflow-hidden bg-black py-28 px-6 lg:px-20 text-white">

  <!-- Floating tiny orbs -->
  <div class="absolute inset-0 pointer-events-none">
    <span class="orb" style="top:10%; left:15%; background:#22c55e; animation-duration:10s;"></span>
    <span class="orb" style="top:40%; left:70%; background:#16a34a; animation-duration:12s;"></span>
    <span class="orb" style="top:60%; left:30%; background:#0ea5e9; animation-duration:14s;"></span>
    <span class="orb" style="top:80%; left:50%; background:#34d399; animation-duration:16s;"></span>
    <span class="orb" style="top:25%; left:50%; background:#22c55e; animation-duration:11s;"></span>
    <span class="orb" style="top:70%; left:20%; background:#0ea5e9; animation-duration:13s;"></span>
  </div>

  <!-- Content -->
  <div class="relative z-10 max-w-5xl mx-auto text-center">
    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
      Ready to Trade with Confidence?
    </h2>

    <p class="text-white/70 text-base md:text-lg mb-14 max-w-3xl mx-auto">
      Access real-time market data, professional tools, and secure withdrawals —
      all from one powerful trading platform.
    </p>

    <div class="flex flex-col sm:flex-row justify-center items-center gap-6">
      <a href="/download" class="cta-primary">
        <DownloadCloud class="w-5 h-5" />
        Download App
      </a>

      <a href="/dashboard" class="cta-secondary">
        Start Trading
        <ArrowRight class="w-5 h-5" />
      </a>
    </div>

    <p class="text-white/50 text-sm mt-8">
      Available on Web, Android & iOS
    </p>
  </div>
</section>
</template>

<style scoped>
/* ===== TINY FLOATING ORBS ===== */
.orb { 
  position: absolute;
  width: 45px;
  height: 45px;
  border-radius: 50%;
  filter: drop-shadow(0 0 15px currentColor);
  opacity: 0.4;
  animation: floatUpDown linear infinite alternate;
}

.orb-1 { width: 30px; height: 30px; top:10%; left:15%; background:#22c55e; animation-duration:12s; }
.orb-2 { width: 22px; height:22px; top:40%; left:70%; background:#16a34a; animation-duration:14s; }
.orb-3 { width: 28px; height:28px; top:60%; left:30%; background:#0ea5e9; animation-duration:16s; }
.orb-4 { width: 20px; height:20px; top:20%; left:50%; background:#34d399; animation-duration:18s; }
.orb-5 { width: 35px; height:35px; top:70%; left:80%; background:#10b981; animation-duration:20s; }

@keyframes floatUpDown {
  0%   { transform: translate(0px, 0px); }
  25%  { transform: translate(10px, -20px); }
  50%  { transform: translate(-10px, 15px); }
  75%  { transform: translate(15px, -10px); }
  100% { transform: translate(0px, 0px); }
}

/* ===== CTA BUTTONS ===== */
.cta-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.9rem 1.8rem;
  border-radius: 9999px;
  font-weight: 600;
  background: rgba(34, 197, 94, 0.15);
  border: 1px solid rgba(34, 197, 94, 0.5);
  backdrop-filter: blur(12px);
  transition: all 0.3s ease;
}

.cta-primary:hover {
  background: rgba(34, 197, 94, 0.25);
  box-shadow: 0 0 30px rgba(34, 197, 94, 0.5);
  transform: translateY(-2px);
}

.cta-secondary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.9rem 1.8rem;
  border-radius: 9999px;
  font-weight: 600;
  border: 1px solid rgba(34, 197, 94, 0.5);
  color: rgb(34, 197, 94);
  transition: all 0.3s ease;
}

.cta-secondary:hover {
  background: rgba(34, 197, 94, 0.15);
  box-shadow: 0 0 25px rgba(34, 197, 94, 0.35);
  transform: translateY(-2px);
}
</style>