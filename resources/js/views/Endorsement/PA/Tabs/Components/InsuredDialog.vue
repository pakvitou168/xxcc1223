<template>
    <Dialog v-model:visible="visible" modal :draggable="false" position="top" header="Insured Person"
        @show="loadSelection" @hide="handleClose" :style="{ width: '42rem' }">
        <span class="p-text-secondary text-sm block mb-5">Update insured person's information.</span>
        <div v-if="loading">
            <LoadingIndicator />
        </div>

        <div class="grid grid-cols-2 gap-5" v-else>
            <div v-for="ele in formFields">
                <label for="" class="form-label">{{ ele.label }} <span class="text-red-500"
                        v-if="ele.required">*</span></label>
                <component :is="ele.component" v-model="formValues[ele.field]" v-bind="ele.props"></component>
                <span class="text-error" v-if="errors[ele.field]">{{ errors[ele.field][0] }}</span>
            </div>
        </div>
        <div class="flex justify-end mt-4 border-t pt-4">
            <Button type="button" label="Cancel" class="rounded-md" severity="secondary" @click="visible = false"></Button>
            <Button type="button" label="Save" class="ml-1 bg-blue-700 text-white px-3 rounded-md bg-opacity-90" icon="pi pi-save" :loading="saving"
                @click="handleSubmit"></Button>
        </div>
    </Dialog>
</template>

<script setup>
import moment from 'moment';
import { computed, defineExpose, ref } from 'vue'
import insuredPersonService from '@/services/pa/insuredPerson.service';
import paService from '@/services/pa/pa.service';
import LoadingIndicator from '@/components/LoadingIndicator.vue'

const visible = ref(false)
const formValues = ref({
    id: '',
    status: "ACT",
    data_id: null
})
const props = defineProps({
    productCode: {
        type: String,
        default: ''
    },
    dataId:{
        type:Number,
        default:null
    },
    dataType:{
        type:String,
        default:"QUOTATION"
    }
})
const emit = defineEmits(['success'])
const classOpts = ref([])
const saving = ref(false)
const loading = ref(false)
const errors = ref({})
const formFields = computed(() => {
    return [
        {
            label: 'Name',
            field: 'name',
            component: 'InputText',
            required: true,
            props: {
                placeholder: "Name"
            }
        },
        {
            label: 'Relationship',
            field: 'relationship',
            component: 'InputText',
            props: {
                placeholder: "Relationship"
            }
        },
        {
            label: 'Gender',
            field: 'gender',
            component: 'Dropdown',
            required: true,
            props: {
                options: [{ label: 'Male', value: 'M' }, { label: 'Female', value: 'F' }],
                class: 'w-full',
                placeholder: "Select an option",
                optionLabel: "label",
                optionValue: "value"
            }
        },
        {
            label: 'Date of birth',
            field: 'date_of_birth',
            component: 'Calendar',
            props: {
                placeholder: "Date of birth",
                maxDate: new Date(),
                dateFormat: 'dd-M-yy',
                showIcon: true
            }
        },
        {
            label: 'Occupation',
            field: 'occupation',
            component: 'InputText',
            required: true,
            props: {
                placeholder: "Occupation",

            }
        },
        {
            label: 'Class code',
            field: 'working_class_code',
            component: 'Dropdown',
            required: true,
            props: {
                placeholder: "Select an option",
                optionLabel: 'label',
                optionValue: 'value',
                options: classOpts.value,
                class: 'w-full'
            }
        },
        {
            label: 'Sum insured',
            field: 'sum_insured',
            component: 'InputNumber',
            required: true,
            props: {
                placeholder: "Sum insured",
                minFractionDigits: 0,
                maxFractionDigits: 2,
                class: "w-full"
            }
        },
        {
            label: 'Permanent disablement',
            field: 'permanent_disablement_amount',
            component: 'InputNumber',
            required: true,
            props: {
                placeholder: "Permanent disablement",
                minFractionDigits: 0,
                maxFractionDigits: 2,
                class: "w-full"
            }
        },
        {
            label: 'Medical expense',
            field: 'medical_expense_amount',
            component: 'InputNumber',
            required: true,
            props: {
                placeholder: "Medical expense",
                minFractionDigits: 0,
                maxFractionDigits: 2,
                class: "w-full"
            }
        },
    ];
})

const toggleDialog = (data = {}) => {
    visible.value = !visible.value
    if (visible.value && data.id) {
        formValues.value = data
        formValues.value.date_of_birth = moment(data.date_of_birth).toDate()
    }
}
const handleClose = () => {
    formValues.value = {}
}
const loadSelection = () => {
    paService.classes().then((res) => {
        classOpts.value = res.data
    })
}
const handleSave = (data) => {
    saving.value = true
    insuredPersonService.save(data).then(res => {
        notify(res.data.message, 'success')
        emit('success')
    }).catch(err => {
        if (err.status === 422) errors.value = err.response?.data?.errors
        notify(err.response.data.message,'error')
    }).finally(() => saving.value = false)
}
const handleUpdate = (data, id) => {
    saving.value = true
    data.append('_method','PATCH')
    insuredPersonService.update(data, id).then(res => {
        notify(res.data.message, 'success')
        emit('success')
    }).catch(err => {
        if (err.status === 422) errors.value = err.response?.data?.errors
        notify(err.response.data.message,'error')
    }).finally(() => saving.value = false)
}
const convertToFormData = (data) => {
    const formData = new FormData();
    appendFormData(formData, data);
    return formData;
}
const appendFormData = (formData, data, parentKey = '') => {
    if (data && typeof data === 'object' && !(data instanceof File)) {
        Object.entries(data).forEach(([key, value]) => {
            const formKey = parentKey ? `${parentKey}[${key}]` : key;
            if (['date_of_birth'].includes(formKey) && value) value = moment(value).format('YYYY-MM-DD')
            appendFormData(formData, value, formKey);
        });
    } else {
        formData.append(parentKey, data ?? '');
    }
}
const handleSubmit = () => {
    if (!formValues.value.master_data_id){
        formValues.value.master_data_id = props.dataId
    }
    const formData = convertToFormData(formValues.value)
    if (!formValues.value.id) {
        handleSave(formData)
    } else {
        handleUpdate(formData, formValues.value.id)
    }
}
defineExpose({
    toggleDialog
})
</script>