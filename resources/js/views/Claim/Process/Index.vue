<template>
	<div>
		<AddNew :canAddNew="canAddNew" :addNew="addNew" />
		<div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter"/>
			<div class="overflow-x-auto scrollbar-hidden">
				<DataTable @ref="tabulator = $event" :options="options" />
			</div>
		</div>
	</div>
</template>

<script>

import AddNew from '@/components/Form/AddNew.vue'
import Search from '@/components/DataTable/SearchBar.vue'
import ProcessService from '@/services/claim/process.service'
import { hasPermission } from '@/services/auth.service'
import { getActionButtons } from '@/components/DataTable/actionButton'

export default {
  components: {
    AddNew,
    Search,
  },

  data() {
    return {
      addNew: {
        name: 'Full Payment',
        prefix: 'New',
        linkAddNew: '/claim/auto/processes/new',
      },
      canAddNew: hasPermission('CLAIM_PROCESS', 'NEW'),
      tabulator: null,
      options: {
        ajaxURL: '/claim-processes',
          columns: [
          {
           title: 'Risk Type',
           field:'product_line_code',
           width:160,
           headerSort: false,
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
            title: 'Issue Date',
            field: 'payment_date',
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
              hasPermission('CLAIM_PROCESS', 'VIEW'),
              this.canUpdate(row.approved_status),
              this.canDelete(row.approved_status),
              this.canRevise(row.approved_status),
            ),
            cellClick:(e, cell)=> {
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector('a.edit svg')?.contains(e.target)) {
                this.$router.push({name: 'ClaimProcessEdit', params: {id: dataId}})
              } else if (cell._cell.element.querySelector('a.revise svg')?.contains(e.target)) {
                this.handleRevise(dataId)
              } else if (cell._cell.element.querySelector('a.view svg')?.contains(e.target)) {
                this.$router.push({name: 'ClaimProcessDetail', params: {id: dataId}})
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
          ProcessService.delete(id).then(res => {
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
        blockScroll: false,
        acceptLabel: 'Revise',
        rejectLabel: 'Cancel',
        accept: () => {
          ProcessService.revise(id).then(res => {
            if (res.data?.success) {
              this.$notify({
                group: 'bottom',
                title: 'Success',
                text: res.data?.message,
              }, 4000);
              this.$router.push({ name: 'ClaimProcessEdit', params: { id: id }});
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
      this.tabulator.setFilter([
        {field: 'claim_no', type: 'like', value: e.target.value},
        {field: 'approved_cmt', type: 'like', value: e.target.value},
      ])
    },

    canUpdate(approve_status) {
      let canUpdatePermission = hasPermission('CLAIM_PROCESS', 'UPDATE')
      if (!canUpdatePermission) return false

      if (approve_status !== null) return false
      return true
    },

    canDelete(approve_status) {
      let canUpdatePermission = hasPermission('CLAIM_PROCESS', 'DELETE')
      if (!canUpdatePermission) return false

      if (approve_status === 'APV') return false
      return true
    },

    canRevise(approve_status) {
      let canRevisePermission = hasPermission('CLAIM_PROCESS', 'REVISE')
      if (!canRevisePermission) return false

      if (approve_status !== 'REJ') return false
      return true
    }
  },
}
</script>
