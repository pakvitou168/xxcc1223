<template>
    <div>
        <div class="grid lg:grid-cols-2 sm:grid-cols-1 gap-x-10 gap-y-5">
            <div class="formulate-input">
                <label class="formulate-input-label">Product Type *</label>
                <Dropdown class="w-full p-inputtext-sm" v-model="formValues.product_code" :options="productOptions"
                    optionLabel="label" optionValue="value" placeholder="Product" :disabled="hasSubmitted">
                    <template #option="slotProps">
                        <p class="text-sm font-semibold">{{ slotProps.option.label }}</p>
                        <span class="text-xs">{{ slotProps.option.desc }}</span>
                    </template>
                </Dropdown>
                <span class="text-red text-xs text-red-700 text-error" v-if="errors['product_code']">{{
                    errors['product_code'][0]
                }}</span>
            </div>
            <div class="clear-fix"></div>
            <div>
                <label for="" class="block mb-1 font-bold">Calculate Options *</label>
                <Dropdown v-model="formValues.calc_option" :options="calculateOptions" optionLabel="label"
                    optionValue="value" class="w-full" :disabled="isEndorsement"
                    placeholder="Select calculation option" />
                <span class="text-red text-xs text-red-700 text-error" v-if="errors['calc_option']">{{
                    errors['calc_option'][0] }}</span>
            </div>
            <div v-if="formValues.calc_option === 'SPECIAL'">
                <label for="" class="block mb-1 font-bold">Negotiation Rate</label>
                <InputNumber v-model="negotiation_rate" placeholder="Negotiation rate" class="w-full" :min="0"
                    :max="100" inputId="minmaxfraction" :minFractionDigits="0" :maxFractionDigits="2" />
                <span class="text-red text-xs text-red-700 text-error" v-if="errors['negotiation_rate']">{{
                    errors['negotiation_rate'][0] }}</span>
            </div>
            <div class="clear-fix" v-else></div>
            <div>
                <label for="" class="block mb-1 font-bold">Customer Type *</label>
                <Dropdown v-model="formValues.customer_type" @change="renderListCustomers($event.value)"
                    :options="customerTypes" optionLabel="label" optionValue="value" class="w-full" :disabled="true"
                    placeholder="Select customer type" />
                <span class="text-red text-xs text-red-700 text-error" v-if="errors['customer_type']">{{
                    errors['customer_type'][0] }}</span>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">Customer Name *</label>
                <Dropdown v-model="formValues.customer_no" @change="changeCustomerName($event.value)"
                    :options="customers" optionLabel="label" optionValue="value" class="w-full" :disabled="true"
                    placeholder="Select customer" />
                <span class="text-red text-xs text-red-700 text-error" v-if="errors['customer_no']">{{
                    errors['customer_no'][0] }}</span>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">Joint Status *</label>
                <Dropdown v-model="formValues.joint_status" :options="joinStatusOptions" optionLabel="label"
                    optionValue="value" class="w-full" :disabled="!canSaveGeneral"
                    placeholder="Select calculation option" />
                <span class="text-red text-xs text-red-700 text-error" v-if="errors['joint_status']">{{
                    errors['joint_status'][0] }}</span>
            </div>
            <div class="col-span-2" v-if="formValues.joint_status === 'J'">
                <JointDetailsFields :customerTypes="customerTypes" :jointDetailsConfig="jointDetailsConfig"
                    @changeJointCustomerName="changeCustomerName(formValues.customer_no)"
                    v-model="formValues.joint_details" :errors="errors" :canSaveGeneral="canSaveGeneral" />
            </div>
            <div class="clear-fix" v-else></div>
            <div>
                <label for="" class="block mb-1 font-bold">Insured Name *</label>
                <Textarea v-model="formValues.insured_name" placeholder="Insured name" class="w-full" rows="5"
                    :disabled="!canSaveGeneral" />
                <span class="text-red text-xs text-red-700 text-error" v-if="errors['insured_name']">{{
                    errors['insured_name'][0] }}</span>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">Insured Name (Khmer) *</label>
                <Textarea v-model="formValues.insured_name_kh" placeholder="Insured name in khmer" class="w-full"
                    rows="5" :disabled="!canSaveGeneral" />
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['insured_name_kh']">{{
                    errors['insured_name_kh'][0] }}</span>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">Period Type *</label>
                <Dropdown v-model="formValues.insurance_period_type" :options="periodTypes" optionLabel="label"
                    optionValue="value" class="w-full" :disabled="isEndorsement" placeholder="Select period type" />
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['insurance_period_type']">{{
                    errors['insurance_period_type'][0]
                }}</span>
            </div>
            <div class="grid grid-cols-2 gap-x-5">
                <div>
                    <label for="" class="block mb-1 font-bold">Inception Date *</label>
                    <Calendar v-model="formValues.effective_date_from" dateFormat="dd-M-yy"
                        @input="setValueForExpiryDate($event.value)"
                        @update:modelValue="updateIsInsurancePeriodChange(true);" :maxDate="formValues.effective_date_to"
                        :disabled="isEndorsement" />
                    <span class="text-red text-xs text-red-700  text-error" v-if="errors['effective_date_from']">{{
                        errors['effective_date_from'][0]
                        }}</span>
                </div>
                <div>
                    <label for="" class="block mb-1 font-bold">Expiry Date *</label>
                    <Calendar v-model="formValues.effective_date_to" dateFormat="dd-M-yy"
                        @update:modelValue="updateIsInsurancePeriodChange(true)"
                        :minDate="formValues.effective_date_from" :disabled="isEndorsement" />
                    <span class="text-red text-xs text-red-700  text-error" v-if="errors['effective_date_to']">{{
                        errors['effective_date_to'][0]
                        }}</span>
                </div>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">Endorsement Clause *</label>
                <MultiSelect v-model="formValues.endorsement_clause" :options="endorsementClauses" optionLabel="label"
                    display="chip" optionValue="value" filter class="w-full" :disabled="!canSaveGeneral"
                    placeholder="Select endorsement clause" />
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['endorsement_clause']">{{
                    errors['endorsement_clause'][0]
                    }}</span>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">General Exclusion *</label>
                <MultiSelect v-model="formValues.general_exclusive" :options="generalExclusions" optionLabel="label"
                    display="chip" optionValue="value" filter class="w-full" :disabled="!canSaveGeneral"
                    placeholder="Select general exclusive" />
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['general_exclusive']">{{
                    errors['general_exclusive'][0]
                    }}</span>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">Business Channel *</label>
                <Dropdown v-model="formValues.sale_channel" :options="saleChannelOptions" optionLabel="label"
                    optionValue="value" class="w-full" @change="changeBusinessCategory($event.value)"
                    :disabled="!canSaveGeneral" placeholder="Select business channel" />
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['sale_channel']">{{
                    errors['sale_channel'][0]
                    }}</span>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">Business Name *</label>
                <Dropdown v-model="formValues.business_code" :options="businessChannelOptions" optionLabel="label"
                    optionValue="value" class="w-full" @change="changeBusinessChannel($event.value)"
                    :disabled="!canSaveGeneral" placeholder="Select business name" />
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['business_code']">{{
                    errors['business_code'][0]
                    }}</span>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">Commission Rate (%)</label>
                <InputNumber v-model="formValues.commission_rate" class="w-full" placeholder="Commission Rate" :min="0"
                    :max="100" :maxFractionDigits="5" :disabled="!canSaveGeneral" />
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['commission_rate']">{{
                    errors['commission_rate'][0]
                }}</span>
            </div>
            <div>
                <label for="" class="font-bold block mb-1">Business Handler *</label>
                <Dropdown class="w-full p-inputtext-sm" v-model="formValues.handler_code"
                    :options="businessHandlerOptions" placeholder="Select business handler" optionLabel="label"
                    optionValue="value" :filter="true" :disabled="!canSaveGeneral">
                </Dropdown>
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['handler_code']">{{
                    errors['handler_code'][0]
                    }}</span>
            </div>
            <div>
                <label for="" class="font-bold block mb-1">Policy Wording Version *</label>
                <Dropdown class="w-full p-inputtext-sm" v-model="formValues.policy_wording_version"
                    :options="policyWordingVersionOptions" placeholder="Select policy wording version"
                    optionLabel="label" optionValue="value" :filter="true" :disabled="!canSaveGeneral">
                </Dropdown>
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['policy_wording_version']">{{
                    errors['policy_wording_version'][0]
                }}</span>
            </div>
            <div class="clear-fix"></div>
            <div>
                <label for="" class="block mb-1 font-bold">Warranty</label>
                <CKEditor v-model="formValues.warranty" placeholder="Warranty" />
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['warranty']">{{
                    errors['warranty'][0]
                    }}</span>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">Memorandum</label>
                <CKEditor v-model="formValues.memorandum" placeholder="Memorandum" />
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['memorandum']">{{
                    errors['memorandum'][0]
                }}</span>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">Subjectivity</label>
                <CKEditor v-model="formValues.subjectivity" placeholder="Subjectivity" />
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['subjectivity']">{{
                    errors['subjectivity'][0]
                }}</span>
            </div>
            <div>
                <label for="" class="block mb-1 font-bold">Remark</label>
                <CKEditor v-model="formValues.remark" placeholder="Remark" />
                <span class="text-red text-xs text-red-700  text-error" v-if="errors['remark']">{{
                    errors['remark'][0]
                }}</span>
            </div>
            <div v-if="generalEndorsement || vehicleEndorsement">
                <label for="" class="block mb-1 font-bold">Endorsement Descriptions</label>
                <Textarea class="w-full" rows="5" v-model="formValues.endorsement_description"/>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-10">
        <!-- Show on general or add, remove vehicles endorsement -->
        <FormulateInput v-if="generalEndorsement || vehicleEndorsement" type="textarea" name="endorsement_description"
            label="Endorsement Descriptions" rows="4" />
    </div>

    <div class="text-right mt-5">
        <router-link :to="{ name: cancelRoute }" class="btn btn-outline-secondary w-24 mr-1"
            tag="button">Cancel</router-link>

        <button type="button" class="btn btn-primary w-24" @click="handleSubmit">Save</button>
    </div>
