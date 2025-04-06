<template>
  <div>
    <LoadingIndicator v-if="isLoading" />
    <div v-else class="grid grid-cols-2 sm:grid-cols-1 gap-10">
      <FormulateForm v-model="formValues" @submit="handleSubmit">
        <FormulateInput type="textarea" name="endorsement_description" label="Endorsement Descriptions" rows="4"
          class="mb-5" />
        <div class="grid sm:grid-cols-3 gap-5">
          <div class="flex my-2">
            <span class="input-label w-1/3">Premium</span>
            <p class="text-sm w-2/3">{{ endorsement.total_premium   }}</p>
          </div>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-1 gap-x-5">
          <FormulateInput type="date" name="endorsement_e_date" label="Endors. Effective Date" :min="today" />
          <FormulateInput type="select" name="refund_option" label="Refund Options" placeholder="Refund Options"
            :options="refundTypeOptions" :value="defaultSelectedRefundType" />
          <FormulateInput type="number" name="custom_refund_amount" :label="isCustomAmountDisabled ? 'Custom Refund Amount' : 'Custom Refund Amount *'
            " placeholder="Refund Options" step="any" min="0" :max="endorsement.total_premium"
            validationName="Custom Refund Amount"
            :validation="isCustomAmountDisabled ? 'optional|min:0' : 'required|min:0'"
            :disabled="isCustomAmountDisabled" />
        </div>

        <div class="text-right">
          <router-link :to="{ name: 'HSEndorsementIndex' }" class="btn btn-outline-secondary w-24 mr-1"
            tag="button">Cancel</router-link>
          <button v-if="!isAllCanceled" type="submit" class="btn btn-primary w-24">Save</button>
        </div>
      </FormulateForm>
    </div>
  </div>
</template>

<script>
import LoadingIndicator from "@/components/LoadingIndicator.vue";
import EndorsementServiceService from "@/services/hs/endorsement_service.service.js";
import EndorsementService from "@/services/hs/endorsement.service.js";
import hsService from "@/services/hs/hs.service.js";
import moment from "moment";

export default {
  props: {
    id: [Number, String],
    dataId: [Number, String],
    endorsement: Object,
  },

  components: {
    LoadingIndicator,
  },

  data() {
    return {
      formValues: {
        endorsement_description: "",
        endorsement_e_date: null,
        custom_refund_amount: null,
      },
      refundTypeOptions: [],
      isLoading: false,
    };
  },

  computed: {
    today() {
      var today = new Date();
      var date = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate();
      return this.endorsement?.hs ? moment(this.endorsement.hs.effective_date_from).format('YYYY-MM-DD').toString() : date;
    },
    defaultSelectedRefundType() {
      if (this.formValues.refund_option) return this.formValues.refund_option;
      const defaultRefundType = this.refundTypeOptions.some((item) => item['value'] === "DEFAULT");
      if (defaultRefundType) return this.refundTypeOptions.find((item) => item.value === "DEFAULT")?.value;
      return this.refundTypeOptions.find((item) => item.value === "NO_REFUND")?.value;
    },
    isCustomAmountDisabled() {
      return (this.formValues.refund_option !== "CUSTOM" && this.formValues.refund_option !== "SPECIAL_REFUND");
    },
    isAllCanceled() {
      return this.endorsement.is_all_cancelled === 'Y';
    }
  },
  methods: {
    handleSubmit() {
      EndorsementService.saveCancellation(this.id, this.formValues)
        .then((response) => {
          if (response.data.success) {
            this.toastMessage(response.data.message, "Success");
            // Update issue_date
            this.handleIssueDate();
            this.$emit("updateSubmitStatus", "SBM");
          }
        })
        .catch(() => this.toastMessage("Error", "Error"));
    },

    listRefundTypeOptions() {
      EndorsementServiceService.listRefundTypeOptions(this.id).then((response) => {
        this.refundTypeOptions = response.data;
      });
    },

    handleIssueDate() {
      hsService.updateIssueDate(this.dataId).catch((e) => {
        console.log(e);
      });
    },

    toastMessage(msg, type, position = "bottom") {
      notify(msg, type,'bottom-right');
    },
  },

  mounted() {
    if (this.endorsement) {
      console.log(this.endorsement.hs)
      this.formValues.endorsement_description = this.endorsement.endorsement_description
      this.formValues.endorsement_e_date = this.endorsement.endorsement_e_date
      this.formValues.custom_refund_amount = this.endorsement.custom_refund_amount
      this.formValues.refund_option = this.endorsement.refund_option
    }
    this.listRefundTypeOptions();
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
</style>
