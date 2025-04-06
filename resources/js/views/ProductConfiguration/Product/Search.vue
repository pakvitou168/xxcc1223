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
                    <label class="form-label">Product Line</label>
                    <select v-model="filterValues.productLine" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="productLine in productLineOptions" :key="productLine" :value="productLine">{{ productLine }}</option>
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
                productLine: '',
                status: '',
            },
            productLineOptions: {},
            statuses: {
                ACT: 'Active',
            },
        }
    },
    methods: {
        setFilter() {
            this.$emit('setFilter', this.filterValues)
        },
        listProductLines() {
            axios.get("/product-line-service/list-product-lines").then((response) => {
                this.productLineOptions = response.data;
            });
        },
    },
    mounted() {
        this.listProductLines();
    },
}
</script>
