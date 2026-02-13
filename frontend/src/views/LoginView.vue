<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Вход в систему
        </h2>
        <div v-if="showRegisteredMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          Регистрация прошла успешно! Теперь вы можете войти.
        </div>
      </div>
      <div class="bg-white py-8 px-4 shadow-xl rounded-xl sm:px-10">
        <form class="space-y-6" @submit.prevent="handleSubmit">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <div class="mt-1">
              <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  autocomplete="email"
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
            <div class="mt-1">
              <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  required
                  autocomplete="current-password"
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <button
                type="submit"
                :disabled="authStore.loading"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-blue-300"
            >
              {{ authStore.loading ? 'Вход...' : 'Войти' }}
            </button>
          </div>

          <div v-if="authStore.error" class="text-sm text-red-600 text-center">
            {{ authStore.error }}
          </div>
        </form>

        <div class="mt-6 text-center">
          <router-link to="/register" class="text-sm text-blue-600 hover:text-blue-500">
            Нет аккаунта? Зарегистрироваться
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const route = useRoute()
const authStore = useAuthStore()
const router = useRouter()
const form = ref({ email: '', password: '' })

const showRegisteredMessage = computed(() => route.query.registered === 'true')
async function handleSubmit() {
  try {
    await authStore.login(form.value)
    router.push('/')
  } catch (e) {}
}
</script>