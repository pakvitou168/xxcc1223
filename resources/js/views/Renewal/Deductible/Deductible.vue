<template>
  <div class="grid">
    <LoadingIndicator v-if="isLoading" />
    <div v-else>
      <div>
        <div v-for="(deductibles, detailId, index) in formValues" :key="detailId" class="pt-5 pb-2 border-b border-gray-300 first:pt-0 last:border-b-0">
          <DeductibleItem
            v-model="formValues[detailId]"
            :index="index"
            :detailId="detailId"
            @toggle="toggle"
            @collapse="collapse"
          />
        </div>
      </div>
      <div class="text-right">
        <router-link :to="{name: cancelRoute}" class="btn btn-outline-secondary w-24 mr-1" tag="button">Cancel</router-link>
        <button class="btn btn-primary w-24" :disabled="isSaving" @click="handleUpdate">Save<span v-if="isSaving">...</span></button>
      </div>
    </div>
  </div>
</template>

<script>

import DeductibleItem from './DeductibleItem.vue';
import LoadingIndicator from '@/components/LoadingIndicator.vue'

export default {
  props: {
    dataId: [Number, String],
    cancelRoute: String,
  },
  components: {
    DeductibleItem,
    LoadingIndicator,
  },
  data() {
    return {
      formValues: {},
      isLoading: true,
      isSaving: false,
    }
  },
  methods: {
    getDeductibleDetails(dataId) {
      this.isLoading = true
      axios.get('/auto-service/get-deductible-details/' + dataId).then(res => {
        this.formValues = res.data
      })
      .catch(e => console.error(e))
      .finally(() => this.isLoading = false)
    },
    toggle(vehicleNo) {
      Object.keys(this.formValues).forEach((item, index) => {
        const header = document.querySelector('.show-deductible-' + index)
        const icon = document.querySelector('.deductible-icon-' + index)

        if(vehicleNo !== index){
          header.classList.add('collapse')
          icon.classList.add('rotate', 'icon-color')
          return;
        }

        header.classList.toggle('collapse')
        icon.classList.toggle('rotate')
        icon.classList.toggle('icon-color')
      })
    },
    collapse(vehicleNo) {
      const classId = document.querySelector('.show-deductible-' + vehicleNo)
      const iconId = document.querySelector('.deductible-icon-' + vehicleNo)

      classId.classList.remove('collapse')
      iconId.classList.remove('rotate')
      iconId.classList.remove('icon-color')
    },
    async handleUpdate() {
      this.isSaving = true
      try {
        let res = await this.updateDeductibleData();
        // Update field updated_at
        await this.updateAmendedDate();
        
        if (res.data.success) {
          notify(res.data.message,'success')
        }
      } catch (e) {
        console.error(e)
        notify('Unexpected Error','error')
      } finally {
        this.isSaving = false
      }
    },
    updateDeductibleData() {
      return axios.post('/deductible-details/updates', this.formValues)
    },
    updateAmendedDate() {
      axios.put('/autos/update-issue-date/' + this.dataId)
    }
  },
  mounted() {
    this.getDeductibleDetails(this.dataId)
  },
}
</script>