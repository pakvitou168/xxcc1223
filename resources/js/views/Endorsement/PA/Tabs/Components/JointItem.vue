<template>
    <div class="flex p-5">
        <div class="flex-1 grid grid-cols-4 gap-4">
            <div v-for="form in formFields">
                <label for="" class="form-label text-sm">{{form.label}} <span v-if="form.required" class="text-red-600">*</span></label>
                <component :is="form.component" v-bind="form.props" v-model="modelValue[form.field]" @[form?.event]="form?.eventHandler" ></component>
            </div>
        </div>
        <div class="w-6 pl-0.5">
            <Button type="button" v-if="editable" class="mt-8 hover:text-red-500 focus:border-none focus:ring" label=""
                icon="pi text-lg pi-minus-circle" @click="$emit('remove')" />
        </div>
    </div>
</template>

<script setup>
import paService from '@/services/pa/pa.service';
import { computed, onMounted, ref } from 'vue';

const emit = defineEmits(['change'])
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
    dataId: {
        type: [Number, String],
        default: null
    },
    editable: {
        type: Boolean,
        default: true
    }
})
const formFields = computed(() => [
    {
        label: 'Customer Type',
        field: "customer_type",
        component: "Dropdown",
        required: true,
        event:'change',
        eventHandler:(e) => {
            filterCustomer(e.value)
        },
        props: {
            placeholder: "Select an option",
            optionLabel: "label",
            optionValue: 'value',
            options: props.customerTypeOpts,
            class:"w-full",
            showClear:true,
            disabled:!props.editable
        }
    },
    {
        label: "Customer Name",
        field: "customer_no",
        component: "Dropdown",
        required: true,
        props: {
            placeholder: "Select an option",
            optionLabel: "label",
            optionValue: 'value',
            options: customerOpts.value,
            class:"w-full",
            showClear:true,
            disabled:!props.editable
        }
    },
    {
        label: "Joint Level",
        field: "joint_level",
        component: "Dropdown",
        required: true,
        props: {
            placeholder: "Select an option",
            optionLabel: "label",
            optionValue: 'value',
            options: props.jointLevelOpts,
            class:"w-full",
            showClear:true,
            disabled:!props.editable
        }
    },
    {
        label: "Permission",
        field: "permission",
        component: "Dropdown",
        required: true,
        props: {
            placeholder: "Select an option",
            optionLabel: "label",
            optionValue: 'value',
            options: props.permissionOpts,
            class:"w-full",
            showClear:true,
            disabled:!props.editable
        }
    }
])
const loading = ref(false)
const customerOpts = ref([])
const filterCustomer = (customerType) => {
    if (customerType) {
        loading.value = true
        paService.searchInsuredPs({ customer_type: customerType }).then((res) => {
            customerOpts.value = res.data.map((item) => {
                item.label = item.customer_no + '-' + item.name_en
                item.value = item.customer_no
                return item
            })
        }).finally(() => loading.value = false)
    }
}
const changeJoint = (value) => {
    if (value) {
        const selectedCst = customerOpts.value.find(item => item.value === value)
        Object.assign(props.modelValue, {
            name_en: selectedCst.name_en,
            name_kh: selectedCst.name_kh
        })
    }
    emit('change')
}
onMounted(() => {
    if (props.dataId) filterCustomer(props.modelValue.customer_type)
})
</script>