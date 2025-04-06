<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Country
            </h2>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12 lg:col-span-6 p-5">
                <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Country Code *</label>
                        <InputText
                            class="w-full p-inputtext-sm"
                            v-model="formValues.country_code"
                            placeholder="Country Code"
                            @input="validateCountryCode"
                        />
                        <span v-if="errors.country_code" class="text-theme-6">
                            {{ errors.country_code[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Description</label>
                        <InputText
                            class="w-full p-inputtext-sm"
                            v-model="formValues.description"
                            placeholder="Description"
                        />
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Alternate Code *</label>
                        <InputText
                            class="w-full p-inputtext-sm"
                            v-model="formValues.alt_country_code"
                            placeholder="Alternate Code"
                            @input="validateAltCode"
                        />
                        <span v-if="errors.alt_country_code" class="text-theme-6">
                            {{ errors.alt_country_code[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">ISD Code *</label>
                        <InputText
                            class="w-full p-inputtext-sm"
                            v-model="formValues.isd_code"
                            placeholder="ISD Code"
                            @input="validateIsdCode"
                        />
                        <span v-if="errors.isd_code" class="text-theme-6">
                            {{ errors.isd_code[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <div class="text-right mt-5">
                            <router-link 
                                to="/customer-management/countries" 
                                class="btn btn-outline-secondary w-24 mr-1"
                            >
                                Cancel
                            </router-link>
                            <button 
                                type="button" 
                                @click="handleSubmit" 
                                class="btn btn-primary w-24"
                            >
                                <span v-if="id">Update</span>
                                <span v-else>Create</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const id = ref(route.params.id ?? null)
const formValues = ref({
    country_code: '',
    description: '',
    alt_country_code: '',
    isd_code: '',
})
const errors = ref({})

const validateCountryCode = () => {
    if (formValues.value.country_code.length > 2) {
        errors.value.country_code = ['Country Code must be less than or equal to 2 characters long.']
    } else {
        errors.value.country_code = null
    }
}
const validateIsdCode = () => {
    const numberValue = Number(formValues.value.isd_code)
    if (isNaN(numberValue) || !Number.isInteger(numberValue)) {
        errors.value.isd_code = ['Isd_code must be a number.']
    } else {
        errors.value.isd_code = null
    }
}
const validateAltCode = () => {
    if (formValues.value.alt_country_code.length > 3) {
        errors.value.alt_country_code = ['Alternate Code must be less than or equal to 3 characters long.']
    } else {
        errors.value.alt_country_code = null
    }
}

const handleSubmit = () => {
    errors.value = {}
    
    if (!id.value) {
        axios.post('/countries', formValues.value)
            .then(response => {
                if (response.data.success) {
                    notify(response.data.message, 'success', 'bottom-right')
                    router.push({ name: 'CountryIndex' })
                }
            })
            .catch(err => {
                if (err.response?.status === 422) {
                    errors.value = err.response.data.errors
                } else {
                    notify('Error', 'error', 'bottom-right')
                }
            })
    } else {
        axios.put(`/countries/${id.value}`, formValues.value)
            .then(response => {
                if (response.data.success) {
                    notify(response.data.message, 'success', 'bottom-right')
                    router.push({ name: 'CountryIndex' })
                }
            })
            .catch(err => {
                if (err.response?.status === 422) {
                    errors.value = err.response.data.errors
                } else {
                    notify('Error', 'error', 'bottom-right')
                }
            })
    }
}

const getCountry = () => {
    if (id.value) {
        axios.get(`/countries/${id.value}`)
            .then(response => {
                formValues.value = response.data
            })
            .catch(() => {
                notify('Error fetching country data', 'error', 'bottom-right')
            })
    }
}

onMounted(() => {
    getCountry()
})
</script>
<!-- <script>
export default {
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {},
            errors: [],
        }
    },
    methods: {
        handleSubmit() {
            if (!this.id) {
                axios.post('/countries', this.formValues).then(response => {
                    if (response.data.success) {
                        this.toastMessage(response.data.message, 'Success')
                        this.$router.push({ name: 'CountryIndex'})
                    }
                }).catch(err => {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors
                    } else {
                        this.toastMessage('Error', 'Error')
                    }
                })
            } else {
                axios.put(`/countries/${this.id}`, this.formValues).then(response => {
                    if (response.data.success) {
                        this.toastMessage(response.data.message, 'Success')
                        this.$router.push({ name: 'CountryIndex'})
                    }
                }).catch(err => {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors
                    } else {
                        this.toastMessage('Error', 'Error')
                    }
                })
            }
        },
        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        },
        getCountry() {
            if (this.id) {
                axios.get(`/countries/${this.id}`).then(response => {
                    this.formValues = response.data
                })
            }
        }
    },
    mounted() {
        this.getCountry()
    }
}
</script> -->