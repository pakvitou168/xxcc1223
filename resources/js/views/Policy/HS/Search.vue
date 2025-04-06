<template>
  <div>
    <div class="grid grid-cols-12 gap-3 pt-3">
      <div class="intro-y col-span-6 lg:col-span-3">
        <FloatLabel>
          <InputText id="search" @input="setFilter" v-model="filterValues.search"/>
          <label for="search">Search</label>
        </FloatLabel>
      </div>
      <div class="intro-y col-span-6 lg:col-span-3">
        <FloatLabel>
          <Calendar id="issued_date_from" v-model="filterValues.issued_date_from" @update:modelValue="setFilter"
                    :maxDate="filterValues.issued_date_to"/>
          <label for="">From Date</label>
        </FloatLabel>
      </div>
      <div class="intro-y col-span-6 lg:col-span-3">
        <FloatLabel>
          <Calendar id="issued_date_to" v-model="filterValues.issued_date_to" @update:modelValue="setFilter"
                    :minDate="filterValues.issued_date_from"/>
          <label for="">To Date</label>
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
      ERROR_MESSAGE: "Something went wrong!",
      SUCCESS_MESSAGE: "Success!",
      filterValues: {
        search: '',
        issued_date_from: '',
        issued_date_to: '',
      },
    }
  },
  methods: {
    setFilter() {
      const filterData = {
        search: this.filterValues.search,
        issued_date_from: this.filterValues.issued_date_from ? moment(this.filterValues.issued_date_from).format('YYYY-MM-DD').toString() : '',
        issued_date_to: this.filterValues.issued_date_to ? moment(this.filterValues.issued_date_to).format('YYYY-MM-DD').toString() : ''
      };
      this.$emit('setFilter', filterData)
    },
    clearSearch() {
      this.filterValues.search = ''
      this.filterValues.issued_date_from = ''
      this.filterValues.issued_date_to = ''
      this.setFilter()
    },
  },
}
</script>