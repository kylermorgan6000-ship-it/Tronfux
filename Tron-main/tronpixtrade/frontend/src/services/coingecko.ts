import { rateLimit } from './rateLimit'

export async function getSpotMarkets(category: string) {
  return rateLimit(async () => {
    const res = await fetch(
      'https://api.coingecko.com/api/v3/coins/markets?' +
      new URLSearchParams({
        vs_currency: 'usd',
        order:
          category === 'gainers'
            ? 'price_change_percentage_24h_desc'
            : category === 'losers'
            ? 'price_change_percentage_24h_asc'
            : 'market_cap_desc',
        per_page: '30',
        page: '1',
      })
    )

    const data = await res.json()

    return data.map((c: any) => ({
      id: c.id,
      symbol: `${c.symbol.toUpperCase()}USDT`,
      base: c.symbol.toUpperCase(),
      quote: 'USDT',
      name: c.name,
      logo: c.image,
      price: c.current_price,
      change24h: c.price_change_percentage_24h,
      volume: c.total_volume,
      marketType: 'spot',
    }))
  })
}