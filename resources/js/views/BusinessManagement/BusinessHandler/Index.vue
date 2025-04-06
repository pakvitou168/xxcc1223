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
            functionCode: 'BUSINESS_HANDLER',
            addNew: {
                label: "Business Handlers",
                name: "Business Handler",
                prefix: "Add New",
                linkAddNew: "/business-management/business-handlers/new"
            },
            tabulator: null,
            options: {
                persistenceID: "business-handlers-table",
                ajaxURL: "/business-handlers",
                columns: [
                    {
                        title: "Handler Code",
                        field: "handler_code",
                        sorter: "string",
                        width: 160,
                    },
                    {
                        title: "Title",
                        field: "title",
                        sorter: "string",
                        width: 120,
                        hozAlign: "center",
                        headerHozAlign: "center",
                    },
                    {
                        title: "Name",
                        field: "name",
                        sorter: "string",
                        widthGrow: 1,
                    },
                    {
                        title: "Phone",
                        field: "phone",
                        sorter: "string",
                        widthGrow: 1,
                    },
                    {
                        title: "Email",
                        field: "email",
                        sorter: "string",
                        widthGrow: 1,
                    },
                    {
                        title: "Status",
                        field: "status",
                        headerSort: false,
                        width: 120,
                        hozAlign: "center",
                        headerHozAlign: "center",
                        formatter: "html",
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
                        cellClick: (e, cell) => {
                            const dataId = cell._cell.row.data.id;
                            const viewButton = cell._cell.element.querySelector("a.view svg");
                            const editButton = cell._cell.element.querySelector("a.edit svg");
                            const deleteButton = cell._cell.element.querySelector("a.delete svg");

                            if (viewButton.contains(e.target)) {
                                this.$router.push("/business-management/business-handlers/" + dataId);
                            } else if (editButton.contains(e.target)) {
                                this.$router.push("/business-management/business-handlers/" + dataId + "/edit");
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
                    axios.delete(`/business-handlers/${id}`).then(response => {
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
                {field: 'handler_code', type: 'like', value: e.target.value},
                {field: 'title', type: 'like', value: e.target.value},
                {field: 'name', type: 'like', value: e.target.value},
                {field: 'phone', type: 'like', value: e.target.value},
                {field: 'email', type: 'like', value: e.target.value},
            ])
        }, 500),
    }
};
</script>
