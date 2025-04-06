<template>
    <div>
        <div class="intro-y box p-5 mt-5">
            <div class="overflow-x-auto scrollbar-hidden">
                <div class="w-full mb-4 table">
                    <div>
                        <div>
                            <div class="intro-y flex mb-4 p-1">
                                <h2 class="text-xl font-medium mr-auto">Component Formula Details</h2>
                                <button v-if="canDelete" class="btn btn-danger mx-1 intro-x" v-on:click="handleDelete(id)">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                <router-link v-if="canUpdate" :to="{ name: 'FormulaUpdate', params: { id: id }}">
                                    <button class="btn btn-primary mx-1 intro-x">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                </router-link>
                            </div>
                            <hr>
                            <div class="flex m-2">
                                <span class="text-base w-1/4 intro-x">Product</span>
                                <p class="text-base text-bold intro-x">{{ products.code }} - {{ products.name }}</p>
                            </div>
                            <hr>
                            <div class="flex m-2">
                                <span class="text-base w-1/4 intro-x">Cover Name</span>
                                <p class="text-base text-bold intro-x">{{ covers.name }}</p>
                            </div>
                            <hr>
                            <div class="flex m-2">
                                <span class="text-base w-1/4 intro-x">Calculate Option</span>
                                <p class="text-base text-bold intro-x">{{ formulas.calc_option }}</p>
                            </div>
                            <hr>
                            <div class="flex m-2">
                                <span class="text-base w-1/4 intro-x">Formula Code</span>
                                <p class="text-base text-bold intro-x">{{ formulas.formula_code }}</p>
                            </div>
                            <hr>
                            <div class="flex m-2">
                                <span class="text-base w-1/4 intro-x">Formula Calculate Sequence</span>
                                <p class="text-base text-bold intro-x">{{ formulas.frm_calc_seq }}</p>
                            </div>
                            <hr>
                            <div class="flex m-2">
                                <span class="text-base w-1/4 intro-x">Status</span>
                                <p class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full" v-if="formulas.status == 'ACT'">Active</p>
                                <p class="text-xs px-2 bg-theme-1 text-white mr-1 py-1 rounded-full" v-else>Delete</p>
                            </div>
                            <hr>
                            <div class="text-right mt-5">
                                 <router-link :to="{ name: 'FormulaIndex', params: { id: id }}">
                                      <button class="btn btn-primary w-24 mr-1">Back</button>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import UserPermissions from '../../../mixins/UserPermissions'

export default {
    mixins: [UserPermissions],

    data(){
        return{
            id: this.$route.params.id,
            functionCode: 'COMP_FORMULA',
            formulas:'',
            covers:'',
            products:'',
        }
    },
    methods: {
        getFormula(){
            axios.get('/formula/' + this.id)
            .then(response => {
                if(response){
                    this.formulas = response.data;
                    this.covers = this.formulas.prod_comp;
                    this.products = this.formulas.product;
                }
            })
        },
        handleDelete(id) {
            this.$confirm.require({
                message: 'Do you want to delete this record?',
                header: 'Delete',
                icon: 'pi pi-info-circle',
                acceptClass: "p-button-danger",
                blockScroll: false,
                accept: () => {
                    axios.delete(`/formula/${id}`).then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success','bottom-right')
                            this.$router.push({ name: 'FormulaIndex' });
                        }
                    }).catch(err => {
                        notify('Error', 'error','bottom-right')
                    })
                },
            });
        },
    },
    mounted(){
        this.getFormula();
    }
}
</script>
