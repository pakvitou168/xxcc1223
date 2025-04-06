<template>
  <div>
		<div class="intro-y flex items-center mt-8">
			<h2 class="text-lg font-medium mr-auto">
				<span v-if="id">Edit</span>
				<span v-else>Create</span>
				Adjuster Company
			</h2>
		</div>
    <div class="grid grid-cols-12 gap-6 mt-5">
      <div class="intro-y col-span-12">
      <div class="intro-y box p-5">
        <div class="grid grid-cols-12 gap-x-10 gap-y-4">
        
        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">Name (English) *</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.name_en"
          placeholder="Name (English)"
          />
          <span v-if="errors.name_en" class="text-theme-6">
          {{ errors.name_en[0] }}
          </span>
        </div>

        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">Name (Khmer)</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.name_kh"
          placeholder="Name (Khmer)"
          />
        </div>

        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">Phone Number *</label>
          <InputNumber
          class="w-full"
          v-model="formValues.phone_number"
          placeholder="Phone Number"
          />
          <span v-if="errors.phone_number" class="text-theme-6">
          {{ errors.phone_number[0] }}
          </span>
        </div>

        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">Email</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.email"
          placeholder="Email"
          />
        </div>

        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">Postal Code</label>
          <InputNumber
          class="w-full"
          v-model="formValues.postal_code"
          placeholder="Postal Code"
          />
        </div>

        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">Home No.</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.home_no"
          placeholder="Home No."
          />
        </div>

        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">Street No.</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.street_no"
          placeholder="Street No."
          />
        </div>

        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">Commune</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.commune"
          placeholder="Commune"
          />
        </div>

        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">District</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.district"
          placeholder="District"
          />
        </div>

        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">City</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.city"
          placeholder="City"
          />
        </div>

        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">Address</label>
          <InputText
          class="w-full p-inputtext-sm"
          v-model="formValues.address"
          placeholder="Address"
          />
        </div>

        <div class="col-span-12 lg:col-span-3">
          <label class="mb-1 block font-bold">Description</label>
          <Textarea
          class="w-full"
          rows="3"
          v-model="formValues.description"
          placeholder="Description"
          />
        </div>

        </div>
        <div class="text-right mt-5">
        <router-link :to="{name: 'AdjusterCompanyIndex'}" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
        <button type="button" @click="handleSubmit" :disabled="isLoading" class="btn btn-primary w-24">
          {{ isLoading ? 'Saving ...' : 'Save' }}
        </button>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script>

import AdjusterService from '@/services/claim/adjuster_company.service'

export default {
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        name_en: '',
        name_kh: '',
        postal_code: '',
        home_no: '',
        street_no: '',
        commune: '',
        district: '',
        city: '',
        phone_number: '',
        email: '',
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
      AdjusterService.save(
        {
        ...this.formValues,
        ...(method === "PUT" && { id: this.id })
        },
        method
      )
      .then(res => {
        notify(res.data?.message, 'success', 'bottom-right');
        this.$router.push({name: 'AdjusterCompanyIndex'})
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
        AdjusterService.getData(this.id).then(res => {
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