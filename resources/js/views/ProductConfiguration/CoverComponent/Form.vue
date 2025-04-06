<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Cover Component
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 lg:col-span-6">
                    <div class="intro-y box p-5">
                        <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Product *</label>
                                <Dropdown
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.product_code"
                                    :options="autoProductOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Product"
                                >
                                    <template #option="slotProps">
                                        <p class="text-sm font-semibold">{{slotProps.option.label}}</p>
                                        <span class="text-xs">{{slotProps.option.desc}}</span>
                                    </template>
                                </Dropdown>
                                <span v-if="errors.product_code" class="text-theme-6">
                                    {{ errors.product_code[0] }}
                                </span>
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Cover Component Code *</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.code"
                                    placeholder="Cover Code"
                                />
                                <span v-if="errors.code" class="text-theme-6">
                                    {{ errors.code[0] }}
                                </span>
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Cover Component Name (English) *</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.name"
                                    placeholder="Cover Component Name (English)"
                                />
                                <span v-if="errors.name" class="text-theme-6">
                                    {{ errors.name[0] }}
                                </span>
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Cover Component Name (Khmer)</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.name_kh"
                                    placeholder="Cover Component Name (Khmer)"
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Cover Component Name (Chinese)</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.name_zh"
                                    placeholder="Cover Component Name (Chinese)"
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Description (English)</label>
                                <Textarea
                                    class="w-full"
                                    rows="3"
                                    v-model="formValues.description"
                                    placeholder="Description (English)"
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Description (Khmer)</label>
                                <Textarea
                                    class="w-full"
                                    rows="3"
                                    v-model="formValues.description_kh"
                                    placeholder="Description (Khmer)"
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Description (Chinese)</label>
                                <Textarea
                                    class="w-full"
                                    rows="3"
                                    v-model="formValues.description_zh"
                                    placeholder="Description (Chinese)"
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Value *</label>
                                <InputNumber
                                    class="w-full"
                                    v-model="formValues.value"
                                    :min="0"
                                    placeholder="Value"
                                />
                                <span v-if="errors.value" class="text-theme-6">
                                    {{ errors.value[0] }}
                                </span>
                            </div>
                        </div>

                        <div class="text-right mt-5">
                            <router-link to="/product-configuration/cover-component" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
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

export default {
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {},
            autoProductOptions: [],
            errors: [],
        }
    },
    methods: {
        listAutoProductsWithDesc() {
            axios.get('/cover-service/list-auto-products-with-desc').then(response => {
                this.autoProductOptions = response.data
            })
        },

        handleSubmit() {
            let res = null

            if (!this.id) {
                res = axios.post('/cover-component', this.formValues)
            } else {
                res = axios.put(`/cover-component/${this.id}`, this.formValues)
            }

            res.then(response => {
                if (response.data.success) {
                    notify(response.data.message, 'success','bottom-right')
                    this.$router.push({
                        name:"CoverComponentIndex"
                    })
                }
            }).catch(err => {
                if (err.response.status === 422) {
                    this.errors = err.response.data.errors
                }
            })
        },

        getCoverComponent() {
            if (this.id) {
                axios.get(`/cover-component/${this.id}`).then(response => {
                    this.formValues = response.data;
                })
            }
        }
    },
    mounted() {
        this.listAutoProductsWithDesc()
        this.getCoverComponent()
    }
}
</script>
