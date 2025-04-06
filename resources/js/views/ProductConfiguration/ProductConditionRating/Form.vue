<template>
  <div>
    <!-- Start Header  -->
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Product Condition Rating
      </h2>
    </div>
    <!-- End Header  -->
    <!--Start Form  -->
    <form @submit.prevent="handleSubmit">
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
                    name="product_code"
                    @change="checkProduct()"
                >
                  <template #option="slotProps">
                    <p class="text-sm font-semibold">{{ slotProps.option.label }}</p>
                    <span class="text-xs">{{ slotProps.option.desc }}</span>
                  </template>
                </Dropdown>
                <span class="text-red text-xs text-red-700 text-error" v-if="productError">Product is required</span>
              </div>
              <div class="col-span-12">
                <label for="" class=" block text-sm font-medium text-gray-900">Has Condition Type</label>
                <input
                    type="checkbox"
                    v-model="formValues.has_cond_type"
                    name="has_cond_type"
                    class="mt-2 h-5 w-5 border-gray-300 text-blue-600 focus:ring-blue-600"
                    @change="conditionTypeChange"
                />
              </div>
              <div class="col-span-12" v-if="formValues.has_cond_type">
                <label class="mb-1 block font-bold">Expression Condition *</label>
                <Dropdown
                    class="w-full p-inputtext-sm"
                    v-model="formValues.cond_expr"
                    :options="expressionConditionOption"
                    optionLabel="description"
                    optionValue="enum_id"
                    placeholder="Expression Condition"
                    name="cond_expr"
                    @change="checkExpression()"
                >
                </Dropdown>
                <span class="text-red text-xs text-red-700 text-error" v-if="expressionError">Expression Condition is required</span>
              </div>
              <div class="col-span-12">
                <label for="" class="block mb-1 font-bold">Code *</label>
                <InputText
                    v-model="formValues.code"
                    class="w-full"
                    placeholder="Code"
                    disabled="true"
                />
              </div>
              <div class="col-span-12">
                <label for="" class="block mb-1 font-bold">Value *</label>
                <InputNumber
                    v-model="formValues.value"
                    class="w-full"
                    :maxFractionDigits="2"
                    :min="0"
                    :max="100"
                    placeholder="value"
                    @change="checkValue()"
                />
                <span class="text-red text-xs text-red-700 text-error" v-if="valueError">Value is required</span>
              </div>

            </div>
            <div class="text-right mt-5">
              <router-link :to="{name: 'ProductConditionIndex'}" class="btn btn-outline-secondary w-24 mr-1"
                           tag="button">Cancel
              </router-link>
              <button type="submit" :disabled="isDisabled"
                      class="btn btn-primary w-24"><i v-if="isLoading"
                                                      class="pi pi-spinner pi-spin mr-1"></i>
                Save
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
<script>
import ProductConditionService from '@/services/product_config/product_condition_rating.service'

