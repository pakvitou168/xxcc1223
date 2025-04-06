<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew"> 
      <button class="btn btn-success shadow-md mr-2" title="Export" @click="openReportDialog">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
          </path>
        </svg>
      </button>
    </AddNew>
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter" />
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options" />
      </div>
    </div>
    <ReviseDialog :isVisible="showDialog" header="Revise Schema" :submitted="submitted" :id="claimId"
      :options="{ APV: 'Approve', REJ: 'Reject' }" value="RVS" @hideDialog="hideDialog" @confirm="reviseSchema" />
    <ReportDialog :isVisible="showReportDialog" header="Claim Report" @hideDialog="hideReportDialog"
      @exportClaimReport="exportClaimReport" />
  </div>
</template>

<script>
import AddNew from "@/components/Form/AddNew.vue";
import Search from "@/views/Claim/Travel/Register/Search.vue";
import ClaimRegisterService from "@/services/claim/travel/claim_register.service";
import { hasPermission } from "@/services/auth.service";
import { getActionButtons } from "@/components/DataTable/actionButton";
import { debounce } from "@/helpers";
import ReviseDialog from "./Components/Revise.vue";
import ReportDialog from "./Components/ReportDialog.vue"

export default {
  components: {
    AddNew,
    Search,
    ReviseDialog,
    ReportDialog
  },

  data() {
    return {
      addNew: {
        label: "Registers",
        prefix: "New",
        linkAddNew: "/claim/travel/registers/new",
      },
      claimId: null,
      submitted: false,
      showDialog: false,
      showReportDialog: false,
      canAddNew: hasPermission("HS_CLAIM_REGISTER", "NEW"),
      tabulator: null,
      options: {
        ajaxURL: "/travel/claim-service/registers",
        columns: [
          {
            title: "Policy No.",
            field: "policy.document_no",
            minWidth: 170,
          },
          {
            title: "Claims No.",
            field: "claim_no",
            minWidth: 150,
          },
          {
            title: "Insured Name",
            field: "insured_person.name",
            minWidth: 150,
          },
          {
            title: "Date of Loss",
            field: "date_of_loss",
            minWidth: 80,
          },
          {
            title: "Date of notification",
            field: "notification_date",
            minWidth: 180,
          },
          {
            title: "Reserve Amount",
            field: "total_reserve_amount",
            width: 150,
            hozAlign: "right",
            formatter: "money",
          },
          {
            title: "Cause of Loss",
            field: "detail.cause",
            headerSort: true,
            minWidth: 170,
          },
          {
            title: "Approve Status",
            field: "c_approved_status",
            headerSort: false,
            headerHozAlign: "center",
            hozAlign: "center",
            width: 135,
            formatter: "html",
            mutator: (_, row) => {
              if (row.approved_status === "APV") {
                return '<span class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Approved</span>';
              } else if (row.approved_status === "REJ") {
                return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Rejected</span>';
              } else if (row.approved_status === null) {
                return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Pending</span>';
              }
              return "";
            },
          },
          {
            title: "Status",
            field: "c_status",
            headerSort: false,
            headerHozAlign: "center",
            hozAlign: "center",
            width: 135,
            formatter: "html",
            mutator: (_, row) => {
              if (row.approved_status === "APV" && row.schema_approved_by != null) {
                return '<span class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Approved</span>';
              } else if (row.approved_status === "REJ") {
                return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Rejected</span>';
              } else if (row.approved_status === null || row.schema_approved_by == null) {
                return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Pending</span>';
              }
              return "";
            },
          },
          {
            title: "Actions",
            field: "actions",
            width: 120,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            mutator: (_, row) =>
              getActionButtons(
                hasPermission("HS_CLAIM_REGISTER", "VIEW"),
                this.canUpdate(row.approved_status),
                this.canDelete(row.approved_status),
                this.canRevise(row.approved_status, row.schema_approved_status),
                this.canSchema(row.approved_status, row.schema_approved_by)
              ),
            cellClick: (e, cell) => {
              let dataId = cell._cell.row.data.detail.claim_id;

              if (
                cell._cell.element
                  .querySelector("a.edit svg")
                  ?.contains(e.target)
              ) {
                this.$router.push({
                  name: "ClaimHSRegisterEdit",
                  params: { id: dataId },
                });
              } else if (
                cell._cell.element
                  .querySelector("a.revise svg")
                  ?.contains(e.target)
              ) {
                this.handleRevise(dataId);
              } else if (
                cell._cell.element
                  .querySelector("a.view svg")
                  ?.contains(e.target)
              ) {
                this.$router.push({
                  name: "TravelClaimRegisterDetail",
                  params: { id: dataId },
                });
              } else if (
                cell._cell.element
                  .querySelector("a.schema svg")
                  ?.contains(e.target)
              ) {
                this.$router.push({
                  name: "TravelClaimRegisterSchema",
                  params: { id: dataId },
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
  methods: {
    handleDelete(id) {
      this.$confirm.require({
        message: "Do you want to delete this record?",
        header: "Delete",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          ClaimRegisterService.delete(id)
            .then((res) => {
              if (res.data.success) {
                this.tabulator.replaceData();
                this.$notify(
                  {
                    group: "bottom",
                    title: "Success",
                    text: res.data?.message,
                  },
                  4000
                );
              }
            })
            .catch((err) => {
              this.$notify(
                {
                  group: "bottom",
                  title: "Error",
                  text: err?.response?.data?.message,
                },
                4000
              );
            });
        },
      });
    },
    reviseSchema(data) {
      this.submitted = true
      ClaimRegisterService.revise(this.claimId, data)
        .then((res) => {
          this.submitted = false
          if (res.data?.success) {
            this.showDialog = false
            this.$notify(
              {
                group: "bottom",
                title: "Success",
                text: res.data?.message,
              },
              4000
            );
            this.$router.push({
              name: "ClaimHSRegisterSchema",
              params: { id: this.claimId },
            });
          }
        })
        .catch((err) => {
          this.submitted = false
          console.log(err);
          this.$notify(
            {
              group: "bottom",
              title: "Error",
              text: err?.response?.data?.message,
            },
            4000
          );
        });
    },
    handleRevise(id) {
      this.showDialog = true
      this.claimId = id
    },

    setFilter(e) {
      debounce(() =>
        this.tabulator.setFilter([
          { field: "document_no", type: "like", value: e.search },
          { field: "insured_name", type: "like", value: e.search },
          { field: "claim_no", type: "like", value: e.search },
          // [
          //   {
          //     field: "notification_date",
          //     type: "=",
          //     value: e.notification_date,
          //   },
          //   { field: "incident_date", type: "=", value: e.incident_date },
          // ],
        ])
      );
    },

    canUpdate(approve_status) {
      let canUpdatePermission = hasPermission("HS_CLAIM_REGISTER", "UPD");
      if (!canUpdatePermission) return false;
      return approve_status == null;
    },
    canDelete(approve_status) {
      let canDeletePermission = hasPermission("HS_CLAIM_REGISTER", "DEL");
      if (!canDeletePermission) return false;
      return approve_status === null;
    },

    canRevise(approve_status, schema_approved_status) {
      let canRevisePermission = hasPermission("HS_CLAIM_REGISTER", "REV");
      if (!canRevisePermission) return false;
      if (approve_status === null) return false;
      return schema_approved_status == "APV";
    },
    canSchema(approve_status, approve_schema) {
      let canUpdatePermission = hasPermission("HS_CLAIM_REGISTER", "UPD");
      if (approve_status != 'APV' || !canUpdatePermission) return false;
      return approve_schema == null;
    },
    hideDialog() {
      this.showDialog = false;
    },
    exportClaimReport(formData) {
      location.href =
        "/hs/claim-report/type" +
        formData.report_type +
        "/from" +
        formData.from_date +
        "/to" +
        formData.to_date;
    },
    openReportDialog() {
      this.showReportDialog = true
    },
    hideReportDialog() {
      this.showReportDialog = false
    },
  },
};
</script>