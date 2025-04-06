<template>
    <Dialog v-model:visible="visible" modal header="Add New Quote" class="md:w-10/12 lg:w-9/12" @show="loadDialog"
        :draggable="false" @hide="clearForm" position="top">
        <LoadingIndicator v-if="isLoading" />
        <div v-else>
            <Stepper v-model:activeStep="activeStep">
                <StepperPanel header="Customer Info." class="stepper-index">
                    <template #header="{ index, clickCallback }">
                        <button class="bg-transparent border-none inline-flex flex-column gap-2 focus:outline-none"
                            @click="clickCallback">
                            <span
                                :class="['rounded-full border-2 w-3rem px-1.5 h-3rem inline-flex align-items-center justify-content-center', { 'bg-blue-500 border-cyan-100 text-white': index <= activeStep, 'surface-border': index > activeStep }]">
                                1
                            </span>
                            <span>Customer Info.</span>
                        </button>
                    </template>
                    <template #content="{ nextCallback }">
                        <div class="w-full bg-gray-50 rounded-md px-4 py-5">
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label for="" class="form-label">Customer Type *</label>
                                    <Dropdown :options="selections.customerTypeOpts" v-model="formValues.customer_type"
                                        @change="filterCustomer($event.value)" class="w-full" optionValue="value"
                                        optionLabel="label" placeholder="Select customer type" showClear />
                                    <span class="text-error" v-if="errors.customer_type">{{ errors.customer_type[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Customer Name *</label>
                                    <Dropdown :options="selections.customerOpts" class="w-full"
                                        @change="detectInsuredName($event.value)" v-model="formValues.customer_no"
                                        :loading="customerLoading" optionValue="value" optionLabel="label"
                                        placeholder="Select customer" filter showClear />
                                    <span class="text-error" v-if="errors.customer_no">{{ errors.customer_no[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Insured Person (.xlsx) * <a
                                            class="text-blue-600 float-end text-sm italic"
                                            v-if="selections.quotationTmp" :href="selections.quotationTmp"
                                            download="pa_quotation_template.xlsx">Download template <i
                                                class="pi pi-download"></i></a></label>
                                    <FileUploader v-model="formValues.file" @update:modelValue="validateFile"
                                        :loading="validatingFile" accept=".xlsx" />

                                    <span class="text-error" v-if="errors.file">{{ errors.file[0] }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Joint Status *</label>
                                    <Dropdown :options="selections.jointOpts" optionLabel="label" optionValue="value"
                                        @change="changeJointStatus($event.value)" placeholder="Select joint status"
                                        class="w-full" v-model="formValues.joint_status" />
                                    <span class="text-error" v-if="errors.joint_status">{{ errors.joint_status[0]
                                    }}</span>
                                </div>
                                <div class="col-span-2" v-if="formValues.joint_status === 'J'">
                                    <JointDetail v-model="formValues.joint_details"
                                        :customerTypeOpts="selections.customerTypeOpts"
                                        :jointLevelOpts="selections.jointLevelOpts"
                                        :permissionOpts="selections.permissionOpts" @change="detectInsuredName" />
                                </div>
                                <div>
                                    <label for="" class="form-label">Insured Name (EN)*</label>
                                    <AutoComplete class="w-full" v-model="formValues.insured_name"
                                        @item-select="selectInsuredPerson" placeholder="Insured name in English"
                                        optionLabel="name_en" optionValue="name_en" dropdown :suggestions="insuredPs"
                                        @dropdown-click="searchInsuredPs" @complete="searchInsuredPs">
                                        <template #option="slotProps">
                                            <div class="flex align-options-center">
                                                <div><span>{{ slotProps.option.customer_no ?? '' }}</span> <span>{{
                                                    slotProps.option.name_en
                                                    ?? ''
                                                        }}</span></div>
                                            </div>
                                        </template>
                                    </AutoComplete>
                                    <span class="text-error" v-if="errors.insured_name">{{ errors.insured_name[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Insured Name (KM) *</label>
                                    <InputText placeholder="Insured name in Khmer"
                                        v-model="formValues.insured_name_kh" />
                                    <span class="text-error" v-if="errors.insured_name_kh">{{ errors.insured_name_kh[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Insured Person Note (EN)</label>
                                    <CKEditor v-model="formValues.insured_person_note"
                                        placeholder="Insured person note in English" />
                                    <span class="text-error" v-if="errors.insured_person_note">{{
                                        errors.insured_person_note[0]
                                        }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Insured Person Note (KM)</label>
                                    <CKEditor v-model="formValues.insured_person_note_kh"
                                        placeholder="Insured person note in Khmer" />
                                    <span class="text-error" v-if="errors.insured_person_note_kh">{{
                                        errors.insured_person_note_kh[0]
                                        }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex pt-4 justify-end border-t mt-5">
                            <Button type="button" label="Close" class="button-default outlined"
                                @click="visible = false"></Button>
                            <Button label="Next" class="button-info ml-1 leading-6" icon="pi pi-arrow-right" iconPos="right"
                                @click="nextCallback" />
                        </div>
                    </template>
                </StepperPanel>
                <StepperPanel header="Product Info">
                    <template #header="{ index, clickCallback }">
                        <button class="bg-transparent border-none inline-flex flex-column gap-2 focus:outline-none"
                            @click="clickCallback">
                            <span
                                :class="['rounded-full border-2 w-3rem px-1.5 h-3rem inline-flex align-items-center justify-content-center', { 'bg-blue-500 border-cyan-100 text-white': index <= activeStep, 'surface-border': index > activeStep }]">
                                2
                            </span>
                            <span>Plan Info.</span>
                        </button>
                    </template>
                    <template #content="{ prevCallback, nextCallback }">
                        <div class="w-full bg-gray-50 rounded-md px-4 py-5">
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label for="" class="form-label">Product Type</label>
                                    <Dropdown :options="selections.productTypeOpts" placeholder="Select product type"
                                        @change="setProductDefaultValue($event.value)" optionLabel="label" showClear
                                        optionValue="value" v-model="formValues.product_code" class="w-full">
                                        <template #option="slotProps">
                                            <div><span class="font-semibold text-blue-600">{{ slotProps.option.value }}.
                                                </span> <span class="text-blue-900">{{ slotProps.option.label }}</span>
                                            </div>
                                        </template>
                                    </Dropdown>
                                    <span class="text-error" v-if="errors.product_code">{{ errors.product_code[0]
                                    }}</span>
                                </div>
                                <div class="clear-fix">

                                </div>
                                <div>
                                    <label for="" class="form-label">Calc.Option *</label>
                                    <Dropdown optionValue="value" class="w-full" optionLabel="label"
                                        :options="selections.calcOpts" v-model="formValues.calc_option"
                                        @change="changeCalc($event.value)" placeholder="Select calculation option" />
                                    <span class="text-error" v-if="errors.calc_option">{{ errors.calc_option[0]
                                    }}</span>
                                </div>
                                <div v-if="formValues.calc_option === 'SPECIAL'">
                                    <label for="" class="form-label">Negotiation Rate (%) *</label>
                                    <InputNumber v-model="formValues.negotiation_rate" class="w-full"
                                        placeholder="Negociation rate" :min="0" :max="100" :minFractionDigits="0"
                                        :maxFractionDigits="2" />
                                    <span class="text-error" v-if="errors.negotiation_rate">{{
                                        errors.negotiation_rate[0] }}</span>
                                </div>
                                <div v-else>

                                </div>


                                <div>
                                    <label for="" class="form-label">Period Type *</label>
                                    <Dropdown :options="selections.periodOpts" class="w-full"
                                        v-model="formValues.insurance_period_type" optionValue="value"
                                        optionLabel="label" placeholder="Select customer" showClear />
                                    <span class="text-error" v-if="errors.insurance_period_type">{{
                                        errors.insurance_period_type[0]
                                        }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label for="" class="form-label">Inception Date *</label>
                                        <Calendar v-model="formValues.effective_date_from" placeholder="Inception date"
                                            dateFormat="dd-M-yy" showIcon :maxDate="formValues.effective_date_to"
                                            @input="setExpiryDate" @update:modelValue="setExpiryDate" />
                                        <span class="text-error" v-if="errors.effective_date_from">{{
                                            errors.effective_date_from[0]
                                            }}</span>
                                    </div>
                                    <div>
                                        <label for="" class="form-label">Expiry Date *</label>
                                        <Calendar v-model="formValues.effective_date_to" placeholder="Expiry date"
                                            dateFormat="dd-M-yy" showIcon :minDate="formValues.effective_date_from" />
                                        <span class="text-error" v-if="errors.effective_date_to">{{
                                            errors.effective_date_to[0]
                                            }}</span>
                                    </div>
                                </div>

                                <div>
                                    <label for="" class="form-label">Endorsement Clause *</label>
                                    <MultiSelect placeholder="Select endorsement clause"
                                        v-model="formValues.endorsement_clauses" optionLabel="label" optionValue="value"
                                        class="w-full" display="chip" :options="selections.endorsementClauseOpts" />
                                    <span class="text-error" v-if="errors.endorsement_clauses">{{
                                        errors.endorsement_clauses[0] }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">General Exclusion *</label>
                                    <MultiSelect placeholder="Select general exclusion"
                                        v-model="formValues.general_exclusions" optionLabel="label" optionValue="value"
                                        class="w-full" display="chip" :options="selections.generalExclusionOpts" />
                                    <span class="text-error" v-if="errors.general_exclusions">{{
                                        errors.general_exclusions[0] }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Business Channel *</label>
                                    <Dropdown placeholder="Select business channel" v-model="formValues.sale_channel"
                                        optionLabel="label" optionValue="value" class="w-full" display="chip"
                                        :options="selections.businessChannelOpts"
                                        @change="filterBusiness($event.value)" />
                                    <span class="text-error" v-if="errors.sale_channel">{{ errors.sale_channel[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Business Name *</label>
                                    <Dropdown placeholder="Select business name" v-model="formValues.business_code"
                                        optionLabel="label" :loading="businessLoading" optionValue="value"
                                        class="w-full" :options="selections.businessOpts"
                                        @change="changeBusiness($event.value)" filter />
                                    <span class="text-error" v-if="errors.business_code">{{ errors.business_code[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Commission Rate (%)</label>
                                    <InputNumber placeholder="Commission rate" v-model="formValues.commission_rate"
                                        :min="0" :max="100" :minFractionDigits="0" :maxFractionDigits="2" class="w-full"
                                        :options="selections.businessOpts" />
                                    <span class="text-error" v-if="errors.commission_rate">{{ errors.commission_rate[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Business Handler *</label>
                                    <Dropdown placeholder="Select business handler" v-model="formValues.handler_code"
                                        optionLabel="label" optionValue="value" class="w-full"
                                        :options="selections.businessHandlerOpts" filter />
                                    <span class="text-error" v-if="errors.handler_code">{{ errors.handler_code[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Surcharge (%)</label>
                                    <InputNumber placeholder="Surcharge" v-model="formValues.surcharge" :min="0"
                                        :max="100" :minFractionDigits="0" :maxFractionDigits="2" class="w-full" />
                                    <span class="text-error" v-if="errors.surcharge">{{ errors.surcharge[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Discount (%)</label>
                                    <InputNumber placeholder="Discount" v-model="formValues.discount" :min="0"
                                        :max="100" :minFractionDigits="0" :maxFractionDigits="2" class="w-full" />
                                    <span class="text-error" v-if="errors.discount">{{ errors.discount[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Geographical Limit *</label>
                                    <Dropdown placeholder="Select geographical limit" v-model="formValues.coverage_id"
                                        optionLabel="label" optionValue="value" class="w-full"
                                        :options="selections.geoGraphicalLimitOpts" />
                                    <span class="text-error" v-if="errors.coverage_id">{{ errors.coverage_id[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Policy Wording Version *</label>
                                    <Dropdown placeholder="Select policy wording version"
                                        v-model="formValues.policy_wording_version" optionLabel="label"
                                        optionValue="value" class="w-full"
                                        :options="selections.policyWordingVersionOpts" />
                                    <span class="text-error" v-if="errors.policy_wording_version">{{
                                        errors.policy_wording_version[0]
                                        }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Automatic Extensions *</label>
                                    <MultiSelect placeholder="Select automatic extensions"
                                        v-model="formValues.automatic_extensions" optionLabel="label"
                                        optionValue="value" class="w-full" display="chip"
                                        :options="selections.autoExtClauseOpts" />
                                    <span class="text-error" v-if="errors.automatic_extensions">{{
                                        errors.automatic_extensions[0] }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Accumulation Limit *</label>
                                    <InputNumber placeholder="Accumulation Limit"
                                        v-model="formValues.accumulation_limit_amount" :min="0" :minFractionDigits="0"
                                        :maxFractionDigits="2" class="w-full" />
                                    <span class="text-error" v-if="errors.accumulation_limit_amount">{{
                                        errors.accumulation_limit_amount[0]
                                        }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Warranty (EN)</label>
                                    <CKEditor v-model="formValues.warranty" placeholder="Warranty in English" />
                                    <span class="text-error" v-if="errors.warranty">{{ errors.warranty[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Warranty (KM)</label>
                                    <CKEditor v-model="formValues.warranty_kh" placeholder="Warranty in Khmer" />
                                    <span class="text-error" v-if="errors.warranty_kh">{{ errors.warranty_kh[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Memorandum (EN)</label>
                                    <CKEditor v-model="formValues.memorandum" placeholder="Memorandum in English" />
                                    <span class="text-error" v-if="errors.memorandum">{{ errors.memorandum[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Memorandum (KM)</label>
                                    <CKEditor v-model="formValues.memorandum_kh" placeholder="Memorandum in Khmer" />
                                    <span class="text-error" v-if="errors.memorandum_kh">{{ errors.memorandum_kh[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Subjectivity (EN)</label>
                                    <CKEditor v-model="formValues.subjectivity" placeholder="Subjectivity in English" />
                                    <span class="text-error" v-if="errors.subjectivity">{{ errors.subjectivity[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Subjectivity (KM)</label>
                                    <CKEditor v-model="formValues.subjectivity_kh"
                                        placeholder="Subjectivity in Khmer" />
                                    <span class="text-error" v-if="errors.subjectivity_kh">{{ errors.subjectivity_kh[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Remark (EN)</label>
                                    <CKEditor v-model="formValues.remark" placeholder="Remark in English" />
                                    <span class="text-error" v-if="errors.remark">{{ errors.remark[0]
                                    }}</span>
                                </div>
                                <div>
                                    <label for="" class="form-label">Remark (KM)</label>
                                    <CKEditor v-model="formValues.remark_kh" placeholder="Remark in Khmer" />
                                    <span class="text-error" v-if="errors.remark_kh">{{ errors.remark_kh[0]
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex pt-4 justify-end">
                            <Button label="Back" severity="secondary" class="ml-1 button-default leading-6" icon="pi pi-arrow-left"
                                @click="prevCallback" />
                            <Button label="Next" class="ml-1 button-info" icon="pi pi-arrow-right" iconPos="right"
                                @click="nextCallback" />
                        </div>
                    </template>
                </StepperPanel>
                <StepperPanel header="Additionals">
                    <template #header="{ index, clickCallback }">
                        <button class="bg-transparent border-none inline-flex flex-column gap-2 focus:outline-none"
                            @click="clickCallback">
                            <span
                                :class="['rounded-full border-2 w-3rem px-1.5 h-3rem inline-flex align-items-center justify-content-center', { 'bg-blue-500 border-cyan-100 text-white': index <= activeStep, 'surface-border': index > activeStep }]">
                                3
                            </span>
                            <span>Additional Info.</span>
                        </button>
                    </template>
                    <template #content="{ prevCallback }">
                        <div class="w-full bg-gray-50 rounded-md px-4 py-5">
                            <div class="grid grid-cols-2">
                                <div class="col-span-2">
                                    <OptionalBnf v-model="formValues.optional_benefits" :errors="errors"
                                        :benefits="selections.optionalBnfOpts" :of="selections.ofOpts" />
                                </div>
                            </div>
                        </div>
                        <div class="flex pt-4 justify-end">
                            <Button label="Back" class="button-default" icon="pi pi-arrow-left"
                                @click="prevCallback" />
                            <Button label="Submit" class="ml-1 button-info leading-6" :loading="isSaving" :disabled="disabledAction"
                             icon="pi pi-save" @click="handleSubmit" />
                        </div>
                    </template>
                </StepperPanel>
            </Stepper>
        </div>
    </Dialog>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import FileUploader from "@/components/Form/FileUploader.vue"
import LoadingIndicator from '@/components/LoadingIndicator.vue';
import QuoteService from '@/services/pa/quote.service';
import paService from '@/services/pa/pa.service';
import JointDetail from './JointDetail.vue';
import CKEditor from '@/components/Form/CKEditor.vue';
import moment from 'moment';
import OptionalBnf from './OptionalBnf.vue';

const activeStep = ref(0)
const isLoading = ref(true);
const isSaving = ref(false);
const businessLoading = ref(false)
const validatingFile = ref(false)
const isValidFile = ref(false)
const customerLoading = ref(false)
const disabledAction = computed(() => {
    return isLoading.value || isSaving.value || !isValidFile.value;
})
const emit = defineEmits(['success'])
const insuredPs = ref([])
const errors = ref([])
const selections = reactive({
    productTypeOpts: [],
    jointOpts: [],
    jointLevelOpts: [],
    permissionOpts: [],
    customerTypeOpts: [],
    customerOpts: [],
    endorsementClauseOpts: [],
    generalExclusionOpts: [],
    businessChannelOpts: [],
    businessOpts: [],
    businessHandlerOpts: [],
    geoGraphicalLimitOpts: [],
    policyWordingVersionOpts: [],
    calcOpts: [],
    periodOpts: [],
    optionalBnfOpts: [],
    autoExtClauseOpts: [],
    ofOpts: [],
    quotation_template: null
})
const visible = ref(false)
const formValues = ref({
    joint_details: [],
    negotiation_rate: '',
    optional_benefits: [],
    insured_person_note: '',
    insured_person_note_kh: '',
    warranty: '',
    warranty_kh: '',
    memorandum: '',
    memorandum_kh: '',
    subjectivity: '',
    subjectivity_kh: '',
    remark: '',
    remark_kh: '',
    effective_date_from: '',
    effective_date_to: '',
    coverage_id: '',
    discount: '',
    surcharge: '',
    commission_rate: '',
    file: null
})
const searchInsuredPs = _.debounce((event) => {
    paService.searchInsuredPs({ search: event.query ?? '' }).then((res) => insuredPs.value = res.data ?? []).catch((err) => notify('Server errors', 'error'))
}, 500)
const toggleDialog = () => {
    visible.value = !(visible.value)
}
const loadDialog = () => {
    setTimeout(() => {
        loadSelection()
    }, 100)
}
const loadSelection = () => {
    paService.selection().then((res) => {
        selections.jointOpts = res.data?.jointStatusOpts
        selections.productTypeOpts = res.data?.productTypeOpts
        selections.customerTypeOpts = res.data?.customerTypeOpts
        selections.jointLevelOpts = res.data?.jointLevelOpts
        selections.permissionOpts = res.data?.permissionOpts
        selections.businessChannelOpts = res.data?.businessChannelOpts
        selections.endorsementClauseOpts = res.data?.endorsementClauseOpts
        selections.businessHandlerOpts = res.data?.businessHandlerOpts
        selections.geoGraphicalLimitOpts = res.data?.geoGraphicalLimitOpts
        selections.generalExclusionOpts = res.data?.generalExclusionOpts
        selections.policyWordingVersionOpts = res?.data?.policyWordingOpts
        selections.calcOpts = res?.data?.calcOpts
        selections.periodOpts = res?.data?.periodOpts
        selections.optionalBnfOpts = res.data?.optionalBnfOpts
        selections.ofOpts = res.data?.ofOpts
        selections.autoExtClauseOpts = res.data?.autoExtClauseOpts
        selections.quotationTmp = res.data?.quotationTmp
        formValues.value.general_exclusions = res.data?.defaultGeneralExclusion
        formValues.value.endorsement_clauses = res.data?.defaultEndorsementClause
        formValues.value.automatic_extensions = res.data?.defaultAutoExtension
    }).then(() => {
        formValues.value.product_code = selections.productTypeOpts.length == 1 ? selections.productTypeOpts[0].value : null
        if (formValues.value.product_code) {
            setProductDefaultValue(formValues.value.product_code)
        }
        formValues.value.optional_benefits = selections.optionalBnfOpts
    }).catch((err) => { notify('Server errors', 'error') }).finally(() => isLoading.value = false)
}
const filterCustomer = (customerType) => {
    if (customerType) {
        customerLoading.value = true
        paService.searchInsuredPs({ customer_type: customerType }).then((res) => {
            selections.customerOpts = res.data.map((item) => {
                item.label = item.customer_no + '-' + item.name_en
                item.value = item.customer_no
                return item
            })
        }).finally(() => customerLoading.value = false)
    }
}
const selectInsuredPerson = (event) => {
    formValues.value.insured_name_kh = event.value.name_kh
}
const setProductDefaultValue = (value) => {
    const selectedProduct = selections.productTypeOpts.find((item) => item.value == value)
    if (selectedProduct) {
        formValues.value.accumulation_limit_amount = selectedProduct.default_accumulation_limit_amount
        formValues.value.discount = selectedProduct.default_discount
        formValues.value.surcharge = selectedProduct.default_surcharge
    }
}
const clearForm = () => {
    isLoading.value = true
    formValues.value = {}
    activeStep.value = 0
}
const setExpiryDate = () => {
    let defaultDayGap = 0;
    let selectedYear = moment(formValues.value.effective_date_from).toDate().getFullYear()
    if (selectedYear.toString().length == 4) {
        let isLeapYear = (selectedYear % 4 === 0)
        if (isLeapYear) defaultDayGap = 365
        else defaultDayGap = 364
        formValues.value.effective_date_to = moment(formValues.value?.effective_date_from).add(defaultDayGap, 'days').toDate()
    }
}
const changeCalc = (value) => {
    if (value !== 'SPECIAL') formValues.value.negotiation_rate = null
}
const filterBusiness = (businessChanncel) => {
    businessLoading.value = true
    paService.filterBusiness(businessChanncel).then((res) => {
        selections.businessOpts = res.data
    }).finally(() => businessLoading.value = false)
}
const changeBusiness = (code) => {
    const selectedBusiness = selections.businessOpts.find(item => item.value == code)
    if (selectedBusiness) {
        formValues.value.commission_rate = selectedBusiness.commission_rate
        formValues.value.handler_code = selectedBusiness.handler_code
    }
}
const validateFile = (file) => {
    console.log("modelValue", formValues.value?.file)
    if (file) {
        isValidFile.value = false
        let form = new FormData()
        form.append('file', file)
        validatingFile.value = true
        paService.validateFileUpload(form).then((res) => {
            if (res.data.success) {
                notify('Selected file contains correct information', 'success')
                errors.value = []
                isValidFile.value = true
            }
        }).catch((err) => {
            if (err.status === 422) {
                errors.value = { 'file': [err.response?.data?.message] }
            } else {
                errors.value.file = [err.response?.data?.message]
            }
        }).finally(() => validatingFile.value = false)
    } else {
        isValidFile.value = false
    }
}
const changeJointStatus = (status) => {
    if (status === "J" && formValues.value.joint_details.length === 0) {
        formValues.value.joint_details.push({})
    } else {
        formValues.value.joint_details = []
    }
}
const step1Fields = [
    'customer_type', 'customer_no', 'insured_name', 'insured_name_kh', 'file', 'joint_status', 'joint_details'
];
const step2Fields = [
    'product_type', 'calc_option', 'insurance_period_type', 'effective_date_from', 'effective_date_to', 'endorsement_cluases', 'general_exclusions', 'businsale_channel', 'business_code', 'commission_rate', 'handler_code', 'coverage_id', 'policy_wording_version', 'negotiation_rate', 'automatic_extensions', 'accumulation_limit_amount'
];
const step3Fields = [
    'optional_benefits'
]
const checkKeys = (keys) => {
    let shouldBreak = false
    for (const key of Object.keys(errors.value)) {
        for (const cKey of keys) {
            if (key.includes(cKey)) {
                shouldBreak = true
            }
        }
        if (shouldBreak) break
    }
    return shouldBreak
}
const handleStepperOnError = () => {
    if (checkKeys(step1Fields)) { activeStep.value = 0 }
    else if (checkKeys(step2Fields)) { activeStep.value = 1 }
    else if (checkKeys(step3Fields)) { activeStep.value = 2 }
    console.log(activeStep.value)
}
const appendFormData = (formData, data, parentKey = '') => {
    if (data && typeof data === 'object' && !(data instanceof File)) {
        Object.entries(data).forEach(([key, value]) => {
            const formKey = parentKey ? `${parentKey}[${key}]` : key;
            if (['effective_date_from', 'effective_date_to'].includes(formKey) && value) value = moment(value).format('YYYY-MM-DD')
            appendFormData(formData, value, formKey);
        });
    } else {
        formData.append(parentKey, data ?? '');
    }
}
function convertToFormData(data) {
    const formData = new FormData();
    appendFormData(formData, data);
    return formData;
}
const detectInsuredName = (value) => {
    if (formValues.value.joint_status === 'J') {
        const nameEn = formValues.value.joint_details.filter(item => item.name_en).map(item => item.name_en)
        const nameKh = formValues.value.joint_details.filter(item => item.name_kh).map(item => item.name_kh)
        formValues.value.insured_name = nameEn.join(', ')
        formValues.value.insured_name_kh = nameKh.join(', ')
    } else if (value) {
        const selectedCst = selections.customerOpts.find(item => item.value === value) ?? {}
        formValues.value.insured_name = selectedCst?.name_en
        formValues.value.insured_name_kh = selectedCst?.name_kh
    }
}
const handleSubmit = () => {
    let formData = convertToFormData(formValues.value)
    isSaving.value = true
    QuoteService.save(formData).then(res => {
        if (res.data?.success) {
            notify("Quote created successfully", 'success')
            emit('success')
        } else notify("Creating quote failed", 'error')
    })
        .catch(err => {
            if (err.status === 422) {
                errors.value = err.response?.data?.errors
                handleStepperOnError()
            }
            else {
                notify("Something went wrong", 'error')
            }
        })
        .finally(() => {
            isSaving.value = false
        })
}
defineExpose({
    toggleDialog
})
onMounted(() => {

})
</script>