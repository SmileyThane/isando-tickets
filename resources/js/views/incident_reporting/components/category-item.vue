<template>
    <v-card :style="`${selected ? `border: 2px solid ${this.themeBgColor}`: ''}`" class="mb-2" outlined
            style="cursor: pointer"
            @click="selectIR(item)"
    >
        <v-list-item>
            <v-list-item-content>
                <v-list-item-title class="subtitle-1 mb-1">
                    <strong class="float-left"
                         style="text-overflow: ellipsis; overflow: hidden; max-width: 35vh;"
                    >{{ item.name }}
                    </strong>
                    <v-spacer v-if="extended"></v-spacer>
                    <v-chip v-if="extended"
                            :color="themeBgColor"
                            :textColor="$helpers.color.invertColor(themeBgColor)"
                            class="float-right text-uppercase"
                            label
                            x-small
                    >
                        {{ item.state ? item.state.name : '' }}
                    </v-chip>
                </v-list-item-title>
                <v-list-item-title v-if="extended">
                    {{ item.description }}
                </v-list-item-title>
                <v-list-item-title v-if="extended">
                    <v-chip v-for="(tag, index) in item.categories"
                            :key="index"
                            :color="themeBgColor"
                            class="text-uppercase"
                            label
                            outlined
                            x-small
                    >
                        {{ tag.name }}
                    </v-chip>
                </v-list-item-title>
                <v-list-item-title v-if="withVersion">
                    {{ item.version }}
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-card>
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    name: 'incident-category-item',
    props: {
        selected: {
            type: Boolean,
            default: false,
        },
        extended: {
            type: Boolean,
            default: false,
        },
        item: {
            type: Object,
            required: true,
        },
        withVersion: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            langMap: this.$store.state.lang.lang_map,
        }
    },
    mounted() {
        const that = this
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        selectIR(item) {
            this.$store.dispatch('IncidentReporting/callSetSelectedIR', item);
        }

    }
}
</script>
