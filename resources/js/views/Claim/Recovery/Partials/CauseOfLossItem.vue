<template>
  <div v-if="modelValue" class="pb-5 border-b">
    <div class="flex gap-x-3 items-center mb-2">
      <h3 class="text-lg font-bold leading-6 text-gray-900 bg-gray-300 p-1 rounded">{{ causeOfLossLabel }}</h3>
    </div>
    <div class="grid lg:grid-cols-4 gap-5">
      <div>
        <label for="" class="form-label">Third Party Recovery *</label>
        <InputNumber class="w-full" v-model="modelValue.third_party_recovery" disabled />
      </div>
      <div>
        <label for="" class="form-label">Insured Sharing *</label>
        <InputNumber class="w-full" v-model="modelValue.insured_sharing_request" disabled />
      </div>
      <div>
        <label for="" class="form-label">Payment Type *</label>
        <Dropdown class="w-full" :options="paymentTypes" optionLabel="label" optionValue="value"
          v-model="modelValue.payment_type" />
      </div>
      <div>
        <label for="" class="form-label">Salvage</label>
        <InputNumber placeholder="Salvage" v-model="modelValue.salvage" class="w-full" />
      </div>
      <div>
        <label for="" class="form-label">Remark</label>
        <Textarea rows="4" placeholder="Remark" v-model="modelValue.remark" class="w-full" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: Object,
      default: {},
    },
    paymentTypes: Object,
    deductiblePaidErrors: Array,
    insuredSharingRequestErrors: Array,
    paymentTypeErrors: Array,
    modelValue: {
      type: Object,
      default: {},
    },
  },

  data() {
    return {
      updatedValue: {}
    }
  },

  computed: {
    causeOfLossLabel() {
      return this.modelValue.cause_of_loss_desc + ' (' + this.modelValue.cause_of_loss_code + ')'
    }
  },

  updated() {
    if (this.value) {
      this.updatedValue = this.value
    }
  }
}
</script>