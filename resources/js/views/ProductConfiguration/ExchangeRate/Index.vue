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
import eventBus from '../../../eventBus'

export default {
    mixins: [UserPermissions],

    components: {
        AddNew,
        Search,
    },

    data() {
        return {
            functionCode: 'EXCHANGE_RATE',
            addNew: {
                label: "Exchange Rates",
                name: "Exchange Rate",
                prefix: "Add New",
                linkAddNew: "/exchange-rate/new",
            },
            tabulator: null,
            options: {
                persistenceID: "exchange-rates-table",
                ajaxURL: "/exchange-rates",
                columns: [
                    {
                        title: "Branch Code",
                        field: "branch_code",
                        sorter: "string",
                        width: 205,
                        mutator: (_, row) => {
                            if (row.branch) return row.branch.name
                        },
                    },
                    {
                        title: "Rate Date",
                        field: "rate_date",
                        sorter: "string",
                    },
                    {
                        title: "From Currency",
                        field: "ccy1",
                        sorter: "string",
                    },
                    {
                        title: "To Currency",
                        field: "ccy2",
                        sorter: "string",
                    },
                    {
                        title: "Rate Type",
                        field: "rate_type",
                        sorter: "string",
                    },
                    {
                        title: "Buy Rate",
                        field: "buy_rate",
                        sorter: "number",
                    },
                    {
                        title: "Sale Rate",
                        field: "sale_rate",
                    },
                    {
                        title: "Middle Rate",
                        field: "mid_rate",
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
                            } else if (value == 'REJ') {
                                return '<span class="text-xs px-2 bg-red-500 text-white mr-1 py-1 rounded-full">Rejected</span>'
                            } else if (value == 'PND') {
                                return '<span class="text-xs px-2 bg-yellow-500 text-white mr-1 py-1 rounded-full">Pending</span>'
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
                                this.$router.push("/exchange-rate/" + dataId + "/edit");
                            } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                                this.$router.push("/exchange-rate/" + dataId);
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
                rejectClass: "p-button-secondary p-button-outlined",
                blockScroll: false,
                rejectLabel: 'Cancel',
                acceptLabel: 'Delete',
                accept: () => {
                    axios.delete(`/exchange-rates/${id}`).then(response => {
                        if (response.data.success) {
                            // refresh table
                            eventBus.$emit('exchangeRateUpdated')
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
                {field: 'branch.code', type: 'like', value: e.search},
                {field: 'branch.name', type: 'like', value: e.search},
                // Filter
                [
                    {field: 'status', type: '=', value: e.status},
                ]
            ])
        }, 500),
    },
}
</script>
