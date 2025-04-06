<template>
  <div >
    <form @submit.prevent="handleSubmit">
      <div class="w-full overflow-x-auto">
        <div style="min-width: 1500px">
          <ReinsuranceItemHeader />
          <div class="w-full overflow-x-auto">
            <div style="min-width: 1500px" v-if="reinsurance && reinsurance.length">
              <div v-for="(item, index) in reinsurance"
                   :key="index"
                   class="relative odd:bg-gray-100 rounded border-gray">
                <ReinsuranceItemForm
                    :reinsuranceDetail="item"
                    :defaultPartnerGroups="defaultPartnerGroups"
                    @getReinsurancePendingDeleteId="getReinsurancePendingDeleteId"
                    :readOnly="readOnly"
                    :errors="errors[index] || {}"
                />
              </div>
            </div>
          </div>
          <ReinsuranceItemFooter v-if="total" :total="total" />
        </div>
      </div>
      <div v-if="canAddReinsuranceData">
        <div v-for="(item, index) in formValues.reinsurances"
             :key="index"
             class="">
          <ReinsuranceItemForm
              :reinsuranceDetail="{}"
              :participantOptions="participantOptions"
              :partnerGroupOptions="partnerGroupOptions"
              :editable="true"
              :readOnly="readOnly"
              :errors="errors[index] || {}"
              @update:value="updateReinsuranceItem(index, $event)"
              :index="index"
              @removeRow="removeReinsuranceItem"
          />
        </div>
        <div class="p-4  border-l border-b border-r border-gray-200">
          <Button
              type="button"
              @click="addReinsuranceItem"
              :disabled="readOnly"
              class="px-2.5 py-1.5 text-sm text-blue-700 border border-blue-700 rounded hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          >
            Add Share
          </Button>
        </div>
      </div>

      <div v-if="canUpdateReinsuranceData" class=" py-4 text-right">
        <Button type="submit" class="w-24 btn btn-primary" severity="primary">Save</Button>
      </div>
    </form>
  </div>
</template>

<script>
import ReinsuranceItemForm from "./ReinsuranceItemForm.vue";
import ReinsuranceItemHeader from "./ReinsuranceItemHeader.vue";
import ReinsuranceItemFooter from "./ReinsuranceItemFooter.vue";
import renewalDataService from "@/services/travel/policy/renewal_data.service";
import policyServiceService from "@/services/travel/policy/policy_service.service";
import travelDetailService from "@/services/travel/policy/travel.service";
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

    isEndorsement: Boolean,
    readOnly: Boolean,
    isEndorsementForm: Boolean,
    generateReinsuranceData:{},
  },

  components: {
    ReinsuranceItemForm,
    ReinsuranceItemHeader,
    ReinsuranceItemFooter,
  },

  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!!",
      SUCCESS_MESSAGE:"Success!",
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
      if (this.isEndorsement) {
        if (this.readOnly) return false;
        else {
          if (this.reinsurance) {
            return (this.reinsurance[0].endorsement_type != "ADD / DELETION" && this.reinsurance[0].endorsement_type != "CANCELLATION");
          }
        }
      } else {
        if (this.readOnly) return false;
        return true;
      }
    },
  },

  methods: {
    handleSubmit() {
      try {
        this.formValues.existedReinsurances = this.reinsurance;
        this.formValues.deletedReinsuranceIdList = this.deletedReinsuranceIdList;

        renewalDataService
            .save(this.formValues)
            .then((response) => {
              if (response.data.success) {
                notify(response.data.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
                this.handleIssueDate();
              }
            })
            .then(() => this.checkIfShareUnderLimit(this.id))
            .then(() => this.$emit("generateReinsuranceData", this.formValues.policy_id))
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
              if (err.response?.status === 422) {
                const apiErrors = err.response.data.errors;
                this.errors = {};

                Object.keys(apiErrors).forEach(key => {
                  const match = key.match(/reinsurances\.(\d+)\.(.+)/);
                  if (match) {
                    const [, index, field] = match;
                    if (!this.errors[index]) {
                      this.errors[index] = {};
                    }
                    this.errors[index][field] = apiErrors[key];
                  }
                });
              } else {
              }
            })
            // .finally(() => {
            //   this.$emit("checkPolicyStatus");
            //   this.checkIfShareUnderLimit(this.id);
            // });
      }catch (e) {
        console.error('errors: ', e)
      }
    },

    checkIfShareUnderLimit(policyId) {
      policyServiceService.checkIfShareUnderLimit(policyId).then((response) => {
        this.isUnderLimit = response.data.isUnderLimit;
      });
    },

    getSum(policyId) {
      renewalDataService
        .getSum(policyId)
        .then((response) => (this.total = response.data));
    },

    handleIssueDate() {
      travelDetailService.updateIssueDate(this.reinsurance[0].data_id).catch((e) => {
        console.log(e);
      });
    },

    getReinsurancePendingDeleteId(reinsuranceId) {
      this.deletedReinsuranceIdList.push(reinsuranceId);
    },

    clearForm() {
      this.formValues.reinsurances = [];
      this.errors = {};
      this.deletedReinsuranceIdList = [];
    },
    addReinsuranceItem() {
      this.formValues.reinsurances.push({
        partner_group: null,
        treaty_code: null,
        share: null,
        ri_commission: null,
        tax_fee: null
      });
    },
    removeReinsuranceItem(index) {
      this.formValues.reinsurances.splice(index, 1);
    },

    updateReinsuranceItem(index, value) {
      // this.$set(this.formValues.reinsurances, index, value);
      this.formValues.reinsurances[index] = { ...value };
    },
  },

  async mounted() {
    // if(this.id) {
    //   console.log('abc:', this.reinsurance);
    // }
    this.checkIfShareUnderLimit(this.id);

    this.getSum(this.id);

    await this.$nextTick();
    this.formValues.reinsurances = [];
    this.clearForm();

    // await this.addReinsuranceItem()

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
