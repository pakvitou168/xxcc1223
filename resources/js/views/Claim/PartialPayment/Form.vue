<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Partial Payment
      </h2>
    </div>
    <!-- <div class="grid grid-cols-2"> -->
    <div class="intro-y box grid gap-y-2 mt-5 p-5">
      <!-- <div class="intro-y box mt-5 p-5"> -->
      <div class="grid lg:grid-cols-4 gap-x-5">
        <div class="formulate-input">
          <label class="form-label">Claims No. *</label>
          <Dropdown class="w-full" v-model="formValues.claim_no" showClear filter optionValue="value" optionLabel="label" placeholder="Select claim no." :options="lovs.registers" @change="listCauseOfLosses($event.value)" />
        </div>
        <div v-if="vehicle && (vehicle.insured_name || vehicle.plate_no)" class="flex items-center col-span-3">
          <div v-if="vehicle.insured_name" class="mt-6">Insured Name: <span class="font-bold">{{ vehicle.insured_name }}</span></div><div class="mt-6" v-if="vehicle.plate_no"><span class="px-6">&nbsp;|&nbsp;</span>Vehicle: <span class="font-bold">{{ vehicle.plate_no }}</span></div>
        </div>
      </div>
      <div v-if="formValues.claim_no" class="formulate-input">
        <CauseOfLoss v-model="formValues.cause_of_losses" :payees="lovs.payees" :errors="errors"
          :paymentTypes="lovs.payment_types" @openDialog="openDialog" />
        <ul v-if="errors.cause_of_losses" class="formulate-input-errors">
          <li class="formulate-input-error">{{ errors.cause_of_losses[0] }}</li>
        </ul>
      </div>

      <div class="grid lg:grid-cols-4 gap-x-5">
        <FormulateInput type="date" disabled="true" v-model="formValues.payment_date" label="Payment Date *" />
      </div>

      <div class="text-right mt-5">
        <router-link :to="{ name: 'PartialPaymentIndex' }" class="btn btn-outline-secondary w-24 mr-1">
          Cancel
        </router-link>
        <button type="submit" @click="handleSubmit" :disabled="isLoading" class="btn btn-primary w-24">
          {{ isLoading ? 'Saving ...' : 'Save' }}
        </button>
      </div>
    </div>
    <PayeeDialog :isVisible="showDialog" :payeeTypes="lovs.payee_types" @hideDialog="hideDialog"
      @setPayeeTypeId="setPayeeTypeId" />
  </div>
  <!-- </div> -->
</template>

<script>

import PartialPaymentService from '@/services/claim/partial_payment.service'
import CauseOfLoss from '@/views/Claim/PartialPayment/Partials/CauseOfLoss.vue'
import PayeeDialog from '@/components/Dialogs/PayeeDialog.vue'

export default {
  components: {
    CauseOfLoss,
    PayeeDialog
  },
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        detail_id: null,
        claim_no: null,
        cause_of_losses: [],
        document_no: null,
        payment_date: new Date().toISOString().slice(0, 10),
      },
      lovs: {
        registers: [],
        payees: [],
        payee_types:[],
        payment_types: []
      },
      vehicle: [],
      filteredRegisters: [],

      errors: {},
      isLoading: false,
      showDialog:false,
      selectedPayeeIndex:null,
    }
  },
  methods: {
    handleSubmit() {
      this.isLoading = true

      const method = this.id ? "PUT" : "POST"
      PartialPaymentService.save(
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
          this.$router.push({ name: 'PartialPaymentIndex' })
        })
        .catch(err => {
          if (err?.response?.status === 422) {
            this.$notify({
              group: 'bottom',
              title: 'Error',
              text: 'Validation Error',
            }, 4000);

            this.errors = err.response.data.errors
          } else {
            this.$notify({
              group: 'bottom',
              title: 'Error',
              text: err?.response?.data?.message,
            }, 4000);
          }
        })
        .finally(() => this.isLoading = false);
    },

    getLovs() {
      PartialPaymentService.getLovs().then(res => {
        this.lovs.registers = res.data?.registers
        this.lovs.payees = res.data?.payees
        this.lovs.payee_types=res.data?.payee_types
        this.lovs.payment_types = res.data?.payment_types
      })
    },

    setPayeeTypeId(item) {
      this.lovs.payees.unshift(item)
      this.formValues.cause_of_losses[this.selectedPayeeIndex].payee_id = item.value
    },

    openDialog(index) {
      this.showDialog = true
      this.selectedPayeeIndex = index
    },

    hideDialog() {
      this.showDialog = false
    },
    filterRegisters(event) {
      setTimeout(() => {
        if (!event.query.trim().length) {
          this.filteredRegisters = [...this.lovs.registers];
        }
        else {
          this.filteredRegisters = this.lovs.registers.filter((item) => {
            return item.toLowerCase().startsWith(event.query.toLowerCase());
          });
        }
      }, 250);
    },

    listCauseOfLosses(value) {
      PartialPaymentService.listCauseOfLosses(value).then(res => {
        console.log(res.data)
        this.getVehicle(res.data[0]?.detail_id)
        this.formValues.cause_of_losses = res.data

      })
    },

    getVehicle(value) {
      PartialPaymentService.getVehicle(value).then(res => this.vehicle = res.data)
    },
    getData() {
      if (this.id) {
        PartialPaymentService.getData(this.id).then(res => {
          this.formValues = res.data
          this.getVehicle(this.formValues.detail_id);
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
  },
  mounted() {
    this.getLovs()
    this.getData()
  }
};
</script>