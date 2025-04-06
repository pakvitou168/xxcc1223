<template>
  <div class="w-full mt-4" v-if="items.length > 0">
    <table class="benefits-table border w-full">
      <thead>
      <tr class="capitalize bg-slate-100">
        <th>Risk no.</th>
        <th>Full Name</th>
        <th>Date of Birth</th>
        <th>Passport No.</th>
        <th>Plan</th>
        <th>Premium/Person</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, index) in items">
        <td>{{ index + 1 }}</td>
        <td>{{ item.full_name }}</td>
        <td class="text-right">{{ item.date_of_birth }}</td>
        <td class="text-right">{{ item.passport_no }}</td>
        <td class="text-right">{{ item.plan }}</td>
        <td class="text-right font-semibold">{{ item.premium }}</td>
      </tr>
      </tbody>
      <tfoot>
      <tr>
        <td class="capitalize text-right font-semibold" colspan="5">grand total</td>
        <td class="text-right font-semibold">{{ Number(totalPremium).toFixed(2) }}</td>
      </tr>
      </tfoot>
    </table>
  </div>
</template>

<script setup>
import {computed, defineProps} from 'vue';

const props = defineProps({
  items: {
    type: [Object],
    required: true,
  },
  totalPremium: {
    type: [Number, String],
    default: 0
  }
});
</script>