export default {
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        product_code: null,
        cond_type: 'N',
        cond_expr: null,
        code: 'REFUND_DEFAULT_PERCENTAGE',
        value: null,
        has_cond_type: false,
        description: '',
        key: null
      },
      productOptions: [],
      expressionConditionOption: [
        {
          enum_id: 'EFFECTIVE_MONTH < 1',
          description: 'Less than 1 Month'
        },
        {
          enum_id: 'EFFECTIVE_MONTH >= 1 AND EFFECTIVE_MONTH < 2',
          description: '1- < 2 Months'
        },
        {
          enum_id: 'EFFECTIVE_MONTH >= 2 AND EFFECTIVE_MONTH < 3',
          description: '2- < 3 Months'
        },
        {
          enum_id: 'EFFECTIVE_MONTH >= 3 AND EFFECTIVE_MONTH < 4',
          description: '3- < 4 Months'
        },
        {
          enum_id: 'EFFECTIVE_MONTH >= 4 AND EFFECTIVE_MONTH < 5',
          description: '4- < 5 Months'
        },
        {
          enum_id: 'EFFECTIVE_MONTH >= 5 AND EFFECTIVE_MONTH < 6',
          description: '5- < 6 Months'
        },
        {
          enum_id: 'EFFECTIVE_MONTH >= 6 AND EFFECTIVE_MONTH < 7',
          description: '6- <7 Months'
        },
        {
          enum_id: 'EFFECTIVE_MONTH >= 7 AND EFFECTIVE_MONTH < 8',
          description: '7- < 8 Months'
        },
        {
          enum_id: 'EFFECTIVE_MONTH >= 8 AND EFFECTIVE_MONTH < 9',
          description: '8- < 9 Months'
        },
        {
          enum_id: 'EFFECTIVE_MONTH >= 9 AND EFFECTIVE_MONTH < 10',
          description: '9- < 10 Months'
        },
        {
          enum_id: 'EFFECTIVE_MONTH >= 10 AND EFFECTIVE_MONTH < 11',
          description: '10- < 11 Months'
        },
        {
          enum_id: 'EFFECTIVE_MONTH >= 11',
          description: '11- 12 Months'
        }
      ],
      errors: {},
      isLoading: false,
      isDisabled: false,
      productError: false,
      expressionError: false,
      valueError: false
    }
  },
  methods: {
    checkValue() {
      if (this.formValues.value == null || this.formValues.value == '') {
        this.valueError = true
      } else {
        this.valueError = false
      }
    },
    checkExpression() {
      if (this.formValues.cond_expr == null || this.formValues.cond_expr == '') {
        this.expressionError = true
      } else {
        this.expressionError = false
      }
    },
    checkProduct() {
      if (this.formValues.product_code == null || this.formValues.product_code == '') {
        this.productError = true
      } else {
        this.productError = false
      }
    },
    validate() {
      this.checkProduct()
      this.checkExpression()
      this.checkValue()
    },
    handleSubmit() {
      this.validate()
      if (this.productError || this.valueError || (this.formValues.has_cond_type && (this.expressionError))) return
      this.isLoading = true
      this.isDisabled = true

      if (this.formValues.has_cond_type) {
        var expressValue = this.expressionConditionOption.find(obj => obj.enum_id == this.formValues.cond_expr)
        this.formValues.description = expressValue.description
      } else {
        this.formValues.description = 'default refund percentage'
      }
      const method = this.id ? "PUT" : "POST"
      ProductConditionService.save(
          {
            ...this.formValues,
            ...(method === "PUT" && {id: this.id})
          },
          method
      ).then(res => {
        this.$notify({
          group: 'bottom',
          title: 'Success',
          text: res.data?.message,
        }, 4000);
        this.$router.push({name: 'ProductConditionIndex'})
      }).catch((err) => {
        if (err?.response?.status === 422) {
          this.$notify(
              {
                group: "bottom",
                title: "Error",
                text: "Validation Error",
              },
              4000
          );

          this.errors = err.response.data.errors;
        } else {
          this.$notify(
              {
                group: "bottom",
                title: "Error",
                text: err?.response?.data?.message,
              },
              4000
          );
        }
      })
          .finally(() => (this.isLoading = false));
    },
    listProducts() {
      axios.get('/product-config/product-condition-rating/list-products').then(response => {
        this.productOptions = response.data
      })
    },
    conditionTypeChange() {
      if (this.formValues.has_cond_type) {
        this.formValues.code = 'INSURANCE_PERIOD_RATE'
        this.formValues.cond_type = 'Y'
      } else {
        this.formValues.code = 'REFUND_DEFAULT_PERCENTAGE'
        this.formValues.cond_type = 'N'
        this.formValues.cond_expr = null
      }
    },
    getConditionRating() {
      if (this.id) {
        ProductConditionService.getData(this.id).then(response => {
          Object.assign(this.formValues, response.data)
          if (this.formValues.cond_type == 'Y') {
            this.formValues.has_cond_type = true
          }
        })
      }
    }
  },
  mounted() {
    this.listProducts()
    this.getConditionRating()
  }
}
</script>