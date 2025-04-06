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
                    <label class="form-label">Cover Name</label>
                    <select v-model="filterValues.cover" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(cover, index) in compCodeOptions" :key="index" :value="index">{{ cover }}</option>
                    </select>
                </div>
            </div>
             <div class="intro-y col-span-6 lg:col-span-2">
                <div class="w-full text-gray-700">
                    <label class="form-label">Formula Code</label>
                    <select v-model="filterValues.formula_code" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(formula_code, index) in formulaOptions" :key="index" :value="index">{{ formula_code }}</option>
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
                formula_code: '',
                status: '',
            },
            productOptions: {},
            compCodeOptions: {},
            formulaOptions:{},
            statuses: {
                ACT: 'Active',
            },

        }
    },
    methods: {
        setFilter() {
            this.$emit('setFilter', this.filterValues)
        },
        listAutoProducts() {
            axios.get('/cover-service/list-auto-products').then(response => {
                this.productOptions = response.data
            })
        },
        listProdComp(){
            axios.get('/formula-service/list-product-comp').then(response =>{
                this.compCodeOptions = response.data
            })
        },
        listFormulaCode(){
            axios.get('/formula-service/list-formula').then(response =>{
                this.formulaOptions = response.data
            })
        },
    },
    mounted() {
        this.listAutoProducts();
        this.listProdComp();
        this.listFormulaCode();
    },
}
</script>
