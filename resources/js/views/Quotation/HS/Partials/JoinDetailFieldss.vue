<template>
  <div class="grid grid-cols-12 gap-5">
    <div class="col-span-12 sm:col-span-6 xl:col-span-3">
      <div class="">
        <label class="">Customer Type *</label>
        <Dropdown
            v-model="value.customer_type"
            class="w-full p-inputtext"
            optionLabel="label"
            optionValue="value"
            :options="customerTypes"
            @change="listCustomers"
        />
        <ul v-if="errors.customer_type" class="">
          <li class="">{{ errors.customer_type[0] }}</li>
        </ul>
      </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-3">
      <div class="">
        <label class="">Customer Name *</label>
        <Dropdown
            v-model="value.customer_no"
            class="w-full p-inputtext"
            optionLabel="label"
            optionValue="value"
            :options="customers"
            :filter="true"
            :showClear="true"
        />
        <ul v-if="errors.customer_no" class="">
          <li class="">{{ errors.customer_no[0] }}</li>
        </ul>
      </div>
    </div>

    <div class="col-span-12 sm:col-span-6 xl:col-span-3">
      <div class="">
        <label class="">Joint Level *</label>
        <Dropdown
            v-model="value.joint_level"
            class="w-full p-inputtext"
            optionLabel="label"
            optionValue="value"
            :options="jointLevels"
        />
        <ul v-if="errors.joint_level" class="">
          <li class="">{{ errors.joint_level[0] }}</li>
        </ul>
      </div>
    </div>

    <div class="col-span-12 sm:col-span-6 xl:col-span-3">
      <div class="">
        <label class="">Permission *</label>
        <Dropdown
            v-model="value.permission"
            class="w-full p-inputtext"
            optionLabel="label"
            optionValue="value"
            :options="permissions"
        />
        <ul v-if="errors.permission" class="">
          <li class="">{{ errors.permission[0] }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import quotationService from '@/services/hs/quotation.service'

export default {
  props: {
    value: Object,
    customerTypes: Array,
    jointLevels: Array,
    permissions: Array,
    errors: Object,
  },
  data() {
    return {
      customers: [],
    }
  },

  methods: {
    listCustomers({value}) {
      quotationService.getCustomersLov(value).then(res => {
        this.customers = res.data
      })
    }
  },

  mounted() {
    // Default value when adding new customer row
    if (!this.value.joint_level) this.value.joint_level = 'PRIMARY'
    if (!this.value.permission) this.value.permission = 'FULL'
  }
}
</script>