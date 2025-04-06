<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Business Category
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-6">
                <div class="intro-y box p-5">
                    <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Name *</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.name" 
                                placeholder="Name"
                            />
                            <span v-if="errors.name" class="text-theme-6">
                                {{ errors.name[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Description</label>
                            <Textarea
                                class="w-full"
                                rows="3"
                                v-model="formValues.description"
                                placeholder="Description"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Prefix *</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.prefix"
                                placeholder="Prefix"
                                @input="validatePrefix"
                            />
                        <span v-if="errors.prefix" class="text-theme-6">
                            {{ errors.prefix[0] }}
                        </span>
                        </div>

                        <div class="col-span-12 text-right mt-5">
                            <router-link to="/business-management/business-categories" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
                            <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
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

<script>

import axios from "axios";

export default {
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {
                prefix: '',
            },
            errors: {},
        }
    },

    methods: {
    validatePrefix() {
      if (this.formValues.prefix && this.formValues.prefix.length > 10) {
        this.errors.prefix = ['Prefix must be less than or equal to 10 characters long.']
      } else {
        this.errors.prefix = null
      }
    },
        handleSubmit() {
            if (!this.id) {
                axios.post('/business-categories', this.formValues)
                    .then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success','bottom-right')
                            this.$router.push({name:"BusinessCategoryIndex"})
                        }
                    }).catch(err => {
                        if (err.response?.status === 422) {
                            this.errors = err.response.data.errors
                        } else {
                            notify('Error', 'error','bottom-right')
                        }
                    })
            } else {
                axios.put(`/business-categories/${this.id}`, this.formValues)
                    .then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success','bottom-right')
                            this.$router.push({name:"BusinessCategoryIndex"})
                        }
                    }).catch(err => {
                        if (err.response?.status === 422) {
                            this.errors = err.response.data.errors
                        } else {
                            notify('Error', 'error','bottom-right')
                        }
                    })
            }
        },

        getBusinessCategory() {
            if (this.id) {
                axios.get(`/business-categories/${this.id}/edit`)
                    .then(response => {
                        this.formValues = response.data;
                    })
            }
        },
    },

    mounted() {
        this.getBusinessCategory()
    }
}
</script>
