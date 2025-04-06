<template>
    <div class="collapse pl-6" :class="`show-cancellation-${index}`">
        <div class="grid grid-cols-2 sm:grid-cols-1 gap-5">
            <div class="flex my-2">
                <span class="w-1/3 font-bold">Make</span>
                <p class="text-sm w-2/3">{{ vehicle.make }}</p>
            </div>
            <div class="flex my-2">
                <span class="w-1/3 font-bold">Model</span>
                <p class="text-sm w-2/3">{{ vehicle.model }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-1 gap-5">
            <div class="flex my-2">
                <span class="input-label w-1/3">Covers</span>
                <p class="text-sm w-2/3">{{ vehicle.selected_cover_pkg }}</p>
            </div>
            <div class="flex my-2">
                <span class="input-label w-1/3">Premium</span>
                <p class="text-sm w-2/3">{{ vehicle.premium   }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-1 gap-x-5 mt-4">
            <div>
                <label for="" class="form-label">Endorsement Effective Date</label>
                <Calendar v-model="modelValue.endorsement_e_date" dateFormat="dd-M-yy" :minDate="today" placeholder="Endorsement Effective Date" showIcon />
            </div>
            <div>
                <label for="" class="form-label">Refund Option</label>
                <Dropdown :options="refundTypeOptions" class="w-full" optionLabel="label" optionValue="value" v-model="modelValue.refund_option" placeholder="Select refund option" />
            </div>
            <div>
                <label for="" class="form-label">{{isCustomAmountDisabled ? 'Custom Refund Amount' : 'Custom Refund Amount *'}}</label>
                <InputNumber v-model="modelValue.custom_refund_amount" placeholder="Refund amount" class="w-full" :max="vehicle.premium" :minFractionDigits="0" :maxFractionDigits="2" :disabled="isCustomAmountDisabled" />
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        index: Number,
        vehicle: Object,
        refundTypeOptions: Array,
        today: Date,
        modelValue:{
            type:Object,
            default:{}
        }
    },

    data() {
        return {
            data: {},
            formValues: {},
        }
    },

    computed: {
        defaultSelectedRefundType() {
            return this.refundTypeOptions?.[0]?.value
        },
        isCustomAmountDisabled() {
            return this.vehicle.refund_option !== 'CUSTOM'
        }
    },

    methods: {
        toggle(index) {
            this.$emit('toggle', index)
        },
    },

    mounted() {
        if(this.index == 0)
            this.$emit('collapse', this.index)
    },
}
</script>

<style scoped>
    .input-label {
        display: block;
        line-height: 1.5;
        font-weight: bold;
    }
    .collapse {
        display: none;
    }
    .rotate {
        transform: rotate(180deg);
    }
    .icon-color {
        color: red;
    }
</style>
