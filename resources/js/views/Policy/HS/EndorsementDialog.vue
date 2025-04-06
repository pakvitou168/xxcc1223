<template>
  <!--    <Dialog-->
  <!--        :visible="isVisible"-->
  <!--        :style="{width: '340px'}"-->
  <!--        :modal="true"-->
  <!--        :closable="false"-->
  <!--        :header="header"-->
  <!--    >-->
  <!--        <FormulateInput-->
  <!--            type="select"-->
  <!--            name="auto_endorsement_type"-->
  <!--            label="Endorsement Type *"-->
  <!--            v-model="form.auto_endorsement_type"-->
  <!--            :options="options"-->
  <!--            :error="errorReason"-->
  <!--            />-->
  <!--        <FormulateInput-->
  <!--            type="date"-->
  <!--            name="endorsement_e_date"-->
  <!--            label="Endorsement Effective Date *"-->
  <!--            v-model="form.endorsement_e_date"-->
  <!--            :min="form.from"-->
  <!--            :max="form.to"-->
  <!--            :error="errorEffectiveDate"-->
  <!--        />-->
  <!--        <FormulateInput-->
  <!--            type="textarea"-->
  <!--            name="endorsement_description"-->
  <!--            label="Endorsement Descriptions"-->
  <!--            v-model="form.endorsement_description"-->
  <!--            rows="4"-->
  <!--        />-->
  <!--        <template #footer>-->
  <!--            <Button label="Cancel" class="btn btn-danger" @click="hideDialog"/>-->
  <!--            <Button label="Confirm" class="btn btn-primary" autofocus @click="$emit('confirm', form)" />-->
  <!--        </template>-->
  <!--    </Dialog>-->
  <Dialog
    :visible="isVisible"
    :style="{ width: '20rem' }"
    :modal="true"
    :closable="false"
    :header="header"
  >
    <div class="grid grid-cols-1 gap-5">
      <div>
        <label class=" block mb-1" for="">Endorsement Type *</label>
        <Dropdown
          v-model="form.auto_endorsement_type"
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
        <div class="h-6">
          <small v-if="errorReason" class="p-error block mt-1">{{ errorReason }}</small>
        </div>
      </div>

      <div>
        <label for="" class="form-label">Endorse. effective date *</label>
        <Calendar v-model="form.endorsement_e_date" dateFormat="dd-M-yy" :minDate="form.from" :maxDate="form.to"
                  placeholder="Effective date"/>
        <span class="text-error" v-if="errorEffectiveDate"> {{ errorEffectiveDate }}
                    </span>
      </div>

      <div>
        <label for="" class="block font-semibold mb-1">Endorsement Descriptions</label>
        <Textarea class="w-full" rows="3" v-model="form.endorsement_description"></Textarea>
      </div>
    </div>
    <template #footer>
      <Button label="Cancel" class="btn btn-danger" @click="hideDialog"/>
      <Button label="Confirm" class="btn btn-primary" autofocus @click="$emit('confirm', form)"/>
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
      ERROR_MESSAGE: "Something went wrong!",
      SUCCESS_MESSAGE: "Success!",
      form: {
        auto_endorsement_type: '',
        endorsement_e_date: null,
        endorsement_description: '',
        from: null,
        to: null,
      },
      options: {},
    }
  },
  computed: {
    errorReason() {
      return (this.submitted && !this.form.auto_endorsement_type) ? 'Endorsement Type is required.' : null
    },
    errorEffectiveDate() {
      return (this.submitted && !this.form.endorsement_e_date) ? 'Endorsement Effective Date is required.' : null
    },
    formattedPartnerGroupOptions() {
      return Object.entries(this.options).map(([value, label]) => ({
        value,
        label
      }))
    },
  },
  methods: {
    hideDialog() {
      this.form = {
        auto_endorsement_type: '',
        endorsement_e_date: null,
        endorsement_description: '',
      }

      this.$emit('hideDialog')
    },
    listEndorsementTypes() {
      axios.get('/hs/policy-service/list-auto-endorsement-types').then(response => this.options = response.data)
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
  }
}
</script>
