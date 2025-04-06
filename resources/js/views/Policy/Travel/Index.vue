<template>
    <div>
        <div class="intro-y box p-5 mt-5">
            <Search @setFilter="setFilter" />
            <div class="overflow-x-auto scrollbar-hidden">
                <DataTable ref="tabulator" :options="options" />
            </div>
        </div>
    </div>
</template>

<script setup>
import {createApp, markRaw, ref} from "vue";
import Search from "./Components/Search.vue";
import { useConfirm } from "primevue/useconfirm";
import { useRouter } from "vue-router";
import PolicyService from "@/services/travel/policy/policy.service.js";
import _ from "lodash";
import Action from "./Components/Action.vue";
import Status from "@/components/DataTable/Status.vue";
const ERROR_Message = ref("Something went wrong!");
const SUCCESS_MESSAGE = ref("Success!");
const router = useRouter();
const confirm = useConfirm();
const tabulator = ref(null);
const options = ref({
    ajaxURL: "/travel/policies",
    columns: [
        {
            title: "Policy No.",
            field: "document_no",
            sorter: "string",
            minWidth: 200,
        },
        {
            title: "Quotation No.",
            field: "quotation_no",
            headerSort: false,
            minWidth: 200
        },
        {
            title: "Customer Name",
            field: "name_en",
            sorter: "string",
            minWidth: 200,
        },
        {
            title: "Premium",
            field: "total_premium",
            sorter: "number",
            formatter: "money",
            width: 120,
            cssClass: 'text-right'
        },
        {
            title: "Issue Date",
            field: "issued_at",
            formatter: "datetime",
            width: 130,
            formatterParams: {
                outputFormat: "DD/MM/YY",
            },
        },
        {
            title: "Version",
            field: "version",
            sorter: "number",
            width: 110,
        },
        {
            title: "Cycle",
            field: "cycle",
            sorter: "number",
            width: 100,
        },
        {
            title: "Status",
            field: "status",
            width: 120,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: (cell, formatterParams, onRendered) => {
                const rowData = cell.getRow().getData();
                const container = document.createElement("div");
                createApp(Status, {status: rowData.status }).mount(container);
                return container;
            }
        },
        {
            title: "Submit Status",
            field: "approved_status",
            width: 150,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: (cell, formatterParams, onRendered) => {
                const rowData = cell.getRow().getData();
                const container = document.createElement("div");
                createApp(Status, {status: rowData.approved_status }).mount(container);
                return container;
            }
        },
        {
            title: "Approved Reason",
            field: "approved_reason",
            headerSort: false,
            minWidth: 180,
            tooltip: true,
            mutator: (_, row) => {
                if (!row.policy) return "";

                return row.policy.approved_reason;
            },
        },
        {
            title: "Actions",
            field: "actions",
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: (cell, formatterParams, onRendered) => {
                const rowData = cell.getRow().getData();
                const container = document.createElement("div");
                const eventHandlers = {
                    onView: () => {
                        handleView(rowData.data_id)
                    },
                    onDelete: () => {
                        handleDelete(rowData.data_id)
                    },
                    onEdit: () => {
                        handleEdit(rowData.id, rowData.data_id)
                    },
                };
                createApp(markRaw(Action), { rowData: rowData, events: eventHandlers }).mount(container);
                return container;
            },
        },
    ],
})

const setFilter = _.debounce((filter) => {
    tabulator.value?.setFilter([
        { field: 'quotation_no', type: 'like', value: filter.search },
        { field: 'name_en', type: 'like', value: filter.search },
        { field: 'document_no', type: 'like', value: filter.search },
        { field: 'total_premium', type: 'like', value: filter.search },
        [
            { field: 'issued_at', type: '>=', value: filter.issued_date_from },
            { field: 'issued_at', type: '<=', value: filter.issued_date_to },
        ],
    ])
}, 500)
const openFormDialog = () => {
    dialogRef.value?.toggleDialog()
}
const handleDelete = (dataId) => {
    confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Delete',
        icon: 'pi pi-exclamation-triangle text-red-500',
        rejectClass: 'p-button-secondary p-button-outlined',
        rejectLabel: 'No',
        acceptLabel: 'Yes',
        acceptClass: 'p-button-danger',
        accept: () => {
            PolicyService.delete(dataId).then((res) => {
                notify(res.data?.message || SUCCESS_MESSAGE.value, "success", "bottom-right");
                tabulator.value.reloadTable()
            }).catch((err) => {
                notify(err.response?.data?.message || ERROR_Message.value, "error", "bottom-right");
            }).finally(() => {
                loading.value = false
            })
        }
    });
}
const handleView = (rowId) => {
    router.push({
        name: 'TravelPolicyDetail',
        params: {
            id: rowId
        }
    });
}
const handleEdit = (rowId, data_id) => {
    router.push({
        name: 'TravelPolicyEdit',
        params: {
            id: rowId
        },
        query: {
            data_id:data_id,
        }
    });
}
</script>