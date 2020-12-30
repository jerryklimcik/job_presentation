import Vue from 'vue';
import DataTable from './Components/DataTable.vue';
import SimpleSearch from './Components/Searches/SimpleSearch/SimpleSearch.vue';
import AdminUserModuleFilter from './Components/ModuleFilter.vue';
import $ from 'jquery';

window.$jquery = $;
window.Event = new Vue();
datatable_data.columns = JSON.parse(datatable_data.columns);

new Vue({
    el: '#grid',
    components: {
        DataTable,
        SimpleSearch,
        AdminUserModuleFilter
    },
    data: {
        columns: datatable_data.columns,
        data_route: datatable_data.data_route,
        module_route: datatable_data.module_uri,
        selectRowsText: 'Show only selected rows',
        moduleFilterData: {},
        datatable_data: datatable_data,
    },
    methods: {
        exportTable: function () {
            Event.$emit('exportTable');
        },
        showSelectedRows: function () {
            Event.$emit('showSelectedRows');
        },
    },
    created() {
        Event.$on('changeTextSelectedRows', (selected) => {
            if (selected) {
                this.selectRowsText = 'Show all rows';
                return;
            }
            this.selectRowsText = 'Show only selected rows';
        });
        
        Event.$on('moduleFilters', (filters) => {
            this.moduleFilterData = filters;
        });

        Event.$on('moduleFilterHasChanged', (filters) => {
            this.moduleFilterData = filters;
            Event.$emit('moduleFilterChanged', filters);
        });
    }
});