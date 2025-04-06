<template>
    <div>
        <AddNew :canAddNew="true" :addNew="addNew" />
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
import ProductConditionService from '@/services/product_config/product_condition_rating.service'

export default {
    mixins: [UserPermissions],
    components: {
      AddNew,
      Search,
    },
    data(){
        return {
            functionCode: 'POLICY_CONDITION_RATING',
            addNew: {
                label: "Product Condition Rating",
                prefix: "Add New",
                linkAddNew: "/product-configuration/product-condition-rating/new",
            },
            issued_date_from: '',
            issued_date_to: '',
            canExport: true,
            tabulator: null,
            options: {
                persistenceID: 'autos-table',
                ajaxURL: "/product-config/product-condition-rating",
                columns: [
                    {
                        title: "Product Code",
                        field: "product_code",
                        sorter: "string",
                        width: 130,
                    },
                    {
                        title: "Code",
                        field: "code",
                        sorter: "string",
                        width: 230,
                    },
                    {
                        title: "Description",
                        field: "description",
                        sorter: "string",
                        width: 200
                    },
                    {
                        title: "Condition Expire",
                        field: "cond_expr",
                        sorter: "string",
                        headerHozAlign: 'left',
                    },
                    {
                        title: "Value",
                        field: "value",
                        sorter: "string",
                        width: 150
                    },
                    {
                        title: "Created At",
                        field: "created_at",
                        sorter: "string",
                        width: 150,
                        mutator: (value) => {
                            if (value)
                                return moment(value).format("DD/MM/YYYY");
                            return "";
                        },
                    },
                    {
                        title: "Status",
                        field: "status",
                        width: 120,
                        mutator: value => {
                            if (value == 'ACT') {
                                return '<span class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full">Active</span>'
                            } else if (value == 'DEL') {
                                return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Deleted</span>'
                            }
                            return ''
                        },
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
                        cellClick: (e, cell)=> {
                            const editButton = cell._cell.element.querySelector("a.edit svg");
                            const deleteButton = cell._cell.element.querySelector("a.delete svg");
                            let dataId = cell._cell.row.data.id;

                            if (editButton.contains(e.target)) {
                                this.$router.push(
                                "/product-configuration/product-condition-rating/" + dataId + "/edit"
                                );
                            } else if (
                                cell._cell.element.querySelector("a.view svg")
                                ?.contains(e.target)
                            ) {
                                this.$router.push({
                                    name: "ProductConditionDetail",
                                    params: { id: dataId }
                                });
                            } else if (deleteButton.contains(e.target)) {
                                this.handleDelete(dataId);
                            }
                        },
                    },
                ]
            }
        }
    },
    methods: {
        // setFilter: _.debounce(function (e) {
        //     this.tabulator.setFilter([
        //         // Search
        //         {field: 'code', type: 'like', value: e.search},
        //         {field: 'product_code', type: 'like', value: e.search},
        //         {field: 'description', type: 'like', value: e.search},
        //         // Filter
        //         // [
        //         //     {field: 'status', type: '=', value: e.status},
        //         // ]
        //     ])
        // }, 500),
        setFilter: _.debounce(function (e) {
          if (this.tabulator && typeof this.tabulator.setFilter === 'function') {
            this.tabulator.setFilter([
              {field: 'code', type: 'like', value: e.search},
              { field: 'product_code', type: 'like', value: e.search },
              { field: 'description', type: 'like', value: e.search },
              // [
              //   { field: 'issued_at', type: '>=', value: e.issued_date_from },
              //   { field: 'issued_at', type: '<=', value: e.issued_date_to },
              // ],
            ])
          }
        }, 500),
        handleDelete(id) {
            this.$confirm.require({
                message: 'Do you want to delete this record?',
                header: 'Delete',
                icon: 'pi pi-info-circle',
                acceptClass: "p-button-danger px-2 py-2",
                rejectClass: "p-button-secondary",
                blockScroll: false,
                acceptLabel: 'Delete',
                rejectLabel: 'Cancel',
                accept: () => {
                    ProductConditionService.delete(id).then(response => {
                        if (response.data.success) {
                        // refresh table
                        this.toastMessage(response.data.message, 'Success')
                        this.tabulator.replaceData()
                        }
                    }).catch(err => {
                        this.toastMessage('Error', 'Error')
                    })
                },
            });
        },
        toastMessage(msg, type, position = "bottom") {
            this.$notify(
                {
                    group: position,
                    title: type,
                    text: msg,
                },
                4000
            );
        },
    }
}
</script>