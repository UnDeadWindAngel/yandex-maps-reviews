<template>
  <div>
    <h1>Настройки организации</h1>
    <div v-if="orgStore.organization">
      <p>Текущая ссылка: {{ orgStore.organization.yandex_url }}</p>
      <p>ID организации: {{ orgStore.organization.org_id }}</p>
    </div>
    <form @submit.prevent="save">
      <label>Ссылка на карточку Яндекс.Карт</label>
      <input v-model="yandexUrl" placeholder="https://yandex.ru/maps/org/..." required />
      <button type="submit" :disabled="orgStore.loading">Сохранить</button>
      <div v-if="orgStore.error" class="error">{{ orgStore.error }}</div>
    </form>
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