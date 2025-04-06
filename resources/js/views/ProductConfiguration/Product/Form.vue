<template>
  <div>
  
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Product
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
              />
              <span v-if="errors.product_line_code" class="text-theme-6">
                {{ errors.product_line_code[0] }}
              </span>
            </div>

            <div class="col-span-12">
              <label class="mb-1 block font-bold">Group</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.group_code"
                :options="autoGroupOptions"
                optionLabel="label"
                optionValue="value"
                placeholder="Group"
              />
            </div>
            <div class="col-span-12">
              <label class="mb-1 block font-bold">Product Name (English) *</label>
              <InputText
               class="w-full p-inputtext-sm"
               v-model="formValues.name"
               placeholder="Product Name (English)"
              />
              <span v-if="errors.name" class="text-theme-6">
                {{ errors.name[0] }}
              </span> 
            </div>

            <div class="col-span-12">
                <label class="mb-1 block font-bold">Product Name (Khmer)</label>
                <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.name_kh"
                placeholder="Product Name (Khmer)"
                />
            </div>

            <div class="col-span-12">
              <label class="mb-1 block font-bold">Product Name (Chinese)</label>
              <InputText
              class="w-full p-inputtext-sm"
              v-model="formValues.name_zh"
              placeholder="Product Name (Chinese)"
              />
            </div>

            <div class="col-span-12">
              <label class="font-bold mb-1 block">Description (English)</label>
              <Textarea
                class="w-full"
                rows="3"
                v-model="formValues.description"
                placeholder="Description (English)"
              />
            </div>

            <div class="col-span-12">
              <label class="font-bold mb-1 block">Description (Khmer)</label>
              <Textarea
              class="w-full"
              rows="3"
              v-model="formValues.description_kh"
              placeholder="Description (Khmer)"
              />
            </div>

            <div class="col-span-12">
              <label class="font-bold mb-1 block">Description (Chinese)</label>
              <Textarea
              class="w-full"
              rows="3"
              v-model="formValues.description_zh"
              placeholder="Description (Chinese)"
              />
            </div>
  
            <div class="col-span-12">
              <label class="font-bold mb-1 block">Alternative Code *</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.alt_code"
                placeholder="Alternative Code"
              />
              <span v-if="errors.alt_code" class="text-theme-6">
                {{ errors.alt_code[0] }}
              </span>
            </div>

            <div class="col-span-12">
              <label for="" class="font-bold block mb-1">Default Surcharge (%)</label>
              <InputNumber
                class="w-full"
                v-model="formValues.default_surcharge"
                :min="0"
                step="any"
                placeholder="Default Surcharge (%)"
              />
            </div>

            <div class="col-span-12">
              <label for="" class="font-bold block mb-1">Default Discount (%)</label>
              <InputNumber
                class="w-full"
                v-model="formValues.default_discount"
                :min="0"
                step="any"
                placeholder="Default Discount (%)"
              />
            </div>

            <div class="col-span-12">
              <label for="" class="font-bold block mb-1">Default NCD (%)</label>
              <InputNumber
                class="w-full"
                v-model="formValues.default_ncd"
                :min="0"
                step="any"
                placeholder="Default NCD (%)"
              />
            </div>
            
            <div class="col-span-12">
              <label class="mb-1 block font-bold">Specification</label>
              <Dropdown 
                class="w-full p-inputtext-sm"
                v-model="formValues.prod_specification"
                :options="commercialVehicleTypeOptions"
                optionLabel="label"
                optionValue="value"
                placeholder="Specification"
                :filter="true"
              />
            </div>

            <div class="col-span-12">
              <label class="font-bold mb-1 block">Limitation to use (English)</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.limitation_to_use_en"
                placeholder="Limitation to use (English)"
              />
            </div>

            <div class="col-span-12">
              <label class="font-bold mb-1 block">Limitation to use (Khmer)</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.limitation_to_use_kh"
                placeholder="Limitation to use (Khmer)"
              />
            </div>

            <div class="col-span-12">
              <label class="font-bold mb-1 block">Limitation to use (Chinese)</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.limitation_to_use_zh"
                placeholder="Limitation to use (Chinese)"
              />
            </div>
              
            <div class="col-span-12">
              <label class="block mb-1 font-bold">Coverage (English) *</label>
              <CKEditor 
              v-model="formValues.coverage_en"
              placeholder="Coverage (English) "
              />
              <span v-if="errors.coverage_en" class="text-theme-6">
              {{ errors.coverage_en[0] }}
              </span>
            </div>

            <div class="col-span-12">
              <label class="block mb-1 font-bold">Coverage (Khmer) *</label>
              <CKEditor 
              v-model="formValues.coverage_kh"
              placeholder="Coverage (Khmer)"
              />
              <span v-if="errors.coverage_kh" class="text-theme-6">
              {{ errors.coverage_kh[0] }}
              </span>
            </div>
            
            <div class="col-span-12">
              <label class="block mb-1 font-bold">Coverage (Chinese) *</label>
              <CKEditor 
                v-model="formValues.coverage_zh"
                placeholder="Coverage (Chinese)"
              />
            </div>
            
            <div class="col-span-12">
              <label class="block mb-1 font-bold">Is Renewable</label>
              <Checkbox 
              class="block mb-1 font-bold"
              v-model="formValues.renewable"
              :binary="true"
              />
              
            </div>

          </div>
          <div class="text-right mt-5">
            <router-link to="/product-configuration/products" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
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
import CKEditor from '../../../components/Form/CKEditor.vue'

