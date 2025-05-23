<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">Business Channel Detail</h2>
            <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                <button v-if="canDelete" class="btn btn-danger mr-2 intro-x" title="Delete" v-on:click="handleDelete(id)">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                <router-link v-if="canUpdate" :to="{ name: 'BusinessChannelEdit', params: { id: id }}">
                    <button class="btn btn-primary intro-x" title="Edit">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                </router-link>
            </div>
        </div>
        <div class="intro-y box overflow-hidden mt-5 p-5">
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Business Code</span>
                <p class="text-base text-bold intro-x">{{ formValues.business_code }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Business Category</span>
                <p class="text-base text-bold intro-x">{{ formValues.business_category ? formValues.business_category.name : '' }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Full Name</span>
                <p class="text-base text-bold intro-x">{{ formValues.full_name }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Sale Channel</span>
                <p class="text-base text-bold intro-x">{{ formValues.sale_channel_name }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Commission Rate %</span>
                <p class="text-base text-bold intro-x">{{ commission_rate }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Tax & Fee (%)</span>
                <p class="text-base text-bold intro-x">{{ formValues.premium_tax_fee_rate }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">WHT (%)</span>
                <p class="text-base text-bold intro-x">{{ formValues.witholding_tax_rate }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Business Handler</span>
                <p class="text-base text-bold intro-x">{{ formValues.business_handler ? formValues.business_handler.name : '' }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Contact Phone</span>
                <p class="text-base text-bold intro-x">{{ formValues.contact_phone }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Contact Email</span>
                <p class="text-base text-bold intro-x">{{ formValues.contact_email }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Contact Address</span>
                <p class="text-base text-bold intro-x">{{ formValues.contact_address }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Parent</span>
                <p class="text-base text-bold intro-x">{{ formValues.parent_channel ? formValues.parent_channel.full_name : '' }}</p>
            </div>
        </div>
        <div class="text-right mt-5">
            <router-link to="/business-management/business-channels" class="btn btn-primary w-24 mr-1" tag="button">Back</router-link>
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
            functionCode: 'BUSINESS_CHANNEL',
            formValues: {}
        }
    },
    computed: {
        commission_rate() {
            return `${this.formValues.commission_rate ?? 0} %`   
        },
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
                    axios.delete(`/business-channels/${id}`).then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success','bottom-right')
                            this.$router.push({
                                name:"BusinessChannelIndex"
                            })
                        }
                    }).catch(err => {
                        notify('Error', 'error','bottom-right')
                    })
                },
            });
        },

        getBusinessChannel() {
            if (this.id) {
                axios.get(`/business-channels/${this.id}`)
                    .then(response => {
                        this.formValues = response.data;
                    })
            }
        },
        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        }
    },

    mounted() {
        this.getBusinessChannel()
    }
}
</script>