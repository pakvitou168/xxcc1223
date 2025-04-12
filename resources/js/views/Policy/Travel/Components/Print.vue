<template>
  <div class="dropdown">
    <button
      class="dropdown-toggle btn btn-primary shadow-md mr-2"
      title="Print"
    >
      <svg
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
        ></path>
      </svg>
    </button>
    <div class="dropdown-menu w-72">
      <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
        <a
          v-if="showInvoice"
          class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
          :href="invoiceWithSignatureUrl"
          target="_blank"
        >Invoice (Signature)</a>

        <template v-for="(option, index) in printOptions" :key="index">
          <a
            class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
            :href="option.url"
            target="_blank"
          >{{ option.label }}</a>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import {computed, defineProps} from 'vue';

const props = defineProps({
  policyId: {
    type: [Number, String],
    required: true
  },
  status: {
    type: [String],
    required: true
  },
});
const printUrl = `/travel/policies/policy-services/${props.policyId}`;
const printOptions = computed(() => {
  if (!props.policyId) return [];

  return [
    {
      label: 'Invoice (No Signature)',
      url: `${printUrl}/download-invoice`
    },
    {
      label: 'Policy Schedule (Letterhead EN)',
      url: `${printUrl}/download-policy-schedule/en?letterhead=1`
    },
    {
      label: 'Policy Schedule (EN)',
      url: `${printUrl}/download-policy-schedule/en?letterhead=0`
    },
    {
      label: 'Policy Signature with No letterhead (EN)',
      url: `${printUrl}/download-policy-schedule/en?letterhead=0&noStamp=1`
    },
    {
      label: 'Policy Schedule (Letterhead KH)',
      url: `${printUrl}/download-policy-schedule/km?letterhead=1`
    },
    {
      label: 'Policy Schedule (KH)',
      url: `${printUrl}/download-policy-schedule/km?letterhead=0`
    },
    {
      label: 'Policy Signature with No letterhead (KH)',
      url: `${printUrl}/download-policy-schedule/km?letterhead=0&noStamp=1`
    }
  ];
});


const showInvoice = computed(() => props.status === 'APV');
const invoiceWithSignatureUrl = computed(() =>
  `${printUrl}/download-invoice/with-signature`
);
</script>
