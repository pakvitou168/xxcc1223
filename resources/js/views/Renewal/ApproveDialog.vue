<template>
    <Dialog
        :visible="isVisible"
        :style="{ width: '20rem' }"
        :modal="true"
        :closable="false"
        :header="header"
    >
    <div class="grid gap-5">
        <div>
            <label for="" class="form-label">Status</label>
            <div class="grid grid-cols-2">
                <div class="align-items-center ml-2" v-for="(option,index) in options" :key="index">
                    <RadioButton v-model="form.status" :inputId="'status'+option.value" :value="option.value" />
                    <label :for="'status'+option.value" class="ml-2">{{option.label}}</label>
                </div>
            </div>
            <span class="text-error" v-if="errorStatus">{{errorStatus}}</span>
        </div>
        <div>
            <label for="" class="form-label">Remark</label>
            <Textarea v-model="form.reason" placeholder="Remark" rows="4" class="w-full"/>
            <span class="text-error" v-if="errorReason">{{errorReason}}</span>
        </div>
    </div>
        <template #footer>
            <Button
                label="Cancel"
                class="p-button-danger p-button-text"
                :disabled="loading"
                @click="hideDialog"
            ></Button>
            <Button autofocus class="p-button-info" :disabled="loading" @click="$emit('confirm', form)">Confirm<span v-if="loading">...</span></Button>
        </template>
    </Dialog>
</template>

<script>
export default {
    props: {
        header: String,
        isVisible: Boolean,
        submitted: Boolean,
        options: Object,
        value: String,
        loading: Boolean,
    },
    data() {
        return {
            form: {
                reason: "",
                status: ""
            }
        };
    },
    computed: {
        errorReason() {
            return this.submitted && !this.form.reason
                ? "Reason is required."
                : null;
        },
        errorStatus(){
            return this.submitted && !this.form.status ? "Status is required" : null
        }
    },
    methods: {
        hideDialog() {
            this.form = {
                reason: "",
                status: ""
            };

            this.$emit("hideDialog", this.form);
        }
    }
};
</script>
