<template>
    <div class="reinsurance">
        <div class="flex">
            <div class="flex-1 grid grid-cols-9 gap-4 mb-1">
                <div v-for="label in headers" class="text-sm font-semibold">{{ label }}</div>
            </div>
            <div class="w-[4rem]"> </div>
        </div>
        <ReinsuranceField class="" :participants="participants" v-for="(reinsurance, index) in modelValue"
            @removeRow="removeRow(index)" v-model="modelValue[index]" :types="types" :defaultGroups="defaultGroups">
        </ReinsuranceField>
        <div class="flex justify-end pt-2">
            <Button class="button-info outlined text-sm" @click="addRow"><i class="pi pi-plus"
                    style="font-size:0.75rem;margin-right: 0.5rem;"></i> Add Share</Button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import ReinsuranceField from './ReinsuranceField.vue';

const totalShare = computed(() => {
    return props.modelValue.length ? props.modelValue.reduce((sum, item) => sum + item.share, 0) : 0;
})
const props = defineProps({
    modelValue: {
        type: Array,
        default: []
    },
    types: {
        type: Array,
        default: []
    },
    defaultGroups: {
        type: Array,
        default: []
    },
    participants: {
        type: Array,
        default: []
    }
})
const addRow = () => {
    props.modelValue.push({})
}
const removeRow = (index) => {
    props.modelValue.splice(index, 1)
}
const headers = [
    'Reinsurance Type',
    'Participant',
    'Share (%)',
    'Premium (USD)',
    'RI Commission (%)',
    'RI Commission (USD)',
    'Tax & Fees (%)',
    'Tax & Fees (USD)',
    'Net Premium (USD)'
]
</script>
<style>
.reinsurance>div.flex:first-of-type {
    @apply rounded-t-lg border-t border-l border-r border-slate-200
}

.reinsurance>div.flex:last-of-type {
    @apply rounded-b-lg border-b border-l border-r border-slate-200
}

.reinsurance div.flex {
    @apply py-4 px-5
}

.reinsurance div.flex:nth-child(odd) {
    @apply bg-slate-100
}
</style>