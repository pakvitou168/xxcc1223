<template>
  <div>
    <AddNew :canAddNew="canAddNew" :addNew="addNew" />
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
import SearchBar from '@/components/DataTable/SearchBar'
import UserPermissions from '../../../mixins/UserPermissions'

export default {
  mixins: [UserPermissions],

  components: {
    AddNew,
    SearchBar,
  },

  data(){
    return {
      functionCode: 'ROLE',
      addNew: {
        label: 'Roles',
        name: 'Role',
        prefix: 'Add New',
        linkAddNew: '/user-management/roles/new',
      },
      tabulator: null,
      options: {
        persistenceID: "roles-table",
        ajaxURL: "/roles",
        columns: [
          {
            title: "Role Code",
            field: "code",
          },
          {
            title: "App Code",
            field: "app_code",
          },
          {
            title: "Name",
            field: "name",
          },

          {
            title: "Description",
            field: "description",
          },
          {
            title: "Status",
            field: "status",
            width: 120,
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "html",
            headerSort: false,
            mutator: this.getStatus
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
                this.$router.push({path: `/user-management/roles/${dataId}/edit`});
              } else if (cell._cell.element.querySelector("a.view svg").contains(e.target)) {
                this.$router.push({path: `/user-management/roles/${dataId}`});
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
    handleDelete(id) {
      this.$confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Delete',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          axios.delete(`/roles/${id}`).then(response => {
            if (response.data.success) {
              // refresh table
              this.toastMessage(response.data.message, 'Success')
              this.tabulator.replaceData()
            }
          }).catch(() => this.toastMessage('Error', 'Error'))
        },
      });
    },

    getStatus(value) {
      if (value == 'ACT') {
        return '<span class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full">Active</span>'
      } else if (value == 'BLK') {
        return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Blocked</span>'
      } else if (value == 'DBL') {
        return '<span class="text-xs px-2 bg-gray-200 text-black mr-1 py-1 rounded-full">Disabled</span>'
      } else if (value == 'DEL') {
        return '<span class="text-xs px-2 bg-theme-6 text-white mr-1 py-1 rounded-full">Deleted</span>'
      } else if (value == 'LCK') {
        return '<span class="text-xs px-2 bg-gray-200 text-black mr-1 py-1 rounded-full">Locked</span>'
      }
      return ''
    },

    setFilter(e) {
      this.tabulator.setFilter([
        { field: 'code', type: 'like', value: e.target.value },
        { field: 'app_code', type: 'like', value: e.target.value },
        { field: 'name', type: 'like', value: e.target.value },
        { field: 'description', type: 'like', value: e.target.value },
      ])
    },

    toastMessage(msg, type, position = 'bottom') {
      this.$notify({
        group: position,
        title: type,
        text: msg
      }, 4000);
    },
  },
}
</script>
