<!-- DeleteButton.vue -->
<template>
  <div class="flex items-center gap-2">
    <button
      v-if="!isPending"
      class="rounded-full p-2 transition-colors duration-200 hover:bg-red-100"
      @mouseenter="isHovered = true"
      @mouseleave="isHovered = false"
      @click="handleClick"
    >
      <!-- Hovered State -->
      <svg
        v-if="isHovered"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="red"
        class="h-6 w-6"
      >
        <path
          fill-rule="evenodd"
          d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm3 10.5a.75.75 0 000-1.5H9a.75.75 0 000 1.5h6z"
          clip-rule="evenodd"
        />
      </svg>

      <!-- Default State -->
      <svg
        v-else
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2"
        class="h-6 w-6"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
    </button>

    <!-- Pending Delete State -->
    <div v-else class="text-red-500 font-medium">
      Pending Delete
    </div>
  </div>
</template>

<script>
export default {
  name: 'DeleteButton',
  props: {
    index: [Number, String],
  },
  data() {

    return {
      ERROR_MESSAGE: "Something went wrong!",
      SUCCESS_MESSAGE: "Success!",
      isHovered: false,
      isPending: false
    }
  },
  methods: {
    handleClick() {
      this.isPending = true;
      // Add your delete logic here
      // You might want to emit an event to parent
      this.$emit('delete-requested', this.index);
    }
  }
}
</script>