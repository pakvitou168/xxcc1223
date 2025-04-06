<template>
  <div>
    <AddNew v-if="canNew" :addNew="addNew"></AddNew>
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter"/>
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options"/>
      </div>
    </div>
  </div>
</template>

<script>
import AddNew from "@/components/Form/AddNew.vue";
import {getActionButtons} from "@/components/DataTable/actionButton";
import {hasPermission} from "@/services/auth.service";
import Search from "./Search.vue";

export default {
  components: {
    AddNew,
    Search,
  },

  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!",
      SUCCESS_MESSAGE:"Success!",
      addNew: {
        label: "H & S Endorsements",
      },
      tabulator: null,
      options: {
        ajaxURL: "/hs/endorsements",
        columns: [
          {
            title: "Policy No.",
            field: "document_no",
            width: 200,
          },
          {
            title: "Customer Name",
            field: "name_en",
            minWidth: 200,
          },
          {
            title: "Premium",
            field: "total_premium",
            hozAlign: "right",
            // width: 140,
            headerSort: false,
          },
          {
            title: "Issue Date",
            field: "issued_at",
            formatter: "datetime",
            width: 110,
            formatterParams: {
              outputFormat: "DD/MM/YY",
            },
          },
          {
            title: "Version",
            field: "version",
            sorter: "number",
            width: 100,
          },
          {
            title: "Cycle",
            field: "cycle",
            sorter: "number",
            width: 100,
          },
          {
            title: "Endorsement Type",
            field: "endorsement_type",
            width: 170,
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
            minWidth: 125,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
            headerSort: false,
            mutator: (_, row) => {
              if (row.endorsement.approved_status === "SBM")
                return '<span class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Submitted</span>';
              else if (row.endorsement.approved_status === "PRG")
                return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">In Progress</span>';
              return "";
            },
          },
          {
            title: "Approved Reason",
            field: "approved_reason",
            tooltip: true,
            headerSort: false,
            minWidth: 180,
            mutator: (_, row) => row.endorsement?.approved_reason,
          },
          {
            title: "Actions",
            field: "actions",
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            mutator: (_, data) => {
              return getActionButtons(
                true,
                this.canUpdate(data),
                this.canDelete(data),
                false
              );
            },
            cellClick: (e, cell) => {
              let dataId = cell._cell.row.data.id;

              if (
                cell._cell.element
                  .querySelector("a.edit svg")
                  ?.contains(e.target)
              ) {
                this.$router.push({
                  name: "HSEndorsementEdit",
                  params: {
                    id: dataId,
                  },
                });
              } else if (
                cell._cell.element
                  .querySelector("a.view svg")
                  ?.contains(e.target)
              ) {
                this.$router.push({
                  name: "HSEndorsementDetail",
                  params: {id: dataId},
                });
              } else if (
                cell._cell.element
                  .querySelector("a.delete svg")
                  ?.contains(e.target)
              ) {
                this.handleDelete(dataId);
              }
            },
          },
        ],
      },
    };
  },
  computed: {
    canNew() {
      return hasPermission('HS_ENDORSEMENT', 'NEW')
    }
  },
  methods: {
    canUpdate(data) {
      if (["APV", "REJ"].includes(data.origin_status)) return false;
      else if (
        data.endorsement.approved_status !== "PRG" &&
        ["ADD/DELETE", "GENERAL"].includes(data.endorsement_type)
      )
        return false;
      else return hasPermission('HS_ENDORSEMENT', 'UPDATE');
    },
    canDelete(data) {
      return ["APV", "REJ"].includes(data.origin_status) && hasPermission('HS_ENDORSEMENT', 'DELETE') ? false : true;
    },
    handleDelete(id) {
      this.$confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Delete',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-danger px-2 py-2",
        rejectClass: "p-button-default",
        blockScroll: false,
        acceptLabel: 'Delete',
        rejectLabel: 'Cancel',
        accept: () => {
          axios
            .delete(`/hs/endorsements/${id}`)
            .then((response) => {
              if (response.data.success) {
                // refresh table
                notify(response.data.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
                this.tabulator.replaceData();
              }
            })
            .catch((err) => {
              let error = err.response;
              notify(err.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
              if (error.status === 409) {
                this.tabulator.replaceData();
              }
            });
        },
      });
    },
    setFilter(e) {
      this.tabulator.setFilter([
        {field: "document_no", type: "like", value: e.search},
        {field: "name_en", type: "like", value: e.search},
        {field: "total_premium", type: "like", value: e.search},
        {field: "version", type: "like", value: e.search},
        {field: "cycle", type: "like", value: e.search},
      ]);
    },
  },
};
</script>