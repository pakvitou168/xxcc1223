<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew" />
    <div class="intro-y box p-5 mt-5">
      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options" />
      </div>
    </div>
  </div>
</template>

<script>

import AddNew from '@/components/Form/AddNew'
import UserPermissions from '../../../mixins/UserPermissions'

export default {
  mixins: [UserPermissions],

  components: {
    AddNew,
  },

  data() {
    return {
      functionCode: 'APPLICATION',
      addNew: {
        label: 'Applications',
        name: 'Application',
        prefix: 'Add New',
        linkAddNew: '/user-management/applications/new',
      },
      tabulator: null,
      options: {
        persistenceID: "applications-table",
        ajaxURL: '/applications',
        columns: [
          {
            title: 'Application Code',
            field: 'code',
            sorter: 'string',
          },
          {
            title: 'Application Name',
            field: 'name',
            sorter: 'string',
          },
          {
            title: 'Access Level',
            field: 'access_lvl',
            sorter: 'number',
          },

          {
            title: 'Allow Change Username',
            field: 'allow_change_username',
            headerSort: false,
            mutator: this.allowChangeType,
            formatter: 'html',
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
                this.$router.push({path: `/user-management/applications/${dataId}/edit`});
              } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                this.$router.push({path: '/user-management/applications/' + dataId});
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
    allowChangeType(value) {
      switch (value) {
        case 'Y':
          return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="check-circle" role="presentation" class="text-green-500 fill-current text-success"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"></path></svg>'
        case 'N':
          return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="x-circle" role="presentation" class="text-red-500 fill-current text-danger"><path d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"></path></svg>'
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
          axios.delete(`/applications/${id}`).then(response => {
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