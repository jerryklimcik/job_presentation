<template>
    <div class="form-group">
        <div class="form-group">
            <select id="pref-perpage" class="form-control input-sm extended-search-form-operator"
                    v-model="internalOperator" @change="changeOperator()">
                <option value="=">=</option>
                <option value="!=">≠</option>
                <option value="like">Like</option>
                <option value="not_like">Not like</option>
                <option value="is_null">Null</option>
                <option value="is_not_null">Not null</option>
            </select>
        </div>
        <div class="form-group">
            <select :id="randomName" v-show="showInput"
                    class="form-control input-sm extended-search-form-input"
                    v-model="internalText" @change="changeText()" v-on:keyup.enter="search">
                <option v-for="(column_name, index) in this.filterData.filterData" :value="index">
                    {{ column_name }}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['filterData'],
        data: function () {
            return {
                internalText: (this.filterData.text && this.filterData.hasFilter !== undefined) ? this.filterData.text : '',
                internalOperator: (this.filterData.operator && this.filterData.hasFilter !== undefined) ? this.filterData.operator : 'like',
                showInput: true,

                // random ID selector
                randomName: ''
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
            initial: function () {
                this.changeText();
                this.changeOperator();
                this.randomName = makeid();
                $(function () {
                    let my_options = $('#' + self.randomName + ' option');
                    my_options.sort(function (a, b) {
                        if (a.text > b.text) return 1;
                        if (a.text < b.text) return -1;
                        return 0
                    });

                    if ((self.filterData.text === "" || self.filterData.text === undefined) && self.filterData.hasFilter === undefined) {
                        let randomName = '#' + self.randomName;
                        $(randomName).empty().append(my_options);
                        $(randomName).val($(randomName + ' option:first').val());
                        self.internalText = $(randomName + ' option:first').val(); // selected first option
                        self.internalOperator = 'like'; // default operator
                        self.showHideInput();
                        self.changeText();
                    }
                });
            },

            makeid: function () {
                let text = "";
                let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
                for (let i = 0; i < 5; i++)
                    text += possible.charAt(Math.floor(Math.random() * possible.length));
                return text;
            }
        },
        mounted: function () {
            this.initial();

            Event.$on('cancelSearch', () => {
                this.internalText = '';
                this.internalOperator = 'like';
                this.changeText();
                this.changeOperator();
            });
            
            Event.$on('columnHasChanged', () => {
                this.initial();
                this.showInput = false;
                this.showInput = true;
                this.showHideInput();
            });
        }
    }
</script>