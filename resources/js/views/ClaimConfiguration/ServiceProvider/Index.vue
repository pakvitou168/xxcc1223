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
import ServiceProviderService from '@/services/claim/service_provider.service'
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
        label: 'Service Providers',
        name: 'Service Provider',
        prefix: 'Add New',
        linkAddNew: '/claim-configuration/service-providers/new',
      },
      canAddNew: hasPermission('SERVICE_PROVIDER', 'NEW'),
      tabulator: null,
      options: {
        ajaxURL: '/claim-service-providers',
        columns: [
          {
            title: 'Name',
            field: 'name',
          },
          {
            title: 'Email',
            field: 'email',
          },
          {
            title: 'Phone Number',
            field: 'phone_number',
          },
          {
            title: 'Type',
            field: 'type',
             headerSort: false,
          },
          {
            title: 'Is Partner',
            field: 'is_partner',
            headerSort: false,
            mutator: this.getIsPartner,
            formatter: "html",
            width: 120,
             headerHozAlign: "center",
          },
          {
            title: 'Actions',
            field: 'actions',
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            mutator: () => getActionButtons(
              hasPermission('SERVICE_PROVIDER', 'VIEW'),
              hasPermission('SERVICE_PROVIDER', 'UPDATE'),
              hasPermission('SERVICE_PROVIDER', 'DELETE'),
            ),
            cellClick:(e, cell)=> {
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector('a.edit svg')?.contains(e.target)) {
                this.$router.push({name: 'ServiceProviderEdit', params: {id: dataId}})
              } else if (cell._cell.element.querySelector('a.view svg')?.contains(e.target)) {
                this.$router.push({name: 'ServiceProviderDetail', params: {id: dataId}})
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
          ServiceProviderService.delete(id).then(res => {
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

    getIsPartner(value) {
      if (value) {
        return `
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="check-circle" role="presentation" class="text-green-500 fill-current text-success mx-auto">
            <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"></path>'
          </svg>
        `;
      } else {
        return `
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="x-circle" role="presentation" class="text-red-500 fill-current text-danger mx-auto">
            <path d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"></path>
          </svg>
        `;
      }
    },

    setFilter(e) {
      this.tabulator.setFilter([
        {field: 'name', type: 'like', value: e.target.value},
        {field: 'email', type: 'like', value: e.target.value},
        {field: 'phone_number', type: 'like', value: e.target.value},
        {field: 'type', type: 'like', value: e.target.value},
        
      ])
    }
  },
}
</script>