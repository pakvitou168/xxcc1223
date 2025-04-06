<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-5">
      <h2 class="text-lg font-medium mr-auto">Renewals</h2>

      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button class="btn btn-primary shadow-md mr-2" title="Process No Claim to Approval"
          :class="{ 'opacity-50': isProcessNoClaimToApproval || !canProcessNoClaimToApproval }"
          :disabled="isProcessNoClaimToApproval || !canProcessNoClaimToApproval" @click="proceedNoClaimToApproval">
          <span v-if="isProcessNoClaimToApproval">Process No Claim to Approval ...</span>
          <span v-else>Process No Claim to Approval</span>
        </button>
        <button class="btn btn-success shadow-md" @click="openReportDialog">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
            </path>
          </svg>
        </button>
      </div>
    </div>

    <div class="intro-y box p-5 mt-5">
      <div class="flex gap-6">
        <Search v-model="filterFields" @setFilter="setFilter" />
        <LoadData @setFilter="handleLoadData" :loading="isLoadingData" :canLoadData="canLoadData"
          :errors="loadDataErrors" />
      </div>

      <div class="overflow-x-auto scrollbar-hidden">
        <DataTable @ref="tabulator = $event" :options="options" />
      </div>
    </div>
    <ReportDialog :isVisible="showReportDialog" header="Renewal Report" :loading="isExporting" @hideDialog="hideDialog"
      @export="exportReport" />
  </div>
</template>

<script>
import { getActionButtons } from '@/components/DataTable/actionButton'
import Search from './Search.vue'
import LoadData from './LoadData.vue'
import renewalService from '@/services/renewal/renewal.service'
import { hasPermission } from '@/services/auth.service'
import ReportDialog from '@/views/Renewal/ReportDialog.vue'
import moment from 'moment'
import Status from "@/components/DataTable/Status.vue";
import { createApp } from 'vue'

