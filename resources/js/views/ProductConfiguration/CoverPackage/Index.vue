<template>
    <div>
        <AddNew :canAddNew="canAddNew" :addNew="addNew" />
        <div class="intro-y box p-5 mt-5">
            <Search @setFilter="setFilter"/>
            <div class="overflow-x-auto scrollbar-hidden">
                <DataTable @ref="tabulator = $event" :options="options" />
            </div>
        </div>
    </div>
</template>

<script>

import AddNew from "@/components/Form/AddNew.vue"
import Search from './Search.vue'
import UserPermissions from '../../../mixins/UserPermissions'

export default {
    mixins: [UserPermissions],

    components: {
        AddNew,
        Search,
    },

    data() {
        return {
            functionCode: 'COVER_PACKAGE',
            addNew: {
                label: "Cover Packages",
                name: "Cover Package",
                prefix: "Add New",
                linkAddNew: "/product-configuration/cover-packages/new",
            },
            tabulator: null,
            options: {
                persistenceID: "cover-packages-table",
                ajaxURL: "/cover-packages",
                columns: [
                    {
                        title: "Cover Package Name",
                        field: "name",
                        sorter: "string",
                        width: 205,
                    },
                    {
                        title: "Description",
                        field: "description",
                        sorter: "string",
                    },
                    {
                        title: "Product",
                        field: "product",
                        headerSort: false,
                        width: 160,
                        mutator: (_, row) => {
                            if (row.product)
                                return `${row.product_code} - ${row.product?.name}`
                        },
                    },
                    {
                        title: "Covers",
                        field: "covers",
                        headerSort: false,
                        mutator: (_, row) => {
                            if (row.cover_package_components)
                                return row.cover_package_components.map(item => item.comp_code).join(', ')
                        },
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
                                this.$router.push("/product-configuration/cover-packages/" + dataId + "/edit");
                            } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                                this.$router.push("/product-configuration/cover-packages/" + dataId + "");
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
                    axios.delete(`/cover-packages/${id}`).then(response => {
                        if (response.data.success) {
                            // refresh table
                            notify(response.data.message, 'success','bottom-right')
                            this.tabulator.replaceData()
                        }
                    }).catch(err => {
                        notify('Error', 'error','bottom-right')
                    })
                },
            });
        },

        setFilter: _.debounce(function (e) {
            this.tabulator.setFilter([
                // Search
                {field: 'name', type: 'like', value: e.search},
                {field: 'description', type: 'like', value: e.search},
                {field: 'product_code', type: 'like', value: e.search},
                {field: 'product.name', type: 'like', value: e.search},
                // Filter
                [
                    {field: 'product_code', type: '=', value: e.product},
                ]
            ])
        }, 500),
    },
}
</script>
