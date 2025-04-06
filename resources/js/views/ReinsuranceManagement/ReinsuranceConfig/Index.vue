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
      functionCode: "REINSURANCE_CONFIG",
      addNew: {
        label: "Reinsurance Config",
        name: "Reinsurance Config",
        prefix: "Add New",
        linkAddNew: "/reinsurance-management/reinsurance-configs/new",
      },
      tabulator: null,
      options: {
        persistenceID: "reinsurance-configs-table",
        ajaxURL: "/reinsurance-configs",
        columns: [
          {
            title: "Prod. Line",
            field: "product_line_code",
            headerSort: false,
            width: 110,
          },
          {
            title: 'UW Year',
            field: 'uw_year',
            sorter: 'number',
            width: 110,
          },
          {
            title: "Prod. Code",
            field: "product_code",
            sorter: "string",
            mutator: (_, row) => {
                if (row._product)
                    return `${row.product_code} - ${row._product?.name}`
            },
            minWidth: 250,
          },
          {
            title: "Rein. Type",
            field: "reinsurance_type",
            sorter: "string",
            mutator: (_, row) => {
              if (row._reinsurance_type) return row._reinsurance_type.name
              else return row.reinsurance_type;
            },
            width: 120,
          },
          {
            title: "Rein. Code",
            field: "reinsurance_code",
            sorter: "string",
            mutator: (_, row) => {
              if (row._reinsurance) return row._reinsurance.name
              else return row.reinsurance_code
            },
            minWidth: 120,
          },
          {
            title: "Partner Code",
            field: "partner_code",
            sorter: "string",
            mutator: (_, row) => {
              if (row._reinsurance_partner) return row._reinsurance_partner.name
              else return row.partner_code;
            },
            minWidth: 270,
          },
          {
            title: "Parent Code",
            field: "parent_code",
            sorter: "string",
            mutator: (_, row) => {
              if (row._parent_code) return row._parent_code.name
              else return row.parent_code;
            },
            minWidth: 120,
          },
          {
            title: "Level",
            field: "lvl",
            sorter: "string",
            minWidth: 80,
          },
          {
            title: "Start From",
            field: "start_from",
            sorter: "string",
            minWidth: 120,
          },
          {
            title: "Start To",
            field: "start_to",
            sorter: "string",
            minWidth: 120,
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
                this.$router.push("/reinsurance-management/reinsurance-configs/" + dataId + "/edit");
              else if (cell._cell.element.querySelector("a.view svg").contains(e.target))
                this.$router.push("/reinsurance-management/reinsurance-configs/" + dataId + "");
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
            .delete(`/reinsurance-configs/${id}`)
            .then((response) => {
              if (response.data.success) {
                // refresh table
                notify(response.data.message, "success", "bottom-right");
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
        { field: "product_line_code", type: "like", value: e.search },
        { field: "product_code", type: "like", value: e.search },
        { field: "reinsurance_type", type: "like", value: e.search },
        { field: "reinsurance_code", type: "like", value: e.search },
        { field: "partner_code", type: "like", value: e.search },
        { field: "uw_year", type: "like", value: e.search },
        [
          {field: 'product_code', type: '=', value: e.product},
        ]
      ]);
    }, 500),

  },
};
</script>
