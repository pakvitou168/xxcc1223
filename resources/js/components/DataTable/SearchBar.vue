<template>
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center">
        <div class="w-56 relative text-gray-700 dark:text-gray-300">
            <input type="text" class="form-control w-56 placeholder-theme-13" placeholder="Search" @input="setFilter">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            debounce: {},
        }
    },
    methods: {
        setFilter(e) {
            clearTimeout(this.debounce)

            this.debounce = setTimeout(() => {
                this.$emit('setFilter', e)
            }, 500)
        }
    }
}
</script>
