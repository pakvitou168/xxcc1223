<template>
    <div>
        <AddNew :addNew="addNew" :canExport="canExport" @export="exportEndorsementsExcel"/>
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

export default {

    components: {
        AddNew,
        Search,
    },

    data() {
        return {
            addNew: {
                label: "Endorsements",
                hasAddNew: false,
            },
            issued_date_from: '',
            issued_date_to: '',
            canExport: true,
            tabulator: null,
            options: {
                persistenceID: "endorsements-table",
                ajaxURL: "/api/endorsements",
                columns: [
                    {
                        title: "Policy No.",
                        field: "document_no",
                        sorter: "string",
                    },
                    {
                        title: "Customer Name",
                        field: "name_en",
                        sorter: "string",
                        minWidth: 200,
                    },
                    {
                        title: "Premium",
                        field: "endorsed_premium",
                        headerSort: false,
                    },
                    {
                        title: 'Issue Date',
                        field: 'issued_at',
                        formatter: "datetime",
                        width: 130,
                        formatterParams:{
                            outputFormat:"DD/MM/YY",
                        },
                    },
                    {
                        title: "Version",
                        field: "version",
                        sorter: "number",
                        width: 120,
                    },
                    {
                        title: "Cycle",
                        field: "cycle",
                        sorter: "number",
                        width: 120,
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
                            if (value === 'APV')
                                return '<span class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Approved</span>'
                            else if (value === 'REJ')
                                return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Rejected</span>'
                            else if (value === 'PND')
                                return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">Pending</span>'

                            return ''
                        },
                    },
                    {
                        title: "Submit Status",
                        field: "approved_status",
                        minWidth: 120,
                        hozAlign: "center",
                        headerHozAlign: "center",
                        formatter: "html",
                        headerSort: false,
                        mutator: (_, row) => {
                            if (row.endorsement.approved_status === 'SBM')
                                return '<span class="text-xs px-2 bg-theme-9 text-white mr-1 py-1 rounded-full">Submitted</span>'
                            else if (row.endorsement.approved_status === 'PRG')
                                return '<span class="text-xs px-2 bg-theme-12 text-white mr-1 py-1 rounded-full">In Progress</span>'
                            return ''
                        },
                    },
                    {
                        title: 'Approved Reason',
                        field: 'approved_reason',
                        tooltip: true,
                        headerSort: false,
                        minWidth: 180,
                        accessorDownload: function(value){
                            if(value)
                                return value;
                        },
                        mutator: (_, row) => row.endorsement?.approved_reason,
                    },
                    {
                        title: "Actions",
                        field: "actions",
                        width: 105,
                        headerSort: false,
                        headerHozAlign: "center",
                        formatter: "html",
                        download:false,
                        cellClick: (e, cell)=> {
                            let dataId = cell._cell.row.data.id;
                            let status = cell._cell.row.data._status;

                            if (cell._cell.element.querySelector("a.edit svg").contains(e.target)) {
                                if(status == 'REJ')
                                    this.handleRevise(dataId);
                                else
                                    this.$router.push({name:"EndorsementEdit",params:{
                                id:dataId}
                                });
                            } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                                this.$router.push({
                                    name:"EndorsementDetail",
                                    params:{
                                        id:dataId
                                    }
                                });
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
                icon: 'pi pi-info-circle text-red-500',
                rejectClass: "p-button-secondary",
                acceptClass: "p-button-danger",
                blockScroll: false,
                accept: () => {
                    axios.delete(`/api/endorsements/${id}`).then(response => {
                        if (response.data.success) {
                            // refresh table
                            notify(response.data.message, 'success')
                            this.tabulator.replaceData()
                        }
                    }).catch(err => {
                        notify(err.response?.data?.message, 'error')
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
                    axios.post(`/api/endorsements/revise/${id}`).then(response => {
                        if (response.data.success) {
                            // refresh table
                            notify(response.data.message, 'success')
                            this.tabulator.replaceData()
                            this.$router.push("/endorsements/" + id + "/edit");
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
                {field: 'version', type: 'like', value: e.search},
                {field: 'cycle', type: 'like', value: e.search},
                {field: 'autoDetails.plate_no', type: '=', value: e.search},
                [
                    {field: 'issued_at', type: '>=', value: e.issued_date_from},
                    {field: 'issued_at', type: '<=', value: e.issued_date_to},
                ],
            ])
        }, 500),

        exportEndorsementsExcel(){
            location.href = '/endorsement-service/export-excel/from' + this.issued_date_from + '/to' + this.issued_date_to;
        }
    },
}
</script>
