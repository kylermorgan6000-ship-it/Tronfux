import { ref, watch } from 'vue'

const favorites = ref<string[]>(
  JSON.parse(localStorage.getItem('favorites') || '[]')
)

watch(favorites, () => {
  localStorage.setItem('favorites', JSON.stringify(favorites.value))
}, { deep: true })

export function useFavorites() {
  const toggle = (symbol: string) => {
    favorites.value.includes(symbol)
      ? favorites.value = favorites.value.filter(s => s !== symbol)
      : favorites.value.push(symbol)
  }

  return { favorites, toggle }
}