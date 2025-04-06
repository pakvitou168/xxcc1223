<template>
  <div>
    <div
      class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title"
    >
      <h2 class="text-lg font-medium mr-auto">H & S Policy Detail</h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button
          v-if="canGenerateEndorsement"
          class="btn btn-primary shadow-md mr-2"
          title="Generate Endorsement"
          @click="openEndorsementDialog"
        >
          <span class="h-6 leading-6">Generate Endorsement</span>
        </button>
        <button
          v-if="canApproveCond"
          class="btn btn-primary shadow-md mr-2"
          title="Approve Policy"
          @click="openApproveDialog"
        >
          <span class="h-6 leading-6">Policy Approval</span>
        </button>
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
                v-if="canPrintInvoice"
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printInvoiceUrlWithSignature"
                target="_blank"
              >Invoice (Signature)</a
              >
              <a
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printInvoiceUrlWithoutSignature"
                target="_blank"
              >Invoice (No Signature)</a
              >
              <!-- <a
                v-if="canPrintCertificate"
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printCertificateUrl"
                target="_blank"
                >Certificate</a
              > -->

              <a
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithLetterHeadLink"
                target="_blank"
              >Policy Schedule (Letterhead EN)</a
              >
              <a
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadLink"
                target="_blank"
              >Policy Schedule (EN)</a
              >
              <a
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadAndStampLink"
                target="_blank"
              >Policy Signature with No letterhead (EN)</a
              >
              <a
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithLetterHeadLinkKh"
                target="_blank"
              >Policy Schedule (Letterhead KH)</a
              >
              <a
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadLinkKh"
                target="_blank"
              >Policy Schedule (KH)</a
              >
              <a
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadAndStampLinkKh"
                target="_blank"
              >Policy Signature with No letterhead (KH)</a
              >
            </div>
          </div>
        </div>
        <button
          v-if="canDeleteCond"
          class="btn btn-danger mr-2 intro-x"
          title="Delete"
          v-on:click="handleDelete(id)"
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
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
            ></path>
          </svg>
        </button>
      </div>
    </div>
    <div>
      <HSDetail
        v-if="hsId"
        :id="hsId"
        :policyId="id"
        :policyStatus="dataDetail.status"
        :documentNo="documentNo"
      />
      <HSCommission v-if="hsId && id" :id="id" :dataId="hsId"/>
      <HSReInsurance
        v-if="hsId && id"
        :dataId="hsId"
        :id="id"
        :productCode="dataDetail.product_code"
      />
    </div>

    <ApproveDialog
      :isVisible="approveDialog"
      header="Policy Approval"
      :submitted="submitted"
      :options="[{ label: 'Approve', value: 'APV' }, { label: 'Reject', value: 'REJ' }]"
      value="APV"
      @hideDialog="hideDialog"
      @confirm="approve"
    />

    <EndorsementDialog
      header="Generate Endorsement"
      :visible="endorsementDialog.opened"
      :submitting="endorsementDialog.submitting"
      :types="endorsementTypes"
      :validFromDate="validPeriod.from"
      :validToDate="validPeriod.to"
      :typeErrors="endorsementDialog.errors?.type ?? []"
      :effectiveDateErrors="endorsementDialog.errors?.effective_date ?? []"
      @hide="
        () => {
          endorsementDialog.opened = false;
          endorsementDialog.submitting = false;
          endorsementDialog.errors = {};
        }
      "
      @confirm="generateEndorsement"
    />
  </div>
</template>

<script>
import HSDetail from "../../Policy/FormTabs/Info/HS/Detail.vue";
import HSCommission from "../../Policy/FormTabs/Info/HS/Commission.vue";
import HSReInsurance from "../../Policy/FormTabs/Info/HS/ReInsurance.vue";
import UserPermissions from "../../../mixins/UserPermissions";
import ApproveDialog from "./ApproveDialog.vue";
import EndorsementDialog from "@/components/Common/HS/EndorsementDialog.vue";
import endorsementService from "@/services/hs/endorsement.service";
import policyServiceService from "@/services/hs/policy_service.service.js";

