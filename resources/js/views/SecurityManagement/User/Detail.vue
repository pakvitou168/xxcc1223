<template>
    <div>
        <Dialog v-model:visible="visible" modal class="md:w-9/12 lg:w-7/12 bg-white sm:mt-4 md:mt-8 lg:mt-12 p-0"
            position="top" :draggable="false" :pt="{
                header: 'p-0'
            }" @show="loadSelection" @hide="clearForm" :closable="false">
            <template #header>
                <div class="flex w-full border-b pl-2 pr-2 py-2">
                    <h4>{{ title }}</h4>
                    <div class="flex flex-1 justify-end">
                        <ButtonGroup>
                            <Button v-for="action in actions" :label="action.label" :icon="action.icon"
                                v-show="action.visible !== undefined ? action.visible : true" v-bind="action.props"
                                @click="action.command" />
                        </ButtonGroup>
                    </div>
                </div>
            </template>
            <div v-if="loading" class="grid min-h-[20rem]">
                <ProgressSpinner class="w-16 h-16 top-[40%]" strokeWidth="6" fill="var(--surface-ground)"
                    animationDuration="1s" aria-label="Custom ProgressSpinner" />
            </div>
            <div class="w-full mt-5 bg-gray-50 p-4 rounded" v-else>
                <div class="grid grid-cols-2 gap-5">
                    <div v-for="field in formFields" :class="field.containerClass + ' grid'">
                        <label for="" class="form-label">{{ field.label }} <span v-if="field.required">*</span></label>
                        <component v-if="field.component" :is="field.component" v-model="formData[field.field]"
                            v-bind="field.props" />
                        <span class="text-error" v-if="errors[field.field]">{{ errors[field.field][0] }}</span>
                        <div v-else></div>
                    </div>
                </div>
            </div>
            <div class="w-full mt-4 bg-gray-100 p-4 rounded" v-if="!loading">
                <h4>Authorization</h4>
                <div class="card bg-gray-100 mt-3">
                    <TabView v-model:activeIndex="activeStep" @tab-click="changeTab">
                        <TabPanel>
                            <template #header>
                                <div :class="{ 'border-b-2 border-cyan-600 pb-1': activeStep === 0 }">Groups</div>
                            </template>
                            <PermissionChips placeholder="No groups selected." :items="formData.groups"
                                :loading="state.groupsListLoading"
                                @remove="groupId => formData.groups = formData.groups.filter(item => item.id !== groupId)" />

                            <SingleTree v-model="formData.groups" :options="state.dropdownList.groupsList"
                                :loading="state.groupsListLoading" />
                        </TabPanel>
                        <TabPanel>
                            <template #header>
                                <div :class="{ 'border-b-2 border-cyan-600 pb-1': activeStep === 1 }">Roles</div>
                            </template>
                            <PermissionChips placeholder="No roles selected." :items="formData.roles"
                                :loading="state.rolesListLoading"
                                @remove="roleId => formData.roles = formData.roles.filter(item => item.id !== roleId)" />

                            <SingleTree v-model="formData.roles" :options="state.dropdownList.rolesList"
                                :loading="state.rolesListLoading" />
                        </TabPanel>
                        <TabPanel>
                            <template #header>
                                <div :class="{ 'border-b-2 border-cyan-600 pb-1': activeStep === 2 }">Functions</div>
                            </template>
                            <PermissionChips placeholder="No functions selected." :items="permissionChipItems"
                                :loading="state.functionListLoading" @remove="removeFunction" />

                            <PermissionSelect v-if="state.dropdownList.permissionsList?.length"
                                v-model="formData.permissions" :permissionsList="state.dropdownList.permissionsList" />
                        </TabPanel>
                        <TabPanel>
                            <template #header>
                                <div :class="{ 'border-b-2 border-cyan-600 pb-1': activeStep === 3 }">Branches</div>
                            </template>
                            <PermissionChips placeholder="No organizations selected." :items="organizationChipItems"
                                :loading="state.organizationsListLoading" @remove="removeOrganization" />

                            <OrganizationSelect v-if="state.dropdownList.organizationsList.length"
                                v-model="formData.organizations"
                                :organizationsList="state.dropdownList.organizationsList" />
                        </TabPanel>
                    </TabView>
                </div>
            </div>
            <div class="mb-6"></div>
        </Dialog>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { computed, nextTick, onMounted, reactive, ref } from 'vue';
