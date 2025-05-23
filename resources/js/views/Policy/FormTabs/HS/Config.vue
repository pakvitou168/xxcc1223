<template>
  <div class="grid grid-cols-2 sm:grid-cols-1 gap-10">
    <form @submit.prevent="handleSubmit">
      <div class="grid grid-cols-1 gap-x-10 mb-4">
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

      </div>
      <div class="grid grid-cols-1 gap-x-10 mb-4">

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
      </div>

      <div v-if="canSave" class="text-right mt-5">
        <router-link :to="{name: 'HSPolicyIndex'}">
          <Button type="button" label="Cancel" class="btn btn-danger w-24 mr-2"/>
        </router-link>
        <Button type="submit" label="Save" class="btn btn-primary w-24" :loading="isSaving"/>
      </div>
    </form>
  </div>
</template>

<script>
import policyServiceService from "@/services/hs/policy_service.service";
import policyService from "@/services/hs/policy.service";
import endorsementService from "@/services/hs/endorsement.service";

export default {
  props: {
    id: [Number, String],
    dataId: [Number, String],
    businessType: String,
    policyType: String,
    isEndorsement: Boolean,
    endorsementType: String,
  },

  data() {
    return {
      ERROR_MESSAGE: "Something went wrong!",
      SUCCESS_MESSAGE: "Success!",
      formValues: {
        business_type: null,
        policy_type: null,
      },
      businessTypeOptions: [],
      policyTypeOptions: [],
      errors: {},
      isSaving: false
    }
  },
  computed: {
    generalEndorsement() {
      return this.endorsementType === 'GENERAL'
    },
    canSave() {
      // if not an endorsement
      if (!this.isEndorsement) return true
      // if endorsement type is GENERAL
      return this.generalEndorsement
    }
  },
  methods: {
    transformOptionsToDropdown(data) {
      return Object.entries(data).map(([value, label]) => ({
        value,
        label
      }))
    },
    async listBusinessTypes() {
      policyServiceService.listBusinessTypes()
      await axios.get('/hs/policy-services/list-business-types').then(response => {
        this.businessTypeOptions = this.transformOptionsToDropdown(response.data)
      })
    },
    async listPolicyTypes() {
      await policyServiceService.listPolicyTypes().then(response => {
        this.policyTypeOptions = this.transformOptionsToDropdown(response.data)
      })
    },
    handleSubmit() {
      if (!this.isEndorsement)
        this.handleSubmitPolicy()
      else
        // For update policy configuration in endorsement
        this.handleSubmitEndorsement()
    },
    async handleSubmitPolicy() {
      await policyService.update(this.formValues, this.id).then(response => {
        if (response.data.success) {
          notify(response.data.message || this.SUCCESS_MESSAGE, "success","bottom-right")
          // Update issue_date
          this.handleIssueDate()
          // Go to the commission tab
          document.querySelector('.nav-tabs a[href="#commission"]').click()
        }
      }).finally(() => this.$emit('checkPolicyStatus'))
        .catch((err) => {
          notify(err.response?.data?.message || this.ERROR_MESSAGE , "error", "bottom-right");
        })
    },
    handleSubmitEndorsement() {
      // If not general endorsement cannot be submitted
      if (!this.canSave) return;
      endorsementService.update(this.formValues, this.id).then(response => {
        if (response.data.success) {
          notify(response.data.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
          // Go to the commission tab
          document.querySelector('.nav-tabs a[href="#commission"]').click()
        }
      }).catch((err) => {
        notify(err.response?.data?.message || this.ERROR_MESSAGE , "error", "bottom-right");
      })
    },

    handleIssueDate() {
      axios.put('/hs/update-issue-date/' + this.dataId).catch((e) => {
        console.log(e)
      })
    },
    getData() {
      if (!this.isEndorsement)
        this.getDataPolicy()
      else
        this.getDataEndorsement()
    },
    getDataPolicy() {
      if (this.id) {
        policyService.detail(this.id).then(response => {
          this.formValues = response.data;
        })
      }
    },
    getDataEndorsement() {
      if (this.id) {
        endorsementService.detail(this.id).then(response => {
          this.formValues = response.data;
        })
      }
    },
  },
  mounted() {
    this.getData()
    this.listBusinessTypes()
    this.listPolicyTypes()
  },
}
</script>

<style scoped>
.p-error {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.p-invalid {
  border-color: #ef4444 !important;
}
</style>
