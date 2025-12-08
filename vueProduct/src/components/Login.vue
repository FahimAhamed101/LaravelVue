<template>
 
  <div class="min-h-screen bg-black flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Sign in to your account
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
        <div v-if="error" class="rounded-md bg-red-50 p-4">
          <div class="text-sm text-red-700">{{ error }}</div>
        </div>
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="email" class="sr-only">Email address</label>
            <input
              id="email"
              type="email"
              required
              v-model="email"
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Email address"
            />
          </div>
          <div>
            <label for="password" class="sr-only">Password</label>
            <input
              id="password"
              type="password"
              required
              v-model="password"
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Password"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            {{ loading ? 'Signing in...' : 'Sign in' }}
          </button>
        </div>
      </form>
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Not have account ? Please Register first.
        </h2>
        <div class="text-center">
          <a
            href="/register"
            class="font-medium text-indigo-600 hover:text-indigo-500"
          >
            Register
          </a>
          </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

export default {
  name: 'LoginPage',
  
  setup() {
    const authStore = useAuthStore();
    const router = useRouter();
    
    const email = ref('');
    const password = ref('');
    const error = ref('');
    const loading = ref(false);

    const handleSubmit = async () => {
      error.value = '';
      loading.value = true;

      const result = await authStore.login({
        email: email.value,
        password: password.value,
      });

      loading.value = false;

      if (result.success) {
        router.push('/dashboard');
      } else {
        error.value = result.error;
      }
    };

    return {
      email,
      password,
      error,
      loading,
      handleSubmit
    };
  }
};
</script>