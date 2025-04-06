<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew" />
    <div class="intro-y box p-5 mt-5">
      <Search  @setFilter="setFilter"/>
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="ref => tabulator = ref" :options="options" />
      </div>
    </div>
  </div>
</template>

<script>
import AddNew from "../../../components/Form/AddNew.vue";
import Search from './Search.vue'
import UserPermissions from '../../../mixins/UserPermissions'

export default {
  mixins: [UserPermissions],
  components: {
    AddNew,
    Search,
  },
  data() {
    return {
      functionCode: 'PRODUCT',
      addNew: {
        persistenceID: "products-table",
        label: "Products",
        name: "Product",
        prefix: "Add New",
        linkAddNew: "/product-configuration/products/new",
      },
      tabulator: null,
        options: {
          ajaxURL: "/products",
          columns: [
          {
            title: "Code",
            field: "code",
            sorter: "string",
            width: 120,
          },
          {
            title: "Product Name",
            field: "name",
            sorter: "string",
            headerHozAlign: 'left',
          },
          {
            title: "Product Line",
            field: "product_line_code",
            headerSort: false,
            headerHozAlign: 'left',
          },
          {
            title: "Alt Code",
            field: "alt_code",
            sorter: "string",
            headerHozAlign: 'left',
            width: 120,
          },
          {
            title: "Description",
            field: "description",
            sorter: "string",
            headerHozAlign: 'left',
          },
          {
            title: 'Is Renewable',
            field: 'renewable',
            width: 160,
            hozAlign: 'center',
            headerHozAlign: 'center',
            formatter: 'html',
            mutator: value => {
              if (value === 'Y') {
                return `
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="check-circle" role="presentation" class="text-green-500 fill-current text-success mx-auto">
                    <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"></path>'
                  </svg>
                `;
              }
              return `
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="x-circle" role="presentation" class="text-red-500 fill-current text-danger mx-auto">
                  <path d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"></path>
                </svg>
              `;
            }
          },
          {
            title: "Status",
            field: "status",
            width: 120,
            mutator: value => {
              if (value == 'ACT') {
                return '<span class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full">Active</span>'
              } else if (value == 'DEL') {
                return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Deleted</span>'
              }
              return ''
            },
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
            headerSort: false,
          },
          {
            title: "Actions",
            field: "actions",
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            cellClick: (e, cell)=> {
              const viewButton = cell._cell.element.querySelector("a.view svg");
              const editButton = cell._cell.element.querySelector("a.edit svg");
              const deleteButton = cell._cell.element.querySelector("a.delete svg");
              let dataId = cell._cell.row.data.id;

              if (editButton.contains(e.target)) {
                this.$router.push(
                  "/product-configuration/products/" + dataId + "/edit"
                );
              } else if (viewButton.contains(e.target)) {
                this.$router.push(
                  "/product-configuration/products/detail/" + dataId + ""
                );
              } else if (deleteButton.contains(e.target)) {
                this.handleDelete(dataId);
              }
            },
          },
        ],
      },
    };
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
          axios.delete(`/products/${id}`).then(response => {
            if (response.data.success) {
              // refresh table
              notify(response.data.message, 'success', 'bottom-right')
              this.tabulator.replaceData()
            }
          }).catch(err => {
              notify('Error', 'error', 'bottom-right')
          })
        },
      });
    },
    setFilter: _.debounce(function (e) {
      this.tabulator.setFilter([
        // Search
        {field: 'code', type: 'like', value: e.search},
        {field: 'name', type: 'like', value: e.search},
        {field: 'alt_code', type: 'like', value: e.search},
        {field: 'description', type: 'like', value: e.search},
        {field: 'productLine.code', type: 'like', value: e.search},
        // Filter
        [
          {field: 'product_line_code', type: '=', value: e.productLine},
          {field: 'status', type: '=', value: e.status},
        ]
      ])      
    }, 500)
  },
};
</script>
