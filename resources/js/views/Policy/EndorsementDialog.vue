<template>
    <Dialog :visible="isVisible" :style="{ width: '340px' }" :modal="true" :closable="false" :header="header">
        <div class="grid grid-cols-1 gap-5">
            <div>
                <label for="" class="form-label">Endorsement type *</label>
                <Dropdown :options="options" class="w-full" v-model="form.auto_endorsement_type" optionLabel="label"
                    optionValue="value" placeholder="Select endorsement type" />
                <span class="text-error" v-if="errors['auto_endorsement_type']"> {{ errors['auto_endorsement_type'][0]
                    }}
                </span>
            </div>
            <div>
                <label for="" class="form-label">Endorse. effective date *</label>
                <Calendar v-model="form.endorsement_e_date" dateFormat="dd-M-yy" :minDate="form.from" :maxDate="form.to"
                    placeholder="Effective date" />
                <span class="text-error" v-if="errors['endorsement_e_date']"> {{ errors['endorsement_e_date'][0] }}
                </span>
            </div>
            <div>
                <label for="" class="form-label">Remark</label>
                <Textarea v-model="form.endorsement_description" rows="5" class="w-full" placeholder="Remark" />
                <span class="text-error" v-if="errors['endorsement_description']"> {{
                    errors['endorsement_description'][0] }}
                </span>
            </div>
        </div>
        <template #footer>
            <Button label="Cancel" class="p-button-danger p-button-text" outlined @click="hideDialog" />
            <Button label="Confirm" type="button" class="p-button-info" :loading="saving" autofocus
                @click="confirmEndorsement" />
        </template>
    </Dialog>
</template>

<script>
import axios from 'axios'
import moment from 'moment';
import Dropdown from 'primevue/dropdown';
export default {
    props: {
        header: String,
        isVisible: Boolean,
        submitted: Boolean,
        saving: {
            type: Boolean,
            default: false
        },
        errors: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            form: {
                auto_endorsement_type: '',
                endorsement_e_date: '',
                endorsement_description: '',
                from: '',
                to: '',
            },
            options: []
        }
    },
    computed: {
        errorReason() {
            return (this.submitted && !this.form.auto_endorsement_type) ? 'Endorsement Type is required.' : null
        },
        errorEffectiveDate() {
            return (this.submitted && !this.form.endorsement_e_date) ? 'Endorsement Effective Date is required.' : null
        },
        today() {
            var today = new Date()
            var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate()
            return date
        }
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
        listAutoEndorsementTypes() {
            axios.get('/policy-service/list-auto-endorsement-types').then(response => this.options = response.data)
        },
        getValidEndorsementDatePeriod() {
            axios.get('/endorsement-service/get-valid-endorsement-date-period/' + this.$route.params.id).then(res => {
                this.form.from = moment(res.data.from).toDate();
                this.form.to = moment(res.data.to).toDate();
            })
        },
        confirmEndorsement() {
            let formData = JSON.parse(JSON.stringify(this.form))
            Object.assign(formData, {
                from: moment(formData.from).format('YYYY-MM-DD'),
                to: moment(formData.to).format('YYYY-MM-DD'),
                endorsement_e_date: moment(formData.endorsement_e_date).format('YYYY-MM-DD')
            })
            this.$emit('confirm', formData)
        }
    },
    mounted() {
        this.getValidEndorsementDatePeriod();
        this.listAutoEndorsementTypes()
    }
}
</script>
