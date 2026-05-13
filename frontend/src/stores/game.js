import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api'

export const useGameStore = defineStore('game', () => {
  const game    = ref(null)
  const loading = ref(false)
  const error   = ref(null)

  async function fetchGame(id) {
    loading.value = true
    error.value   = null
    try {
      const { data } = await api.get(`/games/${id}`)
      game.value = data
    } catch (err) {
      error.value = err.response?.data?.message ?? 'Failed to load game.'
    } finally {
      loading.value = false
    }
  }

  // Silent background refresh — merges in-place, never shows loading spinner.
  // Throws on 404 so callers can detect room/game deletion.
  async function silentRefresh(id) {
    try {
      const { data } = await api.get(`/games/${id}`)
      if (game.value) {
        Object.assign(game.value, data)
      } else {
        game.value = data
      }
    } catch (err) {
      if (err.response?.status === 404) throw err
      // ignore other transient network errors
    }
  }

  async function startGame(roomId) {
    const { data } = await api.post(`/rooms/${roomId}/game`)
    game.value = data
    return data
  }

  async function submitGuess(word) {
    const { data } = await api.post(`/games/${game.value.id}/guess`, { word })
    game.value = data.game
    return data // includes is_correct, round_over, game_over, revealed_word
  }

  function clear() {
    game.value  = null
    error.value = null
  }

  return { game, loading, error, fetchGame, silentRefresh, startGame, submitGuess, clear }
})
