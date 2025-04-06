<template>
    <div class="grid grid-cols-12 gap-3">
        <div class="intro-y col-span-6 lg:col-span-3">
            <div class="w-full text-gray-700">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" v-model="filterValues.search" placeholder="Search" @input="$emit('setFilter', filterValues)">
            </div>
        </div>
        <div class="intro-y col-span-6 lg:col-span-3">
            <div class="w-full text-gray-700">
                <label class="form-label">Group Code</label>
                <select v-model="filterValues.groupCode" class="form-select" @change="$emit('setFilter', filterValues)">
                    <option value="">All</option>
                    <option v-for="(groupCode, index) in groupCodeOptions" :key="index" :value="index">{{ groupCode }}</option>
                </select>
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
                groupCode: '',
            },
            groupCodeOptions: {
                'INDIVIDUAL': 'INDIVIDUAL',
                'CORPORATE': 'CORPORATE'
            }
        }
    },
}
</script>