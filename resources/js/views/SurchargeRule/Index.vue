<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew">
    </AddNew>
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
import Search from '@/components/DataTable/SearchBar.vue'
import DownloadIcon from '../../components/Icons/DownloadIcon.vue'
import UploadIcon from '../../components/Icons/UploadIcon.vue'
import { hasPermission } from '@/services/auth.service'
import { getActionButtons } from '@/components/DataTable/actionButton'

export default {

  components: {
    AddNew,
    Search,
    DownloadIcon,
    UploadIcon,
  },

  data() {
    return {
      canAddNew: hasPermission('SURCHARGE_RULE', 'NEW'),
      addNew: {
        label: 'Surcharges Rules',
        name: 'Surcharge Rules',
        prefix: '',
        linkAddNew: '/surcharge-rules/new',
      },
      issued_date_from: '',
      issued_date_to: '',
      canExport: true,
      tabulator: null,
      options: {
        persistenceID: 'surcharge-rule-table',
        ajaxURL: "/api/surcharge-rules",
        columns: [
          {
            title: "Claim Ratio From (%)",
            field: "claim_ratio_from",
            sorter: "number",
            minWidth: 200,
            cssClass:'text-right'
          },
          {
            title: "Claim Ratio to (%)",
            field: "claim_ratio_to",
            sorter: "number",
            minWidth: 100,
            cssClass:'text-right'
          },
          {
            title: "Surcharge (%)",
            field: "surcharge",
            sorter: "number",
            minWidth: 100,
            cssClass:'text-right'
          },
          {
            title: 'Remark',
            field: 'remark',
            headerSort: false,
            minWidth: 180,
          },
          {
            title: "Actions",
            field: "actions",
            width: 105,
            headerSort: false,
            headerHozAlign: 'center',
            formatter: 'html',
            mutator: (_, row) => getActionButtons(
              false,
              this.canUpdate(),
              this.canDelete(),
            ),
            cellClick: (e, cell) => {
              const dataId = cell._cell.row.data.id;
              if (cell._cell.element.querySelector('a.edit svg').contains(e.target)) {
                this.$router.push('/surcharge-rules/' + dataId + '/edit')
              } else if (cell._cell.element.querySelector('a.delete svg').contains(e.target)) {
                this.handleDelete(dataId)
              }
            },
          },
        ],
      },
    }
  },

  methods: {
    canUpdate() {
      let canUpdatePermission = hasPermission('SURCHARGE_RULE', 'UPDATE')
      if (!canUpdatePermission) return false

      return true
    },
    canDelete() {
      let canDeletePermission = hasPermission('SURCHARGE_RULE', 'DELETE');
      if (!canDeletePermission) return false

      return true
    },

    handleDelete(id) {
      this.$confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Delete',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios.delete(`api/surcharge-rules/${id}`).then(response => {
            if (response.data.success) {
              // refresh table
              this.toastMessage(response.data.message, 'Success')
              this.tabulator.replaceData()
            }
          }).catch(err => {
            this.toastMessage('Error', 'Error')
          })
        },
      });
    },

    setFilter: _.debounce(function (e) {
      this.tabulator.setFilter([
        { field: 'claim_ratio_from', type: 'like', value: e.target.value },
        { field: 'claim_ratio_to', type: 'like', value: e.target.value },
        { field: 'surcharge', type: 'like', value: e.target.value },
        { field: 'remark', type: 'like', value: e.target.value },
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
  