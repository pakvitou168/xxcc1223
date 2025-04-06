<template>
  <div>
    <div class="grid lg:grid-cols-4 gap-x-5">
      <label class="form-label">Type of Loss *</label>
      <label class="form-label">Reserve Amount *</label>
    </div>

    <template v-if="modelValue">
      <CauseOfLossItem v-for="(causeOfLoss, index) in modelValue" :key="index" :causeOfLosses="causeOfLosses"
        :codeErrors="errors[`cause_of_losses.${index}.code`]" :valueErrors="errors[`cause_of_losses.${index}.value`]"
        :recoveryErrors="errors[`cause_of_losses.${index}.recovery_from_third_party`]" v-model="modelValue[index]"
        @deleteRow="deleteRow(index)" />
    </template>

    <div class="grid lg:grid-cols-2 gap-x-5">
      <div class="w-full text-right">
        <button class="btn btn-primary w-24 mt-4" @click="addNonCoverCause">
          Add
        </button>
      </div>
    </div>
  </div>
</template>

<script>

import CauseOfLossItem from '@/views/Claim/Register/Partials/CauseOfLossItem.vue'

export default {
  components: {
    CauseOfLossItem,
  },

  props: {
    value: Array,
    causeOfLosses: Array,
    errors: Object,
    modelValue: Array
  },

  data() {
    return {
      updatedValue: this.value,
    }
  },

  methods: {
    addNonCoverCause() {


      this.$emit('addMore')
    },
    deleteRow(rowId) {
      this.modelValue.splice(rowId, 1)
    },
  },

  updated() {
    if (this.value) {
      this.updatedValue = this.value
    }
  },
}
</script>