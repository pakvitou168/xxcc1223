<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        H & S Policy : {{ formValues.document_no }}
      </h2>
      <button class="btn btn-success shadow-md mr-2" title="Export Template" v-if="enableImport"
              @click="exportEndorsementExcel">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5"/>
        </svg>
      </button>
      <button class="btn btn-primary shadow-md mr-2" :title="isDisabledSubmitButton
          ? 'Check required data before import'
          : 'Import Add/Delete'
        " v-if="enableImport" @click="() =>
            enableImportButton()

          ">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 8l-5-5-5 5M12 4.2v10.3"/>
        </svg>
      </button>
      <button class="btn btn-primary shadow-md mr-2 leading-6"
              :class="{ 'opacity-50 cursor-not-allowed': isDisabledSubmitButton }" :disabled="isDisabledSubmitButton"
              @click="updateSubmitStatus('SBM')">
        Submit
      </button>
    </div>
    <div class="grid grid-cols-12 mt-5">
      <div class="intro-y box col-span-12">
        <div class="flex items-center px-5 pt-3 pb-0 border-b border-gray-200">
          <div class="nav nav-tabs flex-col sm:flex-row justify-center lg:justify-start" role="tablist">
            <a v-for="(tab, index) in tabs" :key="index" :id="tab.id" data-toggle="tab" :data-target="tab.target"
               :href="tab.href" :class="tab.classes" role="tab" @click="changeTab($event, tab)">
              {{ tab.title }}
            </a>
            <a v-if="hasPolicyCancellation" id="cancellation-tab" data-toggle="tab" data-target="#cancellation"
               href="#cancellation" class="py-3 sm:mr-8" role="tab" aria-selected="false">Policy Cancellation</a>
            <a v-if="!hasPolicyCancellation" id="endorsementdes-tab" data-toggle="tab" data-target="#endorsementdes"
               href="#endorsementdes" class="py-3 sm:mr-8" role="tab" aria-selected="false">Endorsement Desc.</a>
          </div>
        </div>
        <div class="p-5">
          <div class="tab-content">
            <div id="info" class="tab-pane active" role="tabpanel" aria-labelledby="info-tab">
              <InfoTab v-if="formValues.data_id" :id="id" :dataId="dataId" :endorsement="formValues"
                       @checkPolicyStatus="isPolicyConfigurationCompleted" @updateGeneralInfo="isUpdateGeneralInfo"/>
            </div>
            <div id="config" class="tab-pane" role="tabpanel" aria-labelledby="config-tab">
              <ConfigTab v-if="id && isShownConfigTab" :id="id" :dataId="dataId" :endorsement="formValues"
                         :businessType="formValues.endorsement.business_type"
                         :policyType="formValues.endorsement.policy_type"
                         @checkPolicyStatus="isPolicyConfigurationCompleted"/>
            </div>
            <div id="commission" class="tab-pane" role="tabpanel" aria-labelledby="commission-tab">
              <!-- <CommissionTab
                v-if="id && isShownCommissionTab"
                :id="id"
                :key="commissionTabKey"
                :endorsement="formValues"
                :dataId="dataId"
              /> -->
            </div>
            <div id="reinsurance" class="tab-pane" role="tabpanel" aria-labelledby="reinsurance-tab">
              <!-- <ReinsuranceTab
                v-if="id && isShownReinsuranceTab"
                :id="id"
                :dataId="dataId"
                :productCode="formValues.product_code"
                :key="reinsuranceTabKey"
                :endorsement="formValues"
                @checkPolicyStatus="isPolicyReinsuranceCompleted"
              /> -->
            </div>
            <div v-if="hasPolicyCancellation" id="cancellation" class="tab-pane" role="tabpanel"
                 aria-labelledby="cancellation-tab">
              <CancellationTab v-if="id" :id="id" :dataId="dataId" :endorsement="formValues"
                               @updateSubmitStatus="updateSubmitStatus"/>
            </div>

            <div v-if="!hasPolicyCancellation" id="endorsementdes" class="tab-pane" role="tabpanel"
                 aria-labelledby="endorsementdes-tab">
              <DescTab v-if="id" :id="id" :dataId="dataId" :desc="formValues.endorsement_description"/>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ImportDialog
      v-if="showImportDialog"
      :visible="showImportDialog"
      :submitting="importing"
      @hide="hideImportDialog"
      @confirm="importEndorsement"
      @noFileAttached="handleNoFileAttached"/>
  </div>
