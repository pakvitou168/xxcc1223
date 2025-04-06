<template>
  <div>
		<div class="intro-y flex items-center mt-8">
			<h2 class="text-lg font-medium mr-auto">
				<span v-if="id">Edit</span>
				<span v-else>Create</span>
				Payee
			</h2>
		</div>
    <div class="grid grid-cols-12 gap-6 mt-5">
      <div class="intro-y col-span-12 lg:col-span-6">
        <div class="intro-y box p-5">
          <div class="grid grid-cols-12 gap-x-10 gap-y-4">
            
            <div class="col-span-12">
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

            <div class="col-span-12">
              <label class="mb-1 block font-bold">Name (Khmer) *</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.name_kh"
                placeholder="Name (Khmer)"
              />
              <span v-if="errors.name_kh" class="text-theme-6">
                {{ errors.name_kh[0] }}
              </span>
            </div>

            <div class="col-span-12">
              <label class="mb-1 block font-bold">Payee Type *</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.type" 
                :options="Object.entries(lovs.payee_types).map(([value, label]) => ({
                  label,
                  value
                }))"
                optionLabel="label"
                optionValue="value"
                placeholder="Select Payee Type"
              />
              <span v-if="errors.type" class="text-theme-6">
                {{ errors.type[0] }}
              </span>
            </div>

            <div class="col-span-12">
              <label class="mb-1 block font-bold">Phone Number</label>
              <InputNumber
                class="w-full"
                v-model="formValues.phone_number"
                placeholder="Phone Number"
              />
            </div>

            <div class="col-span-12">
              <label class="font-bold mb-1 block">Address *</label>
              <Textarea
                class="w-full"
                rows="3"
                v-model="formValues.address"
                placeholder="Address"
              />
              <span v-if="errors.address" class="text-theme-6">
                {{ errors.address[0] }}
              </span>
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
              :to="{name: 'PayeeIndex'}" 
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

import PayeeService from '@/services/claim/payee.service'

export default {
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        name_en: '',
        name_kh: '',
        type: '',
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
      lovs: {
        payee_types: {},
      },
      errors: [],
      isLoading: false,
    }
  },

  methods: {
    handleSubmit() {
      this.isLoading = true

      const method = this.id ? "PUT" : "POST"
      PayeeService.save(
        {
        ...this.formValues,
        ...(method === "PUT" && { id: this.id })
        },
        method
      )
      .then(res => {
          notify(res.data?.message, 'success', 'bottom-right');
        this.$router.push({name: 'PayeeIndex'})
      })
      .catch(err => {
        if (err?.response?.status === 422) {
            notify(res.data?.message, 'success', 'bottom-right');
          this.errors = err.response.data.errors
        } else {
            notify('Validation Error', 'error', 'bottom-right');
        }
      })
      .finally(() => this.isLoading = false);
    },
    getData() {
      if (this.id) {
        PayeeService.getData(this.id).then(res => {
          this.formValues = res.data
        })
        .catch(err => {
          notify('Validation Error', 'error', 'bottom-right');
        })
      }
    },
    getLovs() {
        PayeeService.getLovs().then(res => {
          this.lovs.payee_types = res.data.payee_types
        })
      },
  },

  mounted() {
    this.getLovs()
    this.getData()
  }
}
</script>