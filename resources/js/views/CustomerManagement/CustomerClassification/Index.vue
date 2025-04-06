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
import Search from "./Search.vue"
import UserPermissions from '../../../mixins/UserPermissions'

export default {
    mixins: [UserPermissions],
    components: {
        AddNew,
        Search,
    },
    data() {
        return {
            functionCode: 'CUSTOMER_CLASSIFICATION',
            addNew: {
                label: "Businesses / Occupations",
                name: "Business / Occupation",
                prefix: "Add New",
                linkAddNew: "/customer-management/customer-classifications/new"
            },
            tabulator: null,
            options: {
                ajaxURL: "/customer-classifications",
                columns: [
                    {
                        title: "Group Code",
                        field: "group_code",
                        width: 140,
                    },
                    {
                        title: "Description (English)",
                        field: "description",
                    },
                    {
                        title: "Description (Khmer)",
                        field: "description_kh",
                    },
                    {
                        title: "Description (Chinese)",
                        field: "description_zh",
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
                            let dataId = cell._cell.row.data.cust_classification;

                            if (cell._cell.element.querySelector("a.edit svg").contains(e.target)) {
                                this.$router.push(`/customer-management/customer-classifications/${dataId}/edit`)
                            } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                                this.$router.push(`/customer-management/customer-classifications/${dataId}`)
                            } else if (cell._cell.element.querySelector("a.delete svg").contains(e.target)) {
                                this.handleDelete(dataId)
                            }
                        },
                    },
                ]
            }
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
                    axios.delete(`/customer-classifications/${id}`).then(response => {
                        if (response.data.success) {
                        // refresh table
                        notify(response.data.message, 'success','bottom-right')
                        this.tabulator.replaceData()
                        }
                    }).catch(() => notify('Error', 'error','bottom-right'))
                },
            });
        },
        setFilter(e) {
            this.tabulator.setFilter([
                {field: 'description', type: 'like', value: e.search},
                {field: 'description_kh', type: 'like', value: e.search},
                {field: 'description_zh', type: 'like', value: e.search},
                [
                    {field: 'group_code', type: '=', value: e.groupCode},
                ]
            ])
        }
    }
}
</script>
