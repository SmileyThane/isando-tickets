<template>
    <v-text-field
        v-bind="$attrs"
        v-on="$listeners"
        v-model="formattedTime"
        label="Date From"
        hint="Date From"
        single-line
        counter
        autofocus
        @focus="$event.target.select()"
        v-on:focus="onFocus"
        v-on:keydown="onKeyDown"
    ></v-text-field>
</template>

<script>

import moment from 'moment-timezone';

export default {
    name: "time-field",
    props: ['format', 'value'],
    data: () => {
        return {
            isFocused: false
        };
    },
    methods: {
        onInput(val) {
            console.log('onInput ', val);
            this.$emit('input', val);
        },
        onFocus($event) {
            this.isFocused = $event.type === 'focus' ?? false;
            $event.target.select();
        },
        // onChange(val) {
        //     console.log('onChange ', val);
        //     if (val.toString().length > 4) {
        //         val = val.toString().substr(0, 4);
        //     }
        //     val = this.helperAddZeros(val, 4);
        //     const time = moment(this.value)
        //         .hours(parseInt(val.substr(0, 2)))
        //         .minutes(parseInt(val.substr(-2)));
        //     console.log(time);
        //     this.$emit('onchange', time);
        // },
        helperAddZeros(num, len) {
            while( ("" + num).length < len ) num = "0" + num;
            return num.toString();
        },
        onKeyDown($event) {
            const keyCodes = [8, 46, 37, 38, 39, 40, 13, 27];
            if (
                    ($event.keyCode >= 48 && $event.keyCode <= 57)
                || ($event.keyCode >= 96 && $event.keyCode <= 105)
            ) {
                return true;
            }
            $event.preventDefault();
        }
    },
    computed: {
        formattedTime: {
            get() {
                return moment(this.value).format(this.isFocused ? 'HHmm' : this.format);
            },
            set(val) {
                val = val.toString();
                console.log('set ', val);
                if (val.length > 4) {
                    val = val.substr(0, 4);
                }
                console.log('set ', val);
                val = this.helperAddZeros(val, 4);
                console.log('set ', val);
                const time = moment(this.value)
                    .hours(parseInt(val.substr(0, 2)))
                    .minutes(parseInt(val.substr(-2)));
                console.log(time);
                this.$emit('input', time);
            }
        }
    }
}
</script>
