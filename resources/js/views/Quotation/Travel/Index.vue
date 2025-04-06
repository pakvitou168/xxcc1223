<template>
    <div>
        <AddNew :addNew="addNew">
            <button v-if="hasPermission('TV_QUOTATION','NEW')" class="float-right" @click="openFormDialog">
                <span class="btn btn-primary shadow-md leading-6">New</span>
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
import FormDialog from "@/views/Quotation/Travel/Components/Dialog.vue"
import { ref } from "vue";
import AddNew from "@/components/Form/AddNew.vue";
import Search from "./Components/Search.vue";
import { hasPermission } from "@/services/auth.service";
import { getActionButtons } from "@/components/DataTable/actionButton";
import { useConfirm } from "primevue/useconfirm";
import { useRouter } from "vue-router";
import QuoteService from '@/services/travel/quote.service';

const router = useRouter();
const confirm = useConfirm();
const dialogRef = ref()
const addNew = ref({
    name: "Travel Quotation",
    label: "Travel Quotation",
    prefix: "New",
});
const loading = ref(false)
const tabulator = ref(null)
const options = ref({
    ajaxURL: "/travel/quotations",
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
            headerSort: false,
            width: 160,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
            mutator: (_, row) => {
                if (row?.approved_status === "APV") {
                    return '<span class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Approved</span>';
                } else if (row?.approved_status === "REJ") {
                    return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Rejected</span>';
                } else if (row?.approved_status === "PND") {
                    return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Pending</span>';
                }
                return "";
            },
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
            headerSort: false,
            width: 170,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
            mutator: (_, row) => {
                if (row.accepted_status === "ACP") {
                    return '<span class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Successful</span>';
                } else if (row.accepted_status === "REJ") {
                    return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Unsuccessful</span>';
                } else if (row.accepted_status === "PND") {
                    return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Pending</span>';
                }
                return "";
            },
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
            formatter: "html",
            mutator: (_, row) =>
                getActionButtons(
                    hasPermission("TV_QUOTATION", "VIEW"),
                    false,
                    hasPermission("TV_QUOTATION", "DELETE"),
                    false
                ),
            cellClick: (e, cell) => {
                const dataId = cell._cell.row.data.id;
                if (
                    cell._cell.element
                        .querySelector("a.view svg")
                        .contains(e.target)
                ) {
                    router.push({
                        name: "TravelQuotationDetail",
                        params: {
                            id: dataId,
                        },
                    });
                } else if (
                    cell._cell.element
                        .querySelector("a.delete svg")
                        .contains(e.target)
                ) {
                    handleDelete(dataId);
                }
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
        rejectClass: 'p-button-secondary px-2',
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

<style lang="scss" scoped></style>