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
import Search from "./Search.vue";
import ClaimRegisterService from "@/services/claim/hs/claim_register.service";
import { hasPermission } from "@/services/auth.service";
import { getActionButtons } from "@/components/DataTable/actionButton";
import { debounce } from "@/helpers";
import ReviseDialog from "./Components/Revise.vue";
import ReportDialog from "./Components/ReportDialog.vue"
import Status from "@/components/DataTable/Status.vue";
import Action from "./Components/Action.vue"
import {createApp} from "vue";

export default {
  components: {
    AddNew,
    Search,
    ReviseDialog,
    ReportDialog
  },

  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!",
      SUCCESS_MESSAGE:"Success!",
      addNew: {
        label: "Registers",
        prefix: "New",
        linkAddNew: "/claim/hs/registers/new",
      },
      claimId: null,
      submitted: false,
      showDialog: false,
      showReportDialog: false,
      canAddNew: hasPermission("HS_CLAIM_REGISTER", "NEW"),
      tabulator: null,
      options: {
        ajaxURL: "/hs/claim-service/registers",
        columns: [
          {
            title: "Policy No.",
            field: "document_no",
            minWidth: 170,
          },
          {
            title: "Claims No.",
            field: "claim_no",
            minWidth: 150,
          },
          {
            title: "Insured Name",
            field: "insured_name",
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
            field: "reserve_amount",
            width: 150,
            hozAlign: "right",
            formatter: "money",
          },
          {
            title: "Cause of Loss",
            field: "cause_of_loss",
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
            formatter: (cell, formatterParams, onRendered) => {
              const rowData = cell.getRow().getData();
              const container = document.createElement("div");
              createApp(Status, { status: rowData.approved_status??'PND' }).mount(container);
              return container;
            }
          },
          {
            title: "Status",
            field: "c_status",
            headerSort: false,
            headerHozAlign: "center",
            hozAlign: "center",
            width: 135,
            formatter: (cell, formatterParams, onRendered) => {
              const rowData = cell.getRow().getData();
              const container = document.createElement("div");
              let status = null ;
              if (rowData.approved_status === "APV" && rowData.schema_approved_by != null) {
                status = 'APV'
              } else if (rowData.approved_status === "REJ") {
                status = 'REJ'
              } else if (rowData.approved_status === null || rowData.schema_approved_by == null) {
                status = 'PND'
              }
              createApp(Status, { status: status }).mount(container);
              return container;
            }
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
              let dataId = cell._cell.row.data.claim_id;

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
                  name: "ClaimHSRegisterDetail",
                  params: { id: dataId },
                });
              } else if (
                cell._cell.element
                  .querySelector("a.schema svg")
                  ?.contains(e.target)
              ) {
                this.$router.push({
                  name: "ClaimHSRegisterSchema",
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
        message: 'Do you want to delete this record?',
        header: 'Delete',
        icon: 'pi pi-exclamation-triangle text-red-500',
        rejectClass: 'p-button-secondary p-button-outlined',
        rejectLabel: 'No',
        acceptLabel: 'Yes',
        acceptClass: 'p-button-danger',
        accept: () => {
          ClaimRegisterService.delete(id)
            .then((res) => {
              if (res.data.success) {
                this.tabulator.replaceData();
                notify(res.data.message || this.SUCCESS_MESSAGE, "success","bottom-right");
              }
            })
            .catch((err) => {
              notify(err.response?.data?.message || this.ERROR_MESSAGE, "error","bottom-right");
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
            notify('Success', 'success','bottom-right');

            this.$router.push({
              name: "ClaimHSRegisterSchema",
              params: { id: this.claimId },
            });
          }
        })
        .catch((err) => {
          this.submitted = false
          console.log(err);
          notify('Success', 'success','bottom-right');

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
      let canUpdatePermission = hasPermission("HS_CLAIM_REGISTER", "UPDATE");
      if (!canUpdatePermission) return false;
      return approve_status == null;
    },
    canDelete(approve_status) {
      let canDeletePermission = hasPermission("HS_CLAIM_REGISTER", "DELETE");
      if (!canDeletePermission) return false;
      return approve_status === null;
    },

    canRevise(approve_status, schema_approved_status) {
      let canRevisePermission = hasPermission("HS_CLAIM_REGISTER", "REVISE");
      if (!canRevisePermission) return false;
      if (approve_status === null) return false;
      return schema_approved_status == "APV";
    },
    canSchema(approve_status, approve_schema) {
      let canUpdatePermission = hasPermission("HS_CLAIM_REGISTER", "UPDATE");
      if (approve_status != 'APV' || !canUpdatePermission) return false;
      return approve_schema == null;
    },
    hideDialog() {
      this.showDialog = false;
    },
    exportClaimReport(formData) {
      let fromDateParam = formData.from_date ?
       this.formatDate(formData.from_date) : '';

      let toDateParam = formData.to_date ?
       this.formatDate(formData.to_date) : '';

      location.href =
        "/hs/claim-report/type" +
        formData.report_type +
        "/from" +
        fromDateParam +
        "/to" +
        toDateParam;
    },

    openReportDialog() {
      this.showReportDialog = true
    },
    hideReportDialog() {
      this.showReportDialog = false
    },
    formatDate(date) {
      if (!date) return '';

      if (typeof date === 'string') {
        // If already a string, check if it's properly formatted
        if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
          return date;
        }
        // Try to parse the string to a Date object
        date = new Date(date);
      }

      // Ensure it's a valid date
      if (!(date instanceof Date) || isNaN(date)) {
        return '';
      }

      const year = date.getFullYear();
      // getMonth() returns 0-11, so add 1
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');

      return `${year}-${month}-${day}`;
    },
  },
};
</script>