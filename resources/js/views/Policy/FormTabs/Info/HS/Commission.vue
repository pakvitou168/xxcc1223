<template>
  <LoadingIndicator v-if="isLoading"/>
  <div v-else class="intro-y box overflow-hidden mt-5 px-10 pb-8">
    <div v-if="commissionData">
      <div class="text-center">
        <div class="pt-10">
          <div
            class="text-theme-1 font-semibold text-3xl text-center uppercase"
          >
            Commission
          </div>
        </div>
      </div>
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Business Channel</span>
        <p class="w-3/4">{{ commissionData.business_category }}</p>
      </div>
      <hr/>
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Business Name</span>
        <p class="w-3/4">{{ businessName }}</p>
      </div>
      <hr/>
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Gross Written Premium</span>
        <p class="w-3/4">
          {{ commissionData.gross_written_premium }}
        </p>
      </div>
      <hr/>
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Tax & Fee (%)</span>
        <p class="w-3/4">
          {{ commissionData.premium_tax_fee_rate }}
        </p>
      </div>
      <hr/>
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Tax & Fee Amount</span>
        <p class="w-3/4">{{ commissionData.premium_tax_fee }}</p>
      </div>
      <hr/>
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Net Written Premium</span>
        <p class="w-3/4">{{ commissionData.net_written_premium }}</p>
      </div>
      <hr/>
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Commission Rate (%)</span>
        <p class="w-3/4">
          {{ commissionData.commission_rate }}
        </p>
      </div>
      <hr/>
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Commission Amount</span>
        <p class="w-3/4">{{ commissionData.commission_amount }}</p>
      </div>
      <hr/>
      <div class="flex m-2">
        <span class="w-1/4 font-bold">WHT (%)</span>
        <p class="w-3/4">
          {{ commissionData.witholding_tax_rate }}
        </p>
      </div>
      <hr/>
      <div class="flex m-2">
        <span class="w-1/4 font-bold">WHT Amount</span>
        <p class="w-3/4">{{ commissionData.witholding_tax }}</p>
      </div>
      <hr/>
      <div class="flex m-2">
        <span class="w-1/4 font-bold">Commission Due Amount</span>
        <p class="w-3/4">
          {{ commissionData.commission_due_amount }}
        </p>
      </div>
    </div>
  </div>
</template>
<script>
import LoadingIndicator from "@/components/LoadingIndicator.vue";

export default {
  props: {
    id: Number,
    dataId: Number,
  },
  components: {
    LoadingIndicator,
  },
  data() {
    return {
      isLoading: false,
      commissionData: {},
      businessName: "",
    };
  },
  methods: {
    getCommissionData() {
      if (this.id) {
        this.isLoading = true;
        axios
          .get(`/hs/policy-services/get-commission-data/${this.id}`)
          .then((response) => {
            this.isLoading = false;
            this.commissionData = response.data;
          })
          .then(() =>
            this.getBusinessNameByBusinessCode(
              this.commissionData.business_code
            )
          );
      }
    },
    getBusinessNameByBusinessCode(businessCode) {
      axios
        .get(
          `/hs/policy-services/get-business-name-by-business-code/${businessCode}`
        )
        .then((response) => {
          this.businessName = response.data;
        });
    },
  },
  mounted() {
    this.getCommissionData();
  },
};
</script>