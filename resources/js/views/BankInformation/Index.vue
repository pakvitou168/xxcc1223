<template>
    <div>
        <AddNew :addNew="addNew" />
        <div class="intro-y box p-5 mt-5">
            <SearchBar @setFilter="setFilter" />
            <div class="overflow-x-auto scrollbar-hidden">
                <DataTable @ref="tabulator = $event" :options="options" />
            </div>
        </div>
    </div>
</template>

<script>

import AddNew from "@/components/Form/AddNew.vue"
import SearchBar from '@/components/DataTable/SearchBar.vue'
import UserPermissions from '../../mixins/UserPermissions'


export default {
    mixins: [UserPermissions],
    components: {
        AddNew,
        SearchBar,
    },

    data() {
        return {
            functionCode: 'BANK_INFORMATION',
            addNew: {
                label: "Bank Information",
                name: "Bank Information",
                prefix: "Add New",
                linkAddNew: "/bank-informations/new"
            },
            tabulator: null,
            options: {
                persistenceID: 'bank-informations-table',
                ajaxURL: "/api/bank-informations",
                columns: [
                    {
                        title: "Code",
                        field: "code",
                        sorter: "string",
                        width: 100,
                    },
                    {
                        title: "Name",
                        field: "name",
                        sorter: "string",
                        minWidth: 200,
                    },
                    {
                        title: "Account No.",
                        field: "account_no",
                        sorter: "number",
                        width: 160,
                    },
                    {
                        title: "Account Name",
                        field: "account_name",
                        sorter: "string",
                        minWidth: 200,
                    },
                    {
                        title: "Currency",
                        field: "ccy",
                        sorter: "number",
                        headerHozAlign: "center",
                        hozAlign: 'center',
                        width: 100,
                        headerSort: false,
                    },
                    {
                        title: "Default",
                        field: "default",
                        sorter: "string",
                        width: 90,
                        mutator: this.getIsDefaultPayment,
                        formatter: "html",
                        headerSort: false,
                    },
                    {
                        title: "Actions",
                        field: "actions",
                        width: 100,
                        headerSort: false,
                        headerHozAlign: "center",
                        formatter: "html",
                        download:false,
                        cellClick: (e, cell)=> {
                            let dataId = cell._cell.row.data.id;

                            if (cell._cell.element.querySelector("a.edit svg").contains(e.target)) {
                                this.$router.push("/bank-informations/" + dataId + "/edit");
                            } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                                this.$router.push("/bank-informations/" + dataId + "");
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
        getIsDefaultPayment(value) {
            if (value) {
                return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="check-circle" role="presentation" class="text-green-500 fill-current text-success mx-auto">
                    <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"></path>'
                </svg>
                `;
            } else {
                return  `
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="x-circle" role="presentation" class="text-red-500 fill-current text-danger mx-auto">
                    <path d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"></path>
                </svg>
                `;
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
                    axios.delete(`/api/bank-informations/${id}`).then(response => {
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
                {field: 'code', type: 'like', value: e.target.value},
                {field: 'type', type: 'like', value: e.target.value},
                {field: 'name', type: 'like', value: e.target.value},
                {field: 'account_no', type: 'like', value: e.target.value},
                {field: 'account_name', type: 'like', value: e.target.value},
            ])
        }, 500),

        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        }
    },
}
</script>
