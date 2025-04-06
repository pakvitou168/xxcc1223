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
import Search from './Search.vue'
import AddNew from '@/components/Form/AddNew.vue'
import UserPermissions from '../../../mixins/UserPermissions'

export default {
  mixins: [UserPermissions],

  components: {
    Search,
    AddNew,
  },
  data() {
    return {
      functionCode: "COMP_FRM_EXPRESSION",
      addNew: {
        label: "Component Formula Expression",
        name: "Formula Expression",
        prefix: "Add New",
        linkAddNew: "/product-configuration/comp-frm-expr/new",
      },
      tabulator: null,
      options: {
        ajaxURL: "/comp_form_expression",
        columns: [
          {
            title: "Product",
            field: "product_code",
            sorter: "string",
            width: 180,
            mutator: (_, data) =>
              `${data.product?.code} - ${data.product?.name}`,
          },

          {
            title: "Cover Code",
            field: "component_code",
          },
          {
            title: "Formula Code",
            field: "formula_code",
          },
          {
            title: "Calculate Option",
            field: "calc_option",
          },
          {
            title: "Expr Line",
            field: "expr_line",
            sorter: "number",
            width: 140,
            hozAlign: "center",
            headerHozAlign: "center",
          },
          {
            title: "Status",
            field: "status",
            sorter: "string",
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

              if (
                cell._cell.element
                  .querySelector("a.edit svg")
                  .contains(e.target)
              ) {
                this.$router.push({
                  name: "FrmExpUpdate",
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
                  name: "FrmExpExpDetail",
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
    handleDelete(id) {
      this.$confirm.require({
        message: "Do you want to delete this record?",
        header: "Delete",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios
            .delete(`/comp_form_expression/${id}`)
            .then((response) => {
              if (response.data.success) {
                // refresh table
                notify(response.data.message, "success", 'bottom-right');
                this.tabulator.replaceData();
              }
            })
            .catch(() => notify("Error", "error",'bottom-right'));
        },
      });
    },
    setFilter(e) {
      this.tabulator.setFilter([
        { field: "formula_code", type: "like", value: e.search },
        [
          { field: "product_code", type: "=", value: e.product },
          { field: "component_code", type: "=", value: e.cover },
          { field: "calc_option", type: "=", value: e.cal_option },
          { field: "status", type: "=", value: e.status },
        ],
      ]);
    },
  },
};
</script>
