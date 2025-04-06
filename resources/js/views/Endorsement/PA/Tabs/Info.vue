<template>
    <div class="grid" v-if="state.formLoading">
        <LoadingIndicator />
    </div>
    <div class="w-full" v-else>
        <div class="mb-4 border-[10px] pt-1 border-gray-50 rounded-md" v-for="form in formFields">
            <h4 class="text-lg mb-2 font-semibold"><span>{{ form.title }}</span></h4>
            <div class="bg-gray-50 px-4 py-4 roudned">
                <div class="grid grid-cols-4 gap-5">
                    <div v-for="(ele, index) in form.fields" :class="ele.class">
                        <div class="w-full" v-if="typeof ele.visible !== 'undefined' ? ele.visible : 1">
                            <label for="" class="form-label" v-if="ele.component !== JointDetail">{{ ele.label }} <span
                                    class="text-red-600" v-if="ele.required">*</span></label>
                            <component :is="ele.component" v-model="formValues[ele.field]" v-bind="ele.props"
                                @[ele?.event]="ele?.eventHandler" @[ele?.event1]="ele?.event1Handler"
                                @[ele?.event2]="ele?.event2Handler"></component>
                            <span class="text-error" v-if="errors[ele.field]">{{ errors[ele.field][0] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <Button :label="state.saving ? 'Saving' : 'Save'" @click="handleSave" class="button-primary"
                icon="pi pi-save" :loading="state.saving" />
            <Button label="Next" iconPos="right" @click="$emit('next')" class="button-default ml-1 leading-6"
                icon="pi pi-arrow-right" />
        </div>
    </div>

</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import JointDetail from './Components/JointDetail.vue'
import endorsementService from '@/services/pa/endorsement.service';
import paService from '@/services/pa/pa.service';
import LoadingIndicator from '@/components/LoadingIndicator.vue';
import CKEditor from '@/components/Form/CKEditor.vue';
import moment from 'moment';
import { useRouter } from 'vue-router';

const router = useRouter()
const props = defineProps({
    dataId: {
        type: [Number, String],
        default: null
    },
    editable: {
        type: Boolean,
        default: true
    }
})
const emit = defineEmits(['next', 'loaded'])
const errors = ref({})
const state = reactive({
    saving: false,
    formLoading: true,
    customerLoading: false,
    insuredLoading: false,
    businessLoading: false,
    dropdown: {
        jointOpts: [],
        productTypeOpts: [],
        customerTypeOpts: [],
        jointLevelOpts: [],
        permissionOpts: [],
        businessChannelOpts: [],
        endorsementClauseOpts: [],
        businessHandlerOpts: [],
        geoGraphicalLimitOpts: [],
        generalExclusionOpts: [],
        policyWordingVersionOpts: [],
        calcOpts: [],
        periodOpts: [],
        optionalBnfOpts: [],
        ofOpts: [],
        autoExtClauseOpts: [],
        quotationTmp: [],
        customerOpts: [],
        insuredPs: [],
        businessOpts: []
    }
})
const initFields = {
    joint_details: [],
    negotiation_rate: null,
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
    effective_date_from: null,
    effective_date_to: null,
    coverage_id: '',
    discount: null,
    surcharge: null,
    commission_rate: null,
    file: null
}
const initForm = ref({
    ...initFields
})
const formValues = ref({ ...initFields })

const formFields = computed(() => {
    return [
        {
            title: 'Customer Info.',
            fields: [
                {
                    label: 'Customer Type',
                    field: 'customer_type',
                    component: 'Dropdown',
                    class: 'col-span-2',
                    event: 'change',
                    eventHandler: (e) => {
                        filterCustomer(e.value)
                    },
                    required: true,
                    props: {
                        optionLabel: 'label',
                        optionValue: 'value',
                        options: state.dropdown?.customerTypeOpts,
                        class: 'w-full',
                        placeholder: "Select an option",
                        showClear: true,
                        disabled: true
                    }
                },
                {
                    label: 'Customer Name',
                    field: 'customer_no',
                    component: 'Dropdown',
                    class: 'col-span-2',
                    required: true,
                    event: "change",
                    eventHandler: (e) => {
                        handleInsuredPersonChange(e.value)
                    },
                    props: {
                        optionLabel: 'label',
                        optionValue: 'value',
                        options: state.dropdown?.customerOpts,
                        class: 'w-full',
                        placeholder: "Select an option",
                        loading: state.customerLoading,
                        filter: true,
                        showClear: true,
                        disabled: true
                    }
                },
                {
                    label: 'Joint Status',
                    field: 'joint_status',
                    component: 'Dropdown',
                    class: 'col-span-2',
                    required: true,
                    props: {
                        optionLabel: 'label',
                        optionValue: 'value',
                        options: state.dropdown?.jointOpts,
                        class: 'w-full',
                        placeholder: "Select an option",
                        showClear: true,
                        disabled: true
                    }
                },
                {
                    label: 'Joint Details',
                    field: 'joint_details',
                    component: JointDetail,
                    class: 'col-span-4',
                    visible: formValues.value.joint_status === 'J',
                    event: 'change',
                    eventHandler: () => {
                        handleInsuredPersonChange()
                    },
                    props: {
                        class: 'w-full',
                        customerTypeOpts: state.dropdown.customerTypeOpts,
                        jointLevelOpts: state.dropdown.jointLevelOpts,
                        permissionOpts: state.dropdown.permissionOpts,
                        dataId: props.dataId,
                        editable: false
                    }
                },
                {
                    label: 'Insured Name (EN)',
                    field: 'insured_name',
                    component: 'AutoComplete',
                    class: 'col-span-2',
                    required: true,
                    event: 'item-select',
                    eventHandler: (e) => {
                        selectInsuredPerson(e)
                    },
                    event1: 'dropdown-click',
                    event1Handler: (e) => {
                        searchInsuredPs(e)
                    },
                    event2: 'complete',
                    event2Handler: (e) => {
                        searchInsuredPs(e)
                    },
                    props: {
                        class: 'w-full',
                        placeholder: "Type here",
                        dropdown: true,
                        suggestions: state.dropdown.insuredPs,
                        optionLabel: "name_en",
                        optionValue: "name_en",
                        loading: state.insuredLoading,
                        disabled: !props.editable
                    }
                },
                {
                    label: 'Insured Name (KH)',
                    field: 'insured_name_kh',
                    component: 'InputText',
                    class: 'col-span-2',
                    required: true,
                    props: {
                        placeholder: "Insured person name in Khmer",
                        dropdown: true,
                        disabled: !props.editable
                    }
                },
                {
                    label: "Insured Person Note",
                    field: "insured_person_note",
                    component: CKEditor,
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: "Insured person note in English"
                    }
                },
                {
                    label: "Insured Person Note (KM)",
                    field: "insured_person_note_kh",
                    component: CKEditor,
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: "Insured person note in Khmer"
                    }
                },
            ]
        },
        {
            title: "Product Info.",
            fields: [
                {
                    label: "Product Type",
                    field: 'product_code',
                    required: true,
                    component: 'Dropdown',
                    class: 'col-span-2',
                    props: {
                        options: state.dropdown.productTypeOpts,
                        optionLabel: 'label',
                        optionValue: 'value',
                        class: 'w-full',
                        placeholder: 'Select an option',
                        filter: true,
                        disabled: true
                    }
                },
                {

                },
                {
                    label: "Calculation Opt.",
                    field: 'calc_option',
                    required: true,
                    component: 'Dropdown',
                    class: 'col-span-2',
                    props: {
                        options: state.dropdown.calcOpts,
                        optionLabel: 'label',
                        optionValue: 'value',
                        class: 'w-full',
                        placeholder: 'Select an option',
                        filter: true,
                        disabled: true
                    }
                },
                {
                    label: "Negotiation Rate (%)",
                    field: 'negotiation_rate',
                    required: true,
                    component: 'InputNumber',
                    class: 'col-span-2',
                    visible: formValues.value.calc_option === 'SPECIAL',
                    props: {
                        placeholder: 'Negotiation Rate',
                        maxFractionDigits: 2,
                        minFractionDigits: 0,
                        max: 100,
                        class: 'w-full',
                        disabled: true
                    }
                },
                {
                    label: "Period Type",
                    field: 'insurance_period_type',
                    required: true,
                    component: 'Dropdown',
                    class: 'col-span-2',
                    props: {
                        options: state.dropdown.periodOpts,
                        optionLabel: 'label',
                        optionValue: 'value',
                        class: 'w-full',
                        placeholder: 'Select an option',
                        filter: true,
                        disabled: true
                    }
                },
                {
                    label: "Inception Date",
                    field: 'effective_date_from',
                    required: true,
                    component: 'Calendar',
                    event: 'input',
                    eventHandler: () => setExpiryDate(),
                    event1: 'update:modelValue',
                    event1Handler: () => setExpiryDate(),
                    props: {
                        class: 'w-full',
                        placeholder: 'Select an option',
                        maxDate: formValues.value.effective_date_to,
                        showIcon: true,
                        dateFormat: 'dd-M-yy',
                        disabled: true
                    }
                },
                {
                    label: "Expiry Date",
                    field: 'effective_date_to',
                    required: true,
                    component: 'Calendar',
                    props: {
                        class: 'w-full',
                        placeholder: 'Select an option',
                        minDate: formValues.value.effective_date_from,
                        showIcon: true,
                        dateFormat: 'dd-M-yy',
                        disabled: true
                    }
                },
                {
                    label: "Endorsement Clauses",
                    field: 'endorsement_clauses',
                    required: true,
                    component: 'MultiSelect',
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: 'Select options',
                        options: state.dropdown.endorsementClauseOpts,
                        optionLabel: 'label',
                        optionValue: 'value',
                        display: "chip",
                        disabled: !props.editable
                    }
                },
                {
                    label: "General Exclusion",
                    field: 'general_exclusions',
                    required: true,
                    component: 'MultiSelect',
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: 'Select options',
                        options: state.dropdown.generalExclusionOpts,
                        optionLabel: 'label',
                        optionValue: 'value',
                        display: "chip",
                        disabled: !props.editable
                    }
                },
                {
                    label: "Geographical Limit",
                    field: 'coverage_id',
                    required: true,
                    component: 'Dropdown',
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: 'Select options',
                        options: state.dropdown.geoGraphicalLimitOpts,
                        optionLabel: 'label',
                        optionValue: 'value',
                        showClear: true,
                        disabled: true
                    }
                },
                {
                    label: "Policy Wording Version",
                    field: 'policy_wording_version',
                    required: true,
                    component: 'Dropdown',
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: 'Select options',
                        options: state.dropdown.policyWordingVersionOpts,
                        optionLabel: 'label',
                        optionValue: 'value',
                        display: "chip"
                    }
                },
                {
                    label: "Automatic Extensions",
                    field: 'automatic_extensions',
                    required: true,
                    component: 'MultiSelect',
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: 'Select options',
                        options: state.dropdown.autoExtClauseOpts,
                        optionLabel: 'label',
                        optionValue: 'value',
                        display: "chip",
                        disabled: !props.editable
                    }
                },
                {
                    label: "Accumulation Limit",
                    field: 'accumulation_limit_amount',
                    required: true,
                    component: 'InputNumber',
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: 'Accumulation limit amount',
                        maxFractionDigits: 2,
                        minFractionDigits: 0,
                        disabled: !props.editable
                    }
                },
                {
                    label: "Business Channel",
                    field: 'sale_channel',
                    required: true,
                    component: 'Dropdown',
                    class: 'col-span-2',
                    event: 'change',
                    eventHandler: (e) => {
                        filterBusiness(e.value)
                    },
                    props: {
                        class: 'w-full',
                        placeholder: 'Select an option',
                        options: state.dropdown.businessChannelOpts,
                        optionLabel: 'label',
                        optionValue: 'value',
                        disabled: !props.editable
                    }
                },
                {
                    label: "Business Name",
                    field: 'business_code',
                    required: true,
                    component: 'Dropdown',
                    class: 'col-span-2',
                    event: 'change',
                    eventHandler: (e) => {
                        changeBusinessName(e.value)
                    },
                    props: {
                        class: 'w-full',
                        placeholder: 'Select an option',
                        options: state.dropdown.businessOpts,
                        optionLabel: 'label',
                        optionValue: 'value',
                        loading: state.businessLoading,
                        filter: true,
                        disabled: !props.editable
                    }
                },
                {
                    label: "Business Handler",
                    field: 'handler_code',
                    required: true,
                    component: 'Dropdown',
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: 'Select an option',
                        options: state.dropdown.businessHandlerOpts,
                        optionLabel: 'label',
                        optionValue: 'value',
                        loading: state.businessLoading,
                        filter: true,
                        disabled: !props.editable
                    }
                },
                {
                    label: "Commission Rate (%)",
                    field: 'commission_rate',
                    component: 'InputNumber',
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: 'Commission rate',
                        maxFractionDigits: 2,
                        minFractionDigits: 0,
                        max: 100,
                        disabled: !props.editable
                    }
                },
                {
                    label: "Surcharge (%)",
                    field: 'surcharge',
                    component: 'InputNumber',
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: 'Commission rate',
                        maxFractionDigits: 2,
                        minFractionDigits: 0,
                        max: 100,
                        disabled: !props.editable
                    }
                },
                {
                    label: "Discount (%)",
                    field: 'discount',
                    component: 'InputNumber',
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: 'Commission rate',
                        maxFractionDigits: 2,
                        minFractionDigits: 0,
                        max: 100,
                        disabled: !props.editable
                    }
                },
                {
                    label: "Warranty (EN)",
                    field: "warranty",
                    component: CKEditor,
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: "Warranty in English",
                        disabled: !props.editable
                    }
                },
                {
                    label: "Warranty (KH)",
                    field: "warranty_kh",
                    component: CKEditor,
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: "Warranty in Khmer",
                        disabled: !props.editable
                    }
                },
                {
                    label: "Memorandum (EN)",
                    field: "memorandum",
                    component: CKEditor,
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: "Memorandum in English",
                        disabled: !props.editable
                    }
                },
                {
                    label: "Memorandum (KH)",
                    field: "memorandum_kh",
                    component: CKEditor,
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: "Memorandum in Khmer",
                        disabled: !props.editable
                    }
                },
                {
                    label: "Subjectivity (EN)",
                    field: "subjectivity",
                    component: CKEditor,
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: "Subjectivity in English",
                        disabled: !props.editable
                    }
                },
                {
                    label: "Subjectivity (KH)",
                    field: "subjectivity_kh",
                    component: CKEditor,
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: "Subjectivity in Khmer",
                        disabled: !props.editable
                    }
                },
                {
                    label: "Remark (EN)",
                    field: "remark",
                    component: CKEditor,
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: "Remark in English",
                        disabled: !props.editable
                    }
                },
                {
                    label: "Remark (KH)",
                    field: "remark_kh",
                    component: CKEditor,
                    class: 'col-span-2',
                    props: {
                        class: 'w-full',
                        placeholder: "Remark in Khmer",
                        disabled: !props.editable
                    }
                }
            ]
        },
    ]
})
const handleInsuredPersonChange = (value) => {
    if (formValues.value.joint_status === 'J') {
        const nameEn = formValues.value.joint_details.filter(item => item.name_en).map(item => item.name_en)
        const nameKh = formValues.value.joint_details.filter(item => item.name_kh).map(item => item.name_kh)
        formValues.value.insured_name = nameEn.join(', ')
        formValues.value.insured_name_kh = nameKh.join(', ')
    } else if (value) {
        const selectedCst = state.dropdown.customerOpts.find(item => item.value === value) ?? {}
        formValues.value.insured_name = selectedCst?.name_en
        formValues.value.insured_name_kh = selectedCst?.name_kh
    }
}
const searchInsuredPs = _.debounce((event) => {
    state.insuredLoading = true
    paService.searchInsuredPs({ search: event.query ?? '' }).then((res) => state.dropdown.insuredPs = res.data ?? []).catch((err) => notify('Server errors', 'error')).finally(() => state.insuredLoading = false)
}, 500)
const selectInsuredPerson = (event) => {
    formValues.value.insured_name_kh = event.value.name_kh
}
const filterBusiness = (businessChanncel) => {
    state.businessLoading = true
    paService.filterBusiness(businessChanncel).then((res) => {
        state.dropdown.businessOpts = res.data
    }).finally(() => state.businessLoading = false)
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
const loadSelection = () => {
    paService.selection().then((res) => {
        state.dropdown.jointOpts = res.data?.jointStatusOpts
        state.dropdown.productTypeOpts = res.data?.productTypeOpts.map((item) => {
            return {
                label: item.value + '. ' + item.label,
                value: item.value
            }
        })
        state.dropdown.customerTypeOpts = res.data?.customerTypeOpts
        state.dropdown.jointLevelOpts = res.data?.jointLevelOpts
        state.dropdown.permissionOpts = res.data?.permissionOpts
        state.dropdown.businessChannelOpts = res.data?.businessChannelOpts
        state.dropdown.endorsementClauseOpts = res.data?.endorsementClauseOpts
        state.dropdown.businessHandlerOpts = res.data?.businessHandlerOpts
        state.dropdown.geoGraphicalLimitOpts = res.data?.geoGraphicalLimitOpts
        state.dropdown.generalExclusionOpts = res.data?.generalExclusionOpts
        state.dropdown.policyWordingVersionOpts = res?.data?.policyWordingOpts
        state.dropdown.calcOpts = res?.data?.calcOpts
        state.dropdown.periodOpts = res?.data?.periodOpts
        state.dropdown.optionalBnfOpts = res.data?.optionalBnfOpts
        state.dropdown.ofOpts = res.data?.ofOpts
        state.dropdown.autoExtClauseOpts = res.data?.autoExtClauseOpts
        state.dropdown.quotationTmp = res.data?.quotationTmp
    }).catch((err) => { notify('Server errors', 'error') }).finally(() => state.formLoading = false)
}
const filterCustomer = (customerType) => {
    if (customerType) {
        state.customerLoading = true
        paService.searchInsuredPs({ customer_type: customerType }).then((res) => {
            state.dropdown.customerOpts = res.data.map((item) => {
                item.label = item.customer_no + '-' + item.name_en
                item.value = item.customer_no
                return item
            })
        }).finally(() => state.customerLoading = false)
    }
}
const loadDetail = () => {
    if (props.dataId) {
        endorsementService.edit(props.dataId).then((res) => {
            formValues.value = res.data
            formValues.value.effective_date_from = moment(res.data.effective_date_from).toDate()
            formValues.value.effective_date_to = moment(res.data.effective_date_to).toDate()
            initForm.value = JSON.parse(JSON.stringify(formValues.value))
            if (['APV', 'REJ'].includes(formValues.value.quotation?.aprpoved_status) || formValues.value.quotation?.accepted_status === 'ACP') {
                notify("Quotation cannot be modified", 'warn')
                router.push({
                    name: "PAQuotationIndex"
                });
            } else {
                emit('loaded', formValues.value.product_code)
            }
        }).then(() => {
            filterCustomer(formValues.value.customer_type)
            filterBusiness(formValues.value.sale_channel)
        })
    }
}

const changeBusinessName = (bCode) => {
    const selectedBusiness = state.dropdown.businessOpts.find(item => item.value == code)
    if (selectedBusiness) {
        formValues.value.commission_rate = selectedBusiness.commission_rate
        formValues.value.handler_code = selectedBusiness.handler_code
    }
}

const convertToFormData = (data) => {
    const formData = new FormData();
    appendFormData(formData, data);
    return formData;
}
const appendFormData = (formData, data, parentKey = '') => {
    if (data && Array.isArray(data) && typeof data !== 'object') {
        // If the data is an array, we append each item with the appropriate index
        data.forEach((item, index) => {
            const formKey = `${parentKey}[${index}]`;
            appendFormData(formData, item, formKey);
        });
    } else if (data && typeof data === 'object' && !(data instanceof File)) {
        Object.entries(data).forEach(([key, value]) => {
            const formKey = parentKey ? `${parentKey}[${key}]` : key;
            if (['effective_date_from', 'effective_date_to'].includes(formKey) && value) value = moment(value).format('YYYY-MM-DD')
            appendFormData(formData, value, formKey);
        });
    } else {
        formData.append(parentKey, data ?? '');
    }
}
const handleSave = async () => {
    if (JSON.stringify(initForm.value) === JSON.stringify(formValues.value)) {
        notify("Nothing changes", 'warn')
        return false
    }
    state.saving = true
    const formBody = convertToFormData(formValues.value);
    formBody.append('_method', 'PATCH')
    await endorsementService.update(formBody, props.dataId).then(res => {
        notify(res.data.message, 'success')
        emit('next')
    }).catch(err => {
        if (err.status === 422) errors.value = err.response?.data?.errors
        else notify(err.response.data.message, 'error')
    }).finally(() => {
        state.saving = false
    })
}
onMounted(() => {
    loadSelection();
    loadDetail();
})
</script>