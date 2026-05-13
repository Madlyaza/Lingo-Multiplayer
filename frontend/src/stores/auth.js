import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api'

export const useAuthStore = defineStore('auth', () => {
  const user  = ref(JSON.parse(localStorage.getItem('auth_user')) ?? null)
  const token = ref(localStorage.getItem('auth_token') ?? null)

  const isAuthenticated = computed(() => !!token.value)

  function _persist(userData, tokenValue) {
    user.value  = userData
    token.value = tokenValue
    localStorage.setItem('auth_user',  JSON.stringify(userData))
    localStorage.setItem('auth_token', tokenValue)
  }

  function _clear() {
    user.value  = null
    token.value = null
    localStorage.removeItem('auth_user')
    localStorage.removeItem('auth_token')
  }

  async function register(name, email, password, passwordConfirmation) {
    const { data } = await api.post('/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    })
    _persist(data.user, data.token)
  }

  async function login(email, password) {
    const { data } = await api.post('/login', { email, password })
    _persist(data.user, data.token)
  }

  async function logout() {
    try {
      await api.post('/logout')
    } finally {
      _clear()
    }
  }

  return { user, token, isAuthenticated, register, login, logout }
})
