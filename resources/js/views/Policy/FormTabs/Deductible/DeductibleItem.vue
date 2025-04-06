<template>
    <div>
        <div class="flex mr-1">
            <span>
                <svg class="w-6 h-6 text-green-500 rotate icon-color" :class="`deductible-icon-${index}`" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </span>
            <p class="input-label text-base cursor-pointer w-full" @click="toggle(index)">Vehicle #{{ index + 1 }}</p>
        </div>
        <div class="collapse  pl-8" :class="`show-deductible-${index}`">
            <div class="grid grid-cols-2 sm:grid-cols-1 gap-5">
                <div class="flex my-2">
                    <span class="input-label w-1/3">Make</span>
                    <p class="text-sm w-2/3">{{ make }}</p>
                </div>
                <div class="flex my-2">
                    <span class="input-label w-1/3">Model</span>
                    <p class="text-sm w-2/3">{{ model }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-1 gap-5">
                <div class="flex my-2">
                    <span class="input-label w-1/3">Covers</span>
                    <p class="text-sm w-2/3">{{ covers }}</p>
                </div>
                <div class="flex my-2">
                    <span class="input-label w-1/3">Sum Insured</span>
                    <p class="text-sm w-2/3">{{ sumInsured   }}</p>
                </div>
            </div>
            <label for="" class="block font-semibold mb-2">Deductibles</label>
            <div class="grid grid-cols-4 gap-5 mb-4" v-for="(deductible, dIndex) in modelValue">
                <InputText v-model="modelValue[dIndex].deductible_label" disabled class="cursor-not-allowed"></InputText>
                <InputText v-model="modelValue[dIndex].value" disabled class="cursor-not-allowed"></InputText>
                <div class="flex items-center gap-2">
                    <label for="" class="font-semibold">Value</label>
                    <InputNumber v-model="modelValue[dIndex].cond_value" class="w-full"></InputNumber>
                </div>
                <div class="flex items-center gap-2" v-if="getValueType(dIndex) === 'PERCENTAGE'">
                    <label for="" class="font-semibold text-nowrap">Min. Value</label>
                    <InputNumber v-model="modelValue[dIndex].min_value" class="w-full"></InputNumber>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';


export default {
    props: {
        index: Number,
        detailId: [Number, String],
        cancelPolicyEndorsement: Boolean,
        modelValue: Array,
    },

    data() {
        return {
            data: {},
            deductibles:[]
        }
    },

    computed: {
        make() {
            return this.modelValue[0]?.auto_detail?.make_model?.make?.make
        },
        model() {
            return this.modelValue[0]?.auto_detail?.make_model?.model
        },
        covers() {
            let covers = this.modelValue[0]?.auto_detail?.selected_cover_pkg ?? ''
            return covers.split(',').join(', ')
        },
        sumInsured() {
            return this.modelValue[0]?.auto_detail?.vehicle_value
        },
    },

    methods: {
        toggle(index) {
            this.$emit('toggle', index)
        },
        getValueType(index) {
            return this.modelValue[index]?.cond_value_type
        }
    },

    mounted() {
        if(this.index == 0)
            this.$emit('collapse', this.index)
        if(this.modelValue.length > 0){
            this.deductibles = JSON.parse(JSON.stringify(this.modelValue))
        }
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
