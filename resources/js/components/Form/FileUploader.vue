<template>
    <div @dragover.prevent="" @dragleave="" @drop.prevent="dropFile">
        <label v-if="!content.name" :for="randomId"
            class="border-2 rounded-md border-dashed border-blue-500 border-opacity-70 cursor-pointer block items-center justify-center">
            <div class="w-full text-center my-2 text-md text-blue-600">Select a file <i
                    class="ml-1 pi pi-cloud-upload"></i>
            </div>
        </label>
        <div v-else
            class="border-2 rounded-md border-dashed border-green-600 border-opacity-70 block items-center justify-center">
            <div class="flex py-2 px-4">
                <div class="pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm">{{ content.name
                    }}
                </div>
                <div class="inline-flex justify-center">
                    <button type="button" class="inline-flex justify-center items-center text-red-500"
                        @click="removeContent"><i
                            :class="{ 'pi pi-times': !loading, 'pi pi-spinner pi-spin text-green-600': loading }"></i></button>
                </div>
            </div>
        </div>
        <input type="file" ref="fileInput" :accept="accept" @change="updateContent" class="hidden" :id="randomId">
    </div>
</template>
<script setup>
import { onMounted, ref } from 'vue';

const emit = defineEmits(['change', 'update:modelValue'])
const props = defineProps({
    modelValue: {
        type: Object,
        default: {}
    },
    accept: {
        type: String,
        default: ''
    },
    loading: {
        type: Boolean,
        default: false
    }
})
const content = ref({
    name: ''
})
const fileInput = ref()
const randomId = ref();
const uuidv4 = () => {
    return "10000000-1000-4000-8000-100000000000".replace(/[018]/g, c =>
        (+c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> +c / 4).toString(16)
    );
}
onMounted(() => {
    randomId.value = uuidv4()
})
const updateContent = (e) => {
    const file = e.target.files[0];
    content.value = file
    props.modelValue = content.value
    emit('change', content.value)
    emit('update:modelValue', file)
}
const removeContent = () => {
    document.getElementById(randomId.value).value = ''
    content.value = {}
    props.modelValue = null
    emit('change', null)
    emit('update:modelValue', null)
}
const dropFile = (e) => {
    const file = e.dataTransfer.files[0]
    content.value = file
    props.modelValue = content.value
    emit('change', file)
    emit('update:modelValue', file)
}
</script>