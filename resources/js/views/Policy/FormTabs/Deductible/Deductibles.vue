<template>
    <div class="grid">
        <LoadingIndicator v-if="isLoading" />
        <div v-else>
            <div v-for="(deductibles, detailId, index) in formValues" :key="detailId"
                class="pt-5 pb-2 border-b border-gray-300 first:pt-0 last:border-b-0">
                <DeductibleItem v-model="formValues[detailId]" v-if="detailId" :index="index" :detailId="detailId"
                    @toggle="toggle" @collapse="collapse" :cancelPolicyEndorsement="cancelPolicyEndorsement" />
            </div>
        </div>
        <div class="flex justify-end">
            <router-link :to="{ name: cancelRoute }" class="button-danger mr-1"
                tag="button">Cancel</router-link>
            <button v-if="!cancelPolicyEndorsement" type="submit" class="button-primary" @click="handleSubmit">
                <span><i class="pi pi-spinner pi-spin" v-if="submitting"></i> {{ isQuotationTab ? 'Submit' : 'Save'
                    }}</span>
            </button>
        </div>
    </div>
</template>

<script>

import DeductibleItem from './DeductibleItem.vue'
import LoadingIndicator from '../../../../components/LoadingIndicator.vue'

export default {
    props: {
        id: [Number, String],
        cancelRoute: String,
        // If in auto quotation
        isQuotationTab: {
            type: Boolean,
            default: false,
        },
        redirectRoute: {
            type: String,
            default: '',
        },
        endorsementType: {
            type: String,
            default: ''
        },
    },

    components: {
        DeductibleItem,
        LoadingIndicator,
    },

    data() {
        return {
            formValues: {},
            isLoading: true,
            submitting: false
        }
    },

    computed: {
        cancelPolicyEndorsement() {
            if (this.endorsementType === '') return false

            return this.endorsementType === 'CANCELLATION'
        },
    },

    methods: {
        getDeductibleDetails(autoId) {
            this.isLoading = true
            axios.get('/auto-service/get-deductible-details/' + autoId).then(response => {
                this.isLoading = false

                this.formValues = response.data
            })
        },

        handleSubmit() {
            this.submitting = true
            axios.post('/deductible-details/updates', this.formValues).then(response => {
                if (response.data.success) {
                    this.toastMessage(response.data.message, 'Success')
                    if (this.redirectRoute)
                        this.$router.push({ name: this.redirectRoute })

                    // Update issue_date
                    this.handleIssueDate()

                    // If it is in policy and endorsement
                    if (!this.isQuotationTab) {
                        this.getDeductibleDetails(this.id)
                        // Go to the config tab
                        document.querySelector('.nav-tabs a[href="#config"]').click()
                    }
                }
            }).catch(() => this.toastMessage('Error', 'Error')).finally(() => this.submitting = false)
        },

        handleIssueDate() {
            axios.put('/autos/update-issue-date/' + this.id).catch((e) => {
                console.log(e)
            })
        },

        toggle(vehicleNo) {
            for (let index = 0; index < _.size(this.formValues); index++) {
                var header = document.querySelector('.show-deductible-' + index)
                var icon = document.querySelector('.deductible-icon-' + index)
                if (vehicleNo !== index) {
                    header.classList.add('collapse')
                    icon.classList.add('rotate', 'icon-color')
                    continue;
                }
                header.classList.toggle('collapse')
                icon.classList.toggle('rotate')
                icon.classList.toggle('icon-color')
            }
        },

        collapse(vehicleNo) {
            var classId = document.querySelector('.show-deductible-' + vehicleNo)
            var iconId = document.querySelector('.deductible-icon-' + vehicleNo)
            classId.classList.remove('collapse')
            iconId.classList.remove('rotate')
            iconId.classList.remove('icon-color')
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
        this.getDeductibleDetails(this.id)
    },
}
</script>
