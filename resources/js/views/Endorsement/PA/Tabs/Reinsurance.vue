<template>
    <div>
        <LoadingIndicator v-if="state.loading" class="my-6"></LoadingIndicator>
        <ReinsuranceForm v-else v-model="formValues.reinsurances" :dataId="dataId"
            :participants="state.dropdown.participants" :types="state.dropdown.groups"
            :defaultGroups="state.defaultGroups"></ReinsuranceForm>
        <div class="flex justify-end mt-6">
            <Button label="Back" class="button-default leading-6" icon="pi pi-arrow-left" @click="$emit('back')" />
            <Button label="Submit" @click="handleSubmit" :loading="state.saving"
                class="button-primary ml-1" icon="pi pi-save" />
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import policyService from '@/services/pa/policy.service';
import ReinsuranceForm from './Components/ReinsuranceForm.vue';
import paService from '@/services/pa/pa.service';
import LoadingIndicator from '@/components/LoadingIndicator.vue';
import { useRouter } from 'vue-router';

const router = useRouter()
const emit = defineEmits(['emit'])
const formValues = ref({
    reinsurances: []
})
const state = reactive({
    loading: false,
    saving: false,
    defaultGroups: [],
    dropdown: {
        groups: [],
        participants: []
    },
    errors: {}
})
const props = defineProps({
    dataId: {
        type: [Number, String],
        default: ''
    },
    productCode: {
        type: String,
        default: ''
    }
})

const loadDetail = () => {
    state.loading = true
    policyService.reinsurance(props.dataId, { generate: true }).then(res => {
        formValues.value.reinsurances = res.data
    }).finally(() => state.loading = false)
}
const loadSelection = () => {
    paService.reinsuranceSelection({ productCode: props.productCode }).then(res => {
        state.defaultGroups = res.data.defaultGroups
        state.dropdown.groups = res.data.groups
        state.dropdown.participants = res.data.participants
    })
}
const handleSubmit = () => {
    const totalShare = formValues.value.reinsurances.filter(item => !item.deleted_at).reduce((sum, item) => sum + item.share,0)
    if(totalShare > 100) {
        notify("Share is over the limitation of 100",'warn')
        return false
    }
    state.saving = true
    policyService.updateInsurance(formValues.value, props.dataId).then(res => {
        loadDetail();
        router.push({
            name: 'PAPolicyIndex'
        })
    }).catch(err => {
        if (err.status === 422) state.errors = err.response.data.errors
        else notify(err.response.data.messasge, 'error')
    }).finally(() => state.saving = false)
}
onMounted(() => {
    loadSelection()
    loadDetail()
})
</script>