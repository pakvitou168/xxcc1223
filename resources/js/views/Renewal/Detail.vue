<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
      <h2 class="text-lg font-medium mr-auto">Renewal Detail</h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button v-if="canBeApproved" class="btn btn-primary shadow-md mr-2" title="Approve"
          @click="openApproveDialog('Approve', 'APPROVE')">
          <span class="h-6 leading-6">Approve</span>
        </button>
        <button v-if="canBeAccepted" class="btn btn-primary shadow-md mr-2" title="Accept"
          @click="openApproveDialog('Accept', 'ACCEPT')">
          <span class="h-6 leading-6">Accept</span>
        </button>
        <button v-if="canGenerateNewVersion" class="btn btn-primary shadow-md mr-2"
          :disabled="isLoadingGenerateNewRenewal" @click="handleGenerateNewVersion">
          <span class="h-6 leading-6">Generate New Version<span v-if="isLoadingGenerateNewRenewal">...</span></span>
        </button>
        <button v-if="canBeProcessedToPolicy" class="btn btn-primary shadow-md mr-2"
          :disabled="isLoadingProceedToPolicy" title="Proceed to Renewal Policy" @click="handleProceedToPolicy">
          <span class="h-6 leading-6">Process to Policy<span v-if="isLoadingProceedToPolicy">...</span></span>
        </button>
        <div class="dropdown">
          <button class="dropdown-toggle btn btn-primary shadow-md mr-2" title="Print Quote" id="print-button">
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
                :href="printWithLetterHeadLink" target="_blank">Letterhead (EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadLink" target="_blank">No Letterhead (EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadAndStampLink" target="_blank">Signature with No letterhead (EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithLetterHeadLinkKh" target="_blank">Letterhead (KH)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadLinkKh" target="_blank">No Letterhead (KH)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadAndStampLinkKh" target="_blank">Signature with No letterhead (KH)</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="intro-y box overflow-hidden mt-5">
      <div class="text-center">
        <div class="py-10">
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">
            <span>{{ formValues.product_name }}</span>
          </div>
          <div class="mt-2 text-xl text-center">RENEWAL NOTICE</div>
        </div>
        <div class="flex flex-col lg:flex-row px-5 sm:px-16 pt-5">
          <div class="text-right mt-10 lg:mt-0 lg:ml-auto">
            <div class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2">
              Renewal Policy No.: <span>{{ formValues.document_no }}</span>
            </div>
            <div class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2">
              Business Code:{{ formValues.business_code }}
            </div>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16 py-5">
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">
              ATTENTION TO:
              <p>Dear Valued Client,</p>
            </div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-bold mb-3">
              <span>{{ formValues.insured_name }}</span>
            </div>
          </div>
        </div>
        <div class="w-full text-md font-bold mb-5">
          Phillip General Insurance (Cambodia) Plc. is pleased to invite you to renew your
          expiring Policy. To ensure the full coverage, please review and advise us for
          any revision such as insurable interest, sum insured, and other information. We
          reserve the rights to revise the premium, terms and conditions if there is any
          claim incurred before the expiry.
        </div>
        <div class="w-full text-md font-bold mb-3">
          Kindly check and sign on this renewal notice, and return us before the expiry,
          here are your renewal terms and premium:
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">THE INSURED NAME:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-bold mb-3">
              <span>{{ formValues.insured_name }}</span>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">CORRESPONDENCE ADDRESS:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-bold mb-3">
              <span>{{ formValues.address }}</span>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">BUSINESS/OCCUPATION:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-bold mb-3">
              <span>{{ formValues.occupation }}</span>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">PERIOD OF INSURANCE:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-bold mb-3">
              <span>{{ formValues.insurance_period }}</span>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">COVERAGE:</div>
          </div>
          <div class="w-2/3">
            <div v-for="item in formValues.cover" :key="item.code">
              <div class="text-sm font-bold mb-1">
                {{ item.cover_name }} ({{ item.cover_code }})
              </div>
              <div class="text-sm mb-2" v-html="item.html_detail"></div>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">POLICY WORDING:</div>
          </div>
          <div class="w-2/3">
            <div class="text-sm">
              {{ formValues.policy_wording }}
            </div>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16">
        <div class="flex" v-if="formValues.vehicles && formValues.vehicles.length > 1">
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">INSURED VEHICLE:</div>
          </div>
          <div class="w-2/3">
            <div class="text-sm mb-3 underline cursor-pointer btn-attach" @click="exportVehicles">
              {{ formValues.vehicles.length + " units as per list attached" }}
            </div>
          </div>
        </div>
        <div class="flex" v-if="formValues.vehicles && formValues.vehicles.length > 1">
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">TOTAL SUM INSURED:</div>
          </div>
          <div class="w-2/3">
            <div class="text-sm font-bold mb-3">
              {{ formValues.total_sum_insured }}
            </div>
          </div>
        </div>
        <div class="flex" v-if="formValues.vehicles && formValues.vehicles.length > 1">
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">TOTAL PREMIUM:</div>
          </div>
          <div class="w-2/3">
            <div class="text-sm font-bold mb-3">
              {{ formValues.total_premium }}
            </div>
          </div>
        </div>
        <div class="table-responsive" v-if="formValues.vehicles && formValues.vehicles.length == 1">
          <table class="table">
            <thead>
              <tr>
                <th class="border-b-2 whitespace-nowrap">Make and Model</th>
                <th class="border-b-2 whitespace-nowrap">Plate No.</th>
                <th class="border-b-2 whitespace-nowrap">Chassis No.</th>
                <th class="border-b-2 whitespace-nowrap">Engine No.</th>
                <th class="border-b-2 whitespace-nowrap">Year of Manufacture</th>
                <th v-if="isCommercialVehicle" class="border-b-2 whitespace-nowrap">
                  Seats/Tonnage
                </th>
                <th v-if="!isCommercialVehicle" class="border-b-2 whitespace-nowrap">
                  Cubic Capacity
                </th>
                <th class="border-b-2 whitespace-nowrap">Sum Insured (USD)</th>
                <th class="border-b-2 whitespace-nowrap">Premium (USD)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="vehicle in formValues.vehicles" :key="vehicle.detail_id">
                <td class="border-b">{{ vehicle.make_model }}</td>
                <td class="border-b">{{ vehicle.plate_no }}</td>
                <td class="border-b">{{ vehicle.chassis_no }}</td>
                <td class="border-b">{{ vehicle.engine_no }}</td>
                <td class="border-b">{{ vehicle.manufacturing_year }}</td>
                <td class="border-b">{{ vehicle.cubic }}</td>
                <td class="border-b">{{ formatCurrency(vehicle.sum_insured) }}</td>
                <td class="border-b">{{ formatCurrency(vehicle.premium) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="px-5 sm:px-16 py-5">
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">LIMITATION AS TO USE:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">{{ formValues.limitation_to_use }}</div>
          </div>
        </div>
        <div v-if="formValues.vehicles">
          <div v-if="formValues.nf_discount" class="flex">
            <div class="w-1/3">
              <div class="text-md font-bold mb-3">DISCOUNT:</div>
            </div>
            <div class="w-2/3">
              <div class="text-md font-medium mb-3">{{ formValues.discount }}</div>
            </div>
          </div>

          <div class="flex">
            <div class="w-1/3">
              <div class="text-md font-bold mb-3">NO CLAIM DISCOUNT:</div>
            </div>
            <div class="w-2/3">
              <div class="text-md font-medium mb-3" v-if="formValues.vehicles.length == 1">
                {{ formValues.no_claim_discount }}
              </div>
              <div class="text-md font-bold mb-3" v-else-if="formValues.vehicles.length > 1">
                As per list attached
              </div>
            </div>
          </div>
        </div>

        <div class="flex" v-if="formValues.vehicles">
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">DEDUCTIBLE:</div>
          </div>
          <div class="w-2/3" v-if="formValues.vehicles.length == 1">
            <div class="text-sm font-bold mb-2">
              It is applicable to each and every claim for:
            </div>
            <div class="text-sm mb-2" v-for="(item,index) in formValues.deductibles" :key="'dt'+index">
              {{ item.deductible }}
            </div>
          </div>
          <div class="w-2/3" v-else-if="formValues.vehicles.length > 1">
            <div class="text-sm font-bold mb-2">As per list attached</div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ENDORSEMENTS/CLAUSES:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-for="(endorsement_clause, index) in formValues.endorsement_clauses"
              :key="index">
              {{ endorsement_clause }}
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">GENERAL EXCLUSIONS:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-for="(exclusion_clause, index) in formValues.exclusion_clauses"
              :key="index">
              {{ exclusion_clause }}
            </div>
          </div>
        </div>
        <div v-if="formValues.warranty" class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">WARRANTY:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-html="formValues.warranty"></div>
          </div>
        </div>
        <div v-if="formValues.memorandum" class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">MEMORANDUM:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-html="formValues.memorandum"></div>
          </div>
        </div>
        <div v-if="formValues.subjectivity" class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">SUBJECTIVITY:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-html="formValues.subjectivity"></div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">JURISDICTION:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">{{ formValues.jurisdiction }}</div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">CLAIM AMOUNT (USD):</div>
          </div>
          <div class="w-2/3 grid grid-cols-2">
            <div class="col-span-2">
              <div class="w-full grid grid-cols-3">
                <div class="text-md font-medium mb-3">Claim Incurred: {{ formatCurrency(formValues.claim_incurred) }}</div>
                <div class="text-md font-medium mb-3">Claim Paid: {{ formatCurrency(formValues.claim_paid) }}</div>
                <div class="text-md font-medium mb-3"> Claim Outstanding: {{ formatCurrency(formValues.claim_outstanding) }}</div>
              </div>
            </div>
            <div class="text-md font-medium mb-3 text-red-600 col-span-2">
              For Whole Portfolio
            </div>
            <div class="text-md font-medium mb-3 text-red-600">
              Total Claim Incurred: {{ formatCurrency(formValues.total_claim_incurred) }}
            </div>
            <div class="text-md font-medium mb-3 text-red-600">
              Total Gross Premium: {{ formatCurrency(formValues.total_gross_premium) }}
            </div>
            <div class="text-md font-medium mb-3 text-red-600">
              Total Claim Paid: {{ formatCurrency(formValues.total_claim_paid) }}
            </div>
            <div class="text-md font-medium mb-3 text-red-600">
              Total Claim Ratio(%): {{ formValues.claim_ratio }}
            </div>
            <div class="text-md font-medium mb-3 text-red-600">
              Total Claim Outstanding: {{ formatCurrency(formValues.total_claim_outstanding) }}
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ISSUED ON:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">{{ issuedDate }}</div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ISSUED BY:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              {{ formValues.issued_by }}
            </div>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16 py-5">
        <div class="flex">
          <div class="w-auto">
            <div class="text-md font-bold mb-3 uppercase">
              Phillip General Insurance (Cambodia) Plc.
            </div>
            <div class="my-2" v-bind:class="{ 'relative': signature, 'min-h-40': true }" style="min-height: 150px">
              <img v-if="signature && signature.file_url" class="absolute max-h-20 top-2/3 -left-1/6"
                :src="'/' + signature.file_url" />
              <img v-if="signature && signature.file_url" class="object-cover max-h-40"
                src="/images/stamp/phillip_insurance.png" />
            </div>

            <hr class="my-3" />

            <div class="text-md mb-3 font-medium">Authorised Signature</div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3"></div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3 font-bold pt-1"
              style="text-decoration-line: underline; text-decoration-style: double">
              ACCEPTANCE BY CLIENT:
            </div>
            <div class="text-md font-medium mb-3">
              We examine and understand the above terms and premium payment. We hereby
              accept and agree to the terms to issue the Policy with an effective on
              ...................................................
            </div>
          </div>
        </div>
        <br />
        <div class="flex mt-12">
          <div class="w-1/3"></div>
          <div class="w-2/3">
            <div class="text-md font-medium pt-1 border-t border-gray-200">
              Authorised Signature (Company Stamp if Applicable)
            </div>
          </div>
        </div>
      </div>
    </div>
    <ApproveDialog :isVisible="approvalDialog" :header="approvalDialogTitle" :submitted="submitted"
      :options="[{ value: 'APV', label: 'Approve' }, { value: 'REJ', label: 'Reject' }]" :loading="submittingApproval"
      value="APV" @hideDialog="hideDialog" @confirm="handleApproval" />
  </div>
</template>

<script>
import renewalService from '@/services/renewal/renewal.service'
import ApproveDialog from './ApproveDialog.vue'
import { hasPermission } from '@/services/auth.service'

export default {
  components: {
    ApproveDialog,
  },
  data() {
    return {
      id: this.$route.params.id ?? null,
      functionCode: "POLICY",
      signature: null,
      formValues: {},
      isLoadingProceedToPolicy: false,
      isLoadingGenerateNewRenewal: false,

      approvalDialogTitle: '',
      approvalType: '',
      approvalDialog: false,
      submitted: false,
      submittingApproval: false,
      canBeProcessedToPolicy: false,
    }
  },
  computed: {
    issuedDate() {
      var date = this.formValues.updated_at ?? this.formValues.created_at;
      if (date) {
        return moment(date).format("DD/MM/YYYY");
      }
      return "";
    },

    printWithLetterHeadLink() {
      return `/renewals/${this.id}/download/en?letterhead=1`;
    },
    printWithoutLetterHeadLink() {
      return `/renewals/${this.id}/download/en?letterhead=0`;
    },
    printWithLetterHeadLinkKh() {
      return `/renewals/${this.id}/download/km?letterhead=1`;
    },
    printWithoutLetterHeadLinkKh() {
      return `/renewals/${this.id}/download/km?letterhead=0`;
    },
    printWithoutLetterHeadAndStampLink() {
      return `/renewals/${this.id}/download/en?letterhead=0&noStamp=1`;
    },
    printWithoutLetterHeadAndStampLinkKh() {
      return `/renewals/${this.id}/download/km?letterhead=0&noStamp=1`;
    },
    isCommercialVehicle() {
      return this.hasPassengerOrTonnage;
    },
    hasPassengerOrTonnage() {
      return this.formValues.has_passenger_tonnage
    },
    canBeApproved() {
      let canApprovePermission = hasPermission('RENEWAL', 'APPROVE')
      if (!canApprovePermission) return false

      return this.formValues.submit_status === 'PND' && this.formValues.accept_status === 'PND'
    },
    canBeAccepted() {
      let canAcceptPermission = hasPermission('RENEWAL', 'ACCEPT')
      if (!canAcceptPermission) return false

      return this.formValues.submit_status === 'APV' && this.formValues.accept_status === 'PND'
    },
    canGenerateNewVersion() {
      return this.formValues.submit_status === 'APV'
        && this.formValues.accept_status === 'REJ'
        && this.formValues.status === 'LOS'
    },
  },
  methods: {
    getRenewal() {
      if (this.id) {
        axios.get(`/api/renewals/${this.id}`).then((response) => {
          this.formValues = response.data.data;
          this.signature = response.data.data.signature;
        });
      }
    },
    exportVehicles() {
      location.href =
        "/renewals/" + this.id + "/export-vehicles/" + this.formValues.document_no;
    },
    handleProceedToPolicy() {
      this.$confirm.require({
        message: 'Do you want to proceed to policy?',
        header: 'Confirmation',
        icon: 'pi pi-info-circle',
        acceptLabel: 'Proceed',
        rejectLabel: 'Cancel',
        acceptClass: 'p-button-info',
        rejectClass: 'p-button-danger p-button-outlined',
        blockScroll: false,
        accept: () => {
          this.isLoadingProceedToPolicy = true;
          renewalService.generateRenewedPolicy(this.id).then(res => {
           notify(res.data.message,'success');

            this.$router.push({ name: 'PolicyIndex' })
          })
            .catch(err => {
              notify(err.response.data.message,'error');
            })
            .finally(() => {
              this.isLoadingProceedToPolicy = false;
            })
        },
      });
    },
    openApproveDialog(title, type) {
      this.approvalDialogTitle = title
      this.approvalType = type
      this.approvalDialog = true
      this.submitted = false
    },
    hideDialog() {
      this.approvalDialog = false
      this.submitted = false
      this.approvalDialogTitle = ''
      this.approvalType = ''
    },
    handleApproval(form) {
      this.submitted = true

      if (form.status && form.reason) {
        this.submittingApproval = true
        if (this.approvalType === 'APPROVE') {
          renewalService.approve(form, this.id).then(res => {
            if (res.data?.success) {
              notify(res.data?.message,'success');

              this.$router.push({ name: 'RenewalIndex' })
            }
          }).catch(err => {
            notify(err.response.data.message,'error');
          }).finally(() => this.submittingApproval = false)
        }
        else if (this.approvalType === 'ACCEPT') {
          renewalService.accept(form, this.id).then(res => {
            if (res.data?.success) {
              notify(res.data?.message,'success');

              this.$router.push({ name: 'RenewalIndex' })
            }
          }).catch(err => {
            notify(err.response.data.message,'error');
          }).finally(() => this.submittingApproval = false)
        }
      }
    },
    handleGenerateNewVersion() {
      this.$confirm.require({
        message: 'Do you want to generate new version?',
        header: 'Confirmation',
        icon: 'pi pi-info-circle',
        acceptLabel: 'Generate',
        rejectLabel: 'Cancel',
        blockScroll: false,
        accept: () => {
          this.isLoadingGenerateNewRenewal = true;
          renewalService.generateNewVersion(this.id).then(res => {
            notify(res.data.message,'success');

            this.$router.push({ name: 'RenewalIndex' })
          })
            .catch(err => {
              notify(err.response.data.message,'error');
            })
            .finally(() => {
              this.isLoadingGenerateNewRenewal = false;
            })
        },
      });
    },
    checkCanBeProcessedToPolicy() {
      let canProcessPermission = hasPermission('RENEWAL', 'PROCESS')
      if (!canProcessPermission) {
        this.canBeProcessedToPolicy = false
        return
      }

      renewalService.canGenerateRenewedPolicy(this.id).then(res => this.canBeProcessedToPolicy = res.data.success)
    },
    formatCurrency(number) {
      if (!number) return ''

      if (typeof number === 'string' && !number.includes(",")) {
        number = parseFloat(number)
      }
      return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
    },
  },
  mounted() {
    this.getRenewal()
    this.checkCanBeProcessedToPolicy();
  }
}
</script>

<style scoped>
.img-under {
  position: absolute;
  left: 0px;
  top: 0px;
  z-index: -1;
}

.img-over {
  position: absolute;
  left: 80px;
  top: 10px;
  z-index: -1;
}
</style>