<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal">
      <h2>Create Room</h2>

      <form @submit.prevent="submit">
        <div class="field">
          <label for="room-name">Room Name</label>
          <input
            id="room-name"
            v-model="form.name"
            type="text"
            placeholder="e.g. Friday Night Lingo"
            maxlength="60"
            required
          />
          <span v-if="errors.name" class="error">{{ errors.name[0] }}</span>
        </div>

        <div class="field">
          <label>Visibility</label>
          <div class="toggle-group">
            <button
              type="button"
              :class="['toggle-btn', { active: form.isPublic }]"
              @click="form.isPublic = true"
            >
              🌐 Public
            </button>
            <button
              type="button"
              :class="['toggle-btn', { active: !form.isPublic }]"
              @click="form.isPublic = false"
            >
              🔒 Private
            </button>
          </div>
          <p class="hint">
            {{ form.isPublic
              ? 'Anyone can see and join this room from the lobby.'
              : 'Only players with the join code can enter.' }}
          </p>
        </div>

        <p v-if="generalError" class="error general">{{ generalError }}</p>

        <div class="modal-actions">
          <button type="button" class="btn-secondary" @click="$emit('close')">Cancel</button>
          <button type="submit" :disabled="loading">
            {{ loading ? 'Creating…' : 'Create Room' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useRoomsStore } from '@/stores/rooms'

const emit = defineEmits(['close'])

const rooms  = useRoomsStore()
const router = useRouter()

const form = reactive({ name: '', isPublic: true })
const errors       = ref({})
const generalError = ref('')
const loading      = ref(false)

async function submit() {
  errors.value       = {}
  generalError.value = ''
  loading.value      = true

  try {
    const room = await rooms.createRoom(form.name, form.isPublic)
    emit('close')
    router.push({ name: 'room', params: { id: room.id } })
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors ?? {}
    } else {
      generalError.value = err.response?.data?.message ?? 'Something went wrong.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
  padding: 1rem;
}

.modal {
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 12px;
  padding: 2rem;
  width: 100%;
  max-width: 440px;
}

.modal h2 {
  font-size: 1.4rem;
  font-weight: 700;
  color: #38bdf8;
  margin-bottom: 1.5rem;
}

.hint {
  font-size: 0.8rem;
  color: #64748b;
  margin-top: 0.4rem;
}

.toggle-group {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.35rem;
}

.toggle-btn {
  flex: 1;
  padding: 0.55rem;
  border-radius: 6px;
  border: 1px solid #334155;
  background: #0f172a;
  color: #94a3b8;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
}

.toggle-btn.active {
  border-color: #38bdf8;
  color: #38bdf8;
  background: #0c2233;
}

.modal-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

.btn-secondary {
  flex: 1;
  padding: 0.7rem;
  background: transparent;
  border: 1px solid #334155;
  border-radius: 6px;
  color: #94a3b8;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-secondary:hover { background: #1e293b; }

button[type="submit"] {
  flex: 1;
  margin-top: 0;
}
</style>
