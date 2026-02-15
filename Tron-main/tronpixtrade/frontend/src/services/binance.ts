import { rateLimit } from './rateLimit'

const SPOT_API = 'https://api.binance.com/api/v3'
const FUTURES_API = 'https://fapi.binance.com/fapi/v1'

export async function getBinanceSpotMarkets() {
  return rateLimit(async () => {
    const res = await fetch(`${SPOT_API}/ticker/24hr`)
    const data = await res.json()

    return data
      .filter((c: any) => c.symbol.endsWith('USDT'))
      .slice(0, 30)
      .map((c: any) => ({
        id: c.symbol,
        symbol: c.symbol,
        base: c.symbol.replace('USDT', ''),
        quote: 'USDT',
        name: c.symbol.replace('USDT', ''),
        price: Number(c.lastPrice),
        change24h: Number(c.priceChangePercent),
        volume: Number(c.quoteVolume),
        marketType: 'spot',
      }))
  })
}

export async function getBinanceFuturesMarkets() {
  return rateLimit(async () => {
    const res = await fetch(`${FUTURES_API}/ticker/24hr`)
    const data = await res.json()

    return data
      .filter((c: any) => c.symbol.endsWith('USDT'))
      .slice(0, 30)
      .map((c: any) => ({
        id: c.symbol,
        symbol: c.symbol,
        base: c.symbol.replace('USDT', ''),
        quote: 'USDT',
        name: c.symbol.replace('USDT', ''),
        price: Number(c.lastPrice),
        change24h: Number(c.priceChangePercent),
        volume: Number(c.quoteVolume),
        marketType: 'futures',
      }))
  })
}

export async function getOrderBook(symbol: string, futures = false) {
  const base = futures ? FUTURES_API : SPOT_API
  const res = await fetch(`${base}/depth?symbol=${symbol}&limit=20`)
  return res.json()
}