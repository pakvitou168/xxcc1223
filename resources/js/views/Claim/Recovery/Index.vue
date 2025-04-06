<template>
	<div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-5">
      <h2 class="text-lg font-medium mr-auto">Deductible/Salvage</h2>
    </div>
		<div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter"/>
			<div class="overflow-x-auto scrollbar-hidden">
				<DataTable @ref="tabulator = $event" :options="options" />
			</div>
		</div>
	</div>
</template>

<script>

import Search from '@/components/DataTable/SearchBar.vue'
import RecoveryService from '@/services/claim/recovery.service'
import { hasPermission } from '@/services/auth.service'
import { getActionButtons } from '@/components/DataTable/actionButton'

export default {
  components: {
    Search,
  },

  data() {
    return {
      tabulator: null,
      options: {
        ajaxURL: '/claim-recoveries',
        columns: [
          {
            title: 'Risk Type',
            field: 'product_line_code',
            headerSort: false,
            width: 100,
          },
          {
            title: 'Claims No.',
            field: 'claim_no',
            width: 160,
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
            title: 'Plate No.',
            field: 'plate_no',
            width: 160,
          },
          {
            title: 'Issued Date',
            field: 'issued_at',
            width: 160,
            mutator: value => {
              if (value) return  moment(value).format('DD/MM/YYYY');
              return ''
            },
          },
          {
            title: 'Approve Status',
            field: 'approve_status',
            headerSort: false,
            headerHozAlign: "center",
            hozAlign: 'center',
            width: 160,
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
              hasPermission('CLAIM_RECOVERY', 'VIEW'),
              this.canUpdate(row.approved_status),
              this.canDelete(row.approved_status),
            ),
            cellClick:(e, cell)=> {
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector('a.edit svg')?.contains(e.target)) {
                this.$router.push({name: 'ClaimRecoveryEdit', params: {id: dataId}})
              } else if (cell._cell.element.querySelector('a.view svg')?.contains(e.target)) {
                this.$router.push({name: 'ClaimRecoveryDetail', params: {id: dataId}})
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
        blockScroll: false,
        accept: () => {
          RecoveryService.delete(id).then(res => {
            if (res.data.success) {
              this.tabulator.replaceData()
              notify(res.data?.message,'success');
            }
          }).catch(err => {
            notify(err?.response?.data?.message,'error');
          })
        },
      });
    },

    setFilter(e) {
      this.tabulator.setFilter([
        { field: 'payment_no', type: 'like', value: e.target.value },
        { field: 'claim_no', type: 'like', value: e.target.value },
        { field: 'insured_name', type: 'like', value: e.target.value },
        { field: 'plate_no', type: 'like', value: e.target.value },
        { field: 'approved_cmt', type: 'like', value: e.target.value },
      ])
    },

    canUpdate(approve_status) {
      let canUpdatePermission = hasPermission('CLAIM_RECOVERY', 'UPDATE')
      if (!canUpdatePermission) return false

      if (approve_status !== null) return false
      return true
    },

    canDelete(approve_status) {
      let canDeletePermission = hasPermission('CLAIM_RECOVERY', 'DELETE')
      if (!canDeletePermission) return false

      if (approve_status === 'APV') return false
      return true
    },
  },
}
</script>
