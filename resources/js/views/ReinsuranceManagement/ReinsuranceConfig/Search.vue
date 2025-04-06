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
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            filterValues: {
                search: '',
                product: '',
            },
            productOptions: {},
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
    },
    mounted() {
        this.listProducts()
    },
}
</script>
