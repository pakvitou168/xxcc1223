<template>
    <div>
        <div class="intro-y box p-5 mt-5">
            <div class="overflow-x-auto scrollbar-hidden">
                <div class="w-full mb-4 table">
                    <div class="intro-y flex mb-4 p-1">
                        <h2 class="text-xl font-medium mr-auto">No Claim Discount Detail</h2>
                        <button v-if="canDelete" class="btn btn-danger mx-1 intro-x" @click="handleDelete(id)">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        <router-link v-if="canUpdate" :to="{ name: 'NoClaimDiscountUpdate', params: { id: id }}">
                            <button class="btn btn-primary mx-1 intro-x">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                        </router-link>
                    </div>
                    <hr>
                    <div class="flex m-2">
                        <span class="text-base w-1/4 intro-x">Product</span>
                        <p class="text-base text-bold intro-x">{{ formValues.product_code }} - {{ productName }}</p>
                    </div>
                    <hr>
                    <div class="flex m-2">
                        <span class="text-base w-1/4 intro-x">No Claim Discount (%)</span>
                        <p class="text-base text-bold intro-x">{{ formValues.ncd }}</p>
                    </div>
                    <hr>
                    <div class="flex m-2">
                        <span class="text-base w-1/4 intro-x">Description</span>
                        <p class="text-base text-bold intro-x">{{ formValues.description }}</p>
                    </div>
                    <hr>
                    <div class="text-right mt-5">
                        <router-link to="/product-configuration/no-claim-discounts" class="btn btn-primary w-24 mr-1" tag="button">Back</router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "axios";
import UserPermissions from '../../../mixins/UserPermissions'

export default {
    mixins: [UserPermissions],
    data() {
        return {
            id: this.$route.params.id,
            functionCode: 'NO_CLAIM_DISCOUNT',
            formValues: {},
        }
    },
    computed: {
        productName() {
            return this.formValues.product?.name
        }
    },
    methods: {
        getNoClaimDiscounts() {
            if (this.id) {
                axios.get(`/no-claim-discounts/${this.id}`)
                    .then(response => {
                        this.formValues = response.data;
                    })
            }
        },
        handleDelete(id) {
            this.$confirm.require({
            message: 'Do you want to delete this record?',
            header: 'Delete',
            icon: 'pi pi-info-circle',
            acceptClass: "p-button-danger",
            blockScroll: false,
            accept: () => {
                axios.delete(`/no-claim-discounts/${id}`).then(response => {
                if (response.data.success) {
                    notify(response.data.message, 'success', 'bottom-right')
                    this.$router.push({
                    name:"NoClaimDiscountIndex"
                    })
                }
                }).catch(err => {
                notify('Error', 'error', 'bottom-right')
                })
            },
            });
        },
    },
    mounted() {
        this.getNoClaimDiscounts()
    }
}
</script>
