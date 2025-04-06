<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Business Handler
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-6">
                <div class="intro-y box p-5">
                    <form @submit.prevent="handleSubmit">
                        <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                            <div class="col-span-12" v-if="id">
                                <label class="mb-1 block font-bold">Handler Code</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.handler_code"
                                    disabled
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Title *</label>
                                <Dropdown
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.title"
                                    :options="titleOptions"
                                    placeholder="Select Title"
                                />
                                <span v-if="errors.title" class="text-theme-6">
                                    {{ errors.title[0] }}
                                </span>
                            </div>

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
                                <label class="mb-1 block font-bold">Employee Code *</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.employee_code"
                                    placeholder="Employee Code"
                                    @input="validateEmployeeCode"
                                />
                                <span v-if="errors.employee_code" class="text-theme-6">
                                    {{ errors.employee_code[0] }}
                                </span>
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Phone</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.phone"
                                    placeholder="Phone"
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Email</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.email"
                                    type="email"
                                    placeholder="Email"
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Incentive Rate</label>
                                <InputNumber
                                    class="w-full"
                                    v-model="formValues.incentive_rate"
                                    :min="0"
                                    step="any"
                                    placeholder="Incentive Rate"
                                />
                            </div>
                        </div>

                        <div class="text-right mt-5">
                            <router-link to="/business-management/business-handlers" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
                            <button type="submit" class="btn btn-primary w-24">
                                <span v-if="id">Update</span>
                                <span v-else>Create</span>
                            </button>
                        </div>
                    </form>
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
            formValues: {},
            titleOptions: ['Mr.', 'Mrs.', 'Ms.'],
            errors: {}
        }
    },

    methods: {
        validateEmployeeCode() {
            if (this.formValues.employee_code?.length > 5) {
                this.errors.employee_code = ['Employee Code must be less than or equal to 5 characters long.']
            } else {
                this.errors.employee_code = null
            }
        },
        handleSubmit() {
            if (!this.id) {
                axios.post('/business-handlers', this.formValues)
                    .then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success','bottom-right')
                            this.$router.push({
                                name:"BusinessHandlerIndex"
                            })
                        }
                    }).catch(err => {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors
                    } else {
                        notify('Error', 'error', 'bottom-right')
                    }
                    })
            } else {
                axios.put(`/business-handlers/${this.id}`, this.formValues)
                    .then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success', 'bottom-right')
                            this.$router.push({
                                name:"BusinessHandlerIndex"
                            })
                        }
                    }).catch(err => {
                        notify('Error', 'error','bottom-right')
                    })
            }
        },

        getBusinessHandler() {
            if (this.id) {
                axios.get(`/business-handlers/${this.id}/edit`)
                    .then(response => {
                        this.formValues = response.data;
                    })
            }
        },
    },

    mounted() {
        this.getBusinessHandler()
    }
}
</script>