<template>
  <div class="space-y-6">
    <!-- Header with Add Product Button -->
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-800">Products Management</h2>
      <button
        @click="openAddModal"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
      >
        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Product
      </button>
    </div>

    <!-- Products Table (simplified for testing) -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
      <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Product List
        </h3>
      </div>
      
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id">
              <td class="px-6 py-4">{{ product.name }}</td>
              <td class="px-6 py-4">${{ product.price }}</td>
              <td class="px-6 py-4">
                <button @click="editProduct(product)" class="text-blue-600 hover:text-blue-900 mr-3">
                  Edit
                </button>
                <button @click="deleteProduct(product)" class="text-red-600 hover:text-red-900">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add/Edit Product Modal - SIMPLIFIED VERSION -->
  <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Modal Header -->
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">
            {{ isEditing ? 'Edit Product' : 'Add New Product' }}
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit">
          <!-- Name -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
            <input
              type="text"
              v-model="form.name"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter product name"
            />
            <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">{{ formErrors.name[0] }}</p>
          </div>

          <!-- Description -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              v-model="form.desc"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter description"
            ></textarea>
          </div>

          <!-- Price & Quantity -->
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
              <input
                type="number"
                v-model="form.price"
                required
                min="0"
                step="0.01"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0.00"
              />
              <p v-if="formErrors.price" class="mt-1 text-sm text-red-600">{{ formErrors.price[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
              <input
                type="number"
                v-model="form.qty"
                required
                min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0"
              />
              <p v-if="formErrors.qty" class="mt-1 text-sm text-red-600">{{ formErrors.qty[0] }}</p>
            </div>
          </div>

          <!-- Status -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
            <select
              v-model="form.status"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <option :value="1">Active</option>
            <option :value="0">Inactive</option>
            </select>
          </div>

          <!-- NEW: Thumbnail Field (Image URL for now) -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail URL *</label>
            <input
              type="text"
              v-model="form.thumbnail"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter image URL or leave default"
            />
            <p class="mt-1 text-xs text-gray-500">Example: https://example.com/image.jpg or default-thumbnail.jpg</p>
            <p v-if="formErrors.thumbnail" class="mt-1 text-sm text-red-600">{{ formErrors.thumbnail[0] }}</p>
          </div>

          <!-- NEW: Optional Image URLs -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">First Image URL (Optional)</label>
            <input
              type="text"
              v-model="form.first_image"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Optional first image URL"
            />
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Second Image URL (Optional)</label>
            <input
              type="text"
              v-model="form.second_image"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Optional second image URL"
            />
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Third Image URL (Optional)</label>
            <input
              type="text"
              v-model="form.third_image"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Optional third image URL"
            />
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end space-x-3 mt-6">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 disabled:opacity-50"
            >
              <span v-if="submitting">Saving...</span>
              <span v-else>{{ isEditing ? 'Update' : 'Create' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal - SIMPLIFIED -->
  <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
          <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <div class="mt-3 text-center">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Delete Product
          </h3>
          <div class="mt-2">
            <p class="text-sm text-gray-500">
              Are you sure you want to delete "{{ productToDelete?.name }}"?
            </p>
          </div>
        </div>
        <div class="flex justify-center space-x-4 mt-6">
          <button
            @click="closeModal"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            @click="confirmDelete"
            :disabled="deleting"
            class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 disabled:opacity-50"
          >
            <span v-if="deleting">Deleting...</span>
            <span v-else>Delete</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// State
const products = ref([]);
const loading = ref(false);
const error = ref('');
const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const submitting = ref(false);
const deleting = ref(false);
const isEditing = ref(false);
const productToDelete = ref(null);

// Form - UPDATED to include image fields
const form = ref({
  name: '',
  desc: '',
  price: '',
  qty: '',
  status: 1, 
  thumbnail: 'default-thumbnail.jpg', // Default thumbnail
  first_image: '',
  second_image: '',
  third_image: '',
});

const formErrors = ref({});

// Methods
const fetchProducts = async () => {
  try {
    loading.value = true;
    const response = await axios.get('http://127.0.0.1:8000/api/products', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });
    products.value = response.data.data || [];
  } catch (err) {
    console.error('Error:', err);
    error.value = 'Failed to load products';
  } finally {
    loading.value = false;
  }
};

const openAddModal = () => {
  resetForm();
  isEditing.value = false;
  showAddModal.value = true;
};

const resetForm = () => {
  form.value = {
    name: '',
    desc: '',
    price: '',
    qty: '',
    status: 1, 
    thumbnail: 'default-thumbnail.jpg', // Default thumbnail
    first_image: '',
    second_image: '',
    third_image: '',
  };
  formErrors.value = {};
};

const editProduct = (product) => {
  isEditing.value = true;
  showEditModal.value = true;
  
  form.value = {
    name: product.name,
    desc: product.desc || '',
    price: product.price,
    qty: product.qty,
    status: product.status,
    thumbnail: product.thumbnail || 'default-thumbnail.jpg',
    first_image: product.first_image || '',
    second_image: product.second_image || '',
    third_image: product.third_image || '',
  };
  
  form.value.id = product.id;
};

const deleteProduct = (product) => {
  productToDelete.value = product;
  showDeleteModal.value = true;
};

const confirmDelete = async () => {
  try {
    deleting.value = true;
    await axios.delete(`http://127.0.0.1:8000/api/products/${productToDelete.value.id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });
    
    products.value = products.value.filter(p => p.id !== productToDelete.value.id);
    closeModal();
  } catch (err) {
    console.error('Error:', err);
    alert('Failed to delete product');
  } finally {
    deleting.value = false;
  }
};

const handleSubmit = async () => {
  try {
    submitting.value = true;
    formErrors.value = {};
    
    // Prepare data with all required fields
    const data = {
      name: form.value.name,
      desc: form.value.desc,
      price: parseFloat(form.value.price),
      qty: parseInt(form.value.qty),
      status: parseInt(form.value.status), 
      thumbnail: form.value.thumbnail, // Include thumbnail
      first_image: form.value.first_image || null,
      second_image: form.value.second_image || null,
      third_image: form.value.third_image || null,
    };
    
    console.log('Sending data:', data);
    
    let response;
    if (isEditing.value) {
      response = await axios.put(`http://127.0.0.1:8000/api/products/${form.value.id}`, data, {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
    } else {
      response = await axios.post('http://127.0.0.1:8000/api/products', data, {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
    }
    
    console.log('API response:', response.data);
    await fetchProducts();
    closeModal();
    
    // Show success message
    alert(response.data.message || 'Product saved successfully!');
    
  } catch (err) {
    console.error('Error:', err);
    if (err.response?.status === 422) {
      formErrors.value = err.response.data.errors;
      
      // Show validation errors
      if (formErrors.value) {
        const errorMessages = Object.values(formErrors.value).flat().join(', ');
        alert(`Validation errors: ${errorMessages}`);
      }
    } else {
      alert(err.response?.data?.message || 'Failed to save product');
    }
  } finally {
    submitting.value = false;
  }
};

const closeModal = () => {
  showAddModal.value = false;
  showEditModal.value = false;
  showDeleteModal.value = false;
  isEditing.value = false;
  productToDelete.value = null;
  resetForm();
};

onMounted(() => {
  fetchProducts();
});
</script>