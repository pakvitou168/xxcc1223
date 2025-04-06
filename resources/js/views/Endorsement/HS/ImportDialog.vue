<template>
  <Dialog
    :visible="visible"
    :containerStyle="{width: '340px'}"
    :modal="true"
    :closable="false"
    :header="header"
  >
    <FormulateInput
      v-model="form.file"
      type="file"
      file
      @file-removed="() => {form.file = null}"
      name="file"
      label="Import File"
      accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
      validation="mime:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
    />
    <template #footer>
      <Button label="Cancel" class="btn btn-danger" @click="() => {
                form = {
                    file: ''
                }
                
                $emit('hide')
            }"/>
      <Button class="btn btn-primary" label="Confirm" autofocus :disabled="submitting" :loading="submitting"
              @click="handleSubmit"/>
    </template>
  </Dialog>
</template>

<script>
export default {
  props: {
    header: String,
    visible: Boolean,
    submitting: Boolean
  },
  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!",
      SUCCESS_MESSAGE:"Success!",
      form: {
        file: ''
      }
    }
  },
  methods: {
    handleSubmit() {
      if (this.form.file) {
        let formData = new FormData();
        formData.append('file', this.form.file.files[0].file)
        this.$emit('confirm', formData)
      } else {
        this.$emit('noFileAttached')
      }
    }
  }
}
</script>