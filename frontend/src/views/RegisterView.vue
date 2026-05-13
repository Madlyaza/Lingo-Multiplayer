<template>
  <div class="auth-page">
    <div class="auth-card">
      <h1>Create Account</h1>

      <form @submit.prevent="submit">
        <div class="field">
          <label for="name">Name</label>
          <input id="name" v-model="form.name" type="text" placeholder="Your name" required />
          <span v-if="errors.name" class="error">{{ errors.name[0] }}</span>
        </div>

        <div class="field">
          <label for="email">Email</label>
          <input id="email" v-model="form.email" type="email" placeholder="you@example.com" required />
          <span v-if="errors.email" class="error">{{ errors.email[0] }}</span>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input id="password" v-model="form.password" type="password" placeholder="Min 8 characters" required />
          <span v-if="errors.password" class="error">{{ errors.password[0] }}</span>
        </div>

        <div class="field">
          <label for="password_confirmation">Confirm Password</label>
          <input id="password_confirmation" v-model="form.passwordConfirmation" type="password" placeholder="Repeat password" required />
        </div>

        <p v-if="generalError" class="error general">{{ generalError }}</p>

        <button type="submit" :disabled="loading">
          {{ loading ? 'Creating account…' : 'Register' }}
        </button>
      </form>

      <p class="switch-link">
        Already have an account?
        <router-link to="/login">Log in</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth    = useAuthStore()
const router  = useRouter()

const form = reactive({ name: '', email: '', password: '', passwordConfirmation: '' })
const errors       = ref({})
const generalError = ref('')
const loading      = ref(false)

async function submit() {
  errors.value       = {}
  generalError.value = ''
  loading.value      = true

  try {
    await auth.register(form.name, form.email, form.password, form.passwordConfirmation)
    router.push('/dashboard')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors ?? {}
    } else {
      generalError.value = err.response?.data?.message ?? 'Something went wrong. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>
