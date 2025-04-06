<template>
    <div>
        <div class="grid grid-cols-12 gap-3 pt-3">
          <div class="intro-y col-span-6 lg:col-span-3">
            <div>
              <label class="font-bold block mb-1">Search</label>
              <InputText id="search" @input="setFilter" v-model="filterValues.search" placeholder="Search"/>
            </div>
          </div>
          <div class="intro-y col-span-6 lg:col-span-3">
             <div>
               <label class="font-bold block mb-1">Status</label>
               <Dropdown
                   v-model="filterValues.status"
                   :options="status"
                   optionLabel="label"
                   optionValue="value"
                   class="w-full p-inputtext"
                   placeholder="Status"
                   :filter="true"
                   @change="setFilter"
               />
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
                status: '',
            },
            status: [
                {value : '', label : 'All'},
                {value : 'ACT', label : 'Active'},
                {value : 'PND', label : 'Pending'},
            ],
        }
    },
    methods: {
        setFilter() {
            this.$emit('setFilter', this.filterValues)
        },        
    },
    mounted() {},
}
</script>