</template>

<script>

import Dropdown from 'primevue/dropdown';
import JointDetailsFields from '../../../../Quotation/Auto/JointDetailsFields.vue'
import Vehicle from '../../../../Quotation/Auto/Vehicle.vue'
import RemoveIcon from '../../component/delete-row.vue';
import DeleteItemModal from '../../component/DeleteItemModal.vue';
import CKEditor from '@/components/Form/CKEditor.vue';
import axios from 'axios';
import moment from 'moment';
export default {
    components: {
        Vehicle,
        JointDetailsFields,
        RemoveIcon,
        DeleteItemModal,
        CKEditor
    },
    props: {
        id: Number,
        isEndorsement: Boolean,
        endorsementType: String,
        cancelRoute: String,
        isEndorsementForm: Boolean,
        documentNo: {
            type: String,
            default: '',
        }
    },

    data() {
        return {
            after: false,
            formValues: {
                product_code: '0001',
                // Trigger updates commission_rate and handler_code
                commission_rate: null,
                endorsement_clause: [],
                data_type: 'POLICY',
                calc_option: ''
            },
            productOptions: [],
            // default selected product
            defaultProduct: "0001",
            calculateOptions: [],
            periodTypes: [],
            customerTypes: [],
            customers: [],
            endorsementClauses: [],
            generalExclusions: [],
            businessChannelOptions: [],
            saleChannelOptions: [],
            businessHandlerOptions: [],
            joinStatusOptions: [{
                'value': 'S', 'label': 'Single',

            }, { 'value': 'J', 'label': 'Joint' }],
            jointDetailsConfig: [],
            jointNamesStr: '',
            jointNamesStrKhmer: '',
            isResolvedInsuredName: false,
            warranty: '',
            memorandum: '',
            subjectivity: '',
            remark: '',
            defaultDayGap: 364,
            policyWordingVersionOptions: [],
            // To handle an issue when users change reinsurance period
            isInsurancePeriodChange: false,
            errors: []
        }
    },
    computed: {
        hasSubmitted() {
            // has id means already submitted
            return this.id !== null
        },
        generalEndorsement() {
            return this.endorsementType === 'GENERAL'
        },
        vehicleEndorsement() {
            return this.endorsementType === 'VEHICLE'
        },
        canSaveGeneral() {
            // if not an endorsement
            if (!this.isEndorsement) return true
            // if endorsement type is General
            return this.generalEndorsement
        },
    },
    methods: {
        assignFieldValues() {
            this.formValues.warranty = this.warranty
            this.formValues.memorandum = this.memorandum
            this.formValues.subjectivity = this.subjectivity
            this.formValues.remark = this.remark
        },

        handleSubmit() {
            if (this.isEndorsementForm) {
                axios.put(`/autos/save-endorsement-general/${this.id}`, this.formValues).then(response => {

                    if (response.data.success) {
                        notify(response.data.message, 'success')
                    }
                })
                    .then(() => {
                        this.resolveAuto()
                    })
                    .then(() => {
                        // Go to the vehicles information tab
                        document.querySelector('.nav-tabs a[href="#vehicle-info"]').click()
                    }).catch(() => this.notify('Error', 'error'))
            } else {
                if (this.id) {
                    axios.put('/autos/' + this.id, this.formValues)
                        .then(() => {
                            if (this.isInsurancePeriodChange)
                                this.$emit('updateRequireTotalPremiumState', true)
                            // Go to the vehicles information tab
                            document.querySelector('.nav-tabs a[href="#vehicle-info"]').click()
                        }).then(() => {
                            this.updateIsInsurancePeriodChange(false)
                        }).then(async () => {
                            // Generate comission data after save
                            await this.generateCommissionData();

                            this.$emit('updateBusinessChannel')
                        })
                } else {
                    axios.post('/autos', this.formValues).then(response => {
                        this.id = response.data.id
                    }).finally(() => {
                        // Go to the vehicles information tab
                        document.querySelector('.nav-tabs a[href="#vehicle-info"]').click()
                    })
                }
            }
        },

        generateCommissionData() {
            axios.get(`/autos/${this.id}/get-policy-id`).then(res => {
                const policyId = res.data;
                if (policyId) {
                    axios.get(`/policy-service/generate-commission-data/${policyId}`)
                        .catch(err => console.log(err))
                }
            })
        },

        getServices() {
            axios.get('/auto-service/get-services').then(response => {
                this.productOptions = response.data.productOptions.map(item => {
                    item.label = `${item.label} (${item.desc})`
                    return item
                })
                this.calculateOptions = response.data.calculateOptions
                this.periodTypes = response.data.periodTypes
                this.customerTypes = response.data.customerTypes
                this.endorsementClauses = response.data.endorsementClauses
                this.generalExclusions = response.data.generalExclusions

                this.jointDetailsConfig = response.data.jointDetailsConfig

                // Assign default clauses
                this.formValues.endorsement_clause = response.data.defaultEndorsementClauses?.map((item) => parseInt(item))
                this.formValues.general_exclusive = response.data.defaultGeneralExclusions?.map((item) => parseInt(item))
            })
                .finally(() => {
                    this.resolveAuto()
                    this.listSaleChannels()
                    this.listBusinessHandlers()
                })
        },
        renderListCustomers(e) {
            axios.get('/auto-service/list-customers-by-type/' + e).then(response => {
                this.customers = response.data
            })
        },

        listPolicyWordingVersions() {
            axios.get(`/auto-service/list-policy-wording-version-by-product-code/${this.formValues.product_code}`).then(response => {
                this.policyWordingVersionOptions = response.data
            })
        },

        listSaleChannels() {
            axios.get('/business-channels-service/list-sale-channels').then(response => {
                this.saleChannelOptions = response.data
            })
        },
        listBusinessHandlers() {
            axios.get('/business-channels-service/list-business-handlers').then(response => {
                this.businessHandlerOptions = response.data
            })
        },

        changeBusinessChannel(e) {
            axios.get('/auto-service/find-business-channel/' + e.target.value).then(response => {
                this.formValues.commission_rate = response.data.commission_rate
                this.formValues.handler_code = response.data.handler_code
            })
        },

        changeBusinessCategory(e) {
            axios.get('/auto-service/list-business-channels-by-category/' + e).then(response => {
                this.businessChannelOptions = response.data
            })
        },

        changeCustomerName(e) {
            if (!this.isResolvedInsuredName) {
                var customersNo = [e ?? null]

                if (this.formValues.joint_details) {
                    var jointDetailNames = this.formValues.joint_details.map(item => {
                        return item.customer_no ? item.customer_no : null
                    })
                    customersNo = customersNo.concat(jointDetailNames)
                }
                this.getJointNamesStr(customersNo)
                this.getJointNamesStrKhmer(customersNo)
            } else {
                // Assign resolved insured_name when editing
                this.jointNamesStr = this.formValues.insured_name
                this.jointNamesStrKhmer = this.formValues.insured_name_kh
                this.isResolvedInsuredName = false
            }
        },

        getJointNamesStr(customersNo) {
            axios.post('/auto-service/get-insured-names/en', { customersNo: customersNo }).then(response => {
                this.jointNamesStr = response.data
            })
        },
        getJointNamesStrKhmer(customersNo) {
            axios.post('/auto-service/get-insured-names/kh', { customersNo: customersNo }).then(response => {
                this.jointNamesStrKhmer = response.data
            })
        },

        resolveAuto() {
            if (this.id) {
                axios.get('/autos/' + this.id).then(response => {
                    if (response) {
                        this.formValues = response.data
                        this.formValues.effective_date_from = moment(response.data?.effective_date_from).toDate()
                        this.formValues.effective_date_to = moment(response.data?.effective_date_to).toDate()
                        this.isResolvedInsuredName = true
                        this.formValues.warranty = response.data?.warranty ?? ''
                        this.formValues.subjectivity = response.data?.subjectivity ?? ''
                        this.formValues.memorandum = response.data?.memorandum ?? ''
                        this.formValues.remark = response.data?.remark ?? ''
                        this.changeBusinessCategory(this.formValues.sale_channel)
                        this.renderListCustomers(this.formValues.customer_type)
                    }
                }).finally(() => {
                    this.listPolicyWordingVersions()
                    console.log("console data", this.formValues.endorsement_clause, this.endorsementClauses)
                })
            }
        },
        setValueForExpiryDate() {
            let effect_date_from = new Date(this.formValues.effective_date_from)
            console.log(effect_date_from)
            // check if the input of year is 4 digit
            if (effect_date_from.getFullYear().toString().length == 4) {
                let isLeapYear = (effect_date_from.getFullYear()) % 4 === 0
                if (isLeapYear)
                    this.defaultDayGap = 365
                else
                    this.defaultDayGap = 364
                effect_date_from.setDate(effect_date_from.getDate() + this.defaultDayGap);
                this.formValues.effective_date_to = effect_date_from
            }
        },

        updateIsInsurancePeriodChange(isChanged) {
            this.isInsurancePeriodChange = isChanged
        },
    },

    mounted() {
        this.getServices()
    },
}
</script>
<style scoped>
.collap {
    display: none;
}

.rotate {
    transform: rotate(180deg);
}

.iconColor {
    color: red;
}

.toast-container {
    left: 50% !important;
    top: 50% !important;
    min-width: 20vw !important;
    transform: translate(-50%, -50%) !important;
    position: -webkit-sticky !important;
    position: sticky !important;
    z-index: 1001 !important
}

.p-disabled {
    background: #e9ecef;
    opacity: 1;
}
</style>
