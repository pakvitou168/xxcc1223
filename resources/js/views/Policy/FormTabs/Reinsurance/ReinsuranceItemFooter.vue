<template>
    <div class="grid grid-cols-11 gap-x-2 p-4 reinsurance-footer">
        <div class="col-span-3 font-bold">Total</div>
        <div class="font-bold">{{ total.share }}</div>
        <div class="font-bold">{{ formatCurrency(total.sum_insured) }}</div>
        <div class="font-bold">{{ formatCurrency(total.premium) }}</div>
        <div class="font-bold"></div>
        <div class="font-bold">{{ formatCurrency(total.ri_commission_amt) }}</div>
        <div class="font-bold"></div>
        <div class="font-bold">{{ formatCurrency(total.tax_fee_amt) }}</div>
        <div class="font-bold">{{ formatCurrency(total.net_premium) }}</div>
    </div>
</template>

<script>
import { formatCurrency } from '@/helpers';
export default {
    props: {
        total: Object,
    },
    methods:{
        formatCurrency
    }
}
</script>
<style scoped>
.reinsurance-footer{
    @apply border-2 border-slate-100 rounded-b-lg
}
.reinsurance-footer.odd{
    @apply bg-slate-100
}
</style>
