<template>
    <div class="divide-y divide-gray-300 border rounded">
        <h3 class="text-blue-500 font-semibold px-2 py-3">Schedules Benefits</h3>
            <OptionalBnfItem v-if="benefits" v-for="(bnf, index) in benefits" :key="index" v-model="modelValue[index]" :benefit="bnf"/>
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