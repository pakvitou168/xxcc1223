<template>
  <div v-if="value" class="grid lg:grid-cols-8 gap-x-5">
    <template v-if="modelValue.type === 'NON_COVER'">
      <div class="col-span-2">
        <Dropdown v-model="modelValue.code" class="w-full p-inputtext-sm" optionLabel="cause_name" optionValue="code"
          :filter="true" :showClear="true" :options="causeOfLosses" @change="assignCauseName" />
        <span class="text-error" v-if="codeErrors">{{ codeErrors[0] }}</span>
      </div>
    </template>
    <template v-else>
      <p class="p-3 mb-2.5 col-span-2" style="background-color: #f2f2f2; height: 42px;">{{ causeOfLossLabel }}</p>
    </template>
    <div class="col-span-2">
      <InputNumber v-model="modelValue.value" class="w-full" :minFractionDigits="0" :maxFractionDigits="2" />
      <span class="text-error" v-if="valueErrors">{{ valueErrors[0] }}</span>
    </div>

    <div v-if="isOwnDamageCauseOfLoss" class="col-span-3">
      <div class="w-full">
        <div class="flex">
          <span class="font-bold mt-3 mr-5">Recovery from Third Party:</span>
          <div class="flex-1">
            <InputNumber class="w-full" v-model="modelValue.recovery_from_third_party" :minFractionDigits="0"
            :maxFractionDigits="2" />
            <span class="text-error" v-if="recoveryErrors">{{ recoveryErrors[0] }}</span>
          </div>
        </div>
      </div>
    </div>
    <div>
      <button tabindex="-1" title="Remove" @click="deleteRow" class="mb-2.5 focus:outline-none" style="height: 42px;">
        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
          </path>
        </svg>
      </button>
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
    causeOfLosses: Array,
    codeErrors: Array,
    valueErrors: Array,
    recoveryErrors: Array,
    modelValue: Object
  },

  data() {
    return {
      updatedValue: this.value
    }
  },

  computed: {
    causeOfLossLabel() {
      return this.modelValue.name + ' (' + this.modelValue.code + ')'
    },
    isOwnDamageCauseOfLoss() {
      const OWN_DAMAGE = 'OD'

      return this.modelValue.code === OWN_DAMAGE
    },
  },

  methods: {
    deleteRow() {
      this.$emit('deleteRow')
    },
    assignCauseName({ value }) {
      this.modelValue.name = this.causeOfLosses.filter(item => item.code === value)[0]?.cause_name
    }
  },

  updated() {
    if (this.value) {
      this.updatedValue = this.value
    }
  }
}
</script>