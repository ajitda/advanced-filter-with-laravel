<template>
    <div class="filterable">
        <div class="panel">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Customers Match</span>
                    <select name="" id="">
                        <option value="and">All</option>
                        <option value="or">Any</option>
                    </select>
                    <span> of the following: </span>
                </div>
                <div class="panel-body">

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
            fetch() {
                axios.get(this.url)
                    .then((res)=> {
                        Vue.set(this.$data, 'collection', res.data.collection)
                        this.query.page =
                    })
            }
        }
    }
</script>