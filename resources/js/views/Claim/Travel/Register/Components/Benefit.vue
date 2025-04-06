<template>
    <div v-if="value">
        <h1>{{value}}</h1>
        <FormulateInput type="date" label="Admission Date *"  name="admission_date" />
    </div>
</template>

<script>
export default {
    props: {
        value: {
            type: Object,
            default: {},
        },
        addmissionDateErr: Array,
        daysErr: Array,
        dischargeDateErr: Array,
        feePerDayErr: Array
    },

    data() {
        return {
            updatedValue: this.value
        }
    },

    computed: {
    },

    methods: {
    },

    updated() {
        if (this.value) {
            this.updatedValue = this.value
        }
    }
}
</script>