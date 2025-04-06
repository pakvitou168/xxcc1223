<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew" />
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter" />
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="(ref) => (tabulator = ref)" :options="options" />
      </div>
    </div>
  </div>
</template>

<script>

import AddNew from '../../../components/Form/AddNew.vue'
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
      functionCode: "PRODUCT_LINE",
      addNew: {
        label: "Product Lines",
        name: "Product Line",
        prefix: "Add New",
        linkAddNew: "/product-configuration/product-lines/new",
      },
      tabulator: null,
      options: {
        persistenceID: "product-lines-table",
        ajaxURL: "/product-lines",
        columns: [
          {
            title: "Product Line Code",
            field: "code",
            sorter: "string",
          },
          {
            title: "Description",
            field: "description",
            headerHozAlign: "left",
          },
          {
            title: "Status",
            field: "status",
            headerSort: false,
            width: 120,
            mutator: (value) => {
              if (value == "ACT") {
                return '<span class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full">Active</span>';
              } else if (value == "DEL") {
                return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Deleted</span>';
              }
              return "";
            },
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
          },
          {
            title: "Actions",
            field: "actions",
            headerSort: false,
            headerHozAlign: "center",
            width: 105,
            formatter: "html",
            cellClick: (e, cell) => {
              const viewButton = cell._cell.element.querySelector("a.view svg");
              const editButton = cell._cell.element.querySelector("a.edit svg");
              const deleteButton = cell._cell.element.querySelector("a.delete svg");
              let dataId = cell._cell.row.data.id;

              if (editButton.contains(e.target)) {
                this.$router.push({
                  name: "ProductLineUpdate",
                  params: {
                    id: dataId,
                  },
                });
              } else if (deleteButton.contains(e.target)) {
                this.handleDelete(dataId);
              } else if (e.target == viewButton) {
                this.$router.push({
                  name: "ProductLineDetail",
                  params: {
                    id: dataId,
                  },
                });
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
        message: "Do you want to delete this record?",
        header: "Delete",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios
            .delete(`/product-lines/${id}`)
            .then((response) => {
              if (response.data.success) {
                notify(response.data.message, 'success', 'bottom-right')
                this.tabulator.replaceData();
              }
            })
            .catch(() => {
              notify('Error occurred', 'error', 'bottom-right')
            });
        },
      });
    },
    setFilter: _.debounce(function (e) {
      this.tabulator.setFilter([
        //searchbar
        { field: "code", type: "like", value: e.search },
        { field: "description", type: "like", value: e.search },
        //filter
        [{ field: "status", type: "=", value: e.status }],
      ]);
    }, 500),
  },
};
</script>