<template>
  <div class="intro-y box p-5 mt-5">
    <div class="intro-y flex mb-4 p-1">
      <h2 class="text-xl font-medium mr-auto">Reinsurance Partner Detail</h2>
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
        :to="{ name: 'ReinsurancePartnerUpdate', params: { id: id } }"
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
      <span class="text-base w-1/4 intro-x">Code</span>
      <p class="text-base text-bold intro-x">{{ formValues.code }}</p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Name</span>
      <p class="text-base text-bold intro-x">{{ formValues.name }}</p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Group Code</span>
      <p class="text-base text-bold intro-x">{{ formValues.group_code }}</p>
    </div>
    <hr />
    <div class="flex m-2">
      <span class="text-base w-1/4 intro-x">Description</span>
      <p class="text-base text-bold intro-x">
        {{ formValues.description }}
      </p>
    </div>
    <hr />
    <div class="text-right mt-5">
      <router-link
        :to="{ name: 'ReinsurancePartnerIndex' }"
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
      functionCode: "REINSURANCE_PARTNER",
      formValues: {},
    };
  },
  methods: {
    getReinsurance() {
      if (this.id) {
        axios.get(`/reinsurance-partners/${this.id}`).then((response) => {
          this.formValues = response.data;
          if (response.data?.error) {
            notify(response.data.message, "error",'bottom-right');
          }
        });
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
            .delete(`/reinsurance-partners/${id}`)
            .then((response) => {
              if (response.data.success) {
                // refresh table
                notify(response.data.message, "success",'bottom-right');
                this.$router.push({ name: "ReinsurancePartnerIndex" });
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
