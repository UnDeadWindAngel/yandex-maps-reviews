<template>
  <div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Подключение площадок</h1>

    <!-- Форма подключения -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
      <h2 class="text-lg font-semibold mb-4">Подключить Яндекс</h2>

      <div v-if="orgStore.organization" class="mb-4 text-sm text-gray-600">
        <span class="font-medium">Текущая ссылка:</span>
        <a :href="orgStore.organization.yandex_url" target="_blank" class="text-blue-600 ml-2 break-all">
          {{ orgStore.organization.yandex_url }}
        </a>
      </div>

      <form @submit.prevent="save">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Укажите ссылку на Яндекс
        </label>
        <input
            v-model="yandexUrl"
            type="url"
            placeholder="https://yandex.ru/maps/org/..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
        />
        <p class="text-xs text-gray-500 mt-1">
          Пример: https://yandex.ru/maps/org/abc123/
        </p>

        <div v-if="orgStore.error" class="mt-3 text-sm text-red-600">
          {{ orgStore.error }}
        </div>

        <button
            type="submit"
            :disabled="orgStore.loading"
            class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-blue-300 transition-colors"
        >
          {{ orgStore.loading ? 'Сохранение...' : 'Сохранить' }}
        </button>
      </form>
    </div>

    <!-- Подсказка (можно убрать, если не нужна) -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 text-sm text-yellow-800">
      <p class="font-medium">Как получить ссылку?</p>
      <ol class="list-decimal list-inside mt-1 space-y-1">
        <li>Откройте Яндекс Карты и найдите вашу организацию</li>
        <li>Скопируйте URL из адресной строки браузера</li>
        <li>Вставьте его в поле выше</li>
      </ol>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useOrganizationStore } from '@/stores/organization'
import { useAuthStore } from '@/stores/auth'

const orgStore = useOrganizationStore()
const authStore = useAuthStore()
const yandexUrl = ref('')

// Загружаем организацию при смене пользователя (сработает и при монтировании)
watch(() => authStore.user, async (newUser) => {
  if (newUser) {
    await orgStore.fetchOrganization()
    yandexUrl.value = orgStore.organization?.yandex_url || ''
  } else {
    orgStore.$reset()
    yandexUrl.value = ''
  }
}, { immediate: true })

async function save() {
  await orgStore.saveOrganization(yandexUrl.value)
}
</script>