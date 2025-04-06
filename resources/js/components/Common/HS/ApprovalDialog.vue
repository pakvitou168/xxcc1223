<template>
  <Dialog
    :visible="visible"
    :containerStyle="{width: '340px'}"
    :modal="true"
    :closable="false"
    :header="header"
  >
    <FormulateInput
      type="radio"
      label="Status *"
      v-model="form.status"
      :options="options"
      :value="value"
      input-class="w-1/2 inline-block"
    />
    <FormulateInput
      type="textarea"
      label="Reason *"
      placeholder="Reason"
      rows="3"
      v-model="form.reason"
      :error="errorReason"
    />
    <template #footer>
      <Button label="Cancel" class="p-button-danger p-button-text" @click="() => {
        form = {
          status: '',
          reason: '',
        }

        $emit('hide')
      }"/>
      <Button label="Confirm" autofocus :disabled="submitting" :loading="submitting" @click="$emit('confirm', form)" />
    </template>
  </Dialog>
</template>

<script>
export default {
  props: {
    header: String,
    isVisible: Boolean,
    submitting: Boolean,
    options: Object,
    value: String,
  },
  data() {
    return {
      form: {
        reason: '',
        status: '',
      }
    }
  },
  computed: {
    errorReason() {
      return (this.submitting && !this.form.reason) ? 'Reason is required.' : null
    },
  },
}
</script>