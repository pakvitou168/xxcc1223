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
import PayeeService from '@/services/claim/payee.service'
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
        label: 'Payees',
        name: 'Payee',
        prefix: 'Add New',
        linkAddNew: '/claim-configuration/payees/new',
      },
      canAddNew: hasPermission('PAYEE', 'NEW'),
      tabulator: null,
      options: {
        ajaxURL: '/claim-payees',
        columns: [
          {
            title: 'Name (English)',
            field: 'name_en',
          },
          {
            title: 'Name (Khmer)',
            field: 'name_kh',
          },
          {
            title: 'Type',
            field: 'type',
            mutator: (_, row) => row.payee_type?.name,
          },
          {
            title: 'Phone Number',
            field: 'phone_number',
          },
          {
            title: 'Actions',
            field: 'actions',
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            mutator: () => getActionButtons(
              hasPermission('PAYEE', 'VIEW'),
              hasPermission('PAYEE', 'UPDATE'),
              hasPermission('PAYEE', 'DELETE'),
            ),
            cellClick:(e, cell)=> {
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector('a.edit svg')?.contains(e.target)) {
                this.$router.push({name: 'PayeeEdit', params: {id: dataId}})
              } else if (cell._cell.element.querySelector('a.view svg')?.contains(e.target)) {
                this.$router.push({name: 'PayeeDetail', params: {id: dataId}})
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
          PayeeService.delete(id).then(res => {
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
        {field: 'name_en', type: 'like', value: e.target.value},
        {field: 'name_kh', type: 'like', value: e.target.value},
        {field: 'payeeType.name', type: 'like', value: e.target.value},
        {field: 'phone_number', type: 'like', value: e.target.value},
      ])
    }
  },
}
</script>