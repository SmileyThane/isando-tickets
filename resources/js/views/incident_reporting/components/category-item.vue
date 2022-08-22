<template>
    <v-card outlined style="cursor: pointer" class="mb-2"
        :style="`${selected ? `border: 2px solid ${this.themeBgColor}`: ''}`"
        @click=""
    >
        <v-list-item>
            <v-list-item-content>
                <v-list-item-title class="text-h6 mb-1">
                    <div class="float-left"
                         style="text-overflow: ellipsis; overflow: hidden; max-width: 35vh;"
                    >{{ item.name }}</div>
                    <v-spacer v-if="extended"></v-spacer>
                    <v-chip v-if="extended"
                        class="float-right text-uppercase"
                        label
                        :color="themeBgColor"
                        :textColor="$helpers.color.invertColor(themeBgColor)"
                        x-small
                    >
                        approved
                    </v-chip>
                </v-list-item-title>
                <v-list-item-title v-if="extended">
                    Natural disaster
                    <br>
                    Brauerei Luzern including all child organizations
                    <br>
                    Risk incident potential: From Low
                </v-list-item-title>
                <v-list-item-title v-if="extended">
                    <v-chip v-for="(tag, index) in item.tags"
                            :key="index"
                            class="text-uppercase"
                            label
                            :color="themeBgColor"
                            x-small
                            outlined
                    >
                        {{ tag }}
                    </v-chip>
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

    }
}
</script>
