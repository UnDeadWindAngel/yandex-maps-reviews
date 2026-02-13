<template>
  <div>
    <h1>Добро пожаловать, {{ authStore.user?.name }}</h1>
    <nav>
      <router-link to="/settings">Настройки</router-link> |
      <router-link to="/reviews">Отзывы</router-link> |
      <button @click="logout">Выйти</button>
    </nav>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()
router.replace('/reviews')
</script>

