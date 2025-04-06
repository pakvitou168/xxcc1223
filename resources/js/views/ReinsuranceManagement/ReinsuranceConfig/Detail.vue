<template>
  <div class="intro-y box p-5 mt-5">
    <div class="intro-y flex mb-4 p-1">
      <h2 class="text-xl font-medium mr-auto">Reinsurance Config Detail</h2>
      <button
        v-if="canDelete"
        class="btn btn-danger mx-1 intro-x"
        @click="handleDelete(id)"
      >
        <svg
          class="w-6 h-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
          ></path>
        </svg>
      </button>
      <router-link
        v-if="canUpdate"
        :to="{ name: 'ReinsuranceConfigUpdate', params: { id: id } }"
        class="btn btn-primary mx-1 intro-x"
      >
        <svg
          class="w-6 h-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
          ></path>
        </svg>
      </router-link>
    </div>
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Product Line</span>
      <p class="text-base text-bold intro-x">{{ formValues.product_line_code }}</p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Product</span>
      <p class="text-base text-bold intro-x">{{productCode }} - {{ productName }}</p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Reinsurance Code</span>
      <p class="text-base text-bold intro-x">{{ reinsuranceCode }}</p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Reinsurance Type</span>
      <p class="text-base text-bold intro-x">{{ reinsuranceType }}</p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Partner Code</span>
      <p class="text-base text-bold intro-x">{{ partnerCode }}</p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Start From</span>
      <p class="text-base text-bold intro-x">
        {{ formValues.start_from }}
      </p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Start To</span>
      <p class="text-base text-bold intro-x">
        {{ formValues.start_to }}
      </p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Leaf</span>
      <p class="text-base text-bold intro-x">
        {{ formValues.leaf == 'Y' ? 'Yes' : 'No' }}
      </p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Level</span>
      <p class="text-base text-bold intro-x">
        {{ formValues.lvl }}
      </p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Parent Code</span>
      <p class="text-base text-bold intro-x">
        {{ parentCode }}
      </p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Share Basis</span>
      <p class="text-base text-bold intro-x">
        {{ formValues.share_basis }}
      </p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Uw Year</span>
      <p class="text-base text-bold intro-x">
        {{ formValues.uw_year }}
      </p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Share %</span>
      <p class="text-base text-bold intro-x">
        {{ formValues.share }}
      </p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Amount Cap</span>
      <p class="text-base text-bold intro-x">
        {{ formValues.amount_cap }}
      </p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Ri. Commission %</span>
      <p class="text-base text-bold intro-x">
        {{ formValues.ri_commission }}
      </p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Tax Fee %</span>
      <p class="text-base text-bold intro-x">
        {{ formValues.tax_fee }}
      </p>
    </div>
    <hr />
    <div class="text-right mt-5">
      <router-link
        :to="{ name: 'ReinsuranceConfigIndex' }"
        class="btn btn-primary w-24 mr-1"
        tag="button"
        >Back</router-link
      >
    </div>
  </div>
</template>

<script>
import UserPermissions from "../../../mixins/UserPermissions";

export default {
  mixins: [UserPermissions],

  data() {
    return {
      id: this.$route.params.id,
      functionCode: "REINSURANCE_CONFIG",
      formValues: {},
      productName: '',
      productCode: '',
      reinsuranceCode: '',
      reinsuranceType: '',
      partnerCode: '',
      parentCode: '',

    };
  },
  methods: {
    getReinsurance() {
      if (this.id) {
        axios.get(`/reinsurance-configs/${this.id}`)
        .then((response) => {
          this.formValues = response.data
          this.productName = this.formValues.product_code.name
          this.productCode = this.formValues.product_code.code
          this.reinsuranceCode = this.formValues.reinsurance_code.name
          this.reinsuranceType = this.formValues.reinsurance_type.name
          this.partnerCode = this.formValues.partner_code.name
          this.parentCode = this.formValues.parent_code ? this.formValues.parent_code.name : ''

          if (response.data?.error) {
            notify(response.data.message, "error",'bottom-right');
          }
        })
      }
    },

    handleDelete(id) {
      this.$confirm.require({
        message: "Do you want to delete this record?",
        header: "Delete",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios
            .delete(`/reinsurance-configs/${id}`)
            .then((response) => {
              if (response.data.success) {
                // refresh table
                notify(response.data.message, "success",'bottom-right');
                this.$router.push({ name: 'ReinsuranceConfigIndex' });
              } else if (response.data?.error) {
                notify(response.data.message, "error",'bottom-right');
                this.tabulator?.replaceData();
              }
            })
            .catch((err) => {
              if (err?.response)
                notify(err.response?.data?.message, "error",'bottom-right');
              else notify("Something wrong...!", "error",'bottom-right');
            });
        },
      });
    },

  },

  mounted() {
    this.getReinsurance();
  },
};
</script>
