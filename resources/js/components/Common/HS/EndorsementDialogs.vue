<template>
  <Dialog
      :visible="visible"
      :containerStyle="{width: '340px'}"
      :modal="true"
      :closable="false"
      :header="header"
  >
    <FormulateInput
        v-model="form.type"
        type="select"
        name="endorsement_type"
        label="Endorsement Type *"
        :options="types"
        :error="typeErrors[0]"
    />
    <FormulateInput
        v-model="form.effective_date"
        type="date"
        name="endorsement_e_date"
        label="Endorsement Effective Date *"
        :min="validFromDate"
        :max="validToDate"
        :error="effectiveDateErrors[0]"
    />
    <FormulateInput
        v-model="form.description"
        type="textarea"
        name="endorsement_description"
        label="Endorsement Description"
        rows="4"
    />
    <template #footer>
      <Button label="Cancel" class="p-button-danger p-button-text" @click="() => {
            form = {
                endorsement_type: '',
                endorsement_e_date: null,
                endorsement_description: '',
            }

            $emit('hide')
        }" />
      <Button label="Confirm" autofocus :disabled="submitting" :loading="submitting" @click="$emit('confirm', form)" />
    </template>
  </Dialog>
</template>

<script>
export default {
  props: {
    header: String,
    visible: Boolean,
    submitting: Boolean,
    types: Object,
    validFromDate: {
      type: String,
      default: '',
    },
    validToDate: {
      type: String,
      default: '',
    },
    typeErrors: {
      type: Array,
      default: [],
    },
    effectiveDateErrors: {
      type: Array,
      default: [],
    }

  },
  data() {
    return {
      form: {
        type: '',
        effective_date: null,
        description: '',
      }
    }
  },
}
</script>