<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                User
            </h2>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12 lg:col-span-6">
                <div v-if="id" class="flex items-center px-5 pt-3 pb-0 border-b border-gray-200">
                    <div class="nav nav-tabs flex-col sm:flex-row justify-center lg:justify-start" role="tablist">
                        <a id="user-tab" data-toggle="tab" data-target="#user" href="javascript:;" class="py-3 sm:mr-8 active" role="tab" aria-selected="true">User</a>
                        <a id="roles-tab" data-toggle="tab" data-target="#roles" href="javascript:;" class="py-3 sm:mr-8" role="tab" aria-selected="false">Roles</a>
                        <a id="permissions-tab" data-toggle="tab" data-target="#permissions" href="javascript:;" class="py-3 sm:mr-8" role="tab" aria-selected="false">Permissions</a>
                        <a id="branch-tab" data-toggle="tab" data-target="#branch" href="javascript:;" class="py-3 sm:mr-8" role="tab" aria-selected="false">Branch</a>
                    </div>
                </div>
                <div class="p-5">
                    <FormulateForm v-model="formValues" @submit="handleSubmit">
                        <div class="tab-content">
                            <div id="user" class="tab-pane active" role="tabpanel" aria-labelledby="user-tab">
                                <div class="grid grid-cols-12 gap-x-6">
                                    <div class="col-span-12 lg:col-span-6">
                                        <FormulateInput
                                            type="text"
                                            name="username"
                                            validationName="Username"
                                            label="Username *"
                                            validation="required"
                                            placeholder="Username" />
                                    </div>
                                    <div class="col-span-12 lg:col-span-6">
                                        <FormulateInput
                                            type="text"
                                            name="full_name"
                                            validationName="Full Name"
                                            label="Full Name *"
                                            validation="required"
                                            placeholder="Full Name" />
                                    </div>
                                    <div class="col-span-12 lg:col-span-6">
                                        <FormulateInput
                                            type="email"
                                            name="email"
                                            validationName="Email"
                                            label="Email *"
                                            validation="bail|required|email"
                                            placeholder="Email" />
                                    </div>
                                    <div class="col-span-12 lg:col-span-6">
                                        <FormulateInput
                                            type="select"
                                            name="home_branch"
                                            validationName="Home branch"
                                            label="Home branch *"
                                            validation="required"
                                            placeholder="Home branch"
                                            :options="branchOptions" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-1">
                                    <FormulateInput
                                        type="checkbox"
                                        name="is_ad"
                                        label="Is AD" />
                                </div>
                                <div class="grid grid-cols-12 gap-x-6">
                                    <div class="col-span-12 lg:col-span-6">
                                        <FormulateInput
                                            type="password"
                                            name="password"
                                            label="Password"
                                            placeholder="Password"
                                            validationName="Password"
                                            validation="optional|confirm:password_confirm"
                                            error-behavior="submit"
                                            :validation-messages="{
                                                confirm: ''
                                            }" />
                                    </div>
                                    <div class="col-span-12 lg:col-span-6">
                                        <FormulateInput
                                            type="password"
                                            name="password_confirm"
                                            label="Confirm your password"
                                            placeholder="Confirm your password"
                                            validationName="Confirm your password"
                                            validation="optional|confirm"
                                            error-behavior="submit" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-x-6">
                                    <div class="col-span-12 lg:col-span-6">
                                        <FormulateInput
                                            type="vue-select"
                                            name="groups"
                                            label="Groups"
                                            placeholder="Groups"
                                            :multiple="true"
                                            :options="groupOptions" />
                                    </div>
                                    <!-- Only show in edit -->
                                    <div v-if="id" class="col-span-12 lg:col-span-6">
                                        <FormulateInput
                                            v-if="id"
                                            type="select"
                                            name="status"
                                            placeholder="Status"
                                            label="Status *"
                                            validation="required"
                                            validationName="Status"
                                            :options="statusOptions"
                                        />
                                    </div>
                                    <div class="col-span-12 lg:col-span-6">
                                        <FormulateInput
                                            type="image"
                                            name="signature"
                                            label="Signature"
                                            help="Select a png, jpg."
                                            validation="mime:image/jpeg,image/png"
                                            @change="signature = $event.target.files[0]" />
                                    </div>
                                </div>
                            </div>
                            <div id="roles" class="tab-pane" role="tabpanel" aria-labelledby="roles-tab">
                                <div class="mb-3">
                                    <label class="block mb-2 font-bold">Roles</label>
                                    <treeselect
                                        v-model="rolesVal"
                                        :multiple="true"
                                        :options="roleOptions"
                                        :disable-branch-nodes="true"
                                        search-nested
                                        />
                                </div>
                            </div>
                            <div id="permissions" class="tab-pane" role="tabpanel" aria-labelledby="permissions-tab">
                                <div class="mb-3">
                                    <label class="block mb-2 font-bold">Permissions</label>
                                    <treeselect
                                        v-model="permissionVal"
                                        :multiple="true"
                                        :options="permissionOptions"
                                        search-nested
                                        />
                                </div>
                            </div>
                            <div id="branch" class="tab-pane" role="tabpanel" aria-labelledby="branch-tab">
                                <FormulateInput
                                    name="branches"
                                    type="vue-select"
                                    :multiple="true"
                                    :options="branchOptions"
                                    placeholder="Select an option"
                                    />
                           </div>
                        </div>
                        <div class="text-right mt-3">
                            <router-link to="/user-management/users" class="btn btn-outline-secondary w-24 mr-1" tag="button">Cancel</router-link>
                            <button type="submit" class="btn btn-primary w-24">
                                <span v-if="id">Update</span>
                                <span v-else>Create</span>
                            </button>
                        </div>
                    </FormulateForm>
                </div>
            </div>
        </div>
        <MessageToast />
    </div>
