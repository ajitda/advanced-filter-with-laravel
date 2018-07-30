<template>
    <div class="container">
        <div class="navbar"></div>
        <div className="content">
            <filterable v-bind="filterable"></filterable>
        </div>
    </div>
</template>
< script>
    import Filterable from './components/Filterable.vue';
    export default {
        components: { Filterable },
        data() {
            return {
                filterable:{
                    url: '/api/customers'
            }
        }
    }
</script>