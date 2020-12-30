<template>
    <div>
        <div class="alert alert-danger" v-show="showError">
            Error occurred. Try to refresh page.
        </div>
        <div v-show="!showTable" class="loading-table">
            <div class="wrapper-loading-table">
                <i class="fa fa-cog fa-spin fa-3x fa-fw"></i><br> <span>Loading...</span>
            </div>
        </div>
        <table class="table table-bordered datatable" v-show="showTable">
            <tbody></tbody>
            <tfoot v-show="Object.keys(summary).length > 0">
            <tr>
                <template v-for='column in columnsInfo'>
                    <th>
                        <template v-for="(summary_value, summary_column_name) in summary">
                            <template v-if="column.name == summary_column_name">
                                <template v-for="value in summary_value.value">
                                    {{ value }} <br>
                                </template>
                            </template>
                        </template>
                    </th>
                </template>
            </tr>
            </tfoot>
        </table>
        <delete-modal :module-route="datatableData.module_route"></delete-modal>
    </div>
</template>

<style>
    td, th {
        width: 1px;
        white-space: nowrap;
    }

    .dataTables_filter {
        display: none;
    }

    .search-in-selected-column {
        background-color: red;
        color: yellow;
    }
</style>

<script>
    import 'datatables.net-bs';
    import 'datatables.net-select';
    import 'datatables.net-buttons';
    import 'datatables.net-buttons/js/buttons.html5';
    import 'datatables.net-buttons-bs/js/buttons.bootstrap';
    import Axios from 'axios';

    import Language from './Datatable/Language';
    import Search from './Datatable/Search';
    import SelectedRows from './Datatable/SelectedRows';
    import DeleteModal from './DeleteModal.vue';

    export default {
        props: ['datatableData', 'previousModuleFilterData', 'moduleFilterData'],
        components: {
            DeleteModal
        },
        data: function () {
            return {
                showTable: false,
                showError: false,
                dtHandle: null,
                columnsInfo: [],
                order: [[0, 'desc']],
                primaryColumn: 0,
                exportColumns: [],
                summary: '',
                newColumns: this.datatableData.columns.map(a => Object.assign({}, a)),
                newModuleFilterData: this.moduleFilterData,
                selectedRows: new SelectedRows(),
                search: new Search(),
            }
        },
        computed: {
            finalFilters: function () {
                let filters = {};
                try {
                    filters = JSON.parse(this.search.filter);
                }
                catch (e) {
                    console.log(e);
                }

                filters.selectedRows = [...this.selectedRows.rows];
                return filters;
            }
        },
        watch: {
            moduleFilterData: function (newData) {
                this.newModuleFilterData = newData;
            }
        },
        methods: {
            columnTitle: function (column) {
                if (column.name === 'row_number') {
                    return column.title;
                }

                if (column.title === null || column.title === '') {
                    return '';
                }

                return column.title;
            },

            capitalize: function (param) {
                if (param === null) {
                    return null;
                }
                return param.charAt(0).toUpperCase() + param.slice(1);
            },

            find: function (parameters) {
                this.search.find(parameters);
                this.dtHandle.search(JSON.stringify(this.finalFilters)).draw();
            },

            cancelSearch: function () {
                this.search.clear();
                this.selectedRows.clear();
                this.sendFilter('');
                this.find(this.finalFilters);
            },

            exportTable: function () {
                $jquery(this.$el).find('table').DataTable().buttons('.buttons-excel').trigger();
            },

            changeRowCount: function (rowNumber) {
                this.dtHandle.page.len(rowNumber).draw();
            },

            showSelectedRows: function () {
                let newRowSet = new Set();
                this.dtHandle.rows({selected: true}).every((index) => {
                    newRowSet.add(this.dtHandle.row(index).id());
                });

                this.selectedRows.addNewRows(newRowSet);
                this.dtHandle.rows({selected: true}).deselect();

                this.find(this.finalFilters);

                $jquery(this.$el).find('table').DataTable().on('draw.dt', function () {
                    Event.$emit('changeTextSelectedRows', newRowSet.size !== 0);
                });
            },

            sendFilter: function (parameter) {
                Event.$emit('hasFilter', parameter);
            },

            sendRowCount: function (parameter) {
                Event.$emit('rowCount', parameter);
            },

            sendModuleExtendedFilters(module) {
                Event.$emit('moduleExtendedFilters', this.search.filter, module, this.newModuleFilterData);
            },

            moduleFilterChanged(filters) {
                this.newModuleFilterData = filters;
                this.find(this.finalFilters);
            },

            simpleSearchChangedColumn(selectedColumn) {
                $('.datatable tr td').removeClass('search-in-selected-column');
                $('.datatable tr td.' + selectedColumn).addClass('search-in-selected-column');
            },

            showDeleteModal() {
                $(document).on('click', 'a.deleteModal', function () {
                    Event.$emit('showDeleteModal', $(this).data('id'));
                });
            },

            refreshTable() {
                this.dtHandle.ajax.reload(null, false);
            },
        },
        created() {
            Event.$on('search', this.find);
            Event.$on('cancelSearch', this.cancelSearch);
            Event.$on('showSelectedRows', this.showSelectedRows);
            Event.$on('exportTable', this.exportTable);
            Event.$on('changeRowCount', this.changeRowCount);
            Event.$on('simpleSearchChangedColumn', this.simpleSearchChangedColumn);
            Event.$on('refreshTable', this.refreshTable);
            Event.$on('sendModuleExtendedFilters', this.sendModuleExtendedFilters);
            Event.$on('moduleFilterChanged', this.moduleFilterChanged);

            this.newColumns.map((column, index) => {
                let columnData = {
                    data: index,
                    name: column.name,
                    orderable: column.hasOwnProperty('orderable') ? column.orderable : false,
                    searchable: column.hasOwnProperty('searchable') ? column.searchable : false,
                    visible: column.hasOwnProperty('visible') ? column.visible : false,
                    class: column.hasOwnProperty('class') ? column.class + ' ' + column.name : column.name,
                    title: this.columnTitle(column)
                };
                this.columnsInfo.push(columnData);

                if (column.hasOwnProperty('order')) {
                    this.order[0] = [index, column.order];
                }

                if (column.hasOwnProperty('primaryColumn') && column.primaryColumn) {
                    this.primaryColumn = index;
                }

                if (column.hasOwnProperty('export') && column.export === true) {
                    this.exportColumns.push(index);
                }
            });

            this.showDeleteModal();
        },
        mounted() {
            this.dtHandle = $jquery(this.$el).find('table').DataTable({
                dom: 'frtip',
                pageLength: 10,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: this.exportColumns
                        }
                    },
                ],
                processing: true,
                serverSide: true,
                select: {
                    style: 'multi'
                },
                colReorder: true,
                rowId: this.primaryColumn,
                ajax: (data, callback) => {
                    data.externalFilter = !this.showTable;

                    if (this.previousModuleFilterData !== undefined && this.previousModuleFilterData.length > 0 && !this.showTable) {
                        data.previousModuleFilterData = this.previousModuleFilterData;
                    }

                    if (this.newModuleFilterData !== undefined && Object.keys(this.newModuleFilterData).length > 0) {
                        data.moduleFilterData = this.newModuleFilterData;
                    }

                    let config = {
                        headers: {'X-Requested-With': 'XMLHttpRequest'}  // Laravel recognition
                    };
                    axios.post(this.datatableData.data_route, {
                        data: data,
                    }, config).then((response) => {
                        if (response.data.errorInfo) {
                            this.showError = true;
                            console.log(response.data.errorInfo);
                        } else {
                            if (!this.showTable) {
                                this.sendFilter(response.data.input.data.search);
                                this.sendRowCount(response.data.input.data.length);
                            }
                            this.showTable = true;
                            this.summary = response.data.summary;
                            this.search.filter = response.data.input.data.search.value;
                            this.showError = false;
                            callback(response.data);
                        }
                    }).catch((error) => {
                        this.showError = true;
                        console.log(error);
                    });
                },
                order: this.order,
                columns: this.columnsInfo,
                language: Language.getTranslator(),
            });
        },
        beforeDestroy() {
            Event.$off('sendModuleExtendedFilters', this.sendModuleExtendedFilters);
            Event.$off('moduleFilterChanged', this.moduleFilterChanged);
            Event.$off('cancelSearch', this.cancelSearch);
            Event.$off('search', this.find);
            Event.$off('showSelectedRows', this.showSelectedRows);
            Event.$off('exportTable', this.exportTable);
            Event.$off('changeRowCount', this.changeRowCount);
        },
    }
</script>