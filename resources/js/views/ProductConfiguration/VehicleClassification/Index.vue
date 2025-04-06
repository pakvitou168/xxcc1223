<template>
    <div>
        <AddNew :canAddNew="canAddNew" :addNew="addNew" />
        <div class="intro-y box p-5 mt-5">
            <SearchBar @setFilter="setFilter"/>
            <div class="overflow-x-auto scrollbar-hidden">
                <DataTable @ref="tabulator = $event" :options="options" />
            </div>
        </div>
    </div>
</template>

<script>

import AddNew from "@/components/Form/AddNew.vue";
import SearchBar from '@/components/DataTable/SearchBar.vue'
import UserPermissions from '../../../mixins/UserPermissions'

export default {
    mixins: [UserPermissions],

    components: {
        AddNew,
        SearchBar,
    },

    data() {
        return {
            functionCode: 'VEHICLE_CLASSIFICATION',
            addNew: {
                label: "Vehicle Classifications",
                name: "Vehicle Classification",
                prefix: "Add New",
                linkAddNew: "/product-configuration/vehicle-classifications/new",
            },
            tabulator: null,
            options: {
                persistenceID: "vehicle-classifications-table",
                ajaxURL: "/vehicle-classifications",
                columns: [
                    {
                        title: "Vehicle Classification Code",
                        field: "code",
                        sorter: "string",
                    },
                    {
                        title: "Description",
                        field: "description",
                        sorter: "string",
                    },
                    {
                        title: "Surcharge (%)",
                        field: "surcharge",
                        sorter: "number",
                    },
                    {
                        title: "Status",
                        field: "status",
                        width: 120,
                        hozAlign: "center",
                        headerHozAlign: "center",
                        formatter: "html",
                        headerSort: false,
                        mutator: value => {
                            if (value == 'ACT') {
                                return '<span class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full">Active</span>'
                            } else if (value == 'DEL') {
                                return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Deleted</span>'
                            }
                            return ''
                        },
                    },
                    {
                        title: "Actions",
                        field: "actions",
                        width: 105,
                        headerSort: false,
                        headerHozAlign: "center",
                        formatter: "html",
                        cellClick: (e, cell)=> {
                            let dataId = cell._cell.row.data.id;

                            if (cell._cell.element.querySelector("a.edit svg").contains(e.target)) {
                                this.$router.push("/product-configuration/vehicle-classifications/" + dataId + "/edit");
                            } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                                this.$router.push("/product-configuration/vehicle-classifications/" + dataId + "");
                            } else if (cell._cell.element.querySelector("a.delete svg").contains(e.target)) {
                                this.handleDelete(dataId);
                            }
                        },
                    },
                ]
            },
        }
    },

    methods: {
        handleDelete(id) {
            this.$confirm.require({
                message: 'Do you want to delete this record?',
                header: 'Delete',
                icon: 'pi pi-info-circle',
                acceptClass: "p-button-danger",
                blockScroll: false,
                accept: () => {
                    axios.delete(`/vehicle-classifications/${id}`).then(response => {
                        if (response.data.success) {
                            // refresh table
                            notify(response.data.message, 'success','bottom-right')
                            this.tabulator.replaceData()
                        }
                    }).catch(err => {
                        notify('Error', 'Error','bottom-right')
                    })
                },
            });
        },

        setFilter: _.debounce(function (e) {
            this.tabulator.setFilter([
                // Search
                {field: 'code', type: 'like', value: e.target.value},
                {field: 'description', type: 'like', value: e.target.value},
                {field: 'surcharge', type: 'like', value: e.target.value},
            ])      
        }, 500),
    },
}
</script>