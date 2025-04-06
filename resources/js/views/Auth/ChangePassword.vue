<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                Change Password
            </h2>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12 lg:col-span-6 p-5">
                <div class="grid gap-5">
                    <div v-for="form in formFields">
                        <label for="" class="form-label">{{ form.label }} <span v-if="form.required"
                                class="text-red-600">*</span></label>
                        <component :is="form.component" v-bind="form.props" v-model="formValues[form.field]" />
                        <span class="text-error" v-if="errors[form.field]">{{ errors[form.field][0] }}</span>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <router-link :to="{ name: 'Dashboard' }" class="button-default mr-1"
                        tag="button">Cancel</router-link>
                    <Button type="submit" class="button-primary leading-6" @click="handleSubmit" label="Submit"
                        icon="pi pi-save" :loading="loading">
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter()
const loading = ref(false)
const formValues = ref({});
const errors = ref({})
const formFields = computed(() => {
    return [
        {
            label: "Curernt password",
            component: 'Password',
            field: 'current_password',
            props: {
                type: 'password',
                placeholder: "Current password",
                autocomplete: 'new-password',
                toggleMask:true,
                class:'w-full'
            }
        },
        {
            label: "New password",
            component: 'Password',
            field: 'new_password',
            props: {
                type: 'password',
                placeholder: "New password",
                autocomplete: 'new-password',
                toggleMask:true,
                class:'w-full'
            }
        },
        {
            label: "Confirm password",
            component: 'Password',
            field: 'confirm_password',
            props: {
                type: 'password',
                placeholder: "Confirm password",
                autocomplete: 'new-password',
                toggleMask:true,
                class:'w-full'
            }
        }
    ]
})
const handleSubmit = () => {
    loading.value =true
    axios.post('/auth/change-password', formValues.value).then(response => {
        if (response.data.success) {
            notify(response.data.message, 'success')
            router.push({ name: 'Dashboard' })
        }
    }).catch(err => {
        notify(err.response?.data?.message, 'error')
    }).finally(() => loading.value = false)
}
</script>