<template>
    <div>
        <div class="flex mr-1 mb-3">
            <span>
                <svg class="w-6 h-6 text-green-500 rotate icon-color" :class="`reinsurance-icon-${index}`"
                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </span>
            <span class="input-label text-base cursor-pointer w-full" @click="toggle(index)">Vehicle #{{ index + 1 }}
                (Sum Insured: {{ reinsurance[0].vehicle_value   }} USD, Premium: {{
                    reinsurance[0].vehicle_total_premium   }} USD)<span v-if="isEndorsement"
                    v-html="vehicleStatusTag()"></span></span>
        </div>
        <div class="collapse" :class="`show-reinsurance-${index}`">
            <div class="w-full overflow-x-auto">
                <div style="min-width: 1500px;">
                    <ReinsuranceItemHeader />
                    <div class="border-2 rounded-t-lg border-slate-100 reinsurance-body">
                        <ReinsuranceItemForm v-for="(reinsuranceItem, reInKey) in reinsurance" v-if="reinsurance"
                            :key="reInKey" :reinsuranceDetail="reinsuranceItem" :isEndorsement="isEndorsement"
                            :readOnly="readOnly" :endorsementNo="endorsementNo" v-model="reinsurance[reInKey]" :rowId="reInKey"
                            :class="{ 'px-4 py-6 reinsurance-row': true }" :defaultPartnerGroups="defaultPartnerGroups" :partnerGroupOptions="partnerGroupOptions" :participantOptions="participantOptions"
                            @getReinsurancePendingDeleteId="getReinsurancePendingDeleteId" @removeReinsuranceDetailByRowId="removeReinsuranceDetailByRowId"
                            :editable="reinsuranceItem.editable" />
                        <div v-if="canAddReinsuranceData" class="grid justify-items-end px-4 py-4 reinsurance-row">
                            <button @click="addReinsurance" type="button" class="px-3 py-1 outline-none focus:ring focus:ring-blue-300 border hover:bg-slate-100 rounded-md font-bold text-blue-600 border-blue-600">Add Share</button>
                        </div>
                    </div>
                    <ReinsuranceItemFooter v-if="total" :total="total" :class="{ 'odd': !reinsurance.length % 2 }" />
                </div>
            </div>

            <div v-if="canUpdateReinsuranceData" class="text-right mt-2 mb-5">
                <button type="button" @click="handleSubmit" :disabled="isDisabledButton"
                    class="btn btn-primary w-24"> <i class="pi pi-spinner pi-spin mr-1" v-if="saving"></i> Save</button>
            </div>
        </div>
    </div>
</template>

<script>

import ReinsuranceItemForm from './ReinsuranceItemForm.vue'
import ReinsuranceItemHeader from './ReinsuranceItemHeader.vue'
import ReinsuranceItemFooter from './ReinsuranceItemFooter.vue'

