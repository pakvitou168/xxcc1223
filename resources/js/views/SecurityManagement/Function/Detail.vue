<template>
    <div>
        <Dialog v-model:visible="visible" modal class="md:w-[52rem] bg-white sm:mt-4 md:mt-8 lg:mt-12 p-0"
            position="top" :draggable="false" :pt="{
                header: 'p-0'
            }" @show="loadSelection" @hide="clearForm" :closable="false">
            <template #header>
                <div class="flex w-full border-b pl-2 pr-2 py-2">
                    <h4>{{ title }}</h4>
                    <div class="flex-1 text-right">
                        <ButtonGroup>
                            <Button v-for="action in actions" :label="action.label" :icon="action.icon"
                                v-show="action.visible !== undefined ? action.visible : true" v-bind="action.props"
                                @click="action.command" />
                        </ButtonGroup>
                    </div>
                </div>
            </template>
            <div v-if="loading" class="grid min-h-[20rem]">
                <ProgressSpinner class="w-16 h-16 top-[40%]" strokeWidth="8" fill="var(--surface-ground)"
                    animationDuration=".5s" aria-label="Custom ProgressSpinner" />
            </div>
            <div class="w-full mt-5" v-else>
                <div class="grid grid-cols-2 gap-5">
                    <div v-for="field in formFields" :class="field.containerClass+' grid'">
                        <label for="" class="form-label">{{ field.label }} <span v-if="field.required">*</span></label>
                        <component :is="field.component" v-model="formValue[field.field]" v-bind="field.props" />
                        <span class="text-error" v-if="errors[field.field]">{{ errors[field.field][0] }}</span>
                    </div>
                </div>
            </div>
            <div class="mb-6"></div>
        </Dialog>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { computed, nextTick, onMounted, reactive, ref } from 'vue';
import fncService from '@/services/sm/fnc.service';
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
    status: 'ACT'
}
const selections = reactive({
    statuses: [],
    permissions: [],
    apps: []
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
                autoFocus: true,
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
            label: 'Code',
            component: 'InputText',
            required: true,
            props: {
                class: 'w-full',
                placeholder: 'App code',
                disabled:id.value
            }
        },
        {
            field: 'name',
            label: 'Name',
            required: true,
            component: 'InputText',
            props: {
                class: 'w-full',
                placeholder: 'App name'
            }
        },
        {
            field: 'app_id',
            label: 'App',
            component: 'Dropdown',
            required: true,
            props: {
                class: 'w-full',
                placeholder: 'Select an option',
                optionLabel: 'name',
                optionValue: 'id',
                options: selections.apps,
                showClear: true
            }
        },
        {
            field: 'status',
            label: 'Status',
            component: 'Dropdown',
            required: true,
            props: {
                class: 'w-full',
                placeholder: 'Select an option',
                optionLabel: 'label',
                optionValue: 'value',
                options: selections.statuses,
                showClear: true
            }
        },
        {
            field: 'permissions',
            label: 'Permissions',
            component: 'MultiSelect',
            required: true,
            containerClass:'col-span-2',
            props: {
                class: 'w-full max-w-full overflow-hidden',
                display: 'chip',
                placeholder: 'Select options',
                optionLabel: 'label',
                optionValue: 'value',
                options: selections.permissions,
                appendTo: "body"
            }
        },
        
    ]
})

const toggleDialog = (refId = null) => {
    visible.value = !visible.value
    if (refId) loadDetail(refId)
    else loading.value = false
}
const loadDetail = async (refId) => {
    fncService.detail(refId).then(res => {
        formValue.value = res.data?.functions
        defaultFormValue.value = JSON.parse(JSON.stringify(res.data?.functions))
    }).finally(async () => { loading.value = false; await nextTick() })
}
const formHasChanges = () => {
    return JSON.stringify(formValue.value) !== JSON.stringify(defaultFormValue.value)
}
const handleSubmit = () => {
    if (!formHasChanges()) {
        notify("Nothing changes", 'warn')
        return false
    }
    saving.value = true
    if (id.value) {
        fncService.update(formValue.value, formValue.value.id).then((res) => {
            notify(res.data.message, 'success')
            emit('success', 'update')
        }).catch(err => {
            if (err.status === 422) errors.value = err.response.data.errors
            else notify(err.message, 'error')
        }).finally(() => saving.value = false)
    } else {
        fncService.save(formValue.value).then((res) => {
            notify(res.data.message, 'success')
            emit('success')
        }).catch(err => {
            if (err.status === 422) errors.value = err.response.data.errors
            else notify(err.message, 'error')
        }).finally(() => saving.value = false)
    }

}
const loadStatus = () => {
    return smService.status().then(res => {
        selections.statuses = res.data
    })
}
const clearForm = () => {
    formValue.value = {}
    loading.value = true
}
const loadSelection = async () => {
}
const loadPermission = async () => {
    await fncService.permission().then(res => {
        selections.permissions = res.data
    })
}
const loadApp = async () => {
    await fncService.app().then(res => {
        selections.apps = res.data
    })
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
onMounted(async() => {
    handlePageUrl()
    loadStatus()
    loadPermission()
    loadApp()
})
</script>