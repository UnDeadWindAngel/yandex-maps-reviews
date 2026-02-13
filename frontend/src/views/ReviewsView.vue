<template>
  <div>
    <!-- Кнопка Яндекс Карты (отдельно) -->
    <div class="mb-4 mt-2">
      <a
          v-if="organization?.yandex_url"
          :href="organization.yandex_url"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-sm text-gray-700 rounded-md border border-gray-200 transition-colors"
      >
        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 1a9.002 9.002 0 0 0-6.366 15.362c1.63 1.63 5.466 3.988 5.693 6.465.034.37.303.673.673.673.37 0 .64-.303.673-.673.227-2.477 4.06-4.831 5.689-6.46A9.002 9.002 0 0 0 12 1z" fill="#F43"></path>
          <path d="M12 13.079a3.079 3.079 0 1 1 0-6.158 3.079 3.079 0 0 1 0 6.158z" fill="#fff"></path>
        </svg>
        Яндекс Карты
      </a>
    </div>

    <!-- Сообщение, если организация не настроена -->
    <div v-if="!organization" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
      <p class="text-yellow-800">
        ⚠️ Организация не настроена.
        <router-link to="/settings" class="underline font-medium">Перейти в настройки</router-link>
      </p>
    </div>

    <!-- Основная сетка с двумя колонками (если организация есть) -->
    <div v-else class="grid grid-cols-3 gap-6 items-start">
      <!-- Левая колонка (2/3) – список отзывов -->
      <div class="col-span-2">
        <!-- Состояние загрузки -->
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-300 border-t-blue-600"></div>
          <p class="mt-2 text-gray-500">Загрузка отзывов...</p>
        </div>

        <!-- Нет отзывов -->
        <div v-else-if="reviews.length === 0" class="text-center py-12 bg-white rounded-xl border border-gray-100">
          <p class="text-gray-500">Пока нет отзывов</p>
        </div>

        <!-- Список отзывов -->
        <div v-else class="space-y-4">
          <ReviewCard v-for="review in reviews" :key="review.id" :review="review" />

          <!-- Пагинация -->
          <div class="flex items-center justify-between mt-6 bg-white px-4 py-3 rounded-lg border border-gray-100">
            <button
                @click="prevPage"
                :disabled="currentPage === 1"
                class="px-3 py-1 rounded border disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              ← Назад
            </button>
            <span class="text-sm text-gray-700">
              Страница {{ currentPage }} из {{ lastPage }}
            </span>
            <button
                @click="nextPage"
                :disabled="currentPage === lastPage"
                class="px-3 py-1 rounded border disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Вперёд →
            </button>
          </div>
        </div>
      </div>

      <!-- Правая колонка (1/3) – блок рейтинга -->
      <div class="col-span-1">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm sticky top-4">
          <div class="text-4xl font-bold text-gray-900">{{ summary?.rating?.toFixed(1) || '0.0' }}</div>
          <RatingStars :rating="summary?.rating" size="md" />
          <div class="text-sm text-gray-600 mt-2">
            Всего отзывов: <span class="font-bold">{{ summary?.total_count || 0 }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useOrganizationStore } from '@/stores/organization'
import RatingStars from '@/components/RatingStars.vue'
import ReviewCard from '@/components/ReviewCard.vue'

const orgStore = useOrganizationStore()
const organization = ref(null)
const reviews = ref([])
const summary = ref(null)
const loading = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)

async function fetchOrganization() {
  await orgStore.fetchOrganization()
  organization.value = orgStore.organization
}

async function fetchReviews() {
  if (!organization.value) return

  loading.value = true
  try {
    const response = await axios.get('/api/reviews',
        { params: { page: currentPage.value }
        })
    reviews.value = response.data.reviews
    summary.value = {
      rating: response.data.rating,
      total_count: response.data.total_count
    }
    lastPage.value = response.data.last_page
  } finally {
    loading.value = false
  }
}

async function fetchSummary() {
  if (!organization.value) return
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

onMounted(async() => {
  await fetchOrganization()
  if (organization.value) {
    await fetchReviews()
    await fetchSummary()
  }
})

watch(currentPage, fetchReviews)
</script>