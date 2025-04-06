<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew" />
    <div class="intro-y box p-5 mt-5">
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options" />
      </div>
    </div>
  </div>
</template>

<script>
import AddNew from "@/components/Form/AddNew.vue";
import UserPermissions from '../../../mixins/UserPermissions'

export default {
  mixins: [UserPermissions],

  components: {
    AddNew,
  },
  data() {
    return {
      functionCode: "GROUP",
      addNew: {
        label: "Groups",
        name: "Group",
        prefix: "Add New",
        linkAddNew: "/user-management/groups/new",
      },
      tabulator: null,
      options: {
        ajaxURL: "/groups",
        columns: [
          {
            title: "Group Code",
            field: "code",
          },
          {
            title: "Group Name",
            field: "name",
          },
          {
            title: "Description",
            field: "description",
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
              } else if (value == "DEL") {
                return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Deleted</span>';
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
              let rowId = cell._cell.row.data.id;

              if (
                cell._cell.element
                  .querySelector("a.view svg")
                  .contains(e.target)
              ) {
                this.$router.push({
                  name: "GroupDetail",
                  params: {
                    id: rowId,
                  },
                });
              } else if (
                cell._cell.element
                  .querySelector("a.edit svg")
                  .contains(e.target)
              ) {
                this.$router.push({
                  name: "GroupUpdate",
                  params: {
                    id: rowId,
                  },
                });
              } else if (
                cell._cell.element
                  .querySelector("a.delete svg")
                  .contains(e.target)
              ) {
                this.handleDelete(rowId);
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
            .delete(`/groups/${id}`)
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
  },
};
</script>
