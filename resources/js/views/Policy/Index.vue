<template>
    <div>
        <AddNew :addNew="addNew" :canExport="canExport" @export="exportPoliciesExcel"/>
        <div class="intro-y box p-5 mt-5">
            <Search @setFilter="setFilter" />
            <div class="overflow-x-auto scrollbar-hidden">
                <DataTable @ref="tabulator = $event" :options="options" />
            </div>
        </div>
    </div>
</template>

<script>

import AddNew from "@/components/Form/AddNew.vue"
import Search from './Search.vue'
import Status from "@/components/DataTable/Status.vue";
import Action from "./Action.vue"
import {createApp, markRaw} from "vue";
export default {

    components: {
        AddNew,
        Search,
    },

    data() {
        return {
            addNew: {
                label: "Auto Policies",
                hasAddNew: false,
            },
            issued_date_from: '',
            issued_date_to: '',
            canExport: true,
            tabulator: null,
            options: {
                persistenceID: "policies-table",
                ajaxURL: "/api/policies",
                columns: [
                    {
                        title: "Policy No.",
                        field: "document_no",
                        sorter: "string",
                        minWidth: 200,
                    },
                    {
                        title: "Quotation No.",
                        field: "quotation",
                        headerSort: false,
                        minWidth: 200,
                        mutator: (_, row) => {
                        if (!row.policy.quotation) return ''

                        return row.policy.quotation.document_no
                        },
                    },
                    {
                        title: "Customer Name",
                        field: "name_en",
                        sorter: "string",
                        minWidth: 200,
                    },
                    {
                        title: "Premium",
                        field: "total_premium",
                        sorter: "number",
                        formatter: 'money',
                        width: 100,
                    },
                    {
                        title: 'Issue Date',
                        field: 'issued_at',
                        formatter: "datetime",
                        width: 120,
                        formatterParams:{
                            outputFormat:"DD/MM/YY",
                        },
                    },
                    {
                        title: "Version",
                        field: "version",
                        sorter: "number",
                        width: 90,
                    },
                    {
                        title: "Cycle",
                        field: "cycle",
                        sorter: "number",
                        width: 90,
                    },
                    {
                        title: "Status",
                        field: "status",
                        width: 100,
                        hozAlign: "center",
                        headerHozAlign: "center",
                        formatter: (cell, formatterParams, onRendered) => {
                            const rowData = cell.getRow().getData();
                            const container = document.createElement("div");
                            createApp(Status, {status: rowData.status }).mount(container);
                            return container;
                        }
                    },
                    {
                        title: "Submit Status",
                        field: "approved_status",
                        minWidth: 120,
                        hozAlign: "center",
                        headerHozAlign: "center",
                        formatter: (cell, formatterParams, onRendered) => {
                            const rowData = cell.getRow().getData();
                            const container = document.createElement("div");
                            createApp(Status, {status: rowData.policy?.approved_status }).mount(container);
                            return container;
                        }
                    },
                    {
                        title: 'Approved Reason',
                        field: 'approved_reason',
                        headerSort: false,
                        minWidth: 180,
                        tooltip: true,
                        mutator: (_, row) => {
                        if (!row.policy) return ''

                        return row.policy.approved_reason
                        },
                    },
                    /*{
                        title: "Actions",
                        field: "actions",
                        width: 105,
                        headerSort: false,
                        headerHozAlign: "center",
                        formatter: "html",
                        cellClick: (e, cell)=> {
                            let dataId = cell._cell.row.data.id;
                            let status = cell._cell.row.data._status;
                            if (cell._cell.element.querySelector("a.edit svg").contains(e.target)) {
                                if(status == 'REJ')
                                    this.handleRevise(dataId);
                                else
                                    this.$router.push({name:'PolicyEdit',params:{id:dataId}});
                            } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                                this.$router.push({name:'PolicyDetail',params:{id:dataId}});
                            } else if (cell._cell.element.querySelector("a.delete svg").contains(e.target)) {
                                this.handleDelete(dataId);
                            }
                        },
                    },*/

                    {
                        title: "Actions",
                        field: "actions",
                        width: 105,
                        headerSort: false,
                        headerHozAlign: "center",
                        formatter: (cell, formatterParams, onRendered) => {
                            const rowData = cell.getRow().getData();
                            const container = document.createElement("div");
                            let dataId = cell._cell.row.data.id;
                            let status = cell._cell.row.data._status;
                            const eventHandlers = {
                                onView: () => {
                                    this.$router.push({name:'PolicyDetail',params:{id:dataId}});
                                },
                                onDelete: () => {
                                    this.handleDelete(dataId);
                                },
                                onEdit: () => {
                                    if(status == 'REJ')
                                        this.handleRevise(dataId);
                                    else
                                        this.$router.push({name:'PolicyEdit',params:{id:dataId}});
                                },
                            };
                            createApp(markRaw(Action), { rowData: rowData, events: eventHandlers }).mount(container);
                            return container;
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
                    axios.delete(`/api/policies/${id}`).then(response => {
                        if (response.data.success) {
                            // refresh table
                            notify(response.data.message, 'success')
                            this.tabulator.replaceData()
                        }
                    }).catch(err => {
                        notify('Error', 'error')
                    })
                },
            });
        },
        handleRevise(id) {
            this.$confirm.require({
                message: 'Do you want to revise this record?',
                header: 'Revise',
                icon: 'pi pi-info-circle',
                acceptClass: "p-button-info",
                rejectClass: "p-button-secondary",
                blockScroll: false,
                accept: () => {
                    axios.post(`/api/policies/revise/${id}`).then(response => {
                        if (response.data.success) {
                            // refresh table
                            notify(response.data.message, 'success')
                            this.tabulator.replaceData()
                            this.$router.push({name:'PolicyEdit',params:{id:id}});
                        }
                    }).catch(err => {
                        notify('Error', 'error')
                    })
                },
            });
        },

        setFilter: _.debounce(function (e) {
            this.issued_date_from = e.issued_date_from
            this.issued_date_to = e.issued_date_to
            this.tabulator.setFilter([
                {field: 'document_no', type: 'like', value: e.search},
                {field: 'name_en', type: 'like', value: e.search},
                {field: 'total_premium', type: 'like', value: e.search},
                {field: 'version', type: 'like', value: e.search},
                {field: 'cycle', type: 'like', value: e.search},
                {field: 'autoDetails.plate_no', type: '=', value: e.search},
                [
                    {field: 'issued_at', type: '>=', value: e.issued_date_from},
                    {field: 'issued_at', type: '<=', value: e.issued_date_to},
                ],
            ])
        }, 500),

        exportPoliciesExcel(){
            location.href = '/policy-service/export-policy/from' + this.issued_date_from + '/to' + this.issued_date_to;
        },

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
