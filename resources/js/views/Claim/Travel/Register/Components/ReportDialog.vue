<template>
    <Dialog
        :visible="isVisible"
        :modal="true"
        :closable="false"
        :header="header"
        style="width: 340px; z-index: 1000; transform-origin: center bottom; top: 0px; left: 0px;"
    >
        <div class="">
          <label for="report_type" class="font-bold block mb-1">Report Type *</label>
          <Dropdown
              v-model="form.report_type"

              :options="formattedOptions"
              optionLabel="label"
              optionValue="value"
              class="w-full p-inputtext"
              placeholder="Business Type"
              :filter="true"
          />
          <small v-if="errorReportType" class="p-error block">{{ errorReportType }}</small>
        </div>

        <div>
          <label for="from_date" class="block mb-1 font-bold">From Date</label>
          <Calendar  placeholder="dd-M-yy" v-model="form.from_date" dateFormat="dd-M-yy" />
        </div>
        <div>
          <label for="to_date" class="block mb-1 font-bold">To Date</label>
          <Calendar  placeholder="dd-M-yy"  v-model="form.to_date" :min="form.from_date"  dateFormat="dd-M-yy" />
        </div>

        <template #footer>
            <Button label="Cancel" class="btn btn-danger" @click="hideDialog" />
            <Button label="Export" class="btn btn-primary w-24" autofocus @click="exportClaimReport" />
        </template>
    </Dialog>
</template>

<script>
export default {
    props: {
        header: String,
        isVisible: Boolean,
    },
    data() {
        return {
            submitted: false,
            form: {
                report_type: '',
                from_date: '',
                to_date: '',
            },
            options:{
              'ClaimsPaid': 'Claims Paid',
              'ClaimsOutstanding': 'Claims Outstanding',
              'ClaimsIncurred': 'Claims Incurred'
            }
        }
    },
    computed: {
        errorReportType() {
            return (this.submitted && !this.form.report_type) ? 'Report Type is required.' : null
        },
        formattedOptions() {
          return Object.entries(this.options).map(([value, label]) => ({
            value,
            label
          }))
        },
    },
    methods: {
        hideDialog() {
            this.form = {
                report_type: '',
                from_date: '',
                to_date: '',
            }

            this.$emit('hideDialog')
        },
        exportClaimReport() {
            this.submitted=true;
            if (!this.errorReportType) {
                this.$emit('exportClaimReport', this.form)
            }
        }
    },
}
</script>
