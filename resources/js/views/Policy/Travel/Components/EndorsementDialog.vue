<template>
    <Dialog
        :visible="isVisible"
        :style="{ width: '20rem' }"
        :modal="true"
        :closable="false"
        :header="header"
    >
      <div class="grid grid-cols-1 gap-5">
        <div >
          <label class=" block mb-1" for="">Endorsement Type *</label>
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
          <div class="h-6">
            <small v-if="errorReason" class="p-error block mt-1">{{errorReason}}</small>
          </div>
        </div>

        <div>
          <label for="" class="form-label">Endorse. effective date *</label>
          <Calendar v-model="form.effective_date" dateFormat="dd-M-yy" :minDate="form.from" :maxDate="form.to"
                    placeholder="Effective date" />
          <span class="text-error" v-if="errorEffectiveDate"> {{ errorEffectiveDate }}
                    </span>
        </div>

        <div>
          <label for="" class="block font-semibold mb-1">Endorsement Descriptions</label>
          <Textarea class="w-full" rows="3" v-model="form.description"></Textarea>
        </div>
      </div>
      <template #footer>
        <Button label="Cancel" class="btn btn-danger" @click="hideDialog"/>
        <Button label="Confirm" class="btn btn-primary" autofocus @click="$emit('confirm', form)" />
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
            form: {
                type: '',
                effective_date: null,
                description: '',
                from:null,
                to:null,
            },
            options: {},
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
          return Object.entries(this.options).map(([value, label]) => ({
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

            this.$emit('hideDialog');
            this.$emit('hide');
        },
        listEndorsementTypes() {
            axios.get('/travel/endorsements/list-endorsement-types').then(response => this.options = response.data)
        },
        getValidEndorsementDatePeriod() {
            axios.get('/travel/endorsements/get-valid-endorsement-date-period/'+this.$route.params.id).then(res => {
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
