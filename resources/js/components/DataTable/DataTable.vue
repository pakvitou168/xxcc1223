<template>
    <div>
        <!-- Div to hold the Tabulator table -->
        <div id="table" class="custom-tabulator-pagination"></div>
        <div v-if="tableOptions.pagination" class="absolute inline-block left-[175px] bottom-[3.75rem]">{{
            paginationCounter }}
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import Tabulator from 'tabulator-tables';

const props = defineProps({
    options: {
        type: Object,
        default: {}
    }
})

const emit = defineEmits(['ref'])
const table = ref(null);
const from = ref()
const to = ref()
const total = ref()
const isLoading = ref(false);
const refreshing = ref(false)
const defaultOptions = {
    layout: "fitColumns",
    pagination: "remote",
    paginationSize: 10,
    paginationSizeSelector: [10, 20, 50, 100],
    placeholder: "No Data Available",
    headerSortTristate: true,
    ajaxSorting: true,
    ajaxFiltering: true,
    data: [],
    persistence: {
        sort: true,
        page: {
            size: true,
            page: false,
        }
    },
    ajaxError: err => {
        // If session expired
        if (err.status === 401) location.reload()
    },
    ajaxRequesting: () => {
        // Show loading message inside the table when AJAX request is made
        isLoading.value = true;
    },
    ajaxResponse: (url, params, response) => {
        // response is the raw data from the server
        from.value = response.from
        to.value = response.to
        total.value = response.total
        isLoading.value = false;
        return {
            last_page: response.last_page, // Total number of pages (from API response)
            data: response.data, // Data to populate the table
        };
    },
    rowFormatter: function (row) {
        if (isLoading.value) {
            // Add a loading row if the data is being fetched
            const loadingRow = row.getElement();
            if (!loadingRow.querySelector('.loading-message')) {
                const loadingCell = document.createElement('div');
                loadingCell.classList.add('loading-message');
                loadingCell.innerText = 'Loading Data...';
                loadingRow.appendChild(loadingCell);
            }
        }
    }
};
watch(() => props.options.data, (newData) => {
    if (newData) {
        table.value.setData(newData)
    }
})
const paginationCounter = computed(() => {
    return `${from.value ?? ''} - ${to.value ?? ''} of ${total.value ?? ''}`
})
const tableOptions = computed(() => {
    return Object.assign(defaultOptions, props.options)
})
// Function to handle filter changes (with array of filters)
const setFilter = (filters) => {
    table.value.setFilter(filters);
};
const reloadTable = () => {
    table.value?.setData(tableOptions.value?.ajaxURL)
}
const getSelectedRows = () => {
    return table.value?.getSelectedRows();
}
const getSelectedData = () => {
    return table.value?.getSelectedData();
}
const clearSelectedRows = () => {
    table.value?.deselectRow()
}
onMounted(() => {
    // Initialize Tabulator table with server-side AJAX
    table.value = new Tabulator("#table", tableOptions.value);
    if(tableOptions.value.initialSort){
        table.value.setSort(tableOptions.value.initialSort)
    }
    emit('ref', table)
});
defineExpose({
    setFilter,
    reloadTable,
    getSelectedRows,
    getSelectedData,
    clearSelectedRows
})
</script>
<style>
.tabulator-row .tabulator-cell:last-of-type {
    @apply border-r border-slate-200 !important
}
</style>