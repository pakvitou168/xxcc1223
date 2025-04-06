<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">Business Category Detail</h2>
            <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                <button v-if="canDelete" class="btn btn-danger mr-2 intro-x" title="Delete" v-on:click="handleDelete(id)">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                <router-link v-if="canUpdate" :to="{ name: 'BusinessCategoryEdit', params: { id: id }}">
                    <button class="btn btn-primary intro-x" title="Edit">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                </router-link>
            </div>
        </div>
        <div class="intro-y box overflow-hidden mt-5 p-5">
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
                <span class="text-base w-1/4 intro-x">Prefix</span>
                <p class="text-base text-bold intro-x">{{ formValues.prefix }}</p>
            </div>
        </div>
        <div class="text-right mt-5">
            <router-link to="/business-management/business-categories" class="btn btn-primary w-24 mr-1" tag="button">Back</router-link>
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
            functionCode: 'BUSINESS_CATEGORY',
            formValues: {}
        }
    },
    methods: {
        handleDelete(id) {
            this.$confirm.require({
                message: 'Do you want to delete this record?',
                header: 'Delete',
                icon: 'pi pi-info-circle',
                acceptClass: "p-button-danger",
                blockScroll: false,
                accept: () => {
                    axios.delete(`/business-categories/${id}`).then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success','bottom-right')
                            this.$router.push({name:"BusinessCategoryIndex"})
                        }
                    }).catch(err => {
                        notify('Error', 'error','bottom-right')
                    })
                },
            });
        },

        getBusinessCategory() {
            if (this.id) {
                axios.get(`/business-categories/${this.id}`)
                    .then(response => {
                        this.formValues = response.data;
                    })
            }
        },
    },

    mounted() {
        this.getBusinessCategory()
    }
}
</script>
