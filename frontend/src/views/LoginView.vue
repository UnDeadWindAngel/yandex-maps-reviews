<template>
  <div class="login">
    <h1>Вход</h1>
    <form @submit.prevent="handleSubmit">
      <div>
        <label>Email</label>
        <input v-model="form.email" type="email" required />
      </div>
      <div>
        <label>Пароль</label>
        <input v-model="form.password" type="password" required />
      </div>
      <button type="submit" :disabled="authStore.loading">
        {{ authStore.loading ? 'Вход...' : 'Войти' }}
      </button>
      <div v-if="authStore.error" class="error">{{ authStore.error }}</div>
    </form>
    <router-link to="/register">Регистрация</router-link>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()
const form = ref({ email: '', password: '' })

async function handleSubmit() {
  try {
    await authStore.login(form.value)
    router.push('/')
  } catch (e) {}
}
</script>