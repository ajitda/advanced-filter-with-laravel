<template>
    <div class="container">
        <div class="navbar"></div>
        <div className="content">
            <filterable v-bind="filterable">
                <thead slot="thead">
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Group</th>
                    <th>Total Revenue</th>
                </tr>
                </thead>
                <tr slot-scope="{item}">
                    <td>{{item.id}}</td>
                    <td>{{item.name}}</td>
                    <td>{{item.company}}</td>
                    <td>{{item.email}}</td>
                    <td>{{item.group}}</td>
                    <td>${{Number(item.total_revenue).toFixed(2)}}</td>
                </tr>
            </filterable>
        </div>
    </div>
</template>
< script
type="text/javascript">
    import Filterable from './components/Filterable.vue'
    export default {
        components: {Filterable},
        data() {
            return {
                filterable: {
                    url: '/api/customers'
                }
            }
        }
    }
</script>