<template>
  <div>
    <div class="grid grid-cols-12 gap-3">
      <div class="intro-y col-span-6 lg:col-span-3">
        <FloatLabel>
          <InputText id="search" @input="setFilter" v-model="filterValues.search" />
          <label for="search">Search</label>
        </FloatLabel>
      </div>
      <div class="intro-y col-span-6 lg:col-span-3">
        <button class="btn btn-primary leading-6" @click="clearSearch">Clear</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!",
      SUCCESS_MESSAGE:"Success!",
      filterValues: {
        search: '',
      },
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
