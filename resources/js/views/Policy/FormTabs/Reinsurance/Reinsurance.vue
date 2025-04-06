<template>
    <div>
        <LoadingIndicator v-if="isLoading" />
        <div v-else v-for="(reinsurance, detailId, index) in formValues" :key="detailId" class="pt-5 pb-2 first:pt-0">
            <ReinsuranceItem v-if="id" :id="id" :detailId="detailId" :dataId="dataId" :index="index"
                :reinsurance="reinsurance" @generateReinsuranceData="generateReinsuranceData" @toggle='toggle'
                @collapse='collapse' @addReinsurance="addReinsurance" @addReinsuranceDetail="addReinsuranceDetail"
                @checkPolicyStatus="$emit('checkPolicyStatus')" :participantOptions="participantOptions"
                :partnerGroupOptions="partnerGroupOptions" :canSave="canSave" :isEndorsement="isEndorsement"
                :endorsementNo="endorsementNo" :isEndorsementForm="isEndorsementForm" :readOnly="false"
                :defaultPartnerGroups="defaultPartnerGroups" />
        </div>
    </div>
</template>

<script>

import ReinsuranceItem from './ReinsuranceItem.vue'
import LoadingIndicator from '../../../../components/LoadingIndicator.vue'
export default {
    props: {
        id: [Number, String],
        dataId: [Number, String],
        isEndorsement: Boolean,
        endorsementType: String,
        endorsementNo: String,
        isEndorsementForm: Boolean,
        productCode: String,
    },

    components: {
        ReinsuranceItem,
        LoadingIndicator,
    },

    data() {
        return {
            formValues: [],
            participantOptions: [],
            isLoading: false,
            partnerGroupOptions: [],
            defaultPartnerGroups: []
        }
    },

    computed: {
        vehicleEndorsement() {
            return this.endorsementType === 'VEHICLE'
        },
        canSave() {
            // if not an endorsement
            if (!this.isEndorsement) return true
            // if endorsement type is VEHICLE
            return this.vehicleEndorsement
        }
    },

    methods: {
        generateReinsuranceShare(policyId) {
            if (this.isEndorsement) {
                axios.get(`/endorsement-service/generate-reinsurance-share/${policyId}`)
                    .then(response => {
                        if (response.data.success)
                            this.generateReinsuranceData(policyId)
                    }
                    )
            } else {
                axios.get(`/policy-service/generate-reinsurance-share/${policyId}`)
                    .then(response => {
                        if (response.data.success)
                            this.generateReinsuranceData(policyId)
                    }
                    )
            }
        },

        generateReinsuranceData(policyId) {
            if (this.isEndorsement) {
                axios.get(`/endorsement-service/generate-reinsurance-data/${policyId}`)
                    .then(() => this.getReinsuranceData())
            } else {
                axios.get(`/policy-service/generate-reinsurance-data/${policyId}`)
                    .then(() => this.getReinsuranceData())
            }
        },

        getReinsuranceData() {
            if (this.id) {
                this.isLoading = true
                if (this.isEndorsement) {
                    axios.get(`/api/endorsements/get-reinsurance-data/${this.id}`)
                        .then(response => this.formValues = response.data)
                        .then(() => {
                            // If have not yet generated share
                            if (this.formValues.length === 0)
                                this.generateReinsuranceShare(this.id)
                        }).finally(() => {
                            this.isLoading = false
                            this.$emit('checkPolicyStatus')
                        })
                } else {
                    axios.get(`/policy-service/get-reinsurance-data/${this.id}`)
                        .then(response => this.formValues = response.data)
                        .then(() => {
                            // If have not yet generated share
                            if (this.formValues.length === 0)
                                this.generateReinsuranceShare(this.id)
                        }).finally(() => {
                            this.isLoading = false
                            this.$emit('checkPolicyStatus')
                        })
                }
            }
        },

        listParticipants() {
            axios.get('/policy-service/list-treaty-codes').then(response => {
                this.participantOptions = response.data
            })
        },

        listPartnerGroups() {
            axios.get('/policy-service/list-partner-groups').then(response => {
                this.partnerGroupOptions = response.data
            })
        },

        listDefaultPartnerGroups() {
            axios.get(`/reinsurance-config/get-default-reinsurance-config/${this.productCode}`).then(response => {
                this.defaultPartnerGroups = response.data
            })
        },

        hasEndorsedVehicles() {
            /**
             * Since deleted vehicles do not have reinsurance data, we check only newly added vehicles
             */
            return axios.get(`/endorsement-service/has-endorsement-vehicles/${this.id}`)
        },

        toggle(vehicleNo) {
            for (let index = 0; index < _.size(this.formValues); index++) {
                var header = document.querySelector('.show-reinsurance-' + index)
                var icon = document.querySelector('.reinsurance-icon-' + index)
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
            var classId = document.querySelector('.show-reinsurance-' + vehicleNo)
            var iconId = document.querySelector('.reinsurance-icon-' + vehicleNo)
            classId.classList.remove('collapse')
            iconId.classList.remove('rotate')
            iconId.classList.remove('icon-color')
        },
        addReinsurance(detailId) {
            let items = this.formValues[detailId]
            if (items) {
                items.push({ editable: true })
            }
            this.formValues[detailId] = items
        },
        addReinsuranceDetail(detailId, rowIndex) {
            let items = this.formValues[detailId]
            items = items.filter((item, key) => key != rowIndex)
            this.formValues[detailId] = items
        }
    },

    mounted() {
        if (this.isEndorsement)
            this.hasEndorsedVehicles().then((response) => {
                if (response.data)
                    this.getReinsuranceData()
            });
        else
            this.getReinsuranceData()
        this.listParticipants()
        this.listPartnerGroups()
        this.listDefaultPartnerGroups()
    },
}
</script>
