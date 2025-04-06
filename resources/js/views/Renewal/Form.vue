<template>
  <div>
    <div class="intro-y flex items-center mt-5">
      <h2 class="text-lg font-medium mr-auto">
        Edit Renewal
      </h2>
      <button class="btn btn-primary shadow-md mr-2 leading-6" :disabled="isSubmitting"
        @click="handleSubmit">Submit<span v-if="isSubmitting">...</span></button>
    </div>
    <div class="mt-5">
      <div class="intro-y box">
        <div class="flex items-center px-5 pt-3 pb-0 border-b border-gray-200">
          <div class="nav nav-tabs flex-col sm:flex-row justify-center lg:justify-start" role="tablist">
            <template v-for="(tab, index) in tabs">
              <a
                data-toggle="tab"
                :data-target="tab.target"
                :class="tab.classes"
                @click="changeTab($event, tab)"
              >
                {{ tab.title }}
              </a>
            </template>
          </div>
        </div>
        <div class="p-5">
          <div class="tab-content">
            <div id="info" class="tab-pane active" role="tabpanel">
              <Auto
                v-if="dataId"
                :id="dataId"
                @updateRequireTotalPremiumState="updateRequireTotalPremiumState"
                cancelRoute="RenewalIndex"
              />
            </div>
            <div id="vehicle-info" class="tab-pane" role="tabpanel">
              <Vehicle
                v-if="dataId && isShownVehicleTab"
                :masterDataId="dataId"
                :isQuotation="false"
                :totalPremium="totalPremium"
                :requireUpdateTotalPremium="requireUpdateTotalPremium"
                @updateRequireTotalPremiumState="updateRequireTotalPremiumState"
                @vehicleListUpdated="vehicleListUpdated"
              />
            </div>
            <div id="deductible" class="tab-pane" role="tabpanel">
              <Deductible
                v-if="dataId && isShownDeductibleTab"
                :dataId="dataId"
                cancelRoute="RenewalIndex"
                :key="deductibleTabKey"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>

import Auto from './Info/Auto.vue'
import Vehicle from './Vehicle/VehiclesList.vue'
import Deductible from './Deductible/Deductible.vue'
import renewalService from '@/services/renewal/renewal.service'

export default {
  components: {
    Auto,
    Vehicle,
    Deductible,
  },
  data() {
    return {
      activeTab: 0,
      id: this.$route.params.id ?? null,
      formValues: {
        dataId: null,
      },
      isSubmitting: false,
      totalPremium: null,
      tabs: [
        {
          title: 'Policy Information',
          target: '#info',
          classes: 'py-3 sm:mr-8 cursor-pointer active',
        },
        {
          title: 'Vehicles Information',
          target: '#vehicle-info',
          classes: 'py-3 cursor-pointer sm:mr-8',
        },
        {
          title: 'Deductibles',
          target: '#deductible',
          classes: 'py-3 cursor-pointer sm:mr-8',
        },
      ],
      isShownVehicleTab: false,
      isShownDeductibleTab: false,

      deductibleTabKey: 0,
      requireDeductibleTabRendering: false,
      requireUpdateTotalPremium: false,
    }
  },
  computed: {
    dataId() {
      return this.formValues.dataId
    }
  },
  watch:{
    activeTab(newVal,oldVal){
      if(newVal === 1){
        this.isShownVehicleTab = true
      }else if(newVal === 2){
        this.isShownDeductibleTab = true
      }
    }
  },
  methods: {
    changeTab(event, tab) {
      if (tab.target === '#vehicle-info') {
        this.isShownVehicleTab = true
      } else if (tab.target === '#deductible') {
        this.isShownDeductibleTab = true
        if (this.requireDeductibleTabRendering) {
          this.deductibleTabKey += 1;
          // After re-rendering the deductible tab set requireDeductibleTabRendering to false
          this.setRequireDeductibleTabRenderingStatus(false)
        }
      }
    },
    getRenewal() {
      renewalService.edit(this.id).then(res => {
        this.formValues.dataId = res.data.data_id
      })
        .finally(() => {
          this.getTotalPremium()
        })
    },
    handleSubmit() {
      this.isSubmitting = true
      renewalService.submit(this.id).then(res => {
        if (res.data.success) {
          this.$notify({
            group: 'bottom',
            title: 'Success',
            text: res.data.message,
          }, 4000);

          this.$router.push({ name: 'RenewalIndex' })
        }
      })
        .catch(e => {
          this.$notify({
            group: 'bottom',
            title: 'Error',
            text: err.response.data.message,
          }, 4000);
        })
        .finally(() => this.isSubmitting = false)


    },
    updateRequireTotalPremiumState(isRequired) {
      this.requireUpdateTotalPremium = isRequired
    },
    getTotalPremium() {
      axios.get(`/auto-service/get-total-premium/${this.dataId}`).then(response => this.totalPremium = response.data)
    },
    setRequireDeductibleTabRenderingStatus(status) {
      this.requireDeductibleTabRendering = status
    },
    vehicleListUpdated() {
      this.setRequireDeductibleTabRenderingStatus(true)
    },
  },

  mounted() {
    this.getRenewal()
  },
}
</script>