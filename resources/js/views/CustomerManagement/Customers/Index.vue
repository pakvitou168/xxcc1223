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
        AddNew,
        Search,
    },
    data() {
        return {
            functionCode: 'CUSTOMER',
            addNew: {
                label: 'Customers',
                name: 'Customer',
                prefix: 'Add New',
                linkAddNew: '/customer-management/customer/new',
            },
            tabulator: null,
            options: {
                persistenceID: "customers-table",
                ajaxURL: '/customers',
                columns: [
                {
                    title: 'Customer No.',
                    field: 'customer_no',
                },
                {
                    title: 'Customer Type',
                    field: 'customer_type',
                    headerSort: false,
                    mutator: this.getCustomerType,
                },
                {
                    title: 'Customer Name',
                    field: 'name_en',
                },
                {
                    title: 'Risk Category',
                    field: 'risk_category',
                    mutator: this.getRiskCategory,
                },
                {
                title: 'Status',
                    field: 'status',
                    headerSort: false,
                    width: 120,
                    hozAlign: 'center',
                    headerHozAlign: 'center',
                    formatter: 'html',
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
                            this.$router.push(`/customer-management/customer/${dataId}/edit`)
                        } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                            this.$router.push(`/customer-management/customer/${dataId}`)
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
    getCustomerType(value) {
        switch (value) {
            case 'IC':
                return 'Individual Customer'

            case 'CL':
                return 'Corporate Customer-Local'

            case 'CA':
                return 'Corporate Customer-Abroad'

            default:
                return ''
        }
    },
    getRiskCategory(value) {
        switch (value) {
            case 'L':
                return 'Low'

            case 'M':
                return 'Medium'

            case 'H':
                return 'High'

            default:
                return ''
        }
    },
    handleDelete(id) {
        this.$confirm.require({
            message: 'Do you want to delete this record?',
            header: 'Delete',
            icon: 'pi pi-info-circle',
            acceptClass: "p-button-danger",
            blockScroll: false,
            accept: () => {
                axios.delete(`/customers/${id}`).then(response => {
                    if (response.data.success) {
                        // refresh table
                        notify(response.data.message, "success",'bottom-right');
                        this.tabulator.replaceData()
                    }
                }).catch(() => notify(response.data.message, "error",'bottom-right'))
            },
        });
    },
    setFilter: _.debounce(function (e) {
        this.tabulator.setFilter([
            // Search
            { field: 'customer_no', type: 'like', value: e.search },
            { field: 'name_en', type: 'like', value: e.search },
            // Filter
            [
                {field: 'customer_type', type: '=', value: e.customerType},
            ]
        ])
    }, 500),
  },
}
</script>
