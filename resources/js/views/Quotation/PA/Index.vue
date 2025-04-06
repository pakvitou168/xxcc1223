<template>
    <div>
        <AddNew :addNew="addNew">
            <button class="float-right" @click="openFormDialog" v-if="hasPermission('PA_QUOTATION','NEW')">
                <span class="btn btn-primary shadow-md leading-6">New </span>
            </button>
        </AddNew>
        <div class="intro-y box p-5 mt-5">
            <Search @setFilter="setFilter" />
            <div class="overflow-x-auto scrollbar-hidden">
                <DataTable ref="tabulator" :options="options" />
            </div>
        </div>
        <FormDialog ref="dialogRef" @success="handleSuccess" />
    </div>
</template>

<script setup>
import FormDialog from "@/views/Quotation/PA/Components/Dialog.vue"
import { createApp, ref } from "vue";
import AddNew from "@/components/Form/AddNew.vue";
import Search from "./Components/Search.vue";
import { hasPermission } from "../../../services/auth.service";
import { useConfirm } from "primevue/useconfirm";
import { useRouter } from "vue-router";
import QuoteService from '@/services/pa/quote.service';
import Status from "@/components/DataTable/Status.vue";
import Action from "./Components/Action.vue"

const router = useRouter();
const confirm = useConfirm();
const dialogRef = ref()
const addNew = ref({
    name: "PA Quotation",
    prefix: "New",
});
const loading = ref(false)
const tabulator = ref(null)
const options = ref({
    ajaxURL: "/pa/quotations",
    initialSort: [
        { column: "document_no", dir: "desc" },
    ],
    columns: [
        {
            title: "Quotation No.",
            field: "document_no",
            width: 200,
        },
        {
            title: "Customer Name",
            field: "name_en",
            minWidth: 200,
        },
        {
            title: "Quote Approval",
            field: "approved_status",
            width: 200,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: (cell, formatterParams, onRendered) => {
                const rowData = cell.getRow().getData();
                const container = document.createElement("div");
                createApp(Status, { status: rowData.approved_status }).mount(container);
                return container;
            }
        },
        {
            title: "Approved Reason",
            field: "approved_reason",
            headerSort: false,
            minWidth: 180,
            tooltip: true,
            mutator: (_, row) => row?.approved_reason,
        },
        {
            title: "Quote Acceptance",
            field: "accepted_status",
            width: 200,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: (cell, formatterParams, onRendered) => {
                const rowData = cell.getRow().getData();
                const container = document.createElement("div");
                createApp(Status, { status: rowData.accepted_status }).mount(container);
                return container;
            }
        },
        {
            title: "Accepted Reason",
            field: "accepted_reason",
            headerSort: false,
            minWidth: 180,
            tooltip: true,
            mutator: (_, row) => row?.accepted_reason,
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
                        handleView(rowData.id)
                    },
                    onDelete: () => {
                        handleDelete(rowData.id)
                    },
                    onEdit: () => {
                        handleEdit(rowData.id)
                    },
                };
                createApp(Action, { rowData: rowData, events: eventHandlers }).mount(container);
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
const handleView = (dataId) => {
    router.push({
        name:'PAQuotationDetail',
        params:{
            id:dataId
        }
    })
}
const handleEdit = (dataId) => {
    router.push({
        name:'PAQuotationEdit',
        params:{
            id:dataId
        }
    })
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
            QuoteService.delete(dataId).then((res) => {
                notify(res.data?.message, 'success')
                tabulator.value.reloadTable()
            }).catch((err) => {
                notify(err.response?.data?.message, 'error')
            }).finally(() => {
                loading.value = false
            })
        }
    });
}
const handleSuccess = () => {
    dialogRef.value?.toggleDialog()
    tabulator.value?.reloadTable()
}
</script>