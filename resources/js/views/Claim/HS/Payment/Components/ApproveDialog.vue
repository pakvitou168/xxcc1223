<template>
  <Dialog
    :visible="isVisible"
    :style="{ width: '340px' }"
    :modal="true"
    :closable="false"
    :header="header"
    @hide="handleDialogHide"
  >
    <div class="grid gap-y-5">
      <div v-if="hasOption">
        <label for="status-options" class="form-label">Status *</label>
        <div class="w-full grid grid-cols-2">
          <div
            class="align-items-center mr-2"
            v-for="(option, index) in options"
            :key="option.value"
          >
            <RadioButton
              v-model="form.status"
              :inputId="'status-option-' + index"
              :name="option.label"
              :value="option.value"
              :disabled="submitted"
            />
            <label :for="'status-option-' + index" class="ml-2">{{ option.label }}</label>
          </div>
        </div>
        <span class="p-error block" v-if="errors.status">{{ errors.status }}</span>
      </div>
      <div>
        <label for="comment" class="form-label">Remark *</label>
        <Textarea
          id="comment"
          v-model="form.comment"
          placeholder="Enter your remark here"
          class="w-full"
          rows="4"
          :disabled="submitted"
          aria-required="true"
          :class="{ 'p-invalid': errors.comment }"
        />
        <span class="p-error block" v-if="errors.comment">{{ errors.comment }}</span>
      </div>
    </div>
    <template #footer>
      <Button
        label="Cancel"
        class="p-button-secondary"
        @click="hideDialog"
        :disabled="submitted"
        icon="pi pi-times"
      />
      <Button
        label="Confirm"
        class="p-button-info"
        :loading="submitted"
        :disabled="isSubmitDisabled"
        icon="pi pi-check"
        autofocus
        @click.prevent="handleSubmit"
      />
    </template>
  </Dialog>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Textarea from 'primevue/textarea';
import RadioButton from 'primevue/radiobutton';

// Define props
const props = defineProps({
  header: {
    type: String,
    default: 'Approve'
  },
  isVisible: {
    type: Boolean,
    default: false
  },
  submitted: {
    type: Boolean,
    default: false
  },
  isLoading: {
    type: Boolean,
    default: false
  },
  options: {
    type: Object,
    default: () => ({})
  },
  value: {
    type: String,
    default: ''
  }
});

const ERROR_MSG = ref("Something went wrong!");
const SUCCESS_MSG = ref("Success!");
// Define emits
const emit = defineEmits(['hide-dialog', 'confirm']);

// Form state
const form = ref({
  comment: '',
  status: props.value || ''
});

// Error state
const errors = ref({
  comment: '',
  status: ''
});

// Computed properties
const hasOption = computed(() => {
  return Object.keys(props.options).length > 0;
});

const isSubmitDisabled = computed(() => {
  return (
    props.submitted ||
    (!form.value.comment.trim()) ||
    (hasOption.value && !form.value.status)
  );
});

// Methods
function validateForm() {
  errors.value = {};
  let isValid = true;

  if (!form.value.comment.trim()) {
    errors.value.comment = 'Remark is required.';
    isValid = false;
  } else if (form.value.comment.trim().length < 3) {
    errors.value.comment = 'Remark must be at least 3 characters.';
    isValid = false;
  }

  if (hasOption.value && !form.value.status) {
    errors.value.status = 'Status selection is required.';
    isValid = false;
  }

  return isValid;
}

function resetForm() {
  form.value = {
    comment: '',
    status: props.value || ''
  };
  errors.value = {};
}

function hideDialog() {
  resetForm();
  emit('hide-dialog');
}

function handleDialogHide() {
  if (!props.submitted) {
    hideDialog();
  }
}

function handleSubmit() {
  if (validateForm()) {
    emit('confirm', { ...form.value });
  }
}

// Watchers
watch(() => props.isVisible, (newValue) => {
  if (newValue) {
    // Reset form when dialog becomes visible
    resetForm();
  }
});

watch(() => props.value, (newValue) => {
  form.value.status = newValue || '';
});

// Lifecycle hooks
onMounted(() => {
  // Initialize form status with prop value
  if (props.value) {
    form.value.status = props.value;
  }
});
</script>