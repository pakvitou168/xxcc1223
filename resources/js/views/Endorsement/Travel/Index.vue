<template>
  <div>
    <AddNew v-if="canNew" :addNew="addNew"></AddNew>
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter"/>
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable ref="tabulatorRef" :options="options"/>
      </div>
    </div>
  </div>
</template>

<script setup>
import {computed, createApp, onMounted, reactive, ref} from 'vue';
import {useRouter} from 'vue-router';
import AddNew from "@/components/Form/AddNew.vue";
import {hasPermission} from "@/services/auth.service";
import Search from "./Search.vue";
import Status from "@/components/DataTable/Status.vue";
import Action from "./Components/Action.vue";
import axios from 'axios';
import {useConfirm} from 'primevue/useconfirm';
import {useToast} from 'primevue/usetoast';

const router = useRouter();
const confirm = useConfirm();
const toast = useToast();
const tabulatorRef = ref(null);
const ERROR_MESSAGE = ref("Something went wrong!");
const SUCCESS_MESSAGE = ref("Success!");

// State
const addNew = reactive({
  label: "Travel Endorsements",
});

const tabulator = ref(null);

// Computed properties
const canNew = computed(() => {
  return hasPermission('TV_ENDORSEMENT', 'NEW');
});

// Methods
const canUpdate = (data) => {
  if (["APV", "REJ"].includes(data.origin_status)) return false;
  else if (
    data.endorsement.approved_status !== "PRG" &&
    ["ADD/DELETE", "GENERAL"].includes(data.endorsement_type)
  )
    return false;
  else return hasPermission('TV_ENDORSEMENT', 'UPD');
};

const canDelete = (data) => {
  return ["APV", "REJ"].includes(data.origin_status) && hasPermission('TV_ENDORSEMENT', 'DEL') ? false : true;
};

const handleDelete = (id) => {
  confirm.require({
    message: 'Do you want to delete this record?',
    header: 'Delete',
    icon: 'pi pi-exclamation-triangle text-red-500',
    rejectClass: 'p-button-secondary p-button-outlined',
    rejectLabel: 'No',
    acceptLabel: 'Yes',
    acceptClass: 'p-button-danger',
    accept: () => {
      axios
        .delete(`/travel/endorsements/${id}`)
        .then((response) => {
          if (response.data.success) {
            notify(response.data?.message || SUCCESS_MESSAGE.value , "success", "bottom-right");
            tabulator.value.replaceData();
          }
        })
        .catch((err) => {
          let error = err.response;
          notify(response.data?.message || ERROR_MESSAGE.value , "success", "bottom-right");
          if (error.status === 409) {
            tabulator.value.replaceData();
          }
        });
    },
  });
};

const setFilter = (e) => {
  tabulator.value.setFilter([
    {field: "document_no", type: "like", value: e.search},
    {field: "name_en", type: "like", value: e.search},
    {field: "total_premium", type: "like", value: e.search},
    {field: "version", type: "like", value: e.search},
    {field: "cycle", type: "like", value: e.search},
  ]);
};

// Define options
const options = reactive({
  ajaxURL: "/travel/endorsements",
  initialSort: [
    {column: "document_no", dir: "desc"},
  ],
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
      width: 125,
      hozAlign: "center",
      headerHozAlign: "center",
      formatter: (cell, formatterParams, onRendered) => {
        const rowData = cell.getRow().getData();
        const container = document.createElement("div");
        createApp(Status, {status: rowData.status}).mount(container);
        return container;
      }
    },
    {
      title: "Submit Status",
      field: "approved_status",
      width: 125,
      hozAlign: "center",
      headerHozAlign: "center",
      formatter: (cell, formatterParams, onRendered) => {
        const rowData = cell.getRow().getData();
        const container = document.createElement("div");
        createApp(Status, {status: rowData.approved_status}).mount(container);
        return container;
      }
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
      formatter: (cell, formatterParams, onRendered) => {
        let dataId = cell._cell.row.data.id;
        const rowData = cell.getRow().getData();
        const container = document.createElement("div");
        const eventHandlers = {
          onView: () => {
            router.push({
              name: "TravelEndorsementDetail",
              params: {id: dataId},
            });
          },
          onDelete: () => {
            handleDelete(dataId);
          },
          onEdit: () => {
            router.push({
              name: "TravelEndorsementEdit",
              params: {
                id: dataId,
              },
            });
          },
        };
        createApp(Action, {rowData: rowData, events: eventHandlers}).mount(container);
        return container;
      },
    },
  ],
});

// Handle component refs
onMounted(() => {
  if (tabulatorRef.value) {
    tabulator.value = tabulatorRef.value;
  }
});

// Expose necessary methods/variables for template
defineExpose({
  setFilter,
  handleDelete,
  canUpdate,
  canDelete
});
</script>