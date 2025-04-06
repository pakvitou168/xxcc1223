<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Policy Wording Version
            </h2>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12 lg:col-span-6 p-5">
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
                            @change="changeProductLine"
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
                        <label class="mb-1 block font-bold">Policy Wording *</label>
                        <InputText
                            class="w-full p-inputtext-sm"
                            v-model="formValues.policy_wording"
                            placeholder="Policy Wording"
                        />
                        <span v-if="errors.policy_wording" class="text-theme-6">
                            {{ errors.policy_wording[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Year *</label>
                        <InputNumber
                            class="w-full"
                            v-model="formValues.year"
                            placeholder="Year"
                        />
                        <span v-if="errors.year" class="text-theme-6">
                            {{ errors.year[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="block mb-1 font-bold">Is Default</label>
                        <Checkbox
                            v-model="formValues.is_default"
                            :binary="true"
                        />
                    </div>

                    <div class="col-span-12 text-right mt-5">
                        <router-link to="/product-configuration/policy-wording-versions" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
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
                policy_wording: '',
                year: null,
                is_default: false
            },
            productLineOptions: [], 
            productOptions: [],
            errors: {}
        }
    },
    watch: {
        'formValues.product_line_code': {
            handler(newVal) {
                if (newVal) {
                    this.changeProductLine(newVal);
                } else {
                    this.productOptions = [];
                }
            },
            immediate: true
        }
    },
   
   methods: {
       async listProductLines() {
            try {
                const response = await axios.get("/policy-wording-versions-service/list-product-lines");
                this.productLineOptions = Object.entries(response.data).map(([value, label]) => ({
                    label: value,
                    value
                }));
            } catch (error) {
                notify('Error loading product lines', 'error','bottom-right');
            }
        },


        async changeProductLine(e) {
            if (!e) {
                this.productOptions = [];
                return;
            }
            try {
                const response = await axios.get(`/policy-wording-versions-service/list-products-by-product-line-code-with-desc/${e}`);
                this.productOptions = response.data;
            } catch (error) {
                notify('Error loading products', 'error','bottom-right');
                this.productOptions = [];
            }
       },
        async getData() {
            if (!this.id) return;
            
            try {
                const response = await axios.get(`/policy-wording-versions/${this.id}`);
                // First set the product line code
                this.formValues = {
                    ...response.data,
                    product_line_code: response.data.product_line_code
                };
                // Then load the products for this product line
                await this.changeProductLine(this.formValues.product_line_code);
            } catch (error) {
                notify('Error loading data', 'error','bottom-right');
            }
        },

       handleSubmit() {
            const url = this.id ? `/policy-wording-versions/${this.id}` : '/policy-wording-versions';
            const method = this.id ? 'put' : 'post';

            axios[method](url, this.formValues)
                .then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'success', 'bottom-right');
                        this.$router.push({name: "PolicyWordingVersionIndex"});
                    }
                })
                .catch(err => {
                    this.errors = err.response?.status === 422 
                        ? err.response.data.errors 
                        : notify('Error occurred', 'error','bottom-right');
                });
        }

   },
   
   async created() {
        try {
            await this.listProductLines();
            await this.getData();
        } catch (error) {
            notify('Error initializing form', 'error', 'bottom-right');
        }
    }
}
</script>
