<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Exchange Rate
            </h2>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12 lg:col-span-6 p-5">
              <form @submit.prevent="handleSubmit">
                <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Branch Code *</label>
                        <Dropdown
                            class="w-full p-inputtext-sm"
                            v-model="formValues.branch_code"
                            :options="branchCode"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Branch Code"
                        />
                        <span v-if="errors.branch_code" class="text-theme-6">
                            {{ errors.branch_code[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Rate Date *</label>
                        <Calendar
                            class="w-full"
                            v-model="formValues.rate_date"
                            dateFormat="yy-mm-dd"
                            :minDate="formValues.start_from"
                            placeholder="Rate Date"
                        />
                        <span v-if="errors.rate_date" class="text-theme-6">
                            {{ errors.rate_date[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Currency 1 *</label>
                        <Dropdown
                            class="w-full p-inputtext-sm"
                            v-model="formValues.ccy1"
                            :options="currency"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Currency 1"
                        />
                        <span v-if="errors.ccy1" class="text-theme-6">
                            {{ errors.ccy1[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Currency 2 *</label>
                        <Dropdown
                            class="w-full p-inputtext-sm"
                            v-model="formValues.ccy2"
                            :options="currency"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Currency 2"
                        />
                        <span v-if="errors.ccy2" class="text-theme-6">
                            {{ errors.ccy2[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Rate Type *</label>
                        <Dropdown
                            class="w-full p-inputtext-sm"
                            v-model="formValues.rate_type"
                            :options="rateType"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Rate Type"
                        />
                        <span v-if="errors.rate_type" class="text-theme-6">
                            {{ errors.rate_type[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Buy Rate</label>
                        <InputNumber
                            class="w-full"
                            v-model="formValues.buy_rate"
                            :min="0"
                            mode="decimal"
                            :minFractionDigits="2"
                            placeholder="Buy Rate"
                        />
                        <span v-if="errors.buy_rate" class="text-theme-6">
                            {{ errors.buy_rate[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Sale Rate</label>
                        <InputNumber
                            class="w-full"
                            v-model="formValues.sale_rate"
                            :min="0"
                            mode="decimal"
                            :minFractionDigits="2"
                            placeholder="Sale Rate"
                        />
                        <span v-if="errors.sale_rate" class="text-theme-6">
                            {{ errors.sale_rate[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Middle Rate *</label>
                        <InputNumber
                            class="w-full"
                            v-model="formValues.mid_rate"
                            :min="0"
                            mode="decimal"
                            :minFractionDigits="2"
                            placeholder="Middle Rate"
                        />
                        <span v-if="errors.mid_rate" class="text-theme-6">
                            {{ errors.mid_rate[0] }}
                        </span>
                    </div>
                </div>

                <div class="text-right mt-5">
                    <router-link to="/exchange-rate" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
                    <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
                        <span v-if="id">Update</span>
                        <span v-else>Create</span>
                    </button>
                </div>
              </form>
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
           formValues: {
               branch_code: '',
               rate_date: null,
               ccy1: '',
               ccy2: '', 
               rate_type: '',
               buy_rate: null,
               sale_rate: null,
               mid_rate: null
           },
           branchCode: [],
           currency: [],
           rateType: [],
           errors: {}
       }
   },
   methods: {
       async loadData() {
           try {
               const [branchRes, currencyRes, rateTypeRes] = await Promise.all([
                   axios.get("/exchange-rate-service/branch-code"),
                   axios.get("/exchange-rate-service/currency"),
                   axios.get("/exchange-rate-service/rate-type")
               ]);

               this.branchCode = branchRes.data;
               this.currency = currencyRes.data;
               this.rateType = rateTypeRes.data.map(type => ({
                   label: type,
                   value: type
               }));
           } catch (error) {
              notify('Error loading options', 'error','bottom-right');
           }
       },
       parseNumberValue(value) {
         if (typeof value === 'string') {
           // Remove commas and convert to number
           return parseFloat(value.replace(/,/g, ''));
         }
         return value;
       },

       async getExchangeRate() {
           if (!this.id) return;

           try {
               const response = await axios.get(`/exchange-rates/${this.id}/edit`);
               if (response.data?.error) {
                  notify(response.data.message, 'error','bottom-right');
                   return;
               }
               const data = response.data;
               ['buy_rate', 'sale_rate', 'mid_rate'].forEach(field => {
                 if (data[field]) {
                   data[field] = this.parseNumberValue(data[field]);
                 }
               });

               this.formValues = response.data;

           } catch (error) {
              // notify('Error loading exchange rate', 'error','bottom-right');
             notify('Something went wrong...', 'error','bottom-right');
           }
       },

       validateForm() {
           let isValid = true;
           this.errors = {};

           const requiredFields = {
               branch_code: 'Branch Code',
               rate_date: 'Rate Date',
               ccy1: 'Currency 1',
               ccy2: 'Currency 2',
               rate_type: 'Rate Type',
               mid_rate: 'Middle Rate'
           };

           for (const [field, label] of Object.entries(requiredFields)) {
               if (!this.formValues[field]) {
                   this.errors[field] = [`${label} is required`];
                   isValid = false;
               }
           }
           if (this.formValues.ccy1 && this.formValues.ccy1 === this.formValues.ccy2) {
               this.errors.ccy2 = ['Currency 2 must be different from Currency 1'];
               isValid = false;
           }

           return isValid;
       },

       handleSubmit() {
           if (!this.validateForm()) {
              notify('Please fill in all required fields', 'error','bottom-right');
               return;
           }

           this.errors = {};  
           const url = this.id ? `/exchange-rates/${this.id}` : '/exchange-rates';
           const method = this.id ? 'put' : 'post';
           const successMsg = this.id ? 'Update successful!' : 'Create successful!';
           const formData = {
             rate_date: moment(this.formValues.rate_date).format('YYYY-MM-DD')
           };

           axios[method](url, Object.assign(this.formValues,formData))
               .then(() => {
                  notify(successMsg, 'success','bottom-right');
                   setTimeout(() => {
                       this.$router.push({name: "ExchangeRate"});
                   }, 1000);
               })
               .catch((error) => {
                   if (error.response?.status === 422) {
                       this.errors = error.response.data.errors;
                   } else {
                      notify('Something went wrong...', 'error','bottom-right');
                   }
               });
       },

       
   },

   async mounted() {
       await Promise.all([
           this.loadData(),
           this.getExchangeRate()
       ]);
   }
}
</script>