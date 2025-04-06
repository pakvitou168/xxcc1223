<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Cause of Loss
      </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
      <div class="intro-y col-span-12 lg:col-span-6">
    <div class="intro-y box p-5">
      <div class="grid grid-cols-12 gap-x-10 gap-y-4">
      <div class="col-span-12">
        <label class="mb-1 block font-bold">Cause Code *</label>
        <InputText
        class="w-full p-inputtext-sm"
        v-model="formValues.code"
        placeholder="Cause Code"
        />
        <span v-if="errors.code" class="text-theme-6">
        {{ errors.code[0] }}
        </span>
      </div>

      <div class="col-span-12">
        <label class="mb-1 block font-bold">Cause Name (English) *</label>
        <InputText
        class="w-full p-inputtext-sm"
        v-model="formValues.cause_name"
        placeholder="Cause Name (English)"
        />
        <span v-if="errors.cause_name" class="text-theme-6">
        {{ errors.cause_name[0] }}
        </span>
      </div>

      <div class="col-span-12">
        <label class="mb-1 block font-bold">Cause Name (Khmer)</label>
        <InputText
        class="w-full p-inputtext-sm"
        v-model="formValues.cause_name_kh"
        placeholder="Cause Name (Khmer)"
        />
        <span v-if="errors.cause_name_kh" class="text-theme-6">
        {{ errors.cause_name_kh[0] }}
        </span>
      </div>
      </div>

      <div class="text-right mt-5">
      <router-link
        :to="{name: 'CauseOfLossIndex'}"
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

import CauseOfLossService from '@/services/claim/cause_of_loss.service'

export default {
  data() {
  return {
    id: this.$route.params.id ?? null,
    formValues: {
    code: '',
    cause_name: '',
    },
    errors: [],
    isLoading: false,
  }
  },

  methods: {
  handleSubmit() {
    this.isLoading = true

    const method = this.id ? "PUT" : "POST"
    CauseOfLossService.save(
    {
    ...this.formValues,
    ...(method === "PUT" && { id: this.id })
    },
    method
    ).then(res => {
    notify(res.data?.message, 'success', 'bottom-right');
    this.$router.push({name: 'CauseOfLossIndex'})
    })
    .catch(err => {
    if (err?.response?.status === 422) {
      notify('Validation Error', 'error', 'bottom-right');
      this.errors = err.response.data.errors
    } else {
      notify(err?.response?.data?.message || 'Error saving data', 'error', 'bottom-right');
    }
    })
    .finally(() => this.isLoading = false);
  },

  getData() {
    if (this.id) {
    CauseOfLossService.getData(this.id).then(res => this.formValues = res.data)
    .catch(err => {
      notify(err?.response?.data?.message || 'Error loading data', 'error', 'bottom-right');
    })
    }
  },
  },

  mounted() {
    this.getData()
  }
}
</script>