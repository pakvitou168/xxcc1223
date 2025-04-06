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
                    <div v-for="field in formFields" :class="field.containerClass + ' grid'">
                        <label for="" class="form-label">{{ field.label }} <span v-if="field.required">*</span></label>
                        <component :is="field.component" v-model="formValue[field.field]" v-bind="field.props" v-if="field.visible !== undefined ? field.visible : true"
                            @[field?.event1]="field?.event1Handler" />
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
import roleService from '@/services/sm/role.service';
import smService from '@/services/sm/sm.service';
import PermissionSelect from '../Components/PermissionSelect.vue';

const emit = defineEmits(["success", "close"])
const loading = ref(true)
const saving = ref(false)
const errors = ref({})
const visible = ref(false)
const route = useRoute();
const selectedAppNames = ref('')
const id = computed(() => {
    return route.params.id
})

const title = computed(() => {
    return route.params.id ? `Role : ${formValue.value?.name}` : 'New Role'
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

const formValue = ref({
    ...initForm
})
const defaultFormValue = ref({
    ...initForm
})

const assignSelectedAppNames = (appNames) => {
    selectedAppNames.value = appNames.join(', ')
}
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
                placeholder: 'App code'
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
            field: "permissions",
            label: 'Permissions',
            component: PermissionSelect,
            required: true,
            containerClass: 'col-span-2',
            visible: selections.permissions.length,
            props: {
                class: '',
                permissionsList: selections.permissions
            },
            event1: 'get:selectedAppNames',
            event1Handler: (appNames) => {
                assignSelectedAppNames(appNames)
            }
        }
    ]
})

const toggleDialog = (refId = null) => {
    visible.value = !visible.value
    if (refId && visible.value) loadDetail(refId)
    else loading.value = false
}
const loadDetail = async (refId) => {
    roleService.detail(refId).then(res => {
        formValue.value = res.data?.role
        defaultFormValue.value = JSON.parse(JSON.stringify(res.data?.role))
    }).finally(async () => { loading.value = false; })
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
        roleService.update(formValue.value, id.value).then(res => {
            emit('success', 'update')
            notify(res.data.message, 'success')
        }).catch(err => {
            if (err.status === 422) errors.value = err.response.data.errors
            else notify(err.message, 'error')
        }).finally(() => saving.value = false)
    } else {
        roleService.save(formValue.value).then(res => {
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
    await loadPermission();
}
const loadPermission = async () => {
    await roleService.permission().then(res => {
        selections.permissions = res.data
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
onMounted(async () => {
    await nextTick();
    handlePageUrl()
    await loadStatus();
})
</script>