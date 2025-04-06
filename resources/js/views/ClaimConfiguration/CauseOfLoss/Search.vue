<template>
    <div>
        <div class="grid grid-cols-12 gap-3">
            <div class="intro-y col-span-6 lg:col-span-3">
                <div class="w-full text-gray-700">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" v-model="formValues.search" placeholder="Search" @input="setFilter">
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    data() {
      return {
        formValues: {
          search: '',
        },
      }
    },
    methods: {
      setFilter() {
        this.$emit('setFilter', this.formValues)
      },
    },
}
</script>
