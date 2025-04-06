<template>
    <div>
        <AddNew :canAddNew="canAddNew" :addNew="addNew" />
        <div class="intro-y box p-5 mt-5">
            <SearchBar @setFilter="setFilter"/>
            <div class="overflow-x-auto scrollbar-hidden">
                <DataTable @ref="ref => tabulator = ref" :options="options" />
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
            functionCode: 'BUSINESS_CHANNEL',
            addNew: {
                label: "Business Channels",
                name: "Business Channel",
                prefix: "Add New",
                linkAddNew: "/business-management/business-channels/new"
            },
            tabulator: null,
            options: {
                persistenceID: 'business-channels-table',
                ajaxURL: "/business-channels",
                columns: [
                    {
                        title: "Business Code",
                        field: "business_code",
                        sorter: "string",
                        width: 160,
                    },
                    {
                        title: "Business Category",
                        field: "business_category_id",
                        headerSort: false,
                        mutator: (_, row) => {
                            return row.business_category ? row.business_category.name : ''
                        },
                        widthGrow: 1,
                        minWidth: 170,
                    },
                    {
                        title: "Full Name",
                        field: "full_name",
                        sorter: "string",
                        widthGrow: 1,
                        minWidth: 170,
                    },
                    {
                        title: "Sale Channel",
                        field: "sale_channel_name",
                        sorter: "string",
                        widthGrow: 1,
                        minWidth: 170,
                        headerSort: false,
                    },
                    {
                        title: "Business Handler",
                        field: "handler_code",
                        headerSort: false,
                        mutator: (_, row) => {
                            return row.business_handler ? row.business_handler.name : ''
                        },
                        widthGrow: 1,
                        minWidth: 170,
                    },
                    {
                        title: "Tax & Fee (%)",
                        field: "premium_tax_fee_rate",
                        sorter: "string",
                        hozAlign: "right",
                        width: 145
                    },
                    {
                        title: "WHT (%)",
                        field: "witholding_tax_rate",
                        sorter: "string",
                        hozAlign: "right",
                        width: 110
                    },
                    {
                        title: "Status",
                        field: "status",
                        headerSort: false,
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
                        formatter: "html"
                    },
                    {
                        title: "Actions",
                        field: "actions",
                        width: 105,
                        headerSort: false,
                        headerHozAlign: "center",
                        formatter: "html",
                        cellClick: (e, cell) => {
                            const dataId = cell._cell.row.data.id;
                            const viewButton = cell._cell.element.querySelector("a.view svg");
                            const editButton = cell._cell.element.querySelector("a.edit svg");
                            const deleteButton = cell._cell.element.querySelector("a.delete svg");

                            if (viewButton.contains(e.target)) {
                                this.$router.push("/business-management/business-channels/" + dataId);
                            } else if (editButton.contains(e.target)) {
                                this.$router.push("/business-management/business-channels/" + dataId + "/edit");
                            } else if (deleteButton.contains(e.target)) {
                                this.handleDelete(dataId);
                            }
                        }
                    }
                ]
            }
        };
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
                    axios.delete(`/business-channels/${id}`).then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success', 'bottom-right')
                            this.tabulator.replaceData()
                        }
                    }).catch(err => {
                        notify('Error', 'error', 'bottom-right')
                    })
                },
            });
        },
        setFilter: _.debounce(function (e) {
            this.tabulator.setFilter([
                {field: 'business_code', type: 'like', value: e.target.value},
                {field: 'full_name', type: 'like', value: e.target.value},
                {field: 'sale_channel', type: 'like', value: e.target.value},
                {field: 'commission_rate', type: 'like', value: e.target.value},
                {field: 'contact_phone', type: 'like', value: e.target.value},
                {field: 'contact_email', type: 'like', value: e.target.value},
                {field: 'contact_address', type: 'like', value: e.target.value},
                {field: 'premium_tax_fee_rate', type: 'like', value: e.target.value / 100},
                {field: 'witholding_tax_rate', type: 'like', value: e.target.value / 100},
            ])
        }, 500),
    }
};
</script>
