<template>
  <Dialog :visible="isVisible" :modal="true" :closable="false" :draggable="false"
    class="p-fluid w-5/12 custom-dialog z-10" position="top">
    <template #header>
      <span class="p-dialog-title flex-1">{{ header }}</span>
      <button @click="hideDialog" aria-label="close" type="button" tabindex="-1"
        class="p-dialog-header-icon p-dialog-header-close p-link" style="margin-right: 0">
        <span class="p-dialog-header-close-icon pi pi-times"></span>
      </button>
    </template>
    <div>
      <div class="grid lg:grid-cols-2 gap-x-5 gap-y-4">
        <div>
          <label for="" class="form-label">Driving License Number *</label>
          <InputNumber :useGrouping="false" v-model="formValues.license_no" placeholder="Driver license no." />
          <span class="text-error" v-if="errors['license_no']">{{errors['license_no'][0]}}</span>
        </div>
        <div>
          <label for="" class="form-label">Make *</label>
          <InputText v-model="formValues.vehicle_make" placeholder="Make" />
          <span class="text-error" v-if="errors['vehicle_make']">{{errors['vehicle_make'][0]}}</span>
        </div>
        <div>
          <label for="" class="form-label">Model *</label>
          <InputText v-model="formValues.vehicle_model" placeholder="Model" />
          <span class="text-error" v-if="errors['vehicle_model']">{{errors['vehicle_model'][0]}}</span>
        </div>
        <div>
          <label for="" class="form-label">Phone Number</label>
          <InputText v-model="formValues.phone_number" placeholder="Phone number" />
          <span class="text-error" v-if="errors['phone_number']">{{errors['phone_number'][0]}}</span>
        </div>
        <div>
          <label for="" class="form-label">Plate No. *</label>
          <InputText v-model="formValues.plate_no" placeholder="Plate no." />
          <span class="text-error" v-if="errors['plate_no']">{{errors['plate_no'][0]}}</span>
        </div>
        <div>
          <label for="" class="form-label">Engine No. *</label>
          <InputText v-model="formValues.engine_no" placeholder="Engine No." />
          <span class="text-error" v-if="errors['engine_no']">{{errors['engine_no'][0]}}</span>
        </div>
        <div>
          <label for="" class="form-label">Address</label>
          <InputText v-model="formValues.address" placeholder="Address" />
          <span class="text-error" v-if="errors['address']">{{errors['address'][0]}}</span>
        </div>
        <div>
          <label for="" class="form-label">Description</label>
          <Textarea v-model="formValues.description" placeholder="Description" rows="4" />
          <span class="text-error" v-if="errors['description']">{{errors['description'][0]}}</span>
        </div>
      </div>
    </div>
    <template #footer>
      <Button label="Close" class="p-button-secondary" @click="hideDialog" />
      <Button label="Save" :disabled="isLoading" type="button" :loading="isLoading" class="p-button-info" autofocus @click="handleSubmit" />
    </template>
  </Dialog>
</template>

<script>

import ThirdPartyService from '@/services/claim/third_party.service'

export default {
  props: {
    header: String,
    isVisible: Boolean,
    submitted: Boolean,
  },

  data() {
    return {
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
          this.$notify({
            group: 'bottom',
            title: 'Success',
            text: res.data?.message,
          }, 4000);

          let createdThirdParty = { value: res.data?.data.id, label: res.data?.data.label }
          this.$emit('setThirdPartyId', createdThirdParty)

          this.hideDialog()
        })
        .catch(err => {
          if (err?.response?.status === 422) {
            notify("Validation failed",'error');
            this.errors = err.response.data.errors
          } else {
            notify(err?.response?.data?.message,"error");
          }
        })
        .finally(() => this.isLoading = false);
    },

    hideDialog() {
      this.formValues.license_no = null
      this.formValues.vehicle_make = ''
      this.formValues.vehicle_model = ''
      this.formValues.plate_no = ''
      this.formValues.engine_no = ''
      this.formValues.phone_number = ''
      this.formValues.address = ''
      this.formValues.description = ''

      this.errors = {}

      this.$emit('hideDialog')
    },
  },
}
</script>