</template>

<script>
import ConfigTab from "../FormTabs/Info/HS/Config.vue";
import InfoTab from "../FormTabs/Info/HS/Form.vue";
import DescTab from "../FormTabs/Info/HS/Desc.vue";
import CommissionTab from "../FormTabs/Commission/HS/Commission.vue";
import ReinsuranceTab from "../FormTabs/Reinsurance/HS/Reinsurance.vue";
import CancellationTab from "../FormTabs/Cancellation/HS/Cancellation.vue";
import ImportDialog from "./ImportDialog.vue";
import endorsementService from "@/services/hs/endorsement.service.js";
import endorsementServiceService from "@/services/hs/endorsement_service.service";

export default {
  components: {
    InfoTab,
    ConfigTab,
    CommissionTab,
    ReinsuranceTab,
    CancellationTab,
    ImportDialog,
    DescTab
  },

  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!",
      SUCCESS_MESSAGE:"Success!",
      id: this.$route.params.id ?? null,
      formValues: {},
      isReinsuranceCompleted: false,
      isConfigCompleted: false,
      isDisabledSubmitButton: true,
      totalPremium: null,
      showImportDialog: false,
      importing: false,
      hasPolicyCancellation: false,
      hasSubmitBtn: false,
      tabs: [
        {
          id: "info-tab",
          title: "Endorsement Information",
          target: "#info",
          classes: "py-3 sm:mr-8 active",
          href: "javascript:;",
        },
        {
          id: "config-tab",
          title: "Configuration",
          target: "#config",
          classes: "py-3 sm:mr-8",
          href: "#config",
        },
        {
          id: "commission-tab",
          title: "Commission",
          target: "#commission",
          classes: "py-3 sm:mr-8",
          href: "#commission",
        },
        {
          id: "reinsurance-tab",
          title: "Reinsurance",
          target: "#reinsurance",
          classes: "py-3 sm:mr-8",
          href: "#reinsurance",
        },
      ],

      isShownInfoTab: false,
      isShownConfigTab: false,
      isShownCommissionTab: false,
      isShownReinsuranceTab: false,

      // To handle commission and reinsurance tabs after adding/editing/deleting vehicles
      commissionTabKey: 0,
      reinsuranceTabKey: 0,
      requireCommissionTabRendering: false,
      requireReinsuranceTabRendering: false,

      requireUpdateTotalPremium: false,
    };
  },

  computed: {
    dataId() {
      return this.formValues.data_id;
    },
    enableImport() {
      return (
        ['ADD/DELETE', 'GENERAL'].includes(this.formValues.endorsement_type) &&
        this.formValues.endorsement.approved_status === "PRG" &&
        this.formValues.status === "PND"
      );
    },
    endorsementType() {
      return this.formValues.endorsement_type;
    },
  },

  methods: {
    handleNoFileAttached() {
      notify(this.ERROR_MESSAGE, "error", "bottom-right");
    },
    getEndorsement() {
      if (this.id) {
        axios
          .get(`/hs/endorsements/${this.id}`)
          .then((response) => {
            this.formValues = response.data;
            if (this.formValues.endorsement?.approved_status !== "PRG" &&
              ["ADD/DELETE", "GENERAL"].includes(this.endorsementType)) {
              notify(response.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
              this.$router.push({
                name: "HSEndorsementIndex"
              });
            }
          })
          .then(() => this.showPolicyCancellationTab());
      }
    },

    exportEndorsementExcel() {
      location.href = "/hs/endorsements/" + this.id + "/export-template";
    },

    isPolicyReinsuranceCompleted() {
      if (this.id)
        axios
          .get(`/hs/policy-services/is-policy-reinsurance-completed/${this.id}`)
          .then((response) => {
            this.isReinsuranceCompleted = response.data;
            // Check if Policy Configuration is completed
            if (this.isReinsuranceCompleted) {
              axios
                .get(
                  `/hs/policy-services/is-policy-configuration-completed/${this.id}`
                )
                .then((response) => {
                  this.isConfigCompleted = response.data;
                  // If both Policy Configuration & Policy Reinsurance are completed, allow changing status to submitted
                  if (this.isConfigCompleted)
                    this.isDisabledSubmitButton = false;
                  else {
                    this.isDisabledSubmitButton = true;
                    this.updateSubmitStatus("PRG");
                  }
                });
            } else {
              this.isDisabledSubmitButton = true;
              this.updateSubmitStatus("PRG");
            }
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
            // Check if Policy Reinsurance is completed
            if (this.isConfigCompleted) {
              axios
                .get(
                  `/hs/policy-services/is-policy-reinsurance-completed/${this.id}`
                )
                .then((response) => {
                  this.isReinsuranceCompleted = response.data;
                  // If both Policy Configuration & Policy Reinsurance are completed, allow changing status to submitted
                  if (this.isReinsuranceCompleted)
                    this.isDisabledSubmitButton = false;
                  else {
                    this.isDisabledSubmitButton = true;
                    this.updateSubmitStatus("PRG");
                  }
                });
            } else {
              this.isDisabledSubmitButton = true;
              this.updateSubmitStatus("PRG");
            }
          });
    },

    isUpdateGeneralInfo() {
      this.setRequireCommissionTabRenderingStatus(true)
    },
    setRequireCommissionTabRenderingStatus(status) {
      this.requireCommissionTabRendering = status
    },

    updateSubmitStatus(status) {
      axios
        .put(`/hs/policy-services/update-submit-status/${this.id}`, {
          status: status,
        })
        .then((response) => {
          if (response.data)
            notify(response.data.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
          if (status == "SBM")
            this.$router.push({name: "HSEndorsementIndex"});
        })
        .catch((err) => {
          console.log(err.response);
        });
    },

    updateRequireTotalPremiumState(isRequired) {
      this.requireUpdateTotalPremium = isRequired;
    },

    // setRequireCommissionTabRenderingStatus(status) {
    //   this.requireCommissionTabRendering = status;
    // },

    setRequireReinsuranceTabRenderingStatus(status) {
      this.requireReinsuranceTabRendering = status;
    },

    defaultTab() {
      if (
        this.hasPolicyCancellation &&
        this.endorsementType == "CANCELLATION"
      ) {
        // Remove active class from info tab
        document.querySelector("#info-tab").classList.remove("active");
        document.querySelector("#info").classList.remove("active");

        document.querySelector("#cancellation-tab").classList.add("active");
        document.querySelector("#cancellation").classList.add("active");
      }
    },

    changeTab(_, tab) {
      if (tab.target === "#config") {
        this.isShownConfigTab = true;
      } else if (tab.target === "#commission") {
        this.isShownCommissionTab = true;
        return
        if (this.requireCommissionTabRendering) {
          this.commissionTabKey += 1;
          // After re-rendering the deductible tab set requireCommissionTabRendering to false
          this.setRequireCommissionTabRenderingStatus(false);
        }
      } else if (tab.target === "#reinsurance") {
        this.isShownReinsuranceTab = true;
        return
        if (this.requireReinsuranceTabRendering) {
          this.reinsuranceTabKey += 1;
          // After re-rendering the deductible tab set requireReinsuranceTabRendering to false
          this.setRequireReinsuranceTabRenderingStatus(false);
        }
      }
    },
    importEndorsement(form) {
      this.importing = true;
      form.append("_method", "PATCH");
      return endorsementService
        .importData(form, this.id)
        .then((res) => {
          let data = res.data;
          if (data.success) {
            this.showImportDialog = false;
            notify(data.message, "success", "bottom-right");
            this.$router.push({name: "HSEndorsementIndex"});
          } else {
            notify(data.message, "error", "bottom-right");
          }
        })
        .catch((error) => {
          let err = error.response;
          notify(this.ERROR_MESSAGE, "error", "bottom-right");
          if (err.status === 409) {
            this.$router.push({
              name: "HSEndorsementIndex",
            });
          }
        })
        .finally(() => (this.importing = false));
    },

    showPolicyCancellationTab() {
      endorsementServiceService
        .showPolicyCancellationTab(this.id)
        .then((response) => {
          this.hasPolicyCancellation = response.data;
          // Policy Cancellation does not require Submit btn
          this.hasSubmitBtn = !this.hasPolicyCancellation;
        })
        .then(() => {
          this.defaultTab();
        });
    },
    enableImportButton() {
      if (this.isDisabledSubmitButton) {
        notify("Confirm all tabs before import", "error", "bottom-right")
        return false
      }
      this.showImportDialog = true;
    },
    updateDesc(value) {

    }
  },

  mounted() {
    this.getEndorsement();
    this.isPolicyReinsuranceCompleted();
    this.isPolicyConfigurationCompleted();
  },
};
</script>
