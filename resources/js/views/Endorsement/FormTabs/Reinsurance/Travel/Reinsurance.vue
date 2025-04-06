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
        :endorsement="endorsement"
      />
    </div>
  </div>
</template>

<script>
import ReinsuranceItem from "./ReinsuranceItem.vue";
import LoadingIndicator from "@/components/LoadingIndicator.vue";

export default {
  props: {
    id: [Number, String],
    dataId: [Number, String],
    productCode: String,
    endorsement: Object,
  },

  components: {
    ReinsuranceItem,
    LoadingIndicator,
  },

  data() {
    return {
      isLoading: false,
      formValues: {},
      participantOptions: [],
      partnerGroupOptions: {},
      defaultPartnerGroups: [],
    };
  },

  computed: {
    InsuredPersonEndorsement() {
      return !["GENERAL",'ADD/DELETE'].includes(this.endorsement.endorsement_type) && this.endorsement.status === "PND";
    },
    canSave() {
      // if endorsement type is ADD/DELETE
      return this.InsuredPersonEndorsement;
    },
  },

  methods: {
    generateReinsuranceShare(policyId) {
      if (this.endorsement.endorsement_type !== "ADD/DELETE") {
        axios
          .get(`/hs/policy-services/generate-reinsurance-share/${policyId}`)
          .then((response) => {
            if (response.data.success) this.generateReinsuranceData(policyId);
          });
      }
    },

    generateReinsuranceData(policyId) {
      if (this.endorsement.endorsement_type !== "ADD/DELETE") {
        axios
          .get(`/hs/policy-services/generate-reinsurance-data/${policyId}`)
          .then(() => this.getReinsuranceData());
      }
    },

    getReinsuranceData() {
      if (this.id) {
        this.isLoading = true;
        axios
          .get(`/hs/policy-services/get-reinsurance-data/${this.id}`)
          .then((response) => {
            this.formValues = response.data;
          })
          .then(() => {
            this.isLoading = false;
            // If have not yet generated share
            if (this.formValues.length === 0)
              this.generateReinsuranceShare(this.id);
          })
          .finally(() => {
            this.$emit("checkPolicyStatus");
          });
      }
    },

    listParticipants() {
      axios.get("/hs/policy-services/list-treaty-codes").then((response) => {
        this.participantOptions = response.data;
      });
    },

    listPartnerGroups() {
      axios.get("/hs/policy-services/list-partner-groups").then((response) => {
        this.partnerGroupOptions = response.data;
      });
    },

    listDefaultPartnerGroups() {
      axios
        .get(
          `/hs/reinsurance-config/get-default-reinsurance-config/${this.productCode}`
        )
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
