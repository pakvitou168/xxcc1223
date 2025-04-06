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
          <Dropdown
              class="w-full p-inputtext-sm"
              v-model="filterValues.status"
              :options="statuses"
              placeholder="Status"
              name="status"
              optionLabel="label"
              optionValue="value"
              @change="setFilter"
          >
          </Dropdown>
          <label for="">Status</label>
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
        status: '',
      },
      statuses: [
        { label: 'All', value: '' },
        { label: 'Active', value: 'ACT' },
      ],
    }
  },
  methods: {
    setFilter() {
      const filterData = {
        search: this.filterValues.search,
      };
      this.$emit('setFilter', filterData)
    },
    clearSearch() {
      this.filterValues.search = ''
      this.setFilter()
    },
  },
}
</script>
