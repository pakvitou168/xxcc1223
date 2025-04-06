<template>
    <Tree
      v-if="!loading"
      :value="optionMapping"
      selectionMode="checkbox"
      v-model:selectionKeys="selectedOptions"
      class="user-tree single-tree-container"
      filter
    />
    <div v-else class="bg-white rounded-md border px-4 py-4 text-center">Loading...</div>
  </template>
  
  <script setup>
  import Tree from "primevue/tree";
  import { computed } from "vue"
  
  /** modelValue | options => [{id: '', name: ''}]*/
  const props = defineProps({
    modelValue: {
      type: Array,
      default: [],
    },
    options: {
      type: Array,
      default: [],
    },
    loading: {
      type: Boolean,
      default: true,
    }
  });
  
  const emit = defineEmits(['update:modelValue'])
  
  const selectedOptions = computed({
    get: () => {
      return props.modelValue.reduce((newItem, item) => {
        return {
          ...newItem,
          [item.id]: {
            "checked": true, 
            "partialChecked": false
          }
        }
      }, {})
    },
    set: (selectedOptions) => {
      let options = Object.keys(selectedOptions).map(key => {
        return {
          id: key,
          name: props.options.filter(item => item.id == key)[0]?.name
        }
      })
  
      emit('update:modelValue', options)
    }
  })
  
  const optionMapping = computed(() => {
    return props.options.map(item => {
      return {
        key: item?.id,
        label: item?.name,
      }
    })
  });
  </script>
  
  <style>
  .single-tree-container .p-tree-toggler {
    display: none !important
  }
  
  .single-tree-container .p-treenode-content {
    padding: 0.3rem 0.3rem 0.3rem 0.4rem !important;
  }
  </style>