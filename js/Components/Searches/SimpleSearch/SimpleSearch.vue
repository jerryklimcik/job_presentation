<template>
    <div>
        <div id="main-search">
            <div class="row" style="margin-bottom: 20px">
                <div class="input-group simple-search-group" id="nav_search" v-cloak>
                    <input id="simple-search-input1" v-model="searchInput1" v-on:keyup.enter="simpleSearch"
                            type="text" class="form-control" placeholder="Searching">
                    <span class="input-group-btn" style="display: inline">
				    <button id="search-andor-btn" @click="andorToggle" class="btn btn-default" type="button">
                        {{ operator == 'or' ? 'or' : 'and' }}
                    </button>
			    </span>
                    <input id="simple-search-input2" v-model="searchInput2" v-on:keyup.enter="simpleSearch" type="text" class="form-control" placeholder="Searching">
                    <select id="simple-search-selector" class="form-control" v-model="selectedColumn" @change="simpleSearchChangedColumn">
                        <option value="all">Search everywhere</option>
                        <option v-for="column in filteredColumns" :value="column.name">{{ capitalize(column.title) }}</option>
                    </select> <span class="input-group-btn" style="width: initial">
                    <button id="search-btn" class="btn btn-primary" v-on:click="simpleSearch"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
					<button id="cancel-search-btn" class="btn btn-primary" v-on:click="cancelSearch">
                        <i class="fa fa-times" aria-hidden="true" style="font-size: 18px"></i>
                    </button>
                </span>
                </div>
                <button type="button" id="extended-btn" class="btn btn-primary" data-toggle="collapse" data-target="#filter-panel">
                    <span class="glyphicon glyphicon-cog"></span> Extended searching
                </button>
                <div id="row-count-select" class="form-group form-inline">
                    <label for="row-count">Row count</label>
                    <select class="form-control" id="row-count" @change="changeRowCount($event)" v-model="rowCount">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="300">300</option>
                        <option value="-1">All</option>
                    </select>
                </div>
            </div>
        </div>

        <extended-search :datatable-data="datatableData"></extended-search>
    </div>
</template>

<style>
    #search-andor-btn {
        float: left;
        width: 58px;
        margin-left: -2px;
        margin-right: -2px;
        border-radius: 0
    }
</style>

<script>
    import ExtendedSearch from '../ExtendedSearch/ExtendedSearch.vue';

    export default {
        props: ['datatableData'],
        components: {
            ExtendedSearch
        },
        data: function () {
            return {
                rowCount: 30,
                selectedColumn: 'all',
                searchInput1: '',
                searchInput2: '',
                operator: 'or'
            }
        },
        computed: {
            filteredColumns: function () {
                return this.datatableData.columns.filter(function (item) {
                    return item.visible && item.searchable;
                }).sort(function (a, b) {
                    return a.sequence - b.sequence;
                });
            }
        },
        methods: {
            capitalize: function (param) {
                return param.charAt(0).toUpperCase() + param.slice(1);
            },
            andorToggle: function () {
                this.operator = this.operator === 'or' ? 'and' : 'or';
            },
            simpleSearch: function () {
                let search = {
                    type: 'simpleSearch',
                    searchInput1: this.searchInput1,
                    searchInput2: this.searchInput2,
                    operator: this.operator,
                    selectedColumn: this.selectedColumn
                };
                Event.$emit('search', search);
            },
            cancelSearch: function () {
                this.searchInput1 = '';
                this.searchInput2 = '';
                Event.$emit('cancelSearch');
                Event.$emit('showSelectedRows');
            },
            changeRowCount: function (event) {
                Event.$emit('changeRowCount', event.target.value);
            },

            simpleSearchChangedColumn: function () {
                Event.$emit('simpleSearchChangedColumn', this.selectedColumn);
            }
        },
        created() {
            Event.$on('hasFilter', (parameter) => {
                if (parameter.value !== "" && parameter.value) {
                    let filters = JSON.parse(parameter.value);
                    if (filters.type === 'simpleSearch') {
                        this.searchInput1 = filters.searchInput1;
                        this.searchInput2 = filters.searchInput2;
                        this.operator = filters.operator;
                        this.selectedColumn = filters.selectedColumn;
                    }
                }
            });

            Event.$on('rowCount', function (parameter) {
                this.rowCount = parameter;
            });
        }
    }
</script>