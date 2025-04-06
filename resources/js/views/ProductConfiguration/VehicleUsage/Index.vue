<template>
    <div>
        <AddNew :canAddNew="canAddNew" :addNew="addNew" />
        <div class="intro-y box p-5 mt-5">
            <SearchBar @setFilter="setFilter" />
            <div class="overflow-x-auto scrollbar-hidden">
                <DataTable @ref="ref => tabulator = ref" :options="options" />
            </div>
        </div>
    </div>
</template>

<script>

import AddNew from "@/components/Form/AddNew.vue"
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
            functionCode: 'VEHICLE_USAGE',
            addNew: {
                label: "Vehicle Usages",
                name: "Vehicle Usage",
                prefix: "Add New",
                linkAddNew: "/product-configuration/vehicle-usages/new",
            },
            tabulator: null,
            options: {
                persistenceID: "vehicle-usages-table",
                ajaxURL: "/vehicle-usages",
                columns: [
                    {
                        title: "Product",
                        field: "product_code",
                        headerSort: false,
                        mutator: (_, row) => `${row.product_code} - ${row.product?.name}`
                    },
                    {
                        title: "Name",
                        field: "name",
                        sorter: "string",
                    },
                    {
                        title: "Description",
                        field: "description",
                        sorter: "string",
                    },
                    {
                        title: "Actions",
                        field: "actions",
                        width: 100,
                        headerSort: false,
                        headerHozAlign: "center",
                        formatter: "html",
                        cellClick: (e, cell)=> {
                            let dataId = cell._cell.row.data.id;

                            if (cell._cell.element.querySelector("a.edit svg").contains(e.target)) {
                                this.$router.push({ name: 'VehicleUsageUpdate', params: { id: dataId }})
                            } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                                this.$router.push({ name: 'VehicleUsageDetail', params: { id: dataId }})
                            } else if (cell._cell.element.querySelector("a.delete svg").contains(e.target)) {
                                this.handleDelete(dataId);
                            }
                        },
                    },
                ],
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
                    axios.delete(`/vehicle-usages/${id}`).then(response => {
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
                {field: 'product_code', type: 'like', value: e.target.value},
                {field: 'product.name', type: 'like', value: e.target.value},
                {field: 'name', type: 'like', value: e.target.value},
            ])
        }, 500),
    },
}
</script>