<template>
  <div class="relative inline-block">
    <!-- Пустые звёзды (серые) -->
    <div class="flex" :class="sizeClass">
      <span v-for="i in 5" :key="i" class="text-gray-300">☆</span>
    </div>
    <!-- Заполненные звёзды (жёлтые), обрезанные по ширине рейтинга -->
    <div
        class="absolute top-0 left-0 overflow-hidden whitespace-nowrap"
        :style="{ width: fillWidth + '%' }"
    >
      <div class="flex" :class="sizeClass">
        <span v-for="i in 5" :key="i" class="text-yellow-400">★</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  rating: {
    type: Number,
    default: 0
  },
  max: {
    type: Number,
    default: 5
  },
  size: {
    type: String,
    default: 'md', // 'sm', 'md', 'lg'
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  }
})

// Процент заполнения от максимального рейтинга
const fillWidth = computed(() => (props.rating / props.max) * 100)

// Класс для размера звёзд
const sizeClass = computed(() => {
  const map = {
    sm: 'text-sm',
    md: 'text-2xl',
    lg: 'text-4xl'
  }
  return map[props.size] || map.md
})
</script>