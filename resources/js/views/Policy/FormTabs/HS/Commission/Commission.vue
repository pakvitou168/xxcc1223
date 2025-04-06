<template>
  <div>
    <LoadingIndicator v-if="isLoading"/>
    <div v-else class="pt-5 pb-2 border-b border-gray-300 first:pt-0 last:border-b-0">
      <CommissionItem v-if="formValues"
                      :id="id"
                      :commission="formValues"
                      :dataId="dataId"
                      :canSave="true"
                      @triggerCommissionInputChange="getCommissionData"
                      :businessName="businessName"/>
    </div>
  </div>
</template>

<script>

import LoadingIndicator from '../../../../../components/LoadingIndicator.vue'
import CommissionItem from './CommissionItem.vue'

export default {
  props: {
    id: [Number, String],
    dataId: [Number, String],
    cancelRoute: String,
  },

  components: {
    LoadingIndicator,
    CommissionItem
  },

  data() {
    return {
      ERROR_MESSAGE: "Something went wrong!",
      SUCCESS_MESSAGE: "Success!",
      formValues: {},
      businessName: '',
      isLoading: false,
    }
  },

  methods: {
    checkCommissionDataAvailability() {
      this.isLoading = true,
        axios.get(`/hs/policy-services/is-commission-data-available/${this.id}`)
          .then(response => {
            if (response.data)
              this.getCommissionData()
            else
              this.generateCommissionData()
          })
          .catch(err => console.log(err))
    },
    generateCommissionData() {
      axios.get(`/hs/policy-services/generate-commission-data/${this.id}`)
        .then(() => this.getCommissionData())
        .catch(err => console.log(err))
    },

    getCommissionData() {
      if (this.id) {
        this.isLoading = true
        axios.get(`/hs/policy-services/get-commission-data/${this.id}`)
          .then(response => {
            this.isLoading = false
            this.formValues = response.data
          })
          .then(() => this.getBusinessNameByBusinessCode(this.formValues.business_code))
      }
    },

    getBusinessNameByBusinessCode(businessCode) {
      axios.get(`/hs/policy-services/get-business-name-by-business-code/${businessCode}`).then(response => {
        this.businessName = response.data
      })
    },
  },

  mounted() {
    this.checkCommissionDataAvailability()
  },
}
</script>
