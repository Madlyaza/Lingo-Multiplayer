<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <h1>Lingo Multiplayer</h1>
      <div class="user-info">
        <span>{{ auth.user?.name }}</span>
        <button @click="handleLogout" :disabled="loggingOut">
          {{ loggingOut ? 'Logging out…' : 'Log Out' }}
        </button>
      </div>
    </header>

    <main class="dashboard-main">
      <!-- Actions bar -->
      <div class="actions-bar">
        <button class="btn-create" @click="showCreateModal = true">+ Create Room</button>

        <form class="join-form" @submit.prevent="handleJoinByCode">
          <input
            v-model="joinCode"
            type="text"
            placeholder="Enter join code…"
            :disabled="joining"
          />
          <button type="submit" :disabled="joining || !joinCode.trim()">
            {{ joining ? 'Joining…' : 'Join' }}
          </button>
          <span v-if="joinError" class="error">{{ joinError }}</span>
        </form>
      </div>

      <!-- Public rooms -->
      <section class="rooms-section">
        <div class="section-header">
          <h2>Public Rooms</h2>
          <button class="btn-refresh" @click="rooms.fetchPublicRooms()" :disabled="rooms.loading">
            {{ rooms.loading ? 'Loading…' : '↻ Refresh' }}
          </button>
        </div>

        <p v-if="rooms.error" class="error">{{ rooms.error }}</p>

        <p v-else-if="!rooms.loading && rooms.publicRooms.length === 0" class="empty">
          No public rooms available. Be the first to create one!
        </p>

        <div v-else class="rooms-grid">
          <RoomCard
            v-for="room in rooms.publicRooms"
            :key="room.id"
            :room="room"
            @join="handleJoinPublic"
          />
        </div>
      </section>
    </main>

    <!-- Create room modal -->
    <CreateRoomModal v-if="showCreateModal" @close="showCreateModal = false" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useRoomsStore } from '@/stores/rooms'
import CreateRoomModal from '@/components/CreateRoomModal.vue'
import RoomCard from '@/components/RoomCard.vue'

const auth   = useAuthStore()
const rooms  = useRoomsStore()
const router = useRouter()

const showCreateModal = ref(false)
const loggingOut      = ref(false)
const joinCode        = ref('')
const joinError       = ref('')
const joining         = ref(false)

onMounted(() => rooms.fetchPublicRooms())

async function handleLogout() {
  loggingOut.value = true
  await auth.logout()
  router.push('/login')
}

async function handleJoinByCode() {
  joinError.value = ''
  joining.value   = true
  try {
    const room = await rooms.joinByCode(joinCode.value.trim())
    router.push({ name: 'room', params: { id: room.id } })
  } catch (err) {
    joinError.value = err.response?.data?.message ?? 'Invalid or expired join code.'
  } finally {
    joining.value = false
  }
}

function handleJoinPublic(room) {
  router.push({ name: 'room', params: { id: room.id } })
}
</script>

<style scoped>
.dashboard-main {
  padding: 2rem;
  max-width: 900px;
  margin: 0 auto;
}

.actions-bar {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.btn-create {
  padding: 0.6rem 1.25rem;
  background: #38bdf8;
  color: #0f172a;
  font-weight: 700;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.95rem;
  transition: background 0.2s;
  white-space: nowrap;
}

.btn-create:hover { background: #7dd3fc; }

.join-form {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.join-form input {
  background: #0f172a;
  border: 1px solid #334155;
  border-radius: 6px;
  color: #f1f5f9;
  padding: 0.55rem 0.75rem;
  font-size: 0.95rem;
  outline: none;
  width: 200px;
  transition: border-color 0.2s;
}

.join-form input:focus { border-color: #38bdf8; }

.join-form button {
  padding: 0.55rem 1rem;
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 6px;
  color: #f1f5f9;
  font-size: 0.95rem;
  cursor: pointer;
  transition: background 0.2s;
}

.join-form button:hover:not(:disabled) { background: #334155; }
.join-form button:disabled { opacity: 0.5; cursor: not-allowed; }

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.section-header h2 {
  font-size: 1.15rem;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.btn-refresh {
  background: transparent;
  border: 1px solid #334155;
  border-radius: 6px;
  color: #94a3b8;
  padding: 0.35rem 0.75rem;
  font-size: 0.82rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-refresh:hover:not(:disabled) { background: #1e293b; }

.rooms-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1rem;
}

.empty {
  color: #475569;
  font-size: 0.95rem;
  padding: 1.5rem 0;
}
</style>

