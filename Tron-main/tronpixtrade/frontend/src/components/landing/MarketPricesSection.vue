<script setup lang="ts">
import { ArrowUp, ArrowDown } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// PriceData: { pair: string, bid: number, ask: number, change: number, volume: number, icon?: string }
const prices = ref([]);

// Example: fetch live crypto prices from CoinGecko
const fetchCryptoPrices = async () => {
  try {
    const res = await axios.get(
      'https://api.coingecko.com/api/v3/coins/markets',
      {
        params: {
          vs_currency: 'usd',
          ids: 'bitcoin,ethereum,tether',
          order: 'market_cap_desc',
          per_page: 10,
          page: 1,
          sparkline: false,
          price_change_percentage: '24h'
        }
      }
    );

    prices.value = res.data.map((coin) => ({
      pair: `${coin.symbol.toUpperCase()}/USD`,
      bid: coin.current_price,
      ask: coin.current_price + 0.5, // simulated small spread
      change: coin.price_change_percentage_24h,
      volume: coin.total_volume,
      icon: coin.image
    }));
  } catch (err) {
    console.error('Failed to fetch crypto prices:', err);
  }
};

// Optional: fetch forex prices from a public API (replace with your API)
const fetchForexPrices = async () => {
  try {
    const res = await axios.get('https://api.exchangerate.host/latest?base=USD');
    // Map to some example pairs
    const data = [
      { pair: 'EUR/USD', bid: res.data.rates.EUR, ask: res.data.rates.EUR + 0.0001, change: 0.12, volume: 10000 },
      { pair: 'GBP/USD', bid: res.data.rates.GBP, ask: res.data.rates.GBP + 0.0001, change: -0.23, volume: 8000 }
    ];
    prices.value.push(...data);
  } catch (err) {
    console.error('Failed to fetch forex prices:', err);
  }
};

onMounted(async () => {
  await fetchCryptoPrices();
  await fetchForexPrices();

  // Optionally refresh every 30s
  setInterval(async () => {
    await fetchCryptoPrices();
    await fetchForexPrices();
  }, 30000);
});
</script>

<template>
  <section class="w-full bg-[#0a0a0a] py-16 px-6 lg:px-20 text-white">
    <div class="max-w-6xl mx-auto">
      <h2 class="text-3xl font-bold mb-2">Live Forex & Crypto Prices</h2>
      <p class="text-gray-400 mb-8">
        Track the latest currency and crypto market movements in real-time.
      </p>

      <div class="overflow-x-auto rounded-lg shadow-lg">
        <table class="w-full text-left border-collapse">
          <thead class="bg-[#121212]">
            <tr>
              <th class="px-6 py-3 font-medium text-gray-400">Pair</th>
              <th class="px-6 py-3 font-medium text-gray-400">Bid</th>
              <th class="px-6 py-3 font-medium text-gray-400">Ask</th>
              <th class="px-6 py-3 font-medium text-gray-400">24h Change</th>
              <th class="px-6 py-3 font-medium text-gray-400">Volume</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="price in prices"
              :key="price.pair"
              class="border-b border-[#2a2a2a] hover:bg-[#1a1a1a] transition-colors duration-300"
            >
              <td class="px-6 py-4 font-semibold flex items-center gap-2">
                <img v-if="price.icon" :src="price.icon" class="w-5 h-5 rounded-full" alt="icon" />
                {{ price.pair }}
              </td>
              <td class="px-6 py-4">{{ price.bid.toFixed(4) }}</td>
              <td class="px-6 py-4">{{ price.ask.toFixed(4) }}</td>
              <td class="px-6 py-4 flex items-center gap-1"
                  :class="price.change >= 0 ? 'text-green-400' : 'text-red-500'">
                <component :is="price.change >= 0 ? ArrowUp : ArrowDown" class="w-4 h-4" />
                {{ Math.abs(price.change).toFixed(2) }}%
              </td>
              <td class="px-6 py-4">{{ price.volume.toLocaleString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>