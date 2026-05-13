<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <h1>Lingo Multiplayer</h1>
      <div class="user-info">
        <span>{{ auth.user?.name }}</span>
        <button @click="handleLogout" :disabled="loading">
          {{ loading ? 'Logging out…' : 'Log Out' }}
        </button>
      </div>
    </header>

    <main>
      <p>Welcome, <strong>{{ auth.user?.name }}</strong>! The game lobby will be here.</p>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth    = useAuthStore()
const router  = useRouter()
const loading = ref(false)

async function handleLogout() {
  loading.value = true
  await auth.logout()
  router.push('/login')
}
</script>
