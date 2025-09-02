<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow-sm w-full">
      <div class="w-full px-6">
        <div class="flex items-center justify-between h-16">
          <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Patient Portal</h1>
          <div class="flex items-center gap-6">
            <div class="flex items-center gap-3">
              <span class="text-sm font-medium text-gray-700">
                {{ authStore.fullName }}
              </span>
            </div>
            <button
              @click="handleLogout"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 active:bg-red-700 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="w-full p-6">
      <div class="w-full">
        <div v-if="loading" class="flex flex-col items-center justify-center h-64">
          <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
          <p class="mt-4 text-sm text-gray-600">Loading patient data...</p>
        </div>

        <div v-else-if="error" class="w-full">
          <div class="w-full rounded-md border-l-4 border-red-400 bg-red-50 p-3">
            <h3 class="text-sm font-semibold text-red-800">Error</h3>
            <p class="text-sm text-red-700 mt-1 break-words">{{ error }}</p>
          </div>
        </div>

        <div v-else class="w-full space-y-6 md:space-y-8 lg:space-y-10">
          <div class="w-full bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Patient Information</h2>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <div class="space-y-1">
                  <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Full Name</dt>
                  <dd class="text-base font-semibold text-black">{{ patient?.name }} {{ patient?.surname }}</dd>
                </div>
                <div class="space-y-1">
                  <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Gender</dt>
                  <dd class="text-base font-semibold text-black capitalize">{{ patient?.sex }}</dd>
                </div>
                <div class="space-y-1">
                  <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Date of Birth</dt>
                  <dd class="text-base font-semibold text-black">{{ patient?.birthDate }}</dd>
                </div>
                <div class="space-y-1">
                  <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Patient ID</dt>
                  <dd class="text-base font-semibold text-black">#{{ patient?.id }}</dd>
                </div>
              </div>
            </div>
          </div>
          <div class="w-full bg-white rounded-lg shadow-sm border" style="margin-top: 5px;">
            <div class="px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Test Results</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ orders?.length }} Orders
                </span>
              </div>
            </div>
            <div class="divide-y divide-gray-200">
              <div v-for="order in orders" :key="order.orderId" class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <div>
                    <h3 class="text-base font-semibold text-gray-900">Order #{{ order.orderId }}</h3>
                    <p class="text-sm text-gray-500">{{ order.results?.length }} test results</p>
                  </div>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Completed
                  </span>
                </div>
                <div class="overflow-x-auto">
                  <table class="min-w-full w-full">
                    <thead>
                      <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference Range</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                      <tr v-for="(result, index) in order.results" :key="index" class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                          <div class="text-sm font-medium text-black">{{ result.name }}</div>
                        </td>
                        <td class="px-4 py-3">
                          <div class="text-sm font-semibold text-black">{{ result.value }}</div>
                        </td>
                        <td class="px-4 py-3">
                          <div class="text-sm text-black">
                          <div v-if="result.reference" class="space-y-1">
                            <div v-for="line in splitReference(result.reference)" :key="line" class="leading-tight">
                              {{ line }}
                            </div>
                          </div>
                          <div v-else>Not specified</div>
                        </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import apiService from '@/services/api'

interface Patient {
  id: number
  name: string
  surname: string
  sex: string
  birthDate: string
}

interface TestResult {
  name: string
  value: string
  reference: string
}

interface Order {
  orderId: string
  results: TestResult[]
}

const authStore = useAuthStore()
const loading = ref(true)
const error = ref<string | null>(null)
const patient = ref<Patient | null>(null)
const orders = ref<Order[]>([])

const fetchResults = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await apiService.getResults()
    patient.value = response.patient
    orders.value = response.orders
    authStore.setUser(response.patient)
  } catch (err: any) {
    error.value = err.response?.data?.error || 'Failed to load results'
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  await authStore.logout()
}

const getInitials = (fullName: string): string => {
  if (!fullName) return 'U'
  const names = fullName.trim().split(' ')
  return names.map(name => name.charAt(0).toUpperCase()).join('').substring(0, 2)
}

const splitReference = (reference: string): string[] => {
  if (!reference) return []
  return reference.split('|').map(line => line.trim()).filter(line => line.length > 0)
}

onMounted(() => {
  fetchResults()
})
</script>
