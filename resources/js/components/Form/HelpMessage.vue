<template>
    <span :class="textClass">{{ message }} hello</span>
</template>
<script setup>
import { computed } from 'vue';

const props = defineProps({
    message: {
        type: String,
        default: ''
    },
    serverity: {
        type: String,
        default: 'error'
    },
    class:{
        type:String,
        default:''
    }
})
const textClass = computed(() => {
    return (colors[props.serverity] ?? colors.info)+ props.class;
})
const colors = {
    error: 'text-red-500',
    info: 'text-blue-500',
    warning: 'text-orange-500',
    default: 'text-slate-500'
}
</script>