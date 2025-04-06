<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Component Formula Expression
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 lg:col-span-6">
                    <!-- BEGIN: Input -->
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
                                <Dropdown
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.formula_code"
                                    :options="formulaOption"
                                    optionLabel="label"
                                    optionValue="value"
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
                                <label class="mb-1 block font-bold">Expression Line</label>
                                <InputNumber
                                    class="w-full"
                                    v-model="formValues.expr_line"
                                    :min="0"
                                    placeholder="Expression Line"
                                />
                                <span v-if="errors.expr_line" class="text-theme-6">
                                    {{ errors.expr_line[0] }}
                                </span>
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Expression Type</label>
                                <Dropdown
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.expr_type"
                                    :options="[{label: 'Number', value: 'NUMBER'}]"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Expression Type"
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Condition Expression *</label>
                                <Dropdown
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.cond_expr"
                                    :options="condExprOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Condition Expression"
                                />
                                <span v-if="errors.cond_expr" class="text-theme-6">
                                    {{ errors.cond_expr[0] }}
                                </span>
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Formula Expression *</label>
                                <Textarea
                                    class="w-full"
                                    v-model="formValues.formula_expr"
                                    rows="4"
                                    placeholder="Formula Expression"
                                />
                                <span v-if="errors.formula_expr" class="text-theme-6">
                                    {{ errors.formula_expr[0] }}
                                </span>
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Condition Type</label>
                                <Checkbox
                                    v-model="formValues.cond_type"
                                    :binary="true"
                                />
                            </div>

                            <div class="col-span-12 text-right mt-5">
                                <router-link to="/product-configuration/comp-frm-expr" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
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
           formValues: {
               product_code: '',
               component_code: '',
               formula_code: '',
               calc_option: '',
               expr_line: null,
               expr_type: '',
               cond_expr: '',
               formula_expr: '',
               cond_type: false
           },
           autoProductOptions: [],
           compCodeOptions: [],
           formulaOption: [],
           condExprOptions: [],
           errors: {}
       }
   },
   methods: {
       async loadOptions() {
           try {
               const [products, components, formulas, conditions] = await Promise.all([
                   axios.get('/cover-service/list-auto-products-with-desc'),
                   axios.get('/formula-service/list-product-comp'),
                   axios.get('/formula-service/list-formula'),
                   axios.get('/component-form-expression-service/list-components')
               ]);

               this.autoProductOptions = products.data;
               this.compCodeOptions = Object.entries(components.data).map(([value, label]) => ({
                   label,
                   value
               }));
               this.formulaOption = Object.entries(formulas.data).map(([value]) => ({
                   label: value,
                   value
               }));
               this.condExprOptions = conditions.data.map(value => ({
                   label: value,
                   value
               }));

           } catch (error) {
               notify('Error loading options', 'error','bottom-right');
           }
       },

       handleSubmit() {
           const url = this.id ? `/comp_form_expression/${this.id}` : '/comp_form_expression';
           const method = this.id ? 'put' : 'post';

           axios[method](url, this.formValues)
               .then(response => {
                   if (response.data.success) {
                       notify(response.data.message, 'success', 'bottom-right');
                       this.$router.push({name: "FrmExpIndex"});
                   }
               })
               .catch(err => {
                   this.errors = err.response?.status === 422 
                       ? err.response.data.errors 
                       : {};
               });
       },


       async getData() {
           if (!this.id) return;
           
           try {
               const response = await axios.get(`/comp_form_expression/${this.id}`);
               this.formValues = response.data;
               if (!response.data.cond_type) {
                   this.formValues.cond_type = false;
               }
           } catch (error) {
               notify('Error loading data', 'Error');
           }
       }
   },
   
   async mounted() {
       await Promise.all([
           this.loadOptions(),
           this.getData()
       ]);
   }
}
</script>
