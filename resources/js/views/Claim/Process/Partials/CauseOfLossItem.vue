<template>
  <div v-if="value" class="pb-5 border-b">
    <div class="flex gap-x-3 items-center mb-2">
      <h3 class="text-lg font-bold leading-6 text-gray-900 bg-gray-300 p-1 rounded">{{ causeOfLossLabel }}</h3>
      <p class="text-lg">Partial amount: <span class="font-extrabold">{{
        getCurrencyFormattedValue(modelValue.partial_amount)
          }}</span></p>
    </div>
    <div class="grid lg:grid-cols-4 gap-x-5">
      <div>
        <label class="form-label">Payee *</label>
        <InputGroup>
          <Dropdown v-model="modelValue.payee_id" class="w-full p-inputtext-sm" optionLabel="label" optionValue="value"
            placeholder="Select payee" showClear :filter="true" :showClear="true" :options="payees" />
          <Button icon="pi pi-plus" severity="info" @click="openDialog()" />
        </InputGroup>
        <span v-if="payeeErrors" class="text-error">{{ payeeErrors[0] }}</span>
      </div>
      <div>
        <label for="" class="form-label">Remaining Amount *</label>
        <InputNumber v-model="modelValue.remain_amount" class="w-full" :minFractionDigits="0" :maxFractionDigits="2"
          placeholder="Remaining amount" />
        <span class="text-error" v-if="remainAmountErrors">{{ remainAmountErrors[0] }}</span>
      </div>
      <div>
        <label for="" class="form-label">Payment Type *</label>
        <Dropdown v-model="modelValue.payment_type" class="w-full" placeholder="Select payment type"
          :options="paymentTypes" optionLabel="label" optionValue="value" />
        <span v-if="paymentTypeErrors" class="text-error">{{ paymentTypeErrors[0] }}</span>
      </div>
      <div>
        <label class="form-label">Deductible</label>
        <p class="p-3 mb-2.5 col-span-2 w-full rounded border" style="background-color: #f2f2f2; height: 42px;">
          {{ deductible ? deductible.deductible + '$' : '' }}
        </p>
      </div>
      <div>
        <label for="" class="form-label">Deductible Paid</label>
        <InputNumber v-model="modelValue.deductible_paid" class="w-full" placeholder="Deductible paid" />
      </div>
      <div>
        <label for="" class="form-label">Insured Sharing Request</label>
        <InputNumber v-model="modelValue.insured_sharing_request" class="w-full" placeholder="Insured sharing request" />
      </div>
      <div>
        <label for="" class="form-label">Remark</label>
        <InputText v-model="modelValue.remark" type="text" class="w-full" placeholder="Remark" />
      </div>


      <div v-if="isOwnDamageCauseOfLoss">
        <template v-if="hasRecoveryFromThirdPartyFromRegister">
          <label class="block mb-2.5 font-bold">Recovery from Third Party (Register)</label>
          <p class="p-3 mb-2.5 col-span-2 w-full" style="background-color: #f2f2f2; height: 42px;">
            {{ modelValue.recovery_from_third_party_from_register }}
          </p>
        </template>
        <template v-else>
          <label for="" class="form-label">Recovery from Third Party</label>
          <InputNumber v-model="modelValue.recovery_from_third_party" placeholder="Recovery from third party"
            class="w-full" />
          <span class="text-error" v-if="recoveryErrors">{{ recoveryErrors[0] }}</span>
        </template>
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
    payees: Array,
    deductible: Object,
    payeeErrors: Array,
    paymentTypes: Object,
    remainAmountErrors: Array,
    paymentTypeErrors: Array,
    recoveryErrors: Array,
    modelValue: Object
  },

  data() {
    return {
      updatedValue: {}
    }
  },

  computed: {
    causeOfLossLabel() {
      return this.modelValue.cause_of_loss_desc + ' (' + this.modelValue.cause_of_loss_code + ')'
    },
    isOwnDamageCauseOfLoss() {
      const OWN_DAMAGE = 'OD'

      return this.modelValue.cause_of_loss_code === OWN_DAMAGE
    },
    hasRecoveryFromThirdPartyFromRegister() {
      return this.modelValue?.recovery_from_third_party_from_register > 0
    },
  },
  methods: {
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
    deleteRow() {
      this.$emit('deleteRow')
    },
    generateDeductibles() {

    }
  }
}
</script>