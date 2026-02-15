<script setup lang="ts">
import {
  Facebook,
  Twitter,
  Linkedin,
  Instagram,
  Github,
} from "lucide-vue-next";
</script>

<template>
  <footer class="bg-[#0a0a0a] text-gray-400 px-6 lg:px-20 pt-16 pb-8">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-12">

      <!-- Brand -->
      <div>
        <h3 class="text-white text-lg font-semibold mb-4">
          TronpixTrades
        </h3>
        <p class="text-sm leading-relaxed">
          Trade forex, crypto, and commodities with confidence.
        </p>
      </div>

      <!-- Platform -->
      <div>
        <h4 class="text-white font-medium mb-4">Platform</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="/markets" class="hover:text-cyan-400">Markets</a></li>
          <li><a href="/trading-tools" class="hover:text-cyan-400">Trading Tools</a></li>
          <li><a href="/pricing" class="hover:text-cyan-400">Pricing</a></li>
          <li><a href="/mobile" class="hover:text-cyan-400">Mobile App</a></li>
        </ul>
      </div>

      <!-- Company -->
      <div>
        <h4 class="text-white font-medium mb-4">Company</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="/about" class="hover:text-cyan-400">About Us</a></li>
          <li><a href="/careers" class="hover:text-cyan-400">Careers</a></li>
          <li><a href="/partners" class="hover:text-cyan-400">Partners</a></li>
          <li><a href="/blog" class="hover:text-cyan-400">Blog</a></li>
        </ul>
      </div>

      <!-- Legal & Support -->
      <div>
        <h4 class="text-white font-medium mb-4">Support & Legal</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="/help" class="hover:text-cyan-400">Help Center</a></li>
          <li><a href="/contact" class="hover:text-cyan-400">Contact Support</a></li>
          <li><a href="/privacy" class="hover:text-cyan-400">Privacy Policy</a></li>
          <li><a href="/terms" class="hover:text-cyan-400">Terms of Service</a></li>
        </ul>

        <!-- Social Icons -->
        <div class="flex gap-4 mt-6">
          <a href="https://facebook.com/tronpixtrades" aria-label="Facebook" class="hover:text-cyan-400" target="_blank" rel="noopener noreferrer">
            <Facebook class="w-5 h-5" />
          </a>
          <a href="https://twitter.com/tronpixtrades" aria-label="Twitter" class="hover:text-cyan-400" target="_blank" rel="noopener noreferrer">
            <Twitter class="w-5 h-5" />
          </a>
          <a href="https://linkedin.com/company/tronpixtrades" aria-label="LinkedIn" class="hover:text-cyan-400" target="_blank" rel="noopener noreferrer">
            <Linkedin class="w-5 h-5" />
          </a>
          <a href="https://instagram.com/tronpixtrades" aria-label="Instagram" class="hover:text-cyan-400" target="_blank" rel="noopener noreferrer">
            <Instagram class="w-5 h-5" />
          </a>
          <a href="https://github.com/TronpixTrades" aria-label="GitHub" class="hover:text-cyan-400" target="_blank" rel="noopener noreferrer">
            <Github class="w-5 h-5" />
          </a>
        </div>
      </div>

    </div>

    <!-- Bottom -->
    <div class="mt-12 border-t border-gray-800 pt-6 text-center text-xs text-gray-500">
      © 2026 TronpixTrades. All rights reserved.
    </div>
  </footer>
</template>