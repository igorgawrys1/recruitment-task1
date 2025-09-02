import axios from 'axios'
import type { AxiosInstance, AxiosError, InternalAxiosRequestConfig } from 'axios'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

class ApiService {
  private api: AxiosInstance

  constructor() {
    this.api = axios.create({
      baseURL: API_BASE_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    })

    this.setupInterceptors()
  }

  private setupInterceptors(): void {
    this.api.interceptors.request.use(
      (config: InternalAxiosRequestConfig) => {
        const authStore = useAuthStore()
        const token = authStore.token
        
        if (token && config.headers) {
          config.headers.Authorization = `Bearer ${token}`
        }
        
        return config
      },
      (error: AxiosError) => {
        return Promise.reject(error)
      }
    )

    this.api.interceptors.response.use(
      (response) => response,
      async (error: AxiosError) => {
        if (error.response?.status === 401) {
          const authStore = useAuthStore()
          authStore.logout()
          await router.push('/login')
        }
        return Promise.reject(error)
      }
    )
  }

  async login(credentials: { login: string; password: string }) {
    const response = await this.api.post('/login', credentials)
    return response.data
  }

  async getResults() {
    const response = await this.api.get('/results')
    return response.data
  }

  async logout() {
    const response = await this.api.post('/logout')
    return response.data
  }

  async refreshToken() {
    const response = await this.api.post('/refresh')
    return response.data
  }
}

export default new ApiService()