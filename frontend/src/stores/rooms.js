import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api'

export const useRoomsStore = defineStore('rooms', () => {
  const publicRooms = ref([])
  const currentRoom = ref(null)
  const loading     = ref(false)
  const error       = ref(null)

  async function fetchPublicRooms() {
    loading.value = true
    error.value   = null
    try {
      const { data } = await api.get('/rooms')
      publicRooms.value = data
    } catch (err) {
      error.value = err.response?.data?.message ?? 'Failed to load rooms.'
    } finally {
      loading.value = false
    }
  }

  async function createRoom(name, isPublic) {
    const { data } = await api.post('/rooms', { name, is_public: isPublic })
    if (isPublic) {
      publicRooms.value.unshift(data)
    }
    currentRoom.value = data
    return data
  }

  async function joinByCode(joinCode) {
    const { data } = await api.post('/rooms/join', { join_code: joinCode })
    currentRoom.value = data
    return data
  }

  async function fetchRoom(id) {
    const { data } = await api.get(`/rooms/${id}`)
    currentRoom.value = data
    return data
  }

  return { publicRooms, currentRoom, loading, error, fetchPublicRooms, createRoom, joinByCode, fetchRoom }
})
