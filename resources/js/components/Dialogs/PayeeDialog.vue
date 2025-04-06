<template>
    <Dialog :visible="isVisible" :modal="true" :closable="false" :draggable="false"
        class="p-fluid w-5/12 custom-dialog z-10" position="top">
        <template #header>
            <span class="p-dialog-title flex-1">Add Payee</span>
            <button @click="hideDialog" aria-label="close" type="button" tabindex="-1"
                class="p-dialog-header-icon p-dialog-header-close p-link" style="margin-right: 0">
                <span class="p-dialog-header-close-icon pi pi-times"></span>
            </button>
        </template>
        <div>
            <div class="grid lg:grid-cols-2 gap-5">
                <div>
                    <labe class="form-label">Name (English) *</labe>
                    <InputText type="text" v-model="form.name_en" placeholder="Name in English" />
                    <span class="text-error" v-if="errors['name_en']">{{ errors['name_en'][0] }}</span>
                </div>
                <div>
                    <labe class="form-label">Name (Khmer) *</labe>
                    <InputText type="text" v-model="form.name_kh" placeholder="Name in Khmer" />
                    <span class="text-error" v-if="errors['name_kh']">{{ errors['name_kh'][0] }}</span>
                </div>
                <div>
                    <labe class="form-label">Payee Type *</labe>
                    <Dropdown optionLabel="label" :options="payeeTypes" optionValue="value" v-model="form.type"
                        placeholder="Select payee type" />
                    <span class="text-error" v-if="errors['type']">{{ errors['type'][0] }}</span>
                </div>
                <div>
                    <labe class="form-label">Phone Number</labe>
                    <InputText type="text" v-model="form.phone_number" placeholder="Phone Number" />
                    <span class="text-error" v-if="errors['phone_number']">{{ errors['phone_number'][0] }}</span>
                </div>
                <div>
                    <labe class="form-label">Address *</labe>
                    <Textarea type="text" v-model="form.address" rows="4" placeholder="Address" />
                    <span class="text-error" v-if="errors['address']">{{ errors['address'][0] }}</span>
                </div>
                <div>
                    <labe class="form-label">Description</labe>
                    <Textarea type="text" v-model="form.description" rows="4" placeholder="Description" />
                    <span class="text-error" v-if="errors['description']">{{ errors['description'][0] }}</span>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Cancel" class="p-button-secondary p-button-text" outlined @click="hideDialog()" />
            <Button label="Submit" class="p-button-info" :loading="isLoading" :disabled="isLoading" autofocus @click="handleSubmit" />
        </template>
    </Dialog>
</template>
<script>
import PayeeService from '@/services/claim/payee.service'

export default {
    props: {
        isVisible: Boolean,
        payeeTypes: {},
    },
    data() {
        return {
            form: {
                name_en: '',
                type: null,
                payee_address: '',
                description: ''
            },
            isLoading: false,
            errors: [],
        }
    },
    methods: {
        handleSubmit() {
            this.isLoading = true
            const method = this.id ? "PUT" : "POST"
            PayeeService.save(
                {
                    ...this.form,
                    ...(method === "PUT" && { id: this.id })
                },
                method
            )
                .then(res => {
                    notify(res.data?.message, 'success')
                    let createdPayeeType = { value: res.data?.data.id, label: res.data?.data.name_en }
                    this.$emit('setPayeeTypeId', createdPayeeType)
                    this.hideDialog()
                })
                .catch(err => {
                    if (err?.response?.status === 422) {
                        notify('Validation Error', 'error')
                        this.errors = err.response.data.errors
                    } else {
                        notify(err?.response?.data?.message, 'error')
                    }
                })
                .finally(() => this.isLoading = false);
        },
        hideDialog() {
            this.form.name_en = ''
            this.form.name_kh = ''
            this.form.type = null
            this.form.payee_address = ''
            this.form.description = ''
            this.form.phone_number = ''
            this.form.address = ''
            this.errors = []
            this.$emit('hideDialog')
        }
    },
}
</script>
