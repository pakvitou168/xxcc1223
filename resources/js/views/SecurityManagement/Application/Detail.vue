<template>
    <div>
        <Dialog v-model:visible="visible" modal class="md:w-[32rem] bg-white" position="top" :draggable="false"
            @show="loadSelection" @hide="clearForm" :closable="false">
            <template #container>
                <div class="flex w-full border-b pl-2 pr-2 py-2">
                    <h4>{{ title }}</h4>
                    <div class="flex-1 text-right">
                        <ButtonGroup>
                            <Button v-for="action in actions" :label="action.label" :icon="action.icon" v-show="action.visible !== undefined ? action.visible : true"
                                v-bind="action.props" @click="action.command" />
                        </ButtonGroup>
                    </div>
                </div>
                <div v-if="loading" class="grid min-h-[20rem]">
                    <ProgressSpinner class="w-16 h-16 top-[40%]" strokeWidth="8" fill="var(--surface-ground)"
                        animationDuration=".5s" aria-label="Custom ProgressSpinner" />
                </div>
                <div class="grid gap-5 p-5" v-else>
                    <div v-for="field in formFields">
                        <label for="" class="form-label">{{ field.label }}</label>
                        <component :is="field.component" v-model="formValue[field.field]" v-bind="field.props" />
                        <span class="text-error" v-if="errors[field.field]">{{ errors[field.field][0] }}</span>
                    </div>
                </div>
                <div class="mb-6"></div>
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { computed, onMounted, reactive, ref } from 'vue';
import applicaitonService from '@/services/sm/applicaiton.service';
import smService from '@/services/sm/sm.service';

const loading = ref(true)
const saving = ref(false)
const errors = ref({})
const visible = ref(false)
const route = useRoute();
const id = computed(() => {
    return route.params.id
})

const title = computed(() => {
    return route.params.id ? `App : ${formValue.value?.name}` : 'New App'
});
const initForm = {
    code: '',
    name: '',
    status: ''
}
const selections = reactive({
    statuses: []
})
const props = defineProps({
})
const emit = defineEmits(['success', 'close'])
const formValue = ref({
    ...initForm
})
const defaultFormValue = ref({
    ...initForm
})
const actions = computed(() => {
    return [
        {
            label: 'Close',
            icon: 'pi pi-times',
            props: {
                class: 'border px-2 py-1 bg-slate-100 text-sm uppercase rounded',
                disabled: false
            },
            command: () => { handleClose() }
        },
        {
            label: id.value ? 'Update' : 'Save',
            icon: 'pi pi-save',
            props: {
                class: 'border px-2 py-1 bg-blue-500 text-white text-sm uppercase rounded',
                disabled: false,
                autoFocus:true,
                loading: saving.value
            },
            command: () => { handleSubmit() }
        }
    ]
})

const formFields = computed(() => {
    return [
        {
            field: 'code',
            label: 'App Code',
            component: 'InputText',
            props: {
                class: 'w-full',
                placeholder: 'App code',
                disabled:id.value
            }
        },
        {
            field: 'name',
            label: 'Name',
            component: 'InputText',
            props: {
                class: 'w-full',
                placeholder: 'App name'
            }
        },
        {
            field: 'status',
            label: 'Status',
            component: 'Dropdown',
            props: {
                class: 'w-full',
                placeholder: 'Select an option',
                optionLabel: 'label',
                optionValue: 'value',
                options: selections.statuses,
                showClear: true
            }
        },
    ]
})

const toggleDialog = (refId = null) => {
    visible.value = !visible.value
    if (refId) loadDetail(refId)
    else loading.value = false
}
const loadDetail = (refId) => {
    applicaitonService.detail(refId).then(res => {
        formValue.value = res.data?.application
        defaultFormValue.value = JSON.parse(JSON.stringify(res.data?.application))
    }).finally(() => loading.value = false)
}
const formHasChanges = () => {
    console.log(JSON.stringify(formValue.value), JSON.stringify(defaultFormValue.value))
    return JSON.stringify(formValue.value) !== JSON.stringify(defaultFormValue.value)
}
const handleSubmit = () => {
    if (!formHasChanges()) {
        notify("Nothing changes", 'warn')
        return false
    }
    saving.value = true
    if (id.value) {
        applicaitonService.update(formValue.value, formValue.value.id).then((res) => {
            notify(res.data.message, 'success')
            emit('success', 'update')
        }).catch(err => {
            if (err.status === 422) errors.value = err.response.data.errors
            else notify(err.message, 'error')
        }).finally(() => saving.value = false)
    } else {
        applicaitonService.save(formValue.value).then((res) => {
            notify(res.data.message, 'success')
            emit('success')
        }).catch(err => {
            if (err.status === 422) errors.value = err.response.data.errors
            else notify(err.message, 'error')
        }).finally(() => saving.value = false)
    }

}
const loadStatus = () => {
    smService.status().then(res => {
        selections.statuses = res.data
    })
}
const clearForm = () => {
    formValue.value = {}
    loading.value = true
}
const loadSelection = async () => {
    await loadStatus()
}
const handlePageUrl = () => {
    if (!visible.value && id.value) toggleDialog(id.value)
}
const handleClose = () => {
    toggleDialog()
    if (id.value) {
        emit('close')
    }
}
defineExpose({
    toggleDialog
})
onMounted(() => {
    handlePageUrl()
})
</script>

<style scoped>
.p-dialog-header {
    @apply border-b bg-black !important
}
</style>