<template>
  <div class="flex">
    <div class="w-72 uppercase font-semibold">SCHEDULE OF BENEFITS</div>
    <div class="flex flex-1">
      <span class="mr-1"></span>
    </div>
  </div>
  <div class="w-full mt-4">
    <table class="benefits-table border w-full">
      <thead>
      <tr class="capitalize bg-slate-100">
        <th>Items</th>
        <th>Benefits and Limits</th>
        <th v-if="show_standard_limit" class="text-right">Standard Plan</th>
        <th v-if="show_deluxe_limit" class="text-right">Deluxe Plan</th>
        <th v-if="show_executive_limit" class="text-right">Executive Plan</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="coverage in coverages.length >0">
        <template v-if="!coverage.seq_no">
          <td colspan="2" class="font-bold">{{ coverage.name }}</td>
        </template>
        <template v-else>
          <td>{{ coverage.seq_no }}</td>
          <td>{{ coverage.name }}</td>
        </template>
        <td v-if="show_standard_limit" class="text-right font-bold">{{  coverage.standard_limit }}</td>
        <td v-if="show_deluxe_limit" class="text-right font-bold">{{ coverage.deluxe_limit }}</td>
        <td v-if="show_executive_limit" class="text-right font-bold">{{ coverage.executive_limit }}</td>
      </tr>
      <template v-if="additional_coverages.length > 0">
        <tr>
          <td colspan="5" class="font-bold">Additional Benefits</td>
        </tr>
        <tr v-for="coverage in additional_coverages">
          <td>{{ coverage.seq_no }}</td>
          <td>{{ coverage.name }}</td>
          <td v-if="show_standard_limit" class="text-right font-bold">{{ coverage.standard_limit }}</td>
          <td v-if="show_deluxe_limit" class="text-right font-bold">{{ coverage.deluxe_limit }}</td>
          <td v-if="show_executive_limit" class="text-right font-bold">{{ coverage.executive_limit }}</td>
        </tr>
      </template>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import {computed, defineProps} from 'vue';

const props = defineProps({
  coverages: {
    type: [Object],
  },
  additional_coverages: {
    type: [Object],
  },
  show_standard_limit: {
    type: [Boolean],
  },
  show_deluxe_limit: {
    type: [Boolean],
  },
  show_executive_limit: {
    type: [Boolean],
  },
});


</script>