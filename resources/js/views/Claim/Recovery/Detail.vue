<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
      <h2 class="text-lg font-medium mr-auto">
        Deductible/Salvage Detail
      </h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button v-if="canApprove" class="btn btn-primary shadow-md mr-2" @click="openDialog">
          <span class="h-6 leading-6">Approve</span>
        </button>
        <div v-if="isApproved" class="dropdown">
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
                :href="printWithLetterHeadUrl" target="_blank">Recovery (Letterhead)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadUrl" target="_blank">Recovery (No Letterhead)</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="intro-y box overflow-hidden mt-5 pb-10">
      <div class="text-center">
        <div class="py-10">
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">Claim Recovery</div>
        </div>
      </div>
      <div class="grid 2xl:grid-cols-6 sm:grid-cols-6 px-5 sm:px-16">
        <div class="text-base font-bold mb-1.5">Claims No.</div>
        <div class="text-base col-span-5">: {{ formValues.claim_no }}</div>
        <div class="text-base font-bold mb-1.5">Insured Name</div>
        <div class="text-base col-span-5">: {{ formValues.insured_name }}</div>
        <div class="text-base font-bold mb-1.5">Policy No.</div>
        <div class="text-base col-span-5">: {{ formValues.document_no }}</div>
        <div class="text-base font-bold mb-1.5">Address</div>
        <div class="text-base col-span-5">: {{ formValues.address }}</div>
      </div>
      <div class="px-5 sm:px-16" v-if="formValues.cause_of_losses.length">
        <DataTable ref="tabulator" :options="tableOptions" />
      </div>
    </div>

    <ApproveDialog :isVisible="showDialog" header="Accept on Recovery Paid" :submitted="submitted"
      :options="[{ value: 'APV', label: 'Approve' }, { value: 'REJ', label: 'Reject' }]" value="APV" :errors="errors"
      @hideDialog="hideDialog" @confirm="approve" />
  </div>
</template>

<script>

import ApproveDialog from '@/components/Dialogs/ApproveDialog.vue'
import RecoveryService from '@/services/claim/recovery.service'
import { hasPermission } from '@/services/auth.service'

export default {
  components: {
    ApproveDialog
  },
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        claim_no: '',
        insured_name: '',
        cause_of_losses: [],
      },
      showDialog: false,
      submitted: false,
      errors: []
    }
  },

  computed: {
    canApprove() {
      let canApprovePermission = hasPermission('CLAIM_RECOVERY', 'APPROVE')
      if (!canApprovePermission) return false

      if (this.formValues?.approved_status !== null) return false
      return true
    },
    isApproved() {
      if (this.formValues?.approved_status === 'APV') return true;
      return false;
    },
    printWithLetterHeadUrl() {
      return RecoveryService.printUrl(this.id, 'en', true)
    },
    printWithoutLetterHeadUrl() {
      return RecoveryService.printUrl(this.id, 'en', false)
    },
    tableOptions() {
      return {
        layout: "fitColumns",
        pagination: false,
        placeholder: "No Data Available",
        headerSortTristate: true,
        data: this.formValues?.cause_of_losses,
        ajaxSorting: false,
        ajaxFiltering: false,
        columns: [
          {
            title: 'Type of Loss',
            field: 'cause_of_loss_desc',
            headerSort: false,
          },
          {
            title: 'Third Party Recovery',
            field: 'third_party_recovery',
            headerSort: false,
            headerHozAlign: "right",
            hozAlign: 'right',
            mutator: (_, row) => this.formatCurrency(
              row.third_party_recovery
            ),
          },
          {
            title: 'Insured Sharing',
            field: 'insured_sharing_request',
            headerSort: false,
            headerHozAlign: "right",
            hozAlign: 'right',
            mutator: (_, row) => this.formatCurrency(
              row.insured_sharing_request
            ),
          },
          {
            title: 'Payment Type',
            field: 'payment_type_name',
            headerSort: false,
          },
          {
            title: 'Salvage',
            field: 'salvage',
            headerSort: false,
            headerHozAlign: "right",
            hozAlign: 'right',
            mutator: (_, row) => this.formatCurrency(
              row.salvage
            ),
          },
          {
            title: 'Remark',
            field: 'remark',
            headerSort: false,
          },
        ],
      }
    }
  },

  methods: {
    getData() {
      if (this.id) {
        RecoveryService.getData(this.id).then(res => {
          this.formValues = res.data
        })
          .catch(err => {
            notify(err?.response?.data?.message, 'error');
          })
      }
    },
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
      RecoveryService.approve(form, this.id)
        .then(res => {
          if (res.data?.success) {
            notify(res.data?.message, 'success');
            this.$router.push({ name: 'ClaimRecoveryIndex' })
          }
        }).catch(err => {
          if (err.status == 422) this.errors = err.response?.data?.errors
          notify(err?.response?.data?.message, 'error');
        }).finally(() => this.submitted = false)
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
    this.getData()
  },
}
</script>