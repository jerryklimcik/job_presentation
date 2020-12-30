<template>
    <div class="form-group">
        <div class="form-group">
            <select id="pref-perpage" class="form-control input-sm extended-search-form-operator" v-model="internalOperator" @change="changeOperator()">
                <option value="=">=</option>
                <option value=">">&gt;</option>
                <option value=">=">≥</option>
                <option value="<">&lt;</option>
                <option value="<=">≤</option>
                <option value="!=">≠</option>
                <option value="like">Like</option>
                <option value="not_like">Not like</option>
                <option value="is_null">Null</option>
                <option value="is_not_null">Not null</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" v-show="showInput" class="form-control input-sm extended-search-form-input" v-model="internalText"
                    @keyup="changeText()"
                    v-on:keyup.enter="search">
        </div>
    </div>
</template>

<script>
    export default {
        props: ['filterData'],
        data: function () {
            return {
                internalText: (this.filterData.text && this.filterData.hasFilter !== undefined) ? this.filterData.text : '',
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
                this.internalText = '';
                this.internalOperator = '=';
                this.changeText();
                this.changeOperator();
            });
        }
    }
</script>