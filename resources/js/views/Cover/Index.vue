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
import Search from './Search.vue'
import UserPermissions from '../../mixins/UserPermissions'

export default {
  mixins: [UserPermissions],
  components: {
    Search,
    AddNew,
  },
  data() {
    return {
      functionCode: 'COVER',
      addNew: {
        label: 'Covers',
        name: 'Cover',
        prefix: 'Add New',
        linkAddNew: '/product-configuration/covers/new',
      },
      tabulator: null,
      options: {
        ajaxURL: '/covers',
        columns: [
          {
            title: 'Product',
            field: 'product_code',
            width: 250,
            mutator: (_, row) => `${row.product?.code} - ${row.product?.name}`
          },
          {
            title: 'Cover Code',
            field: 'code',
            width: 130,
          },
          {
            title: 'Cover Name',
            field: 'name',
            minWidth: 200,
          },
          {
            title: 'Description',
            field: 'description',
            minWidth: 200,
          },
          {
            title: 'Is Mandatory',
            field: 'mandatory',
            headerSort: false,
            mutator: this.getIsMandatory,
            width: 160,
            hozAlign: 'center',
            headerHozAlign: 'center',
            formatter: 'html',
          },
          {
            title: 'Is Vehicle Value Required',
            field: 'is_required_vehicle_val',
            headerSort: false,
            mutator: this.getIsVehicleValueRequired,
            width: 160,
            hozAlign: 'center',
            headerHozAlign: 'center',
            formatter: 'html',
          },
          {
            title: 'Value',
            field: 'value',
            sorter: 'number',
            width: 90,
          },
          {
            title: 'Deductible Label',
            field: 'deductible_label',
            minWidth: 190,
          },
          {
            title: 'Sequence No',
            field: 'seq',
            sorter: 'number',
            width: 140
          },
          {
            title: "Status",
            field: "status",
            width: 120,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
            headerSort: false,
            mutator: value => {
                if (value == 'ACT') {
                  return '<span class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full">Active</span>'
                } else if (value == 'DEL') {
                  return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Deleted</span>'
                }
                return ''
            },
          },
          {
            title: "Actions",
            field: "actions",
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            cellClick: (e, cell)=> {
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector("a.edit svg").contains(e.target)) {
                this.$router.push(`/product-configuration/covers/${dataId}/edit`)
              } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                this.$router.push(`/product-configuration/covers/${dataId}`)
              } else if (cell._cell.element.querySelector("a.delete svg").contains(e.target)) {
                this.handleDelete(dataId)
              }
            },
          },
        ],
      },
    }
  },
  methods: {
    getIsMandatory(value) {
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
    getIsVehicleValueRequired(value) {
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
    handleDelete(id) {
      this.$confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Delete',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios.delete(`/covers/${id}`).then(response => {
            if (response.data.success) {
              // refresh table
              notify(response.data.message, 'success','bottom-right')
              this.tabulator.replaceData()
            }
          }).catch(err => {
            notify('Error', 'Error','bottom-right')
          })
        },
      });
    },
    setFilter(e) {
      this.tabulator.setFilter([
        {field: 'name', type: 'like', value: e.search},
        {field: 'code', type: 'like', value: e.search},
        {field: 'product_code', type: 'like', value: e.search},
        {field: 'product.name', type: 'like', value: e.search},
        [
          {field: 'product_code', type: '=', value: e.product},
          {field: 'status', type: '=', value: e.status},
        ]
      ])
    }
  },
}
</script>
