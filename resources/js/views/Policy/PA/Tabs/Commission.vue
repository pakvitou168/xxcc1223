<template>
    <div class="px-6">
        <div class="grid" v-if="!loading">
            <div v-for="form in formFields" class="flex w-full border-b py-2">
                <label for="" class="w-[20rem] pt-3 font-semibold text-sm capitalize">{{ form.label }}</label>
                <div v-if="form.component">
                    <component :is="form.component" v-model="formValues[form.field]" v-bind="form.props"></component>
                </div>
                <div v-else>{{ form.format ? formatCurrency(formValues[form.field]) : formValues[form.field] }}</div>
            </div>
            <div class="flex justify-end mt-6 pt-4">
                <Button label="Back" icon="pi pi-arrow-left" @click="$emit('back')" class="button-default"></Button>
                <Button label="Save" icon="pi pi-save" :loading="saving" @click="handleSubmit" class="ml-1 button-primary"></Button>
                <Button label="Next" icon="pi pi-arrow-right" @click="$emit('next')" iconPos="right"
                    class="ml-1 button-default leading-6"></Button>
            </div>
        </div>
        <LoadingIndicator v-else />
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import policyService from '@/services/pa/policy.service';
import { formatCurrency } from "@/helpers"
import LoadingIndicator from '@/components/LoadingIndicator.vue'

const emit = defineEmits(['back', 'next'])
const props = defineProps({
    dataId: {
        type: [String, Number],
        default: ''
    }
})
const saving = ref(false)
const loading = ref(true)
const formValues = ref({

})
const initForm = ref({})
const formFields = computed(() => {
    return [
        {
            label: "Business Channel",
            component: false,
            field: 'business_category'
        },
        {
            label: "Business Name",
            field: "business_name"
        },
        {
            label: "gross written premium",
            field: 'gross_written_premium',
            format: true
        },
        {
            label: "Tax & Fee (%)",
            field: 'premium_tax_fee_rate',
            component: 'InputNumber',
            format: true
        },
        {
            label: "Tax & Fee Amount",
            field: 'premium_tax_fee',
            format: true
        },
        {
            label: "Net Written Premium",
            field: 'net_written_premium',
            format: true
        },
        {
            label: "Commission Rate (%)",
            field: 'commission_rate',
            component: 'InputNumber'
        },
        {
            label: "Commission Amount",
            field: 'commission_amount',
            format: true
        },
        {
            label: "WHT (%)",
            field: 'witholding_tax_rate',
            component: 'InputNumber'
        },
        {
            label: "WHT Amount",
            field: 'witholding_tax',
            format: true
        },
        {
            label: "Commission Due Amount",
            field: 'commission_due_amount',
            format: true
        }
    ]
})
const loadDetail = () => {
    loading.value = true
    policyService.commission(props.dataId).then(res => {
        formValues.value = res.data
        initForm.value = JSON.parse(JSON.stringify(formValues.value))
    }).finally(() => loading.value = false)
}
const handleSubmit = () => {
    saving.value = true
    policyService.updateCommission(formValues.value,props.dataId).then(res => {
        notify(res.data.message, 'success')
        emit('next')
    }).catch(err => {
        notify(err.response.data.message, 'error')
    }).finally(() => {
        saving.value = false
    })
}
onMounted(() => {
    loadDetail()
})
</script>