<template>
    <div>
        <Dialog v-model:visible="visible" modal :header="title" class="border-t-4 border-blue-500"
            :style="{ width: '25rem' }">
            <div class="w-full grid gap-4 mb-4">
                <div>
                    <label for="" class="form-label">Status *</label>
                    <div class="mb-3 ml-2" v-for="(option, index) in options">
                        <RadioButton v-model="form.status" :value="option.value" @change="changeOption($event.value)" />
                        <label for="" class="ml-2">{{ option.label }}</label>
                    </div>
                    <span class="text-error" v-if="errors.status">{{errors.status[0]}}</span>
                </div>
                <div>
                    <label for="" class="form-label">Remark *</label>
                    <Textarea v-model="form.remark" placeholder="Remark" class="w-full" rows="4" />
                    <span class="text-error" v-if="errors.remark">{{errors.remark[0]}}</span>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <Button type="button" label="Cancel" severity="secondary" @click="visible = false"></Button>
                <Button type="button" class="p-button-info" icon="pi pi-save" @click="handleSubmit" label="Save"
                    :loading="loading"></Button>
            </div>
        </Dialog>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    title: {
        type: String,
        default: 'Confirm'
    },
    options: {
        type: Array,
        default: []
    },
    loading: {
        type: Boolean,
        default: false
    },
    errors:{
        type: Array,
        default: []
    }
})
const emit = defineEmits(['confirm'])
const form = ref({})
const visible = ref(false)
const toggleDialog = () => {
    visible.value = !visible.value
}
const changeOption = (value) => {
    console.log(value)
}
const handleSubmit = () => {
    emit('confirm',form.value)
}
defineExpose({
    toggleDialog
})
</script>

<style lang="scss" scoped></style>