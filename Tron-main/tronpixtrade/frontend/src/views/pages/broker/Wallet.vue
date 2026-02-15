<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { ArrowLeft, Eye, EyeOff, Plus } from 'lucide-vue-next'
import { useWallet } from '@/composables/useWallet'
import { submitBrokerWithdrawal, csrf } from '@/lib/api'

/* -------------------------
STATE
------------------------- */
const showWithdrawConfirm = ref(false)
const showPinModal = ref(false)
const pin = ref('')
const txHash = ref('')

const txResult = ref({
  status: 'idle',
  title: '',
  message: '',
})

const closeResult = () => { txResult.value.status = 'idle' }

/* -------------------------
WALLETS
------------------------- */
const { wallets: fetchedWallets, fetchWallets } = useWallet()
const walletsRef = ref({ spot: { available: 0, inOrders: 0, unrealizedPnl: 0, assets: [] }, futures: { available: 0, inOrders: 0, unrealizedPnl: 0, assets: [] } })

const mapFetched = () => {
  const list = fetchedWallets.value || []
  const spotAssets = []
  const futuresAssets = []

  list.forEach(w => {
    spotAssets.push({
      symbol: w.currency || 'USD',
      name: w.currency || 'USD',
      balance: Number(w.balance) || 0,
      price: 1,
      logo: '',
      wallet_id: w.id,
    })
  })

  walletsRef.value.spot.assets = spotAssets
  walletsRef.value.spot.available = spotAssets.reduce((s, a) => s + (a.balance || 0) * (a.price || 1), 0)
  walletsRef.value.futures.assets = futuresAssets
  walletsRef.value.futures.available = 0
}

onMounted(async () => {
  await fetchWallets()
  mapFetched()
})

const activeAccount = ref('spot')
const wallet = computed(() => walletsRef.value[activeAccount.value])

/* -------------------------
WITHDRAWAL LOGIC
------------------------- */
const withdraw = ref({
  amount: '',
  wallet_id: '',
  currency: 'USDT',
  withdrawal_method: '',
  destination: '',
})

const withdrawFee = 1.5
const withdrawMin = 10

const netReceive = computed(() => {
  const amt = Number(withdraw.value.amount || 0)
  return amt > withdrawFee ? amt - withdrawFee : 0
})

const verifyPinAndWithdraw = async () => {
  showPinModal.value = false
  txResult.value = { status: 'processing', title: 'Processing Withdrawal', message: 'Submitting your request...' }

  try {
    await csrf()
    const data = await submitBrokerWithdrawal({
      wallet_id: withdraw.value.wallet_id,
      amount: Number(withdraw.value.amount),
      currency: withdraw.value.currency,
      withdrawal_method: withdraw.value.withdrawal_method,
      destination: withdraw.value.destination,
      pin: pin.value,
    })

    txHash.value = `TX${(data.id || 0).toString().padStart(8, '0')}`
    txResult.value = { status: 'success', title: 'Withdrawal Submitted', message: 'Your withdrawal is now pending.' }

    pin.value = ''
    await fetchWallets()
    mapFetched()
  } catch (err: any) {
    txResult.value = { status: 'error', title: 'Withdrawal Failed', message: err.response?.data?.message || 'Something went wrong' }
  }
}
</script>

<template>  
  <section class="bg-black min-h-screen text-white text-[14px] pt-[56px]">  
    <div class="max-w-sm mx-auto px-4 pb-16">  <!-- HEADER -->

<div  
  class="fixed top-0 left-0 right-0 z-30 bg-black border-b border-white/10 shadow-[0_1px_0_rgba(255,255,255,0.06)] px-3">    <div class="flex items-center justify-between py-4 relative">  
    <!-- Left -->  
    <div class="flex items-center gap-3">  
      <ArrowLeft
  class="w-5 h-5 text-white/70 cursor-pointer"
  @click="goBack()"
/>
      <h1 class="text-[16px] font-semibold">Wallet</h1>  
    </div>  <!-- Right: Portfolio Selector -->  
<button  
  @click="showPortfolioMenu = !showPortfolioMenu"  
  class="flex items-center gap-1 text-[13px] text-white/80"  
>  
  <span>Portfolio · {{ accountLabel }}</span>  
  <ChevronDown class="w-4 h-4" />  
</button>  

