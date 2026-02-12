import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import { useAuthStore } from './stores/auth'

axios.defaults.withCredentials = true
axios.defaults.baseURL = '/'

async function bootstrap() {
    const app = createApp(App)
    const pinia = createPinia()
    app.use(pinia)

    // 1. Сначала загружаем пользователя
    const authStore = useAuthStore()
    await authStore.fetchUser() // ЖДЁМ завершения

    // 2. Только после этого подключаем роутер
    app.use(router)

    // 3. Монтируем приложение
    app.mount('#app')
}

bootstrap()