<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Reinsurance Partner Group
      </h2>
    </div>
    <div class="grid grid-cols-12 mt-5">
      <div class="intro-y box col-span-12 lg:col-span-6 p-5">
          <div class="grid grid-cols-12 gap-x-10 gap-y-4">
          <div class="col-span-12">
            <label class="mb-1 block font-bold">Code *</label>
            <InputText
            class="w-full p-inputtext-sm"
            v-model="formValues.code" 
            placeholder="Code"
            />
            <span v-if="errors.code" class="text-theme-6">
            {{ errors.code[0] }}
            </span>
          </div>

          <div class="col-span-12">
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

          <div class="col-span-12">
            <label class="mb-1 block font-bold">Description</label>
            <Textarea
            class="w-full" 
            rows="3"
            v-model="formValues.description"
            placeholder="Description"
            />
          </div>

          <div class="text-right mt-5 col-span-12">
            <router-link 
            :to="{ name: 'ReinsurancePartnerGroupIndex' }"
            class="btn btn-outline-secondary w-24 mr-1"
            >
            Cancel
            </router-link>
            <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
            <span v-if="id">Update</span>
            <span v-else>Create</span>
            </button>
          </div>
          </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {},
      errors: {},
    };
  },
  methods: {
    getReinsuranceGroup() {
      if (this.id) {
        axios
          .get(`/reinsurance-partner-groups/${this.id}/edit`)
          .then((response) => {
            this.formValues = response.data;
            if (response.data?.error)
              notify(response.data.message, "error" ,'bottom-right');
          });
      }
    },

    async handleSubmit() {
    try {
        const url = this.id
            ? `/reinsurance-partner-groups/${this.id}`
            : '/reinsurance-partner-groups';
        const method = this.id ? 'put' : 'post';
        
        const response = await axios[method](url, this.formValues);
                
        if (response.data.success) {
            notify(response.data.message, 'success', 'bottom-right');
            this.$router.push({ name: 'ReinsurancePartnerGroupIndex' });
        }
    } catch (err) {
        console.log('Validation Error Response:', err.response?.data); // Add this line
        if (err?.response?.status === 422) {
            this.errors = err.response.data.errors;
        } else {
            notify(err?.response?.data?.message || 'Something went wrong!', 'error', 'bottom-right');
        }
    }
  },
  },

  mounted() {
    this.getReinsuranceGroup();
  },
};
</script>