<!-- Dropdown -->  
<div  
  v-if="showPortfolioMenu"  
  class="absolute right-0 top-[56px] w-36 bg-[#0b0b0b] rounded-xl shadow-lg border border-white/10 overflow-hidden"  
>  
  <button  
    class="flex items-center justify-between w-full px-4 py-3 text-[13px] hover:bg-white/5"  
    @click="activeAccount = 'spot'; showPortfolioMenu = false"  
  >  
    Spot  
    <Check v-if="activeAccount === 'spot'" class="w-4 h-4 text-green-400" />  
  </button>  

  <button  
    class="flex items-center justify-between w-full px-4 py-3 text-[13px] hover:bg-white/5"  
    @click="activeAccount = 'futures'; showPortfolioMenu = false"  
  >  
    Futures  
    <Check v-if="activeAccount === 'futures'" class="w-4 h-4 text-green-400" />  
  </button>  
</div>

  </div>  
</div>  <!-- BALANCE -->

  <div class="mb-5 px-1">    
    <div class="flex justify-between text-white/60 mt-5 mb-1">    
      <span>Total Equity</span>    
    </div>    <div class="text-[26px] font-semibold mb-3">    
  <span v-if="showBalance">${{ totalBalance.toFixed(2) }}</span>    
  <span v-else>********</span>    
<button @click="showBalance = !showBalance" class="ml-[13px]">    
    <Eye v-if="showBalance" class="w-4 h-4" />    
    <EyeOff v-else class="w-4 h-4" />    
  </button>    
</div>    

<div class="grid grid-cols-3 text-[12px] text-white/60">    
  <div>    
    <p>Available</p>    
    <p class="text-white">${{ wallet.available.toFixed(2) }}</p>    
  </div>    
  <div>    
    <p>In Orders</p>    
    <p class="text-white">${{ wallet.inOrders.toFixed(2) }}</p>    
  </div>    
  <div>    
    <p>PnL</p>    
    <p :class="wallet.unrealizedPnl >= 0 ? 'text-green-400' : 'text-red-400'">    
      {{ wallet.unrealizedPnl >= 0 ? '+' : '' }}${{ wallet.unrealizedPnl.toFixed(2) }}    
    </p>    
  </div>    
</div>

  </div>    <!-- ACTIONS -->  
  <div class="flex px-1 gap-3 mb-6">

<button class="wallet-action primary" @click="showDepositModal = true">
<Plus class="w-4 h-4" /> Deposit
</button>
<button class="wallet-action" @click="showWithdrawModal = true">
<ArrowUpRight class="w-4 h-4" /> Withdraw
</button>
<button class="wallet-action" @click="showTransferModal = true">
<ArrowLeftRight class="w-4 h-4" /> Transfer
</button>
</div>

<!-- MODALS -->  
  <!-- INTERNAL TRANSFER MODAL -->

<div  
  v-if="showTransferModal"  
  class="fixed inset-0 z-50 bg-black/60 flex items-end"  
  @click.self="showTransferModal = false"  
>  
  <div class="bg-[#0b0b0b] w-full rounded-t-2xl p-4 max-h-[70vh]">  <div class="flex justify-between items-center mb-4">  
  <p class="font-semibold text-[15px]">Internal Transfer</p>  
  <X class="w-5 h-5 text-white/60" @click="showTransferModal = false" />  
</div>  

<!-- FROM / TO -->  
<div class="bg-white/5 rounded-xl p-3 mb-4">   
<!-- ASSET SELECTOR -->

<button class="flex items-center gap-3 bg-white/5 rounded-xl p-3 mb-4">  
  <img :src="selectedAsset?.logo" class="w-6 h-6 rounded-full" />  
  <span class="font-medium">{{ selectedAsset?.symbol }}</span>  
  <ChevronDown class="w-4 h-4 ml-auto text-white/40" />  
</button>  <div class="flex justify-between items-center text-[13px]">  
    <span>From</span>  
    <span class="text-white/80 capitalize">{{ transfer.from }}</span>  
  </div>  

  <div class="flex justify-center my-2">  
    <button  
      class="p-2 rounded-full bg-white/10"  
      @click="switchTransferDirection"  
    >  
      <ArrowLeftRight class="w-4 h-4" />  
    </button>  
  </div>  

  <div class="flex justify-between items-center text-[13px]">  
    <span>To</span>  
    <span class="text-white/80 capitalize">{{ transfer.to }}</span>  
  </div>  
