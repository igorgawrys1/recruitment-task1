<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="px-8 py-6 bg-white border-b border-gray-200">
          <h2 class="text-center text-2xl font-semibold text-gray-900 mb-2">Patient Portal</h2>
          <p class="text-center text-sm text-gray-600">Access your medical test results</p>
        </div>
        <div class="px-8 py-6">
          <div v-if="authStore.error" class="w-full rounded-md border-l-4 border-red-400 bg-red-50 p-3 flex items-start">
            <div class="ml-3 w-full">
              <h3 class="text-sm font-semibold text-red-800">Sign in failed</h3>
              <p class="text-sm text-red-700 mt-1 break-words">{{ authStore.error }}</p>
            </div>
        </div>
          <form class="space-y-8" @submit.prevent="handleSubmit">
            <div class="space-y-2">
              <label for="login" class="block text-sm font-medium text-gray-700">Username</label>
              <div class="relative">
                <input
                  id="login"
                  v-model="credentials.login"
                  name="login"
                  type="text"
                  required
                  class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-black"
                  placeholder="Enter username"
                />
              </div>
            </div>
            <div class="space-y-2">
              <label for="password" class="block text-sm font-medium text-gray-700">Birth Date</label>
              <div class="relative">
                <input
                  id="password"
                  v-model="credentials.password"
                  name="password"
                  type="date"
                  required
                  class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-black"
                  placeholder="Enter birth date"
                  style="color-scheme: light;"
                />
              </div>
            </div>
            <div class="pt-4">
              <button
              type="submit"
              :disabled="authStore.isLoading"
              class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <svg v-if="authStore.isLoading" class="animate-spin -ml-1 mr-3 w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <LockClosedIcon v-else class="w-5 h-5 mr-2" />
              {{ authStore.isLoading ? 'Signing in...' : 'Sign In' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { ExclamationTriangleIcon, LockClosedIcon } from '@heroicons/vue/20/solid'

const authStore = useAuthStore()

const credentials = ref({
  login: '',
  password: '',
})

const handleSubmit = async () => {
  await authStore.login(credentials.value)
}
</script>