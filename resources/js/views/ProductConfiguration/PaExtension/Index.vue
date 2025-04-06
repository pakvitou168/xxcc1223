<template>
    <div>
      <AddNew :canAddNew="true" :addNew="addNew" />
      <div class="intro-y box p-5 mt-5">
        <Search @setFilter="setFilter"/>
        <div class="overflow-x-auto scrollbar-hidden">
          <DataTable @ref="(ref) => (tabulator = ref)" :options="options" />
        </div>
      </div>
    </div>
  </template>

  <script>
  import AddNew from '../../../components/Form/AddNew.vue'
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
        functionCode: 'PA_EXTENSION_OPTION',
        addNew: {
          label: 'Extension Option',
          name: 'Extension Option',
          prefix: 'Add New',
          linkAddNew: '/product-configuration/pa-extensions/new',
        },
        tabulator: null,
        options: {
          persistenceID: 'extension-options-table',
          ajaxURL: "/extension-options",
          columns: [
            {
              title: 'ID',
              field: 'id',
              width: 90,
            },
            {
              title: 'Type',
              field: 'type',
              sorter: 'string',
            },
            {
              title: 'Code',
              field: 'code',
              sorter: 'string',
            },
            {
              title: 'Name',
              field: 'name',
              sorter: 'string',
            },
            {
              title: 'Description',
              sorter: "string",
              field: 'description',
              hozAlign:"left",
              headerHozAlign: "left",
            },
            {
              title: 'Actions',
              field: 'actions',
              headerSort:false,
              hozAlign:"center",
              headerHozAlign: "center",
              formatter:"html",
              width: 120,
              cellClick: (e, cell) => {
                const viewButton = cell._cell.element.querySelector("a.view svg");
                const editButton = cell._cell.element.querySelector("a.edit svg");
                const deleteButton = cell._cell.element.querySelector("a.delete svg");
                let dataId = cell._cell.row.data.id;

                if (editButton.contains(e.target)) {
                    this.$router.push({
                    name: "ProductConditionUpdate",
                    params: {
                        id: dataId,
                    },
                    });
                } else if (deleteButton.contains(e.target)) {
                    this.handleDelete(dataId);
                } else if (viewButton.contains(e.target)) {
                    this.$router.push({
                    name: "ProductConditionDetail",
                    params: {
                        id: dataId,
                    },
                    });
                }
              }
            },
          ],
        },
      }
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
              .delete(`/extension-options/${id}`)
              .then((response) => {
                if (response.data.success) {
                  notify(response.data.message, 'success', 'bottom-right')
                  this.tabulator.replaceData();
                }
              })
              .catch(() => {
                notify('Error occurred', 'error', 'bottom-right')
              });
          },
        });
      },
      setFilter: _.debounce(function (e) {
        this.tabulator.setFilter([
          //searchbar
          { field: "type", type: "like", value: e.search },
          { field: "code", type: "like", value: e.search },
          { field: "name", type: "like", value: e.search },
          { field: "description", type: "like", value: e.search }
        ]);
      }, 500),
    },
  };
  </script>
