<template>
  <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
    <!-- Верхняя строка: дата + иконка + филиал слева, звёзды справа -->
    <div class="flex items-center justify-between mb-2">
      <div class="flex items-center text-sm text-gray-500">
        <span>{{ formattedDate }}</span>
        <!-- Иконка филиала (SVG) -->
        <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="ml-2 mr-1">
          <path d="M12 1a9.002 9.002 0 0 0-6.366 15.362c1.63 1.63 5.466 3.988 5.693 6.465.034.37.303.673.673.673.37 0 .64-.303.673-.673.227-2.477 4.06-4.831 5.689-6.46A9.002 9.002 0 0 0 12 1z" fill="#F43"></path>
          <path d="M12 13.079a3.079 3.079 0 1 1 0-6.158 3.079 3.079 0 0 1 0 6.158z" fill="#fff"></path>
        </svg>
        <span class="font-medium text-gray-700">{{ review.branch || 'Филиал 1' }}</span>
      </div>
      <!-- Звёзды рейтинга отзыва -->
      <RatingStars :rating="review.rating" size="sm" />
    </div>

    <!-- Имя и телефон -->
    <div class="text-sm text-gray-600 mb-3">
      <span class="font-medium text-gray-800">{{ review.author_name }}</span>
      <span class="mx-2">·</span>
      <span>{{ review.phone || '+7 900 540 40 40' }}</span>
    </div>

    <!-- Текст отзыва -->
    <p class="text-gray-800 leading-relaxed">
      {{ review.text || 'Так, с чего начать... Разнообразная алкогольная продукция, множество закусок и обычных блюд. Кухня вкусная и разнообразная, от супа и салатов до мясных продуктов. Персонал молодые девушки, общительная и доброжелательна, всегда подскажут, вовремя принесут и вызовут такси. Отдыхали на летней веранде, свежо и тепло, в общем самое то в жаркую погоду. Сами залы не сильно рассмотрели, но видел что они удобные и просторные.' }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import RatingStars from './RatingStars.vue'

const props = defineProps({
  review: {
    type: Object,
    required: true
  }
})

const formattedDate = computed(() => {
  if (!props.review.date) return ''
  const date = new Date(props.review.date)
  if (isNaN(date.getTime())) return props.review.date
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  const hasTime = props.review.date.includes(' ') || props.review.date.includes('T')
  if (hasTime) {
    const hours = String(date.getHours()).padStart(2, '0')
    const minutes = String(date.getMinutes()).padStart(2, '0')
    return `${day}.${month}.${year} ${hours}:${minutes}`
  }
  return `${day}.${month}.${year}`
})
</script>