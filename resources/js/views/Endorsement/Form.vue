<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                Endorsement : {{ formValues.document_no }}
            </h2>
            <button v-if='hasSubmitBtn' class="btn btn-primary shadow-md mr-2 leading-6" :class="{'opacity-50 cursor-not-allowed' : isDisabledSubmitButton}" :disabled="isDisabledSubmitButton" @click="updateSubmitStatus('SBM')">Submit</button>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12">
                <div class="flex items-center px-5 pt-3 pb-0 border-b border-gray-200">
                    <div class="nav nav-tabs flex-col sm:flex-row justify-center lg:justify-start" role="tablist">
                        <template v-for="(tab, index) in tabs" :key="index">
                            <a
                                :id="tab.id"
                                data-toggle="tab"
                                :data-target="tab.target"
                                :href="tab.href"
                                :class="tab.classes"
                                role="tab"
                                @click="changeTab($event, tab)"
                            >
                                {{ tab.title }}
                            </a>
                        </template>
                        <a v-if="hasPolicyCancellation" id="cancellation-tab" data-toggle="tab" data-target="#cancellation" href="#cancellation" class="py-3 sm:mr-8" role="tab" aria-selected="false">Policy Cancellation</a>
                    </div>
                </div>
                <div class="p-5">
                    <div class="tab-content">
                        <div id="info" class="tab-pane active" role="tabpanel" aria-labelledby="info-tab">
                            <div v-if="isAutoProduct">
                                <AutoTab
                                    v-if="dataId"
                                    :id="dataId"
                                    :isEndorsement="isEndorsement"
                                    :endorsementType="endorsementType"
                                    cancelRoute="EndorsementIndex"
                                    :isEndorsementForm="true"
                                    :documentNo="formValues.document_no" />
                            </div>
                            <div v-else>
                                <img src="/images/loading.gif" class="mx-auto" alt="">
                            </div>
                        </div>
                        <div id="vehicle-info" class="tab-pane" role="tabpanel" aria-labelledby="vehicle-info-tab">
                            <VehiclesTab
                                v-if="dataId && isShownVehicleTab"
                                :masterDataId="dataId"
                                :totalPremium="totalPremium"
                                :isQuotation="!isEndorsement"
                                :isEndorsement="isEndorsement"
                                :documentNo="formValues.document_no"
                                @vehicleListUpdated="vehicleListUpdated"
                                :endorsementType="endorsementType"/>
                        </div>
                        <div id="deductible" class="tab-pane" role="tabpanel" aria-labelledby="deductible-tab">
                            <DeductibleTab
                                v-if="dataId && isShownDeductibleTab"
                                :id="dataId"
                                cancelRoute="EndorsementIndex"
                                :key="deductibleTabKey"
                                :endorsementType="endorsementType" />
                        </div>
                        <div id="config" class="tab-pane" role="tabpanel" aria-labelledby="config-tab">
                            <ConfigTab
                                v-if="id && isShownConfigTab"
                                :id="id"
                                :isEndorsement="isEndorsement"
                                :endorsementType="endorsementType" />
                        </div>
                        <div id="commission" class="tab-pane" role="tabpanel" aria-labelledby="commission-tab">
                            <CommissionTab
                                v-if="id && isShownCommissionTab"
                                :id="id"
                                :key="commissionTabKey"
                                :dataId="dataId" />
                        </div>
                        <div id="reinsurance" class="tab-pane" role="tabpanel" aria-labelledby="reinsurance-tab">
                            <ReinsuranceTab
                                v-if="id && isShownReinsuranceTab"
                                :id="id"
                                :dataId="dataId"
                                :isEndorsement="isEndorsement"
                                :endorsementType="endorsementType"
                                :endorsementNo="formValues.document_no"
                                :isEndorsementForm="true"
                                :productCode="formValues.product_code"
                                :key="reinsuranceTabKey"
                                @checkPolicyStatus='isEndorsementReinsuranceCompleted'/>
                        </div>
                        <div v-if="hasPolicyCancellation" id="cancellation" class="tab-pane" role="tabpanel" aria-labelledby="cancellation-tab">
                            <CancellationTab
                                v-if="id"
                                :id="id"
                                :dataId="dataId"
                                @updateSubmitStatus='updateSubmitStatus'/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import AutoTab from '../Policy/FormTabs/Info/Auto/Form.vue'
import VehiclesTab from '@/components/Auto/Vehicles/VehiclesList.vue';
import DeductibleTab from '../Policy/FormTabs/Deductible/Deductibles.vue'
import ConfigTab from '../Policy/FormTabs/Config.vue'
import CommissionTab from './FormTabs/Commission/Commission.vue'
import ReinsuranceTab from '../Policy/FormTabs/Reinsurance/Reinsurance.vue'
import CancellationTab from './FormTabs/Cancellation/Cancellation.vue'

