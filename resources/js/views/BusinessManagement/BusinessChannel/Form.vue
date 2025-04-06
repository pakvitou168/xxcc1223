<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Business Channel
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-6">
                <div class="intro-y box p-5">
                    <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                        <div class="col-span-12" v-if="id">
                            <label class="mb-1 block font-bold">Business Code</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.business_code"
                                disabled
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Business Category *</label>
                            <Dropdown
                                class="w-full p-inputtext-sm"
                                v-model="formValues.business_category_id"
                                :options="businessCategoryOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Business Category"
                            />
                            <span v-if="errors.business_category_id" class="text-theme-6">
                                {{ errors.business_category_id[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Full Name *</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.full_name"
                                placeholder="Full Name"
                            />
                            <span v-if="errors.full_name" class="text-theme-6">
                                {{ errors.full_name[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Sale Channel *</label>
                            <Dropdown
                                class="w-full p-inputtext-sm"
                                v-model="formValues.sale_channel"
                                :options="saleChannelOptions"
                                optionLabel="label" 
                                optionValue="value"
                                placeholder="Sale Channel"
                            />
                            <span v-if="errors.sale_channel" class="text-theme-6">
                                {{ errors.sale_channel[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Commission Rate (%)</label>
                            <InputNumber
                                class="w-full"
                                v-model="formValues.commission_rate"
                                :min="0"
                                :minFractionDigits="0"
                                :maxFractionDigits="2"
                                step="any"
                                placeholder="Commission Rate (%)"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Tax & Fee (%) *</label>
                            <InputNumber
                                class="w-full"
                                v-model="formValues.premium_tax_fee_rate"
                                :min="0"
                                :minFractionDigits="0"
                                :maxFractionDigits="2"
                                step="any"
                                placeholder="Tax & Fee (%)"
                            />
                            <span v-if="errors.premium_tax_fee_rate" class="text-theme-6">
                                {{ errors.premium_tax_fee_rate[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">WHT (%) *</label>
                            <InputNumber
                                class="w-full"
                                v-model="formValues.witholding_tax_rate"
                                :min="0"
                                :minFractionDigits="0"
                                :maxFractionDigits="2"
                                step="any"
                                placeholder="WHT (%)"
                            />
                            <span v-if="errors.witholding_tax_rate" class="text-theme-6">
                                {{ errors.witholding_tax_rate[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Business Handler *</label>
                            <Dropdown
                                class="w-full p-inputtext-sm"
                                v-model="formValues.handler_code"
                                :options="businessHandlerOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Business Handler"
                            />
                            <span v-if="errors.handler_code" class="text-theme-6">
                                {{ errors.handler_code[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Contact Phone</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.contact_phone"
                                placeholder="Contact Phone"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Contact Email</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                type="email"
                                v-model="formValues.contact_email"
                                placeholder="Contact Email"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Contact Address</label>
                            <Textarea
                                class="w-full"
                                v-model="formValues.contact_address"
                                rows="3"
                                placeholder="Contact Address"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Parent</label>
                            <Dropdown
                                class="w-full p-inputtext-sm"
                                v-model="formValues.parent_code"
                                :options="parentOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Parent"
                            />
                        </div>
                    </div>

                    <div class="text-right mt-5">
                        <router-link to="/business-management/business-channels" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
                        <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
                            <span v-if="id">Update</span>
                            <span v-else>Create</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "axios";

export default {
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {},
            businessCategoryOptions: [],
            saleChannelOptions:[],
            businessHandlerOptions: [],
            parentOptions: [],
            errors: {},
        }
    },

    methods: {
        async handleSubmit() {
            try {
                const url = this.id ? `/business-channels/${this.id}` : '/business-channels';
                const method = this.id ? 'put' : 'post';
                
                const response = await axios[method](url, this.formValues);
                
                if (response.data.success) {
                    notify(response.data.message, 'success','bottom-right');
                    this.$router.push({name: "BusinessChannelIndex"});
                }
            } catch (err) {
                if (err?.response?.status === 422) {
                    this.errors = err.response.data.errors;
                } else {
                    console.error('Error saving business channel:', err);
                    notify('Error saving business channel', 'error','bottom-right'); 
                }
            }
        },
        getBusinessChannel() {
            if (this.id) {
                axios.get(`/business-channels/${this.id}/edit`)
                    .then(response => {
                        this.formValues = response.data;
                    })
            }
        },
        listBusinessCategories() {
            axios.get('/business-channels-service/list-business-categories').then(response => {
                this.businessCategoryOptions = response.data
            })
        },
        listSaleChannels() {
            axios.get('/business-channels-service/list-sale-channels').then(response => {
                this.saleChannelOptions = response.data
            })
        },
        listBusinessHandlers() {
            axios.get('/business-channels-service/list-business-handlers').then(response => {
                this.businessHandlerOptions = response.data
            })
        },
        listParents() {
            axios.get('/business-channels-service/list-parents').then(response => {
                this.parentOptions = response.data
            })
        },
    },

    mounted() {
        this.getBusinessChannel()
        this.listBusinessCategories()
        this.listSaleChannels()
        this.listBusinessHandlers()
        this.listParents()
    }
}
</script>