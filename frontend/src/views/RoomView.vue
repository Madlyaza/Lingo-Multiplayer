<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <h1>Lingo Multiplayer</h1>
      <div class="user-info">
        <router-link to="/dashboard" class="btn-back">← Lobby</router-link>
      </div>
    </header>

    <main class="room-main">
      <div v-if="loading" class="status-msg">Loading room…</div>
      <div v-else-if="error" class="status-msg error">{{ error }}</div>

      <template v-else-if="room">
        <div class="room-info">
          <h2>{{ room.name }}</h2>
          <span :class="['badge', room.is_public ? 'public' : 'private']">
            {{ room.is_public ? '🌐 Public' : '🔒 Private' }}
          </span>
        </div>

        <!-- Join code for private rooms -->
        <div v-if="!room.is_public && room.join_code" class="join-code-box">
          <span class="label">Join Code</span>
          <div class="code-row">
            <code>{{ room.join_code }}</code>
            <button @click="copyCode" class="btn-copy">{{ copied ? 'Copied!' : 'Copy' }}</button>
          </div>
          <p class="hint">Share this code with friends so they can join.</p>
        </div>

        <div class="waiting-area">
          <p>Waiting for the host to start the game…</p>
        </div>
      </template>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRoomsStore } from '@/stores/rooms'

const route  = useRoute()
const router = useRouter()
const rooms  = useRoomsStore()

const room    = ref(null)
const loading = ref(true)
const error   = ref('')
const copied  = ref(false)

onMounted(async () => {
  try {
    room.value = await rooms.fetchRoom(route.params.id)
  } catch (err) {
    if (err.response?.status === 404) {
      router.push('/dashboard')
    } else {
      error.value = 'Failed to load room.'
    }
  } finally {
    loading.value = false
  }
})

async function copyCode() {
  await navigator.clipboard.writeText(room.value.join_code)
  copied.value = true
  setTimeout(() => (copied.value = false), 2000)
}
</script>

<style scoped>
.room-main {
  padding: 2rem;
  max-width: 700px;
  margin: 0 auto;
}

.btn-back {
  color: #94a3b8;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.2s;
}

.btn-back:hover { color: #f1f5f9; }

.room-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.room-info h2 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #f1f5f9;
}

.badge {
  font-size: 0.78rem;
  font-weight: 700;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
}

.badge.public  { background: #1e3a5f; color: #38bdf8; }
.badge.private { background: #3b1f47; color: #c084fc; }

.join-code-box {
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 8px;
  padding: 1.25rem 1.5rem;
  margin-bottom: 1.5rem;
}

.join-code-box .label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #64748b;
}

.code-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin: 0.5rem 0 0.35rem;
}

.code-row code {
  font-size: 1.3rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: #c084fc;
  background: #0f172a;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
}

.btn-copy {
  padding: 0.4rem 0.9rem;
  background: #334155;
  border: none;
  border-radius: 6px;
  color: #f1f5f9;
  font-size: 0.85rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-copy:hover { background: #475569; }

.hint { font-size: 0.8rem; color: #64748b; }

.waiting-area {
  background: #1e293b;
  border: 1px dashed #334155;
  border-radius: 8px;
  padding: 2rem;
  text-align: center;
  color: #64748b;
}

.status-msg { color: #94a3b8; padding: 1rem 0; }
</style>
