<template>
    <div>
        <LoadingIndicator v-if="isLoading" />
        <div v-else class="grid grid-cols-2 sm:grid-cols-1 gap-10">
            <div>
                <div>
                    <label for="" class="form-label">Endorsement Description</label>
                    <Textarea v-model="formValues.endorsement_description" class="w-full" rows="4"
                        placeholder="Endorsement Description" />
                </div>
                <div v-for="(delVehicle, index) in formValues.vehicles">
                    <div class="flex mr-1">
                        <span>
                            <svg class="w-6 h-6 text-green-500 rotate icon-color" :class="`cancellation-icon-${index}`"
                                fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <p class="text-base cursor-pointer w-full font-bold" @click="toggle(index)">Vehicle #{{ index +
                            1 }}</p>
                    </div>
                    <CancellationItem v-if="vehicle(index)" :vehicle="vehicle(index)" :index="index"
                        v-model="formValues.vehicles[index]" :refundTypeOptions="refundTypeOptions" :today="today"
                        @collapse='collapse' />
                </div>
                <div class="text-right mt-8">
                    <router-link :to="{ name: 'EndorsementIndex' }" class="btn btn-outline-secondary w-24 mr-1"
                        tag="button">Cancel</router-link>
                    <button type="button" @click="handleSubmit" class="btn btn-primary w-24">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import CancellationItem from './CancellationItem.vue'
import CancellationAllVehicles from './CancellationAllVehicles.vue'
import LoadingIndicator from '../../../../components/LoadingIndicator.vue'
import moment from 'moment';

export default {
    props: {
        id: [Number, String],
        dataId: [Number, String],
    },

    components: {
        CancellationItem,
        LoadingIndicator,
        CancellationAllVehicles
    },

    data() {
        return {
            formValues: {
                vehicles: [],
            },
            refundTypeOptions: [],
            refundTypeForAllVehiclesOptions: [],
            isLoading: false,
        }
    },

    computed: {
        today() {
            return new Date()
        }
    },

    methods: {
        vehicle(index) {
            return this.formValues.vehicles?.[index]
        },
        handleSubmit() {
            axios.put('/autos/save-cancel-policy-endorsement/' + this.id, this.formValues).then(response => {
                if (response.data.success) {
                    notify(response.data.message, 'success')
                    // Update issue_date
                    this.handleIssueDate()
                    this.$emit('updateSubmitStatus', 'SBM')
                }
            }).catch(() => notify('Error', 'error'))
        },

        listCancelVehicles(id) {
            this.isLoading = true
            axios.get('/api/endorsements/list-cancel-vehicles/' + id).then(response => {
                this.isLoading = false
                this.formValues = response.data
                this.formValues.vehicles = this.formValues.vehicles.map((item) => {
                    item.endorsement_e_date = item.endorsement_e_date ? moment(item.endorsement_e_date).toDate() : ''
                    return item
                })
            })
        },

        listRefundTypeOptions() {
            axios.get('/endorsement-service/list-refund-type-options').then(response => {
                this.refundTypeOptions = response.data
                this.refundTypeForAllVehiclesOptions = this.refundTypeOptions.filter((item) => item.value !== 'CUSTOM')
            })
        },

        handleIssueDate() {
            axios.put('/autos/update-issue-date/' + this.dataId).catch((e) => {
                console.log(e)
            })
        },

        cancelAllVehicles() {
            var cancelledAllVehicles = {
                refund_option: this.formValues.refund_option_for_all,
                endorsement_e_date: this.formValues.endorsement_e_date_for_all,
                endorsement_description: this.formValues.endorsement_description,
            }

            axios.put('/autos/save-cancel-policy-endorsement/all-vehicles/' + this.id, cancelledAllVehicles).then(response => {
                if (response.data.success) {
                    notify(response.data.message, 'success')
                    // Update issue_date
                    this.handleIssueDate()
                    this.$emit('updateSubmitStatus', 'SBM')
                }
            }).catch(() => notify('Error', 'error'))
        },

        toggle(vehicleNo) {
            for (let index = 0; index < this.formValues.vehicles?.length; index++) {
                var header = document.querySelector('.show-cancellation-' + index)
                var icon = document.querySelector('.cancellation-icon-' + index)
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
            var classId = document.querySelector('.show-cancellation-' + vehicleNo)
            var iconId = document.querySelector('.cancellation-icon-' + vehicleNo)
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
        if (this.index == 0)
            this.$emit('collapse', this.index)

        this.listCancelVehicles(this.id)
        this.listRefundTypeOptions()
    }
}
</script>

<style scoped>
.collapse {
    display: none;
}

.rotate {
    transform: rotate(180deg);
}

.icon-color {
    color: red;
}
</style>
