<template>
    <div class="divide-y divide-gray-300 border rounded">
        <h4 class="px-4 py-2 font-semibold text-blue-900">Joint Details {{ editable }}</h4>
        <JointItem v-for="(item, index) in modelValue" v-model="modelValue[index]" @remove="removeRow(index)"
            @change="$emit('change')" :editable="editable" :customerTypeOpts="customerTypeOpts"
            :jointLevelOpts="jointLevelOpts" :permissionOpts="permissionOpts" :dataId="dataId" />
        <div class="flex justify-end p-5">
            <Button label="Add" class="p-button-info border-2 text-sm py-1 hover:broder-3 rounded-md"
                icon="pi pi-plus text-xs font-bold" v-if="editable" outlined @click="addRow" />
        </div>
    </div>
</template>

<script setup>
import JointItem from './JointItem.vue';

const emit = defineEmits(['change'])
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
    dataId: {
        type: [Number, String],
        default: null
    },
    editable: {
        type: Boolean,
        default: true
    }
})
const addRow = () => {
    props.modelValue = props.modelValue.push({})
}
const removeRow = (index) => {
    props.modelValue = props.modelValue.splice(index, 1)
}
</script>