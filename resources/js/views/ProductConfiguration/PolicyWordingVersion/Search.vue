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
                    <select v-model="filterValues.productLine" class="form-select" @change="setFilter" @input="changeProductLine">
                        <option value="">All</option>
                        <option v-for="productLine in productLineOptions" :key="productLine" :value="productLine">{{ productLine }}</option>
                    </select>
                </div>
            </div>
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">Product</label>
                    <select v-model="filterValues.product" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="product in productOptions" :key="product.value" :value="product.value">{{ product.value }} - {{ product.label }}</option>
                    </select>
                </div>
            </div>
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">Is Default</label>
                    <select v-model="filterValues.isDefault" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option value="Y">Yes</option>
                        <option value="N">No</option>
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
                productLine: '',
                product: '',
                isDefault: '',
            },
            productLineOptions: {},
            productOptions: {},
        }
    },
    methods: {
        listProductLines() {
            axios.get("/policy-wording-versions-service/list-product-lines").then((response) => {
                this.productLineOptions = response.data;
            });
        },
        changeProductLine(e) {
            axios.get("/policy-wording-versions-service/list-products-by-product-line/"+e.target.value).then((response) => {
                this.productOptions = response.data;
            });
        },
        setFilter() {
            this.$emit('setFilter', this.filterValues)
        },
    },

    mounted() {
        this.listProductLines()
    }
}
</script>
