<template>
  <div>
    <FormulateForm @submit="handleSubmit">
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Business Channel</span>
        <p class="w-3/4">{{ commissionData.business_category }}</p>
      </div>
      <hr />
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Business Name</span>
        <p class="w-3/4">{{ businessName }}</p>
      </div>
      <hr />
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Gross Written Premium</span>
        <p class="w-3/4">
          {{ commissionData.gross_written_premium   }}
        </p>
      </div>
      <hr />
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Tax & Fee (%)</span>
        <p v-if="!canEditCommissionData" class="w-3/4">
          {{ commissionData.premium_tax_fee_rate }}
        </p>
        <span v-else class="w-1/12">
          <FormulateInput
            v-model="premium_tax_fee_rate"
            type="number"
            min="0"
            validationName="Tax & Fee (%)"
            validation="required|min:0"
            step="any"
            placeholder="Tax & Fee (%)"
          />
        </span>
      </div>
      <hr />
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Tax & Fee Amount</span>
        <p class="w-3/4">{{ commissionData.premium_tax_fee   }}</p>
      </div>
      <hr />
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Net Written Premium</span>
        <p class="w-3/4">{{ commissionData.net_written_premium   }}</p>
      </div>
      <hr />
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Commission Rate (%)</span>
        <p v-if="!canEditCommissionData" class="w-3/4">
          {{ commissionData.commission_rate }}
        </p>
        <span v-else class="w-1/12">
          <FormulateInput
            v-model="commission_rate"
            type="number"
            min="0"
            validationName="Commission Rate (%)"
            validation="required|min:0"
            step="any"
            placeholder="Commission Rate (%)"
          />
        </span>
      </div>
      <hr />
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Commission Amount</span>
        <p class="w-3/4">{{ commissionData.commission_amount   }}</p>
      </div>
      <hr />
      <div class="flex m-2">
        <span class="w-1/4 font-bold">WHT (%)</span>
        <p v-if="!canEditCommissionData" class="w-3/4">
          {{ commissionData.witholding_tax_rate }}
        </p>
        <span v-else class="w-1/12">
          <FormulateInput
            v-model="witholding_tax_rate"
            type="number"
            min="0"
            validationName="WHT (%)"
            validation="required|min:0"
            step="any"
            placeholder="WHT (%)"
          />
        </span>
      </div>
      <hr />
      <div class="flex m-2">
        <span class="w-1/4 font-bold">WHT Amount</span>
        <p class="w-3/4">{{ commissionData.witholding_tax   }}</p>
      </div>
      <hr />
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Commission Due Amount</span>
        <p class="w-3/4">
          {{ commissionData.commission_due_amount   }}
        </p>
      </div>
      <div v-if="canEditCommissionData" class="text-right">
        <router-link
          :to="{ name: 'HSEndorsementIndex' }"
          class="btn btn-outline-secondary w-24 mr-1"
          tag="button"
          >Cancel</router-link
        >
        <button
          type="submit"
          v-if="canSave"
          :disabled="saving"
          class="btn btn-primary w-24"
        >
          <span v-if="!saving">Save</span><span v-else>Saving...</span>
        </button>
        <button
          v-else
          class="btn btn-primary w-24"
          type="button"
          @click="nextTab"
        >
          <span>Next</span>
        </button>
      </div>
    </FormulateForm>
  </div>
</template>

<script>
import policyService from "@/services/hs/policy_service.service";
import hsService from "@/services/hs/hs.service";

export default {
  props: {
    id: [Number, String],
    canSave: {
      type: Boolean,
      default: false,
    },
    businessName: String,
    commission: Object,
    dataId: [Number, String],
  },

  data() {
    return {
      commissionData: this.commission,
      premium_tax_fee_rate: this.commission.premium_tax_fee_rate,
      witholding_tax_rate: this.commission.witholding_tax_rate,
      commission_rate: this.commission.commission_rate,
      saving: false,
    };
  },

  computed: {
    canEditCommissionData() {console.log(this.canSave)
      return this.canSave;
    },
  },

  methods: {
    handleSubmit() {
      this.saving = true;
      policyService
        .updateCommissionData(
          {
            premium_tax_fee_rate: this.premium_tax_fee_rate,
            commission_rate: this.commission_rate,
            witholding_tax_rate: this.witholding_tax_rate,
            detail_id: this.commissionData.detail_id,
          },
          this.id
        )
        .then((response) => {
          this.saving = false;
          if (response.data.success) {
            // reset the form values by getting the new input from users
            notify(response.data.message, "success", "bottom-right");

            // Update issue_date
            this.handleIssueDate();

            // Go to the reinsurance tab
            document.querySelector('.nav-tabs a[href="#reinsurance"]').click();
          }
        })
        .catch((e) => {
          console.log(e);
          notify("Error", "error", "bottom-right");
        })
        .finally(() => {
          this.saving = false;
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
    nextTab() {
      document.querySelector('.nav-tabs a[href="#reinsurance"]').click();
    },
  },
};
</script>

<style scoped>
.input-label {
  display: block;
  line-height: 1.5;
  font-weight: bold;
}
.icon-color {
  color: red;
}
</style>
