<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
      <h2 class="text-lg font-medium mr-auto">
        Claim Registration
      </h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button v-if="canApprove && !loading" class="btn btn-primary shadow-md mr-2" @click="openDialog">
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
          <div class="dropdown-menu w-40">
            <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithLetterHeadUrl" target="_blank">Letterhead (EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadUrl" target="_blank">No Letterhead (EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithLetterHeadKmUrl" target="_blank">Letterhead (KM)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadKmUrl" target="_blank">No Letterhead (KM)</a>
            </div>
          </div>
        </div>
        <button v-if="canDelete && !loading" class="btn btn-danger mx-1 intro-x" @click="handleDelete(id)">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
            </path>
          </svg>
        </button>
        <router-link v-if="canUpdate" :to="{ name: 'ClaimRegisterEdit', params: { id: id } }">
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
    <div class="intro-y box overflow-hidden mt-5">
      <div class="text-center">
        <div class="py-10">
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">Claim Registration</div>
        </div>
      </div>
      <div class="grid 2xl:grid-cols-3 px-5 sm:px-16">
        <div class="col-span-2">
          <div class="pt-6">
            <div class="text-xl font-bold mb-2.5">Vehicle Policy Details</div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Claims No.</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.claim_no }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Insured Name</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.insured_name }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Policy No.</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.document_no }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Address</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.address }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Insurance Cover Period</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ getInsuranceCoverPeriod }}</div>
              </div>
            </div>
          </div>
          <div class="pt-6">
            <div class="text-xl font-bold mb-2.5">Accident Details</div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Date of Loss</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formatDate(formValues.claim.incident_date) }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Date of Notification</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formatDate(formValues.claim.notification_date) }}</div>
              </div>
            </div>
            <div class="flex mb-3">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold">Cause of Loss</div>
              </div>
              <div class="w-2/4">
                <div class="grid grid-cols-2">
                  <div class="text-base font-bold">Cause of Loss</div>
                  <div class="text-base font-bold text-right">Reserve Amount </div>
                </div>
                <div class="grid grid-cols-2" v-for="(detail, index) in formValues.claim_details" :key="index">
                  <div class="text-base">{{ detail.cause_of_loss_desc }}</div>
                  <div class="text-base text-right">{{ formatCurrency(detail.amount) }}</div>
                </div>

                <div class="grid grid-cols-2" v-for="(detail, index) in formValues.claim_details" :key="`${index}_`">
                  <!-- If cause of loss is Own Damage and has value, show Recovery from Third Party -->
                  <template v-if="detail.cause_of_loss_code === 'OD' && detail.recovery_from_third_party > 0">
                    <div class="text-base">Recovery from Third Party</div>
                    <div class="text-base text-right">{{ formatCurrency(detail.recovery_from_third_party) }}</div>
                  </template>
                </div>

              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Location of Loss</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.incident_location }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Description of Loss</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.remark }}</div>
              </div>
            </div>
          </div>
          <div class="pt-6">
            <div class="text-xl font-bold mb-2.5">Insured Vehicle Details</div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Make Model</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.make_model }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Registration No.</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.registeration_no }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Engine No.</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.engine_no }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Chassis No.</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.chassis_no }}</div>
              </div>
            </div>
          </div>
          <div class="pt-6">
            <div class="text-xl font-bold mb-2.5">Authorized Driver's Details</div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Name</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.driver_name }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Gender</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.gender }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Driver's age</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.driver_age }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Occupation</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.occupation }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Driver's License No.</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.license_no }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Issued Date</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formatDate(formValues.claim.license_issue_date) }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Expiry Date</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formatDate(formValues.claim.license_expire_date) }}</div>
              </div>
            </div>
          </div>
          <div class="pt-6">
            <div class="text-xl font-bold mb-2.5">Third Party Vehicle Details</div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Make Model</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.vehicle_model }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Registration No.</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">{{ formValues.claim.plate_no }}</div>
              </div>
            </div>
          </div>

          <div class="pt-6">
            <div class="text-xl font-bold mb-2.5">Claim Estimation: <span>{{ formatCurrency(formValues.claim_estimation)
                }}</span>
            </div>
          </div>

          <div class="pt-6 py-10">
            <div class="text-xl font-bold mb-2.5">Re-Insurance Arrangement</div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold">Re-insurance Type</div>
              </div>
              <div class="w-2/3 grid grid-cols-2">
                <div class="grid grid-cols-2">
                  <div class="text-base font-bold text-center">Share Rate</div>
                  <div class="text-base font-bold text-right">Reserve Amount</div>
                </div>
              </div>
            </div>
            <div class="flex" v-for="(detail, index) in formValues.reinsurance_details" :key="index">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base">{{ detail.name }}</div>
              </div>
              <div class="w-2/3 grid grid-cols-2">
                <div class="grid grid-cols-2">
                  <div class="text-base text-center">{{ detail.share }}%</div>
                  <div class="text-base text-right">{{ formatCurrency(detail.reserve_amount) }}</div>
                </div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold">TOTAL</div>
              </div>
              <div class="w-2/3 grid grid-cols-2">
                <div class="grid grid-cols-2">
                  <div class="text-base font-bold text-center">{{ formValues.total_share_percentage }}%</div>
                  <div class="text-base font-bold text-right">{{ formatCurrency(formValues.claim_estimation) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <ApproveDialog :isVisible="showDialog" header="Register Approval" :submitted="submitted"
      :options="[{ value: 'APV', label: 'Approve' }, { value: 'REJ', label: 'Reject' }]" :errors="errors" value="APV"
      @hideDialog="hideDialog" @confirm="approve" />

  </div>
</template>

<script>

import ApproveDialog from '@/components/Dialogs/ApproveDialog.vue'
import RegisterService from '@/services/claim/register.service'
import { hasPermission } from '@/services/auth.service'

export default {
  components: {
    ApproveDialog,
  },

  data() {
    return {
      id: this.$route.params.id ?? null,
      showDialog: false,
      submitted: false,
      formValues: {
        claim: {},
        claim_details: [],
        reinsurance_details: [],
      },
      errors: [],
      loading: true
    }
  },

  computed: {
    printWithLetterHeadUrl() {
      return RegisterService.printUrl(this.id, 'en', true)
    },
    printWithoutLetterHeadUrl() {
      return RegisterService.printUrl(this.id, 'en', false)
    },
    printWithLetterHeadKmUrl() {
      return RegisterService.printUrl(this.id, 'km', true)
    },
    printWithoutLetterHeadKmUrl() {
      return RegisterService.printUrl(this.id, 'km', false)
    },
    getInsuranceCoverPeriod() {
      return `${this.formatDate(this.formValues.claim?.insured_period_from)} To ${this.formatDate(this.formValues.claim?.insured_period_to)}`
    },

    canApprove() {
      let canApprovePermission = hasPermission('CLAIM_REGISTER', 'APPROVE')
      if (!canApprovePermission) return false

      if (this.formValues?.approved_status !== null) return false
      return true
    },

    canUpdate() {
      let canUpdatePermission = hasPermission('CLAIM_REGISTER', 'UPDATE')
      if (!canUpdatePermission) return false

      if (this.formValues?.approved_status !== null) return false
      return true
    },
    canDelete() {
      let canDeletePermission = hasPermission('CLAIM_REGISTER', 'DELETE')
      if (!canDeletePermission) return false

      if (this.formValues?.approved_status === 'APV') return false
      return true
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
      RegisterService.approve(form, this.id)
        .then(res => {
          if (res.data?.success) {
            notify(res.data?.message, 'success');
            this.$router.push({ name: 'ClaimRegisterIndex' })
          }
        }).catch(err => {
          if (err.status == 422) this.errors = err.response.data.errors
          else notify(err?.response?.data?.message, 'error');
        }).finally(() => this.submitted = false)
    },

    handleDelete(id) {
      this.$confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Delete',
        icon: 'pi pi-info-circle text-red-500',
        rejectClass: "p-button-secondary",
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          RegisterService.delete(id).then(res => {
            if (res.data.success) {
              notify(res.data?.message, 'success');
              this.$router.push({ name: 'ClaimRegisterIndex' });
            }
          }).catch(err => {
            notify(err?.response?.data?.message, 'error');
          })
        },
      });
    },

    getDetail() {
      RegisterService.detail(this.id).then(res => {
        this.formValues = res.data
      })
        .catch(err => {
          notify(err?.response?.data?.message, 'error');
        }).finally(() => this.loading = false)
    },

    formatDate(date) {
      if (!date) return '';

      return moment(date).format('DD/MM/YYYY');
    },

    formatCurrency(number) {
      if (!number) return ''

      if (typeof number === 'string') {
        number = parseFloat(number)
      }
      return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
    }
  },

  mounted() {
    this.getDetail()
  }
}
</script>