<template>
  <Dialog :visible="isVisible" :style="{ width: '20rem' }" :modal="true" :closable="false" :header="header">
    <div class="grid grid-cols-1 gap-5">
      <div>
        <label class="form-label">Status  <span class="text-red-600">*</span></label>
        <Dropdown class="w-full" placeholder="Select status" :options="options" v-model="form.status"
                  optionLabel="label" optionValue="value" />
        <span class="text-error" v-if="errors['status']">{{ errors['status'][0] }}</span>
      </div>
      <div>
        <label class="form-label">Remark  <span class="text-red-600">*</span></label>
        <Textarea class="w-full" placeholder="Remark" v-model="form.reason" rows="4" />
        <span class="text-error" v-if="errors['reason']">{{ errors['reason'][0] }}</span>
      </div>
    </div>
    <template #footer>
      <Button label="Cancel" class="p-button-danger p-button-text" outlined @click="hideDialog" />
      <Button label="Confirm" type="button" class="p-button-info" :loading="saving" autofocus @click="handleSubmit" />
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
    saving: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!",
      SUCCESS_MESSAGE:"Success!",
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
    hideDialog() {
      this.form = {
        reason: '',
        status: '',
      }
      this.$emit('hideDialog', this.form)
    },
    handleSubmit() {
      if (!this.form.status) {
        this.errors.status = ['Status is required']
        return false
      }else{
        this.errors.status = ['']
      }
      if (!this.form.reason) {
        this.errors.reason = ['Remark is required']
        return false
      }else{
        this.errors.reason = []
      }
      this.resetForm()
      this.$emit('confirm', this.form)
    },
    resetForm() {
      this.errors = [];
    },
    initForm() {
      this.form.status = "APV"
    }

  },
  mounted() {
    this.initForm()
  }
}
</script>