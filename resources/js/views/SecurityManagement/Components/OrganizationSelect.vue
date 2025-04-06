<template>
  <Tree
    :value="organizationOptions"
    :expandedKeys="expandedKeys"
    selectionMode="checkbox"
    v-model:selectionKeys="selectedOrganizations"
    class="user-tree"
    filter
  />
</template>

<script setup>
import { computed, ref, onMounted, } from "vue";
import Tree from "primevue/tree";
import { listTreeOrganizationOptions, listTreeSelectedOrganizations, getSelectedOrganizations, getExpandedKeys } from './organization.select.service.js'

const props = defineProps({
  modelValue: {
    type: Array,
    default: [],
  },
  organizationsList: {
    type: Array,
    default: [],
  },
});

const emit = defineEmits(['update:modelValue'])

const expandedKeys = ref({})

const organizationOptions = computed(() => {
  return listTreeOrganizationOptions(props.organizationsList)
})

const selectedOrganizations = computed({
  get: () => {
    return listTreeSelectedOrganizations(organizationOptions.value, props.modelValue)
  },
  set: (selectedOptions) => {
    let organizations = getSelectedOrganizations(organizationOptions.value, selectedOptions)
    emit('update:modelValue', organizations)
  }
})

onMounted(() => {
  expandedKeys.value = getExpandedKeys(selectedOrganizations.value)
})

</script>