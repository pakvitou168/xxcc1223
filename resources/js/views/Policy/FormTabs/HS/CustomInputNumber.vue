<!-- CustomInputNumber.vue -->
<template>
  <div>
    <InputNumber
      :modelValue="modelValue"
      :class="['w-full', errorClass]"
      :placeholder="placeholder"
      :min="min"
      :max="max"
      :maxFractionDigits="maxFractionDigits"
      :required="required"
      :disabled="disabled"
      @update:modelValue="handleInput"
      @blur="handleBlur"
    />
    <small v-if="error" class="text-red-500">{{ error }}</small>
  </div>
</template>

<script>
export default {
  name: 'CustomInputNumber',
  props: {
    modelValue: {
      type: [Number, null],
      default: null
    },
    placeholder: {
      type: String,
      default: ''
    },
    min: {
      type: Number,
      default: 0
    },
    max: {
      type: Number,
      default: 100
    },
    maxFractionDigits: {
      type: Number,
      default: 5
    },
    required: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    validationName: {
      type: String,
      default: 'Field'
    }
  },

  emits: ['update:modelValue'],

  data() {

    return {
      ERROR_MESSAGE: "Something went wrong!",
      SUCCESS_MESSAGE: "Success!",
      error: ''
    }
  },

  computed: {
    errorClass() {
      return this.error ? 'border-red-500' : ''
    }
  },

  methods: {
    validate(value) {
      if (this.required && (value === null || value === undefined || isNaN(value))) {
        this.error = `${this.validationName} is required`
        return false
      }
      if (value !== null && value < this.min) {
        this.error = `${this.validationName} must be at least ${this.min}`
        return false
      }
      this.error = ''
      return true
    },

    handleInput(value) {
      if (value === null || (typeof value === 'number' && !isNaN(value))) {
        this.$emit('update:modelValue', value)
        this.validate(value)
      }
    },

    handleBlur() {
      this.validate(this.modelValue)
    }
  }
}
</script>