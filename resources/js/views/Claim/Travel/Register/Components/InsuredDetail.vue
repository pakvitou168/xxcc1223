<template>
  <div class="grid grid-cols-3 gap-x-4">
    <div class="formulate-input">
      <label class="formulate-input-label" for="">Insured Person *</label>
      <Dropdown
        class="w-full p-inputtext-sm"
        name="data_detail_id"
        v-model="formValues.data_detail_id"
        optionLabel="name"
        optionValue="code"
        placeholder="Insured person"
        :filter="true"
        :showClear="true"
        :options="insuredPersons"
      >
        <template #option="slotProps">
          <div class="flex align-items-center">
            <div>
              {{ slotProps.option.name }} . {{ slotProps.option.gender }} .
              {{ slotProps.option.dob }}
            </div>
          </div>
        </template>
      </Dropdown>
      <ul v-if="errors.data_detail_id" class="formulate-input-errors">
        <li class="formulate-input-error">
          {{ errors.data_detail_id[0] }}
        </li>
      </ul>
    </div>
    <FormulateInput
      type="number"
      label="Premium value*"
      name="premium"
      :error="errors.premium ? errors.premium[0] : null"
    />
    <FormulateInput
      type="textarea"
      rows="5"
      label="Remark*"
      name="note"
      :error="errors.note ? errors.note[0] : null"
    />
  </div>
</template>
<script>
export default {
  data() {
    return {
      formValues:{

      }
    };
  },
  props: {
    insuredPersons: {
      type: Array,
      default: [],
    },
    value:{
      type:Object,
      default:{
        
      }
    },
    errors: {
      type: Object,
      default: {},
    },
  },
  mounted(){
    this.formValues = this.value
  }
};
</script>