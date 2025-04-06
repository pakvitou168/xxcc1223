<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">Customer Profile Detail</h2>
    </div>
    <div class="intro-y box overflow-hidden mt-5 w-full px-5 sm:px-16 py-10" style="min-height: 500px;">
      <div class="grid grid-cols-3 gap-x-5">
        <div class="col-span-2">
          <div class="grid grid-cols-5" v-if="formValues.customer_profile">
            <span class="text-base intro-x font-bold">Insured Name</span>
            <p class="text-base col-span-4">
              : {{ formValues.customer_profile?.name_en }}
            </p>
          </div>
          <div class="grid grid-cols-5">
            <span class="text-base intro-x font-bold">Address</span>
            <p class="text-base col-span-4">
              : {{ formValues.customer_profile?.address }}
            </p>
          </div>
        </div>
        <div>
          <Calendar
              v-model="underwriteYear"
              class="w-full leading-6 mb-3"
              view="year"
              dateFormat="yy"
              :manualInput="false"
              @date-select="searchByYear($event.getFullYear())"
          />
        </div>
      </div>
      <div v-if="formValues.customer_profile_summary">
        <div class="grid grid-cols-5 gap-x-5 pt-5">
          <div class="text-base font-bold text-red-500">Total Gross Premium</div>
          <div class="text-base font-bold text-red-500">Total Claim insured</div>
          <div class="text-base font-bold text-red-500">Total claim paid</div>
          <div class="text-base font-bold text-red-500">Total claim out standing</div>
          <div class="text-base font-bold text-red-500">Claim radio</div>
        </div>
        <div class="grid grid-cols-5 gap-x-5 pb-5">
          <div class="text-base">
            {{ formatCurrency(formValues.customer_profile_summary.total_gross_premium) }}
          </div>
          <div class="text-base">
            {{ formatCurrency(formValues.customer_profile_summary.total_claim_insured) }}
          </div>
          <div class="text-base">
            {{ formatCurrency(formValues.customer_profile_summary.total_claim_paid) }}
          </div>
          <div class="text-base">
            {{ formatCurrency(formValues.customer_profile_summary.total_claim_outs) }}
          </div>
          <div class="text-base">
            {{
              formatCurrency(formValues.customer_profile_summary.claim_ratio_formatted)
            }}
          </div>
        </div>
        <hr />
      </div>
      <div v-if="formValues.customerProfileList.length">
        <div class="overflow-x-auto scrollbar-hidden">
          <VueTabulator
              ref="tabulator"
              :options="options"
              class="mt-5 tabulator"
              v-model="formValues.customerProfileList"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";

export default {
  data() {
    return {
      id: this.$route.params.id,
      formValues: { customerProfileList: [] },
      underwriteYear: null,
      filterUnderwritingYear:null,
      options: {
        layout: "fitColumns",
        pagination: false,
        placeholder: "No Data Available",
        headerSortTristate: true,
        debounce: {},
        columns: [
          {
            title: "No.",
            field: "no",
            width: 75,
            headerSort: false,
          },
          {
            title: "Product",
            field: "product",
            headerSort: false,
            width: 180
          },
          {
            title: "Policy No",
            field: "policy_no",
            headerSort: false,
          },
          {
            title: "Inception Date",
            field: "inception_date",
            width: 120,
            headerSort: false,
          },
          {
            title: "Expiry Date",
            field: "expiry_date",
            width: 120,
            headerSort: false,
          },
          {
            title: "Premium",
            field: "premium",
            width: 120,
            headerSort: false,
            mutator: (_, row) => this.formatCurrency(row.premium),
          },
          {
            title: "Claim insured",
            field: "claim_insured",
            width: 120,
            headerSort: false,
            mutator: (_, row) => this.formatCurrency(row.claim_insured),
          },
          {
            title: "Claim Paid",
            field: "claim_paid",
            width: 120,
            headerSort: false,
            mutator: (_, row) => this.formatCurrency(row.claim_paid),
          },
          {
            title: "Claim Out standing",
            field: "claim_outstanding",
            width: 120,
            headerSort: false,
            mutator: (_, row) => this.formatCurrency(row.claim_outstanding),
          },
        ],
      },
    };
  },
  methods: {
    getCustomerProfile() {
      if (this.id) {
        axios
            .get(`/customer-profiles/${this.id}`, {
              params: {
                underwrite_year: this.filterUnderwritingYear,
              },
            })
            .then((response) => {
              this.formValues = response.data;
              this.formValues.customerProfileList = response.data.customer_profile_list;
            });
      }
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
    searchByYear(underwritingYear) {
      this.filterUnderwritingYear = underwritingYear;
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getCustomerProfile();
      }, 500);
    }
  },
  mounted() {
    this.getCustomerProfile();
  },
};
</script>
