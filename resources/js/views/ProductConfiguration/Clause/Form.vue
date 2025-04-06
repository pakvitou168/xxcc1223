<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Clause Maintenance
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 lg:col-span-6">
                <div class="intro-y box p-5">
                    <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Product Line *</label>
                            <Dropdown class="w-full p-inputtext-sm" v-model="formValues.product_line_code"
                                :options="productLineOptions" optionLabel="label" optionValue="value"
                                placeholder="Product Line" @change="filterClauseType($event.value)" />
                            <span v-if="errors.product_line_code" class="text-theme-6">
                                {{ errors.product_line_code[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Clause Type *</label>
                            <Dropdown class="w-full p-inputtext-sm" v-model="formValues.clause_type"
                                :options="clauseTypeOptions" :loading="loading" optionLabel="label" optionValue="value"
                                placeholder="Clause Type" />
                            <span v-if="errors.clause_type" class="text-theme-6">
                                {{ errors.clause_type[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Clause (English) *</label>
                            <InputText class="w-full p-inputtext-sm" v-model="formValues.clause"
                                placeholder="Clause (English)" />
                            <span v-if="errors.clause" class="text-theme-6">
                                {{ errors.clause[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Clause (Khmer)</label>
                            <InputText class="w-full p-inputtext-sm" v-model="formValues.clause_kh"
                                placeholder="Clause (Khmer)" />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Clause (Chinese)</label>
                            <InputText class="w-full p-inputtext-sm" v-model="formValues.clause_zh"
                                placeholder="Clause (Chinese)" />
                        </div>

                        <div class="col-span-12">
                            <label class="font-bold mb-1 block">Clause Detail (English)</label>
                            <Textarea class="w-full" rows="3" v-model="formValues.clause_detail"
                                placeholder="Clause Detail (English)" />
                        </div>

                        <div class="col-span-12">
                            <label class="font-bold mb-1 block">Clause Detail (Khmer)</label>
                            <Textarea class="w-full" rows="3" v-model="formValues.clause_detail_kh"
                                placeholder="Clause Detail (Khmer)" />
                        </div>

                        <div class="col-span-12">
                            <label class="font-bold mb-1 block">Clause Detail (Chinese)</label>
                            <Textarea class="w-full" rows="3" v-model="formValues.clause_detail_zh"
                                placeholder="Clause Detail (Chinese)" />
                        </div>

                        <div class="col-span-12">
                            <label class="font-bold mb-1 block">Sequence No.</label>
                            <InputNumber class="w-full" v-model="formValues.sequence" :min="0"
                                placeholder="Sequence No." />
                        </div>

                        <div class="col-span-12">
                            <label class="block mb-1 font-bold">Default Inclusion</label>
                            <Checkbox v-model="formValues.default_inclusion" :binary="true" />
                        </div>
                    </div>
                    <div class="text-right mt-5">
                        <router-link to="/product-configuration/clause-maintenances"
                            class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
                        <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
                            {{ id ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import clauseService from '../../../services/product_config/clause.service';
export default {
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {
                default_inclusion: false
            },
            clauseTypeOptions: [],
            productLineOptions: [],
            errors: {},
            loading:false
        }
    },
    methods: {
        // Helper method to transform object to array
        transformToArray(obj) {
            return Object.entries(obj).map(([value, label]) => ({
                value,
                label
            }));
        },

        handleSubmit() {
            let res = null

            if (!this.id) {
                res = axios.post('/clause-maintenances', this.formValues)
            } else {
                res = axios.put(`/clause-maintenances/${this.id}`, this.formValues)
            }

            res.then(response => {
                if (response.data.success) {
                    notify(response.data.message, 'success', 'bottom-right')
                    this.$router.push({
                        name: 'ClauseMaintenanceIndex'
                    })
                }
            }).catch(err => {
                if (err.response.status === 422) {
                    this.errors = err.response.data.errors
                }
            })
        },


        getServices() {
            axios.get('/clause-maintenance-service/get-services').then(response => {
                // Transform the objects into arrays
                this.productLineOptions = this.transformToArray(response.data.productLineOptions);
            })
        },

        getClause() {
            if (this.id) {
                axios.get(`/clause-maintenances/${this.id}`).then(response => {
                    this.formValues = {
                        ...response.data,
                        default_inclusion: Boolean(response.data.default_inclusion)
                    };
                }).then(() => {
                    this.filterClauseType(this.formValues.product_line_code)
                })
            }
        },
        filterClauseType(productLine) {
            this.loading = true
            clauseService.clauseType(productLine).then(res => {
                this.clauseTypeOptions = res.data
            }).catch(err => {
                notify("Something went wrong", 'error')
            }).finally(() => this.loading = false)
        }
    },
    mounted() {
        this.getClause()
        this.getServices()
    }
}
</script>