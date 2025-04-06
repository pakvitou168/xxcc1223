<template>
    <div>
        <label class="font-bold">For All Vehicles</label>
        <div class="grid grid-cols-2 sm:grid-cols-1 gap-x-5">
            <FormulateInput
                v-if="defaultEndorsementEffectiveDate"
                type="date"
                name="endorsement_e_date_for_all"
                label="Endorsement Effective Date"
                :min="today"
                :value="defaultEndorsementEffectiveDate"
            />
            <FormulateInput
                v-if="defaultSelectedRefundType"
                type="select"
                name="refund_option_for_all"
                label="Refund Options"
                placeholder="Refund Options"
                :options="refundTypeOptions"
                :value="defaultSelectedRefundType"
            />
        </div>
        <div class="text-right">
            <router-link :to="{name: 'EndorsementIndex'}" class="btn btn-outline-secondary w-24 mr-1" tag="button">Cancel</router-link>
            <button type="button" class="btn btn-primary w-24" @click="$emit('cancelAllVehicles')">Save</button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        refundTypeOptions: Array,
        today: String,
        dataId: [Number, String],
    },

    data() {
        return {
            data: {},
            formValues: {},
            defaultEndorsementEffectiveDate: null,
        }
    },

    computed: {
        defaultSelectedRefundType() {
            return this.refundTypeOptions?.[0]?.value
        },
    },

    methods: {
        getDefaultEndorsementEffectiveDate() {
            if (this.dataId){
                axios.get(`/auto-service/get-default-endorsement-e-date/${this.dataId}`).then(response => this.defaultEndorsementEffectiveDate = response.data)
            }
        },
    },

    mounted(){
        this.getDefaultEndorsementEffectiveDate()
    }
}
</script>
