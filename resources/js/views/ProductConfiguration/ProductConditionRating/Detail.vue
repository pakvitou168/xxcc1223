<template>
  <div>
    <div class="intro-y box p-5 mt-5">
      <div class="overflow-x-auto scrollbar-hidden">
        <div class="w-full mb-4 table">
          <div>
            <div>
              <div class="intro-y flex mb-4 p-1">
                <h2 class="text-xl font-medium mr-auto">Product Condition Rating Details</h2>
                <button v-if="canDelete" class="btn btn-danger mx-1 intro-x" @click="handleDelete(id)">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                <router-link v-if="canUpdate" :to="{ name: 'ProductConditionUpdate', params: { id: id }}">
                  <button class="btn btn-primary mx-1 intro-x">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </button>
                </router-link>
              </div>
              <div v-if="conditionRating.id">
                <hr>
                <div class="flex m-2">
                  <span class="text-base w-1/4 intro-x">Product Code</span>
                  <p class="text-base text-bold intro-x">{{ conditionRating.product_code }}</p>
                </div>
                <hr>
                <div class="flex m-2">
                  <span class="text-base w-1/4 intro-x">Code</span>
                  <p class="text-base text-bold intro-x">{{ conditionRating.code }}</p>
                </div>
                <hr>
                <div class="flex m-2">
                  <span class="text-base w-1/4 intro-x">Description</span>
                  <p class="text-base text-bold intro-x">{{ conditionRating.description }}</p>
                </div>
                <hr>
                <div class="flex m-2">
                  <span class="text-base w-1/4 intro-x">Value</span>
                  <p class="text-base text-bold intro-x">{{ conditionRating.value }}</p>
                </div>
                <hr>
                <div class="flex m-2">
                  <span class="text-base w-1/4 intro-x">Created At</span>
                  <p class="text-base text-bold intro-x">{{ formatDate(conditionRating.created_at) }}</p>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { hasPermission } from "@/services/auth.service";
import ProductConditionService from '@/services/product_config/product_condition_rating.service'
export default {
  data() {
    return {
      id: this.$route.params.id,
      canDelete: hasPermission("PRODUCT_CONDITION_RATING", "DELETE"),
      canUpdate: hasPermission("PRODUCT_CONDITION_RATING", "UPDATE"),
      conditionRating: {}
    }
  },
  methods: {
    getData(){
      ProductConditionService.getData(this.id).then(response => {
        this.conditionRating = response.data
      })
    },
    formatDate(date) {
      if (!date) return '';
      return moment(date).format('DD/MM/YYYY');
    },
    handleDelete(id){
      this.$confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Delete',
        icon: 'pi pi-info-circle',
        acceptClass: "p-button-danger",
        rejectClass: "p-button-secondary p-button-outlined",
        blockScroll: false,
        rejectLabel: 'Cancel',
        acceptLabel: 'Delete',
        accept: () => {
          ProductConditionService.delete(id).then(response => {
            if (response.data.success) {
              // refresh table
              notify('Success', 'success','bottom-right');
              // this.tabulator.replaceData()
              this.$router.push({ name: 'ProductConditionIndex' })
            }
          }).catch(err => {
            notify('Error', 'error','bottom-right');
          })
        },
      });
    },
  },
  mounted(){
    this.getData();
  }
}
</script>