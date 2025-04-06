<template>
    <Dialog v-model:visible="state.visible" modal header="Make Endorsement" :style="{ width: '25rem' }"
        @show="loadSelection">
        <div class="grid gap-4" v-if="!state.loading">
            <div v-for="form in formFields">
                <label for="" class="form-label">{{ form.label }} <span class="text-red-600"
                        v-if="form.required">*</span></label>
                <component :is="form.component" v-bind="form.props" v-model="formValues[form.field]"></component>
                <span class="text-error" v-if="errors[form.field]">{{ errors[form.field][0] }}</span>
            </div>
        </div>
        <div class="flex" v-else>
            <LoadingIndicator />
        </div>
        <div class="flex justify-end gap-1 mt-4">
            <Button type="button" label="Cancel" class="button-default" @click="state.visible = false"></Button>
            <Button type="button" class="button-info" :loading="saving" icon="pi pi-save" label="Save"
                @click="handleSubmit"></Button>
        </div>
    </Dialog>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import paService from '@/services/pa/pa.service';
import LoadingIndicator from '@/components/LoadingIndicator.vue';

const props = defineProps({
    errors: {
        type: Object,
        default: {}
    },
    saving: {
        type: Boolean,
        default: false
    }
})
const emit = defineEmits(['submit'])
const state = reactive({
    visible: false,
    loading: false,
    endorsementTypes: []
})
const formValues = ref({
    endorsement_description: ''
})
const formFields = computed(() => [
    {
        label: "Edorsement Type",
        component: 'Dropdown',
        field: 'endorsement_type',
        required: true,
        props: {
            placeholder: "Select endorsement type",
            options: state.endorsementTypes,
            class: 'w-full',
            optionLabel: 'label',
            optionValue: 'value'
        }
    },
    {
        label: 'Effective date',
        field: 'endorsement_effective_date',
        required: true,
        component: 'Calendar',
        props: {
            placeholder: "Effective date",
            showIcon: true,
            class: 'w-full',
            dateFormat: "dd/M/yy"
        }
    },
    {
        label: 'Description',
        field: 'endorsement_description',
        component: 'Textarea',
        props: {
            placeholder: "Description",
            rows: 5,
            class: 'w-full'
        }
    }
])
const loadSelection = () => {
    state.loading = true
    paService.endorsementTypes().then(res => {
        state.endorsementTypes = res.data
    }).finally(() => state.loading = false)
}
const toggleDialog = () => {
    state.visible = !state.visible
}
const handleSubmit = () => {
    emit('submit', formValues.value)
}
defineExpose({
    toggleDialog
})
</script>