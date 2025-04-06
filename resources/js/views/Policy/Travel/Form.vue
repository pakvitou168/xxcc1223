<template>
  <div>
   <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        Travel Policy : {{ formValues?.policy?.document_no }}
      </h2>
<!--      <button class="btn btn-success shadow-md mr-2" title="Export Reinsurance" @click="exportReinsuranceExcel">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
      </button>-->
      <button class="btn btn-primary shadow-md mr-2 leading-6" :class="{'opacity-50 cursor-not-allowed' : isDisabledSubmitButton}" :disabled="isDisabledSubmitButton" @click="updateSubmitStatus('SBM')">Submit</button>
   </div>
    <div class="grid grid-cols-12 mt-5">
      <div class="intro-y box col-span-12">
        <div class="flex items-center px-5 pt-3 pb-0 border-b border-gray-200">
          <div class="nav nav-tabs flex-col sm:flex-row justify-center lg:justify-start" role="tablist">
            <template v-for="(tab, index) in tabs" :key="index" >
              <a data-toggle="tab"
                 :data-target="tab.target"
                 :href="tab.href"
                 :class="tab.classes"
                 role="tab"
                 @click="changeTab($event, tab)"
              >
                {{ tab.title }}
              </a>
            </template>
          </div>
        </div>
        <div class="p-5">
          <div class="tab-content">
            <div id="config" class="tab-pane" :class="{ active: activeTab === 'config' }" role="tabpanel">
              <ConfigTab :id="id" :dataId="dataId"
                         @checkPolicyStatus='isPolicyConfigurationCompleted'/>
            </div>
            <div id="commission" class="tab-pane" :class="{ active: activeTab === 'commission' }" role="tabpanel">
              <CommissionTab v-if="id && isShownCommissionTab" :id="id" :key="commissionTabKey" :dataId="dataId"/>
            </div>
            <div id="reinsurance" class="tab-pane" :class="{ active: activeTab === 'reinsurance' }" role="tabpanel">
              <ReinsuranceTab v-if="id && isShownReinsuranceTab" :id="id" :dataId="dataId" :productCode="formValues.product_code" :key="reinsuranceTabKey" @checkPolicyStatus='isPolicyReinsuranceCompleted'/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ConfigTab from './FormTabs/Policy/Config.vue'
import CommissionTab from './FormTabs/Policy/Commission/Commission.vue'
import ReinsuranceTab from './FormTabs/Policy/Reinsurance/Reinsurance.vue'
import PolicyVerificationService from "@/services/travel/policy/policyVerification.service.js";

