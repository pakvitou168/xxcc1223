<template>
    <div class="w-full">
        <div class="intro-y box p-5">
            <div class="flex">
                <div class="w-[15rem]">
                    <InputText class="rounded" placeholder="Search" @update:modelValue="setFilter"
                        v-model="formFilter.search" />
                </div>
                <div class="flex-1 flex justify-end">
                    <div class="inline-block">
                        <Button severity="danger" v-if="canDelete" @click="handleDelete"
                            class="button-danger" icon="pi pi-trash" label="Delete" />
                        <Button @click="insuredRef.toggleDialog()"
                            class="button-primary ml-1" icon="pi pi-plus" label="Add New" />
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto scrollbar-hidden">
                <DataTable :options="options" ref="tabulator" />
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <Button label="Back" class="button-default" @click="$emit('back')" severity="default" icon="pi pi-arrow-left"/>
            <Button label="Next" class="button-primary leading-6 ml-1" @click="$emit('next')" iconPos="right" icon="pi pi-arrow-right" />
        </div>
    </div>
    <InsuredDialog ref="insuredRef" :productCode="proCode" :dataId="dataId" @success="handleSuccess" />
</template>

<script setup>
import moment from 'moment';
import { computed, ref } from 'vue';
import { formatCurrency } from '@/helpers';
import InsuredDialog from '../Components/InsuredDialog.vue';
import { useConfirm } from "primevue/useconfirm";
import insuredPersonService from '@/services/pa/insuredPerson.service';

const confirm = useConfirm();
const insuredRef = ref(null)
const formFilter = ref({})
const tabulator = ref()
const emit = defineEmits(['back','next'])
const props = defineProps({
    dataId: {
        type: [Number, String],
        default: null
    },
    proCode: {
        type: [String, Number],
        default: ''
    }
})
const selectedRows = ref([])
const selectedData = ref([])
const canDelete = computed(() => {
    return selectedData.value.length > 0;
})
const options = computed(() => {
    return {
        selectable: true,
        ajaxURL: "/pa/quotation/" + props.dataId + "/insured-persons",
        columns: [
            {
                title: "Name",
                field: "name",
                width: 200,
            },
            {
                title: "Relationship",
                field: "relationship",
                width: 150,
            },
            {
                title: "Gender",
                field: "gender",
                width: 120,
            },

            {
                title: "Date of birth",
                field: "date_of_birth",
                width: 170,
                formatter: (cell) => {
                    const data = cell.getRow().getData();
                    return moment(data.date_of_birth).format('DD-MMM-YYYY')
                }
            },
            {
                title: "Sum insured",
                field: "sum_insured",
                width: 150,
                cssClass: 'text-right',
                formatter: (cell) => {
                    const data = cell.getRow().getData();
                    return formatCurrency(data.sum_insured)
                }
            },
            {
                title: "Permanent Disablement",
                field: "permanent_disablement_amount",
                width: 230,
                cssClass: 'text-right',
                formatter: (cell) => {
                    const data = cell.getRow().getData();
                    return formatCurrency(data.sum_insured)
                }
            },
            {
                title: "Medical Expense",
                field: "medical_expense_amount",
                width: 180,
                cssClass: 'text-right',
                formatter: (cell) => {
                    const data = cell.getRow().getData();
                    return formatCurrency(data.sum_insured)
                }
            },
            {
                title: "Class",
                field: "working_class_code"
            },
        ],
        rowDblClick: (e, row) => {
            const data = row._row.data
            handleDetail(data)
        },
        rowSelected: (row) => {
            handleRowSelection()
        },
        rowDeselected: (row) => {
            handleRowSelection()
        }
    }
})
const handleRowSelection = () => {
    selectedRows.value = tabulator.value?.getSelectedRows()
    selectedData.value = tabulator.value?.getSelectedData()
}
const handleDetail = (insuredPerson) => {
    insuredRef.value?.toggleDialog(insuredPerson)
}
const handleDelete = () => {
    if (selectedData.value.length > 0) {
        confirm.require({
            message: 'Are you sure to delete?',
            header: 'Confirmation',
            icon: 'text-red-500 pi pi-exclamation-triangle',
            rejectClass: 'p-button-secondary p-button-outlined',
            acceptClass: 'p-button-danger',
            rejectLabel: 'Cancel',
            acceptLabel: 'Delete',
            accept: () => {
                submitDelete()
            },
            reject: () => {

            }
        });
    } else {
        notify("No insured person selected", 'warn')
    }
}
const submitDelete = () => {
    const ids = selectedData.value.map(item => item.id);
    insuredPersonService.deleteMulti({ ids,dataId: props.dataId }).then(res => {
        notify(res.data.message, 'success')
        tabulator.value?.clearSelectedRows()
        handleRowSelection()
        tabulator.value?.reloadTable()
    }).catch(err => {
        notify(err.response?.data?.message, 'error')
    })
}
const handleSuccess = () => {
    tabulator.value?.reloadTable()
    insuredRef.value?.toggleDialog()
}
const setFilter = _.debounce(() => {
    tabulator.value?.setFilter([
        { field: 'name', type: 'like', value: formFilter.value.search }
    ])
}, 500)
</script>