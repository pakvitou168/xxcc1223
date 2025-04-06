<template>
    <Dialog :visible="isVisible" :style="{ width: '340px' }" :modal="true" :closable="false" :header="header">
        <div class="grid gap-y-5">
            <div v-if="hasOption">
                <label for="" class="form-label">Status *</label>
                <div class="w-full grid grid-cols-2">
                    <div class="align-items-center mr-2" v-for="(option) in options">
                        <RadioButton v-model="form.status" :inputId="'ingredient' + index" :name="option.label"
                            :value="option.value" />
                        <label :for="'ingredient' + index" class="ml-2">{{ option.label }}</label>
                    </div>
                </div>
                <span class="text-error" v-if="errors['status']">{{ errors['status'][0] }}</span>
            </div>
            <div>
                <label for="" class="form-label">Remark *</label>
                <Textarea v-model="form.comment" placeholder="Remark" class="w-full" rows="4" />
                <span class="text-error" v-if="errors['comment']">{{ errors['comment'][0] }}</span>
            </div>
        </div>
        <template #footer>
            <Button label="Cancel" class="p-button-secondary" @click="hideDialog" />
            <Button label="Confirm" class="p-button-info" :loading="submitted" autofocus @click="$emit('confirm', form)" />
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
        errors: {
            type:Array,
            default:[]
        }
    },
    data() {
        return {
            form: {
                comment: '',
                status: '',
            }
        }
    },
    computed: {
        errorComment() {
            return (this.submitted && !this.form.comment) ? 'Comment is required.' : null
        },
        hasOption() {
            return Object.keys(this.options).length
        }
    },
    methods: {
        hideDialog() {
            this.form = {
                comment: '',
                status: '',
            }

            this.$emit('hideDialog', this.form)
        }
    },
}
</script>