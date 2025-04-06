<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Vehicle Classification
            </h2>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12 lg:col-span-6 p-5">
                <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Code *</label>
                        <InputText
                        class="w-full p-inputtext-sm"
                        v-model="formValues.code"
                        placeholder="Code"
                        />
                        <span v-if="errors.code" class="text-theme-6">
                            {{ errors.code[0] }}
                        </span> 
                    </div>
                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Description *</label>
                        <InputText
                        class="w-full p-inputtext-sm"
                        v-model="formValues.description"
                        placeholder="Description"
                        />
                        <span v-if="errors.description" class="text-theme-6">
                            {{ errors.description[0] }}
                        </span> 
                    </div>

                    <div class="col-span-12">
                        <label for="" class="font-bold block mb-1">Surcharge %</label>
                        <InputNumber
                            class="w-full"
                            v-model="formValues.surcharge"
                            :min="0"
                            step="any"
                            placeholder="Surcharge"
                        />
                        <span v-if="errors.surcharge" class="text-theme-6">
                            {{ errors.surcharge[0] }}
                        </span>
                    </div>
                </div>
                <div class="text-right mt-5">
                    <router-link to="/product-configuration/vehicle-classifications" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
                    <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
                    <span v-if="id">Update</span>
                    <span v-else>Create</span>
                    </button>
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
            formValues: {},
            errors: [],
        }
    },
    methods: {
        handleSubmit() {
            if (!this.id) {
                axios.post('/vehicle-classifications', this.formValues).then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'success','bottom-right')
                        this.$router.push("/product-configuration/vehicle-classifications")
                    }
                }).catch(err => {
                    if (err.response.status === 422) {
                        this.errors = err.response.data.errors
                    } else {
                        notify('Error', 'Error','bottom-right')
                    }
                })
            } else {
                axios.put(`/vehicle-classifications/${this.id}`, this.formValues).then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'success','bottom-right')
                        this.$router.push("/product-configuration/vehicle-classifications")
                    }
                }).catch(err => {
                    if (err.response.status === 422) {
                        this.errors = err.response.data.errors
                    } else {
                        notify('Error', 'Error','bottom-right')
                    }
                })
            }
        },
        getVehicleClassification() {
            if (this.id) {
                axios.get(`/vehicle-classifications/${this.id}`).then(response => {
                    this.formValues = response.data;
                })
            }
        },
    },
    mounted() {
        this.getVehicleClassification()
    },
}
</script>