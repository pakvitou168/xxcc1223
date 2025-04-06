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
            functionCode: 'BUSINESS_CATEGORY',
            addNew: {
                label: "Business Categories",
                name: "Business Category",
                prefix: "Add New",
                linkAddNew: "/business-management/business-categories/new"
            },
            tabulator: null,
            options: {
                persistenceID: 'business-categories-table',
                ajaxURL: "/business-categories",
                columns: [
                    {
                        title: "Name",
                        field: "name",
                        sorter: "string",
                        minWidth: 150,
                    },
                    {
                        title: "Description",
                        field: "description",
                        sorter: "string",
                        minWidth: 150,
                    },
                    {
                        title: "Prefix",
                        field: "prefix",
                        sorter: "string",
                        width: 90
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
                                this.$router.push("/business-management/business-categories/" + dataId);
                            } else if (editButton.contains(e.target)) {
                                this.$router.push("/business-management/business-categories/" + dataId + "/edit");
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
                    axios.delete(`/business-categories/${id}`).then(response => {
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
                {field: 'name', type: 'like', value: e.target.value},
                {field: 'description', type: 'like', value: e.target.value},
            ])
        }, 500),
    }
};
</script>
