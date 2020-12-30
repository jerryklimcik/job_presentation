export default class Filter {
    constructor() {
        this.extFilters = {};
        this.reset();
    }

    reset() {
        this.extFilters = {
            'type': 'extendedSearch',
            'default': {
                column: 'all',
                filter: 'TextFilter',
                text: '',
                operator: ''
            },
            'filters': []
        };
    }

    addEmpty() {
        this.extFilters.filters.push({
            andor: 'and',
            column: 'all',
            text: '',
            operator: '',
            filter: 'TextFilter'
        });
    }

    addFilter(data, columns) {
        if (data.column === 'all') {
            this.extFilters.filters.push({
                hasFilter: true,
                andor: data.andor,
                column: data.column,
                text: data.text,
                operator: data.operator,
                filter: 'TextFilter',
                filterData: null,
            });
            return;
        }

        for (let i = 0; i < columns.length; i++) {
            if (columns[i].name === data.column) {
                this.extFilters.filters.push({
                    hasFilter: true,
                    andor: data.andor,
                    column: columns[i].name,
                    text: data.text,
                    operator: data.operator,
                    filter: columns[i].filter,
                    filterData: columns[i].hasOwnProperty('filterData') ? columns[i].filterData.data : null,
                });
            }
        }
    }

    remove(filter) {
        let index = this.extFilters.filters.indexOf(filter);
        this.extFilters.filters.splice(index, 1);
    }

    changeDefaultColumn(columns, event) {
        if (event.target.value === 'all') {
            this.extFilters.default.filter = 'TextFilter';
            return;
        }

        for (let i = 0; i < columns.length; i++) {
            if (columns[i].name === event.target.value) {

                if (columns[i].filter === null) {
                    this.extFilters.default.filter = 'TextFilter';
                    return;
                }
                
                if (columns[i].filter === 'CustomFilter') {
                    this.extFilters.default.text = '';
                    this.extFilters.default.operator = 'like';
                    this.extFilters.default.filterData = columns[i].filterData.data;
                } else {
                    this.extFilters.default.filterData = null;
                }

                if (this.extFilters.default.hasFilter !== undefined) {
                    delete this.extFilters.default.hasFilter;
                }

                this.extFilters.default.filter = columns[i].filter;
                return;
            }
        }
    }

    static changeColumn(columns, event, filter) {
        if (event.target.value === 'all') {
            filter.filter = 'TextFilter';
            return;
        }

        for (let i = 0; i < columns.length; i++) {
            if (columns[i].name === event.target.value) {

                if (columns[i].filter === null) {
                    filter.filter = 'TextFilter';
                    return;
                }
                
                if (columns[i].filter === 'CustomFilter') {
                    filter.text = '';
                    filter.operator = 'like';
                    filter.filterData = columns[i].filterData.data;
                }

                if (columns[i].hasFilter !== undefined) {
                    delete columns[i].hasFilter;
                }

                filter.filter = columns[i].filter;
                return;
            }
        }
    }

    setDefaultColumn(data, columns) {
        if (data.default.column === 'all') {
            this.extFilters.default = {
                hasFilter: true,
                column: data.default.column,
                filter: 'TextFilter',
                filterData: null,
                text: data.default.text,
                operator: data.default.operator
            };
            return;
        }

        for (let i = 0; i < columns.length; i++) {
            if (columns[i].name === data.default.column) {
                this.extFilters.default = {
                    hasFilter: true,
                    column: columns[i].name,
                    filter: columns[i].filter,
                    filterData: columns[i].hasOwnProperty('filterData') ? columns[i].filterData.data : null,
                    text: data.default.text,
                    operator: data.default.operator
                };
            }
        }
    }

    get filters() {
        return this.extFilters;
    }
}