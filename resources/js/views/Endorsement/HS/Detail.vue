<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
      <h2 class="text-lg font-medium mr-auto">H & S Endorsement Detail</h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button v-if="canGenerateEndorsement" class="btn btn-primary shadow-md mr-2" title="Generate Endorsement"
                @click="openEndorsementDialog">
          <span class="h-6 leading-6">Generate Endorsement</span>
        </button>
        <div v-if="canExportInsuredPerson || canExportAllInsuredPerson" class="dropdown">
          <button class="dropdown-toggle btn btn-success shadow-md mr-2" title="Export Excel">
            <DocumentTextIcon/>
          </button>
          <div class="dropdown-menu w-56">
            <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
              <a v-if="canExportInsuredPerson"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 @click="exportInsuredPerson" target="_blank">Endorsements</a>
              <a v-if="canExportAllInsuredPerson"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 @click="exportAllInsuredPerson" target="_blank">
                Update List for All
              </a>
            </div>
          </div>
        </div>
        <button v-if="canApproveCond" class="btn btn-primary shadow-md mr-2" title="Approve Endorsement"
                @click="openApproveDialog">
          <span class="h-6 leading-6">Endorsement Approval</span>
        </button>
        <button v-if="canDeleteCond" class="btn btn-danger mr-2 intro-x" title="Delete" @click="handleDelete(id)">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
            </path>
          </svg>
        </button>
        <router-link v-if="canUpdateCond" :to="{ name: 'HSEndorsementEdit', params: { id: id } }">
          <button class="btn btn-primary intro-x" title="Edit">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
              </path>
            </svg>
          </button>
        </router-link>
        <div class="dropdown ml-2">
          <button class="dropdown-toggle btn btn-primary shadow-md mr-2" title="Print">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
              </path>
            </svg>
          </button>
          <div class="dropdown-menu w-56">
            <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
              <a v-if="canPrintInvoice && isApproved"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printInvoiceUrlWithSignature" target="_blank">Invoice (Signature)</a>
              <a v-if="canPrintInvoice"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printInvoiceUrlWithoutSignature" target="_blank">Invoice (No Signature)</a>

              <a v-if="canPrintCreditNote && isApproved"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printCreditNoteUrlWithSignature" target="_blank">Credit Note (Signature)</a>
              <a v-if="canPrintCreditNote"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printCreditNoteUrlWithoutSignature" target="_blank">Credit Note (No Signature)</a>

              <a
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printLinkLetterHead" target="_blank">Endorsement (Letterhead)</a>
              <a
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printLinkNoLetterHead" target="_blank">Endorsement (No Letterhead)</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <Detail v-if="id" :id="id" :endorsementId="id" :endorsementStatus="formValues.status" :endorsement="formValues"
              :documentNo="documentNo" @get-total-premium="getTotalPremium"/>
    </div>

    <ApproveDialog header="Endorsement Approval" :isVisible="approveDialog" :submitted="submitted"
                   :options="{ APV: 'Approve', REJ: 'Reject' }" value="APV" @hideDialog="hideDialog"
                   @confirm="approve"/>

    <EndorsementDialog header="Generate Endorsement" :isVisible="endorsementDialog.opened"
                       :submitting="endorsementDialog.submitting" :types="endorsementTypes"
                       :validFromDate="validPeriod.from"
                       :validToDate="validPeriod.to" :typeErrors="endorsementDialog.errors?.type ?? []"
                       :effectiveDateErrors="endorsementDialog.errors?.effective_date ?? []" @hide="() => {
        endorsementDialog.opened = false;
        endorsementDialog.submitting = false;
        endorsementDialog.errors = {};
      }
        " @confirm="generateEndorsement"/>
  </div>
</template>

<script>
// import ApproveDialog from '@/components/Common/HS/ApprovalDialog.vue'
import ApproveDialog from "../../Policy/HS/ApproveDialog.vue";
import Detail from "../FormTabs/Info/HS/Detail.vue";
import endorsementService from "@/services/hs/endorsement.service";
import UserPermissions from "../../../mixins/UserPermissions";
import EndorsementDialog from "./EndorsementDialog.vue";
import DocumentTextIcon from '@/components/Icons/DocumentTextIcon.vue'

