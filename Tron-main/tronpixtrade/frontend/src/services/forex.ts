import { rateLimit } from './rateLimit'

const API_KEY = 'YOUR_API_KEY'
const BASE = 'https://api.twelvedata.com'

export async function getForexMarkets() {
  return rateLimit(async () => {
    const res = await fetch(`${BASE}/forex_pairs?apikey=${API_KEY}`)
    const data = await res.json()

    return data.data.slice(0, 30).map((p: any) => ({
      id: p.symbol,
      symbol: p.symbol,
      base: p.base_currency,
      quote: p.quote_currency,
      name: `${p.base_currency}/${p.quote_currency}`,
      price: 0,
      change24h: 0,
      marketType: 'forex',
    }))
  })
}

export async function getForexQuote(symbol: string) {
  return rateLimit(async () => {
    const res = await fetch(
      `${BASE}/price?symbol=${symbol}&apikey=${API_KEY}`
    )
    const data = await res.json()
    return Number(data.price)
  })
}