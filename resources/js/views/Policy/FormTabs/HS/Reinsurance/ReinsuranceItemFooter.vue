<template>
  <div class="grid grid-cols-10 gap-x-2 p-4 border-l border-r border-b">
    <div class="col-span-3 font-bold">Total</div>
    <div class="font-bold">{{ total.share }}</div>
    <div class="font-bold">{{ total.premium }}</div>
    <div class="font-bold"></div>
    <div class="font-bold">{{ total.ri_commission_amt }}</div>
    <div class="font-bold"></div>
    <div class="font-bold">{{ total.tax_fee_amt }}</div>
    <div class="font-bold">{{ total.net_premium }}</div>
  </div>
</template>

<script>
export default {
  props: {
    total: Object,
  },
}
</script>