import userService from '@/services/sm/user.service';
import smService from '@/services/sm/sm.service';
import SingleTree from '@/components/Form/SingleTree.vue';
import PermissionChips from './Components/PermissionChips.vue';
import OrganizationSelect from '../Components/OrganizationSelect.vue'
import PermissionSelect from '../Components/PermissionSelect.vue'
import { listTreePermissionOptions } from '../Components/permission.select.service.js'
import { listTreeOrganizationOptions } from '../Components/organization.select.service.js'

const emit = defineEmits(["success", "close"])
const loading = ref(true)
const saving = ref(false)
const errors = ref({})
const visible = ref(false)
const route = useRoute();
const id = computed(() => {
    return route.params.id
})
const activeStep = ref(0)

const title = computed(() => {
    return route.params.id ? `Group : ${formData.value?.name}` : 'New Group'
});
const initForm = {
    code: '',
    name: '',
    status: 'ACT',
    groups: [],
    roles: [],
    permissions: [],
    organizations: [],
    status: "ACT",
    authenticator: false
}
const selections = reactive({
    statuses: [],
    branches: []
})

const formData = ref({
    ...initForm
})
const defaultFormData = ref({
    ...initForm
})
const state = reactive({
    loading: false,
    groupsListLoading: false,
    rolesListLoading: false,
    functionListLoading: false,
    organizationsListLoading: false,
    dropdownList: {
        status: [],
        groupsList: [],
        rolesList: [],
        permissionsList: [],
        organizationsList: [],
        authenticatorList: [],
        orgBranchList: []
    },
});
const treePermissionsList = computed(() => listTreePermissionOptions(state.dropdownList.permissionsList))
const permissionChipItems = computed(() => {
    let chips = [];

    let permissionIds = formData.value?.permissions.map(item => item.id)

    treePermissionsList.value.forEach(app => {
        app.children?.forEach(fnc => {
            if (fnc.children?.some(item => permissionIds.includes(item.data))) {
                chips.push({
                    id: fnc.data,
                    name: `${app?.label}: ${fnc.label}`
                })
            }
        })
    })

    return chips
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
            label: "Username",
            field: "username",
            required: true,
            component: "InputText",
            props: {
                class: `w-full`,
                placeholder: "Username"
            },
        },
        {
            label: "Full Name",
            field: "full_name",
            required: true,
            component: "InputText",
            props: {
                class: `w-full`,
                placeholder: 'Full name'
            },
        },
        {
            label: "Email",
            field: "email",
            required: true,
            component: "InputText",
            props: {
                class: `w-full`,
                placeholder: 'Email'
            },
        },
        {
            label: "Status",
            field: "status",
            required: true,
            component: "Dropdown",
            props: {
                optionValue: "value",
                optionLabel: "label",
                options: state.dropdownList.status,
                class: `w-full`,
                placeholder: "Select an option"
            },
        },
        {
            label: "Home Branch",
            field: "home_branch",
            required: false,
            component: "Dropdown",
            props: {
                optionValue: "code",
                optionLabel: "name",
                options: state.dropdownList.orgBranchList,
                hasFilter: true,
                filter: true,
                class: `w-full`,
                placeholder: "Select an option"
            },
        },
        {
            label: ''
        },
        {
            label: "Password",
            field: "password",
            required: formData.value.id ? false : true,
            component: "InputText",
            disabled: formData.value.id ? true : false,
            props: {
                type: 'password',
                class: `w-full`,
                autocomplete: 'new-password',
                placeholder: 'Password'
            },

        },
        {
            label: "Confirm Password",
            field: "password_confirmation",
            required: formData.value.id ? false : true,
            disabled: formData.value.id ? true : false,
            component: "InputText",
            props: {
                type: 'password',
                class: `w-full`,
                placeholder: "Confirm password"
            },
        },
    ]
})
const treeOrganizationsList = computed(() => listTreeOrganizationOptions(state.dropdownList.organizationsList))
const organizationChipItems = computed(() => {
  let chips = [];

  let orgIds = formData.value.organizations.map(item => item.id)
  treeOrganizationsList.value.forEach(org => {
    if (orgIds.includes(org.key)) {
      chips.push({
        id: org.key,
        name: org?.label
      })
    }
  })

  return chips
})

