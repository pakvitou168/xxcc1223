<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Role
            </h2>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12 lg:col-span-6 p-5">
                <FormulateForm v-model="formValues" @submit="handleSubmit">
                    <FormulateInput
                        type="text"
                        name="code"
                        label="Role Code *"
                        validation="required|max:16"
                        validationName="Role Code"
                        placeholder="Role Code"
                        :error="errors.code ? errors.code[0] : null"
                    />
                    <FormulateInput
                        type="text"
                        name="name"
                        label="Role Name *"
                        validation="required"
                        validationName="Role Name"
                        placeholder="Role Name"
                    />
                    <FormulateInput
                        type="select"
                        name="app_code"
                        label="App Code *"
                        validation="required|max:100"
                        validationName="App Code"
                        placeholder="App Code"
                        :options="appCodeOptions"
                        @input="changeAppCode"
                    />
                    <div class="mb-3">
                        <label class="block mb-2 font-bold">Permission *</label>
                        <Treeselect v-model="permissions" :multiple="true" :options="permissionOptions"/>
                        <ul v-if="errors.permissions" class="formulate-input-errors">
                            <li class="formulate-input-error">{{ errors.permissions[0] }}</li>
                        </ul>
                    </div>
                    <FormulateInput
                        v-if="id"
                        type="select"
                        name="status"
                        placeholder="Status"
                        label="Status *"
                        validation="required"
                        validationName="Status"
                        :options ="statusOptions"
                    />
                    <FormulateInput
                        type="textarea"
                        name="description"
                        label="Description"
                        placeholder="Description"
                        rows="4"
                    />
                    <div class="text-right mt-5">
                        <router-link :to="{ name: 'RoleIndex'}" class="btn btn-outline-secondary w-24 mr-1" tag="button">Cancel</router-link>
                        <button type="submit" class="btn btn-primary w-24">
                            <span v-if="id">Update</span>
                            <span v-else>Create</span>
                        </button>
                    </div>
                </FormulateForm>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {},
            permissions: [],
            appCodeOptions: {},
            permissionOptions: [],
            statusOptions: {},
            errors: [],
        }
    },

    methods: {
        handleSubmit() {
            this.formValues.permissions = this.permissions
            if (!this.id) {
                axios.post('/roles', this.formValues).then(response => {
                    if (response.data.success) {
                        this.toastMessage(response.data.message, 'Success')
                        this.$router.push({ name: 'RoleIndex'})
                    }
                }).catch(err => {
                    if (err.response.status = 422) {
                        this.errors = err.response.data.errors
                    } else {
                        this.toastMessage('Error', 'Error')
                    }
                })
            } else {
                axios.put(`/roles/${this.id}`, this.formValues).then(response => {
                    if (response.data.success) {
                        this.toastMessage(response.data.message, 'Success')
                        this.$router.push({ name: 'RoleIndex'})
                    }
                }).catch(err => {
                    if (err.response.status = 422) {
                        this.errors = err.response.data.errors
                    } else {
                        this.toastMessage('Error', 'Error')
                    }
                })
            }
        },

        listAppCodes(){
            axios.get('/role-service/list-app-codes').then(response => {
                this.appCodeOptions = response.data
            })
        },

        listStatuses() {
            axios.get('/role-service/list-statuses').then(response => {
                this.statusOptions = response.data
            })
        },

        // get all permission according to app_code (Event onchange)
        changeAppCode(val) {
            axios.get(`/role-service/list-permissions/${val}`).then(response => {
                this.permissionOptions = response.data
            })
        },

        getRole() {
            if (this.id) {
                axios.get('/roles/' + this.id + '/edit').then(response => {
                    this.formValues = response.data
                    this.permissions = response.data.permissions
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
        this.getRole()
        this.listAppCodes()
        this.listStatuses()
    },
}
</script>

<style scoped>
    .formulate-input-error {
        list-style-type: none;
        color: #960505;
        font-size: 0.9em;
        font-weight: 300;
        line-height: 1.5;
        margin-bottom: 0.25em;
        font-weight: bold;
    }
</style>