export default {
  mixins: [UserPermissions],

  components: {
    ApproveDialog,
    Detail,
    EndorsementDialog,
    DocumentTextIcon
  },

  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!",
      SUCCESS_MESSAGE:"Success!",
      id: this.$route.params.id ?? null,
      canGenerateEndorsement: false,
      formValues: {},
      approveDialog: false,
      submitted: false,
      totalPremium: null,
      functionCode: "ENDORSEMENT",
      isReinsuranceCompleted: false,
      isConfigCompleted: false,
      showEndorsementDialog: false,
      insured_length: 0,
      endorsementDialog: {
        opened: false,
        submitting: false,
        errors: {},
      },
      endorsementTypes: {},
      validPeriod: {},
      canExportAllInsuredPerson: false
    };
  },
  computed: {
    hsId() {
      return this.formValues.data_id;
    },
    documentNo() {
      return this.formValues?.document_no;
    },
    canApproveCond() {
      // If don't have permission to approve
      if (!this.canApprove) return false;

      // If endorsement data is not yet completed
      if (!this.isReinsuranceCompleted || !this.isConfigCompleted) return false;

      // If endorsement is not yet approved and approved_status as submit_status is Submitted
      return (
        this.formValues.status === "PND" &&
        this.formValues.endorsement.approved_status === "SBM"
      );
    },
    canUpdateCond() {
      if (
        this.formValues.status === "PND" &&
        this.formValues.endorsement.approved_status === "PRG"
      )
        return true;
      else if (
        this.formValues.status === "PND" &&
        this.formValues.endormement_type === "GENERAL"
      )
        return true;
      else false;
    },
    isApproved() {
      return this.formValues.status === 'APV'
    },
    canDeleteCond() {
      return this.formValues.status === "PND";
    },
    canPrintInvoice() {
      return this.totalPremium > 0
    },
    canPrintCreditNote() {
      return this.totalPremium < 0
    },
    printLinkLetterHead() {
      return `/hs/endorsement-services/${this.id}/download-endorsement?letterhead=1`
    },
    printLinkNoLetterHead() {
      return `/hs/endorsement-services/${this.id}/download-endorsement?letterhead=0`
    },
    printInvoiceUrlWithoutSignature() {
      return `/hs/endorsement-services/${this.id}/download-invoice?signature=0`
    },
    printInvoiceUrlWithSignature() {
      return `/hs/endorsement-services/${this.id}/download-invoice?signature=1`
    },
    printCreditNoteUrlWithoutSignature() {
      return `/hs/endorsement-services/${this.id}/download-credit-note?signature=0`
    },
    printCreditNoteUrlWithSignature() {
      return `/hs/endorsement-services/${this.id}/download-credit-note?signature=1`
    },
    canExportInsuredPerson() {
      return this.insured_length > 0 || this.formValues?.endorsement_type == 'GENERAL'
    },
  },
  methods: {
    checkCanExportAllInsuredPerson() {
      axios.get(`/hs/endorsement-services/can-export-all-insured-person/${this.id}`).then(response => {
        this.canExportAllInsuredPerson = response.data
      })
    },
    openApproveDialog() {
      this.approveDialog = true;
      this.submitted = false;
    },
    getTotalPremium(total_premium) {
      this.totalPremium = total_premium;
    },
    hideDialog() {
      this.approveDialog = false;
      this.showEndorsementDialog = false;
      this.submitted = false;
    },

    getEndorsement() {
      if (this.id) {
        endorsementService
          .detail(this.id)
          .then((response) => {
            this.formValues = response.data;
            this.insured_length = this.formValues.insured_persons_count
          })
          .then(() => this.isPolicyConfigurationCompleted())
          .then(() => this.isPolicyReinsuranceCompleted());
      }
    },
    isPolicyReinsuranceCompleted() {
      if (this.id)
        axios
          .get(`/hs/policy-services/is-policy-reinsurance-completed/${this.id}`)
          .then((response) => {
            this.isReinsuranceCompleted = response.data;
          });
    },

    isPolicyConfigurationCompleted() {
      if (this.id)
        axios
          .get(
            `/hs/policy-services/is-policy-configuration-completed/${this.id}`
          )
          .then((response) => {
            this.isConfigCompleted = response.data;
          });
    },
    approve(form) {
      this.submitted = true;

      if (form.status && form.reason) {
        endorsementService
          .approve(
            {
              approved_status: form.status,
              approved_reason: form.reason,
            },
            this.id
          )
          .then((response) => {
            if (response.data.success) {
              notify(response.data.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
              this.$router.push({name: "HSEndorsementIndex"});
            }
          })
          .catch((err) => {
            let error = err?.response;
            notify(this.ERROR_MESSAGE, "error", "bottom-right");
            if (error.status === 409) {
              this.$router.push({name: "HSEndorsementIndex"});
            }
          }).finally(() => {
          this.submitted = false
        });
      }
    },
    generateInvoice() {
      axios.post('/hs/endorsement-services/generate-invoice', {
        documentNo: this.formValues.document_no,
        requestType: 'INVOICE'
      }).catch(err => {
        console.log(err);
      })
    },

    generateCreditNote() {
      axios.post('/hs/endorsement-services/generate-credit-note', {
        documentNo: this.formValues.document_no,
        requestType: 'CREDIT_NOTE'
      }).catch(err => {
        console.log(err);
      })
    },
    handleDelete(id) {
      this.$confirm.require({
        message: "Do you want to delete this record?",
        header: "Delete",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios
            .delete(`/hs/endorsements/${id}`)
            .then((response) => {
              if (response.data.success) {
                // refresh table
                notify(response.data.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
                this.$router.push({
                  name: "HSEndorsementIndex",
                });
              }
            })
            .catch((err) => {
              let error = err.response;
              notify(this.ERROR_MESSAGE, "error", "bottom-right");
              if (error.status === 409) {
                this.tabulator.replaceData();
              }
            });
        },
      });
    },
    checkCanGenerateEndorsement() {
      endorsementService.canGenerate(this.id).then((res) => {
        this.canGenerateEndorsement = res.data;
      });
    },
    generateEndorsement(form) {
      this.endorsementDialog.submitting = true;

      endorsementService
        .generate(form, this.id)
        .then((res) => {
          notify( res.data?.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
          this.$router.push({name: "HSEndorsementIndex"});
        })
        .catch((err) => {
          if (err.response?.status === 422) {
            this.endorsementDialog.errors = err.response.data.errors;

            notify(err.response?.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
          } else {
            notify(this.ERROR_MESSAGE, "error", "bottom-right");
          }
        })
        .finally(() => {
          this.endorsementDialog.submitting = false;
        });
    },
    getValidPeriod() {
      endorsementService
        .getValidPeriod(this.id)
        .then((res) => (this.validPeriod = res.data));
    },
    openEndorsementDialog() {
      if (Object.keys(this.endorsementTypes).length === 0) {
        this.listEndorsementTypes();
      }

      if (Object.keys(this.validPeriod).length === 0) {
        this.getValidPeriod();
      }

      this.endorsementDialog.opened = true;
    },

    listEndorsementTypes() {
      endorsementService
        .listEndorsementTypes()
        .then((res) => (this.endorsementTypes = res.data));
    },
    exportInsuredPerson() {
      location.href = '/hs/endorsement-services/' + this.formValues?.data_id + '/export-insured-person/' + this.documentNo
    },
    exportAllInsuredPerson() {
      location.href = `/hs/endorsement-services/${this.formValues?.data_id}/export-all-insured-person/${this.documentNo}`
    }
  },

  mounted() {
    this.getEndorsement();
    this.checkCanGenerateEndorsement()
    this.checkCanExportAllInsuredPerson()
  },
};
</script>
