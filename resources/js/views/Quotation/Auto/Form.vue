<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Auto Quotation
            </h2>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12">
                <div class="flex items-center px-5 pt-3 pb-0 border-b border-gray-200">
                    <div class="nav nav-tabs flex-col sm:flex-row justify-center lg:justify-start" role="tablist">
                        <template v-for="(tab, index) in tabs">
                            <a v-if="id || tab.target === '#info'" :key="index" data-toggle="tab"
                                :data-target="tab.target" :href="tab.href" :class="tab.classes" role="tab"
                                @click="changeTab($event, tab)">
                                {{ tab.title }}
                            </a>
                        </template>
                    </div>
                </div>
                <div class="p-5">
                    <div class="tab-content">
                        <div id="info" class="tab-pane active" role="tabpanel" aria-labelledby="info-tab">
                            <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                                <div class="col-span-6">
                                    <label class="mb-1 block font-bold" for="">Product Type *</label>
                                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.product_code"
                                        :options="productOptions" optionLabel="label" optionValue="value" showClear
                                        placeholder="Product" :disabled="hasSubmitted" :filter="true">
                                        <template #option="slotProps">
                                            <p class="text-sm font-semibold">{{ slotProps.option.label }}</p>
                                            <span class="text-xs">{{ slotProps.option.desc }}</span>
                                        </template>
                                    </Dropdown>
                                    <span class="text-red text-xs text-red-700 text-error"
                                        v-if="errors['product_code']">{{
                                            errors['product_code'][0] }}</span>
                                </div>
                                <div class="col-span-6">

                                </div>
                                <InputText type="hidden" v-model="formValues.data_type" value="QUOTATION" />
                                <div class="col-span-6">
                                    <label for="" class="block mb-1 font-bold">Calculate Options *</label>
                                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.calc_option"
                                        :options="calculateOptions" placeholder="Calculate Options" showClear
                                        :disabled="hasSubmitted" optionLabel="label" optionValue="value">
                                    </Dropdown>
                                    <span class="text-red text-xs text-red-700 text-error"
                                        v-if="errors['calc_option']">{{
                                            errors['calc_option'][0] }}</span>
                                </div>
                                <div class="col-span-6" v-if="formValues.calc_option === 'SPECIAL'">
                                    <label for="" class="block mb-1 font-bold">Negotiation Rate (%) *</label>
                                    <InputNumber v-model="formValues.negotiation_rate" class="w-full"
                                        :maxFractionDigits="2" :min="0" :max="100" placeholder="Negotiation Rate" />
                                    <span class="text-red text-xs text-red-700 text-error"
                                        v-if="errors['negotiation_rate']">{{
                                            errors['negotiation_rate'][0] }}</span>
                                </div>
                                <div class="clear-fix" v-else></div>
                                <div class="col-span-6">
                                    <label for="" class="block mb-1 font-bold">Customer Type *</label>
                                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.customer_type" showClear
                                        :options="customerTypes" placeholder="Select customer type"
                                        :disabled="hasSubmitted" optionLabel="label" optionValue="value"
                                        @change="renderListCustomers($event.value)">
                                    </Dropdown>
                                    <span class="text-red text-xs text-red-700 text-error"
                                        v-if="errors['customer_type']">{{
                                            errors['customer_type'][0] }}</span>
                                </div>
                                <div class="col-span-6">
                                    <label for="" class="block mb-1 font-bold">Customer Name *</label>
                                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.customer_no" showClear
                                        :options="customers" placeholder="Customer Name" :disabled="hasSubmitted"
                                        @change="handleInsuredPersonName($event.value)" optionLabel="label"
                                        optionValue="value" :filter="true">
                                    </Dropdown>
                                    <span class="text-red text-xs text-red-700 text-error"
                                        v-if="errors['customer_no']">{{
                                            errors['customer_no'][0] }}</span>
                                </div>
                                <div class="col-span-6">
                                    <label for="" class="block mb-1 font-bold">Joint Status *</label>
                                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.joint_status" showClear
                                        :options="joinStatusOptions" placeholder="Joint status" optionLabel="label"
                                        @update:modelValue="changeJointStatus" optionValue="value">
                                    </Dropdown>
                                    <span class="text-red text-xs text-red-700 text-error"
                                        v-if="errors['joint_status']">{{
                                            errors['joint_status'][0] }}</span>
                                </div>
                                <div class="col-span-12">
                                    <JointDetailsFields :customerTypes="customerTypes" @change="handleInsuredPersonName"
                                        v-if="formValues.joint_status === 'J'" :jointDetailsConfig="jointDetailsConfig"
                                        v-model="formValues.joint_details" :errors="errors" />
                                </div>
                                <div class="col-span-6">
                                    <label for="" class="font-bold mb-1 block">Insured name (English)*</label>
                                    <Textarea v-model="formValues.insured_name" placeholder="Insured name"
                                        class="w-full" rows="5" cols="30" />
                                    <span class="text-red text-xs text-red-700 text-error"
                                        v-if="errors['insured_name']">{{
                                            errors['insured_name'][0] }}</span>
                                </div>
                                <div class="col-span-6">
                                    <label for="" class="font-bold mb-1 block">Insured name (Khmer)*</label>
                                    <Textarea v-model="formValues.insured_name_kh" placeholder="Insured name"
                                        class="w-full" rows="5" cols="30" />
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['insured_name_kh']">{{
                                            errors['insured_name_kh'][0] }}</span>
                                </div>
                                <div class="col-span-6">
                                    <label for="" class="font-bold block mb-1">Period Type *</label>
                                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.insurance_period_type"
                                        :options="periodTypes" placeholder="Select period type" optionLabel="label"
                                        showClear optionValue="value">
                                    </Dropdown>
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['insurance_period_type']">{{
                                            errors['insurance_period_type'][0]
                                        }}</span>
                                </div>
                                <div class="col-span-3">
                                    <label for="" class="font-bold block mb-1">Inception Date *</label>
                                    <Calendar v-model="formValues.effective_date_from" placeholder="Inception Date"
                                        @update:modelValue="updateIsInsurancePeriodChange(true); setValueForExpiryDate()"
                                        @input="setValueForExpiryDate" :maxDate="formValues.effective_date_to"
                                        dateFormat="dd-M-yy" showIcon iconDisplay="input" />
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['effective_date_from']">{{
                                            errors['effective_date_from'][0]
                                        }}</span>
                                </div>
                                <div class="col-span-3">
                                    <label for="" class="font-bold block mb-1">Expiry Date *</label>
                                    <Calendar v-model="formValues.effective_date_to" placeholder="Expiry Date"
                                        @update:modelValue="updateIsInsurancePeriodChange(true)"
                                        :minDate="formValues.effective_date_from" dateFormat="dd-M-yy" showIcon
                                        iconDisplay="input" />
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['effective_date_to']">{{
                                            errors['effective_date_to'][0]
                                        }}</span>
                                </div>

                                <div class="col-span-6">
                                    <label for="" class="font-bold block mb-1">Endorsement Clause *</label>
                                    <MultiSelect v-model="formValues.endorsement_clause" display="chip"
                                        :options="endorsementClauses" optionLabel="label" optionValue="value"
                                        placeholder="Select endorsement clause" class="w-full" filter />
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['endorsement_clause']">{{
                                            errors['endorsement_clause'][0]
                                        }}</span>
                                </div>
                                <div class="col-span-6">
                                    <label for="" class="font-bold block mb-1">General Exclusion *</label>
                                    <MultiSelect v-model="formValues.general_exclusive" display="chip"
                                        :options="generalExclusions" optionLabel="label" optionValue="value"
                                        placeholder="Select general exclusive" class="w-full" filter />
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['general_exclusive']">{{
                                            errors['general_exclusive'][0]
                                        }}</span>
                                </div>
                                <div class="col-span-6">
                                    <label for="" class="font-bold block mb-1">Business Channel *</label>
                                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.sale_channel"
                                        :options="saleChannelOptions" placeholder="Select business channel"
                                        optionLabel="label" @change="changeBusinessCategory($event.value)"
                                        @update:modelValue="changeBusinessCategory" optionValue="value" :filter="true">
                                    </Dropdown>
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['sale_channel']">{{
                                            errors['sale_channel'][0]
                                        }}</span>
                                </div>
                                <div class="col-span-6">
                                    <label for="" class="font-bold block mb-1">Business Name *</label>
                                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.business_code"
                                        :options="businessChannelOptions" placeholder="Select business name"
                                        optionLabel="label" @change="changeBusinessChannel($event.value)"
                                        optionValue="value" :filter="true">
                                    </Dropdown>
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['business_code']">{{
                                            errors['business_code'][0]
                                        }}</span>
                                </div>
                                <div class="col-span-6">
                                    <label for="" class="font-bold block mb-1">Commission Rate *</label>
                                    <InputNumber v-model="formValues.commission_rate" class="w-full"
                                        placeholder="Commission Rate" :min="0" :max="100" :maxFractionDigits="5" />
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['commission_rate']">{{
                                            errors['commission_rate'][0]
                                        }}</span>
                                </div>
                                <div class="col-span-6">
                                    <label for="" class="font-bold block mb-1">Business Handler *</label>
                                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.handler_code"
                                        :options="businessHandlerOptions" placeholder="Select business handler"
                                        optionLabel="label" optionValue="value" :filter="true">
                                    </Dropdown>
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['handler_code']">{{
                                            errors['handler_code'][0]
                                        }}</span>
                                </div>
                                <div class="col-span-6">
                                    <label for="" class="font-bold block mb-1">Policy Wording Version *</label>
                                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.policy_wording_version"
                                        :options="policyWordingVersionOptions"
                                        placeholder="Select policy wording version" optionLabel="label"
                                        optionValue="value" :filter="true">
                                    </Dropdown>
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['policy_wording_version']">{{
                                            errors['policy_wording_version'][0]
                                        }}</span>
                                </div>
                                <div class="clear-fix"></div>
                                <div class="col-span-12">
                                    <label for="" class="block mb-1 font-bold">Warranty</label>
                                    <CKEditor v-model="formValues.warranty" placeholder="Warranty" />
                                    <span class="text-red text-xs text-red-700  text-error" v-if="errors['warranty']">{{
                                        errors['warranty'][0]
                                    }}</span>
                                </div>
                                <div class="col-span-12">
                                    <label for="" class="block mb-1 font-bold">Memorandum</label>
                                    <CKEditor v-model="formValues.memorandum" placeholder="Memorandum" />
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['memorandum']">{{
                                            errors['memorandum'][0]
                                        }}</span>
                                </div>
                                <div class="col-span-12">
                                    <label for="" class="block mb-1 font-bold">Subjectivity</label>
                                    <CKEditor v-model="formValues.subjectivity" placeholder="Subjectivity" />
                                    <span class="text-red text-xs text-red-700  text-error"
                                        v-if="errors['subjectivity']">{{
                                            errors['subjectivity'][0]
                                        }}</span>
                                </div>
                                <div class="col-span-12">
                                    <label for="" class="block mb-1 font-bold">Remark</label>
                                    <CKEditor v-model="formValues.remark" placeholder="Remark" />
                                    <span class="text-red text-xs text-red-700  text-error" v-if="errors['remark']">{{
                                        errors['remark'][0]
                                    }}</span>
                                </div>
                            </div>
                            <div class="flex mt-5 justify-end">
                                <router-link to="/quotation/autos" class="button-default"
                                    tag="button">Cancel</router-link>
                                <Button type="button" @click="handleSubmit" :disabled="submitting"
                                    class="button-primary ml-1" :loading="submitting" label="Save" icon="pi pi-save">
                                </Button>
                            </div>
                        </div>
                        <div id="vehicle-info" class="tab-pane" role="tabpanel" aria-labelledby="vehicle-info-tab">
                            <VehiclesTab v-if="id && isShownVehicleTab" :masterDataId="id"
                                :requireUpdateTotalPremium="requireUpdateTotalPremium" :totalPremium="totalPremium"
                                @vehicleListUpdated="vehicleListUpdated"
                                @updateIsInsurancePeriodChange="updateIsInsurancePeriodChange"
                                @updateRequireTotalPremiumState="updateRequireTotalPremiumState" />
                        </div>
                        <div id="deductible" class="tab-pane" role="tabpanel" aria-labelledby="deductible-tab">
                            <DeductibleTab v-if="id && isShownDeductibleTab" :id="id" cancelRoute="QuotationAutoIndex"
                                :isQuotationTab="true" :key="deductibleTabKey" redirectRoute="QuotationAutoIndex" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import JointDetailsFields from './JointDetailsFields.vue'
