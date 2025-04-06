<template>
  <div class="grid gap-y-5">
    <template v-if="modelValue">
      <CauseOfLossItem
        v-for="(causeOfLoss, index) in modelValue"
        :key="index"
        :deductiblePaidErrors="errors[`cause_of_losses.${index}.deductible_paid`]"
        :insuredSharingRequestErrors="errors[`cause_of_losses.${index}.insured_sharing_request`]"
        :paymentTypeErrors="errors[`cause_of_losses.${index}.payment_type`]"
        v-model="modelValue[index]"
        :paymentTypes="paymentTypes"
      />
    </template>
  </div>
</template>

<script>

import CauseOfLossItem from '@/views/Claim/Recovery/Partials/CauseOfLossItem.vue'

export default {
  components: {
    CauseOfLossItem,
  },

  props: {
    value: Array,
    payees: Array,
    errors: Object,
    paymentTypes: Object,
    modelValue:Array
  },

  data() {
    return {
      updatedValue: [],
    }
  },

  updated() {
    if (this.value) {
      this.updatedValue = this.value
    }
  },
}
</script>