<template>
    <div>
        <div class="grid grid-cols-12 gap-3 pt-3">
            <div class="intro-y col-span-6 lg:col-span-3">
                <FloatLabel>
                    <InputText id="search" @input="setFilter" v-model="filterValues.search" />
                    <label for="search">Search</label>
                </FloatLabel>
            </div>
            <div class="intro-y col-span-6 lg:col-span-3">
                <FloatLabel>
                    <Calendar id="incident_date" showIcon v-model="filterValues.incident_date" @update:modelValue="setFilter" :maxDate="filterValues.notification_date" />
                    <label for="">Accident Date</label>
                </FloatLabel>
            </div>
            <div class="intro-y col-span-6 lg:col-span-3">
                <FloatLabel>
                    <Calendar id="issued_date_to" showIcon v-model="filterValues.notification_date" @update:modelValue="setFilter" :minDate="filterValues.incident_date" />
                    <label for="">Notification Date</label>
                </FloatLabel>
            </div>
            <div class="intro-y col-span-6 lg:col-span-3">
                <button class="btn btn-primary leading-6" @click="clearSearch">Clear</button>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';


export default {
    data() {
        return {
            filterValues: {
                search: '',
                notification_date: '',
                incident_date: '',
            },
        }
    },
    methods: {
        setFilter() {
            const filterData = {
                search: this.filterValues.search,
                notification_date: this.filterValues.notification_date ? moment(this.filterValues.notification_date).format('YYYY-MM-DD').toString() : '',
                incident_date: this.filterValues.incident_date? moment(this.filterValues.incident_date).format('YYYY-MM-DD').toString(): ''
            };
            this.$emit('setFilter', filterData)
        },
        clearSearch() {
            this.filterValues.search = ''
            this.filterValues.notification_date = ''
            this.filterValues.incident_date = ''
            this.setFilter()
        },
    },
}
</script>
