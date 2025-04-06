<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                No Claim Discount
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
                            :options="productOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Product"
                          >
                            <template #option="slotProps">
                              <div class="flex flex-col">
                                <p class="text-sm font-semibold mb-1">{{slotProps.option.label}}</p>
                                <span class="text-xs text-gray-600">{{slotProps.option.desc}}</span>
                              </div>
                            </template>
                          </Dropdown>
                          <span v-if="errors.product_code" class="text-theme-6">
                            {{ errors.product_code[0] }}
                          </span>
                        </div>

                        <div class="col-span-12">
                          <label class="mb-1 block font-bold">No Claim Discount (%) *</label>
                          <InputNumber
                            class="w-full"
                            v-model="formValues.ncd"
                            :min="0"
                            step="any"
                            placeholder="No Claim Discount (%)"
                          />
                          <span v-if="errors.ncd" class="text-theme-6">
                            {{ errors.ncd[0] }}
                          </span>
                        </div>

                        <div class="col-span-12">
                          <label class="mb-1 block font-bold">Description</label>
                          <InputText
                            class="w-full p-inputtext-sm"
                            v-model="formValues.description"
                            placeholder="Description"
                          />
                          <span v-if="errors.description" class="text-theme-6">
                            {{ errors.description[0] }}
                          </span>
                        </div>
                      </div>

                      <div class="text-right mt-5">
                        <router-link to="/product-configuration/no-claim-discounts" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
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
               product_code: '',
               ncd: null,
               description: ''
           },
           productOptions: [],
           errors: {}
       }
   },
   methods: {
       async listNoClaimDiscount() {
           if (!this.id) return;
           
           try {
               const response = await axios.get(`/no-claim-discounts/${this.id}/edit`);
               this.formValues = response.data;
           } catch (error) {
               notify('Error loading discount data', 'error','bottom-right');
           }
       },

       async listProducts() {
           try {
               const response = await axios.get('/no-claim-discounts-service/list-products-with-desc');
               this.productOptions = response.data;
           } catch (error) {
               notify('Error loading products', 'error','bottom-right');
           }
       },

       handleSubmit() {
           const url = this.id ? `/no-claim-discounts/${this.id}` : '/no-claim-discounts';
           const method = this.id ? 'put' : 'post';

           axios[method](url, this.formValues)
               .then(response => {
                   if (response.data.success) {
                       notify(response.data.message, 'success','bottom-right');
                       this.$router.push({name: "NoClaimDiscountIndex"});
                   }
               })
               .catch(err => {
                   this.errors = err.response?.status === 422 
                       ? err.response.data.errors 
                       : {};
               });
       },

   },
   
   async mounted() {
       await Promise.all([
           this.listNoClaimDiscount(),
           this.listProducts()
       ]);
   }
}
</script>
