<template>
    <div class="filterable">
        <div class="panel">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Customers Match</span>
                    <select v-model="query.filter_match">
                        <option value="and">All</option>
                        <option value="or">Any</option>
                    </select>
                    <span> of the following: </span>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <slot name="thead"></slot>
                        <tbody>
                        <slot v-if="collection.data && collection.data.length" v-for="item in collection.data" :item="item"></slot>
                        </tbody>

                    </table>
                </div>
                <div className="panel-footer">
                    <div>
                        <select v-model="query.limit" :disabled="loading" @change="updateLimit">
                            <option>10</option>
                            <option>15</option>
                            <option>25</option>
                            <option>50</option>
                        </select>
                        <small>Showing {{collection.from}} - {{collection.to}} of {{collection.total}} entries. </small>
                    </div>
                    <div>
                        <button class="btn" :disabled="!collection.prev_page_url || loading" @click="prevPage">&laquo; Prev</button>
                        <button class="btn" :disabled="!collection.next_page_url || loading" @click="nextPage">Nex &raquo;</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue'
    import axios from 'axios'
    export default {
        props: {
            url:string
        },
        data() {
            return {
                loading: true,
                query: {
                    order_column: 'created_at',
                    order_direction: 'desc',
                    filter_match: 'and',
                    limit:10,
                    page:1
                },
                collection:{
                    data:[]
                }
            }
        },
        mounted(){
            this.fetch()
        },
        methods:{
            updateLimit(){

            },
            prevPage(){

            },
            nextPage(){

            },
            fetch() {
                axios.get(this.url)
                    .then((res)=> {
                        Vue.set(this.$data, 'collection', res.data.collection)
                        this.query.page = res.data.collection.current_page
                    })
                    .catch((error)=>{

                })
                    .finally(()=>{
                    this.loading = false
                    })
            }
        }
    }
</script>