<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Register
      </h2>
    </div>
    <div class="intro-y box grid gap-y-2 mt-5 p-5">
      <div class="grid lg:grid-cols-4 gap-x-5">
        <div>
          <label class="form-label">Policy No. *</label>
          <Dropdown class="w-full" v-model="formValues.policy_no" filter :options="lovs.policies"
            :loading="policyLoading" placeholder="Select policy" optionLabel="label" optionValue="value"
            @change="listVehicles($event.value)" :showClear="true" />
          <span class="text-error" v-if="errors['policy_no']">{{ errors['policy_no'][0] }}</span>
        </div>
        <div>
          <label class="form-label">Vehicle *</label>
          <Dropdown v-model="formValues.detail_id" class="w-full p-inputtext-sm" optionLabel="plate_no"
            optionValue="detail_id" placeholder="Select vehicle" :filter="true" :showClear="true" :options="vehicles"
            @change="listCovers($event.value)" />
            <span class="text-error" v-if="errors['policy_no']">{{ errors['policy_no'][0] }}</span>
        </div>
        <div v-if="vehicles.length > 0" class="flex items-center">
          <div class="mt-6">Insured Name: <span class="font-bold">{{ vehicles[0].insured_name }}</span></div>
        </div>
      </div>

      <div v-if="formValues.detail_id" class="formulate-input">
        <CauseOfLoss v-model="formValues.cause_of_losses" :causeOfLosses="lovs.causeOfLosses" :errors="errors"
          @addMore="addNonCover" />

        <ul v-if="errors.cause_of_losses" class="formulate-input-errors">
          <li class="formulate-input-error">{{ errors.cause_of_losses[0] }}</li>
        </ul>
      </div>

      <div v-if="formValues.detail_id" class="grid grid-cols-1">
        <label for="" class="form-label">Deductible</label>
        <div class="grid grid-cols-4 gap-x-5 mb-3" v-for="(deductible, index) in formValues.deductibles">
          <InputText v-model="formValues.deductibles[index].deductible_label" disabled />
          <InputText v-model="formValues.deductibles[index].value" disabled />
          <div class="flex items-center">
            <label for="" class="pr-2">Value</label>
            <InputNumber v-model="formValues.deductibles[index].cond_value" class="flex-1" placeholder="Value"
              :minFractionDigits="0" :maxFractionDigits="2" />
              <span class="text-error" v-if="errors[`deductibles.${index}.cond_value`]">{{ errors[`deductibles.${index}.cond_value`][0] }}</span>
          </div>
          <div v-if="formValues.deductibles && getValueType(index) === 'PERCENTAGE'" class="flex items-center">
            <label class="input-label mr-3 text-nowrap">Min Value</label>
            <InputNumber v-model="formValues.deductibles[index].min_value" class="w-full" placeholder="Min value"
              :minFractionDigits="0" :maxFractionDigits="2" />
              <span class="text-error" v-if="errors[`deductibles.${index}.min_value`]">{{ errors[`deductibles.${index}.min_value`][0] }}</span>
          </div>
        </div>
      </div>
      <div class="grid lg:grid-cols-4 gap-x-5 mb-2">
        <div class="formulate-input">
          <label class="formulate-input-label">Driver *</label>
          <InputGroup>
            <Dropdown v-model="formValues.driver_id" class="w-full p-inputtext-sm" placeholder="Driver"
              optionLabel="label" optionValue="value" :filter="true" :showClear="true" :options="lovs.drivers" />
            <Button icon="pi pi-plus" severity="info" @click="openDriverDialog" />
          </InputGroup>
          <span class="text-error" v-if="errors['driver_id']">{{ errors['driver_id'][0] }}</span>
        </div>
        <div>
          <label for="" class="form-label">Date of Accident *</label>
          <Calendar v-model="formValues.incident_date" placeholder="Date of accident" dateFormat="dd-M-yy" showIcon />
          <span class="text-error" v-if="errors['incident_date']">{{ errors['incident_date'][0] }}</span>
        </div>
        <div>
          <label for="" class="form-label">Date of Notification *</label>
          <Calendar v-model="formValues.notification_date" placeholder="Date of notification" dateFormat="dd-M-yy"
            showIcon />
            <span class="text-error" v-if="errors['notification_date']">{{ errors['notification_date'][0] }}</span>
        </div>
        <div>
          <label for="" class="form-label">Place of Accident *</label>
          <InputText v-model="formValues.incident_location" placeholder="Place of accident" />
          <span class="text-error" v-if="errors['incident_location']">{{ errors['incident_location'][0] }}</span>
        </div>
      </div>

      <div class="grid lg:grid-cols-4 gap-x-5">
        <div>
          <label class="form-label">Third Party Vehicle</label>
          <InputGroup>
            <Dropdown v-model="formValues.third_party_id" class="w-full p-inputtext-sm flex-1"
              placeholder="Third Party Vehicle" optionLabel="label" optionValue="value" :filter="true" :showClear="true"
              :options="lovs.thirdParties" />
            <Button icon="pi pi-plus" severity="info" @click="openDialog" />
          </InputGroup>
          <span class="text-error" v-if="errors['third_party_id']">{{ errors['third_party_id'][0] }}</span>
        </div>
        <div>
          <label class="form-label">Loss Adjuster Company</label>
          <Dropdown v-model="formValues.adjuster_company_id" class="w-full p-inputtext-sm"
            placeholder="Loss Adjuster Company" optionLabel="label" optionValue="value" :filter="true" :showClear="true"
            :options="lovs.adjusterCompanies" />
            <span class="text-error" v-if="errors['adjuster_company_id']">{{ errors['adjuster_company_id'][0] }}</span>
        </div>
        <div>
          <label for="" class="form-label">Description of Loss</label>
          <Textarea v-model="formValues.remark" class="w-full" placeholder="Description of loss" rows="4" />
        </div>
      </div>

      <div class="text-right mt-5">
        <router-link :to="{ name: 'ClaimRegisterIndex' }" class="btn btn-outline-secondary w-24 mr-1">
          Cancel
        </router-link>
        <button type="submit" @click="handleSubmit" :disabled="isLoading" class="btn btn-primary w-24">
          {{ isLoading ? 'Saving ...' : 'Save' }}
        </button>
      </div>
    </div>
    <ThirdPartyForm header="Add Third Party Vehicle" :isVisible="showDialog" :submitted="submitted"
      @hideDialog="hideDialog" @setThirdPartyId="setThirdPartyId" />
    <DriverForm header="Add Driver" :isVisible="showDriverDialog" :submitted="submitted" @hideDialog="hideDialog"
      @setDriverId="setDriverId" :genderOptions="genderOptions" />
  </div>
