<template>
    <div class="flex pt-4">
        <div v-if="modelValue.id" class="flex-1 grid grid-cols-9 gap-4">
            <div v-for="(form) in formFields" class="">
                <div v-if="!form.component" class="text-sm">{{ modelValue[form.field] }}</div>
                <component v-else :is="form.component" v-model="modelValue[form.field]" v-bind="form.props"></component>
            </div>
        </div>
        <div v-else class="flex-1 grid grid-cols-5 gap-4 mb-6">
            <div v-for="form in addedFields">
                <label for="" class="form-label">{{ form.label }} <span v-if="form.required"
                        class="text-red-600">*</span></label>
                <component :is="form.component" v-model="modelValue[form.field]" v-bind="form.props"></component>
            </div>
        </div>
        <div :class="{ 'w-[4rem] flex justify-end items-start': 1 }">
            <Button icon="pi pi-minus-circle" title="Add to delete" v-if="!modelValue.deleted_at && canRemove" @click="removeRow" />
            <Button icon="pi pi-times-circle" title="Pending removal" class="text-red-600 bg-opacity-50" v-if="modelValue.deleted_at"
                @click="clearRemove" />
        </div>
    </div>
</template>

<script setup>
import { now } from 'lodash';
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Object,
        default: {}
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
    },
    dataId: {
        type: [Number, String],
        default: ''
    },
})
const canRemove = computed(() => {
    return !(props.defaultGroups.length ? props.defaultGroups.includes(props.modelValue.group_code) && props.modelValue.id : false)
})
const participantOpts = computed(() => {
    return props.modelValue.group_code ? props.participants.filter(item => item.group_code === props.modelValue.group_code) : []
})
const emit = defineEmits(['removeRow'])
const addedFields = computed(() => {
    return [
        {
            label: 'Partner Group',
            field: "group_code",
            component: 'Dropdown',
            required: true,
            props: {
                placeholder: "Select an option",
                class: 'w-full',
                options: props.types,
                optionLabel: 'label',
                optionValue: 'value'
            }
        },
        {
            label: 'Participant',
            field: "treaty_code",
            component: 'Dropdown',
            required: true,
            props: {
                placeholder: "Select an option",
                class: 'w-full',
                options: participantOpts.value,
                optionLabel: 'label',
                optionValue: 'value'
            }
        },
        {
            label: 'Share (%)',
            field: "share",
            component: 'InputNumber',
            required: true,
            props: {
                max: 100,
                minFractionDigits: 0,
                maxFractionDigits: 2,
                class: 'w-full',
            }
        },
        {
            label: 'RI Commission (%) ',
            field: "ri_commission",
            component: 'InputNumber',
            required: true,
            props: {
                max: 100,
                minFractionDigits: 0,
                maxFractionDigits: 2,
                class: 'w-full',
            }
        },
        {
            label: 'Tax & Fee (%) ',
            field: "tax_fee",
            component: 'InputNumber',
            required: true,
            props: {
                max: 100,
                minFractionDigits: 0,
                maxFractionDigits: 2,
                class: 'w-full',
            }
        }
    ]
})
const formFields = computed(() => {
    return [
        {
            field: 'reinsurance_type'
        },
        {
            field: "participant"
        },
        {
            field: 'share',
            component: 'InputNumber',
            props: {
                max: 100,
                minFractionDigits: 0,
                maxFractionDigits: 2,
                disabled:props.modelValue.deleted_at
            }
        },
        {
            field: 'premium',
            component: 'InputNumber',
            props: {
                max: 100,
                minFractionDigits: 0,
                maxFractionDigits: 2,
                disabled: true,
            }
        },
        {
            field: 'ri_commission',
            component: 'InputNumber',
            props: {
                max: 100,
                minFractionDigits: 0,
                maxFractionDigits: 2,
                disabled:props.modelValue.deleted_at
            }
        },
        {
            field: "ri_commission_amt",

            component: 'InputNumber',
            props: {
                max: 100,
                minFractionDigits: 0,
                maxFractionDigits: 2,
                disabled: true,
            }
        },
        {
            field: "tax_fee",
            component: 'InputNumber',
            props: {
                max: 100,
                minFractionDigits: 0,
                maxFractionDigits: 2,
                disabled:props.modelValue.deleted_at
            }
        },
        {
            field: 'tax_fee_amt',
            component: 'InputNumber',
            props: {
                max: 100,
                minFractionDigits: 0,
                maxFractionDigits: 2,
                disabled: true,
            }
        },
        {
            field: 'net_premium',
            component: 'InputNumber',
            props: {
                max: 100,
                minFractionDigits: 0,
                maxFractionDigits: 2,
                disabled: true,
            }
        }
    ]
})
const removeRow = () => {
    if (props.modelValue.id) props.modelValue.deleted_at = now()
    else emit('removeRow')

}
const clearRemove = () => {
    props.modelValue.deleted_at = null
}
</script>