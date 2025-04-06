<template>
  <div class="p-4 border-l border-r border-t border-gray-200 ">
    <div class="grid grid-cols-4 gap-x-2 items-center">
      <div>
        <label class="font-bold block mb-1 text-sm font-medium">Customer Type *</label>
        <Dropdown
            v-model="localValue.customer_type"
            class="w-full border-0"
            optionLabel="label"
            optionValue="value"
            :options="customerTypes"
            :filter="true"
            :showClear="true"
            placeholder="Select Customer Type"
            :required="true"
            :class="['w-full p-inputtext', {'': errors.customer_type}]"
            @change="listCustomers"
        />
        <div class="h-5">
          <small v-if="errors.customer_type" class="p-error block mt-1">{{ errors.customer_type[0] }}</small>
        </div>
      </div>
      <div>
        <label class="font-bold block mb-1 text-sm font-medium">Customer Name *</label>
        <Dropdown
            v-model="localValue.customer_no"
            class="w-full"
            optionLabel="label"
            optionValue="value"
            :options="customers"
            :filter="true"
            :showClear="true"
            placeholder="Select Customer Name"
            :required="true"
            :class="['w-full p-inputtext', {'': errors.customer_no}]"
        />
        <div class="h-5">
          <small v-if="errors.customer_no" class="p-error block mt-1">
            {{ errors.customer_no[0] }}
          </small>
        </div>
      </div>
      <div>
        <label class="font-bold block mb-1 text-sm font-medium" for="">Joint Level *</label>
        <Dropdown
            v-model="localValue.joint_level"
            class="w-full"
            optionLabel="label"
            optionValue="value"
            :options="jointLevels"
            placeholder="Select Joint Level"
            :required="true"
        />
        <div class="h-5">
          <small v-if="errors.joint_level" class="p-error block mt-1">
            {{ errors.joint_level[0] }}
          </small>
        </div>
      </div>
      <div class="relative">
        <label for="tax_fee" class=" font-bold block mb-1 text-sm font-medium">Permission (%) *</label>
        <div class="flex items-center p-0">
          <Dropdown
              v-model="localValue.permission"
              class="w-full"
              optionLabel="label"
              optionValue="value"
              :options="permissions"
              placeholder="Select Permission"
              :required="true"
              :class="['w-full p-inputtext', {'': errors.permission}]"
          />
          <div class="ml-2 flex items-center align-middle">
            <a v-if="showRemoveButton" @click="handleRemove" class=" hover:bg-gray-100 rounded-full cursor-pointer" @mouseover="hover = true" @mouseleave="hover = false">
              <svg v-if="hover" xmlns="http://www.w3.org/2000/svg" class="h-5 w-6" fill="red" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </a>
          </div>
        </div>
        <div class="h-5">
          <small v-if="errors.permission" class="p-error block mt-1">{{ errors.permission[0] }}</small>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import quotationService from '@/services/hs/quotation.service'

export default defineComponent({
  props: {
    value: {
      type: Object,
      default: () => ({
        customer_type: null,
        customer_no: null,
        joint_level: 'PRIMARY',
        permission: 'FULL'
      })
    },
    customerTypes: {
      type: Array,
      default: () => []
    },
    jointLevels: {
      type: Array,
      default: () => []
    },
    permissions: {
      type: Array,
      default: () => []
    },
    errors: {
      type: Object,
      default: () => ({})
    },
    showRemoveButton: {
      type: Boolean,
      default: false
    },
    index: {
      type: Number,
      required: true
    }
  },

  emits: ['remove', 'update:modelValue'],

  data() {
    return {
      customers: [],
      localValue: {},
      hover: false,
    }
  },

  watch: {
    value: {
      immediate: true,
      handler(newValue) {
        this.localValue = { ...newValue }
      }
    },
    localValue: {
      deep: true,
      handler(newValue) {
        this.$emit('update:modelValue', newValue)
      }
    }
  },

  methods: {
    async listCustomers({ value: customerType }) {
      try {
        if (!customerType) return
        const response = await quotationService.getCustomersLov(customerType)
        this.customers = response.data
      } catch (error) {
        console.error('Error:', error)
        this.customers = []
        notify('Error', 'error','bottom-right');
      }
    },

    handleRemove() {
      this.$emit('remove', this.index)
    }
  },

})
</script>