<template>
    <div class="divide-y divide-gray-300 border rounded">
        <h4 class="px-4 py-2 font-semibold text-blue-500">Joint Details</h4>
        <JointItem v-for="(item, index) in modelValue" v-model="modelValue[index]" @remove="removeRow(index)"
            :customerTypeOpts="customerTypeOpts" :jointLevelOpts="jointLevelOpts" :permissionOpts="permissionOpts" :canSaveGeneral="canSaveGeneral" />
        <div class="flex justify-end p-5" v-if="canSaveGeneral">
            <Button label="Add" class="p-button-info text-sm py-1 hover:broder-2" icon="pi pi-plus text-xs font-bold"
                outlined @click="addRow" />
        </div>
    </div>
</template>

<script setup>
import JointItem from './JointItem.vue';

const props = defineProps({
    modelValue: {
        type: Array,
        default: []
    },
    customerTypeOpts: {
        type: Array,
        default: []
    },
    jointLevelOpts: {
        type: Array,
        default: []
    },
    permissionOpts: {
        type: Array,
        default: []
    },
    canSaveGeneral:{
        type:Boolean,
        default:false
    }
})
const addRow = () => {
    props.modelValue = props.modelValue.push({})
}
const removeRow = (index) => {
    props.modelValue = props.modelValue.splice(index, 1)
}
</script>

<style lang="scss" scoped></style>