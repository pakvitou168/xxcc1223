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
import Search from './Search.vue'
import AddNew from '@/components/Form/AddNew.vue'
import UserPermissions from '../../../mixins/UserPermissions'

export default {
  mixins: [UserPermissions],

  components: {
    Search,
    AddNew,
  },
  data() {
    return {
      functionCode: 'COMP_FRM_ELEMENT',
      addNew: {
        label: 'Component Formula Elements',
        name: 'Formula Element',
        prefix: 'Add New',
        linkAddNew: '/product-configuration/comp-frm-element/new',
      },
      tabulator: null,
      options: {
        ajaxURL: '/comp_form_element',
        columns: [
          {
            title: 'Product',
            field: 'product_code',
            mutator: (_, data) => `${data.product?.code} - ${data.product?.name}`,
          },
          {
            title: 'Cover Name',
            field: 'component_code',
            mutator: (_, data) => data.prod_comp?.name,
          },
          {
            title: 'Formula Code',
            field: 'formula_code',
          },
          {
            title: 'Element Code',
            field: 'elem_code',
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
            title: 'Actions',
            field: 'actions',
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            cellClick: (e, cell)=>{
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector('a.edit svg').contains(e.target)) {
                this.$router.push({
                  name:"FrmElementUpdate",
                  params:{
                    id:dataId
                  }
                })
              } else if (cell._cell.element.querySelector('a.view svg').contains(e.target)) {
                this.$router.push({
                  name:"FrmElementDetail",
                  params:{
                    id:dataId
                  }
                })
              } else if (cell._cell.element.querySelector('a.delete svg').contains(e.target)) {
                this.handleDelete(dataId)
              }
            },
          },
        ],
      },
    }
  },
  methods: {
    async handleDelete(id) {
      this.$confirm.require({
      message: 'Do you want to delete this record?',
      header: 'Delete',
      icon: 'pi pi-info-circle',
      acceptClass: "p-button-danger",
      blockScroll: false,
      accept: async () => {
        try {
        const response = await axios.delete(`/comp_form_element/${id}`)
        if (response.data.success) {
          notify(response.data.message, 'success', 'bottom-right')
          this.tabulator.replaceData()
        }
        } catch (err) {
        if (err.response?.status === 422) {
          this.errors = err.response.data.errors
        } else {
          notify('Error', 'error', 'bottom-right')
        }
        }
      }
      })
    },
    setFilter(e) {
      this.tabulator.setFilter([
        //search
        {field: 'elem_code', type: 'like', value: e.search},
        //filter
        [
          {field: 'product_code', type: '=', value: e.product},
          {field: 'component_code', type: '=', value: e.cover},
          {field: 'formula_code', type: '=', value: e.formula_code},
          {field: 'status', type: '=', value: e.status},
        ]
      ])
    }
  },
}
</script>
