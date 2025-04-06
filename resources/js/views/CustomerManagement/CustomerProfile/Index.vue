Detail.vue<template>
  <div>
    <div class="intro-y box p-5 mt-5">
      <Search @setFilter="setFilter" />
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options" />
      </div>
    </div>
  </div>
</template>

<script>
import Search from "./Search.vue";
import { getActionButtons } from '@/components/DataTable/actionButton'
import { hasPermission } from '@/services/auth.service'

export default {
  components: {
    Search,
  },
  data() {
    return {
      tabulator: null,
      options: {
        persistenceID: "customers-table",
        ajaxURL: "/customer-profiles",
        columns: [
          {
            title: "Customer ID",
            field: "customer_no",
          },
          {
            title: "Insured Name",
            field: "name_en",
            headerSort: false,
          },
          {
            title: "Address",
            field: "address",
            headerSort: false
          },
          {
            title: "Actions",
            field: "actions",
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            mutator: (_, row) =>
                getActionButtons(hasPermission("CUSTOMER_PROFILE", "VIEW"), false, false, false),
            cellClick: (e, cell) => {
              let dataId = cell._cell.row.data.id;
              if (cell._cell.element.querySelector("a.view svg")?.contains(e.target)) {
                this.$router.push({
                  name: "CustomerProfileDetail",
                  params: { id: dataId },
                });
              }
            },
          },
        ],
      },
    };
  },
  methods: {
    setFilter: _.debounce(function (e) {
      this.tabulator.setFilter([
        // Search
        { field: "customer_no", type: "like", value: e.search },
        { field: "name_en", type: "like", value: e.search },
      ]);
    }, 500),

    toastMessage(msg, type, position = "bottom") {
      notify(msg, type, position);
    },
  },
};
</script>
