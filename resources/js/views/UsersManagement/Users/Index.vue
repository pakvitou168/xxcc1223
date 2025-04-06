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

import AddNew from '@/components/Form/AddNew.vue'
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
      functionCode: "USER",
      addNew: {
        label: "Users",
        name: "Users",
        prefix: "Add New",
        linkAddNew: "/user-management/users/new",
      },
      tabulator: null,
      options: {
        ajaxURL: "/users",
        columns: [
          {
            title: "Username",
            field: "username",
          },
          {
            title: "Full Name",
            field: "full_name",
          },
          {
            title: "Email",
            field: "email",
          },
          {
            title: "Branch Name",
            field: "branch_name",
          },
          {
            title: "Status",
            field: "status",
            width: 120,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
            headerSort: false,
            mutator: this.getStatus,
          },
          {
            title: "Actions",
            field: "actions",
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            cellClick: this.cellClick,
          },
        ],
      },
    };
  },
  methods: {
    cellClick(e, cell) {
      let rowId = cell._cell.row.data.id;

      if (cell._cell.element.querySelector("a.edit svg").contains(e.target)) {
        this.$router.push({
          name: "user-edit",
          params: {
            id: rowId,
          },
        });
      } else if (
        cell._cell.element.querySelector("a.delete svg").contains(e.target)
      ) {
        this.handleDelete(rowId);
      } else if (
        cell._cell.element.querySelector("a.view svg").contains(e.target)
      ) {
        this.$router.push({
          name: "user-detail",
          params: {
            id: rowId,
          },
        });
      }
    },
    getStatus(value) {
      if (value == "ACT") {
        return '<span class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full">Active</span>';
      } else if (value == "BLK") {
        return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Blocked</span>';
      } else if (value == "DBL") {
        return '<span class="text-xs px-2 bg-gray-200 text-black mr-1 py-1 rounded-full">Disabled</span>';
      } else if (value == "DEL") {
        return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Deleted</span>';
      } else if (value == "LCK") {
        return '<span class="text-xs px-2 bg-gray-200 text-black mr-1 py-1 rounded-full">Locked</span>';
      }
      return "";
    },
    handleDelete(id) {
      this.$confirm.require({
        message: "Do you want to delete this record?",
        header: "Delete",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios
            .delete(`/users/${id}`)
            .then((response) => {
              if (response.data.success) {
                // refresh table
                this.toastMessage(response.data.message, "Success");
                this.tabulator.replaceData();
              }
            })
            .catch(() => this.toastMessage("Error", "Error"));
        },
      });
    },
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
    setFilter(e) {
      this.tabulator.setFilter([
        { field: "username", type: "like", value: e.target.value },
        { field: "full_name", type: "like", value: e.target.value },
        { field: "email", type: "like", value: e.target.value },
      ]);
    },
  },
};
</script>
