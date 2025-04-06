<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Surcharge Rule
            </h2>
        </div>
        <div class="intro-y grid lg:grid-cols-2 sm:grid-cols-1 mt-5">
            <div class="box p-5">
                <div class="grid gap-5">
                    <div>
                        <label for="" class="form-label">Claim Ratio From (%)*</label>
                        <InputNumber v-model="formValues.claim_ratio_from" placeholder="Claim Ratio From"
                            class="w-full" />
                    </div>
                    <div>
                        <label for="" class="form-label">Claim Ratio to (%) *</label>
                        <InputNumber v-model="formValues.claim_ratio_to" placeholder="Claim Ratio to" class="w-full" />
                    </div>
                    <div>
                        <label for="" class="form-label">Surcharge (%) *</label>
                        <InputNumber v-model="formValues.surcharge" placeholder="Surcharge" class="w-full"
                            validation="required" />
                    </div>
                    <div class="text-right mt-5">
                        <router-link to="/surcharge-rules" class="btn btn-outline-secondary w-24 mr-1"
                            tag="button">Cancel</router-link>
                        <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
                            <i :class="{ 'pi pi-spinner pi-spin': saving, 'pi pi-save' : !saving, 'mr-1' : 1 }"></i> Submit
                        </button>
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
            formValues: {},
            errors: [],
            currencyOptions: [],
            saving: false
        }
    },
    methods: {
        getSurcharge() {
            if (this.id) {
                axios.get(`/api/surcharge-rules/${this.id}`)
                    .then(response => {
                        this.formValues = response.data;
                    })
            }
        },

        handleSubmit() {
            this.saving = true
            if (!this.id) {
                axios.post('/api/surcharge-rules', this.formValues)
                    .then(response => {
                        if (response.data.success) {
                            this.toastMessage(response.data.message, 'Success')
                            this.$router.push('/surcharge-rules')
                        }
                    }).catch(err => {
                        this.toastMessage('Error', 'Error')
                        if (err.response.status = 422) {
                            this.errors = err.response.data.errors
                        }
                    }).finally(() => this.id)
            } else {
                axios.put(`/api/surcharge-rules/${this.id}`, this.formValues)
                    .then(response => {
                        if (response.data.success) {
                            this.toastMessage(response.data.message, 'Success')
                            this.$router.push('/surcharge-rules')
                        }
                    }).catch(err => {
                        this.toastMessage('Error', 'Error')
                        if (err.response.status = 422) {
                            this.errors = err.response.data.errors
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
        }
    },
    mounted() {
        this.getSurcharge()
    }
}
</script>
