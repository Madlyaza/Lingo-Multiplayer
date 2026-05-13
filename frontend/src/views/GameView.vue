<template>
  <div class="game-page">
    <header class="dashboard-header">
      <h1>Lingo Multiplayer</h1>
      <div class="user-info">
        <router-link to="/dashboard" class="btn-back">← Lobby</router-link>
      </div>
    </header>

    <div v-if="gameStore.loading" class="status-msg">Loading game…</div>
    <div v-else-if="gameStore.error" class="status-msg error">{{ gameStore.error }}</div>

    <main v-else-if="game" class="game-main">

      <!-- Score bar -->
      <div class="score-bar">
        <div :class="['player-score', { active: game.current_turn_user_id === game.player1_id }]">
          <span class="name">{{ game.player1?.name }}</span>
          <span class="score">{{ game.player1_score }}</span>
        </div>
        <div class="round-info">Round {{ game.round }}</div>
        <div :class="['player-score', 'right', { active: game.current_turn_user_id === game.player2_id }]">
          <span class="score">{{ game.player2_score }}</span>
          <span class="name">{{ game.player2?.name }}</span>
        </div>
      </div>

      <!-- Game Over Banner -->
      <div v-if="game.status === 'finished'" class="banner" :class="didIWin ? 'win' : 'lose'">
        <template v-if="didIWin">🏆 You win! Well done!</template>
        <template v-else>😔 {{ game.winner?.name }} wins. Better luck next time!</template>
        <router-link to="/dashboard" class="banner-link">Back to Lobby</router-link>
      </div>

      <!-- Round over flash -->
      <div v-else-if="roundMessage" class="banner neutral">
        {{ roundMessage }}
      </div>

      <!-- Word hint -->
      <div v-if="game.status === 'in_progress'" class="hint-row">
        <LetterTile
          v-for="(char, i) in hintLetters"
          :key="i"
          :letter="char === '_' ? '' : char"
          :status="char === '_' ? 'empty' : 'correct'"
        />
      </div>

      <!-- Guesses board -->
      <div class="board">
        <GuessRow
          v-for="g in game.current_guesses"
          :key="g.id"
          :guess="g"
          :my-id="auth.user.id"
        />
        <p v-if="game.current_guesses?.length === 0" class="no-guesses">
          No guesses yet — {{ firstGuesserName }} goes first!
        </p>
      </div>

      <!-- Input area -->
      <div v-if="game.status === 'in_progress'" class="input-area">
        <template v-if="isMyTurn">
          <form class="guess-form" @submit.prevent="handleGuess">
            <input
              ref="inputRef"
              v-model="wordInput"
              type="text"
              maxlength="5"
              minlength="5"
              placeholder="Type a word…"
              :disabled="submitting"
              autocomplete="off"
              autocorrect="off"
              spellcheck="false"
              @input="wordInput = wordInput.replace(/[^a-zA-Z]/g, '').toUpperCase()"
            />
            <button type="submit" :disabled="submitting || wordInput.length !== 5">
              {{ submitting ? '…' : 'Guess' }}
            </button>
          </form>
          <p v-if="guessError" class="error">{{ guessError }}</p>
        </template>
        <div v-else class="waiting-turn">
          ⏳ Waiting for {{ opponentName }} to guess…
        </div>
      </div>

    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useGameStore } from '@/stores/game'
import GuessRow from '@/components/GuessRow.vue'
import LetterTile from '@/components/LetterTile.vue'

const route     = useRoute()
const router    = useRouter()
const auth      = useAuthStore()
const gameStore = useGameStore()

const wordInput    = ref('')
const submitting   = ref(false)
const guessError   = ref('')
const roundMessage = ref('')
const inputRef     = ref(null)
let   pollInterval = null

const game = computed(() => gameStore.game)

const isMyTurn = computed(() =>
  game.value?.current_turn_user_id === auth.user?.id
)

const didIWin = computed(() =>
  game.value?.winner_id === auth.user?.id
)

const opponentName = computed(() => {
  if (!game.value) return ''
  const oppId = game.value.player1_id === auth.user?.id
    ? game.value.player2_id
    : game.value.player1_id
  return oppId === game.value.player1_id
    ? game.value.player1?.name
    : game.value.player2?.name
})

const firstGuesserName = computed(() => {
  if (!game.value) return ''
  return game.value.round_first_user_id === game.value.player1_id
    ? game.value.player1?.name
    : game.value.player2?.name
})

