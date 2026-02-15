const queue: (() => Promise<void>)[] = []
let active = false

export function rateLimit<T>(fn: () => Promise<T>): Promise<T> {
  return new Promise(resolve => {
    queue.push(async () => resolve(await fn()))
    if (!active) process()
  })
}

async function process() {
  active = true
  while (queue.length) {
    await queue.shift()!()
    await new Promise(r => setTimeout(r, 350)) // ~3 req/sec
  }
  active = false
}