<template>
    <div class="grid grid-cols-12 gap-5">
        <div class="col-span-12 sm:col-span-6 xl:col-span-3">
            <label for="" class="block mb-1 text-sm font-semibold">Customer Type *</label>
            <Dropdown class="w-full p-inputtext-sm" v-model="modelValue.customer_type" :options="customerTypes"
                placeholder="Customer Type" :disabled="!canSaveGeneral" optionLabel="label" optionValue="value"
                @change="renderListCustomers($event.value)" :showClear="true">
            </Dropdown>
            <span class="text-red text-xs text-red-700 text-error"
                v-if="errors[`joint_details.${index}.customer_type`]">{{
                    errors[`joint_details.${index}.customer_type`][0]
                }}</span>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3">
            <label for="" class="block mb-1 text-sm font-semibold">Customer Name *</label>
            <Dropdown class="w-full p-inputtext-sm" v-model="modelValue.customer_no" :options="customers"
                placeholder="Customer Name" :disabled="!canSaveGeneral" optionLabel="label" optionValue="value"
                @update:modelValue="changeJoint" :filter="true">
            </Dropdown>
            <span class="text-red text-xs text-red-700 text-error"
                v-if="errors[`joint_details.${index}.customer_no`]">{{
                    errors[`joint_details.${index}.customer_no`][0]
                }}</span>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3">
            <label for="" class="block mb-1 text-sm font-semibold">Joint Level *</label>
            <Dropdown class="w-full p-inputtext-sm" v-model="modelValue.joint_level" :options="jointDetailsConfig[0]"
                placeholder="Joint Level" :disabled="!canSaveGeneral" optionLabel="label" optionValue="value">
            </Dropdown>
            <span class="text-red text-xs text-red-700 text-error"
                v-if="errors[`joint_details.${index}.joint_level`]">{{
                    errors[`joint_details.${index}.joint_level`][0]
                }}</span>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 grid grid-cols-12">
            <div class="col-span-10">
                <label for="" class="block mb-1 text-sm font-semibold">Permission *</label>
                <Dropdown class="w-full p-inputtext-sm" v-model="modelValue.permission" :options="jointDetailsConfig[1]"
                    placeholder="Permission" :disabled="!canSaveGeneral" optionLabel="label" optionValue="value">
                </Dropdown>
                <span class="text-red text-xs text-red-700 text-error"
                    v-if="errors[`joint_details.${index}.permission`]">{{
                        errors[`joint_details.${index}.permission`][0]
                    }}</span>
            </div>
            <div class="col-span-2 text-center pt-8">
                <Button label="" icon="pi pi-minus" @click="removeDetail(index)"></Button>
            </div>
        </div>
        <div class="col-span-12 pb-4">
            <hr>
        </div>
    </div>
</template>
<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'
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
    index: {
        type: Number,
        default: ''
    },
    data: {
        type: Object,
        default: {}
    },
    modelValue: {
        type: Object,
        default: {
            name_en: '',
            name_kh: ''
        }
    },
    errors: {
        type: Array,
        default: []
    }
})
const emit = defineEmits(['removeJointItem', 'change'])
const jointItem = ref({})
const customers = ref([])
const renderListCustomers = (value) => {
    axios.get('/auto-service/list-customers-by-type/' + value).then(response => {
        customers.value = response.data
    })
}
const removeDetail = () => {
    emit('removeJointItem', props.index)
}
const changeJoint = (value) => {

    if (value) {
        const selectedCst = customers.value.find(item => item.value === value)
        Object.assign(props.modelValue, {
            name_en: selectedCst.name_en,
            name_kh: selectedCst.name_kh
        })
    }

    emit('change')
}
onMounted(() => {
    if (props.data !== {}) {
        jointItem.value = JSON.parse(JSON.stringify(props.data))
        renderListCustomers(jointItem.value?.customer_type)
    }
})
</script>