<template>
  <Dialog
    :visible="isVisible" :style="{ width: '20rem' }" :modal="true" :closable="false" :header="header"
  >
    <div class="grid grid-cols-1 gap-5">
      <div>
        <label for="" class="block font-semibold mb-1">Status *</label>
        <Dropdown :options="options" class="w-full" v-model="form.status" optionLabel="label"
                  optionValue="value" placeholder="Select status"></Dropdown>
        <span v-if="errors['status']" class="text-xs text-red-500">{{ errors['status'] }}</span>
      </div>
      <div>
        <label for="" class="block font-semibold mb-1">Remark *</label>
        <Textarea class="w-full" rows="3" v-model="form.reason"></Textarea>
        <span v-if="errors['reason']" class="text-xs text-red-500">{{ errors['reason'] }}</span>
      </div>
    </div>
    <template #footer>
      <Button label="Cancel" severity="danger" outlined @click="hideDialog"/>
      <Button label="Confirm"
              class="p-button-info px-2 py-2 border border-blue-500 text-white hover:bg-blue-600 bg-blue-500"
              :loading="submitted" autofocus @click="confirm"/>
    </template>
  </Dialog>
</template>

<script>
export default {
  props: {
    header: String,
    isVisible: Boolean,
    submitted: Boolean,
    options: Array,
    value: String,
  },
  data() {
    return {
      ERROR_MESSAGE: "Something went wrong!",
      SUCCESS_MESSAGE: "Success!",
      form: {
        reason: '',
        status: '',
      },
      errors: []
    }
  },
  computed: {
    errorReason() {
      return (this.submitted && !this.form.reason) ? 'Reason is required.' : null
    },
  },
  methods: {
    confirm() {
      this.errors = []
      if (!this.form.status) {
        this.errors.status = 'Status is required'
        return false
      }
      if (!this.form.reason) {
        this.errors.reason = 'Reason is required'
        return false
      }
      this.errors = []
      this.$emit('confirm', this.form);
    },
    hideDialog() {
      this.form = {
        reason: '',
        status: '',
      }

      this.$emit('hideDialog', this.form)
    }
  },
}
</script>