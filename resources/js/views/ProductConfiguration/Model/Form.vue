<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Model
      </h2>
    </div>
    
    <div class="grid grid-cols-12 mt-5">
      <div class="intro-y box col-span-12 lg:col-span-6 p-5">
        <div class="grid grid-cols-12 gap-x-10 gap-y-4">
          
          <div class="col-span-12">
            <label class="mb-1 block font-bold">Make *</label>
            <Dropdown 
              class="w-full p-inputtext-sm"
              v-model="formValues.make_id"
              :options="makeOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Make"
            />
            <span v-if="errors.make_id" class="text-theme-6">
              {{ errors.make_id[0] }}
            </span>
          </div>

          <div class="col-span-12">
            <label class="mb-1 block font-bold">Model *</label>
            <InputText
             class="w-full p-inputtext-sm"
             v-model="formValues.model"
             placeholder="Model"
            />
            <span v-if="errors.model" class="text-theme-6">
              {{ errors.model[0] }}
            </span> 
          </div>

          <div class="col-span-12">
            <label class="mb-1 block font-bold">Product *</label>
            <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.product_code"
                :options="autoProductOptions"
                optionLabel="label"
                optionValue="value"
                placeholder="Product"
                :showClear="true"
            >
            <template #option="slotProps">
              <p class="text-sm font-semibold">{{slotProps.option.label}}</p>
              <span class="text-xs">{{slotProps.option.desc}}</span>
            </template>
            </Dropdown>
          </div>

          <div class="col-span-12">
              <label class="mb-1 block font-bold">Vehicle Type *</label>
              <Dropdown
                  class="w-full p-inputtext-sm"
                  v-model="formValues.vehicle_type"
                  :options="vehicleTypeOptions"
                  optionLabel="name"
                  optionValue="name"
                  placeholder="Vehicle Type"
                  :showClear="true"
              >
              </Dropdown>
            <span v-if="errors.model" class="text-theme-6">
              {{ errors.vehicle_type[0] }}
            </span> 
          </div>

          <div class="col-span-12">
            <label class="mb-1 block font-bold">Classification *</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.classification"
              :options="classificationOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Classification"
            />
            <span v-if="errors.classification" class="text-theme-6">
              {{ errors.vehicle_type[0] }}
            </span> 
          </div>

          <div class="grid grid-cols-12 gap-x-6">
            <div class="col-span-12 lg:col-span-6">
              <label class="block mb-1 font-bold">Offline</label>
              <Checkbox
                v-model="formValues.available_offline"
                :binary="true"
              />
            </div>
            <div class="col-span-12 lg:col-span-6">
              <label class="block mb-1 font-bold">Online</label>
              <Checkbox
                v-model="formValues.available_online"
                :binary="true"
              />
            </div>
          </div>          

        </div>
        <div class="text-right mt-5">
          <router-link to="/product-configuration/models" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
          <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
            <span v-if="id">Update</span>
            <span v-else>Create</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
 name: 'ModelForm',
 
 data() {
   return {
     id: this.$route.params.id ?? null,
     formValues: {
       make_id: '',
       model: '',
       product_code: '', 
       vehicle_type: '',
       classification: '',
       available_offline: false,
       available_online: false
     },
     autoProductOptions: [],
     makeOptions: [],
     vehicleTypeOptions: [],
     classificationOptions: [],
     errors: {},
     loading: false
   }
 },

 methods: {
   async handleSubmit() {
     this.loading = true;
     try {
       const url = this.id ? `/models/${this.id}` : '/models';
       const method = this.id ? 'put' : 'post';
       
       const response = await axios[method](url, this.formValues);
       
       if (response.data.success) {
         notify(response.data.message, 'success', 'bottom-right');
         this.$router.push({ name: 'ModelIndex' });
       }
     } catch (err) {
       if (err?.response?.status === 422) {
         this.errors = err.response.data.errors;
       } else {
         console.error('Error saving model:', err);
         notify('Error saving model', 'error', 'bottom-right');
       }
     } finally {
       this.loading = false;
     }
   },
   async getModelServices() {
     try {
       const servicesResponse = await axios.get('/model-service/get-model-services');
       this.makeOptions = Array.isArray(servicesResponse.data.makeOptions) 
         ? servicesResponse.data.makeOptions 
         : Object.entries(servicesResponse.data.makeOptions).map(([value, label]) => ({
             label,
             value
           }));
       
       this.vehicleTypeOptions = servicesResponse.data.vehicleTypeOptions;
       this.classificationOptions = Array.isArray(servicesResponse.data.classificationOptions)
         ? servicesResponse.data.classificationOptions
         : Object.entries(servicesResponse.data.classificationOptions).map(([value, label]) => ({
             label,
             value
           }));
       const productsResponse = await axios.get('/model-service/list-products-with-desc');
       this.autoProductOptions = Array.isArray(productsResponse.data)
         ? productsResponse.data
         : Object.entries(productsResponse.data).map(([value, item]) => ({
             label: item.name || item.label || value,
             value: item.code || item.value || value,
             desc: item.description || ''
           }));

     } catch (error) {
       console.error('Error loading model services:', error);
       notify('Error loading form data', 'error', 'bottom-right');
     }
   },

   async getModel() {
     if (!this.id) return;
     
     try {
       const response = await axios.get(`/models/${this.id}`);
       this.formValues = {
         make_id: response.data.make_id ? String(response.data.make_id) : '',
         model: response.data.model || '',
         product_code: response.data.product_code || '',
         vehicle_type: response.data.vehicle_type || '',
         classification: response.data.classification || '',
         available_offline: Boolean(response.data.available_offline),
         available_online: Boolean(response.data.available_online)
       };
       console.log('Processed Form Values:', this.formValues);
     } catch (error) {
       console.error('Error loading model:', error);
       notify('Error loading model data', 'error', 'bottom-right');
     }
   }
 },

 async mounted() {
   try {
     await Promise.all([
       this.getModel(),
       this.getModelServices()
     ]);
   } catch (error) {
     console.error('Error initializing form:', error);
     notify('Error initializing form', 'error', 'bottom-right');
   }
 }
};
</script>
