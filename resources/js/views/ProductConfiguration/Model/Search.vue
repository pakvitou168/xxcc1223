<template>
    <div>
        <div class="grid grid-cols-12 gap-3">
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" v-model="filterValues.search" placeholder="Search" @input="setFilter">
                </div>
            </div>
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">Product</label>
                    <select v-model="filterValues.product" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(product, index) in productOptions" :key="index" :value="index">{{ index }} - {{ product }}</option>
                    </select>
                </div>
            </div>
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">Make</label>
                    <select v-model="filterValues.make" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(make, index) in makeOptions" :key="index" :value="index">{{ make }}</option>
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
                search: '',
                product: '',
                make: '',
                status: '',
            },
            makeOptions: {},
            productOptions: {},
            statuses: {
                ACT: 'Active',
            },
        }
    },
    methods: {
        setFilter() {
            this.$emit('setFilter', this.filterValues)
        },
        getModelServices() {
            axios.get('/model-service/get-model-services').then(response => {
                this.productOptions = response.data.productOptions
                this.makeOptions = response.data.makeOptions
            })
        },
    },
    mounted() {
        this.getModelServices()
    },
}
</script>
