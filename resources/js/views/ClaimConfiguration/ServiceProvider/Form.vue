<template>
  <div>
		<div class="intro-y flex items-center mt-8">
			<h2 class="text-lg font-medium mr-auto">
				<span v-if="id">Edit</span>
				<span v-else>Create</span>
				Service Provider
			</h2>
		</div>
    <div class="grid grid-cols-12 gap-6 mt-5">
      <div class="intro-y col-span-12 lg:col-span-12">
        <div class="intro-y box p-5">
          <div class="grid grid-cols-12 gap-x-10 gap-y-4">
            
            <div class="col-span-3">
              <label class="mb-1 block font-bold">Name *</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.name"
                placeholder="Name"
              />
              <span v-if="errors.name" class="text-theme-6">
                {{ errors.name[0] }}
              </span>
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Email</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.email"
                placeholder="Email"
                type="email"
              />
              <span v-if="errors.email" class="text-theme-6">
                {{ errors.email[0] }}
              </span>
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Phone Number *</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.phone_number"
                placeholder="Phone Number"
                type="tel"
              />
              <span v-if="errors.phone_number" class="text-theme-6">
                {{ errors.phone_number[0] }}
              </span>
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Type *</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.type"
                :options="Object.entries(lovs.types).map(([value, label]) => ({
                  label,
                  value
                }))"
                optionLabel="label"
                optionValue="value"
                placeholder="Select Type"
              />
              <span v-if="errors.type" class="text-theme-6">
                {{ errors.type[0] }}
              </span>
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Home No</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.home_no"
                placeholder="Home No."
              />
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Street No</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.street_no"
                placeholder="Street No."
              />
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Commune</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.commune"
                placeholder="Commune"
              />
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">District</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.district"
                placeholder="District"
              />
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">City</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.city"
                placeholder="City"
              />
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Latitude</label>
              <InputNumber
                class="w-full"
                v-model="formValues.latitude"
                placeholder="Latitude"
                :minFractionDigits="6"
                step="any"
              />
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Longitude</label>
              <InputNumber
                class="w-full"
                v-model="formValues.longitude"
                placeholder="Longitude"
                :minFractionDigits="6"
                step="any"
              />
            </div>

            <div class="col-span-3">
              <label class="block mb-1 font-bold">Is Partner</label>
              <Checkbox
                v-model="formValues.is_partner"
                :binary="true"
              />
            </div>

          </div>
          <div class="text-right mt-5">
            <router-link :to="{name: 'ServiceProviderIndex'}" class="btn btn-outline-secondary w-24 mr-1">
              Cancel
            </router-link>
            <button type="button" @click="handleSubmit" :disabled="isLoading" class="btn btn-primary w-24">
              {{ isLoading ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import ServiceProviderService from '@/services/claim/service_provider.service'

export default {
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        name: '',
        email: '',
        phone_number: '',
        home_no: '',
        street_no: '',
        commune: '',
        district: '',
        city: '',
        latitude: '',
        longitude: '',
        type: '',
        is_partner: '',
      },
      lovs: {
        types: {
          GARAGE: 'GARAGE',
          HOSPITAL: 'HOSPITAL',
        },
      },
      errors: [],
      isLoading: false,
    }
  },

  methods: {
    handleSubmit() {
      this.isLoading = true

      const method = this.id ? "PUT" : "POST"
      ServiceProviderService.save(
        {
        ...this.formValues,
        ...(method === "PUT" && { id: this.id })
        },
        method
      )
      .then(res => {
          notify(res.data?.message, 'success', 'bottom-right');
        this.$router.push({name: 'ServiceProviderIndex'})
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
        ServiceProviderService.getData(this.id).then(res => {
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