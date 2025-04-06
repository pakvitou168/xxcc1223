<template>
    <div>
        <AddNew :addNew="addNew">
            <button class="float-right" @click="openFormDialog">
                <span class="btn btn-primary shadow-md leading-6">New</span>
            </button>
        </AddNew>
        <div class="intro-y box p-5 mt-5">
            <Search @setFilter="setFilter" />
            <div class="overflow-x-auto scrollbar-hidden">
                <DataTable ref="tabulator" :options="options" />
            </div>
        </div>
        <Detail ref="dialogRef" @success="handleSuccess" @close="handleClose" />
    </div>
</template>

<script setup>
import Detail from "@/views/SecurityManagement/Group/Detail.vue"
import { createApp, h, ref } from "vue";
import AddNew from "@/components/Form/AddNew.vue";
import Search from "./Components/Search.vue";
import { hasPermission } from "@/services/auth.service";
import { getActionButtons } from "@/components/DataTable/actionButton";
import { useRouter } from "vue-router";
import Status from "@/components/DataTable/Status.vue";

const router = useRouter();
const dialogRef = ref()
const addNew = ref({
    name: "Group",
    prefix: "New",
});
const tabulator = ref(null)
const options = ref({
    ajaxURL: "/api/sm/groups",
    columns: [
        {
            title: "Code",
            field: "code",
            width: 200,
        },
        {
            title: "Name",
            field: "name",
            minWidth: 200,
        },
        {
            title: "Is default",
            field: "is_default",
            minWidth: 200,
            cellClass: 'text-center',
            formatter: (cell, formatterParams, onRendered) => {
                const rowData = cell.getRow().getData();
                return rowData.is_default ? 'Yes' : ''
            }
        },
        {
            title: "Created date",
            field: "created_at",
            minWidth: 200,
        },
        {
            title: "Updated date",
            field: "updated_at",
            minWidth: 200,
        },
        {
            title: "Status",
            field: "status",
            headerSort: false,
            width: 160,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: (cell, formatterParams, onRendered) => {
                const rowData = cell.getRow().getData();
                const container = document.createElement("div");
                createApp(Status, { rowData }).mount(container);
                return container;
            }
        }
    ],
    rowDblClick: function (e, row) {
        const data = row._row.data
        handleDetail(data.id)
    }
})
const handleDetail = (rowId) => {
    router.push({
        name: 'SMGroupDetail',
        params: {
            id: rowId
        }
    })
    dialogRef.value.toggleDialog(rowId)
}
const setFilter = _.debounce((filter) => {
    tabulator.value?.setFilter([
        { field: 'name', type: 'like', value: filter.search },
        { field: 'code', type: 'like', value: filter.search }
    ])
}, 500)
const openFormDialog = () => {
    dialogRef.value?.toggleDialog()
}
const handleSuccess = (action = '') => {
    dialogRef.value?.toggleDialog()
    tabulator.value?.reloadTable()
    if (action == 'update') router.push({ name: 'SMGroupIndex' })
}
const handleClose = () => {
    router.push({
        name: 'SMGroupIndex'
    })
}
</script>

<style lang="scss" scoped></style>