const removeOrganization = orgId => {
  let controlOrganizations = formData.value.organizations
  formData.value.organizations = controlOrganizations.filter(org => org.id !== orgId)
}
const removeFunction = functionId => {
  let controlPermissions = formData.value.permissions
  treePermissionsList.value.forEach(app => {
    app.children?.forEach(fnc => {
      if (fnc.data === functionId) {
        formData.value.permissions = controlPermissions.filter(item => !fnc.children?.map(item => item.data).includes(item.id))
      }
    })
  })
}
const toggleDialog = (refId = null) => {
    visible.value = !visible.value
    if (refId && visible.value) loadDetail(refId)
    else loading.value = false
}
const loadDetail = async (refId) => {
    userService.detail(refId).then(res => {
        formData.value = res.data?.user
        defaultFormData.value = JSON.parse(JSON.stringify(res.data?.user))
    }).finally(async () => { loading.value = false; })
}
const formHasChanges = () => {
    return JSON.stringify(formData.value) !== JSON.stringify(defaultFormData.value)
}
const handleSubmit = () => {
    if (!formHasChanges()) {
        notify("Nothing changes", 'warn')
        return false
    }
    saving.value = true
    if (id.value) {
        userService.update(formData.value, id.value).then(res => {
            emit('success', 'update')
            notify(res.data.message, 'success')
        }).catch(err => {
            if (err.status === 422) errors.value = err.response.data.errors
            else notify(err.message, 'error')
        }).finally(() => saving.value = false)
    } else {
        userService.save(formData.value).then(res => {
            notify(res.data.message, 'success')
            emit('success')
        }).catch(err => {
            if (err.status === 422) errors.value = err.response.data.errors
            else notify(err.message, 'error')
        }).finally(() => saving.value = false)
    }

}
const loadStatuses = () => {
    smService.status().then(res => {
        state.dropdownList.status = res.data
    })
}
const clearForm = () => {
    formData.value = initForm
    loading.value = true
}
const loadSelection = async () => {
    if (!state.dropdownList?.status.length) {
        loadStatuses();
    }

    if (!state.dropdownList?.orgBranchList?.length) {
        listOrgBranches();
    }
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
const listOrgBranches = () => {
    userService.listBranchesByOrg().then(res => {
        state.dropdownList.orgBranchList = res.data
    })
}
const listGroups = () => {
    if (!state.dropdownList.groupsList.length) {
        state.groupsListLoading = true

        userService.listGroups().then(res => state.dropdownList.groupsList = res.data)
            .finally(() => state.groupsListLoading = false)
    }
}
const listRoles = () => {
    if (!state.dropdownList.rolesList.length) {
        state.rolesListLoading = true

        userService.listRoles().then(res => state.dropdownList.rolesList = res.data)
            .finally(() => state.rolesListLoading = false)
    }
}

const listPermissions = () => {
    if (!state.dropdownList.permissionsList.length) {
        state.functionListLoading = true
        userService.listPermissions().then(res => state.dropdownList.permissionsList = res.data)
            .finally(() => state.functionListLoading = false)
    }
}
const listOrganizations = () => {
    if (!state.dropdownList.organizationsList.length) {
        state.organizationsListLoading = true

        userService.listOrganizations().then(res => state.dropdownList.organizationsList = res.data)
            .finally(() => state.organizationsListLoading = false)
    }
}
const changeTab = ({ index }) => {
    if (index === 0) listGroups()
    else if (index === 1) listRoles()
    else if (index === 2) listPermissions()
    else if (index === 3) listOrganizations()
}
defineExpose({
    toggleDialog
})
onMounted(async () => {
    await nextTick();
    changeTab({index: activeStep.value})
    handlePageUrl()
    loadSelection();
})
</script>