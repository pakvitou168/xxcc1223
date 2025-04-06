<template>
    <div>
        <MessageToast />
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Group
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-6">
                <div class="intro-y box p-5">
                    <FormulateForm v-model="formValues" @submit="handleSubmit">
                        <div class="grid grid-cols-6 gap-x-10 gap-y-1">
                            <div class="col-span-12 lg:col-span-6">
                                <FormulateInput
                                    type="text"
                                    name="code"
                                    label="Group Code *"
                                    validation="required"
                                    validationName="Group Code"
                                    placeholder="Group Code"
                                />
                                <FormulateInput
                                    type="text"
                                    name="name"
                                    label="Group Name *"
                                    validation="required"
                                    validationName="Group Name"
                                    placeholder="Group Name"
                                />
                                <FormulateInput
                                    type="textarea"
                                    name="description"
                                    label="Description"
                                    placeholder="Description"
                                    rows="4"
                                />

                                <div class="mb-3">
                                    <label class="block mb-2 font-bold">Choose Application and Role</label>
                                    <treeselect v-model="permissions" :multiple="true" :options="permissionOptions"/>
                                </div>

                                <div class="text-right mt-5">
                                    <router-link to="/user-management/groups" class="btn btn-outline-secondary w-24 mr-1" tag="button">Cancel</router-link>
                                    <button type="submit" class="btn btn-primary w-24">
                                        <span v-if="id">Update</span>
                                        <span v-else>Create</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </FormulateForm>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import MessageToast from '../../../components/Toast/MessageToast.vue'

export default {
    components: { Treeselect, MessageToast },
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {},
            permissions: [],
            permissionOptions: [],
        }
    },
    methods: {
        getPermissionOptions() {
            axios.get('/group-service/get-group-permissions').then( response => this.permissionOptions = response.data)
        },

        handleSubmit() {
            this.formValues.permissions = this.permissions
            if (!this.id) {
                axios.post('/groups', this.formValues).then(response => {
                    if(response.data.success) {
                        this.toastMessage(response.data.message, 'Success')
                        this.$router.push({name:"GroupIndex"})
                    }
                }).catch(() => this.toastMessage('Error', 'Error'))
            } else {
                axios.put(`/groups/${this.id}`, this.formValues).then(response => {
                    if(response.data.success) {
                        this.toastMessage(response.data.message, 'Success')
                        this.$router.push({name:"GroupIndex"})
                    }
                }).catch(() => this.toastMessage('Error', 'Error'))
            }
        },

        getGroup() {
            if (this.id) {
                axios.get(`/groups/${this.id}/edit`).then(response => {
                    this.formValues = response.data
                    this.permissions = this.formValues.permissions
                })
            }
        },

        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        },
    },
    mounted() {
        this.getGroup()
        this.getPermissionOptions()
    },
}
</script>