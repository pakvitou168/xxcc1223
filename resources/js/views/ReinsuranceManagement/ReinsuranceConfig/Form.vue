<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Reinsurance Config
      </h2>
    </div>
    <div class="mt-5">
      <div class="intro-y col-span-12">
        <div class="intro-y box p-5">
          <div class="grid grid-cols-12 gap-x-10 gap-y-4">
            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">Product Line *</label>
                <Dropdown
                  class="w-full p-inputtext-sm"
                  v-model="formValues.product_line_code"
                  :options="productLineOptions"
                  optionLabel="label" 
                  optionValue="value"
                  placeholder="Product Line"
                  @change="onProductLine($event.value)"
                />
              <span v-if="errors.product_line_code" class="text-theme-6">
                {{ errors.product_line_code[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
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
                  <div>
                    <div class="text-sm font-semibold">{{slotProps.option.label}}</div>
                    <div class="text-xs">{{slotProps.option.desc}}</div>
                  </div>
                </template>
              </Dropdown>
              <span v-if="errors.product_code" class="text-theme-6">
                {{ errors.product_code[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">Reinsurance *</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.reinsurance_code"
                :options="optionsCode.reinsurance"
                optionLabel="label"
                optionValue="value"
                placeholder="Reinsurance"
              />
              <span v-if="errors.reinsurance_code" class="text-theme-6">
                {{ errors.reinsurance_code[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">Reinsurance Type *</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.reinsurance_type"
                :options="optionsCode.type"
                optionLabel="label"
                optionValue="value"  
                placeholder="Reinsurance Type"
              />
              <span v-if="errors.reinsurance_type" class="text-theme-6">
                {{ errors.reinsurance_type[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">Partner *</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.partner_code"
                :options="optionsCode.partner"
                optionLabel="label"
                optionValue="value"
                placeholder="Partner"
              />
              <span v-if="errors.partner_code" class="text-theme-6">
                {{ errors.partner_code[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">Parent Code</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.parent_code"
                :options="parentCodeOptions"
                optionLabel="label"
                optionValue="value"
                placeholder="Parent Code"
              />
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">Start From *</label>
              <Calendar
                v-model="formValues.start_from"
                class="w-full p-inputtext-sm"
                dateFormat="dd/mm/yy"
                placeholder="Start From"
              />
              <span v-if="errors.start_from" class="text-theme-6">
                {{ errors.start_from[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">To *</label>
              <Calendar
                v-model="formValues.start_to"
                class="w-full p-inputtext-sm"
                dateFormat="dd/mm/yy"
                :minDate="formValues.start_from"
                placeholder="To"
              />
              <span v-if="errors.start_to" class="text-theme-6">
                {{ errors.start_to[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">Leaf *</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.leaf"
                :options="[{label: 'Yes', value: 'Y'}, {label: 'No', value: 'N'}]"
                optionLabel="label"
                optionValue="value"
                placeholder="Leaf"
              />
              <span v-if="errors.leaf" class="text-theme-6">
                {{ errors.leaf[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">Share Basis</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.share_basis"
                placeholder="Share Basis"
              />
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">UW Year *</label>
              <InputNumber
                class="w-full"
                v-model="formValues.uw_year"
                placeholder="UW Year"
                :useGrouping="false"
              />
              <span v-if="errors.uw_year" class="text-theme-6">
                {{ errors.uw_year[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">Share % *</label>
              <InputNumber
                class="w-full"
                v-model="formValues.share"
                placeholder="Share"
                :min="0"
                mode="decimal"
                :minFractionDigits="2"
              />
              <span v-if="errors.share" class="text-theme-6">
                {{ errors.share[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">Amount Cap *</label>
              <InputNumber
                class="w-full"
                v-model="formValues.amount_cap"
                placeholder="Amount Cap"
                :min="0"
                mode="decimal"
                :minFractionDigits="2"
              />
              <span v-if="errors.amount_cap" class="text-theme-6">
                {{ errors.amount_cap[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">RI Commission % *</label>
              <InputNumber
                class="w-full"
                v-model="formValues.ri_commission"
                placeholder="RI Commission"
                :min="0"
                mode="decimal"
                :minFractionDigits="2"
              />
              <span v-if="errors.ri_commission" class="text-theme-6">
                {{ errors.ri_commission[0] }}
              </span>
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="mb-1 block font-bold">Tax Fee % *</label>
              <InputNumber
                class="w-full"
                v-model="formValues.tax_fee"
                placeholder="Tax Fee"
                :min="0"
                mode="decimal"
                :minFractionDigits="2"
              />
              <span v-if="errors.tax_fee" class="text-theme-6">
                {{ errors.tax_fee[0] }}
              </span>
            </div>
          </div>
          <div class="text-right mt-5">
            <router-link to="/reinsurance-management/reinsurance-configs" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
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
import axios from "axios";

export default {
  data() {
  return {
    id: this.$route.params.id ?? null,
    formValues: {
      product_line_code: '',
      product_code: '',
      reinsurance_code: '',
      reinsurance_type: {  // Change to object structure
        value: '',
        label: ''
      },
      partner_code: '',
      parent_code: {       // Change to object structure
        value: '',
        label: ''
      },
      start_from: null,
      start_to: null,
      leaf: '',
      share_basis: '',
      uw_year: null,
      share: null,
      amount_cap: null,
      ri_commission: null,
      tax_fee: null
    },
    productLineOptions: [],
    productOptions: [],
    parentCodeOptions: [],
    optionsCode: {
      reinsurance: '',
      type: '',
      partner: '',
    },
    errors: [],
    isLoadingProducts: false,
    isLoadingParentCodes: false,
  };
},
  methods: {
    // list
    listProductLines() {
      axios.get("/product-line-service/list-product-lines").then((response) => {
        this.productLineOptions = response.data;
      });
    },

  changeProduct(value) {
    // Clear dependent field
    this.formValues.parent_code = '';
    this.parentCodeOptions = [];
    
    if (value) {
      this.isLoadingParentCodes = true;
      axios
        .get("/reinsurance-config/parent-by-product-code/" + value)
        .then((response) => {
          this.parentCodeOptions = response.data;
        })
        .catch(err => {
          notify("Error loading parent codes", "error",'bottom-right');
        })
        .finally(() => {
          this.isLoadingParentCodes = false;
        });
      }
    },
    list3Re() {
      axios
        .get("/reinsurance-config/3re")
        .then((response) => {
          Object.assign(this.optionsCode,response.data);
        });
    },

    getReinsuranceConfig() {
      if (this.id) {
        axios.get(`/reinsurance-configs/${this.id}/edit`)
          .then((response) => {
            // Handle date formatting
            const data = {
              ...response.data,
              start_from: response.data.start_from ? new Date(response.data.start_from) : null,
              start_to: response.data.start_to ? new Date(response.data.start_to) : null,
            };

            this.formValues = data;
          })
          .then(async () => {  // Make this async to handle promises
            // Load product options if product line exists
            if (this.formValues.product_line_code) {
              await axios.get("/reinsurance-config/products-by-product-line/" + this.formValues.product_line_code)
                .then((response) => {
                  this.productOptions = response.data;
                });
            }

            // Load parent codes if product exists
            if (this.formValues.product_code) {
              await axios.get("/reinsurance-config/parent-by-product-code/" + this.formValues.product_code)
                .then((response) => {
                  this.parentCodeOptions = response.data;
                });
            }

            // Load reinsurance options (3re)
            await this.list3Re();
          })
          .catch((error) => {
            notify("Error loading data", "error",'bottom-right');
            console.error(error);
          });
      }
    },

  onProductLine(value) {
      // Clear dependent fields
      this.formValues.product_code = '';
      this.formValues.parent_code = '';
      this.productOptions = [];
      
      if (value) {
        this.isLoadingProducts = true;
        axios
          .get("/reinsurance-config/products-by-product-line/" + value)
          .then((response) => {
            this.productOptions = response.data;
          })
          .catch(err => {
            notify("Error loading products", "error",'bottom-right');
          })
          .finally(() => {
            this.isLoadingProducts = false;
          });
      }
    },
    // handle
    handleSubmit() {
      if (!this.id) {
        axios
          .post("/reinsurance-configs", this.formValues)
          .then((response) => {
            if (response.data.success) {
              notify(response.data.message, "success",'bottom-right');
              this.$router.push({ name: "ReinsuranceConfigIndex" });
            }
            else if(response.data?.error) {
              this.errors = response.data.error;
              notify(response.data.message, "error",'bottom-right');
              this.tabulator?.replaceData();
            }
          }).catch(err => {
                if (err?.response)
                    notify(err.response?.data?.message, "error",'bottom-right');
                else
                    notify("Something wrong...!", "error",'bottom-right');
            })
      } else {
        axios
          .put(`/reinsurance-configs/${this.id}`, this.formValues)
          .then((response) => {
            if (response.data.success) {
              notify(response.data.message, "success",'bottom-right');
              this.$router.push({ name: "ReinsuranceConfigIndex" });
            }
            else if(response.data?.error) {
              this.errors = response.data.error;
              notify(response.data.message, "error",'bottom-right');
              this.tabulator?.replaceData();
            }
          })
            .catch(err => {
                if (err?.response)
                    notify(err.response?.data?.message, "error",'bottom-right');
                else
                    notify("Something wrong...!", "error",'bottom-right');
            })
      }
    },

  },
  mounted() {
    this.getReinsuranceConfig();
    this.listProductLines();
    this.list3Re();
  },
};
</script>