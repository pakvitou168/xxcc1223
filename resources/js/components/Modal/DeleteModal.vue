<template>
  <!-- Modal -->
  <div
    id="delete-confirmation-modal"
    class="modal overflow-y-auto show"
    tabindex="-1"
    aria-hidden="false"
    v-show="value"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body p-0">
          <div class="p-5 text-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24px"
              height="24px"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="feather feather-x-circle w-16 h-16 text-theme-6 mx-auto mt-3 text-theme-6"
            >
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <div class="text-3xl mt-5">Delete Record</div>
            <div class="text-gray-600 mt-2">
              Are you sure want to delete this record?
            </div>
          </div>
          <div class="px-5 pb-8 text-center">
            <button
              type="button"
              data-dismiss="modal"
              class="btn btn-outline-secondary w-24 mr-1"
              @click="close"
            >
              Cancel
            </button>
            <button type="button" class="btn btn-danger w-24" @click="destroy">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'Modal',
  props: {
    value: {
      required: false,
    },
  },
  methods: {
    close() {
      this.$emit('input', !this.value)
    },

    destroy() {
      this.$emit('destroy')
    },
  },
}
</script>

<style scoped>
#delete-confirmation-modal {
  margin-top: 0px;
  margin-left: 0px;
  padding-left: 0px;
  z-index: 53;
}
</style>