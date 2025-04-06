<template>
  <Dialog
    :visible="isVisible"
    :style="{width: '20rem'}"
    :modal="true"
    :closable="false"
    :header="header"
  >
    <div class="grid grid-cols-1 gap-5">
      <div>
        <label class="block mb-1 font-bold" for="">Endorsement Type *</label>
        <Dropdown
          v-model="form.type"
          class="w-full border-0"
          optionLabel="label"
          optionValue="value"
          :options="options"
          :filter="true"
          :showClear="true"
          placeholder="Endorsement Type"
          :required="true"
          :class="['w-full p-inputtext']"
        />
        <small v-if="errorReason" class="p-error block mt-1">{{ errorReason }}</small>
      </div>
      <div>
        <label for="" class="block mb-1 font-bold">Endorse. effective date *</label>
        <Calendar v-model="form.effective_date" dateFormat="dd-M-yy" :minDate="form.from" :maxDate="form.to"
                  placeholder="Effective date"/>
        <span class="text-error" v-if="errorEffectiveDate"> {{ errorEffectiveDate }}</span>
      </div>

      <div>
        <label for="" class="block mb-1 font-bold">Endorsement Description</label>
        <Textarea class="w-full" rows="3" v-model="form.description"></Textarea>
      </div>

    </div>
    <template #footer>
      <Button label="Cancel" severity="danger" outlined @click="hideDialog"/>
      <Button label="Confirm"
              class="p-button-info px-2 py-2 border border-blue-500 text-white hover:bg-blue-600 bg-blue-500" autofocus
              @click="$emit('confirm', form)"/>
    </template>
  </Dialog>
</template>

<script>
import axios from 'axios'

export default {
  props: {
    header: String,
    isVisible: Boolean,
    submitted: Boolean,
  },
  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!",
      SUCCESS_MESSAGE:"Success!",
      form: {
        type: '',
        effective_date: null,
        description: '',
        from: null,
        to: null,
      },
      options: [],
    }
  },
  computed: {
    errorReason() {
      return (this.submitted && !this.form.type) ? 'Endorsement Type is required.' : null
    },
    errorEffectiveDate() {
      return (this.submitted && !this.form.effective_date) ? 'Endorsement Effective Date is required.' : null
    },
    formattedPartnerGroupOptions() {
      return Object.entries(this.form.options).map(([value, label]) => ({
        value,
        label
      }))
    },
  },
  methods: {
    hideDialog() {
      this.form = {
        type: '',
        effective_date: null,
        description: '',
      }

      this.$emit('hide')
    },
    listEndorsementTypes() {
      axios.get('/hs/endorsements/list-endorsement-types').then((response) => {
        this.options = Object.entries(response.data).map(([value, label]) => ({
          value,
          label
        }))
      })
    },
    getValidEndorsementDatePeriod() {
      axios.get('/hs/endorsement-service/get-valid-endorsement-date-period/' + this.$route.params.id).then(res => {
        this.form.from = new Date(res.data.from);
        this.form.to = new Date(res.data.to);
      })
    }
  },
  mounted() {
    this.getValidEndorsementDatePeriod();
    this.listEndorsementTypes()
  },

}
</script>
