<template>
    <div>
        <AddNew :addNew="addNew" />
        <div class="intro-y box p-5 mt-5">
            <Search @setFilter="setFilter"/>
            <div class="overflow-x-auto scrollbar-hidden">
                <DataTable @ref="tabulator = $event" :options="options" />
            </div>
        </div>
    </div>
</template>

<script>

import AddNew from "@/components/Form/AddNew.vue";
import Search from './Search.vue'
import UserPermissions from '../../../mixins/UserPermissions'


export default {
    mixins: [UserPermissions],
    components: {
        Search,
        AddNew,
    },

    data() {
        return {
            functionCode: 'NO_CLAIM_DISCOUNT',
            addNew: {
                label: "No Claim Discount",
                name: "No Claim Discount",
                prefix: "Add New",
                linkAddNew: "/product-configuration/no-claim-discounts/new"
            },
            tabulator: null,
            options: {
                persistenceID: "no-claim-discounts-table",
                ajaxURL: "/no-claim-discounts",
                columns: [
                    {
                        title: "Product Code",
                        field: "product_code",
                        sorter: "string",
                        width: 220,
                        mutator: this.getProduct,
                    },
                    {
                        title: "No Claim Discount (%)",
                        field: "ncd",
                        sorter: "number",
                        headerHozAlign: "center",
                        hozAlign: 'center',
                        headerSort: false,
                        width: 220,
                    },
                    {
                        title: "Description",
                        field: "description",
                        sorter: "string",
                        headerSort: false,
                        minWidth: 200,
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
                                this.$router.push("/product-configuration/no-claim-discounts/" + dataId + "/edit");
                            } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                                this.$router.push("/product-configuration/no-claim-discounts/" + dataId + "");
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
        getProduct(value, data) {
            return `${data.product?.code} - ${data.product?.name}`
        },
        handleDelete(id) {
            this.$confirm.require({
                message: 'Do you want to delete this record?',
                header: 'Delete',
                icon: 'pi pi-info-circle',
                acceptClass: "p-button-danger",
                blockScroll: false,
                accept: () => {
                    axios.delete(`/no-claim-discounts/${id}`).then(response => {
                        if (response.data.success) {
                            // refresh table
                            notify(response.data.message, 'success','bottom-right')
                            this.tabulator.replaceData()
                        }
                    }).catch(err => {
                        notify('Error', 'error', 'bottom-right')
                    })
                },
            });
        },

        setFilter(e) {
            this.tabulator.setFilter([
                [
                    {field: 'product_code', type: '=', value: e.product},
                ]
            ])
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
