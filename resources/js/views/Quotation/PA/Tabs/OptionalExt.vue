<template>
    <div>
        <LoadingIndicator v-if="loading" />
        <OptionalBnf v-else v-model="formValues.optional_benefits" :of="bnfBase" :dataId="dataId" :errors="errors" />
        <div class="flex justify-end mt-4 border-t pt-4">
            <Button label="Back" @click="$emit('back')" icon="pi pi-arrow-left" class="button-default" />
            <Button label="Save" @click="handleSave" :loading="saving" icon="pi pi-save"
                class="button-primary ml-1 leading-6" />
        </div>
    </div>
</template>

<script setup>
import LoadingIndicator from "@/components/LoadingIndicator.vue"
import { onMounted, ref } from 'vue'
import paService from '@/services/pa/pa.service.js'
import OptionalBnf from '../Components/OptionalBnf.vue'
import { useRouter } from "vue-router"

const router = useRouter()
const emit = defineEmits(['emit'])
const props = defineProps({
    dataId: {
        type: [Number, String],
        default: null
    },
    proCode: {
        type: [String, Number],
        default: null
    }
})
const errors = ref([])
const loading = ref(true)
const saving = ref(false)
const bnfBase = ref([])
const initForm = ref([])
const formValues = ref({
    optional_benefits: []
})
const loadDetail = () => {
    if (props.dataId) {
        paService.optionalExt(props.dataId).then(res => {
            formValues.value.optional_benefits = res.data
            initForm.value.optional_benefits = JSON.parse(JSON.stringify(formValues.value))
        }).finally(() => {
            loading.value = false
        })
    }
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
const loadSelection = () => {
    paService.optionalExtBase().then(res => {
        bnfBase.value = res.data
    })
}
const handleSave = async () => {
    if (JSON.stringify(formValues.value) === JSON.stringify(initForm.value)) {
        notify("Nothing changes", 'warn')
        return false
    }
    saving.value = true
    const formData = convertToFormData(formValues.value)
    formData.append('_method', 'PATCH')
    await paService.updateOptExt(formData, props.dataId).then(res => {
        notify(res.data.message, 'success')
        router.push({
            name: "PAQuotationIndex"
        })
    }).catch(err => {
        if (err.status === 422) errors.value = err.response?.data?.errors
        notify(err.response?.data?.message, 'error')
    }).finally(() => saving.value = false)
}
onMounted(() => {
    loadSelection()
    loadDetail()
})
</script>