</template>

<script>

import axios from 'axios'
import Treeselect from '@riophae/vue-treeselect'
import MessageToast from '../../../components/Toast/MessageToast'

export default {
    components: {
        Treeselect,
        MessageToast,
    },
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {},
            branchOptions: {},
            groupOptions: {},
            statusOptions: {},
            rolesVal: null,
            roleOptions: [],
            permissionVal: null,
            permissionOptions: [],
            signature: null,
        }
    },
    computed: {
        formData() {
            var formData = new FormData()
                
            formData.append('username', this.formValues.username)
            formData.append('full_name', this.formValues.full_name)
            formData.append('email', this.formValues.email)
            formData.append('home_branch', this.formValues.home_branch)
            formData.append('is_ad', this.formValues.is_ad ?? false)
            formData.append('password', this.formValues.password ?? '')
            formData.append('groups', JSON.stringify(this.formValues.groups ?? []))
            if (this.formValues.status)
                formData.append('status', this.formValues.status)

            return formData
        }
    },
    methods: {
        getOptions() {
            axios.get('/user-service/get-service').then(response => {
                this.branchOptions = response.data.branch
                this.groupOptions = response.data.group
                this.statusOptions = response.data.statuses
            })
        },
        getEdit() {
            if (this.id) {
                axios.get('/users/' + this.id).then(response => {
                    this.formValues = response.data
                    this.formValues.full_name = response.data.full_name
                    this.formValues.branches = response.data.branches.PGI
                })
            }
        },
        handleSubmit() {
            if (!this.id) {
                var formData = this.formData
                if(this.signature !== null){
                    formData.append('signature', this.signature)
                }

                axios.post('/users', formData).then(response => {
                    if (response.data.code == 200) {
                        this.$router.push({name: 'users', params: { result: response.data.msg }})
                    } else {
                        this.$notify({
                            group: 'bottom',
                            title: 'Error',
                            text: response.data.msg,
                        }, 4000)
                    }
                })
            } else {
                var formData = this.formData
                if(this.signature !== null){
                    formData.append('signature', this.signature)
                }
                
                formData.append('roles', JSON.stringify(this.rolesVal))
                formData.append('permissions', JSON.stringify(this.permissionVal))
                formData.append('branches', JSON.stringify(this.formValues.branches))
                
                formData.append("_method", "put")

                axios.post(`/users/${this.id}`, formData).then(response => {
                    if (response.data.code == 200) {
                        this.$router.push({name: 'users', params: { result: response.data.msg }})
                    } else {
                        this.$notify({
                            group: 'bottom',
                            title: 'Error',
                            text: response.data.msg,
                        }, 4000)
                    }
                })
            }
        },

        role() {
            if (this.id) {
                axios.get('/user-service/roles/' + this.id).then(response => {
                    if (response.data.roleAssigned) {
                        this.rolesVal = response.data.roleAssigned
                    } else {
                        this.rolesVal = []
                    }
                    this.roleOptions = response.data.role
                })
            }
        },

        permissions() {
            if (this.id) {
                axios.get('/user-service/permission/' + this.id).then(response => {
                    if (response.data.assign) {
                        this.permissionVal = response.data.assign
                    } else {
                        this.permissionVal = []
                    }
                    this.permissionOptions = response.data.permission
                })
            }
        },
    },
    mounted() {
        this.getEdit()
        this.getOptions()
        this.role()
        this.permissions()
    }
}
</script>