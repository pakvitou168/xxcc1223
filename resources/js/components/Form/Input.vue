<template>
  <div>
    <input
      class="w-full py-4 border-2 focus:outline-none"
      :label="label"
      :value="name"
    />
  </div>
</template>

<script>
export default {
  props: ['label', 'name'],
  data() {
    return {}
  },
}
</script>