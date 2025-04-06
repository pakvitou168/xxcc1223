<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Full Payment
      </h2>
    </div>
    <div class="intro-y box grid gap-y-2 mt-5 p-5">
      <div class="grid lg:grid-cols-4 gap-5">
        <div>
          <label class="form-label">Claims No. *</label>
          <Dropdown v-model="formValues.claim_no" class="w-full" showClear optionLabel="label" optionValue="value"
            :filter="true" placeholder="Select claim no." :options="lovs.registers"
            @change="listCauseOfLosses($event.value)" />
        </div>
        <div v-if="vehicle.insured_name || vehicle.plate_no" class="flex items-center col-span-3">
          <div v-if="vehicle.insured_name" class="mt-6">Insured Name: <span class="font-bold">{{ vehicle.insured_name
              }}</span></div>
          <div class="mt-6" v-if="vehicle.plate_no"><span class="px-6">&nbsp;|&nbsp;</span>Vehicle: <span
              class="font-bold">{{ vehicle.plate_no }}</span></div>
        </div>
      </div>
      <div>
        <CauseOfLoss v-model="formValues.cause_of_losses" :payees="lovs.payees" :deductibles="deductibles"
          :paymentTypes="lovs.payment_types" :errors="errors" @openDialog="openDialog" />
      </div>
      <div class="grid lg:grid-cols-4 gap-x-5">
        <div>
          <label for="" class="form-label">Payment Date *</label>
          <Calendar v-model="formValues.payment_date" dateFormat="dd-M-yy" :disabled="true" />
        </div>
      </div>
      <div class="text-right mt-5">
        <router-link :to="{ name: 'ClaimProcessIndex' }" class="btn btn-outline-secondary w-24 mr-1">
          Cancel
        </router-link>
        <button type="submit" @click="previewDeductible" :disabled="isLoading" class="btn btn-primary w-40 mr-1">
          {{ previewIsLoading ? 'Processing ...' : 'Preview Deductible' }}
        </button>
        <button type="submit" @click="handleSubmit" :disabled="isLoading" class="btn btn-primary w-24">
          {{ isLoading ? 'Saving ...' : 'Save' }}
        </button>
      </div>
    </div>
    <PayeeDialog :isVisible="showDialog" :payeeTypes="lovs.payee_types" @hideDialog="hideDialog"
      @setPayeeTypeId="setPayeeTypeId" />
  </div>
</template>

<script>

import ProcessService from '@/services/claim/process.service'
import CauseOfLoss from '@/views/Claim/Process/Partials/CauseOfLoss.vue'
import PayeeDialog from '@/components/Dialogs/PayeeDialog.vue'
import moment from 'moment';

export default {
  components: {
    CauseOfLoss,
    PayeeDialog
  },

  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        claim_no: null,
        payment_date: new Date(),
        cause_of_losses: [],
      },
      vehicle: [],
      selectedPayeeIndex: null,
      lovs: {
        registers: [],
        payees: [],
        payee_types: {},
        payment_types: {}
      },
      deductibles: [],
      filteredRegisters: [],
      showDialog: false,
      errors: {},
      isLoading: false,
      previewIsLoading: false,
      coLRef:'coLRef'
    }
  },
  methods: {
    pushDeductible(ddts){
      const cofLoss = JSON.parse(JSON.stringify(this.formValues.cause_of_losses));
      ddts.forEach((ele,index) => {
        cofLoss[index].deductible = ele.deductible
      })
      this.formValues.cause_of_losses = cofLoss
    },
    previewDeductible() {
      this.previewIsLoading = true
      this.errors = {};
      const method = "POST"
      ProcessService.previewDeductible(this.formValues)
        .then(res => {
          if (!res.data.success) {
            notify(res.data?.message, 'error');
          } else {
            this.deductibles = res.data.data
            notify(res.data?.message, 'success');
          }
          // this.$router.push({ name: 'ClaimProcessIndex' })
        })
        .catch(err => {
          if (err?.response?.status === 422) {
            notify('Validation failed', 'error');
            this.errors = err.response.data.errors
          } else {
            notify(err?.response?.data?.message, 'error');
          }
        })
        .finally(() => this.previewIsLoading = false);
    },
    handleSubmit() {
      this.isLoading = true

      const method = this.id ? "PUT" : "POST"
      ProcessService.save(
        {
          ...this.formValues,
          ...(method === "PUT" && { id: this.id })
        },
        method,
      )
        .then(res => {
          if (!res.data.success) {
            notify(res.data?.message, 'error');
          } else {
            notify(res.data?.message, 'success');
            this.$router.push({ name: 'ClaimProcessIndex' })
          }
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

    getData() {
      if (this.id) {
        ProcessService.getData(this.id).then(res => {
          this.formValues = res.data
          this.formValues.payment_date = moment(res.data.payment_date).toDate()
          this.getVehicle(this.formValues?.detail_id);
        })
          .catch(err => {
            console.log(err)
            notify(err?.response?.data?.message, 'error');
          })
      }
    },
    getVehicle(detailId) {
      ProcessService.getVehicle(detailId).then(res => {
        this.vehicle = res.data
      })
    },
    getLovs() {
      ProcessService.getLovs().then(res => {
        this.lovs.registers = res.data?.registers
        this.lovs.payees = res.data?.payees
        this.lovs.payee_types = res.data?.payee_types,
          this.lovs.payment_types = res.data?.payment_types
      })
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

    setPayeeTypeId(item) {
      this.lovs.payees.unshift(item)
      this.formValues.cause_of_losses[this.selectedPayeeIndex].payee_id = item.value
    },

    listCauseOfLosses(value) {
      this.errors = [];
      ProcessService.listCauseOfLosses(value).then(res => {
        this.getVehicle(res.data[0].detail_id);
        this.formValues.cause_of_losses = res.data
      })
    },
    openDialog(index) {
      this.showDialog = true
      this.selectedPayeeIndex = index
    },
    hideDialog() {
      this.showDialog = false
    },
  },
  mounted() {
    this.getLovs()
    this.getData()
  }
};
</script>