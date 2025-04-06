<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew" :canExport="canExport" @export="exportAutoQuotationExcel">
      <input type="file" style="display: none;" accept=".xlsm" id="vehicleTemplate" @change="uploadTemplate()">
      <button v-if="canUpload" class="button-primary mr-1" title="Upload Vehicle Template" @click="triggerUploadInput">
        <UploadIcon />
      </button>
      <button v-if="canExport" class="button-primary mr-1" title="Download Vehicle Upload Template"
        @click="downloadTemplate">
        <DownloadIcon />
      </button>
    </AddNew>
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter" />
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options" />
      </div>
    </div>
  </div>
</template>

<script>

import AddNew from '@/components/Form/AddNew.vue'
import Search from './Search.vue'
import UserPermissions from '../../../mixins/UserPermissions'
import DownloadIcon from '../../../components/Icons/DownloadIcon.vue'
import UploadIcon from '../../../components/Icons/UploadIcon.vue'
import { createApp } from 'vue'
import Action from './Components/Action.vue';
import Status from "@/components/DataTable/Status.vue";

export default {
  mixins: [UserPermissions],

  components: {
    AddNew,
    Search,
    DownloadIcon,
    UploadIcon,
    Action
  },

  data() {
    return {
      functionCode: 'AUTO',
      addNew: {
        label: 'Auto Quotations',
        name: 'Auto Quotation',
        prefix: 'Add New',
        linkAddNew: '/quotation/autos/new',
      },
      issued_date_from: '',
      issued_date_to: '',
      canExport: true,
      tabulator: null,
      options: {
        persistenceID: 'autos-table',
        ajaxURL: "/autos",
        columns: [
          {
            title: "Quotation No.",
            field: "document_no",
            sorter: "string",
            width: 200,
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
            minWidth: 100,
            formatter: 'money',
          },
          {
            title: 'Issue Date',
            field: 'issued_at',
            formatter: "datetime",
            width: 130,
            formatterParams: {
              outputFormat: "DD/MM/YY",
            },
          },
          {
            title: 'Quote Approval',
            field: 'approved_status',
            headerSort: false,
            width: 160,
            hozAlign: 'center',
            headerHozAlign: 'center',
            formatter: (cell, formatterParams, onRendered) => {
              const rowData = cell.getRow().getData();
              const container = document.createElement("div");
              createApp(Status, { status: rowData.quotation?.approved_status }).mount(container);
              return container;
            }
          },
          {
            title: 'Approved Reason',
            field: 'approved_reason',
            headerSort: false,
            minWidth: 180,
            tooltip: true,
            mutator: (_, row) => {
              if (!row.quotation) return ''

              return row.quotation.approved_reason
            },
          },
          {
            title: 'Quote Acceptance',
            field: 'accepted_status',
            headerSort: false,
            width: 170,
            hozAlign: 'center',
            headerHozAlign: 'center',
            formatter: (cell, formatterParams, onRendered) => {
              const rowData = cell.getRow().getData();
              const container = document.createElement("div");
              createApp(Status, { status: rowData.quotation?.accepted_status }).mount(container);
              return container;
            }
          },
          {
            title: 'Accepted Reason',
            field: 'accepted_reason',
            headerSort: false,
            minWidth: 180,
            tooltip: true,
            mutator: (_, row) => {
              if (!row.quotation) return ''

              return row.quotation.accepted_reason
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
                  this.$router.push({ name: 'QuotationAutoDetail', params: { id: rowData.id } })
                },
                onDelete: () => {
                  this.handleDelete(rowData.id)
                },
                onEdit: () => {
                  this.$router.push({ name: 'QuotationAutoEdit', params: { id: rowData.id } })
                },
              };
              createApp(Action, { rowData: rowData, events: eventHandlers }).mount(container);
              return container;
            },
          },
        ],
      },
    }
  },

  methods: {
    handleDelete(id) {
      this.$confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Delete',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-danger px-2 py-2",
        rejectClass: "p-button-secondary",
        blockScroll: false,
        acceptLabel: 'Delete',
        rejectLabel: 'Cancel',
        accept: () => {
          axios.delete(`/autos/${id}`).then(response => {
            if (response.data.success) {
              // refresh table
              notify(response.data.message, 'success')
              this.tabulator.replaceData()
            }
          }).catch(err => {
            notify('Error', 'error')
          })
        },
      });
    },

    handleReviseApprovalStatus(id) {
      this.$confirm.require({
        message: 'Do you want to revise this record?',
        header: 'Revise',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-info",
        rejectClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios.put(`/autos/revise-approval-status/${id}`).then(response => {
            if (response.data.success) {
              // refresh table
              notify(response.data.message, 'success')
              this.tabulator.replaceData()
              this.$router.push({ name: 'QuotationAutoEdit', params: { id: id } })
            }
          }).catch(err => {
            notify('Error', 'error')
          })
        },
      });
    },

    handleReviseAcceptanceStatus(id) {
      this.$confirm.require({
        message: 'Do you want to revise this record?',
        header: 'Revise',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-info",
        rejectClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios.put(`/autos/revise-acceptance-status/${id}`).then(response => {
            if (response.data.success) {
              // refresh table
              notify(response.data.message, 'success')
              this.tabulator.replaceData()
              this.$router.push({ name: 'QuotationAutoEdit', params: { id: id } })
            }
          }).catch(err => {
            notify('Error', 'error')
          })
        },
      });
    },

    setFilter: _.debounce(function (e) {
      this.issued_date_from = e.issued_date_from
      this.issued_date_to = e.issued_date_to
      this.tabulator.setFilter([
        { field: 'quotation_no', type: 'like', value: e.search },
        { field: 'name_en', type: 'like', value: e.search },
        { field: 'document_no', type: 'like', value: e.search },
        { field: 'total_premium', type: 'like', value: e.search },
        [
          { field: 'issued_at', type: '>=', value: e.issued_date_from },
          { field: 'issued_at', type: '<=', value: e.issued_date_to },
        ],
      ])
    }, 500),

    exportAutoQuotationExcel() {
      location.href = '/auto-service/export-quotation/from' + this.issued_date_from + '/to' + this.issued_date_to;
    },

    downloadTemplate() {
      location.href = '/uploads/upload_vehicle_template.xlsm'
    },

    triggerUploadInput() {
      document.getElementById("vehicleTemplate").click();
    },

    uploadTemplate() {
      let file = document.getElementById('vehicleTemplate').files[0]
      let formData = new FormData();
      formData.append("file", file ?? "");
      formData.append("_method", "PUT");
      axios.post(`/auto-service/upload-vehicle-template`, formData).then(response => {
        if (response.data.success) {
          notify(response.data.message, 'success')
        }
      }).catch(err => {
        notify('Error', 'error')
      })
    },
  },
}
</script>
