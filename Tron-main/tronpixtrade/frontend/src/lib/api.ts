import axios from 'axios'
import Cookies from 'js-cookie'

const baseURL =
  import.meta.env.VITE_API_BASE ||
  import.meta.env.VITE_API_URL ||
  'http://localhost:8000'

const api = axios.create({
  baseURL,
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

// ---------------- CSRF ----------------
export async function csrf() {
  await api.get('/sanctum/csrf-cookie')
}

// Attach CSRF token automatically
api.interceptors.request.use((config) => {
  const token = Cookies.get('XSRF-TOKEN')
  if (token) {
    config.headers = {
      ...(config.headers || {}),
      'X-XSRF-TOKEN': token,
    }
  }
  return config
})

// ---------------- AUTH ----------------
export async function fetchUser() {
  const res = await api.get('/user')
  return res.data
}

export async function registerUser(payload: {
  name: string
  email: string
  password: string
  password_confirmation: string
  account_type: string
  country: string
}) {
  await csrf()
  const res = await api.post('/register', payload)
  return res.data
}

export async function login(email: string, password: string) {
  await csrf()
  await api.post('/login', { email, password })
  const res = await api.get('/user')
  return res.data
}

export async function logout() {
  await api.post('/logout')
}

// ---------------- SECURITY STEPS ----------------
export async function fetchSecuritySteps(type?: 'bank' | 'broker') {
  const res = await api.get('/security-steps', { params: { type } })
  return res.data
}

export async function createSecurityStep(payload: {
  name: string
  explanation: string
  code: string
  account: string
  type: 'bank' | 'broker'
}) {
  const res = await api.post('/security-steps', payload)
  return res.data
}

export async function updateSecurityStep(id: number, payload: {
  name: string
  explanation: string
  code: string
  account: string
  type: 'bank' | 'broker'
}) {
  const res = await api.put(`/security-steps/${id}`, payload)
  return res.data
}

export async function deleteSecurityStep(id: number) {
  const res = await api.delete(`/security-steps/${id}`)
  return res.data
}

// ---------------- CARDS ----------------
export async function fetchCardsList() {
  const res = await api.get('/cards')
  return res.data?.data ?? res.data
}

export async function fetchCardsAdmin() {
  const res = await api.get('/admin/cards')
  return res.data?.data ?? res.data
}

export async function createCardApplication(payload: {
  bank_account_id: number
  card_name: string
  card_type: string
  daily_limit: number
  monthly_limit: number
}) {
  const res = await api.post('/cards', payload)
  return res.data
}

export async function createCardAdmin(payload: {
  card_type: string
  card_number: string
  holder_name: string
  currency?: string
  status?: string
}) {
  const res = await api.post('/cards', payload)
  return res.data
}

export async function toggleCardStatusAdmin(id: number, status: 'Active' | 'Inactive') {
  const res = await api.patch(`/cards/${id}/status`, { status })
  return res.data
} 


// Fetch all bank users or broker users based on type
export const fetchBankUsers = async (params: { type?: string } = {}) => {
  try {
    const res = await api.get('/admin/users', { params }) // backend route: /api/admin/users
    return res.data.data ?? res.data
  } catch (err) {
    console.error('Error fetching users:', err)
    return []
  }
}

// Fetch broker trades
export const fetchBrokerTrades = async () => {
  try {
    const res = await api.get('/admin/broker-trades') // backend route: /api/admin/broker-trades
    return res.data
  } catch (err) {
    console.error('Error fetching broker trades:', err)
    return { data: [] }
  }
}

// Fetch admin wallets
export const fetchWallets = async () => {
  try {
    const res = await api.get('/admin/wallets') // backend route: /api/admin/wallets
    return res.data
  } catch (err) {
    console.error('Error fetching wallets:', err)
    return []
  }
}



// ---------------- BANK ACCOUNTS ----------------
export async function fetchBankAccountsList() {
  const res = await api.get('/bank-accounts')
  return res.data?.data ?? res.data
}

export async function fetchBankAccounts() {
  const res = await api.get('/bank-accounts')
  return res.data
}

// ---------------- WALLET & TRANSACTIONS ----------------


export async function fetchTransactions(params: { type?: string; per_page?: number } = {}) {
  const res = await api.get('/transactions', { params })
  return res.data?.data ?? res.data
}

export async function verifyWithdrawalPin(pin: string) {
  const res = await api.post('/withdrawals/verify-pin', { pin })
  return res.data
}

export async function submitWithdrawalRequest(payload: {
  wallet_id: number | string
  amount: number
  currency: string
  withdrawal_method: string
  destination: string
}) {
  const res = await api.post('/withdrawals', payload)
  return res.data
}

// ---------------- SECURITY STEPS ----------------

// Fetch security steps for the current user (bank or broker)
export async function fetchUserSecuritySteps() {
  const res = await api.get('/security-steps')
  return res.data
}

// Verify a security step code
export async function verifySecurityStepCode(stepId: number, code: string) {
  const res = await api.post(`/api/security-steps/${stepId}/verify`, { code })
  return res.data
}


// ---------------- WALLETS ----------------

// Fetch all user wallets
export async function fetchWalletsList() {
  const res = await api.get('/wallets')
  return res.data?.data || res.data || []
}

// ---------------- WITHDRAWALS ----------------

// FetchPending withdrawals
export async function fetchPendingWithdrawals() {
  try {
    const res = await api.get('/withdrawals', { params: { status: 'pending' } })
    return res.data?.data || res.data || []
  } catch {
    return []
  }
}

// -------- BANKING/USER KYC --------

// Submit user KYC (FormData for file uploads)
export async function submitKyc(formData: FormData) {
  const res = await api.post('/kyc', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
  return res.data
}

// ---------------- BROKER KYC ----------------

// Submit broker KYC
export async function submitBrokerKyc(payload: {
  document_type: string
  document_front?: string | null
  document_back?: string | null
  selfie?: string | null
  country: string
  date_of_birth: string
}) {
  const res = await api.post('/admin/broker-users/kyc/submit', payload)
  return res.data
}

// ---------------- BROKER PRODUCTS / PLANS ----------------

// Fetch broker investment plans
export async function fetchBrokerPlans() {
  const res = await api.get('/admin/broker-plans')
  return res.data.data ?? res.data
}

// Create broker plan
export async function createBrokerPlan(payload: any) {
  const res = await api.post('/admin/broker-plans', payload)
  return res.data
}

// Update broker plan
export async function updateBrokerPlan(id: number, payload: any) {
  const res = await api.put(`/admin/broker-plans/${id}`, payload)
  return res.data
}

// Delete broker plan
export async function deleteBrokerPlan(id: number) {
  const res = await api.delete(`/admin/broker-plans/${id}`)
  return res.data
}

// ---------------- BROKER TRANSACTIONS ----------------

// Fetch broker transactions with optional filters
export async function fetchBrokerTransactions(params: { type?: string; per_page?: number } = {}) {
  const res = await api.get('/admin/broker-transactions', { params })
  return res.data?.data ?? res.data
}

// ---------------- BROKER TRANSFERS ----------------

// Submit a broker transfer request
export async function submitBrokerTransfer(payload: {
  wallet_id: number | string
  amount: number
  bank_name: string
  account_name: string
  account_number: string
  routing_number: string
  bank_address: string
  transaction_pin: string
}) {
  const res = await api.post('/admin/broker-transfers', payload)
  return res.data
}

// ---------------- BROKER WITHDRAWALS ----------------

// Submit a broker withdrawal
export async function submitBrokerWithdrawal(payload: {
  wallet_id: number | string
  amount: number
  currency: string
  withdrawal_method: string
  destination: string
  pin: string
}) {
  const res = await api.post('/admin/broker-withdrawals', payload)
  return res.data
}


// ---------------- ADMIN USERS ----------------
export async function fetchAdminUsers(params: { type?: string } = { type: 'admin' }) {
  const res = await api.get('/admin/users', { params })
  return res.data
}

export async function fetchAdminUsersList(params: { type?: string; status?: string } = {}) {
  const res = await api.get('/admin/users', { params })
  return res.data.data ?? res.data
}

export async function createAdminUser(payload: {
  name: string
  email: string
  password: string
  role?: string
  status?: string
}) {
  const res = await api.post('/admin/users', payload)
  return res.data
}

export async function updateAdminUser(id: number | string, payload: {
  name?: string
  email?: string
  password?: string
  role?: string
  status?: string
}) {
  const res = await api.put(`/api/admin/users/${id}`, payload)
  return res.data
}

export async function deleteAdminUser(id: number | string) {
  const res = await api.delete(`/api/admin/users/${id}`)
  return res.data
}

// ---------------- ADMIN PROFILE ----------------
export async function updateProfileUser(payload: { name: string; email: string }) {
  const res = await api.post('/profile/update', payload)
  return res.data
}

export async function changePassword(current: string, newPassword: string, confirm: string) {
  const res = await api.post('/password/change', {
    current_password: current,
    password: newPassword,
    password_confirmation: confirm,
  })
  return res.data
}

// ---------------- USER BALANCE ----------------
export async function creditUser(email: string, amount: number, currency = 'USD') {
  const res = await api.post('/admin/users/credit', { email, amount, currency })
  return res.data
}

export async function deductUser(email: string, amount: number, currency = 'USD') {
  const res = await api.post('/admin/users/deduct', { email, amount, currency })
  return res.data
}

// ---------------- KYC (Bank Users) ----------------
export async function fetchKycUsers(type: string = 'bank') {
  const res = await api.get('/admin/users', { params: { type } })
  return res.data.data ?? res.data
}

export async function approveKyc(userId: number) {
  const res = await api.post(`/admin/users/${userId}/kyc/approve`)
  return res.data
}

export async function rejectKyc(userId: number) {
  const res = await api.post(`/admin/users/${userId}/kyc/reject`)
  return res.data
}

export async function updateUserStatus(userId: number, status: string) {
  const res = await api.post(`/admin/users/${userId}/status`, { status })
  return res.data
}

export async function deleteKyc(userId: number) {
  const res = await api.post(`/admin/users/${userId}/kyc/delete`)
  return res.data
}

export async function deleteUser(userId: number) {
  const res = await api.delete(`/admin/users/${userId}`)
  return res.data
}

// ---------------- BROKER USERS ----------------
export async function creditBrokerUser(email: string, amount: number, currency = 'USD') {
  const res = await api.post('/admin/broker-users/credit', { email, amount, currency })
  return res.data
}

export async function deductBrokerUser(email: string, amount: number, currency = 'USD') {
  const res = await api.post('/admin/broker-users/deduct', { email, amount, currency })
  return res.data
}

export async function fetchBrokerKycUsers() {
  const res = await api.get('/admin/broker-users')
  return res.data.data ?? res.data
}

export async function approveBrokerKyc(userId: number) {
  const res = await api.post(`/admin/broker-users/${userId}/kyc/approve`)
  return res.data
}

export async function rejectBrokerKyc(userId: number) {
  const res = await api.post(`/admin/broker-users/${userId}/kyc/reject`)
  return res.data
}

export async function deleteBrokerKyc(userId: number) {
  const res = await api.post(`/admin/broker-users/${userId}/kyc/delete`)
  return res.data
}

// -------- INVESTMENT PLANS --------
export async function createInvestment(payload: {
  plan_id: number
  amount: number
  currency: string
}) {
  const res = await api.post('/investments', payload)
  return res.data
}

export async function fetchUserInvestments() {
  const res = await api.get('/investments')
  return res.data?.data ?? res.data
}

// -------- PIN MANAGEMENT --------
export async function createWithdrawalPin(pin: string) {
  const res = await api.post('/withdrawal-pin', { pin })
  return res.data
}

export async function updateWithdrawalPin(oldPin: string, newPin: string) {
  const res = await api.post('/withdrawal-pin/update', { old_pin: oldPin, new_pin: newPin })
  return res.data
}

export async function hasWithdrawalPin() {
  try {
    const res = await api.get('/withdrawal-pin/status')
    return res.data?.has_pin ?? false
  } catch {
    return false
  }
}

// -------- COPIED TRADES --------
export async function fetchCopiedTrades() {
  const res = await api.get('/copied-trades')
  return res.data?.data ?? res.data
}

export async function closeCopiedTrade(tradeId: number) {
  const res = await api.post(`/copied-trades/${tradeId}/close`)
  return res.data
}

export default api