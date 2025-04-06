<template>
    <div>
        <label for="" class="block font-bold my-2">Joint Details *</label>
        <Card>
            <template #content>
                <JointItem v-for="(customer, index) in modelValue" v-model="modelValue[index]" :customerTypes="customerTypes" @change="handleChange"
                    :jointDetailsConfig="jointDetailsConfig" :errors="errors" :index="index" :canSaveGeneral="canSaveGeneral" :data="customer" @removeJointItem="removeJointDetail"></JointItem>
            </template>
            <template #footer>
                <div class="grid justify-end">
                    <Button label="Add New" @click="addDetail" icon="pi pi-plus" class="text-sm text-blue-500 border border-blue-500 px-2 py-1 float-right" text raised />
                </div>
            </template>
        </Card>
    </div>
</template>

<script setup>
import JointItem from './JointItem.vue';
import { onMounted, ref } from 'vue';
const props = defineProps({
    customerTypes: {
        type: Object,
        default: {}
    },
    jointDetailsConfig: {
        type: Array,
        default: []
    },
    canSaveGeneral: {
        type: Boolean,
        default: true,
    },
    modelValue: {
        type: Array,
        default: []
    },
    errors:{
        type:Array,
        default:[]
    }
})
const emit = defineEmits(['update:modelValue','change'])
const jointDetails = ref([])

const addDetail = () => {
    jointDetails.value = props.modelValue
    jointDetails.value.push({})
    emit('update:modelValue',jointDetails.value)
}
const removeJointDetail = (index) => {
    const data = jointDetails.value
    data.splice(index, 1)
    jointDetails.value = data
}
const handleChange = () => {
    emit('change')
}
onMounted(() => {
    
})
</script>
