<template>
    <div class="grid grid-cols-1 sm:grid-cols-1 gap-y-5 sm:w-full md:w-3/4 lg:w-2/5 xl:w-1/2">
        <div>
            <label for="" class="block mb-1 font-semibold">Business Type *</label>
            <Dropdown :options="businessTypeOptions" v-model="formValues.business_type" :showClear="true"
                :disabled="!canSave" optionLabel="label" class="w-full" optionValue="value"
                placeholder="Select business type" />
            <span v-if="errors['business_type']" class="text-red-500 text-xs">{{ errors['business_type'][0] }}</span>
        </div>
        <div>
            <label for="" class="block mb-1 font-semibold">Policy Type *</label>
            <Dropdown :options="policyTypeOptions" v-model="formValues.policy_type" :showClear="true"
                :disabled="!canSave" optionLabel="label" class="w-full" optionValue="value"
                placeholder="Select policy type" />
            <span v-if="errors['policy_type']" class="text-red-500 text-xs">{{ errors['policy_type'][0] }}</span>
        </div>

        <div v-if="canSave" class="text-right mt-5">
            <router-link :to="{ name: 'PolicyIndex' }" class="btn btn-outline-secondary w-24 mr-1"
                tag="button">Cancel</router-link>
            <button type="button" @click="handleSubmit" class="btn btn-primary w-24"> <i class="pi pi-spinner pi-spin mr-1"
                    v-if="saving"></i> Save</button>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        id: [Number, String],
        dataId: [Number, String],
        businessType: String,
        policyType: String,
        isEndorsement: Boolean,
        endorsementType: String,
    },

    data() {
        return {
            formValues: {},
            businessTypeOptions: [],
            policyTypeOptions: [],
            errors: [],
            saving: false
        }
    },
    computed: {
        generalEndorsement() {
            return this.endorsementType === 'GENERAL'
        },
        canSave() {
            // if not an endorsement
            if (!this.isEndorsement) return true
            // if endorsement type is GENERAL
            return this.generalEndorsement
        }
    },
    methods: {
        listBusinessTypes() {
            axios.get('/policy-service/list-business-types').then(response => {
                this.businessTypeOptions = response.data
            })
        },
        listPolicyTypes() {
            axios.get('/policy-service/list-policy-types').then(response => {
                this.policyTypeOptions = response.data
            })
        },
        handleSubmit() {
            this.errors = []
            if (!this.isEndorsement)
                this.handleSubmitPolicy()
            else
                // For update policy configuration in endorsement
                this.handleSubmitEndorsement()
        },
        handleSubmitPolicy() {
            this.saving = true
            axios.put(`/api/policies/${this.id}`, this.formValues).then(response => {
                if (response.data.success) {
                    this.toastMessage(response.data.message, 'Success')
                    // Update issue_date
                    this.handleIssueDate()
                    // Go to the commission tab
                    document.querySelector('.nav-tabs a[href="#commission"]').click()
                }
            }).finally(() => {
                this.$emit('checkPolicyStatus')
                this.saving = false
            })
                .catch(err => {
                    if (err.status === 422) this.errors = err.response.data?.errors
                    else notify('Something went wrong', 'error')
                })
        },
        handleSubmitEndorsement() {
            // If not general endorsement cannot be submitted
            if (!this.canSave) return;
            this.saving = true
            axios.put(`/api/endorsements/${this.id}`, this.formValues).then(response => {
                if (response.data.success) {
                    this.toastMessage(response.data.message, 'Success')
                    // Go to the commission tab
                    document.querySelector('.nav-tabs a[href="#commission"]').click()
                }
            }).catch(err => {
                if (err.reponse.data?.status === 422) this.errors = err.response.data?.errors
                else notify('Something went wrong', 'error')
            }).finally(() => this.saving = false)
        },

        handleIssueDate() {
            axios.put('/autos/update-issue-date/' + this.dataId).catch((e) => {
                console.log(e)
            })
        },

        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        },
        getData() {
            if (!this.isEndorsement)
                this.getDataPolicy()
            else
                this.getDataEndorsement()
        },
        getDataPolicy() {
            if (this.id) {
                axios.get(`/api/policies/${this.id}`).then(response => {
                    this.formValues = response.data;
                })
            }
        },
        getDataEndorsement() {
            if (this.id) {
                axios.get(`/api/endorsements/${this.id}`).then(response => {
                    this.formValues = response.data;
                })
            }
        },
    },
    mounted() {
        this.getData()
        this.listBusinessTypes()
        this.listPolicyTypes()
    },
}
</script>
