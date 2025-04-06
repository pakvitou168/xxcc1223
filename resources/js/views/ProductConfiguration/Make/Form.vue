<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Make
      </h2>
    </div>
    <div class="grid grid-cols-12 mt-5">
      <div class="intro-y box col-span-12 lg:col-span-6 p-5">
        <div class="grid grid-cols-12 gap-x-10 gap-y-4">
      
            <div class="col-span-12">
            <label class="mb-1 block font-bold">Make *</label>
            <InputText
            class="w-full p-inputtext-sm"
            v-model="formValues.make" 
            placeholder="Make"
            />
            <span v-if="errors.make" class="text-theme-6">
              {{ errors.make[0] }}
            </span>
            </div>
          
          <div class="col-span-12">
            <label class="mb-1 block font-bold">Description</label>
            <InputText
            class="w-full p-inputtext-sm"
            v-model="formValues.description"
            placeholder="Description"
            />
          </div>
          <div class="grid grid-cols-12 gap-x-6">
            
            <div class="col-span-12 lg:col-span-6">
              <label class="block mb-1 font-bold">Offline</label>
              <Checkbox
                v-model="formValues.available_offline"
                :binary="true"
              />
            </div>

            <div class="col-span-12 lg:col-span-6">
              <label class="block mb-1 font-bold">Online</label>
              <Checkbox
                v-model="formValues.available_online"
                :binary="true"
              />
            </div>
          </div>
        </div>

        <div class="text-right mt-5">
           <router-link to="/product-configuration/makes" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
           <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
             <span v-if="id">Update</span>
             <span v-else>Create</span>
           </button>
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
    }
  },
  methods: {
    getMake() {
      if (this.id) {
        axios.get(`/makes/${this.id}`).then(response => {
          this.formValues = response.data
        })
      }
    },
    async handleSubmit() {
      try {
        const url = this.id ? `/makes/${this.id}` : '/makes';
        const response = this.id 
          ? await axios.put(url, this.formValues)
          : await axios.post(url, this.formValues);

        if (response.data.success) {
          notify(response.data.message, 'success', 'bottom-right')
          this.$router.push({ name: 'MakeIndex' })
        }
      } catch (err) {
        if (err.response?.status === 422) {
          this.errors = err.response.data.errors
        }
      }
    }
  },
  mounted() {
    this.getMake()
  }
}
</script>