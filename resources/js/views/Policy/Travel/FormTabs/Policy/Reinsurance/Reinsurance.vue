<template>
  <div>
    <LoadingIndicator v-if="isLoading" />
    <div v-else class="pt-5 pb-2 first:pt-0">
      <ReinsuranceItem
        v-if="id"
        :id="id"
        :dataId="dataId"
        :reinsurance="formValues"
        @generateReinsuranceData="generateReinsuranceData"
        @checkPolicyStatus="$emit('checkPolicyStatus')"
        :participantOptions="participantOptions"
        :partnerGroupOptions="partnerGroupOptions"
        :defaultPartnerGroups="defaultPartnerGroups"
        :canSave="canSave"
        :isEndorsement="isEndorsement"
        :isEndorsementForm="isEndorsementForm"
      />
    </div>
  </div>
</template>

<script>
import ReinsuranceItem from "./ReinsuranceItem.vue";
import LoadingIndicator from "@/components/LoadingIndicator.vue";
import policyServiceService from "@/services/travel/policy/policy_service.service";
import reinsuranceConfigService from "@/services/travel/policy/reinsurance_config.service";
import {dropdownTransform} from "@/mixins/dropdownTransform.js";

export default {
  mixins: [dropdownTransform],
  props: {
    id: [Number, String],
    dataId: [Number, String],
    productCode: String,
    isEndorsement: Boolean,
    isEndorsementForm: Boolean,
    endorsementType: String,
  },

  components: {
    ReinsuranceItem,
    LoadingIndicator,
  },

  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!!",
      SUCCESS_MESSAGE:"Success!",
      formValues: {},
      participantOptions: {},
      isLoading: false,
      partnerGroupOptions: {},
      defaultPartnerGroups: [],
    };
  },

  computed: {
    InsuredPersonEndorsement() {
      return this.endorsementType === "ADD/DELETE";
    },
    canSave() {
      // if not an endorsement
      if (!this.isEndorsement) return true;
      // if endorsement type is ADD/DELETE
      return this.InsuredPersonEndorsement;
    },
  },

  methods: {
    generateReinsuranceShare(policyId) {
      policyServiceService.generateReinsuranceShare(policyId).then((response) => {
        if (response.data.success) this.generateReinsuranceData(policyId);
      });
    },

    generateReinsuranceData(policyId) {
      policyServiceService
        .generateReinsuranceData(policyId)
        .then(() => this.getReinsuranceData());
    },

    getReinsuranceData() {
      if (this.id) {
        this.isLoading = true;
        policyServiceService
          .getReinsuranceData(this.id)
          .then((response) => {
            this.formValues = response.data;
            
          })
          .then(() => {
            this.isLoading = false;
            // If have not yet generated share
            if (this.formValues.length === 0) this.generateReinsuranceShare(this.id);
          })
          .finally(() => {
            this.$emit("checkPolicyStatus");
          });
      }
    },

    listParticipants() {
      policyServiceService.listParticipants().then((response) => {
        this.participantOptions = response.data;
      });
    },

    listPartnerGroups() {
        policyServiceService.listPartnerGroups().then((response) => {
        this.partnerGroupOptions = response.data;
      });
    },

    listDefaultPartnerGroups() {
        reinsuranceConfigService.listDefaultPartnerGroups(this.productCode)
        .then((response) => {
          this.defaultPartnerGroups = response.data;
        });
    },
  },

  mounted() {
    this.getReinsuranceData();
    this.listParticipants();
    this.listPartnerGroups();
    this.listDefaultPartnerGroups();
  },
};
</script>
