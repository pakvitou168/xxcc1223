<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Business / Occupation
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-6">
                <div class="intro-y box p-5">
                    <form @submit.prevent="handleSubmit">
                        <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Group Code *</label>
                                <Dropdown
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.group_code"
                                    :options="Object.entries(groupCodeOptions).map(([value, label]) => ({value, label}))"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Select Group Code"
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Description (English) *</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.description"
                                    placeholder="Description (English)"
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Description (Khmer)</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.description_kh"
                                    placeholder="Description (Khmer)"
                                />
                            </div>

                            <div class="col-span-12">
                                <label class="mb-1 block font-bold">Description (Chinese)</label>
                                <InputText
                                    class="w-full p-inputtext-sm"
                                    v-model="formValues.description_zh"
                                    placeholder="Description (Chinese)"
                                />
                            </div>
                        </div>

                        <div class="text-right mt-5">
                            <router-link to="/customer-management/customer-classifications" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
                            <button type="submit" class="btn btn-primary w-24">
                                <span v-if="id">Update</span>
                                <span v-else>Create</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            id: this.$route.params.id ?? null,
            formValues: {},
            groupCodeOptions: {
                'INDIVIDUAL': 'INDIVIDUAL',
                'CORPORATE': 'CORPORATE'
            }
        }
    },
    methods: {
        handleSubmit() {
            if (!this.id) {
                axios.post('/customer-classifications', this.formValues).then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'success','bottom-right')
                        this.$router.push({ name: 'CustomerClassificationIndex'})
                    }
                }).catch(() => notify('Error', 'error','bottom-right'))
            
            } else {
                axios.put(`/customer-classifications/${this.id}`, this.formValues).then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'success','bottom-right')
                        this.$router.push({ name: 'CustomerClassificationIndex'})
                    }
                }).catch(() => notify('Error', 'error','bottom-right'))
            }
        },
        getCustomerClassification() {
            if (this.id) {
                axios.get(`/customer-classifications/${this.id}/edit`)
                    .then(response => {
                        this.formValues = response.data;
                    })
            }
        },
    },
    mounted() {
        this.getCustomerClassification()
    }
}
</script>