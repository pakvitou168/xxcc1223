<template>
    <div class="px-6 pt-4 sm:w-full md:w-1/2" v-if="!state.loading">
        <div class="grid gap-5">
            <div v-for="form in formFields">
                <label for="" class="form-label">{{ form.label }} <span v-if="form.required" class="text-red-600">*
                    </span></label>
                <component :is="form.component" v-model="formValues[form.field]" v-bind="form.props"></component>
                <span class="text-error" v-if="errors[form.field]">{{ errors[form.field][0] }}</span>
            </div>
        </div>
        <div class="mt-6 flex justify-end border-t pt-3">
            <Button label="Save" :loading="state.saving" icon="pi pi-save" @click="handleSubmit"
                class="button-primary"></Button>
            <Button label="Next" icon="pi pi-arrow-right" @click="$emit('next')" class="button-default leading-6 ml-1"
                iconPos="right"></Button>
        </div>
    </div>
    <div v-else class="mt-6">
        <LoadingIndicator />
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import policyService from '@/services/pa/policy.service';
import paService from '@/services/pa/pa.service';
import LoadingIndicator from '@/components/LoadingIndicator.vue'

const props = defineProps({
    dataId: {
        type: [String, Number],
        default: null
    }
})
const emit = defineEmits(['next'])
const state = reactive({
    saving: false,
    loading: true,
    dropdownList: {
        businessTypes: [],
        policyTypes: []
    }
})
const errors = ref({})
const formValues = ref({

})
const formFields = computed(() => {
    return [
        {
            label: "Business Type",
            field: 'business_type',
            component: 'Dropdown',
            required: true,
            props: {
                options: state.dropdownList.businessTypes,
                placeholder: "Select an option",
                optionLabel: "label",
                optionValue: "value",
                class: 'w-full'
            }
        },
        {
            label: "Policy Type",
            field: 'policy_type',
            component: 'Dropdown',
            required: true,
            props: {
                options: state.dropdownList.policyTypes,
                placeholder: "Select an option",
                optionLabel: "label",
                optionValue: "value",
                class: 'w-full'
            }
        }
    ]
})
const loadSelection = () => {
    paService.policyConfig().then(res => {
        state.dropdownList.businessTypes = res.data.businessTypes
        state.dropdownList.policyTypes = res.data.policyTypes
    }).then(() => {
        loadDetail()
    })
}
const loadDetail = () => {
    state.loading = true
    policyService.config(props.dataId).then(res => {
        formValues.value = res.data
    }).finally(() => state.loading = false)
}
const handleSubmit = () => {
    state.saving = true
    policyService.updateConfig(formValues.value, props.dataId).then(res => {
        notify(res.data.message, 'success')
        emit('next')
        errors.value = {}
    }).finally(() => {
        state.saving = false
    }).catch(err => {
        if (err.status === 422) errors.value = err.response.data.errors
        else notify(err.response.data.message, 'error')
    })
}
onMounted(() => {
    loadSelection()
})
</script>