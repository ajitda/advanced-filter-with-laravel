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
                </div>
            </div>
        </div>
    </div>
</template>
< script
type="text/javascript">
    export default {
        props: {
            url:string
        }
    }
    </script>