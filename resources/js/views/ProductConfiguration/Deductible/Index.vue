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
import AddNew from '@/components/Form/AddNew.vue'
import Search from './Search.vue'
import UserPermissions from '../../../mixins/UserPermissions'

export default {
  mixins: [UserPermissions],

  components: {
    Search,
    AddNew,
  },
  data() {
    return {
      functionCode: "DEDUCTIBLE",
      addNew: {
        label: "Deductibles",
        name: "Deductible",
        prefix: "Add New",
        linkAddNew: "/product-configuration/deductibles/new",
      },
      tabulator: null,
      options: {
        ajaxURL: "/deductibles",
        columns: [
          {
            title: "Product",
            field: "product_code",
            mutator: (_, row) => `${row.product_code} - ${row.product?.name}`,
          },
          {
            title: "Cover",
            field: "comp_code",
            sorter: "string",
            mutator: (_, data) => data.cover?.name,
          },
          {
            title: "Label",
            field: "label",
          },
          {
            title: "Value",
            field: "value",
          },
          {
            title: "Has Condition",
            field: "cond_type",
            mutator: this.getCondType,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
            headerSort: false,
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

              if (
                cell._cell.element
                  .querySelector("a.edit svg")
                  .contains(e.target)
              ) {
                this.$router.push({
                  name: "DeductibleUpdate",
                  params: {
                    id: dataId,
                  },
                });
              } else if (
                cell._cell.element
                  .querySelector("a.view svg")
                  .contains(e.target)
              ) {
                this.$router.push({
                  name: "DeductibleDetail",
                  params: {
                    id: dataId,
                  },
                });
              } else if (
                cell._cell.element
                  .querySelector("a.delete svg")
                  .contains(e.target)
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
    getCondType(value) {
      if (value == "Y") {
        return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="check-circle" role="presentation" class="text-green-500 fill-current text-success mx-auto">
            <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"></path>'
          </svg>
        `;
      } else if (value == "N") {
        return `
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="x-circle" role="presentation" class="text-red-500 fill-current text-danger mx-auto">
            <path d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"></path>
          </svg>
        `;
      }
      return "";
    },
    handleDelete(id) {
      this.$confirm.require({
        message: "Do you want to delete this record?",
        header: "Delete",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios
            .delete(`/deductibles/${id}`)
            .then((response) => {
              if (response.data.success) {
                // refresh table
                notify(response.data.message, "success", "bottom-right");
                this.tabulator.replaceData();
              }
            })
            .catch(() => notify("Error", "error", "bottom-right"));
        },
      });
    },
    setFilter(e) {
      this.tabulator.setFilter([
        //search
        { field: "label", type: "like", value: e.search },
        { field: "value", type: "like", value: e.search },
        { field: "product_code", type: "like", value: e.search },
        { field: "product.name", type: "like", value: e.search },
        //filter
        [
          { field: "product_code", type: "=", value: e.product },
          { field: "comp_code", type: "=", value: e.cover },
          { field: "cond_type", type: "=", value: e.condition },
        ],
      ]);
    },
  },
};
</script>
