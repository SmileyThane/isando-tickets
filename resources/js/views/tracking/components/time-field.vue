<template>
    <div
        :class="classes"
        style="max-width: 100px;"
    >
        <div
            v-if="prependIcon"
            class="v-input__prepend-outer">
            <div class="v-input__icon v-input__icon--prepend">
                <v-icon>{{prependIcon}}</v-icon>
            </div>
        </div>
        <div class="v-input__control">
            <div class="v-input__slot">
                <div class="v-text-field__slot">
                    <label
                        v-if="label"
                        class="v-label v-label--active theme--light"
                        style="left: 0px; right: auto; position: absolute;"
                    >{{label}}</label>
                    <input
                        type="time"
                        autofocus
                        v-model="time"
                        v-bind="$attrs"
                        @keypress="onKeyDown"
                        @blur="setTimeHandler"
                        @focus="$event.target.select()"
                    >
                    <v-menu
                        v-model="calendarMenu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                        left
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                class="mt-1"
                                icon
                                small
                                v-bind="attrs"
                                v-on="on"
                                v-if="!hideCalendar"
                            >
                                <v-icon>mdi-clock-outline</v-icon>
                            </v-btn>
                        </template>
                        <v-date-picker
                            first-day-of-week="1"
                            v-model="date"
                            :min="min"
                        ></v-date-picker>
                        <v-time-picker
                            format="24hr"
                            scrollable
                            v-model="time"
                            @click:minute="calendarMenu = false; setTimeHandler()"
                            @change="calendarMenu = false; setTimeHandler()"
                        ></v-time-picker>
                    </v-menu>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.v-text-field.time-field__flat>.v-input__control>.v-input__slot:before {
    border: none;
}
.v-text-field.time-field__flat>.v-input__control>.v-input__slot>.v-text-field__slot {
    flex: inherit;
}
input[type="time"]::-webkit-calendar-picker-indicator {
    background: none;
    display: none;
}
*:not(.v-icon) {
    font-size: 14px !important;
}
</style>

<script>

// import moment from 'moment';
import moment from 'moment-timezone';
import VInput from 'vuetify/lib/components/VInput/VInput';

export default {
    name: "time-field",
    extends: VInput,
    props: [
        'format',
        'value',
        'prependIcon',
        'label',
        'dense',
        'min',
        'hideCalendar'
    ],
    data: () => {
        return {
            isFocused: false,
            time: '',
            calendarMenu: false,
            date: null,
        };
    },
    created () {
        moment.updateLocale(this.$store.state.lang.short_code, {
            week: {
                dow: 1,
            },
        });
    },
    mounted() {
        this.time = moment(this.value).format(this.format);
        this.date = moment(this.value).format('YYYY-MM-DD');
    },
    methods: {
        // onInput(val) {
        //     this.$emit('input', val);
        // },
        // onFocus($event) {
        //     this.isFocused = $event.type === 'focus' ?? false;
        //     $event.target.select();
        // },
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
            if ($event.keyCode === 13) {
                this.setTimeHandler();
                return true;
            }
            const keyCodes = [8, 46, 37, 38, 39, 40, 13, 27];
            if (
                    ($event.keyCode >= 48 && $event.keyCode <= 57)
                || ($event.keyCode >= 96 && $event.keyCode <= 105)
                || (keyCodes.indexOf($event.keyCode) !== -1)
            ) {
                return true;
            }
            $event.preventDefault();
        },
        setTimeHandler() {
            let val = this.time.toString().replace(':', '');
            val = val.toString();
            if (val.length > 4) {
                val = val.substr(0, 4);
            }
            val = this.helperAddZeros(val, 4);
            const time = moment(this.value)
                .set({
                    hours: parseInt(val.substr(0, 2)),
                    minutes: parseInt(val.substr(-2))
                });
            if (moment(time.format()).isValid()) {
                this.formattedTime = moment(time).format();
                return true;
            }
            return moment().format();
        }
    },
    computed: {
        // time: {
        //     get() {
        //         if (moment(this.value).isValid()) {
        //             return moment(this.value).format();
        //         }
        //         return this.value;
        //     },
        //     set(val) {
        //         this.$emit('input', val);
        //     }
        // },
        formattedTime: {
            get() {
                if (moment(this.value).isValid()) {
                    return moment(this.value).format(this.format);
                }
                return this.value;
            },
            set(val) {
                val = moment(val).set({
                    year: moment(this.date).year(),
                    month: moment(this.date).month(),
                    date: moment(this.date).date(),
                }).format('YYYY-MM-DDTHH:mm:ss');
                this.$emit('input', val);
            }
        },
        formattedDate: function () {
            if (!this.date) {
                this.date = moment().format('YYYY-MM-DD');
            }

        },
        classes: () =>{
            return ['v-input', 'v-input--hide-details', 'v-input--is-label-active',
                'v-input--is-dirty', 'theme--light', 'v-text-field',
                'v-text-field--is-booted', 'v-text-field--placeholder', 'v-input--dense', 'time-field__flat'];
        },
        minTime: () => {
            console.log(this.min);
            return undefined;
        },
    },
    watch: {
        value: function () {
            if (moment(this.value).isValid()) {
                this.time = moment(this.value).format(this.format);
                this.date = moment(this.value).format('YYYY-MM-DD');
            }
        }
    }
}
</script>
