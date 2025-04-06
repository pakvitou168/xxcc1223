<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Access Rule
            </h2>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12 lg:col-span-6 p-5">
                <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">User *</label>
                        <Dropdown 
                            class="w-full p-inputtext-sm"
                            v-model="formValues.user_id"
                            :options="userOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="User"
                        />
                    <span v-if="errors.user_id" class="text-theme-6">
                        {{ errors.user_id[0] }}
                    </span>
                    </div>
                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Make *</label>
                        <Dropdown
                            v-model="formValues.make_id"
                            :options="makeOptions"
                            optionLabel="label" 
                            optionValue="value"
                            placeholder="Make"
                            class="w-full p-inputtext-sm"
                            @change="changeMake"
                            >
                        </Dropdown>
                         <span v-if="errors.make_id" class="text-theme-6">
                        {{ errors.make_id[0] }}
                        </span>
                    </div>
                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Model </label>
                        <Dropdown 
                            class="w-full p-inputtext-sm"
                            v-model="formValues.model_id"
                            :options="modelOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Model"
                        />
                    </div>
                    <div class="grid grid-cols-12 gap-x-12">
            
                    <div class="col-span-12 lg:col-span-6">
                        <label class="block mb-1 font-bold">Offline</label>
                        <Checkbox
                            v-model="formValues.allow_offline"
                            :binary="true"
                        />
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        <label class="block mb-1 font-bold">Online</label>
                        <Checkbox
                            v-model="formValues.allow_online"
                            :binary="true"
                        />
                        </div>
                    </div>            
                </div>
                <div class="text-right mt-5">
                    <router-link to="/product-configuration/access-rules" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
                    <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
                        <span v-if="id">Update</span>
                        <span v-else>Create</span>
                    </button>
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
            formValues: {
                make_id: null,
                model_id: null,
                allow_offline: false,  
                allow_online: false,
                user_id: null
            }, // Initialize with empty values
            userOptions: [],
            makeOptions: [],
            modelOptions: [],
            errors: {},
        }
    },
    methods: {
        async handleSubmit() {
            try {
            const endpoint = !this.id ? '/access-rules' : `/access-rules/${this.id}`
            const response = !this.id 
                ? await axios.post(endpoint, this.formValues)
                : await axios.put(endpoint, this.formValues)

            if (response.data.success) {
                notify(response.data.message, 'success', 'bottom-right')
                this.$router.push({ name: 'AccessRuleIndex' })
            }
            } catch (err) {
            if (err.response?.status === 422) {
                this.errors = err.response.data.errors
            } else {
                notify('Error', 'error', 'bottom-right')
            }
            }
        },
        async getAccessRuleServices() {
            try {
                const response = await axios.get('/access-rule-service/get-services')
                this.userOptions = Object.entries(response.data.userOptions).map(([value, label]) => ({
                    value: parseInt(value),
                    label
                }))
                this.makeOptions = Object.entries(response.data.makeOptions).map(([value, label]) => ({
                    value: parseInt(value),
                    label
                }))
                
                // If we have a make_id in formValues, load models after makes are loaded
                if (this.formValues.make_id) {
                    await this.changeMake(this.formValues.make_id)
                }
            } catch (error) {
                notify('Error loading options', 'error', 'bottom-right')
            }
        },
        changeMake(e) {
            if (!e) return
            axios.get('/access-rule-service/list-models-by-make-id/'+ e).then(response => {
                this.modelOptions = Object.entries(response.data).map(([value, label]) => ({
                    value: parseInt(value),
                    label
                }))
            })
        },
        getAccessRule() {
            if (this.id) {
                axios.get(`/access-rules/${this.id}`).then(response => {
                    this.formValues = response.data
                })
            }
        }
    },
    async mounted() {
        // First load the access rule if editing
        if (this.id) {
            this.getAccessRule()
        }
        await this.getAccessRuleServices()
        this.$watch('formValues.make_id', (newVal) => {
            if (newVal) {
                this.changeMake(newVal)
            }
        })
    }
}
</script>
