<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew">
      <button class="btn btn-success shadow-md mr-2" title="Export" @click="openReportDialog">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
          </path>
        </svg>
      </button>
    </AddNew>
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter" />
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options" />
      </div>
    </div>
    <ReportDialog :isVisible="showReportDialog" header="Claim Report" @hideDialog="hideDialog"
      @exportClaimReport="exportClaimReport" />
  </div>
</template>

<script>

import AddNew from '@/components/Form/AddNew.vue'
import Search from '@/views/Claim/Register/Search.vue'
import RegisterService from '@/services/claim/register.service'
import { hasPermission } from '@/services/auth.service'
import { getActionButtons } from '@/components/DataTable/actionButton'
import { debounce } from '@/helpers'
import ReportDialog from '@/views/Claim/Register/components/ReportDialog.vue'

export default {
  components: {
    AddNew,
    Search,
    ReportDialog,
  },

  data() {
    return {
      addNew: {
        name: 'Registers',
        prefix: 'New',
        linkAddNew: '/claim/auto/registers/new',
      },
      showReportDialog: false,
      canAddNew: hasPermission('CLAIM_REGISTER', 'NEW'),
      tabulator: null,
      options: {
        ajaxURL: '/claim-registers',
        columns: [
          {
            title: 'Risk Type',
            field: 'product_line_code',
            width: 120,
            headerSort: false,
          },
          {
            title: 'Claims No.',
            field: 'claim_no',
            width: 135,
          },
          {
            title: 'Policy No.',
            field: 'document_no',
            width: 160,
          },
          {
            title: 'Insured Name',
            field: 'insured_name',
            minWidth: 160,
          },
          {
            title: 'Date of notification',
            field: 'notification_date',
            width: 170,
            mutator: value => {
              if (value) return moment(value).format('DD/MM/YYYY');
            },
          },
          {
            title: 'Date of accident',
            field: 'incident_date',
            width: 155,
            mutator: value => {
              if (value) return moment(value).format('DD/MM/YYYY');
            },
          },
          {
            title: 'Approve Status',
            field: 'approve_status',
            headerSort: false,
            headerHozAlign: "center",
            hozAlign: 'center',
            width: 135,
            formatter: 'html',
            mutator: (_, row) => {
              if (row.approved_status === 'APV') {
                return '<span class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Approved</span>'
              } else if (row.approved_status === 'REJ') {
                return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Rejected</span>'
              } else if (row.approved_status === null) {
                return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Pending</span>'
              }
              return ''
            },
          },
          {
            title: 'Comment',
            field: 'approved_cmt',
            headerSort: false,
            minWidth: 180,
          },
          {
            title: 'Actions',
            field: 'actions',
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            mutator: (_, row) => getActionButtons(
              hasPermission('CLAIM_REGISTER', 'VIEW'),
              this.canUpdate(row.approved_status),
              this.canDelete(row.approved_status),
              this.canRevise(row.approved_status, row.confirmed_final_claim),
            ),
            cellClick: (e, cell) => {
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector('a.edit svg')?.contains(e.target)) {
                this.$router.push({ name: 'ClaimRegisterEdit', params: { id: dataId } })
              } else if (cell._cell.element.querySelector('a.revise svg')?.contains(e.target)) {
                this.handleRevise(dataId)
              } else if (cell._cell.element.querySelector('a.view svg')?.contains(e.target)) {
                this.$router.push({ name: 'ClaimRegisterDetail', params: { id: dataId } })
              } else if (cell._cell.element.querySelector('a.delete svg')?.contains(e.target)) {
                this.handleDelete(dataId)
              }
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
        acceptClass: "p-button-danger",
        rejectClass: "p-button-secondary",
        blockScroll: false,
        accept: () => {
          RegisterService.delete(id).then(res => {
            if (res.data.success) {
              this.tabulator.replaceData()
              this.$notify({
                group: 'bottom',
                title: 'Success',
                text: res.data?.message,
              }, 4000);
            }
          }).catch(err => {
            this.$notify({
              group: 'bottom',
              title: 'Error',
              text: err?.response?.data?.message,
            }, 4000);
          })
        },
      });
    },

    handleRevise(id) {
      this.$confirm.require({
        message: 'Do you want to revise this record?',
        header: 'Revise',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-info",
        rejectClass: "p-button-secondary",
        blockScroll: false,
        acceptLabel: 'Revise',
        rejectLabel: 'Cancel',
        accept: () => {
          RegisterService.revise(id).then(res => {
            if (res.data?.success) {
              this.$notify({
                group: 'bottom',
                title: 'Success',
                text: res.data?.message,
              }, 4000);
              this.$router.push({ name: 'ClaimRegisterEdit', params: { id: id } });
            }
          }).catch(err => {
            console.log(err)
            this.$notify({
              group: 'bottom',
              title: 'Error',
              text: err?.response?.data?.message,
            }, 4000);
          })
        },
      });
    },

    setFilter(e) {
      debounce(() =>
        this.tabulator.setFilter([
          { field: 'product_line_code', type: 'like', value: e.search },
          { field: 'claim_no', type: 'like', value: e.search },
          { field: 'document_no', type: 'like', value: e.search },
          { field: 'insured_name', type: 'like', value: e.search },
          { field: 'approved_cmt', type: 'like', value: e.search },
          [
            { field: 'notification_date', type: '=', value: e.notification_date },
            { field: 'incident_date', type: '=', value: e.incident_date },
          ]
        ])
      )
    },

    canUpdate(approve_status) {
      let canUpdatePermission = hasPermission('CLAIM_REGISTER', 'UPDATE')
      if (!canUpdatePermission) return false

      if (approve_status !== null) return false
      return true
    },
    canDelete(approve_status) {
      let canDeletePermission = hasPermission('CLAIM_REGISTER', 'DELETE');
      if (!canDeletePermission) return false

      if (approve_status === 'APV') return false
      return true
    },

    canRevise(approve_status, isFinal) {
      let canRevisePermission = hasPermission('CLAIM_REGISTER', 'REVISE')
      if (!canRevisePermission) return false

      if (approve_status === null) return false

      if (isFinal === 'Y') return false

      return true
    },
    openReportDialog() {
      this.showReportDialog = true
    },
    hideDialog() {
      this.showReportDialog = false
    },
    exportClaimReport(formData) {
      location.href = '/claim-report/type' + formData.report_type + '/from' + formData.from_date + '/to' + formData.to_date;
    }
  },
}
</script>
