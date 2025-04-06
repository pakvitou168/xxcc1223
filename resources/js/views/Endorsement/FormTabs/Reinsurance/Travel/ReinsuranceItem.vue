<template>
  <div>
    <FormulateForm @submit="handleSubmit" :errors="errors">
      <div class="w-full overflow-x-auto">
        <div style="min-width: 1500px">
          <ReinsuranceItemHeader />
          <div class="w-full overflow-x-auto">
            <div style="min-width: 1500px">
<!--              <FormulateInput-->
<!--                v-model="reinsurance"-->
<!--                type="group"-->
<!--                :repeatable="false"-->
<!--                group-repeatable-class="formulate-input-group-repeatable relative p-4 odd:bg-gray-100"-->
<!--                element-class="rounded border-gray"-->
<!--                outer-class="formulate-input mb-0"-->
<!--                #default="{ index }"-->
<!--              >-->
<!--                <ReinsuranceItemForm-->
<!--                  v-if="reinsurance && reinsurance[index]"-->
<!--                  :reinsuranceDetail="reinsurance[index]"-->
<!--                  :participantOptions="participantOptions"-->
<!--                  :defaultPartnerGroups="defaultPartnerGroups"-->
<!--                  :partnerGroupOptions="partnerGroupOptions"-->
<!--                  @getReinsurancePendingDeleteId="getReinsurancePendingDeleteId"-->
<!--                  :editable="false"-->
<!--                  :readOnly="!canSave"-->
<!--                />-->
<!--              </FormulateInput>-->
            </div>
          </div>

          <ReinsuranceItemFooter v-if="total" :total="total" />
        </div>
      </div>

      <div v-if="canAddReinsuranceData">
        <FormulateInput
          v-model="formValues.reinsurances"
          type="group"
          :repeatable="true"
          minimum="0"
          add-label="Add Share"
          group-repeatable-class="formulate-input-group-repeatable py-5"
          element-class="formulate-input-element formulate-input-element--group formulate-input-group no-border-top-and-radius"
        >
          <ReinsuranceItemForm
            :reinsuranceDetail="{}"
            :participantOptions="participantOptions"
            :partnerGroupOptions="partnerGroupOptions"
            :editable="true"
          />
        </FormulateInput>
      </div>
      <div class="text-right mt-2 mb-5">
        <router-link
          :to="{ name: 'TravelEndorsementIndex' }"
          class="btn btn-outline-secondary w-24 mr-1"
          tag="button"
          >Cancel</router-link
        >
        <button type="submit" v-if="canSave" class="btn btn-primary w-24">
          Save
        </button>
      </div>
    </FormulateForm>
  </div>
</template>
  
  <script>
import ReinsuranceItemForm from "./ReinsuranceItemForm.vue";
import ReinsuranceItemHeader from "./ReinsuranceItemHeader.vue";
import ReinsuranceItemFooter from "./ReinsuranceItemFooter.vue";

export default {
  props: {
    id: [Number, String],
    dataId: [Number, String],
    reinsurance: {},
    participantOptions: Array,
    partnerGroupOptions: Object,
    defaultPartnerGroups: Array,
    canSave: {
      type: Boolean,
      default: false,
    },

    endorsement: { type: Object, default: {} },
  },

  components: {
    ReinsuranceItemForm,
    ReinsuranceItemHeader,
    ReinsuranceItemFooter,
  },

  data() {
    return {
      formValues: {
        policy_id: this.id,
        reinsurances: [],
      },
      isUnderLimit: false,
      errors: {},
      total: {},
      deletedReinsuranceIdList: [],
    };
  },

  computed: {
    canAddReinsuranceData() {
      return this.canSave && this.isUnderLimit;
    },
    canUpdateReinsuranceData() {
      return this.canSave;
    },
  },

  methods: {
    handleSubmit() {
      this.formValues.existedReinsurances = this.reinsurance;
      this.formValues.deletedReinsuranceIdList = this.deletedReinsuranceIdList;
      axios
        .post("/hs/reinsurance-data", this.formValues)
        .then((response) => {
          if (response.data.success) {
            this.toastMessage(response.data.message, "Success");
            // Update issue_date
            this.handleIssueDate();
          }
        })
        .then(() => this.checkIfShareUnderLimit(this.id))
        .then(() =>
          this.$emit("generateReinsuranceData", this.formValues.policy_id)
        )
        .then(() => {
          // reset form
          this.formValues.reinsurances = [];
          this.errors = {};
        })
        .then(() => {
          this.getSum(this.id);
        })
        .finally(() => this.$emit("checkPolicyStatus"))
        .catch((err) => {
          if (err.response.status === 422)
            this.errors = err.response.data.errors;
          else if (err.response.status === 400)
            this.toastMessage(err.response?.data?.message, "Error");
          else this.toastMessage("Error", "Error");
        });
    },

    checkIfShareUnderLimit(policyId) {
      axios
        .get(`/hs/policy-services/check-if-share-under-limit/${policyId}`)
        .then((response) => {
          this.isUnderLimit = response.data.isUnderLimit;
        });
    },

    getSum(policyId) {
      axios
        .get(`/hs/reinsurance-data/${policyId}/get-sum`)
        .then((response) => (this.total = response.data));
    },

    handleIssueDate() {
      axios
        .put("/hs/update-issue-date/" + this.reinsurance[0].data_id)
        .catch((e) => {
          console.log(e);
        });
    },

    getReinsurancePendingDeleteId(reinsuranceId) {
      this.deletedReinsuranceIdList.push(reinsuranceId);
    },

    toastMessage(msg, type, position = "bottom") {
      notify(msg, type,'bottom-right');
    },
  },
  watch: {
    participantOptions(newValue) {
      console.log(newValue);
    },
  },
  async mounted() {
    this.checkIfShareUnderLimit(this.id);
    this.getSum(this.id);
    await this.$nextTick();
    this.formValues.reinsurances = [];
  },
};
</script>
  
  <style scoped>
.collapse {
  display: none;
}
.rotate {
  transform: rotate(180deg);
}
.icon-color {
  color: red;
}
button:disabled {
  opacity: 0.6;
}
</style>
  