</template>

<script>

import RegisterService from '@/services/claim/register.service'
import CauseOfLoss from '@/views/Claim/Register/Partials/CauseOfLoss.vue'
import ThirdPartyForm from '@/views/Claim/Register/Partials/ThirdPartyForm.vue'
import DriverForm from '@/views/Claim/Register/Partials/DriverForm.vue'
import moment from 'moment';

export default {
  components: {
    CauseOfLoss,
    ThirdPartyForm,
    DriverForm
  },
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        policy_no: null,
        detail_id: null,
        cause_of_losses: [],
        third_party_id: null,
        notification_date: '',
        incident_date: '',
        incident_location: '',
        driver_id: null,
        adjuster_company_id: null,
        remark: '',
        deductibles: []
      },
      lovs: {
        policies: [],
        causeOfLosses: [],
        thirdParties: [],
        drivers: [],
        adjusterCompanies: [],
      },
      vehicles: [],
      filteredPolicies: [],

      errors: {},
      isLoading: false,
      policyLoading: true,
      showDialog: false,
      showDriverDialog: false,
      submitted: false,
      genderOptions: [{ label: 'M', value: 'M' }, { label: 'F', value: 'F' }],
    }
  },
  methods: {
    addNonCover() {
      this.formValues.cause_of_losses.push({
        code: null,
        name: null,
        type: 'NON_COVER',
      })
    },
    handleSubmit() {
      this.isLoading = true

      const method = this.id ? "PUT" : "POST"
      RegisterService.save(
        {
          ...this.formValues,
          ...(method === "PUT" && { id: this.id })
        },
        method,
      )
        .then(res => {
          this.$notify({
            group: 'bottom',
            title: 'Success',
            text: res.data?.message,
          }, 4000);
          this.$router.push({ name: 'ClaimRegisterIndex' })
        })
        .catch(err => {
          if (err?.response?.status === 422) {
            notify('Validation failed', 'error');
            this.errors = err.response.data.errors
          } else {
            notify(err?.response?.data?.message, 'error');
          }
        })
        .finally(() => this.isLoading = false);
    },

    getLovs() {
      RegisterService.getLovs().then(res => {
        this.lovs = res.data
      }).finally(() => this.policyLoading = false)
    },

    listVehicles(value) {
      if (value) RegisterService.listVehicles(value).then(res => this.vehicles = res.data)
    },

    listCovers(value) {
      if (!value) {
        this.formValues.cause_of_losses = []
        return
      }
      RegisterService.listCovers(value).then(res => {
        this.formValues.cause_of_losses = res.data
      })
      this.getDeductible(value)
    },

    clearVehicle() {
      this.formValues.detail_id = null
      this.vehicles = []
    },

    getData() {
      if (this.id) {
        RegisterService.getData(this.id).then(res => {
          this.formValues = res.data
          this.formValues.notification_date = moment(res.data.notification_date).toDate()
          this.formValues.incident_date = moment(res.data.incident_date).toDate()
          this.listVehicles(res.data?.policy_no)
        })
          .catch(err => {
            this.$notify({
              group: 'bottom',
              title: 'Error',
              text: err?.response?.data?.message,
            }, 4000);
          })
      }
    },

    openDialog() {
      this.showDialog = true
      this.submitted = false
    },
    openDriverDialog() {
      this.showDriverDialog = true
      this.submitted = false
    },
    hideDialog() {
      this.showDialog = false
      this.showDriverDialog = false
      this.submitted = false
    },

    setThirdPartyId(item) {
      this.lovs.thirdParties.unshift(item)
      this.formValues.third_party_id = item.value
    },
    setDriverId(item) {
      this.lovs.drivers.unshift(item)
      this.formValues.driver_id = item.value
    },
    getDeductible(detailId) {
      RegisterService.listDeductibles(detailId).then(res => {
        this.formValues.deductibles = res.data
      })
    },
    getValueType(index) {
      if (index) {
        return this.formValues.deductibles[index]?.cond_value_type
      }
    }
  },
  mounted() {
    this.getLovs()
    this.getData()
  }
};
</script>