<template>
  <Tree
    :value="permissionOptions"
    :expandedKeys="expandedKeys"
    selectionMode="checkbox"
    v-model:selectionKeys="selectedPermissions"
    class="user-tree"
    filter
  />
</template>

<script setup>
import { computed, ref, onMounted, watch, } from "vue";
import Tree from "primevue/tree";
import { listTreePermissionOptions, listTreeSelectedPermissions, getSelectedPermissions, getExpandedKeys } from './permission.select.service.js'

const props = defineProps({
  modelValue: {
    type: Array,
    default: [],
  },
  permissionsList: {
    type: Array,
    default: [],
  },
});

const emit = defineEmits(['update:modelValue', 'get:selectedAppNames'])

const expandedKeys = ref({})

const permissionOptions = computed(() => {
  return listTreePermissionOptions(props.permissionsList)
})

const selectedPermissions = computed({
  get: () => {
    return listTreeSelectedPermissions(props.modelValue, permissionOptions.value)
  },
  set: (selectedOptions) => {
    let permissions = getSelectedPermissions(permissionOptions.value, selectedOptions)

    emit('update:modelValue', permissions)
  }
})

const getSelectedAppNames = () => {
  console.log(props.modelValue);
  const uniqueSelectedAppId = [...new Set(props.modelValue.map(item => item.app_id))]
  return permissionOptions.value.filter(item => uniqueSelectedAppId.includes(item.data)).map(item => item.label)
}

onMounted(() => {
  expandedKeys.value = getExpandedKeys(selectedPermissions.value)
  emit('get:selectedAppNames', getSelectedAppNames())
})

watch(() => selectedPermissions.value, () => {
  emit('get:selectedAppNames', getSelectedAppNames())
});

</script>