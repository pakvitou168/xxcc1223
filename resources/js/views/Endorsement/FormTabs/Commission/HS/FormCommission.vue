<template>
  <div>
    <LoadingIndicator v-if="isLoading" />
    <div
      v-else
      class="pt-5 pb-2 border-b border-gray-300 first:pt-0 last:border-b-0"
    >
      <CommissionItem
        v-if="formValues"
        :id="id"
        :commission="formValues"
        :dataId="dataId"
        :canSave="true"
        @triggerCommissionInputChange="getCommissionData"
        :businessName="businessName"
      />
    </div>
  </div>
</template>
<script>
import CommissionItem from "./CommissionItem.vue";
import LoadingIndicator from "@/components/LoadingIndicator.vue";

export default {
  components: {
    CommissionItem,
    LoadingIndicator,
  },
};
</script>