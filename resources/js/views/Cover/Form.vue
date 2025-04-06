<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Cover
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
                            <label class="mb-1 block font-bold">Cover Code *</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.code"
                                placeholder="Cover Code"
                                maxlength="5"
                            />
                            <span v-if="errors.code" class="text-theme-6">
                                {{ errors.code[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Cover Name (English) *</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.name"
                                placeholder="Cover Name (English)"
                                maxlength="50"
                            />
                            <span v-if="errors.name" class="text-theme-6">
                                {{ errors.name[0] }}
                            </span>
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Cover Name (Khmer)</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.name_kh"
                                placeholder="Cover Name (Khmer)"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Cover Name (Chinese)</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.name_zh"
                                placeholder="Cover Name (Chinese)"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Description (English)</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.description"
                                placeholder="Description (English)"
                                maxlength="50"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Description (Khmer)</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.description_kh"
                                placeholder="Description (Khmer)"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Description (Chinese)</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.description_zh"
                                placeholder="Description (Chinese)"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Detail (English)</label>
                            <Textarea
                                class="w-full"
                                v-model="formValues.detail"
                                rows="3"
                                placeholder="Detail (English)"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Detail (Khmer)</label>
                            <Textarea
                                class="w-full"
                                v-model="formValues.detail_kh"
                                rows="3"
                                placeholder="Detail (Khmer)"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Detail (Chinese)</label>
                            <Textarea
                                class="w-full"
                                v-model="formValues.detail_zh"
                                rows="3"
                                placeholder="Detail (Chinese)"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="block mb-1 font-bold">Is Mandatory</label>
                            <Checkbox
                                v-model="formValues.mandatory"
                                :binary="true"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="block mb-1 font-bold">Is Vehicle Value Required</label>
                            <Checkbox
                                v-model="formValues.is_required_vehicle_val"
                                :binary="true"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Value</label>
                            <InputNumber
                                class="w-full"
                                v-model="formValues.value"
                                placeholder="Value"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Deductible Label (English)</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.deductible_label"
                                placeholder="Deductible Label (English)"
                                maxlength="250"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Deductible Label (Khmer)</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.deductible_label_kh"
                                placeholder="Deductible Label (Khmer)"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Deductible Label (Chinese)</label>
                            <InputText
                                class="w-full p-inputtext-sm"
                                v-model="formValues.deductible_label_zh"
                                placeholder="Deductible Label (Chinese)"
                            />
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block font-bold">Sequence No.</label>
                            <InputNumber
                                class="w-full"
                                v-model="formValues.seq"
                                placeholder="Sequence No."
                                :min="1"
                            />
                        </div>

                        <div class="col-span-12 text-right mt-5">
                            <router-link to="/product-configuration/covers" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
                            <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
                                <span v-if="id">Update</span>
                                <span v-else>Create</span>
                            </button>
                        </div>
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
            errors: [],
            autoProductOptions: [],
        }
    },
    methods: {
        listAutoProductsWithDesc() {
            axios.get('/cover-service/list-auto-products-with-desc').then(response => this.autoProductOptions = response.data)
        },
        handleSubmit() {
            if (!this.id) {
                axios.post('/covers', this.formValues).then(response => {
                    if (response.data.success) {
                        console.log(this.formValues);
                        notify(response.data.message, 'success','bottom-right')
                        this.$router.push({ name: 'CoverIndex'})
                    }
                }).catch(err => {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors
                    } else {
                        notify('Error', 'Error','bottom-right')
                    }
                })
            } else {
                axios.put(`/covers/${this.id}`, this.formValues).then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'success','bottom-right')
                        this.$router.push({ name: 'CoverIndex'})
                    }
                }).catch(err => {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors
                    } else {
                        notify('Error', 'Error','bottom-right')
                    }
                })
            }
        },
        getCover() {
            if (this.id) {
                axios.get(`/covers/${this.id}`).then(response => {
                    this.formValues = response.data;
                })
            }
        }
    },
    mounted() {
        this.getCover()
        this.listAutoProductsWithDesc()
    }
}
</script>
