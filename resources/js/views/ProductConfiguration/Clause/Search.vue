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
                    <label class="form-label">Clause Type</label>
                    <select v-model="filterValues.clause_type" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(clause_type,index) in clauseTypeOptions" :key="index" :value="index">{{ clause_type }}</option>
                    </select>
                </div>
            </div>
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">Default Inclusion</label>
                    <select v-model="filterValues.default_inclusion" class="form-select" @change="setFilter">
                        <option value="">All</option>
                        <option v-for="(default_inclusion, index) in default_inclusions" :key="index" :value="index">{{ default_inclusion }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "axios";

export default {
    props:{
        clauseTypeOptions:Object
    },    
    data() {
        return {
            filterValues: {
                search: '',
                clause_type: '',
                default_inclusion: '',
            },
            default_inclusions: {
                Y: 'Yes',
                N: 'No'
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