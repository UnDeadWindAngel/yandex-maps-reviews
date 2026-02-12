import { defineStore } from 'pinia'
import axios from 'axios'
import { ref } from 'vue'

export const useOrganizationStore = defineStore('organization', () => {
    const organization = ref(null)
    const loading = ref(false)
    const error = ref(null)

    async function fetchOrganization() {
        try {
            const response = await axios.get('/api/organization')
            organization.value = response.data
        } catch (err) {
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

    return { organization, loading, error, fetchOrganization, saveOrganization }
})