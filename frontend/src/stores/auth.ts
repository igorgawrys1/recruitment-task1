import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiService from '@/services/api'
import router from '@/router'

interface User {
  id: number
  name: string
  surname: string
  sex: string
  birthDate: string
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('token'))
  const user = ref<User | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const isAuthenticated = computed(() => !!token.value)
  const fullName = computed(() => 
    user.value ? `${user.value.name} ${user.value.surname}` : ''
  )

  async function login(credentials: { login: string; password: string }) {
    isLoading.value = true
    error.value = null

    try {
      const response = await apiService.login(credentials)
      
      token.value = response.access_token
      localStorage.setItem('token', response.access_token)
      
      await router.push('/dashboard')
      return true
    } catch (err: any) {
      if (err.response?.data?.errors) {
        const validationErrors = Object.values(err.response.data.errors).flat()
        error.value = validationErrors.join('. ')
      } else if (err.response?.data?.error) {
        error.value = err.response.data.error
      } else if (err.response?.data?.message) {
        error.value = err.response.data.message
      } else if (err.message) {
        error.value = err.message
      } else {
        error.value = 'Login failed. Please check your credentials.'
      }
      return false
    } finally {
      isLoading.value = false
    }
  }

  async function logout() {
    try {
      await apiService.logout()
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      await router.push('/login')
    }
  }

  function setUser(userData: User) {
    user.value = userData
  }

  async function refreshToken() {
    try {
      const response = await apiService.refreshToken()
      token.value = response.access_token
      localStorage.setItem('token', response.access_token)
      return true
    } catch (error) {
      console.error('Token refresh error:', error)
      logout()
      return false
    }
  }

  return {
    token,
    user,
    isLoading,
    error,
    isAuthenticated,
    fullName,
    login,
    logout,
    setUser,
    refreshToken,
  }
})