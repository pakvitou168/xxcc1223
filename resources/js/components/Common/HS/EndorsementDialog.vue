<template>
  <Dialog
      :visible="visible"
      :style="{ width: '20rem' }"
      :modal="true"
      :closable="false"
      :header="header"
  >
    <div class="grid grid-cols-1 gap-5">
      <div >
        <label class="block mb-1 font-bold" for="">Endorsement Type *</label>
        <Dropdown
            v-model="form.type"
            class="w-full border-0"
            optionLabel="label"
            optionValue="value"
            :options="formattedPartnerGroupOptions"
            :filter="true"
            :showClear="true"
            placeholder="Endorsement Type"
            :required="true"
            :class="['w-full p-inputtext']"
        />
          <small v-if="typeErrors[0]" class="p-error block mt-1">{{typeErrors[0]}}</small>
      </div>

      <div>
        <label for="" class="block mb-1 font-bold">Endorse. effective date *</label>
        <Calendar v-model="form.effective_date" dateFormat="dd-M-yy" :minDate="form.from" :maxDate="form.to"
                  placeholder="Effective date" />
        <span class="text-error" v-if="effectiveDateErrors[0]"> {{ effectiveDateErrors[0] }}
        </span>
      </div>

      <div>
        <label for="" class="block mb-1 font-bold">Endorsement Description</label>
        <Textarea class="w-full" rows="3" v-model="form.description"></Textarea>
      </div>
    </div>
    <template #footer>
      <Button label="Cancel" severity="danger" outlined @click="() => {
            form = {
                endorsement_type: '',
                endorsement_e_date: null,
                endorsement_description: '',
            }

            $emit('hide')
        }" />
      <Button label="Confirm" class="p-button-info px-2 py-2 border border-blue-500 text-white hover:bg-blue-600 bg-blue-500" autofocus :disabled="submitting" :loading="submitting" @click="$emit('confirm', form)" />
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
    computed: {

      formattedPartnerGroupOptions() {
        return Object.entries(this.types).map(([value, label]) => ({
          value,
          label
        }))
      },
    },

}
</script>