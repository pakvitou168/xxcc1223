<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Vehicle Usages
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 lg:col-span-6">
                <div class="intro-y box p-5">
                    <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Product *</label>
                                <Dropdown 
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.product_code"
                                    :options="autoProductOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Product"
                                >
                                    <template #option="slotProps">
                                        <p class="text-sm font-semibold">{{slotProps.option.label}}</p>
                                        <span class="text-xs">{{slotProps.option.desc}}</span>

                                    </template>
                                </Dropdown>
                           <span v-if="errors.product_code" class="text-theme-6">
                                {{ errors.product_code[0] }}
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
                            <label class="mb-1 block font-bold">Description</label>
                            <Textarea
                            class="w-full"
                            rows="3"
                            v-model="formValues.description"
                            placeholder="Description"
                            />
                        </div>
                    </div>
                    <div class="text-right mt-5">
                        <router-link to="/product-configuration/vehicle-usages" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
                        <button type="button" @click="handleSubmit" class="btn btn-primary w-24">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {
                product_code: null,
            },
            autoProductOptions: [],
            errors: [],
        }
    },

    methods: {
        handleSubmit() {
            if (!this.id) {
                axios.post('/vehicle-usages', this.formValues)
                    .then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success', 'bottom-right')
                            this.$router.push({ name: 'VehicleUsageIndex' })
                        }
                    }).catch(err => {
                        if (err.response.status = 422) {
                            this.errors = err.response.data.errors
                        } else {
                            notify('Error', 'Error','bottom-right')
                        }
                    })
            } else {
                axios.put(`/vehicle-usages/${this.id}`, this.formValues)
                    .then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success','bottom-right')
                            this.$router.push({ name: 'VehicleUsageIndex' })
                        }
                    }).catch(err => {
                        if (err.response.status = 422) {
                            this.errors = err.response.data.errors
                        } else {
                            notify('Error', 'Error','bottom-right')
                        }
                    })
            }
        },
        listAutoProducts() {
            axios.get('/vehicle-usages-service/list-auto-products').then(response => this.autoProductOptions = response.data)
        },
        getVehicleUsage() {
            if (this.id) {
                axios.get(`/vehicle-usages/${this.id}`).then(response => {
                    this.formValues = response.data
                })
            }
        },
    },

    mounted() {
        this.getVehicleUsage()
        this.listAutoProducts()
    },
}
</script>