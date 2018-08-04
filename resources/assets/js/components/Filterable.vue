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
<script type="text/javascript">
import Vue from 'vue'
import axios from 'axios'

export default {
    props: {
        url: String,
        filterGroups: Array,
        orderables: Array
    },
    data() {
        return {
            loading: true,
            appliedFilters: [],
            filterCandidates: [],
            query: {
                order_column: 'created_at',
                order_direction: 'desc',
                filter_match: 'and',
                limit: 10,
                page: 1
            },
            collection: {
                data: []
            }
        }
    },
    computed: {
        fetchOperators() {
            return (f) => {
                return this.availableOperators().filter((operator) => {
                    if(f.column && operator.parent.includes(f.column.type)) {
                        return operator
                    }
                })
            }
        },
    },
    mounted() {
        this.fetch()
        this.addFilter()
    },
    methods: {
        updateOrderDirection() {
            if(this.query.order_direction === 'desc') {
                this.query.order_direction = 'asc'
            } else {
                this.query.order_direction = 'desc'
            }
            this.applyChange()
        },
        updateOrderColumn(e) {
            const value = e.target.value
            Vue.set(this.query, 'order_column', value)
            this.applyChange()
        },
        exportToCSV() {
            // next video
        },
        resetFilter() {
            this.appliedFilters.splice(0)
            this.filterCandidates.splice(0)
            this.addFilter()
            this.query.page = 1
            this.applyChange()
        },
        applyFilter() {
            Vue.set(this.$data, 'appliedFilters',
                JSON.parse(JSON.stringify(this.filterCandidates))
            )
            this.query.page = 1;
            this.applyChange()
        },
        removeFilter(f, i) {
            this.filterCandidates.splice(i, 1)
        },
        selectOperator(f, i, e) {
            let value = e.target.value
            if(value.length === 0) {
                Vue.set(this.filterCandidates[i], 'operator', value)
                return
            }

            let obj = JSON.parse(value)

            Vue.set(this.filterCandidates[i], 'operator', obj)

            this.filterCandidates[i].query_1 = null
            this.filterCandidates[i].query_2 = null

            // set default query

            switch(obj.name) {
                case 'in_the_past':
                case 'in_the_next':
                    this.filterCandidates[i].query_1 = 28
                    this.filterCandidates[i].query_2 = 'days'
                    break;
                case 'in_the_peroid':
                    this.filterCandidates[i].query_1 = 'today'
                    break;
            }
        },
        selectColumn(f, i, e) {
            let value = e.target.value
            if(value.length === 0) {
                Vue.set(this.filterCandidates[i], 'column', value)
                return
            }

            let obj = JSON.parse(value)

            Vue.set(this.filterCandidates[i], 'column', obj)

            // set default operator: todo
            switch(obj.type) {
                case 'numeric':
                    this.filterCandidates[i].operator = this.availableOperators()[4]
                    this.filterCandidates[i].query_1 = null
                    this.filterCandidates[i].query_2 = null
                    break;
                case 'string':
                    this.filterCandidates[i].operator = this.availableOperators()[6]
                    this.filterCandidates[i].query_1 = null
                    this.filterCandidates[i].query_2 = null
                    break;
                case 'datetime':
                    this.filterCandidates[i].operator = this.availableOperators()[9]
                    this.filterCandidates[i].query_1 = 28
                    this.filterCandidates[i].query_2 = 'days'
                    break;
                case 'counter':
                    this.filterCandidates[i].operator = this.availableOperators()[14]
                    this.filterCandidates[i].query_1 = null
                    this.filterCandidates[i].query_2 = null
                    break;
            }
        },
        addFilter() {
            this.filterCandidates.push({
                column: '',
                operator: '',
                query_1: null,
                query_2: null
            })
        },
        applyChange() {
            this.fetch()
        },
        updateLimit() {
            this.query.page = 1
            this.applyChange()
        },
        prevPage() {
            if(this.collection.prev_page_url) {
                this.query.page = Number(this.query.page) - 1
                this.applyChange()
            }
        },
        nextPage() {
            if(this.collection.next_page_url) {
                this.query.page = Number(this.query.page) + 1
                this.applyChange()
            }
        },
        getFilters() {
            const f = {}

            this.appliedFilters.forEach((filter, i) => {
                f[`f[${i}][column]`] = filter.column.name
                f[`f[${i}][operator]`] = filter.operator.name
                f[`f[${i}][query_1]`] = filter.query_1
                f[`f[${i}][query_2]`] = filter.query_2
            })

            return f
        },
        fetch() {
            this.loading = true
            const filters = this.getFilters()

            const params = {
                ...filters,
                ...this.query
            }
            axios.get(this.url, {params: params})
                .then((res) => {
                    Vue.set(this.$data, 'collection', res.data.collection)
                    this.query.page = res.data.collection.current_page
                })
                .catch((error) => {

                })
                .finally(() => {
                    this.loading = false
                })
        },
        availableOperators() {
            return [
                {title: 'equal to', name: 'equal_to', parent: ['numeric', 'string'], component: 'single'},
                {title: 'not equal to', name: 'not_equal_to', parent: ['numeric', 'string'], component: 'single'},
                {title: 'less than', name: 'less_than', parent: ['numeric'], component: 'single'},
                {title: 'greater than', name: 'greater_than', parent: ['numeric'], component: 'single'},

                {title: 'between', name: 'between', parent: ['numeric'], component: 'double'},
                {title: 'not between', name: 'not_between', parent: ['numeric'], component: 'double'},

                {title: 'contains', name: 'contains', parent: ['string'], component: 'single'},
                {title: 'starts with', name: 'starts_with', parent: ['string'], component: 'single'},
                {title: 'ends with', name: 'ends_with', parent: ['string'], component: 'single'},

                {title: 'in the past', name: 'in_the_past', parent: ['datetime'], component: 'datetime_1'},
                {title: 'in the next', name: 'in_the_next', parent: ['datetime'], component: 'datetime_1'},
                {title: 'in the peroid', name: 'in_the_peroid', parent: ['datetime'], component: 'datetime_2'},

                {title: 'equal to', name: 'equal_to_count', parent: ['counter'], component: 'single'},
                {title: 'not equal to', name: 'not_equal_to_count', parent: ['counter'], component: 'single'},
                {title: 'less than', name: 'less_than_count', parent: ['counter'], component: 'single'},
                {title: 'greater than', name: 'greater_than_count', parent: ['counter'], component: 'single'},
            ]
        }
    }
}
</script>