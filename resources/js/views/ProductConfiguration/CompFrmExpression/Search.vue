<template>
    <div>
        <div class="grid grid-cols-12 gap-3">
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" v-model="filterValues.search" placeholder="Search" @input="setFilter">
                </div>
            </div>
            <div class="intro-y col-span-6 lg:col-span-2">
                <div class="w-full text-gray-700">
                    <label class="form-label">Product</label>
                    <select v-model="filterValues.product" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(product, index) in productOptions" :key="index" :value="index">{{ index }} - {{ product }}</option>
                    </select>
                </div>
            </div>
            <div class="intro-y col-span-6 lg:col-span-2">
                <div class="w-full text-gray-700">
                    <label class="form-label">Cover Code</label>
                    <select v-model="filterValues.cover" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(cover, index) in compCodeOptions" :key="index" :value="index">{{ cover }}</option>
                    </select>
                </div>
            </div>
            <div class="intro-y col-span-6 lg:col-span-2">
                <div class="w-full text-gray-700">
                    <label class="form-label">Calculate Options</label>
                    <select v-model="filterValues.cal_option" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(cal_option, index) in calculateOptions" :key="index" :value="index">{{ cal_option }}</option>
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
                cover: '',
                status: '',
                cal_option:'',
            },
            productOptions: {},
            compCodeOptions: {},
            calculateOptions:{ STANDARD:'Standard', SPECIAL:'Special'},
            statuses: {
                ACT: 'Active',
            },

        }
    },
    methods: {
        setFilter() {
            this.$emit('setFilter', this.filterValues)
        },
        listProducts() {
            axios.get('/cover-service/list-auto-products').then(response => {
                this.productOptions = response.data
            })
        },
        listProdComp(){
            axios.get('/formula-service/list-product-comp').then(response =>{
                this.compCodeOptions = response.data
            })
        },
    },
    mounted() {
        this.listProducts();
        this.listProdComp();
    },
}
</script>
