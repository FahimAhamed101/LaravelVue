import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/utils/axios';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('token'));
  const isAuthenticated = computed(() => !!token.value);

  // Set authentication header
  const setAuthHeader = (token) => {
    if (token) {
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    } else {
      delete api.defaults.headers.common['Authorization'];
    }
  };

  // Initialize auth header if token exists
  if (token.value) {
    setAuthHeader(token.value);
  }

  const login = async (credentials) => {
    try {
      const response = await api.post('/user/login', credentials);
      const { user: userData, access_token } = response.data;
      
      user.value = userData;
      token.value = access_token;
      localStorage.setItem('token', access_token);
      setAuthHeader(access_token);
      
      return { success: true, data: response.data };
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.message || 'Login failed' 
      };
    }
  };

  const register = async (userData) => {
    try {
      const response = await api.post('/user/register', userData);
      return { success: true, data: response.data };
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.message || 'Registration failed' 
      };
    }
  };

  const logout = async () => {
    try {
      await api.post('/user/logout');
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      // Clear auth state regardless of API response
      user.value = null;
      token.value = null;
      localStorage.removeItem('token');
      setAuthHeader(null);
    }
  };

  const fetchUser = async () => {
    if (!token.value) return;
    
    try {
      // Add your user fetch endpoint if available
      // const response = await api.get('/user');
      // user.value = response.data;
    } catch (error) {
      console.error('Failed to fetch user:', error);
      logout();
    }
  };

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser,
  };
});