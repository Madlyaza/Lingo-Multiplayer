<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <h1>Lingo Multiplayer</h1>
      <div class="user-info">
        <router-link to="/dashboard" class="btn-back">&larr; Lobby</router-link>
      </div>
    </header>

    <main class="room-main">
      <div v-if="loading" class="status-msg">Loading room...</div>
      <div v-else-if="error" class="status-msg error">{{ error }}</div>

      <template v-else-if="room">
        <div class="room-info">
          <h2>{{ room.name }}</h2>
          <span :class="['badge', room.is_public ? 'public' : 'private']">
            {{ room.is_public ? 'Public' : 'Private' }}
          </span>
        </div>

        <!-- Join code for private rooms (host or guest can see it) -->
        <div v-if="!room.is_public && room.join_code && isParticipant" class="join-code-box">
          <span class="label">Join Code</span>
          <div class="code-row">
            <code>{{ room.join_code }}</code>
            <button @click="copyCode" class="btn-copy">{{ copied ? 'Copied!' : 'Copy' }}</button>
          </div>
          <p class="hint">Share this code with friends so they can join.</p>
        </div>

        <!-- Players -->
        <div class="players-box">
          <div class="player-slot">
            <span class="role">Host</span>
            <span class="pname">{{ room.host?.name ?? '—' }}</span>
          </div>
          <div class="vs">VS</div>
          <div class="player-slot">
            <span class="role">Guest</span>
            <span class="pname" :class="{ empty: !room.guest }">
              {{ room.guest?.name ?? 'Waiting...' }}
            </span>
          </div>
        </div>

        <!-- Join button: shown when user is neither host nor guest -->
        <button
          v-if="!isParticipant && room.status === 'waiting'"
          class="btn-primary"
          :disabled="joining"
          @click="joinRoom"
        >
          {{ joining ? 'Joining...' : 'Join Room' }}
        </button>

        <!-- Start game: only for host once guest has joined -->
        <button
          v-if="isHost && room.guest && room.status === 'waiting'"
          class="btn-primary btn-start"
          :disabled="starting"
          @click="handleStartGame"
        >
          {{ starting ? 'Starting...' : 'Start Game' }}
        </button>

        <div v-if="isParticipant && !room.guest" class="waiting-area">
          <p>Waiting for an opponent to join...</p>
        </div>

        <div v-if="room.status === 'in_progress' && isParticipant" class="waiting-area">
          <p>Game in progress — redirecting...</p>
        </div>

        <p v-if="joinError" class="error">{{ joinError }}</p>
      </template>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useRoomsStore } from '@/stores/rooms'
import { useGameStore } from '@/stores/game'
import api from '@/api'

const route     = useRoute()
const router    = useRouter()
const auth      = useAuthStore()
const rooms     = useRoomsStore()
const gameStore = useGameStore()

const room     = ref(null)
const loading  = ref(true)
const error    = ref('')
const copied   = ref(false)
const joining  = ref(false)
const starting = ref(false)
const joinError = ref('')
let pollInterval = null

const isHost        = computed(() => auth.user?.id === room.value?.host_id)
const isGuest       = computed(() => auth.user?.id === room.value?.guest_id)
const isParticipant = computed(() => isHost.value || isGuest.value)

onMounted(async () => {
  try {
    room.value = await rooms.fetchRoom(route.params.id)

    if (room.value.current_game_id) {
      router.replace(`/game/${room.value.current_game_id}`)
      return
    }
  } catch (err) {
    if (err.response?.status === 404) {
      router.push('/dashboard')
      return
    }
    error.value = 'Failed to load room.'
  } finally {
    loading.value = false
  }

  pollInterval = setInterval(pollRoom, 2500)
})

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval)
})

async function pollRoom() {
  try {
    const updated = await rooms.fetchRoom(route.params.id)
    // Merge in-place so Vue doesn't re-render the whole template
    if (room.value) {
      Object.assign(room.value, updated)
    } else {
      room.value = updated
    }

    if (updated.current_game_id) {
      clearInterval(pollInterval)
      router.push(`/game/${updated.current_game_id}`)
    }
  } catch {}
}

async function joinRoom() {
  joining.value  = true
  joinError.value = ''
  try {
    const { data } = await api.post(`/rooms/${room.value.id}/join`)
    room.value = data
  } catch (err) {
    joinError.value = err.response?.data?.message ?? 'Could not join room.'
  } finally {
    joining.value = false
  }
}

async function handleStartGame() {
  starting.value = true
  try {
    const gameData = await gameStore.startGame(room.value.id)
    router.push(`/game/${gameData.id}`)
  } catch (err) {
    joinError.value = err.response?.data?.message ?? 'Failed to start game.'
  } finally {
    starting.value = false
  }
}

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
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
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

.players-box {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 10px;
  padding: 1.25rem 2rem;
}
.player-slot {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}
.player-slot .role { font-size: 0.75rem; text-transform: uppercase; color: #64748b; }
.player-slot .pname { font-size: 1.1rem; font-weight: 600; color: #f1f5f9; }
.player-slot .pname.empty { color: #475569; font-style: italic; }
.vs { font-size: 1rem; font-weight: 800; color: #475569; }

.btn-primary {
  padding: 0.75rem 1.5rem;
  background: #38bdf8;
  border: none;
  border-radius: 8px;
  color: #0f172a;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-primary:hover:not(:disabled) { background: #7dd3fc; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-start { background: #4ade80; }
.btn-start:hover:not(:disabled) { background: #86efac; }

.waiting-area {
  padding: 1rem;
  background: #1e293b;
  border: 1px dashed #334155;
  border-radius: 8px;
  color: #64748b;
  font-size: 0.95rem;
  text-align: center;
}

.status-msg { text-align: center; padding: 3rem; color: #94a3b8; }
.error { color: #f87171; font-size: 0.9rem; }
</style>
