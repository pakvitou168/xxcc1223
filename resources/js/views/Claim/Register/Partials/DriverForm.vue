<template>
    <Dialog :visible="isVisible" :modal="true" :closable="false" :draggable="false"
        class="p-fluid w-5/12 custom-dialog z-10" position="top">
        <template #header>
            <span class="p-dialog-title flex-1">{{ header }}</span>
            <button @click="hideDialog" aria-label="close" type="button" tabindex="-1"
                class="p-dialog-header-icon p-dialog-header-close p-link" style="margin-right: 0">
                <span class="p-dialog-header-close-icon pi pi-times"></span>
            </button>
        </template>
        <div>
            <div class="intro-y box grid gap-y-2">
                <div class="grid lg:grid-cols-2 gap-x-5 gap-y-4">
                    <div>
                        <label for="" class="form-label">Name *</label>
                        <InputText type="text" v-model="formValues.name" placeholder="Name" />
                        <span class="text-error" v-if="errors['name']">{{ errors['name'][0] }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-x-5 gap-y-4">
                        <div>
                            <label for="" class="form-label">Gender *</label>
                            <Dropdown type="select" v-model="formValues.gender" optionValue="value" optionLabel="label"
                                placeholder="Select gender" :options="genderOptions" />
                            <span class="text-error" v-if="errors['gender']">{{ errors['gender'][0] }}</span>
                        </div>
                        <div>
                            <label for="" class="form-label">Driver Age *</label>
                            <InputNumber v-model="formValues.driver_age" placeholder="Driver age"
                                :useGrouping="false" />
                            <span class="text-error" v-if="errors['driver_age']">{{ errors['driver_age'][0] }}</span>
                        </div>
                    </div>
                    <div>
                        <label for="" class="form-label">Occupation</label>
                        <InputText type="text" v-model="formValues.occupation" placeholder="Occupation" />
                        <span class="text-error" v-if="errors['occupation']">{{ errors['occupation'][0] }}</span>
                    </div>
                    <div>
                        <label for="" class="form-label">License No. *</label>
                        <InputText type="text" v-model="formValues.license_no" placeholder="License no." />
                        <span class="text-error" v-if="errors['license_no']">{{ errors['license_no'][0] }}</span>
                    </div>
                    <div>
                        <label for="" class="form-label">License Issued Date *</label>
                        <Calendar showIcon type="text" v-model="formValues.license_issue_date"
                            placeholder="License issued date" />
                        <span class="text-error" v-if="errors['license_issue_date']">{{ errors['license_issue_date'][0] }}</span>
                    </div>
                    <div>
                        <label for="" class="form-label">License Expire Date *</label>
                        <Calendar showIcon type="text" v-model="formValues.license_expire_date"
                            placeholder="License expiry date" />
                        <span class="text-error"
                            v-if="errors['license_expire_date']">{{ errors['license_expire_date'][0] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Close" class="p-button-secondary p-button-text" @click="hideDialog" />
            <Button label="Save" :loading="isLoading" class="p-button-info" autofocus @click="handleSubmitDriver" />
        </template>
    </Dialog>
</template>

<script>

import DriverLicense from '@/services/claim/driver_license.service'

export default {
    props: {
        header: String,
        isVisible: Boolean,
        submitted: Boolean,
        genderOptions: Array
    },

    data() {
        return {
            formValues: {
                name: '',
                gender: '',
                driver_age: null,
                occupation: '',
                license_no: '',
                license_issue_date: '',
                license_expire_date: ''
            },
            errors: {},
            isLoading: false
        }
    },

    methods: {
        handleSubmitDriver() {
            this.isLoading = true

            const method = this.id ? "PUT" : "POST"
            DriverLicense.save(
                {
                    ...this.formValues,
                    ...(method === "PUT" && { id: this.id })
                },
                method
            )
                .then(res => {
                    this.$notify({
                        group: 'bottom',
                        title: 'Success',
                        text: res.data?.message,
                    }, 4000);

                    let CreatedDriverLicense = { value: res.data?.data.id, label: res.data?.data.label }
                    this.$emit('setDriverId', CreatedDriverLicense)

                    this.hideDialog()
                })
                .catch(err => {
                    if (err?.response?.status === 422) {
                        notify("Validation failed", 'error');
                        this.errors = err.response.data.errors
                    } else {
                        notify(err?.response?.data?.message, 'error');
                    }
                })
                .finally(() => this.isLoading = false);
        },

        hideDialog() {
            this.formValues = {}

            this.errors = {}

            this.$emit('hideDialog')
        },
    },
}
</script>