<template>
  <div>
    <AddNew :addNew="addNew" :canExport="canExport" @export="exportPoliciesExcel"/>
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter"/>
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options"/>
      </div>
    </div>
  </div>
</template>

<script>
import AddNew from "../../../components/Form/AddNew.vue";
import Search from "./Search.vue";
import {hasPermission} from "@/services/auth.service";
import {getActionButtons} from "@/components/DataTable/actionButton";

export default {
  components: {
    AddNew,
    Search,
  },

  data() {
    return {
      ERROR_MESSAGE: "Something went wrong!",
      SUCCESS_MESSAGE: "Success!",
      addNew: {
        label: "H & S Policies",
        hasAddNew: false,
      },
      issued_date_from: "",
      issued_date_to: "",
      canExport: true,
      tabulator: null,
      options: {
        persistenceID: "policies-table",
        ajaxURL: "/hs/policies",
        columns: [
          {
            title: "Policy No.",
            field: "document_no",
            sorter: "string",
            minWidth: 200,
          },
          {
            title: "Quotation No.",
            field: "quotation",
            headerSort: false,
            minWidth: 200,
            mutator: (_, row) => {
              if (!row.policy?.quotation) return "";

              return row.policy?.quotation?.document_no;
            },
          },
          {
            title: "Customer Name",
            field: "name_en",
            sorter: "string",
            minWidth: 200,
          },
          {
            title: "Premium",
            field: "total_premium",
            sorter: "number",
            formatter: "money",
            width: 100,
          },
          {
            title: "Issue Date",
            field: "issued_at",
            formatter: "datetime",
            width: 120,
            formatterParams: {
              outputFormat: "DD/MM/YY",
            },
          },
          {
            title: "Version",
            field: "version",
            sorter: "number",
            width: 90,
          },
          {
            title: "Cycle",
            field: "cycle",
            sorter: "number",
            width: 90,
          },
          {
            title: "Status",
            field: "status",
            width: 100,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
            headerSort: false,
            mutator: (value) => {
              if (value === "APV")
                return '<span class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Approved</span>';
              else if (value === "REJ")
                return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Rejected</span>';
              else if (value === "PND")
                return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Pending</span>';
              return "";
            },
          },
          {
            title: "Submit Status",
            field: "approved_status",
            minWidth: 120,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
            headerSort: false,
            mutator: (_, row) => {
              if (row.policy.approved_status === "SBM")
                return '<span class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Submitted</span>';
              else if (row.policy.approved_status === "PRG")
                return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">In Progress</span>';
              return "";
            },
          },
          {
            title: "Approved Reason",
            field: "approved_reason",
            headerSort: false,
            minWidth: 180,
            tooltip: true,
            mutator: (_, row) => {
              if (!row.policy) return "";

              return row.policy.approved_reason;
            },
          },
          {
            title: "Actions",
            field: "actions",
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            mutator: (_, row) =>
              getActionButtons(
                hasPermission('HS_POLICY', 'VIEW'),
                this.canUpdate(row._status),
                this.canDelete(row._status),
                this.canRevise(row._status),
              ),
            cellClick: (e, cell) => {
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector('a.edit svg')?.contains(e.target)) {
                this.$router.push({name: 'HSPolicyEdit', params: {id: dataId}})
              } else if (cell._cell.element.querySelector('a.revise svg')?.contains(e.target)) {
                this.handleRevise(dataId)
              } else if (cell._cell.element.querySelector('a.view svg')?.contains(e.target)) {
                this.$router.push({name: 'HSPolicyDetail', params: {id: dataId}})
              } else if (cell._cell.element.querySelector('a.delete svg')?.contains(e.target)) {
                this.handleDelete(dataId)
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
            .delete(`/hs/policies/${id}`)
            .then((response) => {
              if (response.data.success) {
                // refresh table
                notify(response.data.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
                this.tabulator.replaceData();
              }
            })
            .catch((err) => {
              notify(err.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
            });
        },
      });
    },
    handleRevise(id) {
      this.$confirm.require({
        message: "Do you want to revise this record?",
        header: "Revise",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-info",
        blockScroll: false,
        accept: () => {
          axios
            .post(`/hs/policies/revise/${id}`)
            .then((response) => {
              if (response.data.success) {
                notify(response.data.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
                this.tabulator.replaceData();
                this.$router.push({name: 'HSPolicyEdit', params: {id: id}});
              }
            })
            .catch((err) => {
              notify(err.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
            });
        },
      });
    },

    setFilter: _.debounce(function (e) {
      this.issued_date_from = e.issued_date_from;
      this.issued_date_to = e.issued_date_to;
      this.tabulator.setFilter([
        {field: "document_no", type: "like", value: e.search},
        {field: "name_en", type: "like", value: e.search},
        {field: "total_premium", type: "like", value: e.search},
        {field: "version", type: "like", value: e.search},
        {field: "cycle", type: "like", value: e.search},
        [
          {field: "issued_at", type: ">=", value: e.issued_date_from},
          {field: "issued_at", type: "<=", value: e.issued_date_to},
        ],
      ]);
    }, 500),

    exportPoliciesExcel() {
      location.href =
        "/hs/policy-services/export-policy/from" +
        this.issued_date_from +
        "/to" +
        this.issued_date_to;
    },

    canUpdate(status) {
      let canUpdatePermission = hasPermission("HS_POLICY", "UPDATE");
      if (!canUpdatePermission) return false;

      if (status == 'REJ' || status == 'APV') return false;
      return true;
    },

    canDelete(status) {
      let canDeletePermission = hasPermission("HS_POLICY", "DELETE");
      if (!canDeletePermission) return false;

      if (status === "APV") return false;
      return true;
    },

    canRevise(status) {
      let canRevisePermission = hasPermission("HS_POLICY", "REVISE");
      if (!canRevisePermission) return false;

      if (status !== "REJ") return false;
      return true;
    },
  },
};
</script>
