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
import ThirdPartyService from '@/services/claim/third_party.service'
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
        label: 'Third Parties',
        name: 'Third Party',
        prefix: 'Add New',
        linkAddNew: '/claim-configuration/third-parties/new',
      },
      canAddNew: hasPermission('THIRD_PARTY', 'NEW'),
      tabulator: null,
      options: {
        ajaxURL: '/claim-third-parties',
        columns: [
          {
            title: 'Plate No.',
            field: 'plate_no',
          },
          {
            title: 'Driving License No.',
            field: 'license_no',
          },
          {
            title: 'Vehicle Make',
            field: 'vehicle_make',
          },
          {
            title: 'Vehicle Model',
            field: 'vehicle_model',
          },
          {
            title: 'Engine No.',
            field: 'engine_no',
          },
          {
            title: 'Actions',
            field: 'actions',
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            mutator: () => getActionButtons(
              hasPermission('THIRD_PARTY', 'VIEW'),
              hasPermission('THIRD_PARTY', 'UPDATE'),
              hasPermission('THIRD_PARTY', 'DELETE'),
            ),
            cellClick:(e, cell)=> {
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector('a.edit svg')?.contains(e.target)) {
                this.$router.push({name: 'ThirdPartyEdit', params: {id: dataId}})
              } else if (cell._cell.element.querySelector('a.view svg')?.contains(e.target)) {
                this.$router.push({name: 'ThirdPartyDetail', params: {id: dataId}})
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
          ThirdPartyService.delete(id).then(res => {
            if (res.data.success) {
              this.tabulator.replaceData()
                notify(res.data?.message, 'success', 'bottom-right');
            }
          }).catch(err => {
              notify('Validation Error', 'error', 'bottom-right');

          })
        },
      });
    },

    setFilter(e) {
      this.tabulator.setFilter([
        {field: 'plate_no', type: 'like', value: e.target.value},
        {field: 'license_no', type: 'like', value: e.target.value},
        {field: 'vehicle_make', type: 'like', value: e.target.value},
        {field: 'vehicle_model', type: 'like', value: e.target.value},
        {field: 'engine_no', type: 'like', value: e.target.value},
      ])
    }
  },
}
</script>