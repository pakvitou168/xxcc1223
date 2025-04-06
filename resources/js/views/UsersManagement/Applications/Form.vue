<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Application
      </h2>
    </div>
    <div class="grid grid-cols-12 mt-5">
      <div class="intro-y box col-span-12 lg:col-span-6 p-5">
        <FormulateForm v-model="formValues" @submit="handleSubmit">
          <FormulateInput
            type="text"
            name="code"
            label="Code *"
            validation="required|max:10"
            validationName="Code"
            placeholder="Code"
            :error="errors.code ? errors.code[0] : null"
            />

          <FormulateInput
            type="text"
            name="name"
            label="Name *"
            validation="required|max:50"
            validationName="Name"
            placeholder="Name"
            />

          <FormulateInput
            type="textarea"
            name="description"
            label="Description"
            validation="max:250"
            validationName="Description"
            placeholder="Description"
            rows="3"
            />

          <FormulateInput
            type="number"
            min="0"
            name="level"
            label="Access Level *"
            validation="required|min:0"
            validationName="Access Level"
            placeholder="Access Level"
            />

          <FormulateInput
            type="checkbox"
            name="allow_change_username"
            label="Allow Change Username"
            />

          <div class="text-right mt-5">
            <router-link to="/user-management/applications" class="btn btn-outline-secondary w-24 mr-1" tag="button">Cancel</router-link>
            <button type="submit" class="btn btn-primary w-24">
              <span v-if="id">Update</span>
              <span v-else>Create</span>
            </button>
          </div>
        </FormulateForm>
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
      errors: [],
    }
  },
  methods: {
    handleSubmit() {
      if (!this.id) {
        axios.post('/applications', this.formValues).then(response => {
          if (response.data.success) {
            this.toastMessage(response.data.message, 'Success')
            this.$router.push({name: 'ApplicationIndex'})
          }
        }).catch(err => {
          if (err.response.status === 422) {
            this.errors = err.response.data.errors
          } else {
            this.toastMessage('Error', 'Error')
          }
        })
      } else {
        axios.put(`/applications/${this.id}`, this.formValues).then(response => {
          if (response.data.success) {
            this.toastMessage(response.data.message, 'Success')
            this.$router.push({name: 'ApplicationIndex'})
          }
        }).catch(err => {
          if (err.response.status === 422) {
            this.errors = err.response.data.errors
          } else {
            this.toastMessage('Error', 'Error')
          }
        })
      }
    },
    getApplication() {
      if (this.id) {
        axios.get(`/applications/${this.id}`).then(response => {
          this.formValues = response.data;
          this.formValues.allow_change_username = this.formValues.allow_change_username === 'Y' ? true : false
        })
      }
    },
    toastMessage(msg, type, position = 'bottom') {
      this.$notify({
        group: position,
        title: type,
        text: msg
      }, 4000);
    }
  },
  mounted(){
    this.getApplication()
  } 
}
</script>