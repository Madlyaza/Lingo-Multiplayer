<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <h1>Lingo Multiplayer</h1>
      <div class="user-info">
        <router-link to="/dashboard" class="btn-back">&larr; Lobby</router-link>
      </div>
    </header>

    <main class="lb-main">
      <h2 class="lb-title">Leaderboard</h2>
      <p class="lb-sub">Top 10 players by wins</p>

      <div v-if="loading" class="status-msg">Loading...</div>
      <div v-else-if="error" class="status-msg error">{{ error }}</div>

      <table v-else class="lb-table">
        <thead>
          <tr>
            <th class="col-rank">#</th>
            <th class="col-name">Player</th>
            <th class="col-stat">Wins</th>
            <th class="col-stat">Losses</th>
            <th class="col-stat">Games</th>
            <th class="col-stat">Win %</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(entry, index) in entries"
            :key="entry.id"
            :class="{ 'is-me': entry.id === auth.user?.id, 'top-three': index < 3 }"
          >
            <td class="col-rank">
              <span v-if="index === 0" class="medal">1st</span>
              <span v-else-if="index === 1" class="medal silver">2nd</span>
              <span v-else-if="index === 2" class="medal bronze">3rd</span>
              <span v-else>{{ index + 1 }}</span>
            </td>
            <td class="col-name">
              {{ entry.name }}
              <span v-if="entry.id === auth.user?.id" class="you-tag">you</span>
            </td>
            <td class="col-stat win">{{ entry.wins }}</td>
            <td class="col-stat loss">{{ entry.losses }}</td>
            <td class="col-stat">{{ entry.total_games }}</td>
            <td class="col-stat">{{ winPct(entry) }}%</td>
          </tr>
          <tr v-if="entries.length === 0">
            <td colspan="6" class="empty">No games played yet. Be the first!</td>
          </tr>
        </tbody>
      </table>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api'

const auth    = useAuthStore()
const entries = ref([])
const loading = ref(true)
const error   = ref('')

onMounted(async () => {
  try {
    const { data } = await api.get('/leaderboard')
    entries.value = data
  } catch {
    error.value = 'Failed to load leaderboard.'
  } finally {
    loading.value = false
  }
})

function winPct(entry) {
  if (!entry.total_games) return 0
  return Math.round((entry.wins / entry.total_games) * 100)
}
</script>

<style scoped>
.lb-main {
  max-width: 700px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.lb-title {
  font-size: 1.75rem;
  font-weight: 800;
  color: #f1f5f9;
  margin-bottom: 0.25rem;
}

.lb-sub {
  font-size: 0.9rem;
  color: #64748b;
  margin-bottom: 1.75rem;
}

.lb-table {
  width: 100%;
  border-collapse: collapse;
  background: #1e293b;
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #334155;
}

.lb-table thead tr {
  background: #0f172a;
}

.lb-table th {
  padding: 0.75rem 1rem;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #64748b;
  text-align: left;
}

.lb-table td {
  padding: 0.7rem 1rem;
  font-size: 0.95rem;
  color: #cbd5e1;
  border-top: 1px solid #334155;
}

.lb-table tbody tr:hover {
  background: #263548;
}

.lb-table tbody tr.is-me {
  background: #1e3a5f;
}

.col-rank { width: 56px; }
.col-name { font-weight: 600; color: #f1f5f9; }
.col-stat { width: 80px; text-align: center; }

.medal {
  display: inline-block;
  padding: 0.15rem 0.5rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  background: #854d0e;
  color: #fef08a;
}
.medal.silver { background: #334155; color: #cbd5e1; }
.medal.bronze { background: #431407; color: #fca5a5; }

.you-tag {
  margin-left: 0.4rem;
  font-size: 0.7rem;
  font-weight: 700;
  background: #1e3a5f;
  color: #38bdf8;
  padding: 0.1rem 0.4rem;
  border-radius: 999px;
  vertical-align: middle;
}

.col-stat.win  { color: #4ade80; font-weight: 600; }
.col-stat.loss { color: #f87171; }

.empty {
  text-align: center;
  color: #475569;
  padding: 2rem;
}

.btn-back { color: #94a3b8; text-decoration: none; font-size: 0.9rem; }
.btn-back:hover { color: #f1f5f9; }
.status-msg { text-align: center; padding: 3rem; color: #94a3b8; }
.error { color: #f87171; }
</style>
