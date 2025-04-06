<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
      <h2 class="text-lg font-medium mr-auto">
        Full Payment
      </h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button v-if="canGenerateRecovery && !isLoading" class="btn btn-primary shadow-md mr-2" :disabled="isLoading"
          @click="handleGenerateRecovery(id)">
          <span class="h-6 leading-6">Generate Recovery</span>
        </button>
        <button v-if="canGenerate && !isLoading" class="btn btn-primary shadow-md mr-2" :disabled="isLoading"
          @click="handleGeneratePaymentNo(id)">
          <span class="h-6 leading-6">Generate Payment No.</span>
        </button>
        <button v-if="canApprove && !isLoading" class="btn btn-primary shadow-md mr-2" @click="openDialog">
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
              <a v-if="isApproved"
                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printChequeWithLetterHeadUrl" target="_blank">Cheque Letterhead(EN)</a>
              <a v-if="isApproved"
                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printChequeWithoutLetterHeadUrl" target="_blank">Cheque No Letterhead(EN)</a>
              <a v-if="isApproved"
                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printChequeWithLetterHeadKmUrl" target="_blank">Cheque Letterhead (KM)</a>
              <a v-if="isApproved"
                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printChequeWithoutLetterHeadKmUrl" target="_blank">Cheque No Letterhead (KM)</a>

              <a v-if="isApproved"
                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printRevisionWithLetterHeadUrl" target="_blank">Print Revision (Letterhead)</a>
              <a v-if="isApproved"
                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printRevisionWithoutLetterHeadUrl" target="_blank">Print Revision (No Letterhead)</a>

              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithLetterHeadUrl" target="_blank">Payment Letterhead(EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadUrl" target="_blank">Payment No Letterhead(EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithLetterHeadKmUrl" target="_blank">Payment Letterhead(KM)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadKmUrl" target="_blank">Payment No Letterhead(KM)</a>
            </div>
          </div>
        </div>
        <button v-if="canDelete && !isLoading" class="btn btn-danger mx-1 intro-x" @click="handleDelete(id)">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
            </path>
          </svg>
        </button>
        <router-link v-if="canUpdate && !isLoading" :to="{ name: 'ClaimProcessEdit', params: { id: id } }">
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
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">Full Payment</div>
        </div>
      </div>
      <div class="w-full px-5 sm:px-16">
        <div class="grid grid-cols-6 gap-x-5">
          <div class="xl:col-span-2 col-span-6 grid xl:grid-cols-2 grid-cols-6 gap-5">
            <div class="text-base font-bold mb-1.5">Claims No.</div>
            <div class="text-base white-space-nowrap xl:col-span-1 col-span-5">:
              {{ formValues.claim_payment_detail.claim_no }}</div>
          </div>
          <div class="xl:col-span-2 col-span-6 grid xl:grid-cols-2 grid-cols-6 gap-5">
            <div class="text-base font-bold mb-1.5 xl:col-span-1 col-span-1">Approve Status</div>
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
          <div class="text-base col-span-5 white-space-nowrap">: {{ formValues.claim_payment_detail.insured_name }}
          </div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Policy No.</div>
          <div class="text-base col-span-5 white-space-nowrap">: {{ formValues.claim_payment_detail.document_no }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Address</div>
          <div class="text-base col-span-5 white-space-nowrap">: {{ formValues.claim_payment_detail.address }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5 white-space-nowrap">Insurance Cover Period</div>
          <div class="text-base col-span-5 white-space-nowrap">: {{
            formatDate(formValues.claim_payment_detail.insured_period_from) + "&nbsp;TO&nbsp;" +
            formatDate(formValues.claim_payment_detail.insured_period_to)
          }}</div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Request By</div>
          <div class="text-base white-space-nowrap">: {{ formValues.updated_by_name }}</div>
          <div class="col-span-4">
            <div class="grid grid-cols-4">
              <div class="text-base font-bold mb-1.5">Request Date</div>
              <div class="text-base white-space-nowrap col-span-3">: {{ formatDate(formValues.updated_at) }}</div>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-6 gap-5">
          <div class="text-base font-bold mb-1.5">Approved By</div>
          <div class="text-base white-space-nowrap">: {{ formValues.approved_by_name }}</div>
          <div class="col-span-4">
            <div class="grid grid-cols-4">
              <div class="text-base font-bold mb-1.5">Approved Date</div>
              <div class="text-base white-space-nowrap col-span-3">: {{ formatDate(formValues.approved_at) }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16" v-if="formValues.cause_of_loss.length">
        <div class="overflow-x-auto scrollbar-hidden">
          <DataTable ref="tabulator" :options="options" v-model="formValues.cause_of_loss" />
        </div>
      </div>
    </div>
    <ApproveDialog :isVisible="showDialog" header="Accept on Full Payment" :submitted="submitted" :errors="errors"
      :saving="approvalLoaing" :options="[{ value: 'APV', label: 'Approve' }, { value: 'REJ', label: 'Reject' }]"
      value="APV" @hideDialog="hideDialog" @confirm="approve" />
  </div>
</template>

<script>
import ApproveDialog from '@/components/Dialogs/ApproveDialog.vue'
import ProcessService from '@/services/claim/process.service'
import { hasPermission } from '@/services/auth.service'

export default {
  components: {
    ApproveDialog,
  },
  data() {
    return {
      id: this.$route.params.id ?? null,
      approvalLoaing: false,
      formValues: {
        claim_payment_detail: {
          policy: []
        },
        reinsurance_details: [],
        total_payable_amount: null,
        total_share: null,
        cause_of_loss: []
      },
      tabulator: null,
      showDialog: false,
      submitted: false,
      errors: [],
      isLoading: true,
      alreadyHavePaymentNumbers: false,
      alreadyGeneratedRecovery: false,
    }
  },
  computed: {
    options() {
      return {
        layout: "fitColumns",
        pagination: false,
        placeholder: "No Data Available",
        headerSortTristate: true,
        data:this.formValues.cause_of_loss,
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
            title: 'Remain Amount',
            field: 'remain_amount',
            headerHozAlign: "right",
            hozAlign: 'right',
            width: 140,
            headerSort: false,
            mutator: (_, row) => this.formatCurrency(
              row.remain_amount
            ),
          },
          {
            title: 'Deductible',
            field: 'deductible',
            headerHozAlign: "right",
            hozAlign: 'right',
            width: 100,
            headerSort: false,
            mutator: (_, row) => this.formatCurrency(
              row.deductible
            ),
          },
          {
            title: 'Deductible Paid',
            field: 'deductible_paid',
            headerHozAlign: "right",
            hozAlign: 'right',
            width: 140,
            headerSort: false,
            mutator: (_, row) => this.formatCurrency(
              row.deductible_paid
            ),
          },
          {
            title: 'Insured Sharing Request',
            field: 'insured_sharing_request',
            headerHozAlign: "right",
            hozAlign: 'right',
            headerSort: false,
            width: 200,
            mutator: (_, row) => this.formatCurrency(
              row.insured_sharing_request
            ),
          },
          {
            title: 'Payment Type',
            field: 'payment_type',
            width: 140,
            headerSort: false,
          },
          {
            title: 'Recovery from Third Party',
            field: 'recovery_from_third_party',
            headerHozAlign: "right",
            hozAlign: 'right',
            width: 200,
            headerSort: false,
            mutator: (_, row) => {
              return row?.recovery_from_third_party_from_register ?? row?.recovery_from_third_party
            }
          }
        ],
      }
    },
    canApprove() {
      let canApprovePermission = hasPermission('CLAIM_PROCESS', 'APPROVE')
      if (!canApprovePermission) return false

      if (this.formValues?.approved_status !== null) return false
      return true
    },
    isApproved() {
      if (this.formValues?.approved_status === 'APV') return true;
      return false;
    },
    canUpdate() {
      let canUpdatePermission = hasPermission('CLAIM_PROCESS', 'UPDATE')
      if (!canUpdatePermission) return false

      if (this.formValues?.approved_status !== null) return false
      return true
    },
    canDelete() {
      let canDeletePermission = hasPermission('CLAIM_PROCESS', 'DELETE')
      if (!canDeletePermission) return false

      if (this.formValues?.approved_status == 'APV') return false
      return true
    },
    canGenerate() {
      let canApprovePermission = hasPermission('CLAIM_PROCESS', 'APPROVE')
      if (!canApprovePermission) return false

      if (this.formValues?.approved_status !== 'APV') return false

      if (this.alreadyHavePaymentNumbers) return false
      return true
    },
    canGenerateRecovery() {
      if (this.formValues?.approved_status !== 'APV') return false
      if (!this.alreadyHavePaymentNumbers) return false
      if (this.alreadyGeneratedRecovery) return false

      return true
    },
    printChequeWithLetterHeadUrl() {
      return ProcessService.printChequeUrl(this.id, 'en', true)
    },
    printChequeWithoutLetterHeadUrl() {
      return ProcessService.printChequeUrl(this.id, 'en', false)
    },
    printChequeWithLetterHeadKmUrl() {
      return ProcessService.printChequeUrl(this.id, 'km', true)
    },
    printChequeWithoutLetterHeadKmUrl() {
      return ProcessService.printChequeUrl(this.id, 'km', false)
    },
    printRevisionWithLetterHeadUrl() {
      return ProcessService.printRevisionUrl(this.id, 'en', true)
    },
    printRevisionWithoutLetterHeadUrl() {
      return ProcessService.printRevisionUrl(this.id, 'en', false)
    },
    printWithLetterHeadUrl() {
      return ProcessService.printUrl(this.id, 'en', true)
    },
    printWithoutLetterHeadUrl() {
      return ProcessService.printUrl(this.id, 'en', false)
    },
    printWithLetterHeadKmUrl() {
      return ProcessService.printUrl(this.id, 'km', true)
    },
    printWithoutLetterHeadKmUrl() {
      return ProcessService.printUrl(this.id, 'km', false)
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
      if (form.status && form.comment) {
        this.submitted = true
        this.approvalLoaing = true
        ProcessService.approve(form, this.id).then(res => {
          if (res.data?.success) {
            notify(res.data?.message, 'success');
            this.$router.push({ name: 'ClaimProcessIndex' })
          }
        }).catch(err => {
          notify(err?.response?.data?.message, 'error');
        }).finally(() => this.approvalLoaing = false)
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
          ProcessService.delete(id).then(res => {
            if (res.data.success) {
              notify(res.data?.message, 'success');
              this.$router.push({ name: 'ClaimProcessIndex' });
            }
          }).catch(err => {
            notify(err?.response?.data?.message, 'error');
          })
        },
      });
    },
    handleGeneratePaymentNo(id) {
      this.isLoading = true

      ProcessService.savePaymentNumbers(id).then(res => {
        if (res.data?.success) {
          notify(res.data?.message, 'success');
          // this.$router.push({ name: 'ClaimProcessIndex' })
          this.alreadyHavePaymentNumbers = true;
        }
      }).catch(err => {
        notify(err?.response?.data?.message, 'error');
      })
        .finally(() => this.isLoading = false)
    },
    handleGenerateRecovery(id) {
      this.$confirm.require({
        message: 'Do you want to generate recovery?',
        header: 'Generate Recovery',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-info",
        rejectClass: "p-button-secondary",
        acceptLabel: 'Generate',
        rejectLabel: 'Cancel',
        blockScroll: false,
        accept: () => {
          ProcessService.generateRecovery(id).then(res => {
            if (res.data.success) {
              notify(res.data?.message, 'success');
              this.$router.push({ name: 'ClaimRecoveryIndex' })
            }
          }).catch(err => {
            notify(err?.response?.data?.message, 'error');
          })
        },
      });
    },
    havePaymentNumbers() {
      ProcessService.havePaymentNumbers(this.id).then(res => {
        this.alreadyHavePaymentNumbers = res.data
      })
    },
    hasGeneratedRecovery() {
      ProcessService.alreadyGeneratedRecovery(this.formValues.claim_payment_detail?.claim_no).then(res => {
        this.alreadyGeneratedRecovery = res.data
      })
    },
    getDetail() {
      ProcessService.detail(this.id).then(res => {
        this.formValues = res.data
      })
        .then(() => {
          this.hasGeneratedRecovery()
        })
        .catch(err => {
          notify(err?.response?.data?.message, 'error');
        }).finally(() => this.isLoading = false)
    },
    formatDate(date) {
      if (!date) return '';

      return moment(date).format('DD/MM/YYYY');
    },
    formatMonthAndYear(date) {
      let date_format = new Date(date);
      return date_format.getMonth() + '/' + date_format.getFullYear()
    },

    formatCurrency(number) {
      if (!number) return ''

      if (typeof number === 'string' && !number.includes(",")) {
        number = parseFloat(number)
      }
      return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
    }
  },
  mounted() {
    this.havePaymentNumbers()
    this.getDetail()
  }
}
</script>