export default {
  components: {
    CKEditor
  },
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        product_line_code: '',
        group_code: '',
        name: '',
        name_kh: '',
        name_zh: '',
        description: '',
        description_kh: '',
        description_zh: '',
        alt_code: '',
        default_surcharge: 0,
        default_discount: 0,
        default_ncd: 0,
        prod_specification: '',
        limitation_to_use_en: '',
        limitation_to_use_kh: '',
        limitation_to_use_zh: '',
        coverage_en: '',
        coverage_kh: '',
        coverage_zh: '',
        renewable: false,
        status: 'ACTIVE'
      },
      productLineOptions: [],
      autoGroupOptions: [],
      commercialVehicleTypeOptions: [],
      errors: {},
      loading: false
    }
  },
  methods: {
    async listProductLines() {
      try {
        const response = await axios.get("/product-line-service/list-product-lines");
        this.productLineOptions = response.data.map(item => ({
          label: item.label,
          value: item.value
        }));
      } catch (error) {
        notify('Error loading product lines: ' + error.message, 'error', 'bottom-right');
        console.error('Error loading product lines:', error);
        this.productLineOptions = [];
      }
    },
    async listAutoProductGroups() {
      try {
        const response = await axios.get('/product-service/list-auto-product-groups');
        this.autoGroupOptions = response.data.map(item => ({
          label: item.name || item.label,
          value: item.code || item.value || item.id,
          desc: item.description
        }));
      } catch (error) {
        notify('Error loading auto product groups', 'error', 'bottom-right');
        console.error('Error loading auto product groups:', error);
      }
    },

    async listCommercialVehicleTypes() {
      try {
        const response = await axios.get('/product-service/list-commercial-vehicle-types');
        this.commercialVehicleTypeOptions = response.data.map(item => ({
          label: item.name || item.label,
          value: item.code || item.value || item.id,
          desc: item.description
        }));
      } catch (error) {
        notify('Error loading vehicle types', 'error', 'bottom-right');
        console.error('Error loading vehicle types:', error);
      }
    },

    async getProduct() {
      if (!this.id) return;
      
      try {
        const response = await axios.get(`/products/${this.id}`);
        const data = response.data;
        this.formValues = {
          product_line_code: data.product_line_code || '',
          group_code: data.group_code || '',
          name: data.name || '',
          name_kh: data.name_kh || '',
          name_zh: data.name_zh || '',
          description: data.description || '',
          description_kh: data.description_kh || '',
          description_zh: data.description_zh || '',
          alt_code: data.alt_code || '',
          default_surcharge: Number(data.default_surcharge || 0),
          default_discount: Number(data.default_discount || 0),
          default_ncd: Number(data.default_ncd || 0),
          prod_specification: data.prod_specification || '',
          limitation_to_use_en: data.limitation_to_use_en || '',
          limitation_to_use_kh: data.limitation_to_use_kh || '',
          limitation_to_use_zh: data.limitation_to_use_zh || '',
          coverage_en: data.coverage_en || '',
          coverage_kh: data.coverage_kh || '',
          coverage_zh: data.coverage_zh || '',
          renewable: Boolean(data.renewable),
          status: data.status || 'ACTIVE'
        };
      } catch (error) {
        notify('Error loading product data', 'error', 'bottom-right');
        console.error('Error loading product:', error);
      }
    },
    async handleSubmit() {
      this.loading = true;
      try {
        const url = this.id ? `/products/${this.id}` : '/products';
        const method = this.id ? 'put' : 'post';
        
        const response = await axios[method](url, this.formValues);
        
        if (response.data.success) {
          notify(response.data.message, 'success', 'bottom-right');
          this.$router.push("/product-configuration/products");
        }
      } catch (err) {
        if (err?.response?.status === 422) {
          this.errors = err.response.data.errors;
        } else {
          notify('Error saving product', 'error', 'bottom-right');
          console.error('Error saving:', err);
        }
      } finally {
        this.loading = false;
      }
    }
  },
  async mounted() {
    try {
      await Promise.all([
        this.listProductLines(),
        this.listAutoProductGroups(),
        this.listCommercialVehicleTypes()
      ]);
      await this.getProduct();
    } catch (error) {
      console.error('Error initializing form:', error);
      notify('Error initializing form', 'error', 'bottom-right');
    }
  }
};
</script>