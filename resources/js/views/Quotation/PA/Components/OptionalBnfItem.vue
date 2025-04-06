<template>
    <div class="flex">
        <div
            :class="{ 'flex-1 grid grid-cols-3 gap-x-5 py-2 px-3 bg-white': true, 'cursor-not-allowed pointer-events-none opacity-50': !modelValue.is_selected }">
            <div>
                <p>{{ benefit.label }}</p>
            </div>
            <div class="grid">
                <div class="flex">
                    <span class="mt-2 mr-3">Rating: </span>
                    <div class="w-full">
                        <InputNumber v-model="modelValue.rating" placeholder="Rate" class="w-full" :min="0" :max="100"
                        :minFractionDigits="0" :maxFractionDigits="2" />
                        <span class="text-error w-full block" v-if="ratingErr.length">{{ratingErr[0]}}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex">
                <span class="mt-2 mr-3">of</span>
                <div class="w-full">
                    <Dropdown v-model="modelValue.amount_type" class="w-full" placeholder="Select rating base" :options="of"
                    optionLabel="label" optionValue="value" />
                    <span class="text-error" v-if="amountTypeErr.length">{{amountTypeErr[0]}}</span>
                </div>
            </div>
        </div>
        <div class="w-8 pt-4">
            <Checkbox v-model="modelValue.is_selected" :binary="true" class="w-full" @change="handleSwitch" />
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: Object,
        default: {
            is_selected: false,
            rating: '',
            amount_type: ''
        }
    },
    benefit: {
        type: Object,
        default: {}
    },
    of: {
        type: Array,
        default: []
    },
    amountTypeErr:{
        type: Array,
        default: []
    },
    ratingErr:{
        type: Array,
        default: []
    }
});
const checked = ref([])
const handleSwitch = (event) => {
    if (!props.modelValue.is_selected) {
        Object.assign(props.modelValue, {
            rating: '',
            amount_type: '',
            is_selected:false
        })
    }
}
onMounted(() => {
    Object.assign(props.modelValue, {
        extension_id: props.benefit.id,
        extension_code: props.benefit.value,
        extension_name: props.benefit.label,
        extension_description: props.benefit.description
    })
})
</script>

<style lang="scss" scoped></style>