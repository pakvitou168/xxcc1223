<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Function
      </h2>
    </div>
    <div class="grid grid-cols-12 mt-5">
      <div class="intro-y box col-span-12 lg:col-span-6 p-5">
        <FormulateForm v-model="formValues" @submit="handleSubmit">
          <FormulateInput
            type="text"
            name="code"
            label="Code *"
            validation="required|max:50"
            validationName="Code"
            placeholder="Code"
            :error="errors.code ? errors.code[0] : null"
            />

          <FormulateInput
            type="text"
            name="name"
            label="Name *"
            validation="required|max:100"
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
            type="vue-select"
            name="permission"
            label="Permissions"
            multiple
            placeholder="Permissions"
            :options="permissionOptions"
            />

          <FormulateInput
            type="vue-select"
            name="status"
            label="Status *"
            validation="required"
            validationName="Status"
            placeholder="Status"
            :options="statusOptions"
          />

          <div class="text-right mt-5">
            <router-link to="/user-management/functions" class="btn btn-outline-secondary w-24 mr-1" tag="button">Cancel</router-link>
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

      permissionOptions: {},
      statusOptions: {},
    }
  },
  methods: {
    listPermissions() {
      axios.get('/function-service/list-permissions').then(response => {
        this.permissionOptions = response.data
      })
    },
    listStatus() {
      axios.get('/function-service/list-statuses').then(response => {
        this.statusOptions = response.data
      })
    },

    handleSubmit() {
      if (!this.id) {
        axios.post('/functions', this.formValues).then(response => {
          if (response.data.success) {
            this.toastMessage(response.data.message, 'Success')
            this.$router.push({name: 'FunctionIndex'})
          }
        }).catch(err => {
          if (err.response.status === 422) {
            this.errors = err.response.data.errors
          } else {
            this.toastMessage('Error', 'Error')
          }
        })
      } else {
        axios.put(`/functions/${this.id}`, this.formValues).then(response => {
          if (response.data.success) {
            this.toastMessage(response.data.message, 'Success')
            this.$router.push({name: 'FunctionIndex'})
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

    getFunction() {
      if (this.id) {
        axios.get(`/functions/${this.id}`).then(response => {
          this.formValues = response.data;
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
  mounted() {
    this.getFunction()
    this.listPermissions()
    this.listStatus()
  },
}
</script>
