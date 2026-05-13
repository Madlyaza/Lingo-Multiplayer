<template>
  <div class="room-card" @click="$emit('join', room)">
    <div class="room-card-header">
      <span class="room-name">{{ room.name }}</span>
      <span class="badge waiting">Waiting</span>
    </div>
    <div class="room-card-meta">
      <span>Host: <strong>{{ room.host?.name }}</strong></span>
    </div>
  </div>
</template>

<script setup>
defineProps({ room: { type: Object, required: true } })
defineEmits(['join'])
</script>

<style scoped>
.room-card {
  background: #0f172a;
  border: 1px solid #334155;
  border-radius: 8px;
  padding: 1rem 1.25rem;
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s;
}

.room-card:hover {
  border-color: #38bdf8;
  background: #0c2233;
}

.room-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
}

.room-name {
  font-weight: 600;
  font-size: 1rem;
  color: #f1f5f9;
}

.badge {
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.badge.waiting {
  background: #14532d;
  color: #4ade80;
}

.room-card-meta {
  margin-top: 0.5rem;
  font-size: 0.82rem;
  color: #64748b;
}

.room-card-meta strong {
  color: #94a3b8;
}
</style>
