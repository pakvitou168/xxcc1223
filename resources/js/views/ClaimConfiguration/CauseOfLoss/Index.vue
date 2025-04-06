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
import Search from '@/views/ClaimConfiguration/CauseOfLoss/Search.vue'
import CauseOfLossService from '@/services/claim/cause_of_loss.service'
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
        label: 'Cause of Losses',
        name: 'Cause of Loss',
        prefix: 'Add New',
        linkAddNew: '/claim-configuration/cause-of-losses/new',
      },
      canAddNew: hasPermission('CAUSE_OF_LOSE', 'NEW'),
      tabulator: null,
      options: {
        ajaxURL: '/claim-cause-of-losses',
        columns: [
          {
            title: 'Cause Code',
            field: 'code',
          },
          {
            title: 'Cause Name (English)',
            field: 'cause_name',
          },
          {
            title: 'Cause Name (Khmer)',
            field: 'cause_name_kh',
          },
          {
            title: 'Actions',
            field: 'actions',
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            mutator: () => getActionButtons(
              hasPermission('CAUSE_OF_LOSE', 'VIEW'),
              hasPermission('CAUSE_OF_LOSE', 'UPDATE'),
              hasPermission('CAUSE_OF_LOSE', 'DELETE'),
            ),
            cellClick:(e, cell)=> {
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector('a.edit svg')?.contains(e.target)) {
                this.$router.push({name: 'CauseOfLossEdit', params: {id: dataId}})
              } else if (cell._cell.element.querySelector('a.view svg')?.contains(e.target)) {
                this.$router.push({name: 'CauseOfLossDetail', params: {id: dataId}})
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
        CauseOfLossService.delete(id).then(response => {
        if (response.data.success) {
          notify(response.data.message, 'success', 'bottom-right')
          this.tabulator.replaceData()
        }
        }).catch(err => {
        notify('Error', 'error', 'bottom-right')
        })
      },
      });
    },

    setFilter(e) {
      this.tabulator.setFilter([
        {field: 'code', type: 'like', value: e.search},
        {field: 'cause_name', type: 'like', value: e.search},
      ])
    }
  },
}
</script>