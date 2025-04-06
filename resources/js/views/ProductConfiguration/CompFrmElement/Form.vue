<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Component Formula Element
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
                                    :options="[{label:'Standard', value:'STANDARD'}, {label:'Special', value:'SPECIAL'}]"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Calculate Option"
                                />
                                <span v-if="errors.calc_option" class="text-theme-6">
                                    {{ errors.calc_option[0] }}
                                </span>
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Element Code *</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.elem_code"
                                    placeholder="Element Code"
                                    maxlength="25"
                                />
                                <span v-if="errors.elem_code" class="text-theme-6">
                                    {{ errors.elem_code[0] }}
                                </span>
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Element Type *</label>
                                <Dropdown
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.elem_type"
                                    :options="elementTypeOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Element Type"
                                />
                                <span v-if="errors.elem_type" class="text-theme-6">
                                    {{ errors.elem_type[0] }}
                                </span>
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Element Datatype *</label>
                                <Dropdown
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.elem_datatype"
                                    :options="[{label:'NUMBER', value:'NUMBER'}]"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Element Datatype"
                                />
                                <span v-if="errors.elem_datatype" class="text-theme-6">
                                    {{ errors.elem_datatype[0] }}
                                </span>
                            </div>

                            <div class="col-span-12 text-right mt-5">
                                <router-link to="/product-configuration/comp-frm-element" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
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

import axios from 'axios'

export default {
   data() {
       return {
           id: this.$route.params.id ?? null,
           formValues: {
               product_code: '',
               component_code: '',
               formula_code: '',
               calc_option: '',
               elem_code: '',
               elem_type: '',
               elem_datatype: ''
           },
           autoProductOptions: [],
           compCodeOptions: [],
           formulaOption: [],
           elementTypeOptions: [],
           errors: {}
       }
   },
   methods: {
       async loadOptions() {
           try {
               const [products, components, formulas, elementTypes] = await Promise.all([
                   axios.get('/cover-service/list-auto-products-with-desc'),
                   axios.get('/formula-service/list-product-comp'),
                   axios.get('/formula-service/list-formula'),
                   axios.get('/formula-service/list-element-types')
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
               this.elementTypeOptions = Object.entries(elementTypes.data).map(([value, label]) => ({
                   label,
                   value
               }));
           } catch (error) {
               notify('Error loading options', 'Error');
           }
       },

       handleSubmit() {
           const url = this.id ? `/comp_form_element/${this.id}` : '/comp_form_element';
           const method = this.id ? 'put' : 'post';

           axios[method](url, this.formValues)
               .then(response => {
                   if (response.data.success) {
                       notify(response.data.message, 'success', 'bottom-right');
                       this.$router.push({name: "FrmElementIndex"});
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
               const response = await axios.get(`/comp_form_element/${this.id}`);
               this.formValues = response.data;
           } catch (error) {
               notify('Error loading data', 'error', 'bottom-right');
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