import VehiclesTab from '../../../components/Auto/Vehicles/VehiclesList.vue'
import DeductibleTab from '../../Policy/FormTabs/Deductible/Deductibles.vue'
import moment from 'moment';
import CKEditor from '../../../components/Form/CKEditor.vue';

export default {
    components: {
        JointDetailsFields,
        VehiclesTab,
        DeductibleTab,
        CKEditor
    },
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {
                product_code: '0001',
                // Trigger updates commission_rate and handler_code
                commission_rate: null,
                endorsement_clause: [],
                effective_date_from: null,
                effective_date_to: null,
                joint_details: [],
                warranty: '',
                memorandum: '',
                subjectivity: '',
                remark: '',
                data_type: 'QUOTATION'
            },
            errors: [],
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
            joinStatusOptions: [{ 'label': 'Single', 'value': 'S' }, { 'value': 'J', 'label': 'Joint' }],
            totalPremium: '',
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
            documentNo: null,
            submitting: false,
            tabs: [
                {
                    title: 'Quote Information',
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
            ],

            isShownVehicleTab: false,
            isShownDeductibleTab: false,

            // To handle the deductible tab after adding/editing/deleting vehicles
            deductibleTabKey: 0,
            requireDeductibleTabRendering: false,

            // To handle an issue when users change reinsurance period
            isInsurancePeriodChange: false,
            requireUpdateTotalPremium: false,
        }
    },
    watch: {
        // Trigger when product_code changes (when disabled product_code field does not trigger change event)
        productCode() {
            this.listPolicyWordingVersions()
        },
    },
    computed: {
        hasSubmitted() {
            // has id means already submitted
            return this.id !== null
        },
        productCode() {
            return this.formValues.product_code
        },
    },
    methods: {
        scrollToError() {
            setTimeout(() => {
                var eleClass = ''
                Object.keys(this.errors).forEach((key, index) => {
                    if (index === 0) {
                        eleClass = key;
                    }
                })
                $('html, body').animate({ scrollTop: $(`span.text-error`).offset().top - 75 }, 500);
            }, 500)
        },
        handleSubmit() {
            this.submitting = true
            if (this.id) {
                axios.put('/autos/' + this.id, this.formValues).then(() => {
                    if (this.isInsurancePeriodChange)
                        this.updateRequireTotalPremiumState(true)
                    // Go to the vehicles information tab
                    setTimeout(() => {
                        document.querySelector('.nav-tabs a[href="#vehicle-info"]').click()
                    }, 500)
                })
                    .catch(err => {
                        if (err.response.status === 422) {
                            this.errors = err.response?.data.errors
                            this.scrollToError()
                        }
                    })
                    .finally(() => this.submitting = false)
            } else {
                axios.post('/autos', this.formValues)
                    .then(response => {
                        this.errors = []
                        this.id = response.data.id
                        // Go to the vehicles information tab
                        setTimeout(() => {
                            document.querySelector('.nav-tabs a[href="#vehicle-info"]').click()
                        }, 500)
                    })
                    .catch(err => {
                        if (err.response.status === 422) {
                            this.errors = err.response?.data.errors
                            this.scrollToError()
                        } else {
                            notify(err.message, 'error')
                        }

                    })
                    .finally(() => this.submitting = false)
            }
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
            }).finally(() => {
                this.resolveAuto()
                this.listSaleChannels()
                this.listBusinessHandlers()
                this.listPolicyWordingVersions()
            })
        },

        renderListCustomers(value) {
            axios.get('/auto-service/list-customers-by-type/' + value).then(response => {
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

        changeBusinessChannel(value) {
            axios.get('/auto-service/find-business-channel/' + value).then(response => {
                this.formValues.commission_rate = response.data.commission_rate
                this.formValues.handler_code = response.data.handler_code
            })
        },

        changeBusinessCategory(value) {
            axios.get('/auto-service/list-business-channels-by-category/' + value).then(response => {
                this.businessChannelOptions = response.data
            })
        },

        changeCustomerName(value) {
            if (!this.isResolvedInsuredName) {
                var customersNo = [value ?? null]

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
        handleInsuredPersonName(value = null) {
            if (this.formValues.joint_status === 'J') {
                const nameEn = this.formValues.joint_details.filter(item => item.name_en).map(item => item.name_en)
                const nameKh = this.formValues.joint_details.filter(item => item.name_kh).map(item => item.name_kh)
                this.formValues.insured_name = nameEn.join(', ')
                this.formValues.insured_name_kh = nameKh.join(', ')
            } else if (value) {
                const selectedCst = this.customers.find(item => item.value === value) ?? {}
                this.formValues.insured_name = selectedCst?.name_en
                this.formValues.insured_name_kh = selectedCst?.name_kh
            }
        },
        changeJointStatus(value) {
            if (value === 'J' && !this.formValues.id) this.formValues.joint_details = [{}]
            else if (!this.formValues.id) this.formValues.joint_details = []
        },
        getJointNamesStr(customersNo) {
            axios.post('/auto-service/get-insured-names/en', { customersNo: customersNo }).then(response => {
                this.formValues.insured_name = response.data
            })
        },

        getJointNamesStrKhmer(customersNo) {
            axios.post('/auto-service/get-insured-names/kh', { customersNo: customersNo }).then(response => {
                this.formValues.insured_name_kh = response.data
            })
        },

        resolveAuto() {
            if (this.id) {
                axios.get('/autos/' + this.id).then(response => {
                    if (response) {
                        this.formValues = response.data
                        this.formValues.effective_date_from = moment(response.data?.effective_date_from).toDate()
                        this.formValues.effective_date_to = moment(response.data.effective_date_to).toDate()
                        this.formValues.warranty = response.data?.warranty ?? ''
                        this.formValues.subjectivity = response.data?.subjectivity ?? ''
                        this.formValues.memorandum = response.data?.memorandum ?? ''
                        this.formValues.remark = response.data?.remark ?? ''
                        this.documentNo = this.formValues.document_no;
                        this.changeBusinessCategory(this.formValues.sale_channel)
                        this.renderListCustomers(this.formValues.customer_type)
                        //
                        this.isResolvedInsuredName = true
                        this.totalPremium = this.formValues.total_premium
                    }
                })
            }
        },

        setValueForExpiryDate() {
            let effect_date_from = new Date(this.formValues.effective_date_from)
            // check if the input of year is 4 digit
            if (effect_date_from.getFullYear().toString().length == 4) {
                let isLeapYear = (effect_date_from.getFullYear()) % 4 === 0
                if (isLeapYear)
                    this.defaultDayGap = 365
                else
                    this.defaultDayGap = 364
                effect_date_from.setDate(effect_date_from.getDate() + this.defaultDayGap);
                console.log(effect_date_from)
                this.formValues.effective_date_to = effect_date_from
            }
        },

        updateIsInsurancePeriodChange(isChanged) {
            this.isInsurancePeriodChange = isChanged
        },

        updateRequireTotalPremiumState(isRequired) {
            this.requireUpdateTotalPremium = isRequired
        },

        vehicleListUpdated() {
            this.setRequireDeductibleTabRenderingStatus(true)
        },

        setRequireDeductibleTabRenderingStatus(status) {
            this.requireDeductibleTabRendering = status
        },
        changeTab(_, tab) {
            if (tab.target === '#deductible') {
                this.isShownDeductibleTab = true
                // If the vehicle list is updated, trigger the deductible tab to re-render
                if (this.requireDeductibleTabRendering) {
                    this.deductibleTabKey += 1;
                    // After re-rendering the deductible tab set requireDeductibleTabRendering to false
                    this.setRequireDeductibleTabRenderingStatus(false)
                }
            } else if (tab.target === '#vehicle-info') {
                this.isShownVehicleTab = true
            }
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

.p-disabled {
    background: #e9ecef;
    opacity: 1;
}
</style>
