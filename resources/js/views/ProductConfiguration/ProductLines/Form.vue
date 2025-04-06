<template>
   <div>
       <div class="intro-y flex items-center mt-8">
           <h2 class="text-lg font-medium mr-auto">
               {{ id ? 'Edit' : 'Create' }} Product Line
           </h2>
       </div>
       <div class="grid grid-cols-12 gap-6 mt-5">
           <div class="intro-y col-span-12 lg:col-span-6">
               <div class="intro-y box p-5">
                   <form @submit.prevent="handleSubmit">
                       <div class="mb-4">
                           <label class="form-label">Product Line Code *</label>
                           <InputText 
                               v-model="formValues.code"
                               :class="{'p-invalid': errors.code}"
                               class="w-full" 
                               placeholder="Product Line Code"
                           />
                           <small class="text-theme-6" v-if="errors.code">{{ errors.code[0] }}</small>
                       </div>

                       <div class="mb-4">
                           <label class="form-label">Description</label>
                           <InputText
                               v-model="formValues.description"
                               class="w-full"
                               placeholder="Description"  
                           />
                       </div>

                       <div class="text-right mt-5">
                           <router-link 
                               to="/product-configuration/product-lines" 
                               class="btn btn-outline-secondary w-24 mr-1"
                           >
                               Cancel
                           </router-link>
                           <Button 
                               type="submit"
                               class="btn btn-primary w-24"
                           >
                               {{ id ? 'Update' : 'Create' }}
                           </Button>
                       </div>
                   </form>
               </div>
           </div>
       </div>
   </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const id = ref(route.params.id ?? null)
const formValues = ref({})
const errors = ref([])

const handleSubmit = async () => {
    try {
        let response
        if (!id.value) {
            response = await axios.post('/product-lines', formValues.value)
        } else {
            response = await axios.put(`/product-lines/${id.value}`, formValues.value)
        }

        if (response.data.success) {
            notify(response.data.message, 'success', 'bottom-right')
            router.push({name: 'ProductLineIndex'})
        }
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors
        }
    }
}

const getProductLine = async () => {
    if (id.value) {
        try {
            const response = await axios.get(`/product-lines/${id.value}`)
            formValues.value = response.data
        } catch (err) {
            notify('Error loading product line', 'error', 'bottom-right')
        }
    }
}

onMounted(() => {
    getProductLine()
})
</script>