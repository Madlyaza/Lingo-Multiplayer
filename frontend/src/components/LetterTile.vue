<template>
  <div :class="['tile', status]">{{ letter }}</div>
</template>

<script setup>
defineProps({
  letter: { type: String, default: '' },
  status: { type: String, default: 'empty' }, // correct | present | absent | empty
})
</script>

<style scoped>
.tile {
  width: 52px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  font-weight: 800;
  border-radius: 4px;
  text-transform: uppercase;
  border: 2px solid #334155;
  color: #f1f5f9;
  transition: background 0.2s;
  user-select: none;
}

.tile.correct { background: #166534; border-color: #166534; }
.tile.present { background: #854d0e; border-color: #854d0e; }
.tile.absent  { background: #1e293b; border-color: #1e293b; }
.tile.empty   { background: transparent; border-color: #334155; color: transparent; }
</style>
