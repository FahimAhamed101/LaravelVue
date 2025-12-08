<template>
    <div class="min-h-screen bg-gray-100">
      <!-- Navigation -->
      <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex items-center">
              <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
            </div>
            <div class="flex items-center space-x-4">
              <span class="text-gray-700">
                Welcome, {{ user.name }}
              </span>
              <button
                @click="handleLogout"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      </nav>
  
      <!-- Main Content -->
      <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <h2 class="text-2xl font-bold mb-4">Your Information</h2>
              <div class="space-y-2">
                <p><strong>ID:</strong> {{ user.id }}</p>
                <p><strong>Name:</strong> {{ user.name }}</p>
                <p><strong>Email:</strong> {{ user.email }}</p>
              </div>
              
              <div class="mt-6">
                <button
                  @click="handleLogout"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </template>
  
  <script>
  import { useAuthStore } from '@/stores/auth';
  import { useRouter } from 'vue-router';
  import { computed } from 'vue';
  
  export default {
    name: 'DashboardPage',
    
    setup() {
      const authStore = useAuthStore();
      const router = useRouter();
      
      const user = computed(() => authStore.user || {});
      
      const handleLogout = async () => {
        await authStore.logout();
        router.push('/login');
      };
      
      return {
        user,
        handleLogout
      };
    }
  };
  </script>