const hintLetters = computed(() => {
  if (!game.value?.word_hint) return []

  // Start from the backend hint (first letter + underscores)
  const letters = game.value.word_hint.split('')

  // Overlay every correctly-placed letter found in this round's guesses
  for (const guess of (game.value.current_guesses ?? [])) {
    if (!guess.feedback) continue
    for (let i = 0; i < 5; i++) {
      if (guess.feedback[i] === 'correct') {
        letters[i] = guess.word[i]
      }
    }
  }

  return letters
})

onMounted(async () => {
  await gameStore.fetchGame(route.params.id)

  if (!game.value) {
    router.push('/dashboard')
    return
  }

  startPolling()
})

onUnmounted(() => stopPolling())

function startPolling() {
  pollInterval = setInterval(async () => {
    if (game.value?.status === 'finished') {
      stopPolling()
      return
    }
    // Only poll when waiting for opponent — silently, no loading flash
    if (!isMyTurn.value) {
      try {
        await gameStore.silentRefresh(game.value.id)
      } catch (err) {
        if (err.response?.status === 404) {
          stopPolling()
          sessionStorage.setItem('kicked', 'Your game was closed due to inactivity.')
          router.push('/dashboard')
        }
      }
    }
  }, 2000)
}

function stopPolling() {
  if (pollInterval) {
    clearInterval(pollInterval)
    pollInterval = null
  }
}

async function handleGuess() {
  if (wordInput.value.length !== 5) return
  guessError.value = ''
  submitting.value = true

  try {
    const result = await gameStore.submitGuess(wordInput.value)
    wordInput.value = ''

    if (result.round_over && result.revealed_word) {
      if (result.is_correct) {
        roundMessage.value = `✅ Correct! The word was ${result.revealed_word}`
      } else {
        roundMessage.value = `⏱ Time's up! The word was ${result.revealed_word}`
      }
      setTimeout(() => {
        roundMessage.value = ''
      }, 3000)
    }

    if (!result.game_over) {
      await nextTick()
      inputRef.value?.focus()
    }
  } catch (err) {
    guessError.value = err.response?.data?.message ?? 'Failed to submit guess.'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.game-page { min-height: 100vh; }

.game-main {
  max-width: 560px;
  margin: 0 auto;
  padding: 1.5rem 1rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.score-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 10px;
  padding: 0.75rem 1.25rem;
}

.player-score {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  opacity: 0.5;
  transition: opacity 0.2s;
}

.player-score.active { opacity: 1; }
.player-score.right  { flex-direction: row-reverse; }

.player-score .name  { font-size: 0.9rem; color: #94a3b8; }
.player-score .score {
  font-size: 1.6rem;
  font-weight: 800;
  color: #38bdf8;
  min-width: 28px;
  text-align: center;
}

.round-info {
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #475569;
}

.banner {
  border-radius: 8px;
  padding: 1rem 1.25rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.banner.win     { background: #14532d; color: #4ade80; border: 1px solid #166534; }
.banner.lose    { background: #3b0764; color: #e879f9; border: 1px solid #7e22ce; }
.banner.neutral { background: #1e293b; color: #94a3b8; border: 1px solid #334155; }

.banner-link {
  color: inherit;
  font-size: 0.85rem;
  text-decoration: underline;
  white-space: nowrap;
}

.hint-row {
  display: flex;
  gap: 5px;
  justify-content: center;
}

.board {
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-height: 80px;
}

.no-guesses { color: #475569; font-size: 0.9rem; text-align: center; padding: 1rem; }

.input-area { display: flex; flex-direction: column; gap: 0.5rem; }

.guess-form {
  display: flex;
  gap: 0.5rem;
}

.guess-form input {
  flex: 1;
  background: #0f172a;
  border: 2px solid #334155;
  border-radius: 6px;
  color: #f1f5f9;
  padding: 0.65rem 0.75rem;
  font-size: 1.2rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  outline: none;
  transition: border-color 0.2s;
  text-align: center;
}

.guess-form input:focus { border-color: #38bdf8; }

.guess-form button {
  padding: 0.65rem 1.25rem;
  background: #38bdf8;
  border: none;
  border-radius: 6px;
  color: #0f172a;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}

.guess-form button:hover:not(:disabled) { background: #7dd3fc; }
.guess-form button:disabled { opacity: 0.5; cursor: not-allowed; }

.waiting-turn {
  text-align: center;
  color: #64748b;
  font-size: 0.95rem;
  padding: 0.75rem;
  background: #1e293b;
  border-radius: 6px;
  border: 1px dashed #334155;
}

.btn-back { color: #94a3b8; text-decoration: none; font-size: 0.9rem; }
.btn-back:hover { color: #f1f5f9; }
.status-msg { text-align: center; padding: 3rem; color: #94a3b8; }
</style>
