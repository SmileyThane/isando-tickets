<template>
    <v-card outlined style="cursor: pointer" class="mb-2"
        :style="`${selected ? `border: 2px solid ${this.themeBgColor}`: ''}`"
        @click="$router.push('/incident_reporting/1')"
    >
        <v-card-title class="py-2">
            {{Math.round(Math.random()*100)}}.00 Natural disasters
            <v-spacer></v-spacer>
            <v-menu bottom offset-y class="float-right">
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        rounded
                        icon
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item link>
                        <v-list-item-title>{{ langMap.kb.edit }}</v-list-item-title>
                        <v-list-item-action>
                            <v-icon :color="themeBgColor">mdi-file-edit-outline</v-icon>
                        </v-list-item-action>
                    </v-list-item>
                    <v-list-item link>
                        <v-list-item-title>{{ langMap.kb.delete }}</v-list-item-title>
                        <v-list-item-action>
                            <v-icon :color="themeBgColor">mdi-file-remove-outline</v-icon>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-card-title>
        <v-list-item class="py-0">
            <v-list-item-content class="py-0">
                <div class="text-overline">
                    Risks: {{Math.round(Math.random()*10)}}
                </div>
                <v-list-item-subtitle>Greyhound divisely hello coldly fonwderfully</v-list-item-subtitle>
            </v-list-item-content>
        </v-list-item>

        <v-card-actions class="py-0 pb-2">
            <v-btn :color="themeBgColor" text>
                Open category
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    name: 'incident-category',
    props: {
        selected: {
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
}
</script>
