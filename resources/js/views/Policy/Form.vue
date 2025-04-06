<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="isAutoProduct">Auto</span>
                Policy : {{ formValues.document_no }}
            </h2>
            <button class="btn btn-success shadow-md mr-2" title="Export Reinsurance" @click="exportReinsuranceExcel">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </button>
            <button class="btn btn-primary shadow-md mr-2 leading-6"
                :class="{ 'opacity-50 cursor-not-allowed': isDisabledSubmitButton }" :disabled="isDisabledSubmitButton"
                @click="updateSubmitStatus('SBM')">Submit</button>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12">
                <div class="flex items-center px-5 pt-3 pb-0 border-b border-gray-200">
                    <div class="nav nav-tabs flex-col sm:flex-row justify-center lg:justify-start" role="tablist">
                        <template v-for="(tab, index) in tabs" :key="index">
                            <a data-toggle="tab" :data-target="tab.target" :href="tab.href" :class="tab.classes"
                                role="tab" @click="changeTab($event, tab)">
                                {{ tab.title }}
                            </a>
                        </template>
                    </div>
                </div>
                <div class="p-5">
                    <div class="tab-content">
                        <div id="info" class="tab-pane active" role="tabpanel" aria-labelledby="info-tab">
                            <div v-if="isAutoProduct">
                                <AutoTab :id="dataId" cancelRoute="PolicyIndex"
                                    @updateRequireTotalPremiumState="updateRequireTotalPremiumState"
                                    @updateBusinessChannel="updateBusinessChannel" />
                            </div>
                            <div v-else>
                                <img src="/images/loading.gif" class="mx-auto" alt="">
                            </div>
                        </div>
                        <div id="vehicle-info" class="tab-pane" role="tabpanel" aria-labelledby="vehicle-info-tab">
                            <VehiclesTab v-if="dataId && isShownVehicleTab" :masterDataId="dataId"
                                :totalPremium="totalPremium" :isQuotation="false" :isPolicy="true"
                                :requireUpdateTotalPremium="requireUpdateTotalPremium"
                                @vehicleListUpdated="vehicleListUpdated"
                                @updateRequireTotalPremiumState="updateRequireTotalPremiumState" />
                        </div>
                        <div id="deductible" class="tab-pane" role="tabpanel" aria-labelledby="deductible-tab">
                            <DeductibleTab v-if="dataId && isShownDeductibleTab" :id="dataId" :key="deductibleTabKey"
                                cancelRoute="PolicyIndex" />
                        </div>
                        <div id="config" class="tab-pane" role="tabpanel" aria-labelledby="config-tab">
                            <ConfigTab v-if="isShownConfigTab" :id="id" :dataId="dataId"
                                @checkPolicyStatus='isPolicyConfigurationCompleted' />
                        </div>
                        <div id="commission" class="tab-pane" role="tabpanel" aria-labelledby="commission-tab">
                            <CommissionTab v-if="id && isShownCommissionTab" :id="id" :key="commissionTabKey"
                                :dataId="dataId" />
                        </div>
                        <div id="reinsurance" class="tab-pane" role="tabpanel" aria-labelledby="reinsurance-tab">
                            <ReinsuranceTab v-if="id && isShownReinsuranceTab" :id="id" :dataId="dataId"
                                :productCode="formValues.product_code" :key="reinsuranceTabKey"
                                @checkPolicyStatus='isPolicyReinsuranceCompleted' />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import AutoTab from './FormTabs/Info/Auto/Form.vue'
import DeductibleTab from './FormTabs/Deductible/Deductibles.vue'
import ConfigTab from './FormTabs/Config.vue'
import CommissionTab from './FormTabs/Commission/Commission.vue'
import ReinsuranceTab from './FormTabs/Reinsurance/Reinsurance.vue'
import VehiclesTab from '@/components/Auto/Vehicles/VehiclesList.vue'

