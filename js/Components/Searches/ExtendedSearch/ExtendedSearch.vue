<template>
    <div class="row ext-search">
        <div class="col-md-12" style="padding-right: 0">
            <div id="filter-panel" class="collapse filter-panel" v-bind:class="{ in: showFilter }">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-inline" role="form">
                            <div>
                                <div class="form-group">
                                    <select id="ext-search-column-1"
                                            class="form-control input-sm"
                                            @change="changeDefaultColumn($event)"
                                            v-model="extFilters.filters.default.column">
                                        <option value="all">Search everywhere</option>
                                        <option v-for="column in filteredColumns" :value="column.name">
                                            {{ capitalize(column.title) }}
                                        </option>
                                    </select>
                                </div>
                                <component :is="extFilters.filters.default.filter"
                                        :filterData="extFilters.filters.default"
                                        @updateText="extFilters.filters.default.text = $event"
                                        @updateOperator="extFilters.filters.default.operator = $event">
                                </component>
                                <div class="form-group">
                                    <button type="button" class="btn btn-default filter-col btn-sm"
                                            v-on:click="addEmptyFilter">
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i> Add filter
                                    </button>
                                    <button class="btn btn-default filter-col btn-sm" v-on:click="search">
                                        <i class="fa fa-search" aria-hidden="true"></i> Search
                                    </button>
                                    <button class="btn btn-default filter-col btn-sm"
                                            v-show="extFilters.filters.default.text != ''" v-on:click="cancelSearch">
                                        <i class="fa fa-times" aria-hidden="true"></i> Cancel filter
                                    </button>
                                </div>

                                <div v-for="filter in extFilters.filters.filters" style="margin: 10px 0">
                                    <div class="form-group">
                                        <select id="pref-perpage" class="form-control input-sm" v-model="filter.andor">
                                            <option value="and">and</option>
                                            <option value="or">or</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select id="pref-perpage"
                                                class="form-control input-sm"
                                                v-model="filter.column"
                                                @change="changeColumn($event, filter)">
                                            <option value="all">Search everywhere</option>
                                            <option v-for="column in filteredColumns" :value="column.name">
                                                {{ capitalize(column.title) }}
                                            </option>
                                        </select>
                                    </div>
                                    <component :is="filter.filter"
                                            :filterData="filter"
                                            @updateText="filter.text = $event"
                                            @updateOperator="filter.operator = $event">
                                    </component>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-default filter-col btn-sm"
                                                v-on:click="addEmptyFilter">
                                            <i class="fa fa-plus-circle" aria-hidden="true"></i> Add filter
                                        </button>
                                        <span v-on:click="removeFilter(filter)">
											<i class="fa fa-times" aria-hidden="true" style="cursor: pointer"></i>
										</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Filter from './Classes/Filter';
    import TextFilter from './FilterTypes/Text.vue';
    import NumberFilter from './FilterTypes/Number.vue';
    import BooleanFilter from './FilterTypes/Boolean.vue';
    import DateFilter from './FilterTypes/Date.vue';
    import DateTimeFilter from './FilterTypes/DateTime.vue';
    import CustomFilter from './FilterTypes/Custom.vue';
    import MoneyFilter from './FilterTypes/Money.vue';

    export default {
        props: ['datatableData'],
        components: {
            TextFilter,
            NumberFilter,
            BooleanFilter,
            DateFilter,
            DateTimeFilter,
            CustomFilter,
            MoneyFilter,
        },
        data() {
            return {
                showFilter: false,
                filterLink: '',
                generateLinkBtn: false,
                extFilters: new Filter(),
                newColumns: this.datatableData.columns.map(a => Object.assign({}, a))
            }
        },
        computed: {
            filteredColumns: function () {
                return this.newColumns.filter(function (item) {
                    return item.visible && item.searchable;
                }).sort(function (a, b) {
                    return a.sequence - b.sequence;
                }).map(function (item) {
                    if (item.filter === 'CustomFilter') {
                        item.name = item.filterData.column;
                    }
                    return item;
                });
            }
        },
        methods: {
            capitalize: function (param) {
                return param.charAt(0).toUpperCase() + param.slice(1);
            },

            addEmptyFilter: function () {
                this.extFilters.addEmpty();
                this.moveRight();
            },

            removeFilter: function (filter) {
                this.extFilters.remove(filter);
                if (this.extFilters.filters.filters.length === undefined || this.extFilters.filters.filters.length === 0) {
                    this.moveLeft();
                }
            },

            moveRight: function () {
                $('#ext-search-column-1').animate({
                    'margin-left': '76px'
                });
            },

            moveLeft: function () {
                $('#ext-search-column-1').animate({
                    'margin-left': '0'
                });
            },

            search: function () {
                let filterToRequest = JSON.parse(JSON.stringify(this.extFilters));

                this.removeFilterData(filterToRequest);
                Event.$emit('search', filterToRequest.extFilters);
            },
            
            cancelSearch: function () {
                this.extFilters.reset();
                this.moveLeft();
                Event.$emit('cancelSearch');
                Event.$emit('showSelectedRows');
            },
            
            changeDefaultColumn: function (event) {
                Event.$emit('columnHasChanged');
                this.extFilters.changeDefaultColumn(this.filteredColumns, event);
            },
            
            changeColumn: function (event, filter) {
                Event.$emit('columnHasChanged');
                Filter.changeColumn(this.filteredColumns, event, filter);
            },

            removeFilterData: function (filters) {
                if (filters.extFilters.default.hasOwnProperty('filterData')) {
                    delete filters.extFilters.default.filterData;
                }

                if (filters.extFilters.hasOwnProperty('filters') &&
                    filters.extFilters.filters !== undefined &&
                    filters.extFilters.filters.length > 0
                ) {
                    filters.extFilters.filters.forEach(function (filter) {
                        delete filter.filterData;
                    });
                }
            },

            generateLink: function () {
                let filterToRequest = JSON.parse(JSON.stringify(this.extFilters));
                this.removeFilterData(filterToRequest);
                this.generateLinkBtn = true;

                let self = this;
                let config = {
                    headers: {'X-Requested-With': 'XMLHttpRequest'} // Laravel recognition
                };
                axios.post(self.datatableData.filters_route, {
                    filters: filterToRequest,
                    module: self.datatableData.module_uri
                }, config).then(function (response) {
                    self.filterLink = response.data;
                    self.generateLinkBtn = false;
                }).catch(function (error) {
                    console.log(error);
                });
            },
        },
        created() {
            Event.$on('searchFromExt', () => {
                this.search();
            });

            Event.$on('hasFilter', (parameter) => {
                if (parameter.value !== "" && parameter.value) {
                    let filters = JSON.parse(parameter.value);
                    if (filters.type === 'extendedSearch') {
                        this.showFilter = true;
                        this.extFilters.setDefaultColumn(filters, this.filteredColumns);

                        if (filters.filters.length) {
                            filters.filters.forEach(function (filter) {
                                if (filter.hasOwnProperty('type') && filter.type === 'selectedRows') {
                                    return;
                                }

                                this.extFilters.addFilter(filter, this.filteredColumns);
                                this.moveRight();
                            });
                        }
                    }
                } else {
                    this.showFilter = false;
                }
            });
        }
    }
</script>