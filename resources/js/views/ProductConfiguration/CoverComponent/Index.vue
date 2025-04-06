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
import axios from 'axios'
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
      functionCode: 'COVER_COMPONENT',
      addNew: {
        label: 'Cover Components',
        name: 'Cover Component',
        prefix: 'Add New',
        linkAddNew: '/product-configuration/cover-component/new',
      },
      tabulator: null,
      options: {
        ajaxURL: '/cover-component',
        columns: [
          {
            title: 'Product',
            field: 'product_code',
            mutator: (_, data) => `${data.product?.code} - ${data.product?.name}`,
          },
          {
            title: 'Cover Component Code',
            field: 'code',
          },
          {
            title: 'Cover Component Name',
            field: 'name',
          },
          {
            title: 'Description',
            field: 'description',
          },
           {
            title: 'Value',
            field: 'value',
            sorter: 'number',
            width: 100,
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
                this.$router.push({
                  name:"CoverComponentUpdate",
                  params:{
                    id:dataId
                  }
                })
              } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                this.$router.push({
                  name:"CoverCompDetail",
                  params:{
                    id:dataId
                  }
                })
              } else if (cell._cell.element.querySelector("a.delete svg").contains(e.target)) {
                this.handleDelete(dataId);
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
          axios.delete(`/cover-component/${id}`).then(response => {
            if (response.data.success) {
              // refresh table
              notify(response.data.message, 'success','bottom-right')
              this.tabulator.replaceData()
            }
          }).catch(() => notify('Error', 'error','bottom-right'))
        },
      });
    },


    setFilter(e) {
      this.tabulator.setFilter([
        //search
        {field: 'code', type: 'like', value: e.search},
        {field: 'name', type: 'like', value: e.search},
        {field: 'product_code', type: 'like', value: e.search},
        {field: 'product.name', type: 'like', value: e.search},
       //filter
        [
          {field: 'product_code', type: 'like', value: e.product},
          {field: 'status', type: 'like', value: e.status},
        ]
      ])
    }
  },
}
</script>
