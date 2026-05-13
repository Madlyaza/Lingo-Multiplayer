<template>
  <div class="auth-page">
    <div class="auth-card">
      <h1>Welcome Back</h1>

      <form @submit.prevent="submit">
        <div class="field">
          <label for="email">Email</label>
          <input id="email" v-model="form.email" type="email" placeholder="you@example.com" required />
          <span v-if="errors.email" class="error">{{ errors.email[0] }}</span>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input id="password" v-model="form.password" type="password" placeholder="Your password" required />
          <span v-if="errors.password" class="error">{{ errors.password[0] }}</span>
        </div>

        <p v-if="generalError" class="error general">{{ generalError }}</p>

        <button type="submit" :disabled="loading">
          {{ loading ? 'Logging in…' : 'Log In' }}
        </button>
      </form>

      <p class="switch-link">
        Don't have an account?
        <router-link to="/register">Register</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth   = useAuthStore()
const router = useRouter()

const form = reactive({ email: '', password: '' })
const errors       = ref({})
const generalError = ref('')
const loading      = ref(false)

async function submit() {
  errors.value       = {}
  generalError.value = ''
  loading.value      = true

  try {
    await auth.login(form.email, form.password)
    router.push('/dashboard')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors ?? {}
    } else {
      generalError.value = err.response?.data?.message ?? 'Invalid credentials. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>
