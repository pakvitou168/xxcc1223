<template>
  <div class="intro-y box overflow-hidden mt-5 px-10 pb-8">
    <div class="text-center">
      <div class="pt-10">
        <div class="text-theme-1 font-semibold text-3xl text-center uppercase">
          Reinsurance
        </div>
      </div>
    </div>
    <div class="table w-full mt-6">
      <div class="table-header-group">
        <div class="table-row font-bold">
          <div class="table-cell py-4 border-b border-gray-300">Reinsurance Type</div>
          <div class="table-cell py-4 border-b border-gray-300">Participants</div>
          <div class="table-cell py-4 border-b border-gray-300 text-right">Share (%)</div>
          <div class="table-cell py-4 border-b border-gray-300 text-right">Premium (USD)</div>
          <div class="table-cell py-4 border-b border-gray-300 text-right">RI Commission (%)</div>
          <div class="table-cell py-4 border-b border-gray-300 text-right">RI Commission (USD)</div>
          <div class="table-cell py-4 border-b border-gray-300 text-right">Tax & Fees (%)</div>
          <div class="table-cell py-4 border-b border-gray-300 text-right">Tax & Fees (USD)</div>
          <div class="table-cell py-4 border-b border-gray-300 text-right">Net Premium (USD)</div>
        </div>
      </div>
      <div class="table-row-group">
        <div
          class="table-row"
          v-for="(row, index) in reInsurance"
          :key="'row' + index"
        >
          <div class="table-cell py-4 border-b border-gray-300">
            {{ row.reinsurance_type }}
          </div>
          <div class="table-cell py-4 border-b border-gray-300">
            {{ row.participant }}
          </div>
          <div class="table-cell py-4 text-right border-b border-gray-300">
            {{ formatCurrency(row.share) }}
          </div>
          <div class="table-cell py-4 text-right border-b border-gray-300">
            {{ formatCurrency(row.premium) }}
          </div>
          <div class="table-cell py-4 text-right border-b border-gray-300">
            {{ formatCurrency(row.ri_commission) }}
          </div>
          <div class="table-cell py-4 text-right border-b border-gray-300">
            {{ formatCurrency(row.ri_commission_amt) }}
          </div>
          <div class="table-cell py-4 text-right border-b border-gray-300">
            {{ formatCurrency(row.tax_fee) }}
          </div>
          <div class="table-cell py-4 text-right border-b border-gray-300">
            {{ formatCurrency(row.tax_fee_amt) }}
          </div>
          <div class="table-cell py-4 text-right border-b border-gray-300">
            {{ formatCurrency(row.net_premium) }}
          </div>
        </div>
      </div>
      <div class="table-footer-group" v-if="total">
        <div class="table-row font-bold ">
          <div class="table-cell"></div>
          <div class="table-cell py-4 text-right">Total</div>
          <div class="table-cell py-4 text-right">{{ total.share }}</div>
          <div class="table-cell py-4 text-right">{{ total.premium }}</div>
          <div class="table-cell py-4 text-right"></div>
          <div class="table-cell py-4 text-right">{{ total.ri_commission_amt }}</div>
          <div class="table-cell py-4 text-right"></div>
          <div class="table-cell py-4 text-right">{{ total.tax_fee_amt }}</div>
          <div class="table-cell py-4 text-right">{{ total.net_premium }}</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import ReinsuranceItem from "../../HS/Reinsurance/ReinsuranceItem.vue";

export default {
  props: {
    dataId: Number,
    id: [Number, String],
    productCode: String,
  },
  components: {
    ReinsuranceItem,
  },
  data() {
    return {
      reInsurance: {},
      participantOptions: [],
      partnerGroupOptions: {},
      defaultPartnerGroups: [],
      total: {}
    };
  },
  methods: {
    getReinsuranceData() {
      if (this.id) {
        axios
          .get(`/hs/policy-services/get-reinsurance-data/${this.id}`)
          .then((response) => {
            this.reInsurance = response.data;
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
    getSum() {
      axios
        .get(`/hs/reinsurance-data/${this.id}/get-sum`)
        .then((response) => (this.total = response.data));
    },
    formatCurrency(number) {
      if (!number) return "";
      if (typeof number === "string" && !number.includes(",")) {
        number = parseFloat(number);
      }
      return number.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
  },
  mounted() {
    this.getReinsuranceData();
    this.listParticipants();
    this.listPartnerGroups();
    this.listDefaultPartnerGroups();
    this.getSum();
  },
};
</script>