import { ref } from 'vue'

interface Notification {
  id: string
  title: string
  message: string
  type: 'success' | 'error' | 'warning' | 'info'
  timestamp: Date
  read: boolean
}

export function useNotifications() {
  const notifications = ref<Notification[]>([])
  const isConnected = ref(false)

  // Add notification
  const addNotification = (notification: Omit<Notification, 'id' | 'timestamp' | 'read'>) => {
    const id = `notif-${Date.now()}-${Math.random()}`
    const newNotif: Notification = {
      id,
      ...notification,
      timestamp: new Date(),
      read: false,
    }
    notifications.value.unshift(newNotif)

    // Auto-remove after 5 seconds
    setTimeout(() => {
      removeNotification(id)
    }, 5000)
  }

  // Remove notification
  const removeNotification = (id: string) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index >= 0) {
      notifications.value.splice(index, 1)
    }
  }

  // Mark as read
  const markAsRead = (id: string) => {
    const notif = notifications.value.find(n => n.id === id)
    if (notif) {
      notif.read = true
    }
  }

  // Mock realtime setup (for now, without Pusher)
  const subscribe = () => {
    isConnected.value = true
    console.log('Notifications enabled')
    // In production, integrate with Pusher or Socket.io here
  }

  const unsubscribe = () => {
    isConnected.value = false
    console.log('Notifications disabled')
  }

  return {
    notifications,
    isConnected,
    addNotification,
    removeNotification,
    markAsRead,
    subscribe,
    unsubscribe,
  }
}
