<template>
    <Dialog :visible="isVisible" :style="{ width: '340px' }" :modal="true" :closable="false" :header="header">
        <div class="grid gap-y-5">
            <div>
                <label for="" class="form-label">Report Type *</label>
                <Dropdown class="w-full" :options="options" v-model="form.report_type" placeholder="Select report type"
                    optionLabel="label" optionValue="value" />
            </div>
            <div>
                <label for="" class="form-label">From date</label>
                <Calendar class="w-full" :options="options" v-model="form.from_date" placeholder="From date" showIcon />
            </div>
            <div>
                <label for="" class="form-label">To Date</label>
                <Calendar class="w-full" :options="options" v-model="form.to_date" placeholder="To date" showIcon />
            </div>
        </div>
        <template #footer>
            <Button label="Cancel" class="p-button-secondary p-button-text" outlined @click="hideDialog" />
            <Button label="Export" class="p-button-info" autofocus @click="exportClaimReport" />
        </template>
    </Dialog>
</template>

<script>
import moment from 'moment';

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
            options: [{ value: 'ClaimsPaid', label: 'Claims Paid' }, { value: 'ClaimsOutstanding', label: 'Claims Outstanding' }, { value: 'ClaimsIncurred', label: 'Claims Incurred' }],
        }
    },
    computed: {
        errorReportType() {
            return (this.submitted && !this.form.report_type) ? 'Report Type is required.' : null
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
            this.submitted = true;
            if (!this.errorReportType) {
                let formData = JSON.parse(JSON.stringify(this.form))
                formData = Object.assign(formData, {
                    from_date: this.form.from_date ? moment(this.form.from_date).format('YYYY-MM-DD') : '',
                    to_date: this.form.to_date ? moment(this.form.to_date).format('YYYY-MM-DD') : ''
                })
                this.$emit('exportClaimReport', formData)
            }
        }
    },
}
</script>
