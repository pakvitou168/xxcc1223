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
                    <label class="form-label">Name (English) *</label>
                    <InputText type="text" v-model="form.name_en" placeholder="Name in English" />
                    <span class="p-error block" v-if="errors.name_en">{{ errors.name_en[0] }}</span>
                </div>
                <div>
                    <label class="form-label">Name (Khmer) *</label>
                    <InputText type="text" v-model="form.name_kh" placeholder="Name in Khmer" />
                    <span class="p-error block" v-if="errors.name_kh">{{ errors.name_kh[0] }}</span>
                </div>
                <div>
                    <label class="form-label">Payee Type *</label>
                    <Dropdown optionLabel="label" :options="payeeTypes" optionValue="value" v-model="form.type"
                        placeholder="Select payee type" />
                    <span class="p-error block" v-if="errors.type">{{ errors.type[0] }}</span>
                </div>
                <div>
                    <label class="form-label">Phone Number</label>
                    <InputText type="text" v-model="form.phone_number" placeholder="Phone Number" />
                    <span class="p-error block" v-if="errors.phone_number">{{ errors.phone_number[0] }}</span>
                </div>
                <div>
                    <label class="form-label">Address *</label>
                    <Textarea type="text" v-model="form.address" placeholder="Address" />
                    <span class="p-error block" v-if="errors.address">{{ errors.address[0] }}</span>
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <Textarea type="text" v-model="form.description" placeholder="Description" />
                    <span class="p-error block" v-if="errors.description">{{ errors.description[0] }}</span>
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
import PayeeService from '@/services/claim/payee.service.js'

export default {
    props: {
        isVisible: Boolean,
        payeeTypes: {},
    },
    data() {
        return {
            ERROR_MESSAGE:"Something went wrong!",
            SUCCESS_MESSAGE:"Success!",
            form: {
                name_kh: '',
                name_en: '',
                phone_number: '',
                type: null,
                payee_address: '',
                description: ''
            },
            isLoading: false,
            errors: {},
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
                    notify(res.data?.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
                    let createdPayeeType = { value: res.data?.data.id, label: res.data?.data.name_en }
                    this.$emit('setPayeeTypeId', createdPayeeType)
                    this.hideDialog()
                })
                .catch(err => {
                    if (err.response?.status === 422) {
                      this.errors = err.response.data.errors;
                    }
                    notify(err?.response?.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
                    console.error(err)
                })
                .finally(() => this.isLoading = false);
        },
        hideDialog() {
            this.form =  {
                name_kh: '',
                name_en: '',
                phone_number: '',
                type: null,
                payee_address: '',
                description: ''
            }
            this.errors ={}
            this.$emit('hideDialog')
        }
    },
}
</script>
