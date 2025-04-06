<template>
  <div>
		<div class="intro-y flex items-center mt-8">
			<h2 class="text-lg font-medium mr-auto">
				<span v-if="id">Edit</span>
				<span v-else>Create</span>
				Driver License
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
              <label class="mb-1 block font-bold">Gender *</label>
              <Dropdown
                class="w-full p-inputtext-sm"
                v-model="formValues.gender"
                :options="genderOptions"
                optionLabel="label"
                optionValue="value"
                placeholder="Gender"
              />
              <span v-if="errors.gender" class="text-theme-6">
                {{ errors.gender[0] }}
              </span>
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Driver Age *</label>
              <InputNumber
                class="w-full"
                v-model="formValues.driver_age"
                placeholder="Driver Age"
              />
              <span v-if="errors.driver_age" class="text-theme-6">
                {{ errors.driver_age[0] }}
              </span>
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Occupation</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.occupation"
                placeholder="Occupation"
              />
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">License No. *</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.license_no"
                placeholder="License No."
              />
              <span v-if="errors.license_no" class="text-theme-6">
                {{ errors.license_no[0] }}
              </span>
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">License Issue Date *</label>
              <Calendar
                class="w-full"
                v-model="formValues.license_issue_date"
                dateFormat="yy-mm-dd"
              />
              <span v-if="errors.license_issue_date" class="text-theme-6">
                {{ errors.license_issue_date[0] }}
              </span>
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">License Expire Date *</label>
              <Calendar
                class="w-full"
                v-model="formValues.license_expire_date"
                dateFormat="yy-mm-dd"
              />
              <span v-if="errors.license_expire_date" class="text-theme-6">
                {{ errors.license_expire_date[0] }}
              </span>
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Postal Code</label>
              <InputNumber
                class="w-full"
                v-model="formValues.postal_code"
                placeholder="Postal Code"
              />
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Home No.</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.home_no"
                placeholder="Home No."
              />
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Street No.</label>
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
              <label class="mb-1 block font-bold">Address</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.address"
                placeholder="Address"
              />
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Phone Number</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.phone_number"
                placeholder="Phone Number"
              />
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Email</label>
              <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.email"
                placeholder="Email"
              />
              <span v-if="errors.email" class="text-theme-6">
                {{ errors.email[0] }}
              </span>
            </div>

            <div class="col-span-3">
              <label class="mb-1 block font-bold">Description</label>
              <Textarea
                class="w-full"
                v-model="formValues.description"
                rows="3"
                placeholder="Description"
              />
            </div>

          </div>
          <div class="text-right mt-5">
            <router-link :to="{name: 'DriverLicenseIndex'}" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
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

import DriverLicenseService from '@/services/claim/driver_license.service'

export default {
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        name: '',
        gender: '',
        driver_age: null,
        occupation: '',
        license_no: '',
        license_issue_date: null, // Changed to null since it's a date
        license_expire_date: null, // Changed to null since it's a date
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
      genderOptions: [
        { value: 'M', label: 'Male' },
        { value: 'F', label: 'Female' }
      ],
      errors: [],
      isLoading: false,
    }
  },

  methods: {
    handleSubmit() {
      this.isLoading = true;

      const method = this.id ? "PUT" : "POST";
      const payload = {
        ...this.formValues,
        license_issue_date: this.formValues.license_issue_date ? new Date(this.formValues.license_issue_date).toISOString().split('T')[0] : null,
        license_expire_date: this.formValues.license_expire_date ? new Date(this.formValues.license_expire_date).toISOString().split('T')[0] : null,
        ...(method === "PUT" && { id: this.id })
      };

      DriverLicenseService.save(payload, method)
        .then(res => {
          notify(res.data?.message, 'success', 'bottom-right');
          this.$router.push({name: 'DriverLicenseIndex'})
        })
        .catch(err => {
          console.error('Save error:', err.response);
          if (err?.response?.status === 422) {
            this.errors = err.response.data.errors;
          }
         notify('Validation Error', 'error', 'bottom-right');
        })
        .finally(() => this.isLoading = false);
    },

    getData() {
      if (this.id) {
        DriverLicenseService.getData(this.id)
          .then(res => {
            // Transform dates from string to Date objects for Calendar component
            const formattedData = {
              ...res.data,
              license_issue_date: res.data.license_issue_date ? new Date(res.data.license_issue_date) : null,
              license_expire_date: res.data.license_expire_date ? new Date(res.data.license_expire_date) : null,
              // Convert nulls to empty strings for text inputs
              name: res.data.name || '',
              occupation: res.data.occupation || '',
              postal_code: res.data.postal_code || '',
              home_no: res.data.home_no || '',
              street_no: res.data.street_no || '',
              commune: res.data.commune || '',
              district: res.data.district || '',
              city: res.data.city || '',
              phone_number: res.data.phone_number || '',
              email: res.data.email || '',
              address: res.data.address || '',
              description: res.data.description || ''
            };
            this.formValues = formattedData;
          })
          .catch(err => {
            console.error('Fetch error:', err.response);
            notify('Validation Error', 'error', 'bottom-right');
          });
      }
    },
  },

  mounted() {
    this.getData();
  }
}
</script>