export default {
    components: {
        AutoTab,
        VehiclesTab,
        DeductibleTab,
        ConfigTab,
        CommissionTab,
        ReinsuranceTab,
    },

    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {},
            isReinsuranceCompleted: false,
            isConfigCompleted: false,
            isDisabledSubmitButton: true,
            totalPremium: null,

            tabs: [
                {
                    title: 'Policy Information',
                    target: '#info',
                    classes: 'py-3 sm:mr-8 active',
                    href: 'javascript:;',
                },
                {
                    title: 'Vehicles Information',
                    target: '#vehicle-info',
                    classes: 'py-3 sm:mr-8',
                    href: '#vehicle-info',
                },
                {
                    title: 'Deductibles',
                    target: '#deductible',
                    classes: 'py-3 sm:mr-8',
                    href: '#deductible',
                },
                {
                    title: 'Policy Configuration',
                    target: '#config',
                    classes: 'py-3 sm:mr-8',
                    href: '#config',
                },
                {
                    title: 'Commission',
                    target: '#commission',
                    classes: 'py-3 sm:mr-8',
                    href: '#commission',
                },
                {
                    title: 'Reinsurance',
                    target: '#reinsurance',
                    classes: 'py-3 sm:mr-8',
                    href: '#reinsurance',
                },
            ],

            isShownVehicleTab: false,
            isShownDeductibleTab: false,
            isShownConfigTab: false,
            isShownCommissionTab: false,
            isShownReinsuranceTab: false,

            // To handle deductible, commission and reinsurance tabs after adding/editing/deleting vehicles
            deductibleTabKey: 0,
            commissionTabKey: 0,
            reinsuranceTabKey: 0,
            requireDeductibleTabRendering: false,
            requireCommissionTabRendering: false,
            requireReinsuranceTabRendering: false,

            requireUpdateTotalPremium: false,
        }
    },

    computed: {
        isAutoProduct() {
            return this.formValues.product_line_code === 'AUTO'
        },
        dataId() {
            return this.formValues.data_id
        },
    },

    methods: {
        getPolicy() {
            if (this.id) {
                axios.get(`/api/policies/${this.id}`).then(response => {
                    this.formValues = response.data
                    if (this.formValues.status !== 'APV' && this.formValues.approved_status !== 'SBM') {
                        this.isPolicyReinsuranceCompleted()
                        this.isPolicyConfigurationCompleted()
                    } else if (this.formValues.status === 'APV' && this.formValues.approved_status === 'SBM') {
                        notify("Policy status changed, you cannot access this page", "warn")
                        this.$router.push({
                            name: 'PolicyIndex'
                        })
                    }
                }).finally(() => {
                    this.getTotalPremium()
                })
            }
        },

        getTotalPremium() {
            axios.get(`/auto-service/get-total-premium/${this.formValues.data_id}`).then(response => this.totalPremium = response.data)
        },

        exportReinsuranceExcel() {
            location.href = '/policy-service/export-reinsurance/' + this.id + '/' + this.formValues.product_code;
        },

        isPolicyReinsuranceCompleted() {
            if (this.id)
                axios.get(`/policy-service/is-policy-reinsurance-completed/${this.id}`).then(response => {
                    this.isReinsuranceCompleted = response.data;
                    // Check if Policy Configuration is completed
                    if (this.isReinsuranceCompleted) {
                        axios.get(`/policy-service/is-policy-configuration-completed/${this.id}`).then(response => {
                            this.isConfigCompleted = response.data;
                            // If both Policy Configuration & Policy Reinsurance are completed, allow changing status to submitted
                            if (this.isConfigCompleted)
                                this.isDisabledSubmitButton = false
                            else {
                                this.isDisabledSubmitButton = true
                                this.updateSubmitStatus('PRG')
                            }
                        })
                    } else {
                        this.isDisabledSubmitButton = true
                        this.updateSubmitStatus('PRG')
                    }
                })
        },

        isPolicyConfigurationCompleted() {
            if (this.id)
                axios.get(`/policy-service/is-policy-configuration-completed/${this.id}`).then(response => {
                    this.isConfigCompleted = response.data;
                    // Check if Policy Reinsurance is completed
                    if (this.isConfigCompleted) {
                        axios.get(`/policy-service/is-policy-reinsurance-completed/${this.id}`).then(response => {
                            this.isReinsuranceCompleted = response.data;
                            // If both Policy Configuration & Policy Reinsurance are completed, allow changing status to submitted
                            if (this.isReinsuranceCompleted)
                                this.isDisabledSubmitButton = false
                            else {
                                this.isDisabledSubmitButton = true
                                this.updateSubmitStatus('PRG')
                            }
                        })
                    } else {
                        this.isDisabledSubmitButton = true
                        this.updateSubmitStatus('PRG')
                    }
                })
        },

        updateSubmitStatus(status) {
            axios.put(`/policy-service/update-submit-status/${this.id}`, { status: status }).then(response => {
                if (response.data)
                    this.toastMessage(response.data.message, 'Success')
                if (status == 'SBM')
                    this.$router.push({ name: 'PolicyIndex' })
            }).catch(err => {
                console.log(err)
            })
        },

        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        },

        updateRequireTotalPremiumState(isRequired) {
            this.requireUpdateTotalPremium = isRequired
        },

        vehicleListUpdated() {
            this.setRequireDeductibleTabRenderingStatus(true)
            this.setRequireCommissionTabRenderingStatus(true)
            this.setRequireReinsuranceTabRenderingStatus(true)
            this.isPolicyReinsuranceCompleted()
        },

        updateBusinessChannel() {
            this.setRequireCommissionTabRenderingStatus(true)
        },

        setRequireDeductibleTabRenderingStatus(status) {
            this.requireDeductibleTabRendering = status
        },

        setRequireCommissionTabRenderingStatus(status) {
            this.requireCommissionTabRendering = status
        },

        setRequireReinsuranceTabRenderingStatus(status) {
            this.requireReinsuranceTabRendering = status
        },

        changeTab(_, tab) {
            if (tab.target === '#vehicle-info') {
                this.isShownVehicleTab = true
            } else if (tab.target === '#deductible') {
                this.isShownDeductibleTab = true
                // If the vehicle list is updated, trigger the deductible tab to re-render
                if (this.requireDeductibleTabRendering) {
                    this.deductibleTabKey += 1;
                    // After re-rendering the deductible tab set requireDeductibleTabRendering to false
                    this.setRequireDeductibleTabRenderingStatus(false)
                }
            } else if (tab.target === '#config') {
                this.isShownConfigTab = true
            } else if (tab.target === '#commission') {
                this.isShownCommissionTab = true
                // If the vehicle list is updated, trigger the commission tab to re-render
                if (this.requireCommissionTabRendering) {
                    this.commissionTabKey += 1;
                    // After re-rendering the deductible tab set requireCommissionTabRendering to false
                    this.setRequireCommissionTabRenderingStatus(false)
                }
            } else if (tab.target === '#reinsurance') {
                this.isShownReinsuranceTab = true
                // If the vehicle list is updated, trigger the reinsurance tab to re-render
                if (this.requireReinsuranceTabRendering) {
                    this.reinsuranceTabKey += 1;
                    // After re-rendering the deductible tab set requireReinsuranceTabRendering to false
                    this.setRequireReinsuranceTabRenderingStatus(false)
                }
            }
        },
    },

    mounted() {
        this.getPolicy()
    },
}
</script>
