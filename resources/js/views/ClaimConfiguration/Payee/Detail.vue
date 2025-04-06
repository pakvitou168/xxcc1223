<template>
  <div>
    <div class="intro-y box p-5 mt-5">
      <div class="overflow-x-auto scrollbar-hidden">
        <div class="w-full mb-4 table">
          <div class="intro-y flex mb-4 p-1">
              <h2 class="text-xl font-medium mr-auto">Payee Detail</h2>
              <button  v-if="canDelete" class="btn btn-danger mx-1 intro-x" @click="handleDelete(id)">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
              <router-link v-if="canUpdate" :to="{ name: 'PayeeEdit', params: { id: id }}">
                  <button class="btn btn-primary mx-1 intro-x">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </button>
              </router-link>
          </div>
          <hr>
          <div class="flex m-2">
              <span class="text-base w-1/4 intro-x">Name (English)</span>
              <p class="text-base text-bold intro-x">{{ formValues.name_en }}</p>
          </div>
          <hr>
          <div class="flex m-2">
              <span class="text-base w-1/4 intro-x">Name (Khmer)</span>
              <p class="text-base text-bold intro-x">{{ formValues.name_kh }}</p>
          </div>
          <hr>
          <div class="flex m-2">
              <span class="text-base w-1/4 intro-x">Payee Type</span>
              <p class="text-base text-bold intro-x">{{ formValues.type ? formValues.payee_type.name : ''}}</p>
          </div>
          <hr>
          <div class="flex m-2">
              <span class="text-base w-1/4 intro-x">Phone Number</span>
              <p class="text-base text-bold intro-x">{{ formValues.phone_number }}</p>
          </div>
          <hr>
          <div class="flex m-2">
              <span class="text-base w-1/4 intro-x">Address</span>
              <p class="text-base text-bold intro-x">{{ formValues.address }}</p>
          </div>
          <hr>
          <div class="flex m-2">
              <span class="text-base w-1/4 intro-x">Description</span>
              <p class="text-base text-bold intro-x">{{ formValues.description }}</p>
          </div>
          <hr>
          <div class="text-right mt-5">
                <router-link :to="{name: 'PayeeIndex'}" class="btn btn-primary w-24 mr-1" tag="button">Back</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import PayeeService from '@/services/claim/payee.service'
import { hasPermission } from '@/services/auth.service'

export default {
  data() {
    return {
      id: this.$route.params.id,
      formValues: {},
      canUpdate: hasPermission('PAYEE', 'UPDATE'),
      canDelete: hasPermission('PAYEE', 'DELETE'),
    }
  },
  computed: {
    getIssueDate() {
      if (!this.formValues?.license_issue_date) return ''
      return new Date(this.formValues.license_issue_date).toLocaleDateString()
    },
    getExpiredDate() {
      if (!this.formValues?.license_expire_date) return ''
      return new Date(this.formValues.license_expire_date).toLocaleDateString()
    },
  },
  methods: {
    getData() {
      if (this.id) {
        PayeeService.getData(this.id).then(res => {
          this.formValues = res.data
        })
        .catch(err => {
          notify('Validation Error', 'error', 'bottom-right');
        })
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
          PayeeService.delete(id).then(res => {
            if (res.data.success) {
              notify(res.data?.message, 'success', 'bottom-right');
              this.$router.push({ name: 'PayeeIndex' });
            }
          }).catch(err => {
            console.log(err)
              notify('Validation Error', 'error', 'bottom-right');

          })
        },
      });
    },
  },
  mounted() {
    this.getData();
  }
}
</script>
