<template>
    <div class="flex p-5">
        <div class="flex-1 grid grid-cols-4 gap-4">
            <div>
                <label for="" class="form-label text-sm">Customer Type *</label>
                <Dropdown class="w-full" v-model="modelValue.customer_type" optionLabel="label"
                    @change="filterCustomer($event.value)" optionValue="value" :options="customerTypeOpts"
                    placeholder="Select customer type" showClear :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label text-sm">Customer Name *</label>
                <Dropdown class="w-full" v-model="modelValue.customer_no" :options="customerOpts" optionLabel="label"
                    :loading="loading" optionValue="value" placeholder="Select customer" filter showClear :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label text-sm">Joint Level *</label>
                <Dropdown class="w-full" v-model="modelValue.joint_level" :options="jointLevelOpts" optionLabel="label"
                    optionValue="value" placeholder="Select joint level" showClear :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label text-sm">Permission *</label>
                <Dropdown class="w-full" v-model="modelValue.permission" :options="permissionOpts" optionLabel="label"
                    optionValue="value" placeholder="Select permission" showClear :disabled="!canSaveGeneral" />
            </div>
        </div>
        <div class="w-6 pl-0.5">
            <Button type="button" class="mt-8 hover:text-red-500 focus:border-none focus:ring" label=""
                icon="pi text-lg pi-minus-circle" :disabled="!canSaveGeneral" @click="$emit('remove')" />
        </div>
    </div>
</template>

<script setup>
import QuoteService from '@/services/pa/quote.service';
import { onMounted, ref } from 'vue';
const props = defineProps({
    modelValue: {
        type: Object,
        default: {
            customer_type: null,
            customer_no: null,
            joint_level: null,
            permission: null
        }
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
const loading = ref(false)
const customerOpts = ref([])
const filterCustomer = (customerType) => {
    if (customerType) {
        loading.value = true
        QuoteService.searchInsuredPs({ customer_type: customerType }).then((res) => {
            customerOpts.value = res.data.map((item) => {
                item.label = item.customer_no + '-' + item.name_en
                item.value = item.customer_no
                return item
            })
        }).finally(() => loading.value = false)
    }
}
onMounted(() => {
    if(props.modelValue.customer_type) filterCustomer(props.modelValue.customer_type)
})
</script>

<style lang="scss" scoped></style>