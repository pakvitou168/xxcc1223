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
            <div class="intro-y box grid gap-y-2 mt-5 p-5">
                <div class="grid lg:grid-cols-2 gap-x-2 gap-y-2">
                    <div class="">
                      <label class=" block mb-1 font-bold" for="">Name *</label>
                      <InputText
                         placeholder="Name"
                         name="name"
                         v-model="formValues.name"
                      />
                      <ul v-if="errors.name" class="formulate-input-errors">
                        <li class="formulate-input-error">{{ errors.name[0] }}</li>
                      </ul>
                    </div>
                    <div class="">
                      <label class=" block mb-1 font-bold" for="">Type *</label>
                      <InputText
                         placeholder="Type"
                         name="type"
                         v-model="formValues.type"
                      />
                      <ul v-if="errors.type" class="formulate-input-errors">
                        <li class="formulate-input-error">{{ errors.type[0] }}</li>
                      </ul>
                    </div>
                    <div class="">
                      <label class=" block mb-1 font-bold" for="">Contact name</label>
                      <InputText
                         placeholder="Contact name"
                         name="contact_name"
                         v-model="formValues.contact_name"
                      />
                      <ul v-if="errors.contact_name" class="formulate-input-errors">
                        <li class="formulate-input-error">{{ errors.contact_name[0] }}</li>
                      </ul>
                    </div>
                  <div class="">
                      <label class=" block mb-1 font-bold" for="">Contact number</label>
                      <InputText
                         placeholder="Contact number"
                         name="contact_number"
                         v-model="formValues.contact_number"
                      />
                      <ul v-if="errors.contact_number" class="formulate-input-errors">
                        <li class="formulate-input-error">{{ errors.contact_number[0] }}</li>
                      </ul>
                    </div>
                </div>
                <div class="grid grid-cols-1">
                  <label for="" class="font-bold mb-1 block">Address</label>
                  <Textarea placeholder="Address" v-model="formValues.address"
                            class="w-full" rows="5" cols="30"/>
                  <ul v-if="errors.address" class="formulate-input-errors">
                    <li class="formulate-input-error">{{ errors.address[0] }}</li>
                  </ul>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Close" class="btn btn-danger" @click="hideDialog" />
            <Button label="Add" class="btn btn-primary w-24" :loading="isLoading" type="button" autofocus @click="handleSubmitClinic" />
        </template>
    </Dialog>
</template>

<script>

import clinicService from '@/services/claim/hs/clinic.service'

export default {
    props: {
        header: String,
        isVisible: Boolean,
        submitted: Boolean
    },

    data() {
        return {
            formValues: {
                name: '',
                type: '',
                contact_name: '',
                contact_number: '',
                address: ''
            },
            typeOptions: { 'PANEL': 'Panel', 'NON_PANEL': 'Non panel' },
            errors: {},
            isLoading: false,
        }
    },

    methods: {
        handleSubmitClinic() {
            this.isLoading = true

            const method = this.id ? "PUT" : "POST"
            clinicService.store(
                {
                    ...this.formValues,
                    ...(method === "PUT" && { id: this.id })
                },
                method
            )
                .then(res => {
                    let response = res.data
                    this.$notify({
                        group: 'bottom',
                        title: 'Success',
                        text: res.data?.message,
                    }, 4000);

                    let createdClinic = { code: response?.data.id, name: response?.data.name,type:response?.data.type }
                    this.$emit('setSelectedClinic', createdClinic)
                    this.hideDialog()
                })
                .catch(err => {
                    if (err?.response?.status === 422) {
                        this.$notify({
                            group: 'bottom',
                            title: 'Error',
                            text: 'Validation Error',
                        }, 4000);

                        this.errors = err.response.data.errors
                    } else {
                        this.$notify({
                            group: 'bottom',
                            title: 'Error',
                            text: err?.response?.data?.message,
                        }, 4000);
                    }
                })
                .finally(() => this.isLoading = false);
        },

        hideDialog() {
            this.formValues.name = ''
            this.formValues.type = ''
            this.formValues.contact_name = ''
            this.formValues.contact_number = ''
            this.formValues.address = ''

            this.errors = {}

            this.$emit('hideDialog')
        },
    },
}
</script>