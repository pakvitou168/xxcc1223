<template>
  <div class="w-full">
    <div class="grid grid-cols-4 gap-x-4 mb-4">
      <InputText v-model="info.label" disabled />
      <InputText
        :value="info.ccy + ' ' + formatCurrency(info.value)"
        disabled
      />
      <div class="flex">
        <label for="" class="pt-2 w-12">Value</label>
        <InputNumber
          class="flex-1"
          v-model="modelValue.cond_value"
          :maxFractionDigits="2"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { formatCurrency } from '@/helpers.js'
const props = defineProps({
  index: Number,
  modelValue: {
    type: Array,
    default: [],
  },
  info:null
});
</script>

<style scoped>
.input-label {
  display: block;
  line-height: 1.5;
  font-weight: bold;
}

.collapse {
  display: none;
}

.rotate {
  transform: rotate(180deg);
}

.icon-color {
  color: red;
}
</style>
