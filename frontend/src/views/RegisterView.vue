<template>
  <div class="register">
    <h1>Регистрация</h1>
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="name">Имя</label>
        <input
            id="name"
            v-model="form.name"
            type="text"
            required
            autocomplete="name"
        />
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input
            id="email"
            v-model="form.email"
            type="email"
            required
            autocomplete="email"
        />
      </div>

      <div class="form-group">
        <label for="password">Пароль</label>
        <input
            id="password"
            v-model="form.password"
            type="password"
            required
            autocomplete="new-password"
        />
      </div>

      <div class="form-group">
        <label for="password_confirmation">Подтверждение пароля</label>
        <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            required
            autocomplete="new-password"
        />
      </div>

      <button type="submit" :disabled="authStore.loading">
        {{ authStore.loading ? 'Регистрация...' : 'Зарегистрироваться' }}
      </button>

      <div v-if="authStore.error" class="error">
        {{ authStore.error }}
      </div>
    </form>

    <p>
      Уже есть аккаунт?
      <router-link to="/login">Войти</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

async function handleSubmit() {
  try {
    await authStore.register(form.value)
    router.push('/')
  } catch (e) {
    // Ошибка уже записана в authStore.error
  }
}
</script>

<style scoped>
.register {
  max-width: 400px;
  margin: 0 auto;
  padding: 20px;
}
.form-group {
  margin-bottom: 15px;
}
label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}
input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}
button {
  width: 100%;
  padding: 10px;
  background-color: #42b883;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
button:disabled {
  background-color: #cccccc;
}
.error {
  color: red;
  margin-top: 10px;
}
</style>