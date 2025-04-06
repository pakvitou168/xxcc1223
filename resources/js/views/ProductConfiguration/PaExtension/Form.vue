<template>
    <div>
      <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
          <span v-if="id">Edit</span>
          <span v-else>Create</span>
          Extension Option
        </h2>
      </div>
      <div class="grid grid-cols-12 mt-5">
        <div class="intro-y box col-span-12 lg:col-span-6 p-5">
          <div v-if="apiError" class="bg-theme-12 text-theme-6 p-4 mb-4 rounded">
            <p class="font-bold">API Error:</p>
            <p>{{ apiError }}</p>
          </div>

          <div class="grid grid-cols-12 gap-x-10 gap-y-4">
              <div class="col-span-12">
                <label for="name" class="mb-1 block font-bold">Name (English) *</label>
                <InputText
                  id="name"
                  name="name"
                  class="w-full p-inputtext-sm"
                  v-model="formValues.name"
                  placeholder="Name in English"
                />
                <span v-if="errors.name" class="text-theme-6">
                  {{ errors.name[0] }}
                </span>
              </div>

              <div class="col-span-12">
                <label for="nameKh" class="mb-1 block font-bold">Name (Khmer)</label>
                <InputText
                  id="nameKh"
                  name="nameKh"
                  class="w-full p-inputtext-sm"
                  v-model="formValues.nameKh"
                  placeholder="Name in Khmer"
                />
                <span v-if="errors.nameKh" class="text-theme-6">
                  {{ errors.nameKh[0] }}
                </span>
              </div>

              <div class="col-span-12">
                <label for="description" class="mb-1 block font-bold">Description (English)</label>
                <Textarea
                  id="description"
                  name="description"
                  class="w-full"
                  v-model="formValues.description"
                  placeholder="Description in English"
                  rows="3"
                />
              </div>

              <div class="col-span-12">
                <label for="descriptionKh" class="mb-1 block font-bold">Description (Khmer)</label>
                <Textarea
                  id="descriptionKh"
                  name="descriptionKh"
                  class="w-full"
                  v-model="formValues.descriptionKh"
                  placeholder="Description in Khmer"
                  rows="3"
                />
              </div>
          </div>

          <div class="text-right mt-5">
             <router-link to="/product-configuration/pa-extensions" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
             <button type="button" @click="handleSubmit" :disabled="loading" class="btn btn-primary w-24">
               <span v-if="loading">Loading...</span>
               <span v-else-if="id">Update</span>
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
        formValues: {
          name: '',
          nameKh: '',
          description: '',
          descriptionKh: ''
        },
        errors: {},
        loading: false,
        apiError: null
      }
    },
    methods: {
      getExtensionOption() {
        if (this.id) {
          this.loading = true;
          this.apiError = null;

          axios.get(`/extension-options/${this.id}`)
            .then(response => {
              console.log("Got extension option data:", response.data);
              this.formValues = {
                name: response.data.name || '',
                nameKh: response.data.nameKh || '',
                description: response.data.description || '',
                descriptionKh: response.data.descriptionKh || ''
              };
            })
            .catch(error => {
              console.error("Error fetching extension option:", error);
              this.apiError = `Error fetching data: ${error.response?.data?.message || error.message}`;
              notify('Error fetching data', 'error', 'bottom-right');
            })
            .finally(() => {
              this.loading = false;
            });
        }
      },
      async handleSubmit() {
        try {
          this.loading = true;
          this.apiError = null;
          this.errors = {};
          const apiData = {
            name: this.formValues.name,
            nameKh: this.formValues.nameKh || '',
            description: this.formValues.description || '',
            descriptionKh: this.formValues.descriptionKh || ''
          };

          console.log("Submitting data:", apiData);

          const url = this.id ? `/extension-options/${this.id}` : '/extension-options';
          const response = this.id
            ? await axios.put(url, apiData)
            : await axios.post(url, apiData);

          console.log("API Response:", response.data);

          if (response.data.success) {
            notify(response.data.message, 'success', 'bottom-right')
            this.$router.push({ name: 'ProductConditionIndex' })
          } else {
            this.apiError = response.data.message || "Unknown error occurred";
            notify(this.apiError, 'error', 'bottom-right');
          }
        } catch (err) {
          console.error("API Error:", err);
          if (err.response?.status === 422) {
            this.errors = err.response.data.errors;
          } else if (err.response?.data?.message) {
            this.apiError = err.response.data.message;
          } else {
            this.apiError = err.message || "Unknown error occurred";
          }

          notify(this.apiError, 'error', 'bottom-right');
        } finally {
          this.loading = false;
        }
      }
    },
    mounted() {
      this.getExtensionOption()
    }
  }
  </script>