</div>  

<!-- AMOUNT -->  
<div class="mb-4">  
  <p class="text-[12px] text-white/60 mb-1">Amount (USDT)</p>  
  <div class="flex items-center bg-white/5 rounded-xl px-3 py-2">  
    <input  
      v-model="transfer.amount"  
      type="number"  
      placeholder="0.00"  
      class="bg-transparent outline-none flex-1"  
    />  
    <button class="text-green-400 text-[12px]">Max</button>  
  </div>  
</div>  

<button class="wallet-action primary w-full">  
  Confirm Transfer  
</button>

  </div>  
</div>  <!-- DEPOSIT MODAL -->  <div  
  v-if="showDepositModal"  
  class="fixed inset-0 z-50 bg-black/60 flex items-end"  
  @click.self="showDepositModal = false"  
>  
  <div class="bg-[#0b0b0b] w-full rounded-t-2xl p-4 max-h-[70vh]">  <div class="flex justify-between items-center mb-4">  
  <p class="font-semibold text-[15px]">Deposit USDT</p>  
  <X class="w-5 h-5 text-white/60" @click="showDepositModal = false" />  
</div>  

<!-- NETWORK -->  
<div class="mb-4">  
  <p class="text-[12px] text-white/60 mb-1">Network</p>  
  <select  
    v-model="deposit.network"  
    class="w-full bg-white/5 rounded-xl px-3 py-2 text-[13px]"  
  >  
    <option v-for="n in networks" :key="n">{{ n }}</option>  
  </select>  
</div>  

<!-- ADDRESS -->  
<div class="bg-white/5 rounded-xl p-3 text-[12px] mb-3">  
  <p class="break-all text-white/80">  
    0x9fA4b1E7cD9aA1234567890ABCDEF  
  </p>  
</div>  

<p class="text-[11px] text-yellow-400">  
  Only send USDT via {{ deposit.network }} network. Sending other assets may result in permanent loss.  
</p>

  </div>  
</div>  <!-- WITHDRAW MODAL -->  <div  
  v-if="showWithdrawModal"  
  class="fixed inset-0 z-50 bg-black/60 flex items-end"  
  @click.self="showWithdrawModal = false"  
>  
  <div class="bg-[#0b0b0b] w-full rounded-t-2xl p-4 max-h-[70vh]">  <div class="flex justify-between items-center mb-4">  
  <p class="font-semibold text-[15px]">Withdraw USDT</p>  
  <X class="w-5 h-5 text-white/60" @click="showWithdrawModal = false" />  
</div>  

<div class="mb-4">  
  <p class="text-[12px] text-white/60 mb-1">Amount</p>  
  <input  
    v-model="withdraw.amount"  
    type="number"  
    placeholder="0.00"  
    class="w-full bg-white/5 rounded-xl px-3 py-2"  
  />  
</div>  

<div class="text-[12px] text-white/60 space-y-1 mb-4">  
  <p>Fee: <span class="text-white">{{ withdrawFee }} USDT</span></p>  
  <p>Minimum: <span class="text-white">{{ withdrawMin }} USDT</span></p>  
  <p>  
    You will receive:  
    <span class="text-green-400">{{ netReceive.toFixed(2) }} USDT</span>  
  </p>  
</div>  

<button

class="wallet-action primary w-full"
:disabled="Number(withdraw.amount) < withdrawMin"
@click="showWithdrawConfirm = true"

> 

Continue
</button>

  </div>  
