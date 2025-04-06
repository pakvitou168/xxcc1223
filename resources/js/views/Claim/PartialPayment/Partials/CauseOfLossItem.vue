<template>
  <div v-if="value" class="pb-5 border-b">
    <div class="flex gap-x-3 items-center mb-2">
      <h3 class="text-lg font-bold leading-6 text-gray-900 bg-gray-300 p-1 rounded">{{ causeOfLossLabel }}</h3>
      <p class="text-lg">Partial Remaining Amount: <span class="font-extrabold">{{
        getCurrencyFormattedValue(modelValue.remain_amount)
      }}</span></p>
    </div>
    <div class="grid lg:grid-cols-4 gap-x-5">
      <label class="block mb-2.5 font-bold">Payee *</label>
      <label class="block mb-2.5 font-bold">Amount *</label>
      <label class="block mb-2.5 font-bold">Payment Type *</label>
      <label class="block mb-2.5 font-bold">Remark</label>
    </div>
    <div class="grid lg:grid-cols-4 gap-x-5">
      <div>
        <InputGroup>
          <Dropdown v-model="modelValue.payee_id" class="w-full p-inputtext-sm" placeholder="Select payee" optionLabel="label"
            optionValue="value" :filter="true" :showClear="true" :options="payees" />
          <Button icon="pi pi-plus" severity="info" @click="openDialog()" />
        </InputGroup>
        <span class="text-error" v-if="payeeErrors">{{ payeeErrors[0] }}</span>
      </div>
      <div>
        <InputNumber class="w-full"  placeholder="Amount" v-model="modelValue.amount" :minFractionDigits="0" :maxFractionDigits="2" />
      </div>
      <div>
        <Dropdown :options="paymentTypes" class="w-full" placeholder="Select payment type" optionValue="value" optionLabel="label" v-model="modelValue.payment_type" />
      </div>
      <div class="flex">
        <div class="w-full">
          <InputText v-model="modelValue.remark" placeholder="Remark" />
        </div>
        <button tabindex="-1" title="Remove" @click="deleteRow" class="mb-2.5 ml-5 focus:outline-none"
          style="height: 42px;width:25px">
          <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
            </path>
          </svg>
        </button>
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
    amountErrors: Array,
    payees: Array,
    payeeErrors: Array,
    paymentTypes: Object,
    paymentTypeErrors: Array,
    modelValue: {
      type: Object,
      default: {}
    }
  },

  data() {
    return {
      updatedValue: this.value
    }
  },

  computed: {
    causeOfLossLabel() {
      return this.modelValue.cause_of_loss_desc + ' (' + this.modelValue.cause_of_loss_code + ')'
    }
  },

  methods: {
    deleteRow() {
      this.$emit('deleteRow')
    },
    getCurrencyFormattedValue(number) {
      if (number == null) return ''
      if (typeof number === 'string') {
        number = parseFloat(number)
      }
      return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
    },
    openDialog() {
      this.$emit('openDialog');
    },
  },

  updated() {
    if (this.value) {
      this.updatedValue = this.value
    }
  }
}
</script>