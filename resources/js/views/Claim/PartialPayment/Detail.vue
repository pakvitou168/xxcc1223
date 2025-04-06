<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
      <h2 class="text-lg font-medium mr-auto">
        Partial Payment
      </h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button v-if="canGenerate && !pageLoading" class="btn btn-primary shadow-md mr-2" :disabled="isLoading"
          @click="handleGeneratePaymentNo(id)">
          <span class="h-6 leading-6" v-if="!isLoading">Generate Payment No.</span>
          <span v-else class="h-6 leading-6">Generating...</span>
        </button>
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
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                v-if="isApproved" :href="printChequeWithLetterHeadUrl" target="_blank">Print Cheque (Letterhead EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                v-if="isApproved" :href="printChequeWithoutLetterHeadUrl" target="_blank">Print Cheque (No Letterhead
                EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printPaymentWithLetterHeadUrl" target="_blank">Claim Payment (Letterhead EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printPaymentWithoutLetterHeadUrl" target="_blank">Claim Payment (No Letterhead EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                v-if="isApproved" :href="printChequeWithLetterHeadUrlKh" target="_blank">Print Cheque (Letterhead
                KH)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                v-if="isApproved" :href="printChequeWithoutLetterHeadUrlKh" target="_blank">Print Cheque (No Letterhead
                KH)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printPaymentWithLetterHeadUrlKh" target="_blank">Claim Payment (Letterhead KH)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printPaymentWithoutLetterHeadUrlKh" target="_blank">Claim Payment (No Letterhead KH)</a>
            </div>
          </div>
        </div>
        <button v-if="canDelete && !pageLoading" class="btn btn-danger mx-1 intro-x" @click="handleDelete(id)">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
            </path>
          </svg>
        </button>
        <router-link v-if="canUpdate" :to="{ name: 'PartialPaymentEdit', params: { id: id } }">
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
    <div class="intro-y box overflow-hidden mt-5 pb-10">
      <div class="text-center">
        <div class="py-10">
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">Partial Payment</div>
        </div>
      </div>
      <div class="w-full px-5 sm:px-16" v-if="formValues.claim_payment">
        <div class="grid grid-cols-6 gap-x-5">
          <div class="xl:col-span-2 col-span-6 grid xl:grid-cols-2 grid-cols-6 gap-5">
            <div class="text-base font-bold mb-1.5">Claims No.</div>
            <div class="text-base white-space-nowrap xl:col-span-1 col-span-5">: {{ formValues.claim_payment.claim_no }}
            </div>
          </div>
          <div class="xl:col-span-2 col-span-6 grid xl:grid-cols-2 grid-cols-6 gap-5">
            <div class="text-base font-bold mb-1.5 xl:col-span-1">Approve Status</div>
            <div class="text-base xl:col-span-1 col-span-5">
              <span v-if="formValues.approved_status == 'APV'"
                class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Approved</span>
              <span v-else-if="formValues.approved_status == 'REJ'"
                class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Rejected</span>
              <span v-else class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Pending</span>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Insured Name</div>
          <div class="text-base white-space-nowrap col-span-5">: {{ formValues.claim_payment.insured_name }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Policy No.</div>
          <div class="text-base white-space-nowrap col-span-5">: {{ formValues.claim_payment.document_no }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Address</div>
          <div class="text-base white-space-nowrap col-span-5">: {{ formValues.claim_payment.address }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Insurance Cover Period</div>
          <div class="text-base white-space-nowrap col-span-5">: {{
            formatDate(formValues.claim_payment.insured_period_from) + "&nbsp;TO&nbsp;" +
            formatDate(formValues.claim_payment.insured_period_to)
          }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Request By</div>
          <div class="text-base">: {{ formValues.updated_by_name }}</div>
          <div class="col-span-4">
            <div class="grid grid-cols-4">
              <div class="text-base font-bold mb-1.5">Request Date</div>
              <div class="text-base">: {{ formatDate(formValues.updated_at) }}</div>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Approved By</div>
          <div class="text-base">: {{ formValues.approved_by_name }}</div>
          <div class="col-span-4">
            <div class="grid grid-cols-4">
              <div class="text-base font-bold mb-1.5">Approved Date</div>
              <div class="text-base">: {{ formatDate(formValues.approved_at) }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16">
        <div class="overflow-x-auto scrollbar-hidden" v-if="formValues.claim_payment_details">
          <DataTable ref="tabulator" :options="options" v-model="formValues.claim_payment_details" />
        </div>
      </div>
    </div>

    <ApproveDialog :isVisible="showDialog" header="Accept on Partial Payment" :submitted="submitted"
      :options="[{ value: 'APV', label: 'Approve' }, { value: 'REJ', label: 'Reject' }]" value="APV" :errors="errors"
      @hideDialog="hideDialog" @confirm="approve" />
  </div>
</template>

<script>

import ApproveDialog from '@/components/Dialogs/ApproveDialog.vue'
import PartialPaymentService from '@/services/claim/partial_payment.service'
import { hasPermission } from '@/services/auth.service'

export default {
  components: {
    ApproveDialog,
  },

  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {},
      showDialog: false,
      submitted: false,

      isLoading: false,
      alreadyHavePaymentNumbers: false,
      tabulator: null,
      errors: [],
      pageLoading:true
    }
  },

  computed: {
    options() {
      return {
        layout: "fitColumns",
        pagination: false,
        data: this.formValues.claim_payment_details,
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
            field: 'payee_name',
            minWidth: 170,
            headerSort: false,
          },
          {
            title: 'Type of Loss',
            field: 'cause_of_loss_desc',
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
            mutator: (_, row) => this.formatCurrency(
              row.claim_amount
            ),
          },
          {
            title: 'Payable Amount',
            field: 'claim_payable',
            width: 140,
            headerHozAlign: "right",
            hozAlign: 'right',
            headerSort: false,
            mutator: (_, row) => this.formatCurrency(
              row.claim_payable
            ),
          },
          {
            title: 'Remaining Amount',
            field: 'remain_amount',
            width: 160,
            headerSort: false,
            headerHozAlign: "right",
            hozAlign: 'right',
            mutator: (_, row) => this.formatCurrency(
              row.remain_amount
            ),
          },
          {
            title: 'Payment Type',
            field: 'payment_type',
            width: 140,
            headerSort: false,
          },
        ]
      }
    },
    canApprove() {
      let canApprovePermission = hasPermission('CLAIM_PARTIAL_PAYMENT', 'APPROVE')
      if (!canApprovePermission) return false

      if (this.formValues?.approved_status !== null) return false
      return true
    },
    isApproved() {
      if (this.formValues?.approved_status === 'APV') return true;
      return false;
    },
    canUpdate() {
      let canUpdatePermission = hasPermission('CLAIM_PARTIAL_PAYMENT', 'UPDATE')
      if (!canUpdatePermission) return false

      if (this.formValues?.approved_status !== null) return false
      return true
    },
    canDelete() {
      let canDeletePermission = hasPermission('CLAIM_PARTIAL_PAYMENT', 'DELETE')
      if (!canDeletePermission) return false

      if (this.formValues?.approved_status === 'APV') return false
      return true
    },
    canGenerate() {
      let canApprovePermission = hasPermission('CLAIM_PARTIAL_PAYMENT', 'APPROVE')
      if (!canApprovePermission) return false

      if (this.formValues?.approved_status !== 'APV') return false

      if (this.alreadyHavePaymentNumbers) return false
      return true
    },
    printChequeWithLetterHeadUrl() {
      return PartialPaymentService.printChequeUrl(this.id, 'en', true)
    },
    printChequeWithoutLetterHeadUrl() {
      return PartialPaymentService.printChequeUrl(this.id, 'en', false)
    },
    printPaymentWithLetterHeadUrl() {
      return PartialPaymentService.printPaymentUrl(this.id, 'en', true)
    },
    printPaymentWithoutLetterHeadUrl() {
      return PartialPaymentService.printPaymentUrl(this.id, 'en', false)
    },
    printChequeWithLetterHeadUrlKh() {
      return PartialPaymentService.printChequeUrl(this.id, 'km', true)
    },
    printChequeWithoutLetterHeadUrlKh() {
      return PartialPaymentService.printChequeUrl(this.id, 'km', false)
    },
    printPaymentWithLetterHeadUrlKh() {
      return PartialPaymentService.printPaymentUrl(this.id, 'km', true)
    },
    printPaymentWithoutLetterHeadUrlKh() {
      return PartialPaymentService.printPaymentUrl(this.id, 'km', false)
    }
  },

  methods: {
    openDialog() {
      this.showDialog = true
      this.submitted = false
    },
    hideDialog() {
      this.showDialog = false
      this.submitted = false
    },
    approve(form) {
      this.submitted = true

      if (form.status && form.comment) {
        PartialPaymentService.approve(form, this.id)
          .then(res => {
            if (res.data?.success) {
              notify(res.data?.message, 'success');
              this.$router.push({ name: 'PartialPaymentIndex' })
            }
          }).catch(err => {
            notify(err?.response?.data?.message, 'error');
          })
      }
    },
    handleDelete(id) {
      this.$confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Delete',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-danger",
        rejectClass: "p-button-secondary",
        blockScroll: false,
        accept: () => {
          PartialPaymentService.delete(id).then(res => {
            if (res.data.success) {
              notify(res.data?.message, 'success');
              this.$router.push({ name: 'PartialPaymentIndex' });
            }
          }).catch(err => {
            notify(err?.response?.data?.message, 'error');
          })
        },
      });
    },

    handleGeneratePaymentNo(id) {
      this.isLoading = true

      PartialPaymentService.savePaymentNumbers(id).then(res => {
        if (res.data?.success) {
          notify(res.data?.message, 'success');
          this.$router.push({ name: 'PartialPaymentIndex' })
        }
      }).catch(err => {
        notify(err?.response?.data?.message, 'error');
      })
        .finally(() => this.isLoading = false)
    },

    getDetail() {
      PartialPaymentService.detail(this.id).then(res => {
        this.formValues = res.data
      })
        .catch(err => {
          notify(err?.response?.data?.message, 'error');
        }).finally(() => this.pageLoading = false)
    },
    havePaymentNumbers() {
      PartialPaymentService.havePaymentNumbers(this.id).then(res => {
        this.alreadyHavePaymentNumbers = res.data
      })
    },
    formatCurrency(number) {
      if (!number) return ''

      if (typeof number === 'string' && !number.includes(",")) {
        number = parseFloat(number);
      }
      return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
    },
    formatDate(date) {
      if (!date) return '';

      return moment(date).format('DD/MM/YYYY');
    },
  },

  mounted() {
    this.havePaymentNumbers()
    this.getDetail()
  }
}
</script>