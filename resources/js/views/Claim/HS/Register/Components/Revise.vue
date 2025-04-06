<template>
    <Dialog :visible="isVisible" :style="{ width: '340px' }" :modal="true" :closable="false" :header="header">
        <form @submit.prevent="confirmRevise">
            <div class="">
              <label for="reason" class="font-bold block mb-2">Reason *</label>
              <textarea
                  id="reason"
                  v-model="form.comment"
                  :class="{'p-invalid': errorComment}"
                  name="comment"
                  placeholder="Reason"
                  :autoResize="true"
                  required
                  class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  rows="3"
              />
              <small v-if="errorComment" class="p-error block mt-1">{{ errorComment }}</small>
            </div>
            <button type="submit" id="btnSubmit" class="btn btn-primary " style="display:none;"></button>
        </form>
        <template #footer>
            <Button label="Cancel" severity="danger" @click="hideDialog" />
            <Button label="Confirm" class="p-button-info px-2 py-2 border border-blue-500 text-white hover:bg-blue-600 bg-blue-500" :loading="submitted" autofocus @click="invokeSubmit" />
        </template>
    </Dialog>
</template>

<script>
export default {
    props: {
        header: String,
        isVisible: Boolean,
        submitted: Boolean,
        options: Object,
        value: String,
    },
    data() {
        return {
            ERROR_MESSAGE:"Something went wrong!",
            SUCCESS_MESSAGE:"Success!",
            form: {
                comment: '',
            },
        }
    },
    computed: {
        errorComment() {
            return (this.submitted && !this.form.comment) ? 'Comment is required.' : null
        },
    },
    methods: {
        hideDialog() {
            this.form = {
                comment: '',
            }
            this.$emit('hideDialog', this.form)
        },
        confirmRevise() {
            this.$emit('confirm', this.form)
        },
        invokeSubmit(){
            document.getElementById('btnSubmit').click();
        }
    },
}
</script>