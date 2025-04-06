<template>
    <div class="col-span-12 mt-4">
        <form @submit.prevent="handleSubmit">
            <div class="grid grid-cols-2">
             <div>
               <label for="endorsement_description" class="block mb-1 font-bold">Endorsement description *</label>
               <CKEditor
                   v-model="formDes.endorsement_description"
                   placeholder="Endorsement description"
               />
               <div class="h-6">
                 <small v-if="errors.tax_fee" class="p-error block mt-1">{{ errors.tax_fee[0] }}</small>
               </div>
             </div>
            </div>
            <div class="grid  grid-cols-2">
                <div class="text-right ">
                    <button type="button" class="btn btn-secondary w-24 mr-3"
                        @click="$router.push({ name: 'HSEndorsementIndex' })">
                        <span>Cancel</span>
                    </button>
                    <button type="submit" :disabled="saving" class="btn btn-primary w-24">
                        <span v-if="!saving">Save</span>
                        <span v-else>Saving...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
<script>
import axios from 'axios';
import CKEditor from "@/components/Form/CKEditor.vue";


export default {
  components: {CKEditor},
    props: {
        id: [Number, String],
        desc: [String]
    },
    data() {
        return {
            formDes: {

            },
            saving: false,
            errors:{},
        }
    },
    watch: {
        desc: {
            deep: true,
            handler(newVal, oldVal) {
                this.formDes.endorsement_description = newVal
            }
        }
    },
    methods: {
        handleSubmit() {
            this.saving = true
            axios.post('/hs/endorsements/' + this.id + '/update-description', this.formDes).then((res) => {
                this.saving = false
               notify(res.data?.message, "Success");
            }).catch((err) => {
               notify(err.response?.data?.message, "Error");
            })
        },
        toastMessage(msg, type, position = "bottom") {
            this.$notify(
                {
                    group: position,
                    title: type,
                    text: msg,
                },
                4000
            );
        },
    }
}
</script>