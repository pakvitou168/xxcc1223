<template>
    <Dialog header="Delete Vehicle" class="custom-dialog-top" :style="{ width: '340px' }" position="center"
        :visible="isVisible" :modal="true" :closable="false" :draggable="false">
        <div class="grid grid-cols-1">
            <label class="mb-2">Endorsement Effective Date <span class="text-red-500">*</span></label>
            <Calendar type="date" showIcon v-model="endorsementEffectiveDate" dateFormat="dd-M-yy" />
        </div>

        <template #footer>
            <Button label="Cancel" type="button" class="p-button-secondary p-button-text" @click="hideDialog" />
            <Button label="Delete" type="button" class="p-button-danger" :loading="saving" outlined autofocus
                @click="handleDelete" />
        </template>
    </Dialog>
</template>
<script>
import moment from 'moment';

export default {
    props: {
        isVisible: Boolean,
        submitted: Boolean,
        defaultEndorsementEffectiveDate: String,
        selectedRowsIds: Array,
        documentNo: String
    },
    data() {
        return {
            policyId: this.$route.params.id,
            endorsementEffectiveDate: moment(this.defaultEndorsementEffectiveDate).toDate(),
            isDisabledSubmitButton: false,
            saving: false
        }
    },
    methods: {
        handleDelete() {
            this.isDisabledSubmitButton = true
            this.saving = true
            axios.put(`/auto-details/delete-vehicle-endorsement/${this.selectedRowsIds[0]}`, {
                policy_id: this.policyId,
                endorsement_e_date: this.endorsementEffectiveDate,
            }).then(response => {
                if (response.data.success) {
                    this.toastMessage(response.data.message, 'Success')
                }
            }).catch(err => {
                if (err.response?.status === 499) {
                    this.toastMessage(err.response.data.message, 'Error')
                } else {
                    this.toastMessage('Error', 'Error')
                }
            })
                .finally(() => {
                    this.isDisabledSubmitButton = false
                    this.saving = false
                    this.hideDialog()
                    this.$emit('startCalculatingPremium')
                    this.calculateEndorsementPremium()
                })
        },
        hideDialog() {
            this.$emit('hideDialog')

            this.endorsementEffectiveDate = this.defaultEndorsementEffectiveDate
        },
        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        },
        calculateEndorsementPremium() {
            axios.get(`/api/endorsements/get-premium/${this.policyId}/${this.documentNo}`).then(response => {
                this.$emit('updateTotalPremium', response.data)
                this.$emit('generateCommissionData')
                this.$emit('generateReinsurance')
                this.$emit('vehicleListUpdated')
                this.$emit('finishCalculatingPremium')
            })
        },
    },
}
</script>
