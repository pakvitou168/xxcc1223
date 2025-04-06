<template>
    <div>
        <div class="grid grid-cols-12 gap-3">
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">User</label>
                    <select v-model="filterValues.user" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(user, index) in userOptions" :key="index" :value="index">{{ user }}</option>
                    </select>
                </div>
            </div>
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">Make</label>
                    <select v-model="filterValues.make" class="form-select" @change="setFilter" @input="changeMake">
                        <option value="">All</option>
                        <option v-for="(make, index) in makeOptions" :key="index" :value="index">{{ make }}</option>
                    </select>
                </div>
            </div>
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">Model</label>
                    <select v-model="filterValues.model" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(model, index) in modelOptions" :key="index" :value="index">{{ model }}</option>
                    </select>
                </div>
            </div>
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">Status</label>
                    <select v-model="filterValues.status" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(status, index) in statuses" :key="index" :value="index">{{ status }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "axios";

export default {
    data() {
        return {
            filterValues: {
                user: '',
                make: '',
                model: '',
                status: '',
            },
            userOptions: {},
            makeOptions: {},
            modelOptions: {},
            statuses: {
                ACT: 'Active',
            },
        }
    },
    methods: {
        setFilter() {
            this.$emit('setFilter', this.filterValues)
        },
        getAccessRuleServices() {
            axios.get('/access-rule-service/get-services').then(response => {
                this.userOptions = response.data.userOptions
                this.makeOptions = response.data.makeOptions
            })
        },
        changeMake(e) {
            axios.get('/access-rule-service/list-models-by-make-id/'+ e.target.value).then(response => {
                this.modelOptions = response.data
            })
        },
    },
    mounted() {
        this.getAccessRuleServices()
    },
}
</script>
