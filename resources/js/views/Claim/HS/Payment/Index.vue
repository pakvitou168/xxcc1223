<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew" />
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter" />
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable ref="tabulator" :options="options" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { createApp, ref, reactive } from 'vue';
import AddNew from "@/components/Form/AddNew.vue";
import Search from "./Components/Search.vue";
import { hasPermission } from "@/services/auth.service";
import { useConfirm } from "primevue/useconfirm";
import { useRouter } from 'vue-router';
import moment from 'moment';
import Status from "@/components/DataTable/Status.vue";
import Action from "./Components/Action.vue"
const router = useRouter();
const ERROR_MSG = ref("Something went wrong!");
const SUCCESS_MSG = ref("Success!");
const addNew = reactive({
  label: "Payments",
  prefix: "New",
  linkAddNew: "/claim/hs/payments/new",
});
const canAddNew = hasPermission("HS_CLAIM_PAYMENT", "NEW");
const tabulator = ref(null)
const options = ref({
  ajaxURL: "/hs/claim-payment/payments",
  initialSort: [
    { column: "claim_no", dir: "desc" },
  ],
  columns: [
    {
      title: "Claims No.",
      field: "claim_no",
      width: 160,
    },
    {
      title: "Policy No.",
      field: "policy_no",
      width: 160,
    },
    {
      title: "Claimant Name",
      field: "claimant_name",
      minWidth: 160,
    },
    {
      title: "Payee Name",
      field: "payee_name_en",
      minWidth: 160,
    },
    {
      title: "Amount",
      field: "amount",
      width: 130,
    },
    {
      title: "Requested Date",
      field: "requested_date",
      width: 137,
      mutator: (value) => {
        if (value)
          return moment(value).format("DD/MM/YYYY");
        return "";
      },
    },
    {
      title: "Cause of Loss Disability",
      field: "cause_of_loss_disability",
      minWidth: 180,
    },
    {
      title: "Approve Status",
      field: "approve_status",
      headerSort: false,
      headerHozAlign: "center",
      hozAlign: "center",
      width: 160,
      formatter: (cell, formatterParams, onRendered) => {
        const rowData = cell.getRow().getData();
        const container = document.createElement("div");
        createApp(Status, { status: rowData.approved_status??'PND' }).mount(container);
        return container;
      }
    },
    {
      title: "Comment",
      field: "approved_cmt",
      headerSort: false,
      minWidth: 160,
    },
    {
      title: "Actions",
      field: "actions",
      width: 105,
      headerSort: false,
      headerHozAlign: "center",
      formatter: (cell, formatterParams, onRendered) => {
        const rowData = cell.getRow().getData();
        const container = document.createElement("div");
        const eventHandlers = {
          onView: () => {
            router.push({
              name: "ClaimHSPaymentDetail",
              params: { id: rowData.txn_id},
            });
          },
          onEdit: () => {
            router.push({
              name: "ClaimHSPaymentEdit",
              params: { id: rowData.txn_id },
            });
          },
        };
        createApp(Action, { rowData: rowData, events: eventHandlers }).mount(container);
        return container;
      },
    },
  ],
});

// Methods
const setFilter = _.debounce((filter) => {
  tabulator.value?.setFilter([
    { field: 'payment_no', type: 'like', value: filter.search },
    { field: 'claim_no', type: 'like', value: filter.search },
    { field: 'approved_cmt', type: 'like', value: filter.search },
  ])
}, 500)

const openFormDialog = () => {
  dialogRef.value?.toggleDialog()
}

</script>