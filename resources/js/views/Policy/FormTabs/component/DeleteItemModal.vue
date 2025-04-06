<template>
 <Dialog :visible="true" :containerStyle="{width: '340px'}" :modal="true" position="bottom" :closable="false" header="Delete Vehicle" class="custom-dialog-top" >
    <div class="relative w-full py-1">
        <label class="text-sm">Endorsement Effective Date<span class="text-red-500">* {{validate}}</span></label>
    </div>
    <FormulateInput type="date" v-model="e_date" />
    <template #footer>
        <Button label="Cancel" class="p-button-danger p-button-text" @click="$emit('close')"/>
        <Button label="Delete Vehicle" autofocus @click="handleDelete" />
    </template>
</Dialog>
</template>
<script>
export default {
    props:['eDate'],
    data() {
        return {
            e_date:'',
            validate:'',
        }
    },
    methods: {
        handleDelete() {
            this.validate = ''
            this.$emit('handleRemoveItem', this.e_date);
        },
    },
}
</script>
