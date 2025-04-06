<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew" />
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter" />
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options" />
      </div>
    </div>
  </div>
</template>

<script>
import AddNew from "@/components/Form/AddNew.vue";
import Search from "./Search.vue";
import UserPermissions from "../../../mixins/UserPermissions";

export default {
  mixins: [UserPermissions],

  components: {
    AddNew,
    Search,
  },

  data() {
    return {
      functionCode: "REINSURANCE_PARTNER_GROUP",
      addNew: {
        label: "Reinsurance Partner Groups",
        name: "Reinsurance Partner Group",
        prefix: "Add New",
        linkAddNew: "/reinsurance-management/reinsurance-partner-groups/new",
      },
      tabulator: null,
      options: {
        persistenceID: "reinsurance-partner-groups-table",
        ajaxURL: "/reinsurance-partner-groups",
        columns: [
          {
            title: "Code",
            field: "code",
            sorter: "string",
          },
          {
            title: "Name",
            field: "name",
            sorter: "string",
          },
          {
            title: "Description",
            field: "description",
            headerSort: false,
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
              let dataId = cell._cell.row.data.id;
              if (cell._cell.element.querySelector("a.edit svg").contains(e.target))
                this.$router.push("/reinsurance-management/reinsurance-partner-groups/" + dataId + "/edit");
              else if (cell._cell.element.querySelector("a.view svg").contains(e.target))
                this.$router.push("/reinsurance-management/reinsurance-partner-groups/" + dataId + "");
              else if (cell._cell.element.querySelector("a.delete svg").contains(e.target))
                this.handleDelete(dataId);
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
            .delete(`/reinsurance-partner-groups/${id}`)
            .then((response) => {
              if (response.data.success) {
                // refresh table
                notify(response.data.message, "success",'bottom-right');
                this.tabulator.replaceData();
              }
              else if(response.data?.error) {
                notify(response.data.message, "error",'bottom-right');
                this.tabulator?.replaceData();
              }
            })
            .catch((err) => {
              if(err?.response)
                notify(err.response?.data?.message, "error",'bottom-right');
              else
                notify("Something wrong...!", "error",'bottom-right');
            });
        },
      });
    },

    setFilter: _.debounce(function (e) {
      this.tabulator.setFilter([
        // Search
        { field: "name", type: "like", value: e.search },
        { field: "description", type: "like", value: e.search },
        { field: "code", type: "like", value: e.search },
      ]);
    }, 500),
  },
};
</script>
