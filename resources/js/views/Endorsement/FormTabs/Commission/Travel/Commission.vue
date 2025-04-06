<template>
  <div>
    <LoadingIndicator v-if="isLoading" />
    <div v-else class="pt-5 pb-2 border-b border-gray-300 first:pt-0 last:border-b-0">
      <CommissionItem
        v-if="formValues"
        :id="id"
        :commission="formValues"
        :dataId="dataId"
        :canSave="editable"
        @triggerCommissionInputChange="getCommissionData"
        :businessName="businessName"
      />
    </div>
  </div>
</template>

<script>
import LoadingIndicator from "../../../../../components/LoadingIndicator.vue";
import CommissionItem from "./CommissionItem.vue";
import policyService from "@/services/travel/policy/policy_service.service";
import endorsementService from "@/services/travel/policy/endorsement_service.service";

export default {
  props: {
    id: [Number, String],
    dataId: [Number, String],
    cancelRoute: String,
    endorsement: Object,
  },

  components: {
    LoadingIndicator,
    CommissionItem,
  },

  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!",
      SUCCESS_MESSAGE:"Success!",
      formValues: {},
      businessName: "",
      isLoading: false,
    };
  },
  computed: {
    editable() {
      return (
        !["GENERAL", "ADD/DELETE"].includes(
          this.endorsement.endorsement_type
        ) && this.endorsement.status === "PND"
      );
    },
  },
  methods: {
    checkCommissionDataAvailability() {
      this.isLoading = true;
      endorsementService
        .isCommissionDataAvailable(this.id)
        .then((response) => {
          if (response.data) {
            this.getCommissionData();
          } else if (this.endorsement.endorsement_type !== "ADD/DELETE") {
            this.generateCommissionData();
          }
        })
        .catch((err) => console.log(err))
        .finally(() => (this.isLoading = false));
    },
    generateCommissionData() {
      policyService
        .generateCommissionData(this.id)
        .then(() => this.getCommissionData())
        .catch((err) => console.log(err));
    },

    getCommissionData() {
      if (this.id) {
        this.isLoading = true;
        policyService
          .getCommissionData(this.id)
          .then((response) => {
            this.isLoading = false;
            this.formValues = response.data;
          })
          .then(() =>
            this.getBusinessNameByBusinessCode(this.formValues.business_code)
          );
      }
    },

    getBusinessNameByBusinessCode(businessCode) {
      policyService
        .getBusinessNameByBusinessCode(businessCode)
        .then((response) => {
          this.businessName = response.data;
        });
    },

    toastMessage(msg, type, position = "bottom") {
      notify(msg, type,'bottom-right');
    },
  },

  mounted() {
    this.checkCommissionDataAvailability();
  },
};
</script>
