<template>
    <div class="form-group">
        <div class="form-group">
            <select id="pref-perpage" class="form-control input-sm extended-search-form-operator"
                    v-model="internalOperator"
                    @change="changeOperator()">
                <option value="=">=</option>
                <option value="!=">≠</option>
                <option value="is_null">Null</option>
                <option value="is_not_null">Not null</option>
            </select>
        </div>
        <div class="form-group">
            <select id="pref-perpage" v-show="showInput" class="form-control input-sm extended-search-form-input"
                    v-model="internalText"
                    @change="changeText()" v-on:keyup.enter="search">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['filterData'],
        data: function () {
            return {
                internalText: (this.filterData.text && this.filterData.hasFilter !== undefined) ? this.filterData.text : 1,
                internalOperator: (this.filterData.operator && this.filterData.hasFilter !== undefined) ? this.filterData.operator : '=',
                showInput: true,
            }
        },
        methods: {
            changeText: function () {
                this.$emit('updateText', this.internalText);
            },

            changeOperator: function () {
                this.showHideInput();
                this.$emit('updateOperator', this.internalOperator);
            },

            search: function () {
                Event.$emit('searchFromExt');
            },

            showHideInput: function () {
                if (this.internalOperator === 'is_null' || this.internalOperator === 'is_not_null') {
                    this.showInput = false;
                    return;
                }
                this.showInput = true;
            },
        },
        created: function () {
            this.changeText();
            this.changeOperator();

            Event.$on('cancelSearch', () => {
                this.internalText = '1';
                this.internalOperator = '=';
                this.changeText();
                this.changeOperator();
            });
        }
    }
</script>