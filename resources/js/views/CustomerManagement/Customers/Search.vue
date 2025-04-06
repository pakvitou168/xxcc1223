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
                    <label class="form-label">Customer Type</label>
                    <select v-model="filterValues.customerType" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(customerType, key) in customerTypeOptions" :key="key" :value="key">{{ customerType }}</option>
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
                customerType: '',
            },
            customerTypeOptions: {
                IC: 'Individual Customer',
                CL: 'Corporate Customer-Local',
                CA: 'Corporate Customer-Abroad',
            },
        }
    },
    methods: {
        setFilter() {
            this.$emit('setFilter', this.filterValues)
        },
    },
}
</script>