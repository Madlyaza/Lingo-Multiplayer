<template>
  <div :class="['guess-row', { 'mine': isMine }]">
    <span class="guesser">{{ guess.user_name }}</span>
    <div class="tiles">
      <LetterTile
        v-for="(letter, i) in letters"
        :key="i"
        :letter="letter"
        :status="guess.feedback[i]"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import LetterTile from './LetterTile.vue'

const props = defineProps({
  guess:  { type: Object, required: true },
  myId:   { type: Number, required: true },
})

const letters = computed(() => props.guess.word.split(''))
const isMine  = computed(() => props.guess.user_id === props.myId)
</script>

<style scoped>
.guess-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.guesser {
  width: 72px;
  font-size: 0.78rem;
  color: #64748b;
  text-align: right;
  flex-shrink: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.guess-row.mine .guesser { color: #38bdf8; }

.tiles {
  display: flex;
  gap: 5px;
}
</style>
