<template>
  <div class="grid gap-y-5">
    <template v-if="modelValue">
      <CauseOfLossItem v-for="(causeOfLoss, index) in modelValue" :key="index" :payees="payees" :deductible="deductibles[index]"
        :payeeErrors="errors[`cause_of_losses.${index}.payee_id`]" :paymentTypes="paymentTypes" :paymentTypeErrors="errors[`cause_of_losses.${index}.payment_type`]"
        :remainAmountErrors="errors[`cause_of_losses.${index}.remain_amount`]"
        :recoveryErrors="errors[`cause_of_losses.${index}.recovery_from_third_party`]"
        v-model="modelValue[index]" @openDialog="openDialog(index)" @deleteRow="deleteRow(index)" />
    </template>
  </div>
</template>

<script>

import CauseOfLossItem from '@/views/Claim/Process/Partials/CauseOfLossItem.vue'

export default {
  components: {
    CauseOfLossItem,
  },

  props: {
    value: Array,
    payees: Array,
    paymentTypes: Object,
    deductibles: Array,
    errors: Object,
    modelValue:Array
  },

  data() {
    return {
      updatedValue: [],
    }
  },
  methods: {
    openDialog(index) {
      this.$emit('openDialog', index);
    },
    deleteRow(rowId) {
      this.modelValue.splice(rowId, 1)
    },
  },
}
</script>