export default {
  mixins: [UserPermissions],

  components: {
    HSDetail,
    ApproveDialog,
    EndorsementDialog,
    HSCommission,
    HSReInsurance,
  },

  data() {
    return {
      ERROR_MESSAGE: "Something went wrong!",
      SUCCESS_MESSAGE: "Success!",
      id: this.$route.params.id ? Number(this.$route.params.id) : null,
      dataDetail: {},
      approveDialog: false,
      submitted: false,
      functionCode: "HS_POLICY",
      isReinsuranceCompleted: false,
      isConfigCompleted: false,

      endorsementDialog: {
        opened: false,
        submitting: false,
        errors: {},
      },

      endorsementTypes: {},
      validPeriod: {},

      canGenerateEndorsement: false,
    };
  },

  computed: {
    hsId() {
      return Number(this.dataDetail?.data_id);
    },
    documentNo() {
      return this.dataDetail.document_no;
    },
    printInvoiceUrlWithSignature() {
      return `/hs/policy-services/${this.id}/download-invoice/with-signature`;
    },
    printInvoiceUrlWithoutSignature() {
      return `/hs/policy-services/${this.id}/download-invoice`;
    },
    printCertificateUrl() {
      return `/hs/policy-services/${this.id}/download-hs-certificate`;
    },
    printWithLetterHeadLink() {
      return `/hs/policy-services/${this.id}/download-policy-schedule/en?letterhead=1`;
    },
    printWithoutLetterHeadLink() {
      return `/hs/policy-services/${this.id}/download-policy-schedule/en?letterhead=0`;
    },

    printWithoutLetterHeadAndStampLink() {
      return `/hs/policy-services/${this.id}/download-policy-schedule/en?letterhead=0&noStamp=1`;
    },
    printWithLetterHeadLinkKh() {
      return `/hs/policy-services/${this.id}/download-policy-schedule/km?letterhead=1`;
    },
    printWithoutLetterHeadLinkKh() {
      return `/hs/policy-services/${this.id}/download-policy-schedule/km?letterhead=0`;
    },
    printWithoutLetterHeadAndStampLinkKh() {
      return `/hs/policy-services/${this.id}/download-policy-schedule/km?letterhead=0&noStamp=1`;
    },
    canPrintInvoice() {
      // console.log(this.dataDetail?.status)
      // If policy is approved
      return this.dataDetail?.status === "APV";
    },
    canPrintCertificate() {
      // If policy is approved
      return this.dataDetail.status === "APV";
    },
    canApproveCond() {
      // If don't have permission to approve
      if (!this.canApprove) return false;

      // If policy data is not yet completed
      if (!this.isReinsuranceCompleted || !this.isConfigCompleted) return false;

      // If policy is not yet approved and approved_status as submit_status is Submitted
      return (this.dataDetail.status === "PND" && this.dataDetail.approved_status === "SBM");
    },
    canUpdateCond() {
      // If already approved
      if (this.dataDetail.status !== "PND") return false;

      return this.canUpdate;
    },
    canDeleteCond() {
      // If already approved
      if (this.dataDetail.status === "APV") return false;
      return this.canDelete;
    },
  },

  methods: {
    getPolicy() {
      if (this.id) {
        axios.get(`/hs/policies/${this.id}`).then((response) => {
          this.dataDetail = response.data;
        });
      }
    },

    handleDelete(id) {
      this.$confirm.require({
        message: "Do you want to delete this record?",
        header: "Delete",
        icon: "pi pi-info-circle",
        acceptClass: "btn btn-danger mr-2 intro-x",
        rejectClass: "btn btn-primary",
        blockScroll: false,
        accept: () => {
          axios
            .delete(`/hs/policies/${id}`)
            .then((response) => {
              if (response.data.success) {
                notify(response.data.message || this.SUCCESS_MESSAGE, "success","bottom-right");
                this.$router.push({name: "HSPolicyIndex"});
              }
            })
            .catch((err) => {
              notify(err.response?.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
            });
        },
      });
    },

    openApproveDialog() {
      this.approveDialog = true;
      this.submitted = false;
    },

    hideDialog() {
      this.approveDialog = false;
      this.showEndorsementDialog = false;
      this.submitted = false;
    },

    approve(form) {
      this.submitted = true;

      if (form.status && form.reason) {
        axios
          .post(`/hs/policies/approve/${this.id}`, {
            approved_status: form.status,
            approved_reason: form.reason,
          })
          .then((response) => {
            if (response.data.success) {
              notify(response.data.message || this.SUCCESS_MESSAGE, "success","bottom-right");
              this.$router.push({name: "HSPolicyIndex"});
            }
          })
          .catch((err) => {
            notify(err.response?.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
          });
      }
    },

    generateInvoice() {
      axios
        .post("/hs/policies/generate-hs-invoice", {
          documentNo: this.dataDetail.document_no,
          requestType: "INVOICE",
        })
        .catch((err) => {
          console.log(err);
        });
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

    generateEndorsement(form) {
      this.endorsementDialog.submitting = true;

      endorsementService
        .generate(form, this.id)
        .then((res) => {
          notify(res.data?.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
          this.$router.push({name: "HSEndorsementIndex"});
        })
        .catch((err) => {
          if (err.response?.status === 422) {
            this.endorsementDialog.errors = err.response.data.errors;
            notify(err.response?.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
          } else {
            notify(err.response?.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
          }
        })
        .finally(() => {
          this.endorsementDialog.submitting = false;
        });
    },

    listEndorsementTypes() {
      endorsementService
        .listEndorsementTypes()
        .then((res) => (this.endorsementTypes = res.data));
    },

    getValidPeriod() {
      endorsementService
        .getValidPeriod(this.id)
        .then((res) => (this.validPeriod = res.data));
    },

    checkCanGenerateEndorsement() {
      endorsementService.canGenerate(this.id).then((res) => {
        this.canGenerateEndorsement = res.data;
      });
    },

    getCommissionData() {
      if (this.id)
        axios
          .get(`/hs/policy-service/get-commission-data/${this.id}`)
          .then((response) => {
            this.commissionData = response.data;
          });
    },
    isPolicyReinsuranceCompleted() {
      if (this.id)
        policyServiceService.isPolicyReinsuranceCompleted(this.id).then((response) => {
          this.isReinsuranceCompleted = response.data;
        });
    },

    isPolicyConfigurationCompleted() {
      if (this.id)
        policyServiceService.isPolicyConfigurationCompleted(this.id).then((response) => {
          this.isConfigCompleted = response.data;
        });
    },
  },

  mounted() {
    this.getPolicy();
    this.checkCanGenerateEndorsement();
    this.isPolicyReinsuranceCompleted();
    this.isPolicyConfigurationCompleted();
  },
};
</script>
