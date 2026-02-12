import { defineStore } from 'pinia'
import axios from 'axios'
import { ref } from 'vue'

export const useOrganizationStore = defineStore('organization', () => {
    const organization = ref(null)
    const loading = ref(false)
    const error = ref(null)

    async function fetchOrganization() {
        try {
            await axios.get('/sanctum/csrf-cookie')
            const response = await axios.get('/api/organization')
            organization.value = response.data
        } catch (err) {
            if (err.response?.status !== 401) {
                const authStore = useAuthStore()
                await authStore.logout()
            }
            else{
                console.error('Ошибка загрузки организации:', err)
            }
            organization.value = null
        }
    }

    async function saveOrganization(yandexUrl) {
        loading.value = true
        error.value = null
        try {
            await axios.get('/sanctum/csrf-cookie')
            const response = await axios.post('/api/organization', { yandex_url: yandexUrl })
            organization.value = response.data
            return response.data
        } catch (err) {
            error.value = err.response?.data?.message || 'Ошибка сохранения'
            throw err
        } finally {
            loading.value = false
        }
    }

    function $reset() {
        organization.value = null
        loading.value = false
        error.value = null
    }

    return { organization, loading, error, fetchOrganization, saveOrganization, $reset }
})