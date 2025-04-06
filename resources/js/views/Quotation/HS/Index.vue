<template>
  <div>

    <AddNew :addNew="addNew" @click="openFormDialog">
      <button v-if="canAddNew" class="button-primary leading-6">
        <i class="pi pi-plus" style="font-size: 0.75rem"></i> New
      </button>
    </AddNew>
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter" />
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options" />
      </div>
    </div>
    <FormDialog header="New Quote" :isVisible="showFormDialog" @hideDialog="hideDialog" />

    <Toast />
  </div>
</template>

<script>
import AddNew from "@/components/Form/AddNew.vue";
import Search from './Search.vue'
import FormDialog from "@/views/Quotation/HS/FormDialog.vue";
import { hasPermission } from "@/services/auth.service";
import Status from "@/components/DataTable/Status.vue";
import UserPermissions from '../../../mixins/UserPermissions'
import { createApp } from "vue";
import Action from "./Components/Action.vue";

export default {
  mixins: [UserPermissions],
  components: {
    AddNew,
    Search,
    FormDialog,
  },
  data() {
    return {
      addNew: {
        label: "H & S Quotation",
        name: 'H & S Quotation',
        prefix: "New",
      },
      issued_date_from: '',
      issued_date_to: '',
      showFormDialog: false,
      tabulator: null,
      canAddNew: hasPermission("HS_QUOTATION", "NEW"),
      options: {
        ajaxURL: "/hs/quotations",
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
            formatter: (cell, formatterParams, onRendered) => {
              const rowData = cell.getRow().getData();
              const container = document.createElement("div");
              createApp(Status, { status: rowData.quotation?.approved_status }).mount(container);
              return container;
            }
          },
          {
            title: "Approved Reason",
            field: "approved_reason",
            headerSort: false,
            minWidth: 180,
            tooltip: true,
            mutator: (_, row) => row.quotation?.approved_reason,
          },
          {
            title: "Quote Acceptance",
            field: "accepted_status",
            headerSort: false,
            width: 170,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: (cell, formatterParams, onRendered) => {
              const rowData = cell.getRow().getData();
              const container = document.createElement("div");
              createApp(Status, { status: rowData.quotation?.accepted_status }).mount(container);
              return container;
            }
          },
          {
            title: "Accepted Reason",
            field: "accepted_reason",
            headerSort: false,
            minWidth: 180,
            tooltip: true,
            mutator: (_, row) => row.quotation?.accepted_reason,
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
                  this.$router.push({ name: 'HSQuotationDetail', params: { id: rowData.id } })
                },
                onDelete: () => {
                  this.handleDelete(rowData.id)
                },
              };
              createApp(Action, { rowData: rowData, events: eventHandlers }).mount(container);
              return container;
            },
          },
        ],
      },
    };
  },
  methods: {
    openFormDialog() {
      this.showFormDialog = true;
    },
    hideDialog(reloadData) {
      this.showFormDialog = false;
      if (reloadData) this.tabulator.replaceData();
    },
    handleDelete(id) {
      this.$confirm.require({
        message: 'Are you sure to delete?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle text-danger',
        rejectClass: 'p-button-secondary p-button-outlined',
        rejectLabel: 'Cancel',
        acceptLabel: 'Save',
        accept: () => {
          
        },
        reject: () => {
        }
      });
    },

    setFilter: _.debounce(function (e) {
      this.tabulator.setFilter([
        { field: "quotation_no", type: "like", value: e.search },
        { field: "name_en", type: "like", value: e.search },
        { field: "document_no", type: "like", value: e.search },
        { field: "total_premium", type: "like", value: e.search },
      ]);
    }, 500),

  },
  mounted() {

  },
};
</script>