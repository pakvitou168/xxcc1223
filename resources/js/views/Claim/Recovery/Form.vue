<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        Edit Deductible/Salvage
      </h2>
    </div>
    <div class="intro-y box grid gap-y-5 mt-5 p-5">
      <div class="grid lg:grid-cols-4 gap-x-5 pb-5 border-b">
        <div class="flex items-center">
          <div class="text-lg">Claim No.: <span class="font-bold">{{ formValues.claim_no }}</span></div>
        </div>
        <div class="flex items-center">
          <div class="text-lg">Insured Name: <span class="font-bold">{{ formValues.insured_name }}</span></div>
        </div>
      </div>
      <div class="formulate-input">
        <CauseOfLoss v-model="formValues.cause_of_losses" :paymentTypes="lovs.payment_types" :errors="errors" />
        <ul v-if="errors.cause_of_losses" class="formulate-input-errors">
          <li class="formulate-input-error">{{ errors.cause_of_losses[0] }}</li>
        </ul>
      </div>
      <div class="text-right mt-5">
        <router-link :to="{name: 'ClaimRecoveryIndex'}" class="btn btn-outline-secondary w-24 mr-1">
          Cancel
        </router-link>
        <button type="submit" @click="handleSubmit" :disabled="isLoading" class="btn btn-primary w-24">
          {{ isLoading ? 'Saving ...' : 'Save' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>

import CauseOfLoss from '@/views/Claim/Recovery/Partials/CauseOfLoss.vue'
import RecoveryService from '@/services/claim/recovery.service'

export default {
  components: {
    CauseOfLoss,
  },

  props: {
    paymentTypes: Object,
  },

  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        claim_no: '',
        insured_name: '',
        cause_of_losses: [],
      },

      lovs: {
        payment_types: {},
      },

      errors: [],
      isLoading: false,
    }
  },
  methods: {
    handleSubmit() {
      this.isLoading = true

      const method = this.id ? "PUT" : "POST"
      RecoveryService.save(
        {
          ...this.formValues,
          ...(method === "PUT" && { id: this.id })
        },
        method,
      )
        .then(res => {
          notify(res.data?.message,'success');
          this.$router.push({ name: 'ClaimRecoveryIndex' })
        })
        .catch(err => {
          if (err?.response?.status === 422) {
            notify('Validation failed','error');
            this.errors = err.response.data.errors
          } else {
            notify(err?.response?.data?.message,'error');
          }
        })
        .finally(() => this.isLoading = false);
    },
    getData() {
      if (this.id) {
        RecoveryService.getData(this.id).then(res => {
            this.formValues = res.data
          })
          .catch(err => {
            console.log(err)
            notify(err?.response?.data?.message,'error');
          })
      }
    },
    getLovs() {
      RecoveryService.getLovs().then(res => {
        this.lovs.payment_types = res.data?.payment_types
      })
    },
  },
  mounted() {
    this.getLovs()
    this.getData()
  },
}
</script>