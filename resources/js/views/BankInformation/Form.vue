<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Bank Information
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 lg:col-span-6">
                <div class="intro-y box p-5">
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
                            <label class="mb-1 block font-bold">Bank Name *</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.name"
                                placeholder="Bank Name"
                            />
                            <span v-if="errors.name" class="text-theme-6">
                                {{ errors.name[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Account No. *</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.account_no"
                                placeholder="Account No."
                            />
                            <span v-if="errors.account_no" class="text-theme-6">
                                {{ errors.account_no[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Account Name *</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.account_name"
                                placeholder="Account Name"
                            />
                            <span v-if="errors.account_name" class="text-theme-6">
                                {{ errors.account_name[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Currency *</label>
                            <Dropdown
                                class="w-full p-inputtext-sm"
                                v-model="formValues.ccy"
                                :options="currencyOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Currency"
                            />
                            <span v-if="errors.ccy" class="text-theme-6">
                                {{ errors.ccy[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="block mb-1 font-bold">Is Default</label>
                            <Checkbox
                                v-model="formValues.default"
                                :binary="true"
                            />
                        </div>

                        <div class="col-span-12 text-right mt-5">
                            <router-link to="/bank-informations" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
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

export default {
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {},
            errors: [],
            currencyOptions: [],
        }
    },
    methods: {
        getBankInformation() {
            if (this.id) {
                axios.get(`/api/bank-informations/${this.id}/edit`)
                    .then(response => {
                        this.formValues = response.data;
                    })
            }
        },

        listCurrency() {
            axios.get("/exchange-rate-service/currency").then((response) => {
                this.currencyOptions = response.data;
            });
        },

        handleSubmit() {
            this.setDefaultFieldsValue();
            if (!this.id) {
                axios.post('/api/bank-informations', this.formValues)
                    .then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success','bottom-right')
                            this.$router.push({
                                name:"BankInformationIndex"
                            })
                        }
                    }).catch(err => {
                        notify('Error', 'error','bottom-right')
                        if (err.response.status = 422) {
                            this.errors = err.response.data.errors
                        }
                    })
            } else {
                axios.put(`/api/bank-informations/${this.id}`, this.formValues)
                    .then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success','bottom-right')
                            this.$router.push({
                                name:"BankInformationIndex"
                            })
                        }
                    }).catch(err => {
                        notify('Error', 'error','bottom-right')
                        if (err.response.status = 422) {
                            this.errors = err.response.data.errors
                        }
                    })
            }
        },
        setDefaultFieldsValue(){
            this.formValues.type = 'BANK_ACCOUNT'
            this.formValues.status = 'ACT'
        },
    },
    mounted() {
        this.getBankInformation()
        this.listCurrency()
    }
}
</script>
