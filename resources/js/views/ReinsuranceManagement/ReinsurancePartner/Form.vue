<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Reinsurance Partner
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
            <label class="mb-1 block font-bold">Group Code *</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.group_code"
              :options="groupCode"
              optionLabel="label"
              optionValue="value" 
              placeholder="Group Code"
            />
            <span v-if="errors.group_code" class="text-theme-6">
              {{ errors.group_code[0] }}
            </span>
          </div>

          <div class="col-span-12">
            <label class="mb-1 block font-bold">Description</label>
            <Textarea
              class="w-full"
              v-model="formValues.description"
              rows="3" 
              placeholder="Description"
            />
          </div>

          <div class="col-span-12 text-right mt-5">
            <router-link :to="{ name: 'ReinsurancePartnerIndex' }" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
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
      formValues: {
        code: '',
        name: '',
        group_code: '',
        description: ''
      },
      errors: {},
      groupCode: [] // Will be transformed to array of objects
    };
  },
  methods: {
    getReinsurance() {
      if (this.id) {
        axios.get(`/reinsurance-partners/${this.id}/edit`).then((response) => {
          console.log('Reinsurance Data:', response.data);
          this.formValues = {
            code: response.data.code || '',
            name: response.data.name || '',
            group_code: response.data.group_code || '',
            description: response.data.description || ''
          };
        });
      }
    },

    listGroupCode(){
        axios.get("/reinsurance-partners-service/get-group-code").then((response) => {
            console.log('Group Code Data:', response.data);
            // Transform array of strings to array of objects for dropdown
            this.groupCode = response.data.map(code => ({
                label: code.replace(/_/g, ' '), // Replace underscores with spaces for display
                value: code
            }));
            console.log('Transformed Group Codes:', this.groupCode);
        });
    },

    async handleSubmit() {
      try {
        const url = this.id ? `/reinsurance-partners/${this.id}` : '/reinsurance-partners';
        const method = this.id ? 'put' : 'post';
        
        const response = await axios[method](url, this.formValues);
        
        if (response.data.success) {
          notify(response.data.message, 'success', 'bottom-right');
          this.$router.push({ name: 'ReinsurancePartnerIndex' });
        }
      } catch (err) {
        if (err?.response?.status === 422) {
          this.errors = err.response.data.errors;
        } else {
          console.error('Error saving reinsurance partner:', err);
          notify('Error saving reinsurance partner', 'error', 'bottom-right');
        }
      }
    }
  },

  mounted() {
    this.getReinsurance();
    this.listGroupCode();
  },
};
</script>
