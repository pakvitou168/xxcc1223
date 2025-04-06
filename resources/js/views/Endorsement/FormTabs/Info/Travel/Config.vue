<template>
  <div class="grid grid-cols-2 sm:grid-cols-1 gap-10">
    <form @submit.prevent="handleSubmit">
      <div class="col-span-6">
        <label class="font-bold block mb-1">Business Type *</label>
        <Dropdown
            v-model="formValues.business_type"
            :options="businessTypeOptions"
            optionLabel="label"
            optionValue="value"
            class="w-full p-inputtext"
            placeholder="Business Type"
            :disabled="!canSave"
            :filter="true"
        />
        <small v-if="errors.business_type" class="p-error block">{{ errors.business_type[0] }}</small>
      </div>

      <div class="col-span-6">
        <label class="font-bold block mb-1">Policy Type *</label>
        <Dropdown
            v-model="formValues.policy_type"
            :options="policyTypeOptions"
            optionLabel="label"
            optionValue="value"
            class="w-full p-inputtext"
            placeholder="Policy Type"
            :disabled="!canSave"
        />
        <small v-if="errors.policy_type" class="p-error block">{{ errors.policy_type[0] }}</small>
      </div>

      <div v-if="canSave" class="text-right mt-5">
        <router-link
          :to="{ name: 'TravelEndorsementIndex' }"
          class="btn btn-outline-secondary w-24 mr-1"
          tag="button"
          >Cancel</router-link
        >
        <button type="submit" class="btn btn-primary w-24" :disabled="saving">
          <span v-if="saving">Saving...</span>
          <span v-else>Save</span>
        </button>
      </div>
     </form>
  </div>
</template>

<script>
import policyServiceService from "@/services/travel/policy/policy_service.service";
import policyService from "@/services/travel/policy/policy.service";
import endorsementService from "@/services/travel/policy/endorsement.service";

export default {
  props: {
    id: [Number, String],
    dataId: [Number, String],
    businessType: String,
    policyType: String,
    endorsement: Object,
  },

  data() {
    return {
      saving: false,
      formValues: {},
      formData: {
        partner_group: null,
        treaty_code: null,
        share: null,
        ri_commission: null,
        tax_fee: null
      },
      businessTypeOptions: [],
      policyTypeOptions: [],
      errors: {},
    };
  },
  computed: {
    generalEndorsement() {
      return this.endorsement.endorsement_type === "GENERAL";
    },
    canSave() {
      // if not an endorsement
      if (this.endorsement) return true;
      // if endorsement type is GENERAL
      return this.generalEndorsement;
    },
  },
  methods: {
    transformOptionsToDropdown(data) {
      return Object.entries(data).map(([value, label]) => ({
        value,
        label
      }))
    },
    listBusinessTypes() {
      policyServiceService.listBusinessTypes();
      axios.get("/hs/policy-services/list-business-types").then((response) => {
        this.businessTypeOptions = this.transformOptionsToDropdown(response.data)
      });
    },
    listPolicyTypes() {
      policyServiceService.listPolicyTypes().then((response) => {
        this.policyTypeOptions =  this.transformOptionsToDropdown(response.data)
      });
    },
    handleSubmit() {
      if (!this.endorsement) this.handleSubmitPolicy();
      // For update policy configuration in endorsement
      else this.handleSubmitEndorsement();
    },
    handleSubmitPolicy() {
      policyService
        .save(this.formValues, this.id)
        .then((response) => {
          if (response.data.success) {
            this.toastMessage(response.data.message, "Success");
            // Update issue_date
            this.handleIssueDate();
            // Go to the commission tab
            document.querySelector('.nav-tabs a[href="#commission"]').click();
          }
        })
        .finally(() => this.$emit("checkPolicyStatus"))
        .catch(() => this.toastMessage("Error", "Error"));
    },
    handleSubmitEndorsement() {
      // If not general endorsement cannot be submitted
      if (!this.canSave) return;
      this.saving = true
      endorsementService
        .config(this.formValues, this.id)
        .then((response) => {
          if (response.data.success) {
            this.toastMessage(response.data.message, "Success");
            // Go to the commission tab
            document.querySelector('.nav-tabs a[href="#commission"]').click();
          }
        })
        .catch(() => this.toastMessage("Error", "Error"))
        .finally(() => this.saving = false)
    },

    handleIssueDate() {
      axios.put("/hs/update-issue-date/" + this.dataId).catch((e) => {
        console.log(e);
      });
    },

    toastMessage(msg, type, position = "bottom") {
      this.$notify(
        {
          group: position,
          title: type,
          text: msg,
        },
        4000
      );
    },
    getData() {
      if (!this.isEndorsement) this.getDataPolicy();
      else this.getDataEndorsement();
    },
    getDataPolicy() {
      if (this.id) {
        policyService.detail(this.id).then((response) => {
          this.formValues = response.data;
        });
      }
    },
    getDataEndorsement() {
      if (this.id) {
        endorsementService.detail(this.id).then((response) => {
          this.formValues = response.data;
        });
      }
    },
  },
  mounted() {
    this.getData();
    this.listBusinessTypes();
    this.listPolicyTypes();
  },
};
</script>