</div>  <!-- ASSET FILTERS -->  
  <div class="flex items-center gap-2 mb-3">  
    <div class="flex items-center gap-2 bg-white/5 px-3 py-2 rounded-lg flex-1">  
      <Search class="w-4 h-4 text-white/40" />  
      <input  
        v-model="search"  
        placeholder="Search asset"  
        class="bg-transparent outline-none text-[13px] w-full"  
      />  
    </div>  

    <button  
      @click="hideSmall = !hideSmall"  
      class="text-[12px] px-3 py-2 rounded-lg"  
      :class="hideSmall ? 'bg-green-500/20 text-green-400' : 'bg-white/5 text-white/60'"  
    >  
      Hide small  
    </button>  
  </div>  

  <!-- ASSETS -->  
  <div>  
    <div class="flex justify-between text-[12px] text-white/50 mb-2">  
      <span>Asset</span>  
      <span>Total</span>  
    </div>  

    <div class="space-y-2">  
      <div  
        v-for="asset in filteredAssets"  
        :key="asset.symbol"  
        @click="selectedAsset = asset"  
        class="flex justify-between items-center py-3 px-2 rounded-lg hover:bg-white/5"  
      >  
        <div class="flex gap-3 items-center">  
          <img :src="asset.logo" class="w-8 h-8 rounded-full" />  
          <div>  
            <p class="font-medium">{{ asset.symbol }}</p>  
            <p class="text-[12px] text-white/40">{{ asset.name }}</p>  
          </div>  
        </div>  

        <div class="text-right">  
          <p class="font-medium">{{ asset.balance }}</p>  
          <p class="text-[12px] text-white/40">  
            ${{ (asset.balance * asset.price).toFixed(2) }}  
          </p>  
        </div>  
      </div>  
    </div>  
  </div>  

  <!-- ASSET ACTION SHEET -->  
  <div  
    v-if="selectedAsset"  
    class="fixed inset-0 bg-black/70 flex items-end z-50"  
    @click.self="selectedAsset = null"  
  >  
    <div class="bg-[#0b0b0b] w-full rounded-t-xl p-4">  
      <div class="flex justify-between items-center mb-4">  
        <div class="flex gap-3 items-center">  
          <img :src="selectedAsset?.logo" class="w-8 h-8 rounded-full" />  
          <div>  
            <p class="font-semibold">{{ selectedAsset?.symbol }}</p>  
            <p class="text-[12px] text-white/40">{{ selectedAsset?.name }}</p>  
          </div>  
        </div>  
        <X class="w-5 h-5 text-white/60" @click="selectedAsset = null" />  
      </div>  

      <div class="flex gap-3">  
        <button class="wallet-action primary">  
          <Plus class="w-4 h-4" /> Deposit  
        </button>  
        <button class="wallet-action">  
          <ArrowUpRight class="w-4 h-4" /> Withdraw  
        </button>  
      </div>  
    </div>  
  </div>  
     
     
   <!-- WITHDRAW CONFIRMATION MODAL -->

<!-- CONFIRMATION SHEET -->  <div  
  v-if="showWithdrawConfirm"  
  class="fixed inset-0 z-50 bg-black/70 flex items-end"  
  @click.self="showWithdrawConfirm = false"  
>  
  <div class="bg-[#0b0b0b] w-full rounded-t-3xl p-5">  <div class="flex justify-between items-center mb-4">  
  <p class="font-semibold text-[15px]">Confirm Withdrawal</p>  
  <X class="w-5 h-5 text-white/60" @click="showWithdrawConfirm = false" />  
</div>  

<!-- Summary -->  
<div class="bg-white/5 rounded-2xl p-4 space-y-3 text-[13px]">  

  <div class="flex justify-between">  
    <span class="text-white/60">Amount</span>  
    <span class="font-semibold">{{ withdraw.amount }} USDT</span>  
  </div>  

  <div class="flex justify-between">  
    <span class="text-white/60">Fee</span>  
    <span>{{ withdrawFee }} USDT</span>  
  </div>  

  <div class="flex justify-between">  
    <span class="text-white/60">Net Receive</span>  
    <span class="text-green-400 font-semibold">  
      {{ netReceive.toFixed(2) }} USDT  
    </span>  
  </div>  

  <div class="flex justify-between">  
    <span class="text-white/60">Estimated Arrival</span>  
    <span>5–15 minutes</span>  
  </div>  
</div>  

<p class="text-[12px] text-yellow-400/80 mt-4">  
  ⚠️ Withdrawals cannot be reversed once submitted.  
</p>  

<!-- Continue -->  
<button
  class="wallet-action primary w-full mt-5"
  @click="() => { 
    withdraw.wallet_id = selectedAsset?.wallet_id || ''; 
    showWithdrawConfirm = false; 
    showPinModal = true; 
  }"
>
  Confirm & Continue
</button>

  </div>  
</div>  <!-- PIN MODAL -->  <div  
  v-if="showPinModal"  
  class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center px-6"  
  @click.self="showPinModal = false"  
>  
  <div class="bg-[#111] w-full max-w-sm rounded-2xl p-6">  <h2 class="text-[16px] font-semibold mb-2">  
  Enter Transaction PIN  
</h2>  

<p class="text-[13px] text-white/60 mb-5">  
  For security, confirm this withdrawal using your 4-digit PIN.  
