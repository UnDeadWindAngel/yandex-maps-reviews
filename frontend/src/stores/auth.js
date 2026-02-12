import { defineStore } from 'pinia'
import axios from 'axios'
import { ref } from 'vue'
import { useOrganizationStore } from './organization'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const loading = ref(false)
    const error = ref(null)

    // Вход
    async function login(credentials) {
        loading.value = true
        error.value = null
        try {
            await axios.get('/sanctum/csrf-cookie')
            const response = await axios.post('/api/login', credentials)
            user.value = response.data.user
            return response.data
        } catch (err) {
            error.value = err.response?.data?.message || 'Ошибка входа'
            throw err
        } finally {
            loading.value = false
        }
    }

    // Регистрация
    async function register(data) {
        loading.value = true
        error.value = null
        try {
            await axios.get('/sanctum/csrf-cookie')
            const response = await axios.post('/api/register', data)
            user.value = response.data.user
            return response.data
        } catch (err) {
            error.value = err.response?.data?.message || 'Ошибка регистрации'
            throw err
        } finally {
            loading.value = false
        }
    }

    // Вспомогательная функция для удаления кук
    function clearCookies() {
        const cookies = ['laravel_session', 'XSRF-TOKEN'];
        cookies.forEach(name => {
            document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=localhost`;
            document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/`;
        });
    }

    // Выход
    async function logout() {
        loading.value = true
        try {
            await axios.get('/sanctum/csrf-cookie')
            await axios.post('/api/logout')
            user.value = null
            clearCookies()
            const orgStore = useOrganizationStore()
            orgStore.$reset()
        } catch (err) {
            error.value = 'Ошибка выхода'
        } finally {
            loading.value = false
        }
    }

    // Получение текущего пользователя (вызывается при загрузке приложения)
    async function fetchUser() {
        try {
            const response = await axios.get('/api/user')
            user.value = response.data.user
            console.log(response.data.user)
        } catch (err) {
            // 401 — пользователь не аутентифицирован, это нормально
            if (err.response?.status === 401) {
                user.value = null
                clearCookies()
            } else {
                console.error('Ошибка при получении пользователя:', err)
            }
        }
    }

    return { user, loading, error, login, register, logout, fetchUser }
})