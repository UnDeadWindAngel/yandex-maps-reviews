<template>
  <div class="min-h-screen bg-white flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-50 border-r border-gray-200 p-6">
      <div class="mb-8">
        <img :src="logo" alt="Daily Grow" class="h-8 w-auto" />
        <p class="text-sm text-gray-600 mt-1">{{ userName }}</p>
      </div>

      <nav class="space-y-2">
        <div class="flex items-center gap-2 px-2 py-1 text-sm font-medium text-gray-600">
          <span class="text-lg">🔧</span>
          <span>Отзывы</span>
        </div>

        <!-- Подпункты (белые кнопки) -->
        <div class="ml-6 space-y-1">
          <router-link
              to="/reviews"
              class="block px-4 py-2 bg-white rounded-lg text-gray-700 hover:bg-gray-50 border border-gray-200 shadow-sm transition"
              active-class="bg-blue-50 border-blue-200 text-blue-700"
          >
            📋 Отзывы
          </router-link>
          <router-link
              to="/settings"
              class="block px-4 py-2 bg-white rounded-lg text-gray-700 hover:bg-gray-50 border border-gray-200 shadow-sm transition"
              active-class="bg-blue-50 border-blue-200 text-blue-700"
          >
            ⚙️ Настройка
          </router-link>
        </div>
      </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-8 bg-white flex flex-col">
      <div class="p-4 flex justify-end border-b border-gray-200">
        <button
            @click="logout"
            class="w-5 h-10 rounded-full text-gray-700 hover:flex items-center justify-center transition-colors"
            title="Выйти"
        >
          <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
          >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
            />
          </svg>
        </button>
      </div>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import logo from '@/assets/logo.png'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const userName = computed(() => authStore.user?.name || 'Название аккаунта')
async function logout() {
  await authStore.logout()
  await router.push('/login')
}
</script>