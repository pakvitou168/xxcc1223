<template>
    <div class="intro-y box p-5 mt-5">
        <div class="intro-y flex mb-4 p-1">
            <h2 class="text-xl font-medium mr-auto">Make Detail</h2>
            <button v-if="canDelete" class="btn btn-danger mx-1 intro-x" @click="handleDelete(id)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
            <router-link v-if="canUpdate" :to="{ name: 'MakeUpdate', params: { id: id }}" class="btn btn-primary mx-1 intro-x">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </router-link>
        </div>
        <hr>
        <div class="flex m-2">
            <span class="text-base w-1/4 intro-x">Make</span>
            <p class="text-base text-bold intro-x">{{ formValues.make }}</p>
        </div>
        <hr>
        <div class="flex m-2">
            <span class="text-base w-1/4 intro-x">Description</span>
            <p class="text-base text-bold intro-x">{{ formValues.description }}</p>
        </div>
        <hr>
        <div class="flex m-2">
            <span class="text-base w-1/4 intro-x">Offline</span>
            <svg v-if="formValues.available_offline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="check-circle" role="presentation" class="text-green-500 fill-current text-success">
                <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"></path>'
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="x-circle" role="presentation" class="text-red-500 fill-current text-danger">
                <path d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"></path>
            </svg>
        </div>
        <hr>
        <div class="flex m-2">
            <span class="text-base w-1/4 intro-x">Online</span>
            <svg v-if="formValues.available_online" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="check-circle" role="presentation" class="text-green-500 fill-current text-success">
                <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"></path>'
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="x-circle" role="presentation" class="text-red-500 fill-current text-danger">
                <path d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"></path>
            </svg>
        </div>
        <hr>
        <div class="text-right mt-5">
            <router-link to="/product-configuration/makes" class="btn btn-primary w-24 mr-1" tag="button">Back</router-link>
        </div>
    </div>
</template>

<script>

import UserPermissions from '../../../mixins/UserPermissions'

export default {
    mixins: [UserPermissions],
    data() {
        return {
            id: this.$route.params.id,
            functionCode: 'MAKE',
            formValues: {},
        }
    },
    methods: {
        getMake(){
            axios.get('/makes/' + this.id).then(response => {
                this.formValues = response.data
            })
        },

        handleDelete(id) {
            this.$confirm.require({
                message: 'Do you want to delete this record?',
                header: 'Delete',
                icon: 'pi pi-info-circle',
                acceptClass: "p-button-danger",
                blockScroll: false,
                accept: () => {
                    axios.delete('/makes/' + id)
                        .then(response => {
                            if (response.data.success) {
                                notify(response.data.message, 'success', 'bottom-right')
                                this.$router.push({ name: 'MakeIndex' })
                            }
                        })
                        .catch(err => {
                            notify('Error', 'error')
                        })
                },
            });
        }
    },
    mounted(){
        this.getMake();
    }
}
</script>