<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew" />
    <div class="intro-y box p-5 mt-5">
      <SearchBar @setFilter="setFilter" />
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options" />
      </div>
    </div>
  </div>
</template>

<script>

import AddNew from '@/components/Form/AddNew'
import SearchBar from '@/components/DataTable/SearchBar'
import UserPermissions from '../../../mixins/UserPermissions'

export default {
  mixins: [UserPermissions],

  components: {
    AddNew,
    SearchBar,
  },
  data() {
    return {
      functionCode: "FUNCTION",

      addNew: {
        label: "Functions",
        name: "Function",
        prefix: "Add New",
        linkAddNew: "/user-management/functions/new",
      },
      tabulator: null,
      options: {
        persistenceID: "functions-table",
        ajaxURL: "/functions",
        columns: [
          {
            title: "Function Code",
            field: "code",
            sorter: "string",
          },
          {
            title: "Function Name",
            field: "name",
            sorter: "string",
          },
          {
            title: "Description",
            field: "description",
            sorter: "string",
          },
          {
            title: "Status",
            field: "status",
            width: 120,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
            headerSort: false,
            mutator: (value) => {
              if (value == "ACT") {
                return '<span class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full">Active</span>';
              } else if (value == "BLK") {
                return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Blocked</span>';
              } else if (value == "DBL") {
                return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Disabled</span>';
              } else if (value == "DEL") {
                return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Deleted</span>';
              } else if (value == "LCK") {
                return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Locked</span>';
              }
              return "";
            },
          },
          {
            title: "Actions",
            field: "actions",
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            cellClick: (e, cell) => {
              let dataId = cell._cell.row.data.id;

              if (
                cell._cell.element
                  .querySelector("a.edit svg")
                  .contains(e.target)
              ) {
                this.$router.push({
                  name: "FunctionUpdate",
                  params: {
                    id: dataId,
                  },
                });
              } else if (
                cell._cell.element
                  .querySelector("a.view svg")
                  .contains(e.target)
              ) {
                this.$router.push({
                  name: "FunctionDetail",
                  params: {
                    id: dataId,
                  },
                });
              } else if (
                cell._cell.element
                  .querySelector("a.delete svg")
                  .contains(e.target)
              ) {
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
        message: "Do you want to delete this record?",
        header: "Delete",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios
            .delete(`/functions/${id}`)
            .then((response) => {
              if (response.data.success) {
                // refresh table
                this.toastMessage(response.data.message, "Success");
                this.tabulator.replaceData();
              }
            })
            .catch((err) => {
              this.toastMessage("Error", "Error");
            });
        },
      });
    },

    setFilter: _.debounce(function (e) {
      this.tabulator.setFilter([
        // Search
        { field: "code", type: "like", value: e.target.value },
        { field: "name", type: "like", value: e.target.value },
      ]);
    }, 500),

    toastMessage(msg, type, position = "bottom") {
      this.$notify(
        {
          group: position,
          title: type,
          text: msg,
        },
        4000
      );
    },
  },
};
</script>