export default {
  components: {
    ConfigTab,
    CommissionTab,
    ReinsuranceTab,
  },

  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!!",
      SUCCESS_MESSAGE:"Success!",
      id: this.$route.params.id ?? null,
      data_id_: this.$route.query.data_id ?? null,
      formValues: {},
      isReinsuranceCompleted: false,
      isConfigCompleted: false,
      isDisabledSubmitButton: true,
      totalPremium: null,
      tabs: [
        {
          title: 'Policy Configuration',
          target: '#config',
          classes: 'py-3 sm:mr-8 active',
          href: 'javascript:;',
        },
        {
          title: 'Commission',
          target: '#commission',
          classes: 'py-3 sm:mr-8',
          href: '#commission',
        },
        {
          title: 'Reinsurance',
          target: '#reinsurance',
          classes: 'py-3 sm:mr-8',
          href: '#reinsurance',
        },
      ],

      isShownConfigTab: false,
      isShownCommissionTab: false,
      isShownReinsuranceTab: false,

      // To handle commission and reinsurance tabs after adding/editing/deleting vehicles
      commissionTabKey: 0,
      reinsuranceTabKey: 0,
      requireCommissionTabRendering: false,
      requireReinsuranceTabRendering: false,

      requireUpdateTotalPremium: false,
      activeTab: 'config',
    }
  },

  computed: {
    dataId() {
      return this.data_id_? this.data_id_: this.formValues.data_id
    },
  },
  watch: {
    // Handle hash changes
    '$route.hash'(newHash) {
      this.handleHashChange(newHash)
    }
  },
  methods: {
    getPolicy() {
      if (this.data_id_) {
        axios.get(`/travel/policies/${this.data_id_}`).then(response => {
          this.formValues = response.data;
          if(!this.formValues.data_id) {
            this.formValues.data_id = response.data?.policy?.data_id;
          }
        });
      }
    },

    exportReinsuranceExcel(){
      location.href = '/travel/policies/service/export-reinsurance/' + this.id + '/' + this.formValues.product_code;
    },

    isPolicyReinsuranceCompleted() {
      if (this.id)
        PolicyVerificationService.isPolicyReinsuranceCompleted(this.id).then(response => {
          this.isReinsuranceCompleted = response.data;
          // Check if Policy Configuration is completed
          if(this.isReinsuranceCompleted){
            PolicyVerificationService.isPolicyConfigurationCompleted(this.id).then(response => {
              this.isConfigCompleted = response.data;
              // If both Policy Configuration & Policy Reinsurance are completed, allow changing status to submitted
              if(this.isConfigCompleted)
                this.isDisabledSubmitButton = false
              else{
                this.isDisabledSubmitButton = true
                this.updateSubmitStatus('PRG')
              }
            })
          } else {
            this.isDisabledSubmitButton = true
            this.updateSubmitStatus('PRG')
          }
        })
    },

    isPolicyConfigurationCompleted() {
      if (this.id)
        PolicyVerificationService.isPolicyConfigurationCompleted(this.id).then(response => {
          this.isConfigCompleted = response.data;
          // Check if Policy Reinsurance is completed
          if(this.isConfigCompleted){
            PolicyVerificationService.isPolicyReinsuranceCompleted(this.id).then(response => {
              this.isReinsuranceCompleted = response.data;
              // If both Policy Configuration & Policy Reinsurance are completed, allow changing status to submitted
              if(this.isReinsuranceCompleted)
                this.isDisabledSubmitButton = false
              else{
                this.isDisabledSubmitButton = true
                this.updateSubmitStatus('PRG')
              }
            })
          } else {
            this.isDisabledSubmitButton = true
            this.updateSubmitStatus('PRG')
          }
        })
    },

    updateSubmitStatus(status){

      PolicyVerificationService.updateSubmitStatus(this.id,{status: status}).then(response => {
        if(response.data)
           notify(response.data.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
        if(status == 'SBM')
          this.$router.push({name: 'TravelPolicyIndex'})
      }).catch(err => {
        console.log(err)
      })
    },

    updateRequireTotalPremiumState(isRequired){
      this.requireUpTotalPremium = isRequired
    },

    setRequireCommissionTabRenderingStatus(status){
      this.requireCommissionTabRendering = status
    },

    setRequireReinsuranceTabRenderingStatus(status){
      this.requireReinsuranceTabRendering = status
    },

    changeTab(_, tab) {
      if (tab.target === '#config') {
        this.isShownConfigTab = true
      } else if (tab.target === '#commission') {
        this.isShownCommissionTab = true
        if(this.requireCommissionTabRendering){
          this.commissionTabKey += 1;
          // After re-rendering the deductible tab set requireCommissionTabRendering to false
          this.setRequireCommissionTabRenderingStatus(false)
        }
      } else if (tab.target === '#reinsurance') {
        this.isShownReinsuranceTab = true
        if(this.requireReinsuranceTabRendering){
          this.reinsuranceTabKey += 1;
          // After re-rendering the deductible tab set requireReinsuranceTabRendering to false
          this.setRequireReinsuranceTabRenderingStatus(false)
        }
      }
    },
    handleHashChange(hash) {
      const tabId = hash.replace('#', '') || 'config'
      const tab = this.tabs.find(t => t.target === `#${tabId}`)
      if (tab) {
        this.activateTab(tab)
      }
    },

    activateTab(tab) {
      // Remove active class from all tabs
      this.tabs.forEach(t => {
        t.classes = t.classes.replace(' active', '')
      })

      // Add active class to selected tab
      tab.classes += ' active'
      this.activeTab = tab.target.replace('#', '')

      // Handle tab visibility
      this.changeTab(null, tab)
    },
  },

  mounted() {
    this.getPolicy()
    this.isPolicyReinsuranceCompleted()
    this.isPolicyConfigurationCompleted()

    // Handle initial hash
    this.handleHashChange(window.location.hash)

    // Listen for hash changes
    window.addEventListener('hashchange', () => {
      this.handleHashChange(window.location.hash)
    })
  },
  beforeDestroy() {
    window.removeEventListener('hashchange', () => {
      this.handleHashChange(window.location.hash)
    })
  }
}
</script>
