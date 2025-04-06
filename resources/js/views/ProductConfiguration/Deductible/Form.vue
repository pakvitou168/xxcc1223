<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Deductible
      </h2>
    </div>
    
    <div class="intro-y box mt-5 p-5">
      <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12">
          <div class="grid grid-cols-12 gap-x-10 gap-y-4">
            
            <!-- Product Line, Product, Cover Row -->
            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Product Line *</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.product_line_code"
                :options="lovs.productLines"
                optionLabel="label"
                optionValue="value"
                placeholder="Product Line"
                @change="changeProductLine"
              />
              <span v-if="errors.product_line_code" class="text-theme-6">
                {{ errors.product_line_code[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Product *</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.product_code"
                :options="lovs.products"
                optionLabel="label"
                optionValue="value"
                placeholder="Product"
                @change="changeProduct"
              >
                <template #option="slotProps">
                  <p class="text-sm font-semibold">{{ slotProps.option.label }}</p>
                  <span class="text-xs">{{ slotProps.option.desc }}</span>
                </template>
              </Dropdown>
              <span v-if="errors.product_code" class="text-theme-6">
                {{ errors.product_code[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Cover *</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.comp_code"
                :options="lovs.covers"
                optionLabel="label"
                optionValue="value"
                placeholder="Cover"
              />
              <span v-if="errors.comp_code" class="text-theme-6">
                {{ errors.comp_code[0] }}
              </span>
            </div>

            <!-- Labels Row -->
            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Label (English) *</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.label"
                placeholder="Label (English)"
              />
              <span v-if="errors.label" class="text-theme-6">
                {{ errors.label[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Label (Khmer)</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.label_kh"
                placeholder="Label (Khmer)"
              />
            </div>

            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Label (Chinese)</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.label_zh"
                placeholder="Label (Chinese)"
              />
            </div>

            <!-- Descriptions Row -->
            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Description (English)</label>
              <Textarea
                class="w-full"
                v-model="formValues.description"
                rows="3"
                placeholder="Description (English)"
              />
            </div>

            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Description (Khmer)</label>
              <Textarea
                class="w-full"
                v-model="formValues.description_kh"
                rows="3"
                placeholder="Description (Khmer)"
              />
            </div>

            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Description (Chinese)</label>
              <Textarea
                class="w-full"
                v-model="formValues.description_zh"
                rows="3"
                placeholder="Description (Chinese)"
              />
            </div>

            <!-- Value Labels Row -->
            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Value Label (English) *</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.value"
                placeholder="Value Label (English)"
              />
              <span v-if="errors.value" class="text-theme-6">
                {{ errors.value[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Value Label (Khmer)</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.value_kh"
                placeholder="Value Label (Khmer)"
              />
            </div>

            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Value Label (Chinese)</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.value_zh"
                placeholder="Value Label (Chinese)"
              />
            </div>

            <!-- Value Configuration Row -->
            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Value Type *</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.cond_value_type"
                :options="lovs.valueTypes"
                optionLabel="label"
                optionValue="value"
                placeholder="Value Type"
              />
              <span v-if="errors.cond_value_type" class="text-theme-6">
                {{ errors.cond_value_type[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Value *</label>
              <InputNumber
                class="w-full"
                v-model="formValues.cond_value"
                placeholder="Value"
                :min="0"
                mode="decimal"
                :minFractionDigits="0"
                :maxFractionDigits="2"
                :step="0.01"
              />
              <span v-if="errors.cond_value" class="text-theme-6">
                {{ errors.cond_value[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-4">
              <div class="grid grid-cols-2 gap-x-5">
                <div>
                  <label class="mb-1 block font-bold">Min Value *</label>
                  <InputNumber
                    class="w-full"
                    v-model="formValues.min_value"
                    placeholder="Min Value"
                    :min="0"
                    mode="decimal"
                    :minFractionDigits="2"
                  />
                  <span v-if="errors.min_value" class="text-theme-6">
                    {{ errors.min_value[0] }}
                  </span>
                </div>
                <div>
                  <label class="mb-1 block font-bold">Max Value</label>
                  <InputNumber
                    class="w-full"
                    v-model="formValues.max_value"
                    placeholder="Max Value"
                    :min="0"
                    mode="decimal"
                    :minFractionDigits="2"
                  />
                  <span v-if="errors.max_value" class="text-theme-6">
                    {{ errors.max_value[0] }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Condition Row -->
            <div class="col-span-12">
              <div class="flex items-center">
                <Checkbox
                  v-model="formValues.cond_type"
                  :binary="true"
                  class="mr-2"
                />
                <label class="font-bold">Has Condition</label>
              </div>
            </div>

            <div v-if="formValues.cond_type" class="col-span-12 lg:col-span-4">
              <label class="mb-1 block font-bold">Condition Expression</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.cond_expr"
                placeholder="Condition Expression"
              />
            </div>
          </div>

          <!-- Form Actions -->
          <div class="text-right mt-5">
            <router-link 
              :to="{name: 'DeductibleIndex'}" 
              class="btn btn-outline-secondary w-24 mr-1"
            >
              Cancel
            </router-link>
            <button 
              type="button"
              @click="handleSubmit"
              :disabled="isDisabled"
              class="btn btn-primary w-24"
            >
              {{ isLoading ? 'Saving ...' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import DeductibleService from '@/services/product_config/deductible.service'

export default {
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        product_line_code: '',
        product_code: '',
        comp_code: '',
        label: '',
        label_kh: '',
        label_zh: '',
        description: '',
        description_kh: '',
        description_zh: '',
        value: '',
        value_kh: '',
        value_zh: '',
        cond_value_type: '',
        cond_value: 0,
        min_value: 0,
        max_value: 0,
        cond_type: false,
        cond_expr: '',
        currency: 'USD',
        status: 'ACT'
      },
      lovs: {
        productLines: [],
        products: [],
        covers: [],
        valueTypes: []
      },
      errors: {},
      isLoading: false,
      isDisabled: false
    }
  },
  methods: {
    normalizeOptions(data, labelKey = 'name', valueKey = 'code', descKey = null) {
      if (!Array.isArray(data)) return [];
      
      return data.map(item => {
        const option = {
          label: item[labelKey] || item.label || item[valueKey],
          value: item[valueKey] || item.value || item[labelKey]
        };
        
        if (descKey && item[descKey]) {
          option.desc = item[descKey];
        }
        
        return option;
      });
    },


    handleSubmit() {
      this.isLoading = true;
      this.isDisabled = true;

      const method = this.id ? "PUT" : "POST";
      const payload = {
        ...this.formValues,
        cond_value: this.formValues.cond_value?.toString(),
        min_value: this.formValues.min_value?.toString(),
        max_value: this.formValues.max_value?.toString(),
        ...(method === "PUT" && { id: this.id })
      };

      DeductibleService.save(payload, method)
        .then(res => {
          notify('success', 'success','bottom-right', res.data?.message);
          this.$router.push({name: 'DeductibleIndex'});
        })
        .catch(err => {
          if (err?.response?.status === 422) {
            notify('error', 'error','bottom-right');
            this.errors = err.response.data.errors;
          }
        })
        .finally(() => {
          this.isLoading = false;
          this.isDisabled = false;
        });
    },

    getLovs() {
      DeductibleService.getLovs().then(res => {
        this.lovs.productLines = Array.isArray(res.data?.productLines) 
          ? res.data.productLines 
          : Object.entries(res.data?.productLines || {}).map(([value, label]) => ({
              label,
              value
            }));

        this.lovs.valueTypes = Object.entries(res.data?.valueTypes || {}).map(([value, label]) => ({
          label: label,
          value: value
        }));
      });
    },

    changeProductLine({value}) {
      this.formValues.product_code = null;
      this.formValues.comp_code = null; 
      this.listProducts(value);
    },

    listProducts(value) {
      DeductibleService.listProducts(value).then(res => {
        this.lovs.products = (res.data || []).map(product => ({
          label: product.label || '',  
          value: product.value || '',  
          desc: product.desc || ''     
        }));
      }).catch(err => {
        notify('error', 'Error', 'Failed to load products');
      });
    },

    changeProduct({value}) {
      this.formValues.comp_code = null;
      this.listCovers(value);
    },

    listCovers(value) {
      DeductibleService.listCovers(value).then(res => {
        this.lovs.covers = this.normalizeOptions(res.data);
      });
    },

    getData() {
      if (this.id) {
        DeductibleService.getData(this.id).then(res => {
          const data = {
            ...res.data,
            cond_value: parseFloat(res.data.cond_value) || 0,
            min_value: parseFloat(res.data.min_value) || 0,
            max_value: parseFloat(res.data.max_value) || 0,
            cond_type: Boolean(res.data.cond_type)
          };

          this.formValues = data;
          this.formValues.product_line_code = data.product?.product_line_code;
          
          if (data.product_line_code) {
            this.listProducts(data.product_line_code);
          }
          if (data.product_code) {
            this.listCovers(data.product_code);
          }
        })
        .catch(err => {
          notify('error', 'Error', err?.response?.data?.message);
        });
      }
    }
  },
  mounted() {
    console.log('Component mounted, id:', this.id);
    this.getData();
    this.getLovs();
  }
}
</script>