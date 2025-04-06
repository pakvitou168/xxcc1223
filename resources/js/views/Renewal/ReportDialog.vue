<template>
  <div>
    <Dialog :visible="isVisible" :style="{ width: '20rem' }" :modal="true" :closable="false" :header="header">
      <div class="grid gap-5">
        <div>
          <label for="" class="form-label">From Date</label>
          <Calendar v-model="form.expired_date_from" placeholder="From Date" showIcon />
        </div>
        <div>
          <label for="" class="form-label">To Date</label>
          <Calendar v-model="form.expired_date_to" placeholder="To Date" showIcon />
        </div>
      </div>
      <template #footer>
        <Button label="Cancel" class="p-button-danger p-button-text" :disabled="loading" @click="hideDialog"></Button>
        <Button autofocus class="p-button-info" :disabled="loading" @click="exportReport">Export<span v-if="loading">ing...</span></Button>
      </template>
    </Dialog>
  </div>
</template>

<script>
import moment from 'moment';

  export default {
    props: {
      header: String,
      isVisible: Boolean,
      loading: Boolean,
    },
    data() {
      return {
        form: {
          expired_date_from: '',
          expired_date_to: '',
        },
      }
    },
    methods: {
      hideDialog() {
        this.form = {
          expired_date_from: '',
          expired_date_to: '',
        }
        this.$emit('hideDialog')
      },
      exportReport() {
      this.$emit('export', {
        expired_date_from: moment(this.form.expired_date_from).format('YYYY-MM-DD'),
        expired_date_to: moment(this.form.expired_date_to).format('YYYY-MM-DD')
      })
      },
    },
  }
</script>