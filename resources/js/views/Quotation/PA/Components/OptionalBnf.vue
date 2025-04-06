<template>
    <div class="divide-y divide-gray-300 border rounded">
        <h3 class="text-blue-500 font-semibold px-2 py-3">Additional Benefits</h3>
        <div v-if="!dataId" class="divide-y divide-gray-300">
            <OptionalBnfItem v-for="(bnf, index) in benefits" :key="index" v-model="modelValue[index]" :benefit="bnf"
                :of="of" :ratingErr="errors['optional_benefits.' + index + '.rating']"
                :amountTypeErr="errors['optional_benefits.' + index + '.amount_type']" />
        </div>
        <div v-else class="divide-y divide-gray-300">
            <OptionalBnfItem v-for="(bnf, index) in modelValue" :key="index" v-model="modelValue[index]"
                :benefit="bnf.benefit" :of="of" :ratingErr="errors['optional_benefits.' + index + '.rating']"
                :amountTypeErr="errors['optional_benefits.' + index + '.amount_type']" />
        </div>
    </div>
</template>

<script setup>
import OptionalBnfItem from './OptionalBnfItem.vue'

const props = defineProps({
    modelValue: {
        type: Array,
        default: []
    },
    benefits: {
        type: Array,
        default: []
    },
    of: {
        type: Array,
        default: []
    },
    errors: {
        type: Array,
        default: []
    },
    dataId: {
        type: [String, Number],
        default: null
    }
})
</script>