</p>  

<!-- PIN Input -->  
<input  
  v-model="pin"  
  maxlength="4"  
  type="password"  
  placeholder="••••"  
  class="w-full text-center text-[20px] tracking-[8px]  
         bg-white/5 rounded-xl py-3 mb-4"  
/>  

<!-- Actions -->  
<button  
  class="wallet-action primary w-full"  
  :disabled="pin.length !== 4"  
  @click="verifyPinAndWithdraw"  
>  
  Verify & Submit  
</button>  

<button  
  class="w-full mt-3 text-[13px] text-white/50"  
  @click="showPinModal = false"  
>  
  Cancel  
</button>

  </div>  
</div>  <!-- TRANSACTION RESULT (FULL PAGE) -->

<div  
  v-if="txResult.status !== 'idle'"  
  class="fixed inset-0 z-[999] bg-black flex flex-col"  
>  
  <!-- Top Right Close -->  
  <div class="flex justify-end p-4">  
    <button  
      class="text-[13px] text-white/60"  
      @click="closeResult"  
    >  
      Close  
    </button>  
  </div>    <!-- Center Content -->    <div class="flex-1 flex flex-col items-center justify-center px-6 text-center">  <!-- ICON -->  
<div  
  class="w-20 h-20 rounded-full flex items-center justify-center mb-4"  
  :class="txResult.status === 'success'  
    ? 'bg-green-500/20'  
    : txResult.status === 'error'  
    ? 'bg-red-500/20'  
    : 'bg-white/10'"  
>  
  <Check  
    v-if="txResult.status === 'success'"  
    class="w-10 h-10 text-green-400"  
  />  
  <X  
    v-else-if="txResult.status === 'error'"  
    class="w-10 h-10 text-red-400"  
  />  
  <div v-else class="loader" />  
</div>  

<!-- TEXT -->  
<h2 class="text-[18px] font-semibold mb-2">  
  {{ txResult.title }}  
</h2>  

<p class="text-[13px] text-white/60 mb-8">  
  {{ txResult.message }}  
</p>  

<!-- ACTIONS (SUCCESS ONLY) -->  
<div  
  v-if="txResult.status === 'success'"  
  class="grid grid-cols-3 gap-4 w-full"  
>   
<!-- TX HASH -->

<div  
  v-if="txResult.status === 'success'"  
  class="bg-white/5 rounded-xl px-4 py-3 mb-6 w-full text-left"  
>  
  <p class="text-[12px] text-white/60 mb-1">Transaction Hash</p>    <div class="flex justify-between items-center">  
    <span class="text-[13px] font-mono text-green-400">  
      {{ txHash }}  
    </span>  <a  
  :href="`https://tronscan.org/#/transaction/${txHash}`"  
  target="_blank"  
  class="text-[12px] text-blue-400"  
>  
  View →  
</a>

  </div>  
</div>  
      <button class="result-action">  
        <ArrowUpRight class="w-5 h-5" />  
        <span>View</span>  
      </button>  <button class="result-action">  
    <ArrowLeftRight class="w-5 h-5" />  
    <span>Share</span>  
  </button>  

  <button class="result-action">  
    <Plus class="w-5 h-5" />  
    <span>PDF</span>  
  </button>  
</div>  

<!-- ERROR ACTION -->  
<button  
  v-if="txResult.status === 'error'"  
  class="wallet-action primary w-full mt-6"  
  @click="closeResult"  
>  
  Try Again  
</button>

  </div>  
</div> 
</div>
<FooterLink />
  </section>  
</template> 
<style scoped>  
.wallet-action {  
  flex: 1;  
  height: 40px;  
  display: flex;  
  align-items: center;  
  justify-content: center;  
  gap: 6px;  
  border-radius: 10px;  
  background: rgba(255,255,255,0.06);  
  font-size: 13px;  
  font-weight: 500;  
}  
  
.wallet-action.primary {  
  background: linear-gradient(135deg, #22c55e, #15803d);  
  box-shadow: 0 0 12px rgba(34,197,94,.45);  
}   
  
.result-action {  
  display: flex;  
  flex-direction: column;  
  align-items: center;  
  gap: 6px;  
  background: rgba(255,255,255,0.06);  
  padding: 14px 0;  
  border-radius: 14px;  
  font-size: 12px;  
  color: white;  
}  
</style>