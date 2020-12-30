<template>
    <form class="form-inline">
        <div style="display: inline-block">
            <div class="form-group">
                <label for="user-status">State:</label>
                <select class="form-control" id="inactive-user" v-model="inactiveUser" @change="changeFilter">
                    <option value="0">Active</option>
                    <option value="1">Inactive</option>
                    <option value="-1">All</option>
                </select>
            </div>
        </div>
    </form>
</template>

<style>
    .form-group {
        margin-right: 15px;
    }
</style>

<script>
    export default {
        data: function () {
            return {
                inactiveUser: 0
            }
        },
        methods: {
            changeFilter: function () {
                let data = {
                    inactive: this.inactiveUser,
                };
                Event.$emit('moduleFilterHasChanged', data);
            }
        },
        mounted() {
            let data = {
                inactive: this.inactiveUser,
            };
            Event.$emit('moduleFilters', data);
        }
    }
</script>