export default {
  components: {
    Search,
    LoadData,
    ReportDialog,
  },

  data() {
    return {
      tabulator: null,
      isProcessNoClaimToApproval: false,
      isLoadingData: false,
      showReportDialog: false,
      isExporting: false,
      filterFields: {
        search: '',
        status: '',
        submitStatus: '',
      },
      loadDataErrors: {},

      options: {
        ajaxURL: '/api/renewals',
        columns: [
          {
            title: 'Product Line',
            field: 'product_line_code',
            headerSort: false,
            width: 110,
          },
          {
            title: 'Renewal Policy No.',
            field: 'document_no',
            width: 170,
          },
          {
            title: 'Customer Name',
            field: 'insured_name',
            minWidth: 160,
            headerSort: false,
          },
          {
            title: 'Biz Channel',
            field: 'business_channel_name',
            width: 140,
            headerSort: false,
          },
          {
            title: 'Premium',
            field: 'premium',
            width: 115,
            mutator: (_, row) => {
              if (!row.premium) return ''

              return parseFloat(row.premium).toFixed(2)
            },
            hozAlign: "right",
          },
          {
            title: 'Total Vehicles',
            field: 'total_vehicle',
            width: 120,
            hozAlign: "right",
            headerSort: false,
          },
          {
            title: 'Total Claim',
            field: 'claim_request_count',
            width: 120,
            hozAlign: "right",
          },
          {
            title: 'Expired Date',
            field: 'expired_date',
            width: 120,
            headerSort: false,
          },
          {
            title: 'Submit Status',
            field: 'submit_status',
            width: 130,
            headerSort: false,
            headerHozAlign: "center",
            hozAlign: 'center',
            formatter: (cell, formatterParams, onRendered) => {
                const rowData = cell.getRow().getData();
                const container = document.createElement("div");
                createApp(Status, { status: rowData.submit_status }).mount(container);
                return container;
            }
          },
          {
            title: 'Submit Reason',
            field: 'approved_reason',
            headerSort: false,
            minWidth: 180,
            tooltip: true,
            mutator: (_, row) => {
              if (!row.approved_reason) return ''

              return row.approved_reason
            },
          },
          {
            title: 'Accepted Status',
            field: 'accept_status',
            width: 150,
            headerSort: false,
            headerHozAlign: "center",
            hozAlign: 'center',
            formatter: (cell, formatterParams, onRendered) => {
                const rowData = cell.getRow().getData();
                const container = document.createElement("div");
                createApp(Status, { status: rowData.accept_status }).mount(container);
                return container;
            }
          },
          {
            title: 'Accepted Reason',
            field: 'accepted_reason',
            headerSort: false,
            minWidth: 180,
            tooltip: true,
            mutator: (_, row) => {
              if (!row.accepted_reason) return ''

              return row.accepted_reason
            },
          },
          {
            title: 'Status',
            field: 'status',
            width: 95,
            headerSort: false,
            headerHozAlign: "center",
            hozAlign: 'center',
            formatter: (cell, formatterParams, onRendered) => {
                const rowData = cell.getRow().getData();
                const container = document.createElement("div");
                createApp(Status, { status: rowData.status }).mount(container);
                return container;
            }
          },
          {
            title: 'Actions',
            field: 'actions',
            width: 95,
            headerSort: false,
            headerHozAlign: "center",
            formatter: "html",
            mutator: (_, row) => getActionButtons(
              hasPermission('RENEWAL', 'VIEW'),
              this.canUpdate(row.submit_status),
              false,
              this.canRevise(row.submit_status)
            ),
            cellClick: (e, cell) => {
              let dataId = cell._cell.row.data.id;

              if (cell._cell.element.querySelector('a.view svg')?.contains(e.target)) {
                this.$router.push({ name: 'RenewalDetail', params: { id: dataId } })
              } else if (cell._cell.element.querySelector('a.edit svg')?.contains(e.target)) {
                this.$router.push({ name: 'RenewalEdit', params: { id: dataId } })
              } else if (cell._cell.element.querySelector('a.revise svg')?.contains(e.target)) {
                this.handleRevise(dataId)
              }
            },
          },
        ],
      },
    }
  },

  computed: {
    canLoadData() {
      return hasPermission('RENEWAL', 'LOAD')
    },
    canProcessNoClaimToApproval() {
      return hasPermission('RENEWAL', 'PROCESS')
    },
  },

  methods: {
    proceedNoClaimToApproval() {
      this.$confirm.require({
        message: 'Do you want to approve all no claim policies?',
        header: 'Confirmation',
        icon: 'pi pi-info-circle',
        acceptLabel: 'Approve',
        rejectLabel: 'Cancel',
        acceptClass: 'p-button-info',
        rejectClass: 'p-button-danger p-button-outlined',
        blockScroll: false,
        accept: () => {
          this.isProcessNoClaimToApproval = true

          renewalService.autoApproveNoClaimPolicies().then(res => {
            this.clearFilter()

            this.$notify({
              group: 'bottom',
              title: 'Success',
              text: res.data.message,
            }, 4000);
          })
            .catch(err => {
              this.$notify({
                group: 'bottom',
                title: 'Error',
                text: err.response.data.message,
              }, 4000);
            })
            .finally(() => {
              this.isProcessNoClaimToApproval = false
            })
        },
      });
    },

    setFilter(filterValues) {
      this.tabulator.setFilter([
        { field: 'document_no', type: 'like', value: filterValues.search },
        { field: 'premium', type: 'like', value: filterValues.search },
        { field: 'claim_request_count', type: 'like', value: filterValues.search },
        [
          { field: 'submit_status', type: '=', value: filterValues.submitStatus },
          { field: 'status', type: '=', value: filterValues.status },
        ]
      ])
    },

    async handleLoadData(filterValues) {
      this.isLoadingData = true
      console.log(filterValues)
      // Clear error
      this.loadDataErrors = {}

      try {
        await renewalService.generateRenewalList(filterValues.uw_year, moment(filterValues.expired_date_from).format('YYYY-MM-DD'), moment(filterValues.expired_date_to).format('YYYY-MM-DD'))
      } catch (e) {
        if (e.response.status === 422) {
          this.loadDataErrors = e.response.data?.errors
        } else {
          console.error(e)
        }

        // Exit before filtering if error happens
        return;
      } finally {
        this.isLoadingData = false
      }

      this.clearFilter()
    },

    clearFilter() {
      this.setFilter({ search: '', status: '', submitStatus: '' })

      // Clear Search and filters fields
      this.filterFields.search = ''
      this.filterFields.status = ''
      this.filterFields.submitStatus = ''
    },

    handleRevise(id) {
      this.$confirm.require({
        message: 'Do you want to revise this record?',
        header: 'Revise',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-info",
        blockScroll: false,
        acceptLabel: 'Revise',
        rejectLabel: 'Cancel',
        accept: () => {
          renewalService.revise(id).then(res => {
            if (res.data?.success) {
              this.$notify({
                group: 'bottom',
                title: 'Success',
                text: res.data?.message,
              }, 4000);
              this.$router.push({ name: 'RenewalEdit', params: { id: id } });
            }
          }).catch(err => {
            console.error(err)
            this.$notify({
              group: 'bottom',
              title: 'Error',
              text: err?.response?.data?.message,
            }, 4000);
          })
        },
      });
    },

    canUpdate(submitStatus) {
      let canUpdatePermission = hasPermission('RENEWAL', 'UPDATE')
      if (!canUpdatePermission) return false

      return submitStatus === 'PND' || submitStatus === 'PRG'
    },
    canRevise(submitStatus) {
      let canRevisePermission = hasPermission('RENEWAL', 'REVISE')
      if (!canRevisePermission) return false

      return submitStatus === 'REJ'
    },
    openReportDialog() {
      this.showReportDialog = true
    },
    hideDialog() {
      this.showReportDialog = false
    },
    async exportReport(formData) {
      this.isExporting = true
      await renewalService.export(formData).then(res => {
        const fileName = res.headers['content-disposition'].split('; ')
          .filter(item => item.startsWith('filename'))[0]
          .replace(/"/g, '')
          .replace('filename=', '')

        const a = document.createElement('a')
        const file = new Blob([res.data], {
          type: res.headers["content-type"],
        });
        a.href = URL.createObjectURL(file);
        a.download = fileName;
        a.click();
      })
        .catch(err => {
          console.error(err)
          this.$notify({
            group: 'bottom',
            title: 'Error',
            text: 'Something went wrong.',
          }, 4000);
        })
      this.isExporting = false
    },
  },
}
</script>