<template>
    <div class="form-group">
        <div class="form-group">
            <select id="pref-perpage" class="form-control input-sm extended-search-form-operator"
                    v-model="internalOperator"
                    @change="changeOperator()">
                <option value="=">=</option>
                <option value=">">&gt;</option>
                <option value=">=">≥</option>
                <option value="<">&lt;</option>
                <option value="<=">≤</option>
                <option value="!=">≠</option>
                <option value="is_null">Null</option>
                <option value="is_not_null">Not null</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" :id="randomName" v-show="showInput"
                    class="form-control input-sm extended-search-form-input input-datepicker"
                    v-model="internalText"
                    v-on:blur="selected"
                    v-on:keyup.enter="search">
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    import 'moment/locale/cs';

    moment.locale('cs');
    let $ = require('jquery');
    $.fn.datetimepicker = require('eonasdan-bootstrap-datetimepicker');

    export default {
        props: ['filterData'],
        data: function () {
            return {
                internalText: (this.filterData.text && this.filterData.hasFilter !== undefined) ? moment(this.filterData.text, 'YYYY-MM-DD HH:mm:ss').format('DD.MM.YYYY HH:mm:ss') : '',
                internalOperator: (this.filterData.operator && this.filterData.hasFilter !== undefined) ? this.filterData.operator : '=',
                showInput: true,
                dateFormat: 'DD.MM.YYYY HH:mm:ss',

                // random ID selector
                randomName: ''
            }
        },
        methods: {
            changeText: function () {
                this.$emit('updateText', moment(this.internalText, this.dateFormat).format('YYYY-MM-DD HH:mm:ss'));
            },

            changeOperator: function () {
                this.showHideInput();
                this.$emit('updateOperator', this.internalOperator);
            },

            search: function () {
                Event.$emit('searchFromExt');
            },

            // blur
            selected: function () {
                this.internalText = $('#' + this.randomName).val();
                this.changeText();
            },

            showHideInput: function () {
                if (this.internalOperator === 'is_null' || this.internalOperator === 'is_not_null') {
                    this.showInput = false;
                    return;
                }
                this.showInput = true;
            },

            makeid: function () {
                let text = "";
                let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
                for (let i = 0; i < 5; i++)
                    text += possible.charAt(Math.floor(Math.random() * possible.length));
                return text;
            }
        },
        created: function () {
            // each date instance must have unique ID
            this.randomName = makeid();
            
            this.changeText();
            this.changeOperator();

            Event.$on('cancelSearch', () => {
                this.internalText = '';
                this.internalOperator = '=';
                this.changeText();
                this.changeOperator();
            });

            $(function () {
                $('#' + this.randomName).datetimepicker({
                    format: this.dateFormat
                });
            });
        }
    }
</script>