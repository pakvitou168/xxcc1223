<template>
  <div class="grid gap-y-5">
    <template v-if="modelValue">
      <CauseOfLossItem
        v-for="(causeOfLoss, index) in modelValue"
        :key="index"
        :amountErrors="errors[`cause_of_losses.${index}.amount`]"
        :payees="payees"
        :payeeErrors="errors[`cause_of_losses.${index}.payee_id`]"
        :paymentTypes="paymentTypes" :paymentTypeErrors="errors[`cause_of_losses.${index}.payment_type`]"
        v-model="modelValue[index]"
        @deleteRow="deleteRow(index)"
        @openDialog="openDialog(index)"
      />
    </template>
  </div>
</template>

<script>

import CauseOfLossItem from '@/views/Claim/PartialPayment/Partials/CauseOfLossItem.vue'

export default {
  components: {
    CauseOfLossItem,
  },

  props: {
    value: Array,
    payees: Array,
    paymentTypes:Object,
    errors: Object,
    modelValue:Array
  },

  data() {
    return {
      updatedValue: this.value,
    }
  },

  methods: {
    deleteRow(rowId) {
      this.modelValue.splice(rowId, 1)
    },
    openDialog(index) {
      this.$emit('openDialog',index);
    },
  },

  updated() {
    if (this.value) {
      this.updatedValue = this.value
    }
  },
}
</script>