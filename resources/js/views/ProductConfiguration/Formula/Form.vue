<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Component Formula
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 lg:col-span-6">
            <div class="intro-y box p-5">
                <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                <div class="col-span-12">
                    <label class="mb-1 block font-bold">Product Line *</label>
                    <Dropdown
                    class="w-full p-inputtext-sm"
                    v-model="formValues.product_line_code"
                    :options="productLineOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Product Line"
                    @change="changeProductLine($event.value)"
                    />
                    <span v-if="errors.product_line_code" class="text-theme-6">
                    {{ errors.product_line_code[0] }}
                    </span>
                </div>

                <div class="col-span-12">
                    <label class="mb-1 block font-bold">Product *</label>
                    <Dropdown
                    class="w-full p-inputtext-sm"
                    v-model="formValues.product_code"
                    :options="productOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Product"
                    @change="changeProduct($event.value)"
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
                    <Dropdown
                    class="w-full p-inputtext-sm"
                    v-model="formValues.component_code"
                    :options="compCodeOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Cover Code"
                    />
                    <span v-if="errors.component_code" class="text-theme-6">
                    {{ errors.component_code[0] }}
                    </span>
                </div>

                <div class="col-span-12">
                    <label class="mb-1 block font-bold">Formula Code *</label>
                    <InputText
                    class="w-full p-inputtext-sm"
                    v-model="formValues.formula_code"
                    placeholder="Formula Code"
                    />
                    <span v-if="errors.formula_code" class="text-theme-6">
                    {{ errors.formula_code[0] }}
                    </span>
                </div>

                <div class="col-span-12">
                    <label class="mb-1 block font-bold">Calculate Option *</label>
                    <Dropdown
                    class="w-full p-inputtext-sm"
                    v-model="formValues.calc_option"
                    :options="[
                        {label: 'Standard', value: 'STANDARD'},
                        {label: 'Special', value: 'SPECIAL'}
                    ]"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Calculate Option"
                    />
                    <span v-if="errors.calc_option" class="text-theme-6">
                    {{ errors.calc_option[0] }}
                    </span>
                </div>

                <div class="col-span-12">
                    <label class="mb-1 block font-bold">Formula Calculate Sequence *</label>
                    <InputNumber
                    class="w-full"
                    v-model="formValues.frm_calc_seq"
                    :min="0"
                    placeholder="Formula Calculate Sequence"
                    />
                    <span v-if="errors.frm_calc_seq" class="text-theme-6">
                    {{ errors.frm_calc_seq[0] }}
                    </span>
                </div>
                </div>

                <div class="text-right mt-5">
                <router-link to="/product-configuration/formula" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
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
            formValues: {
                product_line_code: '',
                product_code: '',
                component_code: '',
                formula_code: '',
                calc_option: '',
                frm_calc_seq: null
            },
            productOptions: [],
            compCodeOptions: [],
            errors: {},
            productLineOptions: []
        }
    },
    methods: {
        listProductsLine() {
            axios.get("/product-line-service/list-product-lines").then((response) => {
                this.productLineOptions = Object.entries(response.data).map(([value, label]) => ({
                    label: value,
                    value
                }));
            });
        },

        changeProductLine(e) {
            if (!e) return;
            axios.get(`/formula-service/list-products-by-product-line-code/${e}`).then(response => {
                this.productOptions = response.data;
                if (!this.id) {
                    this.formValues.product_code = '';
                    this.formValues.component_code = '';
                }
            });
        },

        changeProduct(e) {
            if (!e) return;
            axios.get(`/formula-service/list-covers-by-product-code/${e}`).then(response => {
                this.compCodeOptions = response.data;
            });
        },

        handleSubmit() {
            let res = null

            if (!this.id) {
            res = axios.post('/formula', this.formValues)
            } else {
            res = axios.put(`/formula/${this.id}`, this.formValues)
            }

            res.then(response => {
            if (response.data.success) {
                notify(response.data.message, 'success', 'bottom-right')
                this.$router.push({
                name: "FormulaIndex"
                })
            }
            }).catch(err => {
            if (err.response.status === 422) {
                this.errors = err.response.data.errors
            }
            })
        },
        async getFormula() {
    if (!this.id) return;
    
    try {
        const response = await axios.get(`/formula/${this.id}`);
        this.formValues = response.data;
        
        await Promise.all([
            this.changeProductLine(this.formValues.product_line_code),
            this.changeProduct(this.formValues.product_code)
        ]);
    } catch (error) {
        notify('Error', 'error', 'bottom-line');
    }
}
    },
    
    mounted() {
        this.listProductsLine();
        this.getFormula();
    }
}
</script>
