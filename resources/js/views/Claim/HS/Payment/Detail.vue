<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
      <h2 class="text-lg font-medium mr-auto">
        Payment
      </h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button v-if="canApprove" class="btn btn-primary shadow-md mr-2" @click="openDialog">
          <span class="h-6 leading-6">Approve</span>
        </button>
        <div class="dropdown">
          <button class="dropdown-toggle btn btn-primary shadow-md mr-2" title="Print">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
              </path>
            </svg>
          </button>
          <div class="dropdown-menu w-60">
            <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
              <a v-if="isApproved" class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printDischargeWithLetterHeadUrl" target="_blank">Discharge Letterhead</a>
              <a v-if="isApproved" class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printDischargeWithoutLetterHeadUrl" target="_blank">Discharge No Letterhead</a>
              <a v-if="isApproved" class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printChequeWithLetterHeadUrl" target="_blank">Print Cheque (Letterhead)</a>
              <a v-if="isApproved" class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printChequeWithoutLetterHeadUrl" target="_blank">Print Cheque (No Letterhead)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printPaymentWithLetterHeadUrl" target="_blank">Claim Payment (Letterhead)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printPaymentWithoutLetterHeadUrl" target="_blank">Claim Payment (No Letterhead)</a>
            </div>
          </div>
        </div>
        <router-link v-if="canUpdate" :to="{ name: 'ClaimHSPaymentEdit', params: { id } }">
          <button class="btn btn-primary mx-1 intro-x">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
              </path>
            </svg>
          </button>
        </router-link>
      </div>
    </div>

    <div v-if="isLoading" class="intro-y box overflow-hidden mt-5 pb-10 flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-theme-1"></div>
    </div>

    <div v-else-if="loadError" class="intro-y box overflow-hidden mt-5 pb-10">
      <div class="p-5 text-center text-theme-6">
        <div class="text-xl mb-2">Failed to load payment details</div>
        <p>{{ loadError }}</p>
        <button @click="getDetail" class="btn btn-primary mt-4">Retry</button>
      </div>
    </div>

    <div v-else class="intro-y box overflow-hidden mt-5 pb-10">
      <div class="text-center">
        <div class="py-10">
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">Payment</div>
        </div>
      </div>

      <div v-if="!formValues.claim_payment" class="w-full px-5 sm:px-16 text-center py-8">
        <div class="text-theme-6 text-lg">No payment data available</div>
      </div>

      <div v-else class="w-full px-5 sm:px-16">
        <div class="grid grid-cols-6 gap-x-5 gap-y-2">
          <div class="xl:col-span-2 col-span-6 grid xl:grid-cols-2 grid-cols-6 gap-5">
            <div class="text-base font-bold mb-1.5">Policy Holder</div>
            <div class="text-base white-space-nowrap xl:col-span-1 col-span-5">:
              {{ formValues.claim_payment.policy_holder || '' }}</div>
          </div>
          <div class="xl:col-span-2 col-span-6 grid xl:grid-cols-2 grid-cols-6 gap-5">
            <div class="text-base font-bold mb-1.5 xl:col-span-1">Approve Status</div>
            <div class="text-base xl:col-span-1 col-span-5">
              <span v-if="formValues.claim_payment.approved_status === 'APV'"
                    class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Approved</span>
              <span v-else-if="formValues.claim_payment.approved_status === 'REJ'"
                    class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Rejected</span>
              <span v-else class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Pending</span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Policy No.</div>
          <div class="text-base white-space-nowrap col-span-5">: {{ formValues.claim_payment.policy_no || '' }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Claims No.</div>
          <div class="text-base white-space-nowrap col-span-5">: {{ formValues.claim_payment.claim_no || '' }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Date of Disability</div>
          <div class="text-base white-space-nowrap col-span-5">:
            {{ formatDate(formValues.claim_payment.date_of_disability) }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Reporting Date</div>
          <div class="text-base white-space-nowrap col-span-5">:
            {{ formatDate(formValues.claim_payment.reporting_date) }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Date of Completed Documents</div>
          <div class="text-base white-space-nowrap col-span-5">:
            {{ formatDate(formValues.claim_payment.date_of_completed_doc) }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Claimant Name</div>
          <div class="text-base white-space-nowrap col-span-5">:
            {{ formValues.claim_payment.claimant_name || '' }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Disability</div>
          <div class="text-base white-space-nowrap col-span-5">:
            {{ formValues.claim_payment.cause_of_loss_disability || '' }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Payment Type</div>
          <div class="text-base white-space-nowrap col-span-5">:
            {{ formValues.claim_payment.payment_type_name || '' }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Amount</div>
          <div class="text-base white-space-nowrap col-span-5">: $
            {{ formValues.claim_payment.amount ? formatCurrency(formValues.claim_payment.amount) : '' }}</div>
        </div>

        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Payee Name</div>
          <div class="text-base white-space-nowrap xl:col-span-1 col-span-5">:
            {{ formValues.claim_payment.payee_name_en || '' }}</div>
        </div>

        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">For Treatment of</div>
          <div class="text-base white-space-nowrap col-span-5">:
            {{ formValues.claim_payment.cause_of_loss || '' }}</div>
        </div>

        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Request Date</div>
          <div class="text-base white-space-nowrap col-span-5">:
            {{ formatDate(formValues.claim_payment.requested_date) }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Request By</div>
          <div class="text-base">: {{ formValues.claim_payment.prepared_by_name || '' }}</div>
          <div class="col-span-4">
            <div class="grid grid-cols-4">
              <div class="text-base font-bold mb-1.5">Approved By</div>
              <div class="text-base">: {{ formValues.claim_payment.approved_by_name || '' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <ApproveDialog
      :is-visible="showDialog"
      header="Accept on Payment"
      :submitted="submitted"
      :is-loading="isLoading"
      :options="{}"
      value="APV"
      @hide-dialog="hideDialog"
      @confirm="approve"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ApproveDialog from './Components/ApproveDialog.vue';
import ClaimHSPaymentService from "@/services/claim/hs/claim_payment.service";
import { hasPermission } from '@/services/auth.service';
import moment from 'moment';

// Router setup
const route = useRoute();
const router = useRouter();
const ERROR_MSG = ref("Something went wrong!");
const SUCCESS_MSG = ref("Success!");
const id = ref(route.params.id || null);

// State management
const formValues = ref({});
const showDialog = ref(false);
const submitted = ref(false);
const isLoading = ref(true);
const loadError = ref(null);
const errors = ref({});

// Tabulator options for data table
const options = ref({
  layout: "fitColumns",
  pagination: false,
  placeholder: "No Data Available",
  headerSortTristate: true,
  columns: [
    {
      title: 'Payment No.',
      field: 'payment_no',
      width: 120,
      headerSort: false,
    },
    {
      title: 'Payee Name',
      field: 'payee_name_en',
      minWidth: 170,
      headerSort: false,
    },
    {
      title: 'Cause of Loss Disability',
      field: 'cause_of_loss_disability',
      minWidth: 170,
      headerSort: false,
    },
    {
      title: 'Reserve Amount',
      field: 'claim_amount',
      width: 130,
      headerSort: false,
      headerHozAlign: "right",
      hozAlign: 'right',
      mutator: (_, row) => formatCurrency(row.claim_amount),
    },
    {
      title: 'Payable Amount',
      field: 'claim_payable',
      width: 140,
      headerHozAlign: "right",
      hozAlign: 'right',
      headerSort: false,
      mutator: (_, row) => formatCurrency(row.claim_payable),
    },
    {
      title: 'Remaining Amount',
      field: 'remain_amount',
      width: 160,
      headerSort: false,
      headerHozAlign: "right",
      hozAlign: 'right',
      mutator: (_, row) => formatCurrency(row.remain_amount),
    },
    {
      title: 'Payment Type',
      field: 'payment_type',
      width: 140,
      headerSort: false,
    },
  ],
});

// Computed properties
const canApprove = computed(() => {
  if (!hasPermission('HS_CLAIM_PAYMENT', 'APPROVE')) return false;
  if (!formValues.value.claim_payment) return false;
  return formValues.value.claim_payment.approved_status === null;
});

const isApproved = computed(() => {
  if (!formValues.value.claim_payment) return false;
  return formValues.value.claim_payment.approved_status === 'APV';
});

const canUpdate = computed(() => {
  if (!hasPermission('HS_CLAIM_PAYMENT', 'UPDATE')) return false;
  if (!formValues.value.claim_payment) return false;
  return formValues.value.claim_payment.approved_status === null;
});

// URL computed properties
const printChequeWithLetterHeadUrl = computed(() => {
  return id.value ? ClaimHSPaymentService.printChequeUrl(id.value, 'en', true) : '#';
});

const printChequeWithoutLetterHeadUrl = computed(() => {
  return id.value ? ClaimHSPaymentService.printChequeUrl(id.value, 'en', false) : '#';
});

const printPaymentWithLetterHeadUrl = computed(() => {
  return id.value ? ClaimHSPaymentService.printPaymentUrl(id.value, 'en', true) : '#';
});

const printPaymentWithoutLetterHeadUrl = computed(() => {
  return id.value ? ClaimHSPaymentService.printPaymentUrl(id.value, 'en', false) : '#';
});

const printDischargeWithLetterHeadUrl = computed(() => {
  return id.value ? ClaimHSPaymentService.printDischargeUrl(id.value, 'en', true) : '#';
});

const printDischargeWithLetterHeadUrlKM = computed(() => {
  return id.value ? ClaimHSPaymentService.printDischargeUrl(id.value, 'km', true) : '#';
});

const printDischargeWithoutLetterHeadUrl = computed(() => {
  return id.value ? ClaimHSPaymentService.printDischargeUrl(id.value, 'en', false) : '#';
});

const printDischargeWithoutLetterHeadUrlKM = computed(() => {
  return id.value ? ClaimHSPaymentService.printDischargeUrl(id.value, 'km', false) : '#';
});

// Methods
function openDialog() {
  showDialog.value = true;
  submitted.value = false;
}

function hideDialog() {
  showDialog.value = false;
  submitted.value = false;
}

function approve(form) {
  if (!form) {
    notify('Invalid form data', 'error', 'bottom-right');
    return;
  }

  submitted.value = true;
  form.status = 'APV';

  if (!form.status || !form.comment) {
    notify('Status and comment are required', 'error', 'bottom-right');
    return;
  }

  if (!id.value) {
    notify('Invalid payment ID', 'error', 'bottom-right');
    return;
  }

  isLoading.value = true;
  ClaimHSPaymentService.approve(form, id.value)
    .then(res => {
      isLoading.value = false;
      submitted.value = false;
      if (res.data?.success) {
        notify('Payment approved successfully', 'success', 'bottom-right');
        router.push({ name: 'ClaimHSPaymentIndex' });
      } else {
        notify(res.data?.message || 'Approval was not successful', 'warning', 'bottom-right');
      }
    })
    .catch(err => {
      isLoading.value = false;
      submitted.value = false;
      notify(err.response?.data?.message ?? 'Error approving payment', 'error', 'bottom-right');
    });
}

function getDetail() {
  if (!id.value) {
    loadError.value = 'Invalid payment ID';
    isLoading.value = false;
    return;
  }

  isLoading.value = true;
  loadError.value = null;

  ClaimHSPaymentService.detail(id.value)
    .then(res => {
      formValues.value = res.data || {};
      isLoading.value = false;

      if (!formValues.value.claim_payment) {
        loadError.value = 'No payment data found';
      }
    })
    .catch(err => {
      isLoading.value = false;
      loadError.value = err.response?.data?.message ?? 'Failed to load payment details';
      notify(loadError.value, 'error', 'bottom-right');
    });
}

function formatCurrency(number) {
  if (number === null || number === undefined || number === '') return '';

  try {
    if (typeof number === 'string' && !number.includes(",")) {
      number = parseFloat(number);
    }

    return number.toLocaleString('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  } catch (e) {
    console.error('Error formatting currency:', e);
    return 'Error';
  }
}

function formatDate(date) {
  if (!date) return '';

  try {
    const momentDate = moment(date);
    if (!momentDate.isValid()) return '';
    return momentDate.format('DD/MM/YYYY');
  } catch (e) {
    console.error('Error formatting date:', e);
    return 'Error';
  }
}

// Watch for route changes
watch(() => route.params.id, (newId) => {
  if (newId !== id.value) {
    id.value = newId || null;
    getDetail();
  }
});

// Lifecycle hooks
onMounted(() => {
  getDetail();
});
</script>