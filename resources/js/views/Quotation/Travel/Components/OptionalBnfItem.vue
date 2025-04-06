<template>
    <div class="flex justify-between p-2 bg-white">
        <div >
            <p>{{ benefit.name }}</p>
        </div>
        <div class="w-8">
            <Checkbox v-model="modelValue.is_selected" :binary="true" class="w-full" @change="handleSwitch" :disabled="modelValue.category === 'AGG_LIMIT'"/>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: Object,
        default: {
            is_selected: {
                type : Boolean,
                default : false
            },
            id: null,
        }
    },
    benefit: {
        type: Object,
        default: {}
    }
});
const handleSwitch = (event) => {
        Object.assign(props.modelValue, {
            is_selected:props.modelValue.is_selected
        })
}
</script>