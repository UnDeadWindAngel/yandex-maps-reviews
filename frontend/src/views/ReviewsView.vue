<template>
  <div>
    <h1>Отзывы</h1>
    <div v-if="summary">
      <p>Рейтинг: {{ summary.rating }} ({{ summary.total_count }} отзывов)</p>
    </div>
    <div v-if="loading">Загрузка...</div>
    <div v-else>
      <div v-for="review in reviews" :key="review.id" class="review">
        <p><strong>{{ review.author_name }}</strong> ({{ review.rating }})</p>
        <p>{{ review.text }}</p>
        <p>{{ review.date }}</p>
        <img v-if="review.photo" :src="review.photo" width="100" />
      </div>
      <div class="pagination">
        <button @click="prevPage" :disabled="currentPage === 1">Назад</button>
        <span>Стр. {{ currentPage }} из {{ lastPage }}</span>
        <button @click="nextPage" :disabled="currentPage === lastPage">Вперед</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

const reviews = ref([])
const summary = ref(null)
const loading = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)

async function fetchReviews() {
  loading.value = true
  try {
    const response = await axios.get('/api/reviews', { params: { page: currentPage.value } })
    reviews.value = response.data.reviews
    summary.value = { rating: response.data.rating, total_count: response.data.total_count }
    lastPage.value = response.data.last_page
  } finally {
    loading.value = false
  }
}

async function fetchSummary() {
  const response = await axios.get('/api/reviews/summary')
  summary.value = response.data
}

function nextPage() {
  if (currentPage.value < lastPage.value) {
    currentPage.value++
  }
}

function prevPage() {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

onMounted(() => {
  fetchSummary()
  fetchReviews()
})

watch(currentPage, fetchReviews)
</script>