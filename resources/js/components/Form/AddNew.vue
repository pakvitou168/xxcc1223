<template>
  <div class="intro-y flex flex-col sm:flex-row items-center mt-5">
    <h2 class="text-lg font-medium mr-auto">{{ addNew.label }}</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
      <slot/>
        <button v-if="canExport" class="btn btn-success shadow-md mr-2" :title= "'Export ' + addNew.label" @click="$emit('export')">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </button>
      <div v-if="hasAddNew && addNew.linkAddNew">
        <router-link v-if="canAddNew" :to="addNew.linkAddNew" class="float-right">
          <button class="btn btn-primary shadow-md mr-2 leading-6"><i class="pi pi-plus text-xs mr-1"></i> {{ addNew.prefix }} {{ addNew.name }}</button>
        </router-link>
      </div>
    </div>
</div>
</template>

<script>
export default {
  props: {
    addNew: {
      type: Object,
      required: true,
    },
    canAddNew: {
      type: Boolean,
      default: true
    },
    hasAddNew: {
      default: true
    },
    canExport:{
        type: Boolean,
        default: false
    }
  },
  data() {
    return {}
  },
}
</script>
