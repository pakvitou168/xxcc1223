<template>
  <div>
		<div class="intro-y flex items-center mt-8">
			<h2 class="text-lg font-medium mr-auto">
				<span v-if="id">Edit</span>
				<span v-else>Create</span>
				Third Party
			</h2>
		</div>
    <div class="grid grid-cols-12 gap-6 mt-5">
      <div class="intro-y col-span-12 lg:col-span-6">
      <div class="intro-y box p-5">
        <div class="grid grid-cols-12 gap-x-10 gap-y-4">
        
        <div class="col-span-12">
          <label class="mb-1 block font-bold">Driving License Number *</label>
          <InputNumber
          class="w-full p-inputtext-sm"
          v-model="formValues.license_no"
          placeholder="Driving License Number"
          />
          <span v-if="errors.license_no" class="text-theme-6">
          {{ errors.license_no[0] }}
          </span>
        </div>

        <div class="col-span-12">
          <label class="mb-1 block font-bold">Vehicle Make *</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.vehicle_make" 
          placeholder="Vehicle Make"
          />
          <span v-if="errors.vehicle_make" class="text-theme-6">
          {{ errors.vehicle_make[0] }}
          </span>
        </div>

        <div class="col-span-12">
          <label class="mb-1 block font-bold">Vehicle Model *</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.vehicle_model"
          placeholder="Vehicle Model"
          />
          <span v-if="errors.vehicle_model" class="text-theme-6">
          {{ errors.vehicle_model[0] }}
          </span>
        </div>

        <div class="col-span-12">
          <label class="mb-1 block font-bold">Plate Number *</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.plate_no"
          placeholder="Plate Number"
          />
          <span v-if="errors.plate_no" class="text-theme-6">
          {{ errors.plate_no[0] }}
          </span>
        </div>

        <div class="col-span-12">
          <label class="mb-1 block font-bold">Engine No *</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.engine_no"
          placeholder="Engine No"
          />
          <span v-if="errors.engine_no" class="text-theme-6">
          {{ errors.engine_no[0] }}
          </span>
        </div>

        <div class="col-span-12">
          <label class="mb-1 block font-bold">Manufacturing Year</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.manufacturing_year"
          placeholder="Manufacturing Year"
          />
        </div>

        <div class="col-span-12">
          <label class="mb-1 block font-bold">Phone Number</label>
          <InputNumber
          class="w-full p-inputtext-sm"
          v-model="formValues.phone_number"
          placeholder="Phone Number"
          />
        </div>

        <div class="col-span-12">
          <label class="mb-1 block font-bold">Address</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.address"
          placeholder="Address"
          />
        </div>

        <div class="col-span-12">
          <label class="font-bold mb-1 block">Description</label>
          <Textarea
          class="w-full"
          rows="3"
          v-model="formValues.description"
          placeholder="Description"
          />
        </div>
        
        </div>

        <div class="text-right mt-5">
        <router-link 
          :to="{name: 'ThirdPartyIndex'}" 
          class="btn btn-outline-secondary w-24 mr-1"
        >
          Cancel
        </router-link>
        <button 
          type="button" 
          @click="handleSubmit" 
          :disabled="isLoading"
          class="btn btn-primary w-24"
        >
          {{ isLoading ? 'Saving...' : 'Save' }}
        </button>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script>

import ThirdPartyService from '@/services/claim/third_party.service'

export default {
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        license_no: null,
        vehicle_make: '',
        vehicle_model: '',
        plate_no: '',
        engine_no: '',
        phone_number: '',
        address: '',
        description: '',
      },
      errors: [],
      isLoading: false,
    }
  },

  methods: {
    handleSubmit() {
      this.isLoading = true

      const method = this.id ? "PUT" : "POST"
      ThirdPartyService.save(
        {
        ...this.formValues,
        ...(method === "PUT" && { id: this.id })
        },
        method
      )
      .then(res => {
        notify(res.data?.message, 'success', 'bottom-right');
        this.$router.push({name: 'ThirdPartyIndex'})
      })
      .catch(err => {
        if (err?.response?.status === 422) {
          notify('Validation Error', 'error', 'bottom-right');
          this.errors = err.response.data.errors
        } else {
          notify('Validation Error', 'error', 'bottom-right');
        }
      })
      .finally(() => this.isLoading = false);
    },
    getData() {
      if (this.id) {
        ThirdPartyService.getData(this.id).then(res => {
          this.formValues = res.data
        })
        .catch(err => {
          notify('Validation Error', 'error', 'bottom-right');
        })
      }
    },
  },

  mounted() {
    this.getData()
  }
}
</script>