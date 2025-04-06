<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew" />
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter" :clauseTypeOptions="clauseTypeOptions"/>
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

export default {
  mixins: [UserPermissions],

  components: {
    Search,
    AddNew,
  },
  data() {
    return {
      clauseTypeOptions:{},
      functionCode: 'CLAUSE_MAINTENANCE',
      addNew: {
        label: 'Clause Maintenance',
        name: 'Clause Maintenance',
        prefix: 'Add New',
        linkAddNew: '/product-configuration/clause-maintenances/new',
      },
      tabulator: null,
      options: {
        persistenceID: 'clause-maintenances-table',
        ajaxURL: '/clause-maintenances',
        columns: [
          {
            title: 'Clause Code',
            field: 'code',
            width: 140
          },
           {
            title: 'Clause',
            field: 'clause',
          },
           {
            title: 'Product Line',
            field: 'product_line',
            mutator: (_, row) => {
                  return row.product_line_code
              },          
            },
          {
            title: 'Clause Type',
            field: 'clause_type',
            width: 140,
          },
           {
            title: 'Default Inclusion',
            field: 'default_inclusion',
            mutator: this.getDefaultInclusion,
            formatter: 'html',
            width: 190
          },
          {
            title: 'Sequence No.',
            field: 'sequence',
            sorter: 'number',
            width: 150
          },
          {
            title: 'Actions',
            field: 'actions',
            width: 120,
            headerSort: false,
            hozAlign: 'left',
            headerHozAlign: 'left',
            formatter: 'html',
            cellClick:(e, cell)=> {
              let dataId = cell._cell.row.data.id

              if (cell._cell.element.querySelector('a.edit svg ').contains(e.target)) {
                this.$router.push(`/product-configuration/clause-maintenances/${dataId}/edit`)
              }  else if (cell._cell.element.querySelector('a.view svg ').contains(e.target)) {
                this.$router.push(`/product-configuration/clause-maintenances/${dataId}`)
              }  else if (cell._cell.element.querySelector('a.delete svg').contains(e.target)) {
                this.handleDelete(dataId)
              }
            },
          },
        ],
      },
    }
  },
  methods: {
    getDefaultInclusion(value) {
      if (value == 'Y') {
        return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="check-circle" role="presentation" class="text-green-500 fill-current text-success mx-auto">
            <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"></path>'
          </svg>
        `;
      } else if (value == 'N') {
        return  `
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="x-circle" role="presentation" class="text-red-500 fill-current text-danger mx-auto">
            <path d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"></path>
          </svg>
        `;
      }
      return ''
    },
    handleDelete(id) {
      this.$confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Delete',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios.delete(`/clause-maintenances/${id}`).then(response => {
            if (response.data.success) {
              // refresh table
              notify(response.data.message, 'success','bottom-right')
              this.tabulator.replaceData()
            }
          }).catch(() => notify('Error', 'error','bottom-right'))
        },
      });
    },
    toastMessage(msg, type, position = 'bottom') {
      this.$notify({
        group: position,
        title: type,
        text: msg
      }, 4000)
    },
    setFilter(e) {
      this.tabulator.setFilter([
        {field: 'code', type: 'like', value: e.search},
        {field: 'clause', type: 'like', value: e.search},
        [
          { field: 'clause_type', type: '=', value: e.clause_type},
          { field: 'default_inclusion', type: '=', value: e.default_inclusion},
        ]
      ])
    },
    getServices() {
        axios.get('/clause-maintenance-service/get-clause-types').then(response => {
          if(response.data){
            this.clauseTypeOptions = response.data
          }
        });
    },
  },
  mounted() {
        this.getServices();
    },
}
</script>