export default {
    components: {
        AutoTab,
        VehiclesTab,
        DeductibleTab,
        ConfigTab,
        CommissionTab,
        ReinsuranceTab,
        CancellationTab,
    },

    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {},
            isEndorsement: true,
            hasPolicyCancellation: false,
            isDisabledSubmitButton: true,
            isReinsuranceCompleted: false,
            hasSubmitBtn: false,
            totalPremium: null,
            // To handle deductible, reinsurance and commission tab after adding/editing/deleting vehicles
            reloadCompKey: 0,
            tabs: [
                {
                    id: 'info-tab',
                    title: 'Endorsement Information',
                    target: '#info',
                    classes: 'py-3 sm:mr-8 active',
                    href: 'javascript:;',
                },
                {
                    id: 'vehicle-info-tab',
                    title: 'Vehicles Information',
                    target: '#vehicle-info',
                    classes: 'py-3 sm:mr-8',
                    href: '#vehicle-info',
                },
                {
                    id: 'deductible-tab',
                    title: 'Deductibles',
                    target: '#deductible',
                    classes: 'py-3 sm:mr-8',
                    href: '#deductible',
                },
                {
                    id: 'config-tab',
                    title: 'Policy Configuration',
                    target: '#config',
                    classes: 'py-3 sm:mr-8',
                    href: '#config',
                },
                {
                    id: 'commission-tab',
                    title: 'Commission',
                    target: '#commission',
                    classes: 'py-3 sm:mr-8',
                    href: '#commission',
                },
                {
                    id: 'reinsurance-tab',
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
        }
    },

    computed: {
        isAutoProduct() {
            return this.formValues.product_line_code === 'AUTO'
        },
        dataId() {
            return this.formValues.data_id
        },
        endorsementType() {
            return this.formValues.auto?.endorsement_type
        },
        isGeneralEndorsement() {
            return this.endorsementType === 'GENERAL'
        },
    },

    methods: {
        getEndorsement() {
            if (this.id) {
                axios.get(`/api/endorsements/${this.id}`).then(response => {
                    this.formValues = response.data
                })
                .then(() => {
                    // For general endorsement, let's enable disabled btn and there is no need to check reinsurance
                    if(this.isGeneralEndorsement)
                        this.isDisabledSubmitButton = false
                })
                .then(() => this.showPolicyCancellationTab())
                .finally(() => this.calculateEndorsementPremium(this.formValues.document_no))
            }
        },

        calculateEndorsementPremium(documentNo) {
            axios.get(`/api/endorsements/get-premium/${this.id}/${documentNo}`).then(response => {
                this.totalPremium = response.data
            })
        },

        showPolicyCancellationTab() {
            axios.get(`/endorsement-service/show-policy-cancellation-tab/${this.id}`).then(response => {
                this.hasPolicyCancellation = response.data
                // Policy Cancellation does not require Submit btn
                this.hasSubmitBtn = !this.hasPolicyCancellation
            })
            .then(() => {
                this.defaultTab()
            })
        },

        isEndorsementReinsuranceCompleted() {
            if (this.id)
                axios.get(`/endorsement-service/is-endorsement-reinsurance-completed/${this.id}`).then(response => {
                    this.isReinsuranceCompleted = response.data;
                    if(this.isReinsuranceCompleted){
                        this.isDisabledSubmitButton = false
                    } else {
                        this.isDisabledSubmitButton = true
                        this.updateSubmitStatus('PRG')
                    }
                })
        },

        updateSubmitStatus(status){
            axios.put(`/endorsement-service/update-submit-status/${this.id}`, {status: status}).then(response => {
                if(response.data)
                    this.toastMessage(response.data.message, 'Success')
                if(status == 'SBM')
                    this.$router.push({name: 'EndorsementIndex'})
            }).catch(err => {
                console.log(err)
            })
        },

        vehicleListUpdated(){
            this.setRequireDeductibleTabRenderingStatus(true)
            this.setRequireCommissionTabRenderingStatus(true)
            this.setRequireReinsuranceTabRenderingStatus(true)
            this.isEndorsementReinsuranceCompleted()
        },

        setRequireDeductibleTabRenderingStatus(status){
            this.requireDeductibleTabRendering = status
        },

        setRequireCommissionTabRenderingStatus(status){
            this.requireCommissionTabRendering = status
        },

        setRequireReinsuranceTabRenderingStatus(status){
            this.requireReinsuranceTabRendering = status
        },

        defaultTab() {
            if (this.hasPolicyCancellation && this.endorsementType == 'CANCELLATION') {
                // Remove active class from info tab
                document.querySelector('#info-tab').classList.remove('active')
                document.querySelector('#info').classList.remove('active')

                document.querySelector('#cancellation-tab').classList.add('active')
                document.querySelector('#cancellation').classList.add('active')
            }
        },

        changeTab(_, tab) {
            if (tab.target === '#vehicle-info') {
                this.isShownVehicleTab = true
            } else if (tab.target === '#deductible') {
                this.isShownDeductibleTab = true
                // If the vehicle list is updated, trigger the deductible tab to re-render
                if(this.requireDeductibleTabRendering){
                    this.deductibleTabKey += 1;
                    // After re-rendering the deductible tab set requireDeductibleTabRendering to false
                    this.setRequireDeductibleTabRenderingStatus(false)
                }
            } else if (tab.target === '#config') {
                this.isShownConfigTab = true
            } else if (tab.target === '#commission') {
                this.isShownCommissionTab = true
                // If the vehicle list is updated, trigger the commission tab to re-render
                if(this.requireCommissionTabRendering){
                    this.commissionTabKey += 1;
                    // After re-rendering the deductible tab set requireCommissionTabRendering to false
                    this.setRequireCommissionTabRenderingStatus(false)
                }
            } else if (tab.target === '#reinsurance') {
                this.isShownReinsuranceTab = true
                // If the vehicle list is updated, trigger the reinsurance tab to re-render
                if(this.requireReinsuranceTabRendering){
                    this.reinsuranceTabKey += 1;
                    // After re-rendering the deductible tab set requireReinsuranceTabRendering to false
                    this.setRequireReinsuranceTabRenderingStatus(false)
                }
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
        this.getEndorsement()
        this.isEndorsementReinsuranceCompleted()
    },
}
</script>
