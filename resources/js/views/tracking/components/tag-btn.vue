<template>
    <v-menu
        :close-on-content-click="false"
        :nudge-width="200"
        offset-x
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                :icon="!value.length"
                x-small
                fab
                :color="color"
                v-bind="attrs"
                v-on="on"
            >
                <v-icon
                    v-if="value.length"
                    style="color: white"
                >mdi-tag</v-icon>
                <v-icon v-else center>
                    mdi-tag-outline
                </v-icon>
            </v-btn>
        </template>
        <v-card
            style="max-width: 400px"
        >
            <template>
                <v-combobox
                    v-model="value"
                    :hide-no-data="!search"
                    :items="items"
                    :search-input.sync="search"
                    hide-selected
                    return-object
                    item-text="name"
                    item-id="name"
                    multiple
                    small-chips
                    solo
                >
                    <template v-slot:no-data>
                        <v-list-item>
                            <span class="subheading">Create&nbsp;</span>
                            <v-chip
                                :color="color"
                                label
                                small
                                outlined
                            >
                                {{ search }}
                            </v-chip>
                        </v-list-item>
                    </template>
                    <template v-slot:selection="{ attrs, item, parent, selected }">
                        <v-chip
                            v-if="item.name === Object(item).name"
                            v-bind="attrs"
                            :color="color"
                            :input-value="selected"
                            label
                            small
                            outlined
                        >
                        <span v-if="item.id">
                            {{ item.name }}
                            <v-icon
                                small
                                @click="parent.selectItem(item)"
                            >
                                mdi-close-circle-outline
                            </v-icon>
                        </span>
                            <v-progress-circular
                                v-if="!item.id"
                                indeterminate
                                :size="20"
                                :color="color"
                            ></v-progress-circular>
                        </v-chip>
                    </template>
                    <template v-slot:item="{ parent, item, on, attrs }">
                        <v-chip
                            :color="color"
                            label
                            small
                            style="color: white"
                        >
                            {{ item.name }}
                        </v-chip>
                    </template>
                </v-combobox>
            </template>
        </v-card>
    </v-menu>
</template>

<script>
export default {
    props: ['value', 'items', 'color'],
    data: () => {
        return {
            search: ''
        };
    }
};
</script>