export default {
    props: {
        id: [Number, String],
        detailId: [Number, String],
        dataId: [Number, String],
        index: Number,
        reinsurance: Array,
        participantOptions: Array,
        canSave: {
            type: Boolean,
            default: false
        },
        isEndorsement: Boolean,
        readOnly: Boolean,
        endorsementNo: String,
        isEndorsementForm: Boolean,
        partnerGroupOptions: Object,
        defaultPartnerGroups: Array
    },

    components: {
        ReinsuranceItemForm,
        ReinsuranceItemHeader,
        ReinsuranceItemFooter,
    },

    data() {
        return {
            formValues: {
                policy_id: this.id,
                product_line_code: 'AUTO',
                detail_id: this.detailId,
                isEndorsement: this.isEndorsement,
                reinsurances: [],
            },
            isUnderLimit: false,
            errors: {},
            total: {},
            deletedReinsuranceIdList: [],
            saving:false
        }
    },

    computed: {
        isDisabledButton() {
            return false
        },
        canAddReinsuranceData() {
            return this.canSave && this.isUnderLimit
        },
        /**
         * If the vehicle is in POLICY, DELETION or CANCEL State, do not allow to edit reinsurance data
         */
        canUpdateReinsuranceData() {
            if (this.isEndorsement) {
                if (this.readOnly)
                    return false
                else {
                    if (this.reinsurance) {
                        return this.reinsurance[0].endorsement_state != 'POLICY' && this.reinsurance[0].endorsement_state != 'DELETION' && this.reinsurance[0].endorsement_state != 'CANCEL' && this.reinsurance[0].endorsement_stage == this.endorsementNo
                    }
                }
            } else {
                if (this.readOnly)
                    return false
                else
                    return true
            }
        },
    },

    methods: {
        addReinsurance(){
            console.log("detail id: "+this.detailId)
            this.$emit('addReinsurance',this.detailId)
        },
        removeReinsuranceDetailByRowId(rowIndex){
            this.$emit('addReinsuranceDetail',this.detailId,rowIndex)
        },
        handleSubmit() {
            this.saving = true
            this.formValues[this.detailId] = this.reinsurance.filter((item) => item.id)
            this.formValues.deletedReinsuranceIdList = this.deletedReinsuranceIdList
            this.formValues.reinsurances = this.reinsurance.filter((item) => !item.id)
            axios.post('/reinsurance-data/stores', this.formValues)
                .then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'success')
                        // Update issue_date
                        this.handleIssueDate()
                    }
                })
                .then(() => this.checkIfShareUnderLimit(this.id, this.detailId))
                .then(() => this.$emit('generateReinsuranceData', this.formValues.policy_id))
                .then(() => {
                    // reset form
                    this.formValues.reinsurances = []
                    this.errors = {}
                })
                .then(() => {
                    this.getSum(this.id, this.detailId)
                })
                .finally(() => {this.$emit('checkPolicyStatus'); this.saving = false})
                .catch(err => {
                    if (err.response.status === 422)
                        this.errors = err.response.data.errors

                    else if (err.response.status === 400)
                        notify(err.response?.data?.message, 'error','bottom-right')

                    else notify('Something went wrong', 'error')
                })
        },

        checkIfShareUnderLimit(policyId, detailId) {
            axios.get(`/policy-service/check-if-share-under-limit/${policyId}/${detailId}`).then(response => {
                this.isUnderLimit = response.data.isUnderLimit
            })
        },

        getSum(policyId, detailId) {
            if (this.isEndorsement)
                axios.get(`/reinsurance-data/${policyId}/get-sum/${detailId}/${this.endorsementNo}`).then(response => this.total = response.data)
            else
                axios.get(`/reinsurance-data/${policyId}/get-sum/${detailId}`).then(response => this.total = response.data)
        },

        handleIssueDate() {
            axios.put('/autos/update-issue-date/' + this.dataId).catch((e) => {
                console.log(e)
            })
        },

        getReinsurancePendingDeleteId(reinsuranceId) {
            this.deletedReinsuranceIdList.push(reinsuranceId)
        },

        toggle(index) {
            this.$emit('toggle', index)
        },

        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        },

        vehicleStatusTag() {
            if (this.reinsurance[0]?.endorsement_state === 'DELETION')
                return `
                    <span class="text-xs px-1 rounded-full bg-theme-6 text-white mr-1 px-2 py-1">Deleted</span>
                    `;
            else if (this.reinsurance[0]?.endorsement_state === 'CANCEL')
                return `
                    <span class="text-xs px-1 rounded-full bg-theme-6 text-white mr-1 px-2 py-1">Cancelled</span>
                `;
            else if (this.reinsurance[0]?.endorsement_state === 'ADDITION') {
                return `
                    <span class="text-xs px-1 rounded-full bg-theme-9 text-white mr-1 px-2 py-1">New</span>
                `;
            }
        },
    },

    async mounted() {
        // Do not collapse the first vehicle
        if (this.index == 0)
            this.$emit('collapse', this.index)
        this.checkIfShareUnderLimit(this.id, this.detailId)

        this.getSum(this.id, this.detailId)

        await this.$nextTick()
        this.formValues.reinsurances = []
    },
}
</script>

<style scoped>
.collapse {
    @apply hidden
}

.rotate {
    @apply rotate-180;
}

.icon-color {
    @apply text-red-600;
}

button:disabled {
    @apply opacity-60;
}

.reinsurance-body .reinsurance-row:nth-child(odd) {
    @apply bg-slate-100;
}
</style>
