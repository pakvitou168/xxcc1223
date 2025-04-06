<template>
    <div>
        <div class="intro-y box p-5 mt-5">
            <div class="intro-y flex mb-4 p-1">
                <h2 class="text-xl font-medium mr-auto">Role Detail</h2>
                <button v-if="canDelete" class="btn btn-danger mx-1 intro-x" @click="handleDelete(id)">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                <router-link v-if="canUpdate" :to="{ name: 'RoleUpdate', params: { id: id }}" class="btn btn-primary mx-1 intro-x">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </router-link>
            </div>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Code</span>
                <p class="text-base text-bold intro-x">{{ formValues.code }}</p>
            </div>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">App Code</span>
                <p class="text-base text-bold intro-x">{{ formValues.app_code }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Name</span>
                <p class="text-base text-bold intro-x">{{ formValues.name }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Description</span>
                <p class="text-base text-bold intro-x">{{ formValues.description }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Status</span>
                <p class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full" v-if="formValues.status == 'ACT'">Active</p>
                <p class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full" v-else>{{ formValues.status }}</p>
            </div>
        </div>
        <div class="intro-y box p-5 mt-5">
            <div class="overflow-x-auto scrollbar-hidden">
                <div class="flex w-full p-1">
                    <div class="flex-1">
                        <h2 class="text-xl font-medium mr-auto mt-1">Functions</h2>
                    </div>
                </div>
                <div class="intro-y box">
                    <div class="overflow-x-auto scrollbar-hidden">
                        <DataTable :options="options" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import UserPermissions from '../../../mixins/UserPermissions'

export default {
    mixins: [UserPermissions],

    data(){
        return {
            id: this.$route.params.id,
            functionCode: 'ROLE',
            formValues: {},
            options: {
                ajaxURL: "/role-service/roles/functions/" + this.$route.params.id,
                columns: [
                    {
                        title: "Code",
                        field: "code",

                    },
                    {
                        title: "App Code",
                        field: "app_code",
                        headerSort: false,
                    },
                    {
                        title: "Name",
                        field: "name",
                    },
                    {
                        title: "Permission",
                        field: "permission",
                        headerSort: false,
                        mutator: (_, row) => {
                            if (row.roles) {
                                let permissionStr = row.roles[0]?.permission
                                return permissionStr.split('#').join(', ')
                            }
                        },
                    },
                ],
            },
        };
    },
    methods: {
        getRole(){
            axios.get('/roles/' + this.id).then(response => {
                this.formValues = response.data;
            });
        },

        handleDelete(id) {
            this.$confirm.require({
                message: 'Do you want to delete this record?',
                header: 'Delete',
                icon: 'pi pi-info-circle',
                acceptClass: "p-button-danger",
                blockScroll: false,
                accept: () => {
                    axios.delete(`/roles/${id}`).then(response => {
                        if (response.data.success) {
                            this.toastMessage(response.data.message, 'Success')
                            this.$router.push({ name: 'RoleIndex' });
                        }
                    }).catch(() => this.toastMessage('Error', 'Error'))
                },
            });
        },

        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        }
    },
    mounted(){
        this.getRole();
    }
}
</script>
