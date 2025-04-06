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
import Search from "@/components/DataTable/SearchBar.vue";
import DriverLicenseService from "@/services/claim/driver_license.service";
import { hasPermission } from "@/services/auth.service";
import { getActionButtons } from "@/components/DataTable/actionButton";

export default {
  components: {
    AddNew,
    Search,
  },

  data() {
    return {
      addNew: {
        label: "Driver Licenses",
        name: "Driver License",
        prefix: "Add New",
        linkAddNew: "/claim-configuration/driver-licenses/new",
      },
      canAddNew: hasPermission("DRIVER_LICENSE", "NEW"),
      tabulator: null,
      options: {
        ajaxURL: "/claim-driver-licenses",
        columns: [
          {
            title: "Name",
            field: "name",
          },
          {
            title: "Gender",
            field: "gender",
          },
          {
            title: "Age",
            field: "driver_age",
          },
          {
            title: "Occupation",
            field: "occupation",
          },
          {
            title: "License No.",
            field: "license_no",
          },
          {
            title: "Actions",
            field: "actions",
            width: 105,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            mutator: () =>
              getActionButtons(
                hasPermission("DRIVER_LICENSE", "VIEW"),
                hasPermission("DRIVER_LICENSE", "UPDATE"),
                hasPermission("DRIVER_LICENSE", "DELETE")
              ),
            cellClick: (e, cell) => {
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector("a.edit svg")?.contains(e.target)) {
                this.$router.push({ name: "DriverLicenseEdit", params: { id: dataId } });
              } else if (
                cell._cell.element.querySelector("a.view svg")?.contains(e.target)
              ) {
                this.$router.push({
                  name: "DriverLicenseDetail",
                  params: { id: dataId },
                });
              } else if (
                cell._cell.element.querySelector("a.delete svg")?.contains(e.target)
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
          DriverLicenseService.delete(id)
            .then((res) => {
              if (res.data.success) {
                this.tabulator.replaceData();
                notify(res.data?.message, 'success', 'bottom-right');
              }
            })
            .catch((err) => {
              notify('Validation Error', 'error', 'bottom-right');
            });
        },
      });
    },

    setFilter(e) {
      this.tabulator.setFilter([
        { field: "name", type: "like", value: e.target.value },
        { field: "gender", type: "like", value: e.target.value },
        { field: "driver_age", type: "like", value: e.target.value },
        { field: "occupation", type: "like", value: e.target.value },
        { field: "license_no", type: "like", value: e.target.value },
      ]);
    },
  },
};
</script>
